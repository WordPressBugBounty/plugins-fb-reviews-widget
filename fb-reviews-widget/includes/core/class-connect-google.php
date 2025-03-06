<?php

namespace WP_TrustReviews\Includes\Core;

use WP_TrustReviews\Includes\Plugin;
use WP_TrustReviews\Includes\Helper;

class Connect_Google {

    const GOOGLE_API_URL = 'https://maps.googleapis.com/maps/api/place/';

    const YELP_API_URL = 'https://api.yelp.com/v3/businesses/';

    private $helper;

    public function __construct(Helper $helper) {
        $this->helper = $helper;

        add_action('wp_ajax_' . Plugin::SLG . '_hide_review', array($this, 'hide_review'));
        add_action('wp_ajax_' . Plugin::SLG . '_connect_google', array($this, 'connect_google'));
        add_action('wp_ajax_' . Plugin::SLG . '_connect_yelp', array($this, 'connect_yelp'));
    }

    public function hide_review() {
        global $wpdb;

        if (current_user_can('manage_options')) {
            if (isset($_POST['_wpnonce']) === false) {
                $error = __('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.', Plugin::NAME);
                $response = compact('error');
            } else {
                check_admin_referer(Plugin::SLG . '_wpnonce');

                $review = $wpdb->get_row(
                    $wpdb->prepare(
                        "SELECT * FROM " . $wpdb->prefix . Database::REVIEW_TABLE .
                        " WHERE id = %d", $_POST['id']
                    )
                );

                $hide = $review->hide == '' ? 'y' : '';
                $wpdb->update($wpdb->prefix . Database::REVIEW_TABLE, array('hide' => $hide), array('id' => $_POST['id']));

                // Cache clear
                if ($_POST['feed_id']) {
                    delete_transient(Plugin::SLG . '_feed_' . Plugin::VER . '_' . $_POST['feed_id'] . '_reviews', false);
                } else {
                    $feed_ids = get_option(Plugin::SLG . '_feed_ids');
                    if (!empty($feed_ids)) {
                        $ids = explode(",", $feed_ids);
                        foreach ($ids as $id) {
                            delete_transient(Plugin::SLG . '_feed_' . Plugin::VER . '_' . $id . '_reviews', false);
                        }
                    }
                }

                $response = array('hide' => $hide);
            }
            header('Content-type: text/javascript');
            echo json_encode($response);
            die();
        }
    }

    public function connect_google() {
        if (current_user_can('manage_options')) {
            if (isset($_POST['_wpnonce']) === false) {
                $error = __('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.', Plugin::NAME);
                $response = compact('error');
            } else {
                check_admin_referer(Plugin::SLG . '_wpnonce');

                if (isset($_POST['key'])) {
                    $key = sanitize_text_field(wp_unslash($_POST['key']));
                    if (strlen($key) > 0) {
                        update_option(Plugin::SLG . '_google_api_key', $key);
                    }
                }
                $google_api_key = get_option(Plugin::SLG . '_google_api_key');

                $id = sanitize_text_field(wp_unslash($_POST['id']));
                $lang = sanitize_text_field(wp_unslash($_POST['lang']));
                $local_img = sanitize_text_field(wp_unslash($_POST['local_img']));
                $token = sanitize_text_field(wp_unslash($_POST['token']));

                if ($google_api_key && strlen($google_api_key) > 0) {
                    $url = $this->api_url($id, $google_api_key, $lang);
                } else {
                    $url = 'https://app.trustembed.com/grc/details/json?pid=' . $id . '&token=' . $token .
                           '&siteurl=' . get_option('siteurl') . '&authcode=' . get_option(Plugin::SLG . '_auth_code');

                    /*$url = Plugin::G_APP_URL . '/get/json' .
                           '?siteurl=' . get_option('siteurl') .
                           '&authcode=' . get_option(Plugin::SLG . '_auth_code') .
                           '&pid=' . $id;*/
                    if ($lang && strlen($lang) > 0) {
                        $url = $url . '&lang=' . $lang;
                    }
                }

                $res = wp_remote_get($url);
                $body = wp_remote_retrieve_body($res);
                $body_json = json_decode($body);

                if ($body_json && isset($body_json->result)) {

                    if ($google_api_key && strlen($google_api_key) > 0) {
                        $photo = $this->business_avatar($body_json->result, $google_api_key);
                        $body_json->result->business_photo = $photo;
                    }

                    $this->save_google_reviews($body_json->result, $local_img);

                    $result = array(
                        'id'      => $body_json->result->place_id,
                        'name'    => $body_json->result->name,
                        'photo'   => isset($body_json->result->business_photo) && strlen($body_json->result->business_photo)
                                         ? $body_json->result->business_photo : Plugin::G_BIZ_LOGO(),
                        'reviews' => $body_json->result->reviews
                    );
                    $status = 'success';

                    if ($_POST['feed_id']) {
                        delete_transient(Plugin::SLG . '_feed_' . Plugin::VER . '_' . $_POST['feed_id'] . '_reviews', false);
                    }
                } else {
                    $result = $body_json;
                    $status = 'failed';
                }
                $response = compact('status', 'result');
            }
            header('Content-type: text/javascript');
            echo json_encode($response);
            die();
        }
    }

