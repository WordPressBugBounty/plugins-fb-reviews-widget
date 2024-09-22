<?php

namespace WP_TrustReviews\Includes\Core;

use WP_TrustReviews\Includes\Plugin;

class Core {

    public function __construct() {
    }

    public static function get_default_options() {
        return array(
            'view_mode'                 => 'list',
            'pagination'                => '10',
            'text_size'                 => '',
            'disable_user_link'         => false,
            'hide_based_on'             => false,
            'hide_writereview'          => false,
            'hide_reviews'              => false,
            'hide_avatar'               => false,
            'hide_backgnd'              => false,
            'show_round'                => false,
            'show_shadow'               => false,

            'slider_autoplay'           => true,
            'slider_hide_border'        => false,
            'slider_hide_prevnext'      => false,
            'slider_hide_dots'          => false,
            'slider_text_height'        => '',
            'slider_speed'              => 5,

            'header_merge_social'       => false,
            'header_hide_social'        => false,
            'header_center'             => false,
            'header_hide_photo'         => false,
            'header_hide_name'          => false,

            'dark_theme'                => false,
            'centered'                  => false,
            'max_width'                 => '',
            'max_height'                => '',

            'open_link'                 => true,
            'nofollow_link'             => true,
            'lazy_load_img'             => true,
            'google_def_rev_link'       => false,
            'reviewer_avatar_size'      => 56,
            'reviews_limit'             => '',
            'cache'                     => 12,
        );
    }

    public function get_reviews($feed, $is_admin = false) {
        $connection = json_decode($feed->post_content);

        if ($is_admin) {
            return $this->get_data($connection, $is_admin);
        }

        $cache_time            = isset($connection->options->cache) ? $connection->options->cache : null;
        $data_cache_key        = Plugin::SLG . '_feed_' . Plugin::VER . '_' . $feed->ID . '_reviews';
        $connection_cache_key  = Plugin::SLG . '_feed_' . Plugin::VER . '_' . $feed->ID . '_options';

        $data                  = get_transient($data_cache_key);
        $cached_connection     = get_transient($connection_cache_key);
        $serialized_connection = serialize($connection);

        if ($data === false || $serialized_connection !== $cached_connection || !$cache_time) {
            $expiration = $cache_time;
            switch ($expiration) {
                case '1':
                    $expiration = 3600;
                    break;
                case '3':
                    $expiration = 3600 * 3;
                    break;
                case '6':
                    $expiration = 3600 * 6;
                    break;
                case '12':
                    $expiration = 3600 * 12;
                    break;
                case '24':
                    $expiration = 3600 * 24;
                    break;
                case '48':
                    $expiration = 3600 * 48;
                    break;
                case '168':
                    $expiration = 3600 * 168;
                    break;
                default:
                    $expiration = 3600 * 24;
            }
            $data = $this->get_data($connection, $is_admin);
            set_transient($data_cache_key, $data, $expiration);
            set_transient($connection_cache_key, $serialized_connection, $expiration);
        }
        return $data;
    }

