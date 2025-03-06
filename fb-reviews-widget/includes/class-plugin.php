<?php

namespace WP_TrustReviews\Includes;

use WP_TrustReviews\Includes\Admin\Admin_Menu;
use WP_TrustReviews\Includes\Admin\Admin_Tophead;
use WP_TrustReviews\Includes\Admin\Admin_Notice;
use WP_TrustReviews\Includes\Admin\Admin_Feed_Columns;
use WP_TrustReviews\Includes\Admin\Admin_Rev;
use WP_TrustReviews\Includes\Admin\Admin_Rateus_Ajax;

use WP_TrustReviews\Includes\Core\Core;
use WP_TrustReviews\Includes\Core\Connect_Google;
use WP_TrustReviews\Includes\Core\Database;

final class Plugin {

    const VER = '2.4';
    const SLG = 'trustreviews';
    const PFX = 'trustreviews_';
    const NAME = 'fb-reviews-widget';

    const FB_URL = 'https://facebook.com/';
    const FB_GURL = 'https://graph.facebook.com/';

    const TR_APP_URL = 'https://app.trust.reviews';
    const FB_APP_URL = self::TR_APP_URL . '/fb';
    const FB_AUTH_URL = self::TR_APP_URL .  '/auth/fb';

    const RP_APP_URL = self::TR_APP_URL;
    const G_APP_URL = self::RP_APP_URL . '/gpaw';
    const Y_APP_URL = self::RP_APP_URL . '/yarw';

    protected $name;
    protected $version;
    protected $activator;
    protected $deactivator;

    public function __construct() {
        $this->name = Plugin::NAME;
        $this->version = Plugin::VER;
    }

    public static function PATH() {
        return plugin_dir_path(TRUSTREVIEWS_PLUGIN_FILE);
    }

    public static function URL() {
        return plugins_url(basename(self::PATH()), basename(TRUSTREVIEWS_PLUGIN_FILE));
    }

    public static function ASSETS_URL() {
        return self::URL() . '/assets/';
    }

    public static function G_BIZ_LOGO() {
        return self::ASSETS_URL() . 'img/gmblogo.svg';
    }

    public function register() {
        register_activation_hook(TRUSTREVIEWS_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(TRUSTREVIEWS_PLUGIN_FILE, array($this, 'deactivate'));

        add_action('admin_init', array($this, 'admin_init'));
        add_action('plugins_loaded', array($this, 'register_services'));
    }

    public function admin_init() {
        if (get_option(Plugin::SLG . '_do_activation', false)) {
            delete_option(Plugin::SLG . '_do_activation');
            wp_safe_redirect(admin_url('admin.php?page=' . Plugin::SLG));
        }
    }

    public function register_services() {
        $this->init_language();

        $helper = new Helper();

        $database = new Database();

        $activator = new Activator($database);
        $activator->register();

        $assets = new Assets(Plugin::ASSETS_URL(), $this->version, get_option(Plugin::SLG . '_debug_mode') == '1');
        $assets->register();

        $post_types = new Post_Types();
        $post_types->register();

        $feed_deserializer = new Feed_Deserializer(new \WP_Query());

        $debug_info = new Debug_Info($activator, $feed_deserializer);

        $feed_page = new Feed_Page($feed_deserializer);
        $feed_page->register();

        $core = new Core();

        $view_svg = new View_Svg();

        $view = new View($view_svg);

        $builder_page = new Builder_Page($feed_deserializer, $core, $view);
        $builder_page->register();

        $feed_old = new Feed_Old();

        $feed_shortcode = new Feed_Shortcode($feed_deserializer, $assets, $core, $view, $feed_old);
        $feed_shortcode->register();

        Feed_Widget::$static_feed_deserializer = $feed_deserializer;
        Feed_Widget::$static_core              = $core;
        Feed_Widget::$static_view              = $view;
        Feed_Widget::$static_assets            = $assets;

        Feed_Widget_Old::$static_core          = $core;
        Feed_Widget_Old::$static_view          = $view;
        Feed_Widget_Old::$static_feed_old      = $feed_old;

        add_action('widgets_init', function() {
            register_widget('WP_TrustReviews\Includes\Feed_Widget');
            register_widget('WP_TrustReviews\Includes\Feed_Widget_Old');
        });

        $feed_block = new Feed_Block($feed_deserializer, $core, $view, $assets);
        $feed_block->register();

        $connect_google = new Connect_Google($helper);

        $reviews_cron = new Reviews_Cron($connect_google, $feed_deserializer);
        $reviews_cron->register();

        $this->deactivator = new Deactivator($reviews_cron);

        if (is_admin()) {
            $feed_serializer = new Feed_Serializer();
            $feed_ajax = new Feed_Ajax($feed_serializer, $feed_deserializer, $core, $view);

            $admin_notice = new Admin_Notice();
            $admin_notice->register();

            $admin_menu = new Admin_Menu();
            $admin_menu->register();

            $admin_tophead = new Admin_Tophead();
            $admin_tophead->register();

            $admin_feed_columns = new Admin_Feed_Columns($feed_deserializer);
            $admin_feed_columns->register();

            $plugin_overview_ajax = new Plugin_Overview_Ajax($core);
            $plugin_overview = new Plugin_Overview($view_svg);
            $plugin_overview->register();

            $settings_save = new Settings_Save($activator, $reviews_cron);
            $settings_save->register();

            $plugin_settings = new Plugin_Settings($debug_info);
            $plugin_settings->register();

            $plugin_support = new Plugin_Support();
            $plugin_support->register();

            $admin_rev = new Admin_Rev();
            $admin_rev->register();

            $rateus_ajax = new Admin_Rateus_Ajax();
        }
    }

    public function init_language() {
        load_plugin_textdomain(Plugin::NAME, false, basename(dirname(TRUSTREVIEWS_PLUGIN_FILE)) . '/languages');
    }

    public function activate($network_wide = false) {
        $now = time();
        update_option(Plugin::SLG . '_activation_time', $now);

        add_option(Plugin::SLG . '_is_multisite', $network_wide);

        add_option(Plugin::SLG . '_do_activation', true);

        $activator = new Activator(new Database());
        $activator->activate();
    }

    public function deactivate() {
        $this->deactivator->deactivate();
    }
}