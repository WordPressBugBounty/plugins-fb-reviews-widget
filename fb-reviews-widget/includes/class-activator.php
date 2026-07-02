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
            Plugin::SLG . '_last_error',
        );
    }

    public function register() {
        add_action('init', array($this, 'init'));
    }

    public function init() {
        if (is_admin()) {
            $this->check_version();
        }
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
            $this->exist_install($last_active_version);
            if (get_option(Plugin::SLG . '_debug_mode') !== '1') {
                update_option(Plugin::SLG . '_version', $current_version);
                update_option(Plugin::SLG . '_revupd_cron', '1');
            }
        }
    }

    private function first_install() {
        $this->database->create();

        add_option(Plugin::SLG . '_active', '1');
        add_option(Plugin::SLG . '_google_api_key', '');
    }

    private function exist_install($last_active_version) {
        $this->update_db($last_active_version);
    }

    public function update_db($last_active_version) {
        global $wpdb;

        $rev = $wpdb->prefix . Database::REVIEW_TABLE;
        $biz = $wpdb->prefix . Database::BUSINESS_TABLE;

        if (version_compare($last_active_version, '2.0', '<')) {
            $this->first_install();
        }

        if (version_compare($last_active_version, '2.7', '<')) {

            // add column map_url
            $columns = $wpdb->get_col("SHOW COLUMNS FROM {$biz}", 0);
            if (!in_array('map_url', $columns, true)) {
                $wpdb->query("ALTER TABLE {$biz} ADD map_url VARCHAR(512)");
            }

            // add columns images, reply, reply_time
            $columns = array_flip(
                $wpdb->get_col("SHOW COLUMNS FROM {$rev}", 0)
            );
            foreach ([
                'review_id'  => 'VARCHAR(64)',
                'images'     => 'TEXT',
                'reply'      => 'TEXT',
                'reply_time' => 'INTEGER'
            ] as $col => $type) {
                if (!isset($columns[$col])) {
                    $wpdb->query("ALTER TABLE {$rev} ADD {$col} {$type}");
                }
            }

            // set default platform google
            $wpdb->query("UPDATE {$rev} SET platform = 'google' WHERE platform IS NULL OR platform = ''");

            // set review_id
            $wpdb->query(
                "UPDATE {$rev} r
                 JOIN {$biz} b ON b.id = r.biz_id
                 SET r.review_id = MD5(CONCAT(
                     r.platform, ':', b.pid, ':',
                     COALESCE(
                         NULLIF(r.author_url, ''),
                         CONCAT(COALESCE(NULLIF(r.author_name, ''), ''), ':', r.time)
                     )
                 ))
                 WHERE r.review_id IS NULL OR r.review_id = ''"
            );

            $this->database->create_text_table();
            $this->database->migrate_review_texts();
        }

        if (version_compare($last_active_version, '2.8', '<')) {
            $text = $wpdb->prefix . Database::TEXT_TABLE;

            $rev_col = $wpdb->get_row("SHOW FULL COLUMNS FROM {$rev} WHERE Field = 'review_id'");
            $txt_col = $wpdb->get_row("SHOW FULL COLUMNS FROM {$text} WHERE Field = 'review_id'");

            if ($rev_col && $txt_col && !empty($rev_col->Collation)) {
                $collation = $rev_col->Collation;

                if ($txt_col->Collation !== $collation) {
                    $valid_collation = $wpdb->get_var($wpdb->prepare("SHOW COLLATION WHERE `Collation` = %s", $collation));
                    if ($valid_collation) {
                        $wpdb->query("ALTER TABLE {$text} MODIFY review_id VARCHAR(64) COLLATE {$collation} NOT NULL");
                    }
                }
            }
        }

        /*if (version_compare($last_active_version, '2.8', '<')) {

            // remove duplicates
            $wpdb->query("DELETE r1 FROM {$rev} r1 INNER JOIN {$rev} r2 ON r1.platform = r2.platform AND r1.review_id = r2.review_id AND r1.id < r2.id");

            // add index platform and review_id
            $idx = Plugin::PFX . 'platform_review_idx';
            $exists = $wpdb->get_var("SHOW INDEX FROM {$rev} WHERE Key_name = '{$idx}'");
            if (!$exists) {
                $wpdb->query("ALTER TABLE {$rev} ADD UNIQUE INDEX {$idx} (platform, review_id)");
            }
        }*/

        if (!empty($wpdb->last_error)) {
            update_option(Plugin::SLG . '_last_error', time() . ': ' . $wpdb->last_error);
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
    public function delete_all_options($multisite = false, $except = array()) {
        $network_wide = get_option(Plugin::SLG . '_is_multisite');
        if ($multisite && $network_wide) {
            $this->delete_all_options_multisite($except);
        } else {
            $this->delete_all_options_single_site($except);
        }
    }

    private function delete_all_options_multisite($except = array()) {
        global $wpdb;

        $site_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");

        foreach($site_ids as $site_id) {
            switch_to_blog($site_id);
            $this->delete_all_options_single_site($except);
            restore_current_blog();
        }
    }

    private function delete_all_options_single_site($except = array()) {
        foreach ($this->options() as $opt) {
            if (!in_array($opt, $except)) {
                delete_option($opt);
            }
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
