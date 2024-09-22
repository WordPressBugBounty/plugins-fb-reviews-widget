<?php

namespace WP_TrustReviews\Includes;

use WP_TrustReviews\Includes\Core\Core;

class Plugin_Overview {

    private $view_svg;

    public function __construct(View_Svg $view_svg) {
        $this->view_svg = $view_svg;
    }

    public function register() {
        add_action(Plugin::SLG . '_admin_page_' . Plugin::SLG, array($this, 'init'));
        add_action(Plugin::SLG . '_admin_page_' . Plugin::SLG, array($this, 'render'));
    }

    public function init() {

    }

    public function render() {

        wp_nonce_field(Plugin::SLG . '_wpnonce');

        wp_enqueue_script(Plugin::SLG . '-admin-apexcharts-js');

        ob_start();
        ?>

        <div class="{slg}-page-title">
            Overview
        </div>

        <div class="{slg}-overview-workspace">

            <div class="{slg}-overview-places">
                <select id="{slg}-overview-months">
                    <option value="6" selected>6 months</option>
                    <option value="12">a year</option>
                    <option value="24">2 years</option>
                    <option value="36">3 years</option>
                    <option value="60">5 years</option>
                </select>
                <select id="{slg}-overview-places"></select>
            </div>

            <div class="{slg}-flex-row">
                <div class="{slg}-flex-col6">

                    <div class="{slg}-card">
                        <div class="{slg}-card-body">
                            <div id="chart"></div>
                        </div>
                    </div>

                </div>

                <div class="{slg}-flex-col4">

                    <div class="{slg}-flex-row">

                        <div class="{slg}-flex-col">
                            <div class="{slg}-card">
                                <div class="{slg}-card-header">Rating</div>
                                <div class="{slg}-card-body {slg}-card-fh">
                                    <div id="{slg}-overview-rating"><img src="<?php echo Plugin::ASSETS_URL(); ?>img/dots-spinner.svg"></div>
                                </div>
                            </div><br>
                            <div class="{slg}-card">
                                <div class="{slg}-card-header">Usage Stats</div>
                                <div class="{slg}-card-body {slg}-card-fh">
                                    <div id="{slg}-overview-stats"><img src="<?php echo Plugin::ASSETS_URL(); ?>img/dots-spinner.svg"></div>
                                </div>
                            </div>
                        </div>

                        <div class="{slg}-flex-col">
                            <div class="{slg}-card">
                                <div class="{slg}-card-header">Latest Reviews</div>
                                <div class="{slg}-card-body {slg}-card-fh" style="padding-top:0">
                                    <div id="{slg}-overview-reviews"><img src="<?php echo Plugin::ASSETS_URL(); ?>img/dots-spinner.svg"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <?php
        $this->view_svg->render();
        echo preg_replace('/{slg}/', Plugin::SLG, ob_get_clean());
    }
}