    public function get_data($connection, $is_admin = false) {

        if ($connection == null) {
            return null;
        }

        foreach ($this->get_default_options() as $field => $value) {
            $connection->options->{$field} = isset($connection->options->{$field}) ? esc_attr($connection->options->{$field}) : $value;
        }
        $options = $connection->options;

        /*if (isset($connection->connections) && is_array($connection->connections)) {
            $google_business = null;

        } else {
            $google_business = isset($connection->google) ? $connection->google : null;
        }

        $google_biz = array();
        $google_reviews = array();

        if ($google_business != null) {

            foreach ($google_business as $biz) {

                $result = $this->get_google_reviews($biz, $is_admin);
                array_push($google_biz, $result['business']);
                $google_reviews = array_merge($google_reviews, $result['reviews']);

                if (isset($biz->refresh) && $biz->refresh) {
                    $args = array($biz->id);
                    if (isset($biz->lang) && strlen($biz->lang) > 0) {
                        array_push($args, $biz->lang);
                    }
                }
            }
        }*/

        $bizs = [];
        $reviews = [];

        foreach ($connection->connections as $conn) {
            switch ($conn->platform) {
                case 'facebook':
                    $result = $this->get_fb_reviews($conn, $options);
                    break;
                default:
                    $result = $this->get_db_reviews($conn, $is_admin);
            }
            if (isset($result['business'])) {
                array_push($bizs, $result['business']);
                $reviews = array_merge($reviews, $result['reviews']);
            }
        }

        usort($reviews, array($this, 'sort_recent'));

        //$reviews = array();
        if (!$options->hide_reviews) {

            /*$revs = array();
            if (count($google_reviews) > 0) {
                array_push($revs, $google_reviews);
            }

            // Sorting
            while (count($revs) > 0) {
                foreach ($revs as $i => $value) {
                    $review = array_shift($revs[$i]);
                    array_push($reviews, $review);
                    if (count($revs[$i]) < 1) {
                        unset($revs[$i]);
                    }
                }
            }

            // Normalize reviews array indexes after unset filter above
            $reviews = array_values($reviews);*/

            // Trim reviews limit
            /*if ($options->reviews_limit > 0) {
                $reviews = array_slice($google_reviews, 0, $options->reviews_limit);
            }*/
        }

        return array('businesses' => $bizs, 'reviews' => $reviews, 'options' => $options);
    }

