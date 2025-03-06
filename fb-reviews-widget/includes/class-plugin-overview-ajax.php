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
        if (!current_user_can('manage_options')) {
            die('The account you\'re logged in to doesn\'t have permission to access this page.');
        }
        
        check_admin_referer(Plugin::SLG . '_wpnonce');
        
        $overview = $this->core->get_overview(isset($_POST['pid']) ? $_POST['pid'] : 0);
        echo json_encode($overview);

        die();
    }
}
