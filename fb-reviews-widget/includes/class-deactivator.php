<?php

namespace WP_TrustReviews\Includes;

class Deactivator {

    private $reviews_cron;

    public function __construct(Reviews_Cron $reviews_cron) {
        $this->reviews_cron = $reviews_cron;
    }

    public function deactivate() {
        $this->reviews_cron->deactivate();
    }

}