    public function get_db_reviews($biz, $is_admin = false) {
        global $wpdb;

        $rating = 0;
        $review_count = 0;
        $revs = [];

        // Get place
        $place = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . Database::BUSINESS_TABLE .
                " WHERE pid = %s", $biz->id
            )
        );

        if ($place) {

            // Get reviews
            $reviews_where = $is_admin ? '' : ' AND hide = \'\'';

            $reviews_lang = strlen($biz->lang) > 0 ? $biz->lang : 'en';
            $reviews_where = $reviews_where . ' AND (language = \'' . $reviews_lang . '\' OR language = \'\' OR language IS NULL)';

            $revs = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM " . $wpdb->prefix . Database::REVIEW_TABLE .
                    " WHERE biz_id = %d" . $reviews_where . " ORDER BY time DESC", $place->id
                )
            );

            // Setup photo
            $place->photo = strlen($biz->photo) > 0 ? $biz->photo : (strlen($place->photo) > 0 ? $place->photo : Plugin::G_BIZ_LOGO());

            // Calculate reviews count
            if (isset($place->review_count) && $place->review_count > 0) {
                $review_count = $place->review_count;
            } else {
                $review_count = $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT count(*) FROM " . $wpdb->prefix . Database::REVIEW_TABLE .
                        " WHERE biz_id = %d", $place->id
                    )
                );
            }

            // Calculate rating
            $rating = 0;
            if ($place->rating > 0) {
                $rating = $place->rating;
            } else if (count($revs) > 0) {
                foreach ($revs as $review) {
                    $rating = $rating + $review->rating;
                }
                $rating = round($rating / count($revs), 1);
            }
            $rating = number_format((float)$rating, 1, '.', '');
        }

        $business = json_decode(json_encode(
            array(
                'id'                  => $biz->id,
                'name'                => $biz->name ? $biz->name : $place->name,
                'url'                 => isset($place->url) ? $place->url : null,
                'photo'               => isset($place->photo) ? $place->photo : Plugin::G_BIZ_LOGO(),
                'address'             => isset($place->address) ? $place->address : null,
                'rating'              => $rating,
                'review_count'        => $review_count,
                'provider'            => $place->platform
            )
        ));

        $reviews = array();
        foreach ($revs as $rev) {
            $review = json_decode(json_encode(
                array(
                    'id'          => $rev->id,
                    'hide'        => $rev->hide,
                    'biz_id'      => $biz->id,
                    'biz_url'     => $place->url,
                    'rating'      => $rev->rating,
                    'text'        => wp_encode_emoji($rev->text),
                    'author_img'  => $rev->author_img,
                    'author_url'  => $rev->author_url,
                    'author_name' => $rev->author_name,
                    'time'        => $rev->time,
                    'provider'    => $rev->platform
                )
            ));
            array_push($reviews, $review);
        }

        return array('business' => $business, 'reviews' => $reviews);
    }

    public function get_fb_reviews($biz, $opts, $zzz = true) {
        global $wpdb;

        $business = null;
        $reviews = array();

        $limit = isset($opts->fb_api_limit) && strlen($opts->fb_api_limit) > 0 ? $opts->fb_api_limit : 50;

        // Support old requests
        if (isset($biz->access_token) && !empty($biz->access_token)) {
            $api_url = Plugin::FB_GURL . "v16.0/" . $biz->id . "?access_token=" . $biz->access_token . "&fields=ratings.fields(reviewer{id,name,picture.width(120).height(120)},created_time,rating,recommendation_type,review_text,open_graph_story{id}).limit(" . $limit . "),overall_star_rating,rating_count";
        } else {
            $api_url = Plugin::FB_APP_URL . "/ratings?id=" . $biz->id . "&auth_code=" . get_option(Plugin::SLG . '_auth_code') . "&limit=" . $limit;
        }

        $res = wp_remote_get($api_url);
        $body = wp_remote_retrieve_body($res);
        $json = json_decode($body);

        // Error handling
        if (isset($json->error)) {
            return array('error' => $json->error);
        }

        $fb_rating = 0;
        $fb_count = 0;

        if (isset($json->ratings) && isset($json->ratings->data)) {
            $fb_reviews = $json->ratings->data;
            $fb_count = count($fb_reviews);
            if ($fb_count > 0) {
                foreach ($fb_reviews as $fb_rev) {
                    $fb_review_rating = $this->get_fb_rating($fb_rev);
                    $fb_rating = $fb_rating + $fb_review_rating;
                    $rev = json_decode(json_encode(
                        array(
                            'hide'        => '',
                            'biz_id'      => $biz->id,
                            'biz_url'     => Plugin::FB_URL . $biz->id,
                            'rating'      => $fb_review_rating,
                            'text'        => isset($fb_rev->review_text) ? wp_encode_emoji(str_replace("\n", '<br>', $fb_rev->review_text)) : '',
                            'author_img'  => isset($fb_rev->reviewer->picture) ? $fb_rev->reviewer->picture->data->url : Plugin::FB_URL,
                            'author_url'  => Plugin::FB_URL . (isset($fb_rev->open_graph_story) ? $fb_rev->open_graph_story->id : $biz->id.'/reviews'),
                            'author_name' => isset($fb_rev->reviewer->name) ? $fb_rev->reviewer->name : '',
                            'time'        => strtotime($fb_rev->created_time),
                            'provider'    => 'facebook',
                        )
                    ));
                    array_push($reviews, $rev);
                }
                $fb_rating = round($fb_rating / $fb_count, 1);
                $fb_rating = number_format((float)$fb_rating, 1, '.', '');
            }
        }

        if (isset($json->overall_star_rating)) {
            $fb_rating = number_format((float)$json->overall_star_rating, 1, '.', '');
        }
        if (isset($json->rating_count) && $json->rating_count > 0) {
            $fb_count = $json->rating_count;
        }
        if (isset($biz->rating_count) && $biz->rating_count > 0) {
            $fb_count += $biz->rating_count;
        }

        $business = json_decode(json_encode(
            array(
                'id'           => $biz->id,
                'name'         => $biz->name,
                'photo'        => strlen($biz->photo) > 0 ? $biz->photo : Plugin::FB_GURL . $biz->id . '/picture',
                'url'          => Plugin::FB_URL . $biz->id,
                'rating'       => $fb_rating,
                'review_count' => $fb_count,
                'provider'     => 'facebook'
            )
        ));

        if ($zzz) {
            // Database information
            $biz_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM " . $wpdb->prefix . Database::BUSINESS_TABLE .
                    " WHERE pid = %s", $biz->id
                )
            );

            if ($biz_id) {

                // Update Google place
                $update_params = array(
                    'name'    => $business->name,
                    'rating'  => $fb_rating,
                    'updated' => round(microtime(true) * 1000),
                );

                if ($fb_count > 0) {
                    $update_params['review_count'] = $fb_count;
                }
                if (isset($business->photo) && strlen($business->photo) > 0) {
                    $update_params['photo'] = $business->photo;
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
                    if ($stats[0]->rating != $fb_rating || ($fb_count > 0 && $stats[0]->review_count != $fb_count)) {
                        $wpdb->insert($wpdb->prefix . Database::STATS_TABLE, array(
                            'biz_id'       => $biz_id,
                            'time'         => time(),
                            'rating'       => $fb_rating,
                            'review_count' => $fb_count
                        ));
                    }
                } else {
                    $wpdb->insert($wpdb->prefix . Database::STATS_TABLE, array(
                        'biz_id'       => $biz_id,
                        'time'         => time(),
                        'rating'       => $fb_rating,
                        'review_count' => $fb_count
                    ));
                }
            } else {
                $wpdb->insert($wpdb->prefix . Database::BUSINESS_TABLE, array(
                    'pid'          => $business->id,
                    'name'         => $business->name,
                    'photo'        => $business->photo,
                    'url'          => $business->url,
                    'rating'       => $fb_rating,
                    'review_count' => $fb_count,
                    'platform'     => 'facebook',
                    'updated'      => round(microtime(true) * 1000)
                ));
                $biz_id = $wpdb->insert_id;

                if ($fb_rating > 0) {
                    $wpdb->insert($wpdb->prefix . Database::STATS_TABLE, array(
                        'biz_id'       => $biz_id,
                        'time'         => time(),
                        'rating'       => $fb_rating,
                        'review_count' => $fb_count
                    ));
                }
            }
        }

        return array('business' => $business, 'reviews' => $reviews);
    }

    public function get_fb_rating($review) {
        if (isset($review->rating)) {
            return $review->rating;
        } elseif (isset($review->recommendation_type)) {
            return ($review->recommendation_type == 'negative' ? 1 : 5);
        } else {
            return 5;
        }
    }

    public function get_overview($pid = 0) {
        global $wpdb;

        // -------------- Get Google place --------------
        $place_sql = "SELECT id, pid, name, photo, rating, review_count, platform, updated" .
                     " FROM " . $wpdb->prefix . Database::BUSINESS_TABLE .
                     " WHERE rating > 0 AND review_count > 0" . ($pid > 0 ? ' AND id = %d' : '') .
                     " ORDER BY id DESC";

        $places = $pid > 0 ?
                  // Query for specific Google place
                  $wpdb->get_results($wpdb->prepare($place_sql, sanitize_text_field(wp_unslash($pid)))) :
                  // Query for summary (all places)
                  $wpdb->get_results($place_sql);

        $count = count($places);
        if ($count < 1) {
            return null;
        }

        $rating = 0;
        $review_count = 0;
        $google_places = array();
        $biz_ids = array();

        $fb_reviews;

        foreach ($places as $place) {
            $id = $place->id;
            $name = $place->name;
            $rating += $place->rating;
            $review_count += $place->review_count;

            array_push($biz_ids, $place->id);
            array_push($google_places, json_decode(json_encode(array('id' => $place->id, 'name' => $place->name, 'updated' => $place->updated))));

            if ($place->platform == 'facebook') {

                // TODO workaround !!!
                $place->id = $place->pid;

                $fb_reviews = $this->get_fb_reviews($place, [], false);
            }
        }

        if ($count > 1) {
            $rating = round($rating / $count, 1);
            $rating = number_format((float)$rating, 1, '.', '');
            array_unshift($google_places, json_decode(json_encode(array('id' => 0, 'name' => 'Summary for all places'))));
        }

        // -------------- Get Google reviews --------------
        $google_reviews = array();

        $reviews = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . Database::REVIEW_TABLE .
                " WHERE biz_id IN (" . implode(', ', array_fill(0, count($biz_ids), '%d')) . ")" .
                " ORDER BY time DESC LIMIT 10",
                $biz_ids
            )
        );

        // Merge different reviews
        if (isset($fb_reviews['reviews'])) {
            $reviews = array_merge($reviews, $fb_reviews['reviews']);
            usort($reviews, array($this, 'sort_recent'));
        }

        foreach ($reviews as $rev) {
            $review = json_decode(json_encode(
                array(
                    'id'            => isset($rev->id) ? $rev->id : 0,
                    'hide'          => $rev->hide,
                    'rating'        => $rev->rating,
                    'text'          => wp_encode_emoji($rev->text),
                    'author_url'    => $rev->author_url,
                    'author_name'   => $rev->author_name,
                    'time'          => $rev->time,
                    'provider'      => 'google'
                )
            ));
            array_push($google_reviews, $review);
        }

        // -------------- Get Google stats --------------
        $stats = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM " . $wpdb->prefix . Database::STATS_TABLE .
                " WHERE biz_id IN (" . implode(', ', array_fill(0, count($biz_ids), '%d')) . ")" .
                " ORDER BY id DESC LIMIT 10000",
                $biz_ids
            )
        );

        // -------------- Get min/max stats values --------------
        $stats_minmax = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT t1.* FROM " . $wpdb->prefix . Database::STATS_TABLE . " t1" .
                " JOIN (" .
                    "SELECT min(time) AS min_value, max(time) AS max_value, biz_id FROM " . $wpdb->prefix . Database::STATS_TABLE .
                    " WHERE biz_id IN (" . implode(', ', array_fill(0, count($biz_ids), '%d')) . ")" .
                    " GROUP BY biz_id" .
                ") AS t2 ON t1.biz_id = t2.biz_id AND (t1.time = t2.min_value OR t1.time = t2.max_value)",
                $biz_ids
            )
        );

        return
            array(
                'rating'       => $rating,
                'review_count' => $review_count,
                'places'       => $google_places,
                'reviews'      => $google_reviews,
                'stats'        => $stats,
                'stats_minmax' => $stats_minmax
            );
    }

    /*public function merge_biz($businesses, $id = '', $name = '', $url = '', $photo = '', $provider = '') {
        $count = 0;
        $rating = 0;
        $review_count = array();
        $review_count_manual = array();
        $business_platform = array();
        $biz_merge = null;
        foreach ($businesses as $business) {
            if ($business->rating < 1) {
                continue;
            }

            $count++;
            $rating += $business->rating;

            if (isset($business->review_count_manual) && $business->review_count_manual > 0) {
                $review_count_manual[$business->id] = $business->review_count_manual;
            } else {
                $review_count[$business->id] = $business->review_count;
            }

            array_push($business_platform, $business->provider);

            if ($biz_merge == null) {
                $biz_merge = json_decode(json_encode(
                    array(
                        'id'           => strlen($id)       > 0 ? $id       : $business->id,
                        'name'         => strlen($name)     > 0 ? $name     : $business->name,
                        'url'          => strlen($url)      > 0 ? $url      : $business->url,
                        'photo'        => strlen($photo)    > 0 ? $photo    : $business->photo,
                        'provider'     => strlen($provider) > 0 ? $provider : $business->provider,
                        'review_count' => 0,
                    )
                ));
            }
            $rating_tmp = round($rating / $count, 1);
            $rating_tmp = number_format((float)$rating_tmp, 1, '.', '');
            $biz_merge->rating = $rating_tmp;
        }
        $review_count = array_merge($review_count, $review_count_manual);
        foreach ($review_count as $id => $count) {
            $biz_merge->review_count += $count;
        }
        $biz_merge->platform = array_unique($business_platform);
        return $biz_merge;
    }*/

    private function sort_recent($a, $b) {
        return $b->time - $a->time;
    }

}