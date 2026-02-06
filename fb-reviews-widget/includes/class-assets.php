<?php

namespace WP_TrustReviews\Includes;

class Assets {

    private $url;
    private $version;
    private $debug;

    private $css_cache = array();

    private static $css_assets = array(
        'rpi-stars-css'                  => 'https://cdn.reviewsplugin.com/assets/css/stars.css',
        Plugin::SLG . '-admin-main-css'      => 'css/admin-main',
        Plugin::SLG . '-public-main-css'     => 'css/public-main',
    );

    private static $js_assets = array(
        'rpi-toast-js'                       => 'https://cdn.reviewsplugin.com/assets/js/toast.js',
        'rpi-time-js'                        => 'https://cdn.reviewsplugin.com/assets/js/time.js',
        Plugin::SLG . '-admin-main-js'       => 'js/admin-main',
        Plugin::SLG . '-admin-builder-js'    => 'js/admin-builder',
        Plugin::SLG . '-admin-apexcharts-js' => 'js/admin-apexcharts',
        Plugin::SLG . '-public-main-js'      => 'js/public-main'
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

            add_filter('script_loader_tag', array($this, 'script_async'), 10, 2);
        }
        add_filter('get_rocket_option_remove_unused_css_safelist', array($this, 'rucss_safelist'));
    }

    function script_async($tag, $handle) {
        $defer = array(
            Plugin::SLG . '-admin-main-js',
            Plugin::SLG . '-admin-builder-js',
            Plugin::SLG . '-public-main-js',
        );
        if (!in_array($handle, $defer, true)) {
            return $tag;
        }
        if (strpos($tag, ' defer') !== false || strpos($tag, ' async') !== false) {
            return $tag;
        }
        return preg_replace('/<script\b/i', '<script defer="defer"', $tag, 1);
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
            'rpi-stars-css',
            Plugin::SLG . '-admin-main-css',
            Plugin::SLG . '-public-main-css'
        );
        $this->register_styles_loop($styles);
    }

    public function register_scripts() {
        $scripts = array(
            Plugin::SLG . '-admin-main-js',
            Plugin::SLG . '-public-main-js',
            Plugin::SLG . '-admin-apexcharts-js'
        );
        if ($this->debug) {
            array_push($scripts, 'rpi-toast-js');
            array_push($scripts, 'rpi-time-js');
            array_push($scripts, Plugin::SLG . '-admin-builder-js');
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
            wp_enqueue_script('rpi-toast-js');
            wp_enqueue_script(Plugin::SLG . '-admin-builder-js');
        }
        wp_localize_script(Plugin::SLG . '-admin-main-js', 'TRUSTREVIEWS_VARS', $vars);
        wp_enqueue_script(Plugin::SLG . '-admin-main-js');

        $this->enqueue_public_scripts();
    }

    public function enqueue_public_styles() {
        if ($this->debug) {
            wp_enqueue_style('rpi-stars-css');
        }

        $handle = Plugin::SLG . '-public-main-css';
        $inlinecss_off = get_option(Plugin::SLG . '_inlinecss_off');
        if ($inlinecss_off !== 'true') {
            $css = $this->get_css_content('public-main');
            if (!empty($css)) {
                wp_dequeue_style($handle);
                wp_deregister_style($handle);
                wp_register_style($handle, false);
                wp_enqueue_style($handle);
                wp_add_inline_style($handle, $css);
                return;
            }
        }

        wp_enqueue_style($handle);
        wp_style_add_data($handle, 'rtl', 'replace');
    }

    public function enqueue_public_scripts() {
        if ($this->debug) {
            wp_enqueue_script('rpi-time-js');
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
        $css = self::$css_assets[$asset];
        return strpos($css, 'https:') === 0 ? $css : $this->url . ($this->debug ? 'src/' : $this->version . '/') . $css . '.css';
    }

    public function get_js_asset($asset) {
        $js = self::$js_assets[$asset];
        return strpos($js, 'https:') === 0 ? $js : $this->url . ($this->debug ? 'src/' : $this->version . '/') . $js . '.js';
    }

    public function version() {
        return $this->version;
    }

    private function get_css_content($name) {
        $key = $name . (is_rtl() ? '-rtl' : '');

        if (isset($this->css_cache[$key])) {
            return $this->css_cache[$key];
        }

        $file = TRUSTREVIEWS_PLUGIN_PATH . '/assets/' . ($this->debug ? 'src/' : $this->version . '/') . 'css/' . $key . '.css';
        if (!file_exists($file) || !is_readable($file)) {
            return $this->css_cache[$key] = '';
        }
        $css = (string) file_get_contents($file);
        return $this->css_cache[$key] = $css;
    }
}