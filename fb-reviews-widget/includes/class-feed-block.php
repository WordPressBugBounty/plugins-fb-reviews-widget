<?php

namespace WP_TrustReviews\Includes;

use WP_TrustReviews\Includes\Core\Core;

class Feed_Block {

    private $core;
    private $view;
    private $assets;
    private $feed_deserializer;

    public function __construct(Feed_Deserializer $feed_deserializer, Core $core, View $view, Assets $assets) {
        $this->core = $core;
        $this->view = $view;
        $this->assets = $assets;
        $this->feed_deserializer = $feed_deserializer;
    }

    public function register() {
        add_action('init', [$this, 'register_block'], 999);
        add_action('block_categories_all', [$this, 'register_category']);
    }

    public function register_block() {

        $assets = require(Plugin::PATH() . 'build/index.asset.php');

        wp_register_script(
            Plugin::SLG . '-reviews-block-js',
            plugins_url('build/index.js', TRUSTREVIEWS_PLUGIN_FILE),
            array('wp-block-editor', 'wp-blocks'),
            $this->assets->version()
        );

        wp_localize_script(Plugin::SLG . '-reviews-block-js', 'TrustreviewsBlockData', array(
            'slg'        => Plugin::SLG,
            'feeds'      => $this->feed_deserializer->get_all_feeds_short(),
            'builderUrl' => admin_url('admin.php?page=' . Plugin::SLG . '-builder')
        ));

        register_block_type(Plugin::PATH(), [
            'editor_script'   => Plugin::SLG . '-reviews-block-js',
            'render_callback' => [$this, 'render'],
            'attributes'      => ['id' => ['type' => 'string']]
        ]);
    }

    public function register_category($cats) {
        return array_merge($cats, [['slug' => Plugin::SLG, 'title' => 'Trust Reviews Block']]);
    }

    public function render($atts) {
        if (isset($atts['id'])) {

            $feed = $this->feed_deserializer->get_feed($atts['id']);
            if (!$feed) {
                return null;
            }

            $data = $this->core->get_reviews($feed);
            return $this->view->render($feed->ID, $data['businesses'], $data['reviews'], $data['options']);
        }
    }
}
