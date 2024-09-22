<?php

namespace WP_TrustReviews\Includes;

use WP_TrustReviews\Includes\Core\Database;

class Activator {

    private $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function options() {
        return array(
            Plugin::SLG . '_version',
            Plugin::SLG . '_active',
            Plugin::SLG . '_google_api_key',
            Plugin::SLG . '_language',
            Plugin::SLG . '_activation_time',
            Plugin::SLG . '_auth_code',
            Plugin::SLG . '_debug_mode',
            Plugin::SLG . '_feed_ids',
            Plugin::SLG . '_do_activation',
            Plugin::SLG . '_revupd_cron',
            Plugin::SLG . '_revupd_cron_timeout',
            Plugin::SLG . '_revupd_cron_log',
            Plugin::SLG . '_rev_notice_hide',
            Plugin::SLG . '_rev_notice_show',
            Plugin::SLG . '_rate_us',
        );
    }

    public function register() {
        add_action('init', array($this, 'check_version'));
        add_filter('https_ssl_verify', '__return_false');
        add_filter('block_local_requests', '__return_false');
    }

    public function check_version() {
        if (version_compare(get_option(Plugin::SLG . '_version'), Plugin::VER, '<')) {
            $this->activate();
        }
    }

    /**
	 * Activates the plugin on a multisite
	 */
    public function activate() {
        $network_wide = get_option(Plugin::SLG . '_is_multisite');
        if ($network_wide) {
            $this->activate_multisite();
        } else {
            $this->activate_single_site();
        }
    }

    private function activate_multisite() {
        global $wpdb;

        $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

        foreach($site_ids as $site_id) {
            switch_to_blog($site_id);
            $this->activate_single_site();
            restore_current_blog();
        }
    }

    private function activate_single_site() {
        $current_version     = Plugin::VER;
        $last_active_version = get_option(Plugin::SLG . '_version');

        if (empty($last_active_version)) {
            $this->first_install();
            update_option(Plugin::SLG . '_version', $current_version);
            update_option(Plugin::SLG . '_auth_code', $this->random_str(127));
            update_option(Plugin::SLG . '_revupd_cron', '1');
        } elseif ($last_active_version !== $current_version) {
            $this->exist_install($current_version, $last_active_version);
            update_option(Plugin::SLG . '_version', $current_version);
            update_option(Plugin::SLG . '_revupd_cron', '1');
        }
    }

    private function first_install() {
        $this->database->create();

        add_option(Plugin::SLG . '_active', '1');
        add_option(Plugin::SLG . '_google_api_key', '');
    }

    private function exist_install($current_version, $last_active_version) {
        $this->update_db($last_active_version);
    }

    public function update_db($last_active_version) {
        global $wpdb;

        switch($last_active_version) {
            case version_compare($last_active_version, '2.0', '<'):
                $this->first_install();
        }
    }

    /**
	 * Creates the plugin database on a multisite
	 */
    public function create_db() {
        $network_wide = get_option(Plugin::SLG . '_is_multisite');
        if ($network_wide) {
            $this->create_db_multisite();
        } else {
            $this->create_db_single_site();
        }
    }

    private function create_db_multisite() {
        global $wpdb;

        $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

        foreach($site_ids as $site_id) {
            switch_to_blog($site_id);
            $this->create_db_single_site();
            restore_current_blog();
        }
    }

    private function create_db_single_site() {
        $this->database->create();
    }

    /**
	 * Drops the plugin database on a multisite
	 */
    public function drop_db($multisite = false) {
        $network_wide = get_option(Plugin::SLG . '_is_multisite');
        if ($multisite && $network_wide) {
            $this->drop_db_multisite();
        } else {
            $this->drop_db_single_site();
        }
    }

    private function drop_db_multisite() {
        global $wpdb;

        $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

        foreach($site_ids as $site_id) {
            switch_to_blog($site_id);
            $this->drop_db_single_site();
            restore_current_blog();
        }
    }

    private function drop_db_single_site() {
        $this->database->drop();
    }

    /**
	 * Delete all options of the plugin on a multisite
	 */
    public function delete_all_options($multisite = false) {
        $network_wide = get_option(Plugin::SLG . '_is_multisite');
        if ($multisite && $network_wide) {
            $this->delete_all_options_multisite();
        } else {
            $this->delete_all_options_single_site();
        }
    }

    private function delete_all_options_multisite() {
        global $wpdb;

        $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

        foreach($site_ids as $site_id) {
            switch_to_blog($site_id);
            $this->delete_all_options_single_site();
            restore_current_blog();
        }
    }

    private function delete_all_options_single_site() {
        foreach ($this->options() as $opt) {
            delete_option($opt);
        }
    }

    /**
	 * Delete all feeds of the plugin on a multisite
	 */
    public function delete_all_feeds($multisite = false) {
        $network_wide = get_option(Plugin::SLG . '_is_multisite');
        if ($multisite && $network_wide) {
            $this->delete_all_feeds_multisite();
        } else {
            $this->delete_all_feeds_single_site();
        }
    }

    private function delete_all_feeds_multisite() {
        global $wpdb;

        $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

        foreach($site_ids as $site_id) {
            switch_to_blog($site_id);
            $this->delete_all_feeds_single_site();
            restore_current_blog();
        }
    }

    private function delete_all_feeds_single_site() {
        $args = array(
            'post_type'      => Post_Types::FEED_POST_TYPE,
            'post_status'    => array('any', 'trash'),
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );

        $query = new \WP_Query($args);
        $posts = $query->posts;

        if (!empty($posts)) {
            foreach ($posts as $post) {
                wp_delete_post($post, true);
            }
        }
    }

    private function random_str($len) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charlen = strlen($chars);
        $randstr = '';
        for ($i = 0; $i < $len; $i++) {
            $randstr .= $chars[rand(0, $charlen - 1)];
        }
        return $randstr;
    }

}
