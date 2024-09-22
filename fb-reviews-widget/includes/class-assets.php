<?php

namespace WP_TrustReviews\Includes;

class Assets {

    private $url;
    private $version;
    private $debug;

    private static $css_assets = array(
        Plugin::SLG . '-admin-main-css'      => 'css/admin-main',
        Plugin::SLG . '-public-clean-css'    => 'css/public-clean',
        Plugin::SLG . '-public-main-css'     => 'css/public-main',
    );

    private static $js_assets = array(
        Plugin::SLG . '-admin-main-js'       => 'js/admin-main',
        Plugin::SLG . '-admin-builder-js'    => 'js/admin-builder',
        Plugin::SLG . '-admin-apexcharts-js' => 'js/admin-apexcharts',
        Plugin::SLG . '-public-time-js'      => 'js/public-time',
        Plugin::SLG . '-public-main-js'      => 'js/public-main',
    );

    public function __construct($url, $version, $debug) {
        $this->url     = $url;
        $this->version = $version;
        $this->debug   = $debug;
    }

    public function register() {
        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this, 'register_styles'));
            add_action('admin_enqueue_scripts', array($this, 'register_scripts'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
            add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        } else {
            add_action('wp_enqueue_scripts', array($this, 'register_styles'));
            add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
            $demand_assets = get_option(Plugin::SLG . '_demand_assets');
            if (!$demand_assets || $demand_assets != 'true') {
                add_action('wp_enqueue_scripts', array($this, 'enqueue_public_styles'));
                add_action('wp_enqueue_scripts', array($this, 'enqueue_public_scripts'));
            }
            add_filter('script_loader_tag', array($this, 'add_async'), 10, 2);
        }
        add_filter('get_rocket_option_remove_unused_css_safelist', array($this, 'rucss_safelist'));
    }

    function add_async($tag, $handle) {
        $js_assets = array(
            Plugin::SLG . '-admin-main-js'    => 'js/admin-main',
            Plugin::SLG . '-admin-builder-js' => 'js/admin-builder',
            Plugin::SLG . '-public-time-js'   => 'js/public-time',
            Plugin::SLG . '-public-main-js'   => 'js/public-main',
        );
        if (isset($handle) && array_key_exists($handle, $js_assets)) {
            return str_replace(' src', ' defer="defer" src', $tag);
        }
        return $tag;
    }

    function rucss_safelist($safelist) {
        $css_main = $this->get_css_asset(Plugin::SLG . '-public-main-css');
        if (array_search($css_main, $safelist) !== false) {
            return $safelist;
        }
        $safelist[] = $css_main;
        return $safelist;
    }

    public function register_styles() {
        $styles = array(
            Plugin::SLG . '-admin-main-css',
            Plugin::SLG . '-public-main-css'
        );
        if ($this->debug) {
            array_push($styles, Plugin::SLG . '-public-clean-css');
        }
        $this->register_styles_loop($styles);
    }

    public function register_scripts() {
        $scripts = array(
            Plugin::SLG . '-admin-main-js',
            Plugin::SLG . '-public-main-js',
            Plugin::SLG . '-admin-apexcharts-js'
        );
        if ($this->debug) {
            array_push($scripts, Plugin::SLG . '-admin-builder-js');
            array_push($scripts, Plugin::SLG . '-public-time-js');
        }
        $this->register_scripts_loop($scripts);
    }

    public function enqueue_admin_styles() {
        wp_enqueue_style('wp-jquery-ui-dialog');
        wp_enqueue_style(Plugin::SLG . '-admin-main-css');
        wp_style_add_data(Plugin::SLG . '-admin-main-css', 'rtl', 'replace');
        $this->enqueue_public_styles();
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-draggable');
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('jquery-ui-dialog');

        $vars = array(
            'slg'        => Plugin::SLG,
            'supportUrl' => admin_url('admin.php?page=' . Plugin::SLG . '-support'),
            'builderUrl' => admin_url('admin.php?page=' . Plugin::SLG . '-builder'),
            'pluginName' => Plugin::NAME,
        );

        if ($this->debug) {
            wp_enqueue_script(Plugin::SLG . '-admin-builder-js');
        }
        wp_localize_script(Plugin::SLG . '-admin-main-js', 'TRUSTREVIEWS_VARS', $vars);
        wp_enqueue_script(Plugin::SLG . '-admin-main-js');

        $this->enqueue_public_scripts();
    }

    public function enqueue_public_styles() {
        if ($this->debug) {
            wp_enqueue_style(Plugin::SLG . '-public-clean-css');
            wp_style_add_data(Plugin::SLG . '-public-clean-css', 'rtl', 'replace');
        }
        wp_enqueue_style(Plugin::SLG . '-public-main-css');
        wp_style_add_data(Plugin::SLG . '-public-main-css', 'rtl', 'replace');
    }

    public function enqueue_public_scripts() {
        if ($this->debug) {
            wp_enqueue_script(Plugin::SLG . '-public-time-js');
        }
        wp_enqueue_script(Plugin::SLG . '-public-main-js');
    }

    private function register_styles_loop($styles) {
        foreach ($styles as $style) {
            wp_register_style($style, $this->get_css_asset($style), array(), $this->version);
        }
    }

    private function register_scripts_loop($scripts) {
        foreach ($scripts as $script) {
            wp_register_script($script, $this->get_js_asset($script), array(), $this->version);
        }
    }

    public function get_css_asset($asset) {
        return $this->url . ($this->debug ? 'src/' : '') . self::$css_assets[$asset] . '.css';
    }

    public function get_js_asset($asset) {
        $src = $this->debug && !strrpos($asset, 'apexcharts') ? 'src/' : '';
        return $this->url . $src . self::$js_assets[$asset] . '.js';
    }

    public function version() {
        return $this->version;
    }

}