    function refresh_reviews($args) {

        $pid = $args[0];
        $reviews_lang = $args[1];
        $local_img = isset($args[2]) ? $args[2] : 'false';

        $url = '';
        $google_api_key = get_option(Plugin::SLG . '_google_api_key');
        $api_key_filled = $google_api_key && strlen($google_api_key) > 0;

        if ($api_key_filled) {

            $url = $this->api_url($pid, $google_api_key, $reviews_lang, 'newest');

        } else {

            $url = 'https://app.trustembed.com/grc/details/json?pid=' . $id . '&token=' . $token .
                   '&siteurl=' . get_option('siteurl') . '&authcode=' . get_option(Plugin::SLG . '_auth_code') . '&time=' . time();

            /*$url = Plugin::G_APP_URL . '/update/json' .
                   '?siteurl=' . get_option('siteurl') .
                   '&authcode=' . get_option(Plugin::SLG . '_auth_code') .
                   '&pid=' . $pid .
                   '&time=' . time();*/
            if ($reviews_lang && strlen($reviews_lang) > 0) {
                $url = $url . '&lang=' . $reviews_lang;
            }
        }

        if (strlen($url) > 0) {
            $res = wp_remote_get($url);
            $body = wp_remote_retrieve_body($res);
            $body_json = json_decode($body);

            if ($body_json && isset($body_json->result)) {

                if ($api_key_filled) {
                    $photo = $this->business_avatar($body_json->result, $google_api_key);
                    $body_json->result->business_photo = $photo;
                }

                $this->save_google_reviews($body_json->result, $local_img);
            }
        }
    }

    public function connect_yelp() {
        if (current_user_can('manage_options')) {
            if (isset($_POST['_wpnonce']) === false) {
                $error = __('Unable to call request. Make sure you are accessing this page from the Wordpress dashboard.', Plugin::NAME);
                $response = compact('error');
            } else {
                check_admin_referer(Plugin::SLG . '_wpnonce');

                $id = sanitize_text_field(wp_unslash($_POST['id']));
                $lang = sanitize_text_field(wp_unslash($_POST['lang']));
                $local_img = sanitize_text_field(wp_unslash($_POST['local_img']));

                $yelp_api_key = get_option(Plugin::SLG . '_yelp_api_key');

                if ($yelp_api_key && strlen($yelp_api_key) > 0) {

                    $yelp_biz_url = self::YELP_API_URL . $id;
                    $auth_header = array('Authorization' => 'Bearer ' . $yelp_api_key);

                    $body_json = $this->helper->json_remote_get($yelp_biz_url, $auth_header);

                    $yelp_revs_url = $yelp_biz_url . '/reviews';
                    if (strlen($lang) > 0) {
                        $yelp_revs_url = $yelp_revs_url . '?locale=' . $lang;
                    }

                    $body_json_reviews = $this->helper->json_remote_get($yelp_revs_url, $auth_header);

                    $body_json->photo = $body_json->image_url;
                    $body_json->reviews = $body_json_reviews->reviews;

                } else {
                    $url = Plugin::Y_APP_URL . '/get/json' .
                           '?siteurl=' . get_option('siteurl') .
                           '&authcode=' . get_option(Plugin::SLG . '_auth_code') .
                           '&id=' . $id;
                    if ($lang && strlen($lang) > 0) {
                        $url = $url . '&lang=' . $lang;
                    }

                    $res = wp_remote_get($url);
                    $body = wp_remote_retrieve_body($res);
                    $body_json = json_decode($body);
                }

                if ($body_json && empty($body_json->error_message)) {

                    $this->save_yelp_reviews($body_json, $local_img);

                    $result = array(
                        'id'      => $body_json->id,
                        'name'    => $body_json->name,
                        'photo'   => strlen($body_json->photo) ? $body_json->photo : Plugin::G_BIZ_LOGO(),
                        'reviews' => $body_json->reviews
                    );
                    $status = 'success';

                    if ($_POST['feed_id']) {
                        delete_transient(Plugin::SLG . '_feed_' . Plugin::VER . '_' . $_POST['feed_id'] . '_reviews', false);
                    }
                } else {
                    $result = $body_json;
                    $status = 'failed';
                }
                $response = compact('status', 'result');
            }
            header('Content-type: text/javascript');
            echo json_encode($response);
            die();
        }
    }

