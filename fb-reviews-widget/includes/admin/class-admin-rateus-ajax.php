<?php

namespace WP_TrustReviews\Includes\Admin;

use WP_TrustReviews\Includes\Plugin;

class Admin_Rateus_Ajax {

    public function __construct() {
        add_action('wp_ajax_' . Plugin::SLG . '_rateus_ajax', array($this, 'rateus_ajax'));
        add_action('wp_ajax_' . Plugin::SLG . '_rateus_ajax_feedback', array($this, 'rateus_ajax_feedback'));
    }

    public function rateus_ajax() {
        $this->check_nonce();

        $rate = trim(sanitize_text_field(wp_unslash($_POST['rate'])));
        update_option(Plugin::SLG . '_rate_us', time() . ':' . $rate);
        echo json_encode(array('rate' => $rate));

        die();
    }

    public function rateus_ajax_feedback() {
        $this->check_nonce();

        $rate  = trim(sanitize_text_field(wp_unslash($_POST['rate'])));
        $email = trim(sanitize_text_field(wp_unslash($_POST['email'])));
        $msg   = trim(sanitize_text_field(wp_unslash($_POST['msg'])));
        update_option(Plugin::SLG . '_rate_us', time() . ':' . $rate);

        $request = wp_remote_post('https://app.trust.reviews/plugins/feedback', array(
            'timeout'   => 15,
            'sslverify' => false,
            'body'      => array(
                'rate'  => $rate,
                'email' => $email,
                'msg'   => $msg
            )
        ));
        echo json_encode(array('rate' => $rate, 'email' => $email, 'msg' => $msg));

        die();
    }

    private function check_nonce() {
        if (!current_user_can('manage_options')) {
            die('The account you\'re logged in to doesn\'t have permission to access this page.');
        }
        check_admin_referer(Plugin::SLG . '_wpnonce');
    }
}
