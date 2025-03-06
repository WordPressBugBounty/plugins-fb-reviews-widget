<?php

namespace WP_TrustReviews\Includes;

use WP_TrustReviews\Includes\Core\Core;
use WP_TrustReviews\Includes\Core\Database;

class Builder_Page {

    private $view;
    private $core;
    private $feed_deserializer;

    public function __construct(Feed_Deserializer $feed_deserializer, Core $core, View $view) {
        $this->feed_deserializer = $feed_deserializer;
        $this->core = $core;
        $this->view = $view;
    }

    public function register() {
        add_action(Plugin::SLG . '_admin_page_' . Plugin::SLG . '-builder', array($this, 'init'));
    }

    public function init() {
        if (isset($_GET[Plugin::SLG . '_notice'])) {
            $this->add_admin_notice();
        }

        $feed = null;
        if (isset($_GET[Post_Types::FEED_POST_TYPE . '_id'])) {
            $feed = $this->feed_deserializer->get_feed(sanitize_text_field(wp_unslash($_GET[Post_Types::FEED_POST_TYPE . '_id'])));
        }

        $this->render($feed);
    }

    public function add_admin_notice($notice_code = 0) {

    }

    public function render($feed) {
        global $wp_version;
        if (version_compare($wp_version, '3.5', '>=')) {
            wp_enqueue_media();
        }

        $feed_id = '';
        $feed_post_title = '';
        $feed_content = '';
        $feed_inited = false;
        $businesses = null;
        $reviews = null;

        $rate_us = get_option(Plugin::SLG . '_rate_us');

        if ($feed != null) {
            $feed_id = $feed->ID;
            $feed_post_title = $feed->post_title;
            $feed_content = trim($feed->post_content);

            $data = $this->core->get_reviews($feed, true);
            $businesses = $data['businesses'];
            $reviews = $data['reviews'];
            $options = $data['options'];
            if (isset($businesses) && count($businesses) || isset($reviews) && count($reviews)) {
                $feed_inited = true;
            }
        }

        ob_start();
        ?>
        <div class="{slg}-builder">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php?action=' . Post_Types::FEED_POST_TYPE . '_save')); ?>">
                <?php wp_nonce_field(Plugin::SLG . '_wpnonce'); ?>
                <input type="hidden" id="{slg}_post_id" name="<?php echo Post_Types::FEED_POST_TYPE; ?>[post_id]" value="<?php echo esc_attr($feed_id); ?>">
                <input type="hidden" id="{slg}_current_url" name="<?php echo Post_Types::FEED_POST_TYPE; ?>[current_url]" value="<?php echo home_url($_SERVER['REQUEST_URI']); ?>">
                <div class="{slg}-builder-workspace">
                    <div class="{slg}-toolbar">
                        <div class="{slg}-toolbar-title">
                            <input id="{slg}_title" class="{slg}-toolbar-title-input" type="text" name="<?php echo Post_Types::FEED_POST_TYPE; ?>[title]" value="<?php if (isset($feed_post_title)) { echo $feed_post_title; } ?>" placeholder="Enter a widget name" maxlength="255" autofocus>
                        </div>
                        <div class="{slg}-toolbar-control">
                            <?php if ($feed_inited) { ?>
                            <label>
                                <span id="{slg}_sc_msg">Shortcode </span>
                                <input id="{slg}_sc" type="text" value="[{slg} id=<?php echo esc_attr($feed_id); ?>]" data-shortcode="[{slg} id=<?php echo esc_attr($feed_id); ?>]" onclick="this.select(); document.execCommand('copy'); window.{slg}_sc_msg.innerHTML = 'Shortcode Copied! ';" readonly/>
                            </label>
                            <div class="{slg}-toolbar-options">
                                <label title="Sometimes, you need to use this shortcode in PHP, for instance in header.php or footer.php files, in this case use this option"><input type="checkbox" onclick="var el = window.{slg}_sc; if (this.checked) { el.value = '&lt;?php echo do_shortcode( \'' + el.getAttribute('data-shortcode') + '\' ); ?&gt;'; } else { el.value = el.getAttribute('data-shortcode'); } el.select();document.execCommand('copy'); window.{slg}_sc_msg.innerHTML = 'Shortcode Copied! ';"/>Use in PHP</label>
                            </div>
                            <?php } ?>
                            <button id="{slg}_save" type="submit" class="button button-primary">Save & Update</button>
                        </div>
                    </div>
                    <div class="{slg}-builder-preview">
                        <textarea id="{slg}-builder-connection" name="<?php echo Post_Types::FEED_POST_TYPE; ?>[content]" style="display:none"><?php echo $feed_content; ?></textarea>
                        <div id="{slg}_collection_preview">
                        <?php
                        if ($feed_inited) {
                            echo $this->view->render($feed_id, $businesses, $reviews, $options, true);
                        } else {
                            ?>To show reviews in this preview, firstly connect it on the right menu (CONNECT FACEBOOK) and click
                            '<b>Save & Update</b>' button. Then you can use this created widget on a sidebar or through a shortcode.<?php
                        }
                        ?>
                        </div>
                    </div>
                </div>
                <div id="{slg}-builder-option" class="{slg}-builder-options"></div>
            </form>
        </div>

        <?php if (!$rate_us) { ?>
        <div id="{slg}-rate_us-wrap">
            <div id="{slg}-rate_us">
                <div class="{slg}-rate_us-content">
                    <div class="{slg}-rate_us-head">
                        How's experience with Trust.Reviews?
                    </div>
                    <div class="{slg}-rate_us-body">
                        Rate us clicking on the stars:
                        <?php $this->view->stars(5); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <div id="{slg}-rate_us-feedback" title="Thanks for your feedback!" style="display:none;">
            <b>Please tell us how we can improve the plugin.</b>
            <p style="font-size:16px;">
                <span id="{slg}-rate_us-feedback-stars"></span>
            </p>
            <p style="font-size:16px;">
                <input type="text" value="<?php global $current_user; echo $current_user->user_email; ?>" placeholder="Contact email"/>
            </p>
            <p style="font-size:16px;">
                <textarea autofocus placeholder="Describe your experience and how we can improve that"></textarea>
            </p>
            <button class="{slg}-rate_us-cancel">Cancel</button><button  class="{slg}-rate_us-send">Send</button>
        </div>

        <div id="dialog" title="Google API key required" style="display:none;">
            <p style="font-size:16px;">
                This plugin uses our default <b>Google Places API key which is mandatory for retrieving Google reviews</b> through official way approved by Google (without crawling). Our API key can make 5 requests to Google API for each WordPress server and it's exceeded at the moment.
            </p>
            <p style="font-size:16px;">
                To continue working with Google API and daily reviews refreshing, please create your own API key by <a href="<?php echo admin_url('admin.php?page=' . Plugin::SLG . '-support&' . Plugin::SLG . '_tab=fig#fig_api_key'); ?>" target="_blank">this instruction</a> and save it on the settings page of the plugin.
            </p>
            <p style="font-size:16px;">
                Don’t worry, it will be free because Google is currently giving free credit a month and it should be enough to use the plugin for connecting several Google places and daily refresh of reviews.
            </p>
        </div>

        <script>
            jQuery(document).ready(function($) {
                function builder_init_listener(attempts) {
                    if (!window.TrustReviews || !TrustReviews.Builder) {
                        if (attempts > 0) {
                            setTimeout(function() { builder_init_listener(attempts - 1); }, 200);
                        }
                        return;
                    }
                    window.TrustReviews.Builder($, {
                        slg        : '<?php echo Plugin::SLG; ?>',
                        opt_el     : '#{slg}-builder-option',
                        authcode   : '<?php echo get_option(Plugin::SLG . '_auth_code'); ?>',
                        fbAppUrl   : '<?php echo Plugin::FB_APP_URL; ?>',
                        fbAuthUrl  : '<?php echo Plugin::FB_AUTH_URL; ?>',
                        gAppUrl    : '<?php echo Plugin::G_APP_URL; ?>',
                        supportUrl : '<?php echo admin_url('admin.php?page=' . Plugin::SLG . '-support'); ?>',
                        <?php if (strlen($feed_content) > 0) { echo 'conns: ' . $feed_content; } ?>
                    }).init();
                }
                builder_init_listener(20);
            });
        </script>
        <style>
            .update-nag { display: none; }
        </style>
        <?php
        $this->view->render_svg();
        echo preg_replace('/{slg}/', Plugin::SLG, ob_get_clean());
    }
}
