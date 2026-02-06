<?php

namespace WP_TrustReviews\Includes\Core;

use WP_TrustReviews\Includes\Plugin;

class Database {

    const BUSINESS_TABLE = Plugin::PFX . 'biz';

    const REVIEW_TABLE = Plugin::PFX . 'review';

    const TEXT_TABLE = self::REVIEW_TABLE . '_text';

    const STATS_TABLE = Plugin::PFX . 'stats';

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
               "map_url VARCHAR(512),".
               "website VARCHAR(255),".
               "review_count INTEGER,".
               "platform VARCHAR(127),".
               "updated BIGINT(20),".
               "PRIMARY KEY (`id`),".
               "UNIQUE INDEX " . Plugin::PFX . "pid_idx (`pid`)".
               ") " . $charset_collate . ";";

        $this->execsql($sql);

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::REVIEW_TABLE . " (".
               "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
               "biz_id BIGINT(20) UNSIGNED NOT NULL,".
               "review_id VARCHAR(64) NOT NULL,".
               "rating INTEGER NOT NULL,".
               "text TEXT,".
               "time INTEGER NOT NULL,".
               "url VARCHAR(255),".
               "language VARCHAR(10),".
               "author_name VARCHAR(255),".
               "author_url VARCHAR(255),".
               "author_img VARCHAR(255),".
               "images TEXT,".
               "reply TEXT,".
               "reply_time INTEGER,".
               "platform VARCHAR(127),".
               "hide VARCHAR(1) DEFAULT '' NOT NULL,".
               "PRIMARY KEY (`id`),".
               "INDEX " . Plugin::PFX . "biz_id_idx (`biz_id`)".
               ") " . $charset_collate . ";";

        $this->execsql($sql);

        $this->create_text_table();

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::STATS_TABLE . " (".
               "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
               "biz_id BIGINT(20) UNSIGNED NOT NULL,".
               "time INTEGER NOT NULL,".
               "rating DOUBLE PRECISION,".
               "review_count INTEGER,".
               "PRIMARY KEY (`id`),".
               "INDEX " . Plugin::PFX . "biz_id_idx (`biz_id`)".
               ") " . $charset_collate . ";";

        $this->execsql($sql);
    }

    public function create_text_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . self::TEXT_TABLE . " (".
               "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
               "review_id VARCHAR(64) NOT NULL,".
               "lang VARCHAR(10) NOT NULL,".
               "text TEXT,".
               "PRIMARY KEY (`id`),".
               "UNIQUE INDEX " . Plugin::PFX . "review_lang_idx (`review_id`, `lang`),".
               "INDEX idx_review_id (`review_id`)".
               ") " . $charset_collate . ";";

        $this->execsql($sql);
    }

    public function migrate_review_texts() {
        global $wpdb;

        $text_table = $wpdb->prefix . self::TEXT_TABLE;
        $rev_table  = $wpdb->prefix . self::REVIEW_TABLE;

        $wpdb->query(
            "INSERT INTO {$text_table} (review_id, lang, text)
             SELECT
                 r.review_id,
                 r.language AS lang,
                 r.text
             FROM {$rev_table} r
             WHERE r.text IS NOT NULL AND r.text <> ''
               AND r.review_id IS NOT NULL AND r.review_id <> ''
               AND r.language IS NOT NULL AND r.language <> ''
               AND (r.hide IS NULL OR r.hide <> '1')
             ON DUPLICATE KEY UPDATE text = VALUES(text)"
        );

        $this->log_error($wpdb->last_error);
    }

    private function execsql($sql) {
        global $wpdb;

        if (!function_exists('dbDelta')) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta($sql);

        $this->log_error($wpdb->last_error);
    }

    public function drop() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . self::BUSINESS_TABLE . ";");
        $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . self::REVIEW_TABLE . ";");
        $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . self::TEXT_TABLE . ";");
        $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . self::STATS_TABLE . ";");
    }

    private function log_error($last_error) {
        if (isset($last_error) && strlen($last_error) > 0) {
            $now = (int) floor(microtime(true) * 1000);
            update_option(Plugin::SLG . '_last_error', $now . ': ' . $last_error);
        }
    }
}
