<?php

namespace WP_TrustReviews\Includes\Admin;

use WP_TrustReviews\Includes\Plugin;

class Admin_Rev {

    public function register() {
        add_action('admin_notices', array($this, 'leaverev'));
    }

    public function leaverev() {
        $activation_time = get_option(Plugin::SLG . '_activation_time');

        if ($activation_time == '') {
            $activation_time = time() - 86400*2;
            update_option(Plugin::SLG . '_activation_time', $activation_time);
        }

        $rev_notice = isset($_GET[Plugin::SLG . '_rev_notice']) ? $_GET[Plugin::SLG . '_rev_notice'] : '';
        if ($rev_notice == 'later') {
            $activation_time = time() - 86400*2;
            update_option(Plugin::SLG . '_activation_time', $activation_time);
            update_option(Plugin::SLG . '_rev_notice_hide', 'later');
        } else if ($rev_notice == 'never') {
            update_option(Plugin::SLG . '_rev_notice_hide', 'never');
        }

        $rev_notice_hide = get_option(Plugin::SLG . '_rev_notice_hide');
        $rev_notice_show = get_option(Plugin::SLG . '_rev_notice_show');

        if ($rev_notice_show == '' || $rev_notice_show == Plugin::SLG) {

            if ($rev_notice_hide != 'never' && $activation_time < (time() - 86400*3)) {
                update_option(Plugin::SLG . '_rev_notice_show', Plugin::SLG);
                $class = 'notice notice-info is-dismissible';
                $url = remove_query_arg(array('taction', 'tid', 'sortby', 'sortdir', 'opt'));
                $url_later = esc_url(add_query_arg(Plugin::SLG . '_rev_notice', 'later', $url));
                $url_never = esc_url(add_query_arg(Plugin::SLG . '_rev_notice', 'never', $url));

                $notice = '<p style="font-weight:normal;font-size:15px;">' .
                              'Hey, I am happy to see that you\'ve been using <b>Plugin for Trust.Reviews</b> for a while now – that’s awesome!<br>' .
                              'Could you tell about your site and experience with the plugin in ' .
                              '<a href="https://wordpress.org/support/plugin/' . Plugin::NAME . '/reviews/#new-post" style="color:#ffb900;line-height:90%;font-size:1.5em;letter-spacing:0.03em;position:relative;top:0.08em;text-decoration:none;" target="_blank">★★★★★</a> ' .
                              'WordPress review?<br><br>' .
                              '--<br>Thanks!<br>Daniel K. founder of Trust.Reviews Plugin' .
                          '</p>' .
                          '<p>' .
                              '<a href="https://wordpress.org/support/plugin/' . Plugin::NAME . '/reviews/#new-post" style="text-decoration:none;" target="_blank">' .
                                  '<button class="button button-primary" style="margin-right:5px;">OK, you deserve it</button>' .
                              '</a>' .
                              '<a href="' . $url_later . '" style="text-decoration:none;">' .
                                  '<button class="button button-secondary">Not now, maybe later</button>' .
                              '</a>' .
                              '<a href="' . $url_never . '" style="text-decoration:none;">' .
                                  '<button class="button button-secondary" style="float:right;">Do not remind me again</button>' .
                              '</a>' .
                          '</p>' .
                          '<p style="color:#999;font-size:12px;">' .
                              'By the way, if you have been thinking about upgrading to the ' .
                              '<a href="https://trust.reviews" target="_blank">Business</a> ' .
                              'version, here is a 30% off onboard coupon ->  <b>GRGROW23</b>' .
                          '</p>';

                printf('<div class="%1$s" style="position:fixed;top:50px;right:20px;padding-right:30px;z-index:2;margin-left:20px">%2$s</div>', esc_attr($class), $notice);
            } else {
                update_option(Plugin::SLG . '_rev_notice_show', '');
            }

        }
    }
}
