<?php

namespace WP_TrustReviews\Includes;

use WP_TrustReviews\Includes\Core\Core;

class Feed_Widget extends \WP_Widget {

    public static $static_core;
    public static $static_view;
    public static $static_assets;
    public static $static_feed_deserializer;

    private $core;
    private $view;
    private $assets;
    private $feed_deserializer;

    public function __construct() {
        parent::__construct(
            Plugin::SLG . '_widget',
            __('Trust Reviews Widget', Plugin::NAME),
            array(
                'classname'   => 'trust-reviews-widget',
                'description' => __('Display Google, Facebook and Yelp reviews on your website.', Plugin::NAME),
            )
        );

        $this->core              = self::$static_core;
        $this->view              = self::$static_view;
        $this->assets            = self::$static_assets;
        $this->feed_deserializer = self::$static_feed_deserializer;
    }

    public function widget($args, $instance) {
        if (get_option(Plugin::SLG . '_active') === '0') {
            return;
        }

        if (!isset($instance['feed_id']) || strlen($instance['feed_id']) < 1) {
            return null;
        }

        $feed = $this->feed_deserializer->get_feed($instance['feed_id']);
        if (!$feed) {
            return null;
        }

        $demand_assets = get_option(Plugin::SLG . '_demand_assets');
        if ($demand_assets || $demand_assets == 'true') {
            $this->assets->enqueue_public_styles();
            $this->assets->enqueue_public_scripts();
        }

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
    }

    public function form($instance) {
        $wp_query = new \WP_Query();
        $wp_query->query(array(
            'post_type'      => Post_Types::FEED_POST_TYPE,
            'posts_per_page' => 100,
            'no_found_rows'  => true,
        ));
        $feeds = $wp_query->posts;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Title:', Plugin::SLG); ?>
            </label>
            <input
                type="text"
                id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                class="widefat"
                name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                value="<?php if (isset($instance['title'])) { echo esc_attr($instance['title']); } ?>"
            >
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('feed_id')); ?>">
                <?php esc_html_e('Feed:', Plugin::SLG); ?>
            </label>
            <select
                id="<?php echo esc_attr($this->get_field_id('feed_id')); ?>"
                name="<?php echo esc_attr($this->get_field_name('feed_id')); ?>"
                style="display:block;width:100%"
            >
                <option value="">Select Feed</option>
                <?php foreach ($feeds as $feed) : ?>
                    <option
                        value="<?php echo esc_attr($feed->ID); ?>"
                        <?php if (isset($instance['feed_id'])) { selected($feed->ID, $instance['feed_id']); } ?>
                    >
                        <?php echo esc_html('ID ' . $feed->ID . ': ' . $feed->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title']   = sanitize_text_field($new_instance['title']);
        $instance['feed_id'] = sanitize_text_field($new_instance['feed_id']);
        return $instance;
    }
}
