<?php

namespace WP_TrustReviews\Includes\Admin;

use WP_TrustReviews\Includes\Plugin;
use WP_TrustReviews\Includes\Post_Types;

class Admin_Menu {

    public function __construct() {
    }

    public function register() {
        add_action('admin_menu', array($this, 'add_page'), 9);
        add_action('admin_menu', array($this, 'add_subpages'));
        add_filter('submenu_file', array($this, 'remove_submenu_pages'));
        add_filter('admin_body_class', array($this, 'add_admin_body_class'));
    }

    public function add_page() {
        add_menu_page(
            'Trust.Reviews',
            'Trust.Reviews',
            'edit_posts',
            Plugin::SLG,
            '',
            Plugin::ASSETS_URL() . 'img/menu_icon.png',
            25
        );

        $overview_page = new Admin_Page(
            Plugin::SLG,
            'Overview',
            'Overview',
            'edit_posts',
            Plugin::SLG
        );
        $overview_page->add_page();
    }

    public function add_subpages() {
        $builder_page = new Admin_Page(
            Plugin::SLG,
            'Reviews Builder',
            'Builder',
            'edit_posts',
            Plugin::SLG . '-builder'
        );
        $builder_page->add_page();

        $setting_page = new Admin_Page(
            Plugin::SLG,
            'Settings',
            'Settings',
            'manage_options',
            Plugin::SLG . '-settings'
        );
        $setting_page->add_page();

        /*$support_page = new Admin_Page(
            Plugin::SLG,
            'Support',
            'Support',
            'manage_options',
            Plugin::SLG . '-support'
        );
        $support_page->add_page();*/
    }

    public function remove_submenu_pages($submenu_file) {
        global $plugin_page;

        $hidden_pages = array(
            Plugin::SLG . '-builder',
        );

        if ($plugin_page && in_array($plugin_page, $hidden_pages)) {
            $submenu_file = 'edit.php?post_type=' . Post_Types::FEED_POST_TYPE;
        }

        foreach ($hidden_pages as $page) {
            remove_submenu_page(Plugin::SLG, $page);
        }

        return $submenu_file;
    }

    public function add_admin_body_class($classes) {
        $current_screen = get_current_screen();

        if (empty($current_screen)) {
            return;
        }

        if (strpos($current_screen->id, Plugin::SLG) !== false) {
            $classes .= ' ' . Plugin::SLG . '-admin ';
        }
        return $classes;
    }

}
