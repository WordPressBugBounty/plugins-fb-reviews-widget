<?php

namespace WP_TrustReviews\Includes\Admin;

use WP_TrustReviews\Includes\Plugin;

class Admin_Tophead {

    public function register() {
        add_action('wp_after_admin_bar_render', array($this, 'render'));
    }

    public function render() {
        $current_screen = get_current_screen();

        if (empty($current_screen)) {
            return;
        }

        if (strpos($current_screen->id, Plugin::SLG) !== false) {

            $current_screen->render_screen_meta();

            ob_start();
            ?>
            <div class="{slg}-tophead">
                <div class="{slg}-tophead-title">
                    <span style="position:relative;display:inline-block;margin:0 6px 0 0;vertical-align: middle;">
                        <svg viewBox="0 0 1792 1792" width="32" height="32">
                            <path d="M1728 647q0 22-26 48l-363 354 86 500q1 7 1 20 0 21-10.5 35.5t-30.5 14.5q-19 0-40-12l-449-236-449 236q-22 12-40 12-21 0-31.5-14.5t-10.5-35.5q0-6 2-20l86-500-364-354q-25-27-25-48 0-37 56-46l502-73 225-455q19-41 49-41t49 41l225 455 502 73q56 9 56 46z" fill="#FFAF02"></path>
                        </svg>
                        <span style="position:absolute;bottom: 4px;right: 0px;width: 18px;height: 18px;background:#fff;border-radius:50%;border: 1px solid #fff;"></span>
                        <svg width="18" height="18" viewBox="0 0 1792 1792" style="position:absolute;bottom: 5px;right: 1px;border-radius:50%">
                            <path d="M1299 813l-422 422q-19 19-45 19t-45-19l-294-294q-19-19-19-45t19-45l102-102q19-19 45-19t45 19l147 147 275-275q19-19 45-19t45 19l102 102q19 19 19 45t-19 45zm141 83q0-148-73-273t-198-198-273-73-273 73-198 198-73 273 73 273 198 198 273 73 273-73 198-198 73-273zm224 0q0 209-103 385.5t-279.5 279.5-385.5 103-385.5-103-279.5-279.5-103-385.5 103-385.5 279.5-279.5 385.5-103 385.5 103 279.5 279.5 103 385.5z" fill="#8cc976"></path>
                        </svg>
                    </span>
                    trust.reviews
                </div>
                <div class="{slg}-version">
                    <div class="{slg}-version-free">Free Version: <?php echo Plugin::VER; ?></div>
                    <div class="{slg}-version-upgrade">
                        <span>Upgrade to business</span>
                        <div id="{slg}-upgrade-tips">
                            <div class="{slg}-upgrade-head">Most easiest way to show all Google and Facebook reviews with business version</div>
                            <a href="https://trust.reviews" target="_blank">Upgrade</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            echo preg_replace('/{slg}/', Plugin::SLG, ob_get_clean());
        }
    }
}
