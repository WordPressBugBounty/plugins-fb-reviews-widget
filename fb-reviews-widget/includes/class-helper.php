<?php

namespace WP_TrustReviews\Includes;

class Helper {

    public function json_remote_get($url, $headers = array()) {
        $headers['Accept']       = 'application/json';
        $headers['Content-Type'] = 'application/json';
        $headers['user-agent']   = '';
        $response = wp_remote_get($url, array('headers' => $headers));
        return $this->json_remote_handle($url, $response);
    }

    public function json_remote_post($url, $params = array()) {
        $args = array('headers' => array('Accept' => 'application/json'));
        if (count($params) > 0) {
            $args['body'] = $params;
        }
        $response = wp_remote_post($url, $args);
        return $this->json_remote_handle($url, $response);
    }

    private function json_remote_handle($url, $response) {
        if (!is_wp_error($response)) {
            $headers = wp_remote_retrieve_headers($response);

            if (isset($headers['content-type']) && strpos($headers['content-type'], 'application/json') !== false) {
                $json = json_decode(wp_remote_retrieve_body($response));

                if (isset($json) && isset($json->error) && isset($json->error->message)) {
                    $this->log_error($url . '; ' . $json->error->message);
                }
                return $json;
            }
        } else {
            $error = $response->get_error_message();
            $this->log_error($url . '; ' . $error);
            return json_decode('{"error": "' . $error . '"}');
        }
    }

    public function log_error($error) {
        update_option(Plugin::SLG . '_last_error', round(microtime(true) * 1000) . '; ' . $error);
    }

}