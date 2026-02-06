<?php

namespace WP_TrustReviews\Includes\Core;

use WP_TrustReviews\Includes\Plugin;
use WP_TrustReviews\Includes\Core\Database;

class Google_Dao {

    private $helper;

    public function __construct(Connect_Helper $helper) {
        $this->helper = $helper;
    }

    public function save($place, $local_img = true) {
        global $wpdb;

        $log = array(round(microtime(true) * 1000));
        update_option(Plugin::SLG . '_last_error', '');

        $db_biz_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM " . $wpdb->prefix . Database::BUSINESS_TABLE . " WHERE pid = %s AND platform = %s LIMIT 1", $place->pid, $place->provider
            )
        );

        // Insert or update Google place
        if ($db_biz_id) {
            $this->update_place($place, $db_biz_id, $local_img, $log);
        } else {
            $db_biz_id = $this->insert_place($place, $local_img, $log);
        }
        $this->log_last_error($wpdb);

        // Insert or update Google reviews
        if (isset($place->reviews)) {

            $reviews = $place->reviews;

            foreach ($reviews as $review) {
                $db_review_id = 0;

                $rid = $this->review_id($place->pid, $review);
                if (empty($rid)) continue;

                if (!empty($rid)) {
                    $db_review_id = $wpdb->get_var(
                        $wpdb->prepare(
                            "SELECT id FROM {$wpdb->prefix}" . Database::REVIEW_TABLE . " WHERE biz_id = %d AND platform = %s AND review_id = %s LIMIT 1",
                            $db_biz_id,
                            $review->provider,
                            $rid
                        )
                    );
                }

                $review_lang = null;
                if (isset($review->language)) {
                    $review_lang = ($review->language == 'en-US' ? 'en' : $review->language);
                }

                $db_review_id = empty($db_review_id) ? $this->old_review_id($db_biz_id, $review, $review_lang) : $db_review_id;

                $author_img = null;
                if (isset($review->author_img)) {
                    if ($local_img === true || $local_img == 'true') {
                        $img_name = $place->pid . '_' . md5($review->author_img);
                        $author_img = $this->helper->upload_image($review->author_img, $img_name);
                    } else {
                        $author_img = $review->author_img;
                    }
                }

                $images = null;
                if (isset($review->images) && count($review->images) > 0) {
                    if ($local_img === true || $local_img == 'true') {
                        $saved_imgs = [];
                        foreach ($review->images as $img) {
                            $img_name = $place->pid . '_img_' . md5($img);
                            $saved_img = $this->helper->upload_image($img, $img_name);
                            array_push($saved_imgs, $saved_img);
                        }
                        $images = implode(';', $saved_imgs);
                    } else {
                        $images = implode(';', $review->images);
                    }
                }

                $reply = null;
                $reply_time = null;
                if (isset($review->reply) && strlen($review->reply) > 0) {
                    $reply = $review->reply;
                    $reply_time = $review->reply_time;
                }

                if ($db_review_id) {
                    $this->update_review($place->pid, $review, $review_lang, $author_img, $images, $reply, $reply_time, $db_review_id, $log);
                } else {
                    $this->insert_review($place->pid, $review, $review_lang, $author_img, $images, $reply, $reply_time, $db_biz_id, $log);
                }
                $this->log_last_error($wpdb);
            }
        }

        update_option('grw_save_log', implode('_', $log));
    }

    private function old_review_id($db_biz_id, $review, $lang) {
        global $wpdb;

        $where = " WHERE";
        $where_params = array();

        if (!empty($review->provider)) {
            $where .= " platform = %s AND";
            array_push($where_params, $review->provider);
        }

        if (!empty($review->author_url)) {
            $where .= " author_url = %s";
            array_push($where_params, $review->author_url);
        } else {
            $where .= " time = %s";
            array_push($where_params, $review->time);
            if (!empty($review->author_name)) {
                $where .= " AND author_name = %s";
                array_push($where_params, $review->author_name);
            }
        }

        if (!empty($lang)) {
            $where .= " AND language = %s";
            array_push($where_params, $lang);
        }

        if ($db_biz_id) {
            $where .= " AND biz_id = %d";
            array_push($where_params, $db_biz_id);
        }

        $sql = "SELECT id FROM " . $wpdb->prefix . Database::REVIEW_TABLE . $where/* . " ORDER BY id DESC LIMIT 1"*/;
        return $wpdb->get_var($wpdb->prepare($sql, $where_params));
    }

    public function insert_place($place, $local_img, &$log = []) {
        global $wpdb;

        // Insert Google place
        $pid = $place->pid;
        $name = $place->name;
        $rating = isset($place->rating) ? $place->rating : null;
        $review_count = isset($place->user_ratings_total) ? $place->user_ratings_total : (isset($place->reviews) ? count($place->reviews) : null);

        $atts = array(
            'pid'     => $pid,
            'rating'       => $rating,
            'review_count' => $review_count,
            'name'         => $name,
            'photo'        => $this->get_place_photo($place, $local_img),
            'url'          => isset($place->url) ? $place->url     : null,
            'website'      => isset($place->website) ? $place->website : null,
            //'icon'         => isset($place->icon) ? $place->icon : null,
            'address'      => isset($place->formatted_address) ? $place->formatted_address : null,
            'platform'     => $place->provider,
            'updated'      => round(microtime(true) * 1000)
        );
        if (!empty($place->map_url)) {
            $atts['map_url'] = $place->map_url;
        }

        $wpdb->insert($wpdb->prefix . Database::BUSINESS_TABLE, $atts);
        $db_biz_id = $wpdb->insert_id;

        array_push($log, 'ip[' . $pid . ',' . $name . ',' . $rating . ',' . $review_count . ']');

        if ($rating > 0) {
            $this->insert_stats($rating, $review_count, $db_biz_id);
        }

        return $db_biz_id;
    }

    public function update_place($place, $db_biz_id, $local_img, &$log = []) {
        global $wpdb;

        $name = $place->name;
        $rating = $place->rating;

        // Update Google place name and rating
        $update_params = array(
            'name'    => $name,
            'rating'  => $rating,
            'updated' => round(microtime(true) * 1000),
        );

        // Update total reviews
        $review_count = isset($place->user_ratings_total) ? $place->user_ratings_total : 0;
        if ($review_count > 0) {
            $update_params['review_count'] = $review_count;
        }

        // Update business photo
        if (!empty($place->business_photo)) {
            $update_params['photo'] = $this->get_place_photo($place, $local_img);
        }

        // Update map URL
        if (!empty($place->map_url)) {
            $update_params['map_url'] = $place->map_url;
        }

        $wpdb->update($wpdb->prefix . Database::BUSINESS_TABLE, $update_params, array('id' => $db_biz_id));

        array_push($log, 'up[' . $db_biz_id . ',' . $name . ',' . $rating . ',' . $review_count . ']');

        $this->update_stats($place->rating, $review_count, $db_biz_id);
    }

    public function get_place_photo($place, $local_img) {
        $photo = null;
        if (!empty($place->business_photo)) {
            if ($local_img === true || $local_img == 'true') {
                $photo = $this->helper->upload_image($place->business_photo, $place->pid);
            } else {
                $photo = $place->business_photo;
            }
        }
        return $photo;
    }

    public function update_stats($rating, $review_count, $db_biz_id) {
        global $wpdb;

        // Insert Google place rating stats
        $stats = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT rating, review_count FROM " . $wpdb->prefix . Database::STATS_TABLE . " WHERE biz_id = %d ORDER BY id DESC LIMIT 1", $db_biz_id
            )
        );
        if (count($stats) > 0) {
            if ($stats[0]->rating != $rating || ($review_count > 0 && $stats[0]->review_count != $review_count)) {
                $this->insert_stats($rating, $review_count, $db_biz_id);
            }
        } else {
            $this->insert_stats($rating, $review_count, $db_biz_id);
        }
    }

    public function insert_stats($rating, $review_count, $db_biz_id) {
        global $wpdb;

        $wpdb->insert($wpdb->prefix . Database::STATS_TABLE, array(
            'biz_id'       => $db_biz_id,
            'time'         => time(),
            'rating'       => $rating,
            'review_count' => $review_count
        ));
        $this->log_last_error($wpdb);
    }

    public function update_review($pid, $review, $review_lang, $author_img, $images, $reply, $reply_time, $db_review_id, &$log = []) {
        global $wpdb;

        $update_params = array(
            'review_id' => $this->review_id($pid, $review),
            'rating'    => $review->rating,
            'text'      => $review->text
        );
        if ($author_img) {
            $update_params['author_img'] = $author_img;
        }
        if ($images) {
            $update_params['images'] = $images;
        }
        if ($reply) {
            $update_params['reply'] = $reply;
            $update_params['reply_time'] = $reply_time;
        }
        if (isset($review->url)) {
            $update_params['url'] = $review->url;
        }

        $wpdb->update($wpdb->prefix . Database::REVIEW_TABLE, $update_params, array('id' => $db_review_id));

        $this->upsert_review_text($pid, $review, $review_lang);

        array_push(
            $log,
            'ur[' .
                $db_review_id . ',' .
                $review->author_name . ',' .
                $review->rating . ',' .
                (isset($review->text) ? strlen($review->text) : '') . ',' .
                $review_lang .
            ']'
        );
    }

    public function insert_review($pid, $review, $review_lang, $author_img, $images, $reply, $reply_time, $db_biz_id, &$log = []) {
        global $wpdb;

        $wpdb->insert($wpdb->prefix . Database::REVIEW_TABLE, array(
            'biz_id'            => $db_biz_id,
            'review_id'         => $this->review_id($pid, $review),
            'rating'            => $review->rating,
            'text'              => $review->text,
            'time'              => $review->time,
            'language'          => $review_lang,
            'author_name'       => $review->author_name,
            'author_url'        => isset($review->author_url) ? $review->author_url : null,
            'author_img' => $author_img,
            'url'               => isset($review->url) ? $review->url : null,
            'platform'          => $review->provider,
            'images'            => $images,
            'reply'             => $reply,
            'reply_time'        => $reply_time
        ));

        $db_review_id = $wpdb->insert_id;

        $this->upsert_review_text($pid, $review, $review_lang);

        array_push(
            $log,
            'ir[' .
                $review->author_name . ',' .
                $review->rating . ',' .
                (isset($review->text) ? strlen($review->text) : '') . ',' .
                $review_lang .
            ']'
        );
    }

    private function upsert_review_text($pid, $review, $lang) {
        global $wpdb;

        if (empty($lang)) return;
        if (!isset($review->text) || $review->text === '') return;
        if (empty($review_id = $this->review_id($pid, $review))) return;

        $wpdb->query($wpdb->prepare('INSERT INTO ' . $wpdb->prefix . Database::TEXT_TABLE . ' (review_id, lang, text) ' .
            'VALUES (%s, %s, %s) ON DUPLICATE KEY UPDATE text = VALUES(text)', $review_id, $lang, $review->text));

        $this->log_last_error($wpdb);
    }

    private function review_id($pid, $review) {
        if (empty($review->provider)) return;
        if (!empty($review->id)) return $review->provider . ':' . $review->id;
        if (empty($pid)) return;

        $time = isset($review->time) ? (string)$review->time : '';

        return md5(
            $review->provider . ':' . $pid . ':' .
            (empty($review->author_url) ?
            (empty($review->author_name) ? $time : $review->author_name . ':' . $time) :
            $review->author_url)
        );
    }

    private function log_last_error($wpdb) {
        if (!empty($wpdb->last_error)) {
            $last_error = $wpdb->last_error;
            $opt = get_option('grw_last_error');
            if (empty($opt)) {
                $now = floor(microtime(true) * 1000);
                $opt = $now . ': ';
            }
            update_option('grw_last_error', $opt . $last_error . '; ');
        }
    }
}