<?php

namespace WP_TrustReviews\Includes;

use WP_TrustReviews\Includes\Core\Core;

class Feed_Shortcode {

    private $core;
    private $view;
    private $assets;
    private $feed_old;
    private $feed_deserializer;

    public function __construct(Feed_Deserializer $feed_deserializer, Assets $assets, Core $core, View $view, Feed_Old $feed_old) {
        $this->feed_deserializer = $feed_deserializer;
        $this->assets            = $assets;
        $this->core              = $core;
        $this->view              = $view;
        $this->feed_old          = $feed_old;
    }

    public function register() {
        add_shortcode(Plugin::SLG, array($this, 'init'));

        // Support old shortcodes
        add_shortcode('fbrev', array($this, 'fbrev'));
    }

    public function init($atts) {
        if (get_option(Plugin::SLG . '_active') === '0') {
            return '';
        }

        $atts = shortcode_atts(array('id' => 0), $atts, Plugin::SLG);
        $feed = $this->feed_deserializer->get_feed($atts['id']);

        if (!$feed) {
            return null;
        }

        $demand_assets = get_option(Plugin::SLG . '_demand_assets');
        if ($demand_assets || $demand_assets == 'true') {
            $this->assets->enqueue_public_styles();
            $this->assets->enqueue_public_scripts();
        }

        $data = $this->core->get_reviews($feed);
        return $this->view->render($feed->ID, $data['businesses'], $data['reviews'], $data['options']);
    }

    public function fbrev($atts) {
        if (get_option(Plugin::SLG . '_active') === '0') {
            return '';
        }

        $feed = $this->feed_old->get_feed($atts['page_id'], $atts);
        $data = $this->core->get_reviews($feed);
        return $this->view->render($feed->ID, $data['businesses'], $data['reviews'], $data['options']);
    }
}
