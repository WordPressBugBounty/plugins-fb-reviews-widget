<?php

namespace WP_TrustReviews\Includes;

use WP_TrustReviews\Includes\Core\Core;

class Plugin_Overview_Ajax {

    private $core;

    public function __construct(Core $core) {
        $this->core = $core;

        add_action('wp_ajax_' . Plugin::SLG . '_overview_ajax', array($this, 'overview_ajax'));
    }

    public function overview_ajax() {
        $overview = $this->core->get_overview(isset($_POST['pid']) ? $_POST['pid'] : 0);
        echo json_encode($overview);

        die();
    }
}
