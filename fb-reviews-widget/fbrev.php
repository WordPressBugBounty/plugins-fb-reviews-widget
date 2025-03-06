<?php
/*
Plugin Name: Social Reviews & Recommendations
Plugin URI: https://wordpress.org/plugins/fb-reviews-widget
Description: Combines Facebook, Google and Yelp Reviews in widgets and shortcodes! Boost user confidence, number of customers and sales on site!
Version: 2.4
Author: Trust.reviews <support@trust.reviews>
Author URI: https://trust.reviews
Text Domain: fb-reviews-widget
Domain Path: /languages
*/

namespace WP_TrustReviews;

use WP_TrustReviews\Includes\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

require(ABSPATH . 'wp-includes/version.php');

define('TRUSTREVIEWS_PLUGIN_FILE' , __FILE__);

require_once __DIR__ . '/autoloader.php';

/*-------------------------------- Links --------------------------------*/
function plugin_action_links($links, $file) {
    $plugin_file = basename(__FILE__);
    if (basename($file) == $plugin_file) {
        $settings_link = '<a href="' . admin_url('admin.php?page=' . Plugin::SLG . '-builder') . '">' .
                             '<span style="background-color:#fb8e28;color:#fff;font-weight:bold;padding:0px 8px 2px">' .
                                 'Connect Reviews' .
                             '</span>' .
                         '</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}
add_filter('plugin_action_links', 'WP_TrustReviews\\plugin_action_links', 10, 2);

/*-------------------------------- Row Meta --------------------------------*/
function plugin_row_meta($input, $file) {
    if ($file != plugin_basename( __FILE__ )) {
        return $input;
    }

    $links = array(
        //'<a href="' . admin_url('admin.php?page=' . Plugin::SLG . '-support') . '" target="_blank">' .
            //__('View Documentation', Plugin::NAME) .
        //'</a>',

        '<a href="' . esc_url('https://trust.reviews') . '" target="_blank">' .
            __('Upgrade to Business', Plugin::NAME) . ' &raquo;' .
        '</a>',

        '<a href="' . esc_url('https://wordpress.org/support/plugin/' . Plugin::NAME . '/reviews/#new-post') . '" target="_blank">' .
            __('Rate plugin', Plugin::NAME) . ' <span style="color:#ffb900;font-size:1.5em;position:relative;top:0.1em;">★★★★★</span>' .
        '</a>',
    );
    $input = array_merge($input, $links);
    return $input;
}
add_filter('plugin_row_meta', 'WP_TrustReviews\\plugin_row_meta', 10, 2);

/*-------------------------------- Plugin init --------------------------------*/
$plugin = new Includes\Plugin();
$plugin->register();

?>