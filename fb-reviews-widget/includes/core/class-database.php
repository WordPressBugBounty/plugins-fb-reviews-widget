<?php

namespace WP_TrustReviews\Includes\Core;

use WP_TrustReviews\Includes\Plugin;

class Database {

    const BUSINESS_TABLE = Plugin::PFX . 'biz';

    const REVIEW_TABLE   = Plugin::PFX . 'review';

    const STATS_TABLE    = Plugin::PFX . 'stats';

    public function create() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        if (!function_exists('dbDelta')) {
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        }

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::BUSINESS_TABLE . " (".
               "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
               "pid VARCHAR(80) NOT NULL,".
               "name VARCHAR(255) NOT NULL,".
               "photo VARCHAR(255),".
               "address VARCHAR(255),".
               "rating DOUBLE PRECISION,".
               "url VARCHAR(255),".
               "website VARCHAR(255),".
               "review_count INTEGER,".
               "platform VARCHAR(127),".
               "updated BIGINT(20),".
               "PRIMARY KEY (`id`),".
               "UNIQUE INDEX " . Plugin::PFX . "pid_idx (`pid`)".
               ") " . $charset_collate . ";";

        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::REVIEW_TABLE . " (".
               "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
               "biz_id BIGINT(20) UNSIGNED NOT NULL,".
               "rating INTEGER NOT NULL,".
               "text VARCHAR(10000),".
               "time INTEGER NOT NULL,".
               "url VARCHAR(255),".
               "language VARCHAR(10),".
               "author_name VARCHAR(255),".
               "author_url VARCHAR(255),".
               "author_img VARCHAR(255),".
               "platform VARCHAR(127),".
               "hide VARCHAR(1) DEFAULT '' NOT NULL,".
               "PRIMARY KEY (`id`),".
               "INDEX " . Plugin::PFX . "biz_id_idx (`biz_id`)".
               ") " . $charset_collate . ";";

        dbDelta($sql);

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::STATS_TABLE . " (".
               "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
               "biz_id BIGINT(20) UNSIGNED NOT NULL,".
               "time INTEGER NOT NULL,".
               "rating DOUBLE PRECISION,".
               "review_count INTEGER,".
               "PRIMARY KEY (`id`),".
               "INDEX " . Plugin::PFX . "biz_id_idx (`biz_id`)".
               ") " . $charset_collate . ";";

        dbDelta($sql);
    }

    public function drop() {
        global $wpdb;

        $wpdb->query("DROP TABLE " . $wpdb->prefix . self::BUSINESS_TABLE . ";");
        $wpdb->query("DROP TABLE " . $wpdb->prefix . self::REVIEW_TABLE . ";");
        $wpdb->query("DROP TABLE " . $wpdb->prefix . self::STATS_TABLE . ";");
    }

}
