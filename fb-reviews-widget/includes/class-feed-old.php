<?php

namespace WP_TrustReviews\Includes;

class Feed_Old {

    public static $widget_fields = array(
        'title'                => '',
        'page_id'              => '',
        'page_name'            => '',
        'page_photo'           => '',
        'page_access_token'    => '',
        'rating_count'         => '',
        'text_size'            => '120',
        'dark_theme'           => '',
        'view_mode'            => 'list',
        'pagination'           => '7',
        'disable_user_link'    => '',
        'max_width'            => '',
        'max_height'           => '',
        'hide_based_on'        => false,
        'hide_reviews'         => false,
        'centered'             => false,
        'open_link'            => true,
        'nofollow_link'        => true,
        'show_success_api'     => true,
        'fb_rating_calc'       => false,
        'lazy_load_img'        => true,
        'cache'                => '24',
        'api_ratings_limit'    => 500,
    );

    public function __construct() {
    }

    public function get_feed($id, $params) {
        $feed_content = array();
        $conn = array();
        $opts = array();

        foreach (self::$widget_fields as $variable => $value) {
            if ($variable == 'page_id' || $variable == 'page_name' || $variable == 'page_photo' || $variable == 'page_access_token' || $variable == 'rating_count') {
                $key = str_replace('page_', '', $variable);
                $key = str_replace('reviews_', '', $key);
                $conn[$key] = !isset($params[$variable]) ? self::$widget_fields[$variable] : esc_attr($params[$variable]);
            } else {
                $opts[$variable] = !isset($params[$variable]) ? self::$widget_fields[$variable] : esc_attr($params[$variable]);
            }
        }

        $conn['platform'] = 'facebook';
        $feed_content['connections'] = array($conn);
        $feed_content['options'] = $opts;

        return (object) array('ID' => $id, 'post_content' => json_encode($feed_content));
    }
}