    function save_google_reviews($place, $local_img) {
        $place->pid = $place->place_id;
        $place->photo = isset($place->business_photo) ? $place->business_photo : Plugin::G_BIZ_LOGO();
        $place->review_count = $place->user_ratings_total;
        $place->address = isset($place->formatted_address) ? $place->formatted_address : '';
        foreach ($place->reviews as $review) {
            $review->author_img = $review->profile_photo_url;
        }
        $this->save_reviews($place, $local_img, 'google');
    }

    function save_yelp_reviews($place, $local_img) {
        $place->pid = $place->id;
        foreach ($place->reviews as $review) {
            $review->time = strtotime($review->time_created);
            $review->author_name = $review->user->name;
            $review->author_img = $review->user->image_url;
            $review->author_url = $review->user->profile_url;
        }
        $this->save_reviews($place, $local_img, 'yelp');
    }

    function save_reviews($place, $local_img, $platform) {
        global $wpdb;

        $biz_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM " . $wpdb->prefix . Database::BUSINESS_TABLE .
                " WHERE pid = %s", $place->pid
            )
        );

        // Insert or update Google place
        if ($biz_id) {

            // Update Google place
            $update_params = array(
                'name'    => $place->name,
                'rating'  => $place->rating,
                'updated' => round(microtime(true) * 1000),
            );

            $review_count = isset($place->review_count) ? $place->review_count : 0;

            if ($review_count > 0) {
                $update_params['review_count'] = $review_count;
            }
            if (isset($place->photo) && strlen($place->photo) > 0) {
                $update_params['photo'] = $place->photo;
            }
            $wpdb->update($wpdb->prefix . Database::BUSINESS_TABLE, $update_params, array('ID' => $biz_id));

            // Insert Google place rating stats
            $stats = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT rating, review_count FROM " . $wpdb->prefix . Database::STATS_TABLE .
                    " WHERE biz_id = %d ORDER BY id DESC LIMIT 1", $biz_id
                )
            );
            if (count($stats) > 0) {
                if ($stats[0]->rating != $place->rating || ($review_count > 0 && $stats[0]->review_count != $review_count)) {
                    $wpdb->insert($wpdb->prefix . Database::STATS_TABLE, array(
                        'biz_id'       => $biz_id,
                        'time'         => time(),
                        'rating'       => $place->rating,
                        'review_count' => $review_count
                    ));
                }
            } else {
                $wpdb->insert($wpdb->prefix . Database::STATS_TABLE, array(
                    'biz_id'       => $biz_id,
                    'time'         => time(),
                    'rating'       => $place->rating,
                    'review_count' => $review_count
                ));
            }

        } else {

            // Insert Google place
            $place_rating = isset($place->rating) ? $place->rating : null;
            $review_count = isset($place->review_count) ? $place->review_count : (isset($place->reviews) ? count($place->reviews) : null);

            $wpdb->insert($wpdb->prefix . Database::BUSINESS_TABLE, array(
                'pid'          => $place->pid,
                'name'         => $place->name,
                'photo'        => $place->photo,
                'address'      => $place->address,
                'rating'       => $place_rating,
                'url'          => isset($place->url)     ? $place->url     : null,
                'website'      => isset($place->website) ? $place->website : null,
                'review_count' => $review_count,
                'platform'     => $platform,
                'updated'      => round(microtime(true) * 1000)
            ));
            $biz_id = $wpdb->insert_id;

            if ($place_rating > 0) {
                $wpdb->insert($wpdb->prefix . Database::STATS_TABLE, array(
                    'biz_id'       => $biz_id,
                    'time'         => time(),
                    'rating'       => $place_rating,
                    'review_count' => $review_count
                ));
            }
        }

        // Insert or update Google reviews
        if ($place->reviews) {

            $reviews = $place->reviews;

            foreach ($reviews as $review) {
                $google_review_id = 0;
                if (isset($review->author_url) && strlen($review->author_url) > 0) {
                    $where = " WHERE author_url = %s";
                    $where_params = array($review->author_url);
                } elseif (isset($review->author_name) && strlen($review->author_name) > 0) {
                    $where = " WHERE author_name = %s";
                    $where_params = array($review->author_name);
                } else {
                    $where = " WHERE time = %s";
                    $where_params = array($review->time);
                }

                $review_lang = null;
                if (isset($review->language)) {
                    $review_lang = ($review->language == 'en-US' ? 'en' : $review->language);
                    if (strlen($review_lang) > 0) {
                        $where = $where . " AND language = %s";
                        array_push($where_params, $review_lang);
                    }
                }

                if ($biz_id) {
                    $where = $where . " AND biz_id = %d";
                    array_push($where_params, $biz_id);
                }

                $google_review_id = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT id FROM " . $wpdb->prefix . Database::REVIEW_TABLE . $where, $where_params
                    )
                );

                $author_img = null;
                if (isset($review->author_img)) {
                    if ($local_img === true || $local_img == 'true') {
                        $img_name = $place->pid . '_' . md5($review->author_img);
                        $author_img = $this->upload_image($review->author_img, $img_name);
                    } else {
                        $author_img = $review->author_img;
                    }
                }

                if ($google_review_id) {
                    $update_params = array(
                        'rating' => $review->rating,
                        'text'   => $review->text
                    );
                    if ($author_img) {
                        $update_params['author_img'] = $author_img;
                    }
                    $wpdb->update($wpdb->prefix . Database::REVIEW_TABLE, $update_params, array('id' => $google_review_id));
                } else {
                    $wpdb->insert($wpdb->prefix . Database::REVIEW_TABLE, array(
                        'biz_id'      => $biz_id,
                        'rating'      => $review->rating,
                        'text'        => $review->text,
                        'time'        => $review->time,
                        'language'    => $review_lang,
                        'url'         => isset($review->url) ? $review->url : null,
                        'author_name' => $review->author_name,
                        'author_url'  => isset($review->author_url) ? $review->author_url : null,
                        'author_img'  => $author_img,
                        'platform'    => $platform
                    ));
                }
            }
        }
    }

    function api_url($placeid, $google_api_key, $reviews_lang = '', $reviews_sort = '') {
        $url = self::GOOGLE_API_URL . 'details/json?placeid=' . $placeid . '&key=' . $google_api_key;
        if (strlen($reviews_lang) > 0) {
            $url = $url . '&language=' . $reviews_lang;
        }
        if (strlen($reviews_sort) > 0) {
            $url = $url . '&reviews_sort=' . $reviews_sort;
        }
        return $url;
    }

    function business_avatar($response_result_json, $google_api_key) {
        if (isset($response_result_json->photos)) {
            $url = add_query_arg(
                array(
                    'photoreference' => $response_result_json->photos[0]->photo_reference,
                    'key'            => $google_api_key,
                    'maxwidth'       => '300',
                    'maxheight'      => '300',
                ),
                'https://maps.googleapis.com/maps/api/place/photo'
            );
            return $this->upload_image($url, $response_result_json->place_id);
        }
        return null;
    }

    function upload_image($url, $name) {
        $res = wp_remote_get($url, array('timeout' => 8));

        if(is_wp_error($res)) {
            // LOG
            return null;
        }

        $bits = wp_remote_retrieve_body($res);
        $filename = $name . '.jpg';

        $upload_dir = wp_upload_dir();
        $full_filepath = $upload_dir['path'] . '/' . $filename;
        if (file_exists($full_filepath)) {
            wp_delete_file($full_filepath);
        }

        $upload = wp_upload_bits($filename, null, $bits);
        return $upload['url'];
    }

}