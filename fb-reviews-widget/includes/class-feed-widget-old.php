<?php

namespace WP_TrustReviews\Includes;

use WP_TrustReviews\Includes\Core\Core;

class Feed_Widget_Old extends \WP_Widget {

    public static $static_core;
    public static $static_view;
    public static $static_feed_old;

    private $core;
    private $view;
    private $feed_old;

    public function __construct() {
        parent::__construct(
            'fbrev_widget',
            __('Facebook Reviews Widget', Plugin::NAME),
            array(
                'classname'   => 'fb-reviews-widget',
                'description' => __('Display Facebook Reviews on your website.', Plugin::NAME),
            )
        );

        $this->core              = self::$static_core;
        $this->view              = self::$static_view;
        $this->feed_old          = self::$static_feed_old;
    }

    public function widget($args, $instance) {
        if (get_option(Plugin::SLG . '_active') === '0') {
            return;
        }

        if (isset($instance['page_id']) && strlen($instance['page_id']) > 0) {

            $feed = $this->feed_old->get_feed($this->id, $instance);
            $data = $this->core->get_reviews($feed);

            echo $args['before_widget'];

            if (!empty($instance['title'])) {
                echo $args['before_title'] . esc_html($instance['title']) . $args['after_title'];
            }

            $businesses = $data['businesses'];
            $reviews = $data['reviews'];
            $options = $data['options'];
            if (count($businesses) > 0 || count($reviews) > 0) {
                echo $this->view->render($feed->ID, $businesses, $reviews, $options);
            }

            echo $args['after_widget'];

        } else {
            echo __('Facebook Reviews widget is disconnected, please delete this widget, create new one and connect reviews again', Plugin::NAME);
        }
    }

    public function form($instance) {
        ?>
        <p>This is an old Facebook Reviews widget and it does not support after release 2.0.</p>
        <p>Please remove this widget and replace it to <b>Trust Reviews Widget</b>.</p>
        <?php
    }
}
