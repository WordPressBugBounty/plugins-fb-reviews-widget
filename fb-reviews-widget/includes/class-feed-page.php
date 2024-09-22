<?php

namespace WP_TrustReviews\Includes;

class Feed_Page {

    private $feed_deserializer;

    public function __construct(Feed_Deserializer $feed_deserializer) {
        $this->feed_deserializer = $feed_deserializer;
    }

    public function register() {
        add_filter('views_edit-' . Post_Types::FEED_POST_TYPE, array($this, 'render'), 20);
    }

    public function render() {
        $feed_count = $this->feed_deserializer->get_feed_count();
        ?>
        <div class="<?php echo Plugin::SLG; ?>-admin-feeds">
            <a class="button button-primary" href="<?php echo admin_url('admin.php'); ?>?page=<?php echo Plugin::SLG; ?>-builder">Create Widget</a>
            <?php if ($feed_count < 1) { ?>
            <h3 style="display:inline;vertical-align:middle;"> - First of all, create a widget to connect and show reviews through a shortcode or sidebar widget</h3>
            <?php } ?>
        </div>
        <?php
    }
}
