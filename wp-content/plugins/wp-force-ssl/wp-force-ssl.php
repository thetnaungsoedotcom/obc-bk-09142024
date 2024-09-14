<?php
/*
  Plugin Name: WP Force SSL PRO
  Plugin URI: https://wpforcessl.com/
  Description: Control and fix site's SSL certificate and options.
  Version: 5.29
  Author: WebFactory Ltd
  Author URI: https://www.webfactoryltd.com/
  Text Domain: wp-force-ssl

  Copyright 2015 - 2021  WebFactory Ltd  (email: wpforcessl@webfactoryltd.com)

  This program is NOT free software.
*/

// include only file
if (!defined('ABSPATH')) {
    die('Do not open this file directly.');
}

define('WP_FORCE_SSL_FILE', __FILE__);

require_once dirname(__FILE__) . '/inc/wp-force-ssl-tools.php';
require_once dirname(__FILE__) . '/inc/wp-force-ssl-status-tests.php';
require_once dirname(__FILE__) . '/inc/wp-force-ssl-utility.php';
require_once dirname(__FILE__) . '/inc/wf-licensing.php';

class WP_Force_SSL
{
    protected static $instance = null;
    public $version = 0;
    public $plugin_url = '';
    public $plugin_dir = '';
    public $licensing_servers = array('https://dashboard.wpforcessl.com/api/');
    protected $options = array();
    public $scanner = array();

    /**
     * Creates a new WP_Force_SSL object and implements singleton
     *
     * @return WP_Force_SSL
     */
    static function getInstance()
    {
        if (!is_a(self::$instance, 'WP_Force_SSL')) {
            self::$instance = new WP_Force_SSL();
        }

        return self::$instance;
    } // getInstance


    /**
     * Initialize properties, hook to filters and actions
     *
     * @return null
     */
    private function __construct()
    {
        global $wp_force_ssl_tools;
        $this->version = $this->get_plugin_version();
        $this->plugin_dir = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);
        $this->load_options();
        $options = $this->get_options();

        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_head',  array($this, 'cleanup_enqueues'), 99999);
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('wp_ajax_wp_force_ssl_dismiss_notice', array($this, 'ajax_dismiss_notice'));
        add_action('wp_ajax_wp_force_ssl_run_tool', array($this, 'ajax_run_tool'));
        add_action('admin_print_scripts', array($this, 'remove_admin_notices'));
        add_action('admin_footer', array($this, 'admin_footer'));

        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links'));
        add_filter('plugin_row_meta', array($this, 'plugin_meta_links'), 10, 2);
        add_filter('admin_footer_text', array($this, 'admin_footer_text'));
        add_filter('wf_licensing_wfssl_query_server_meta', array($this, 'licensing_meta'), 10, 2);

        add_action('plugins_loaded', array($this, 'plugins_loaded'));

        add_action('wp_ajax_test_ssl_nonce_action', array($this, 'ajax_check_ssl'));
        add_action('wp_ajax_nopriv_test_ssl_nonce_action', array($this, 'ajax_check_ssl'));

        add_action('admin_head-settings_page_wp-force-ssl', array($this, 'rebrand_css'));
        add_action('wp_before_admin_bar_render', array($this, 'admin_bar'));

        if ($this->ssl_status()) {
            add_action('send_headers', array($wp_force_ssl_tools, 'add_security_headers'));
        }

        if ($this->ssl_status() && $options['php_301_redirect']) {
            add_action('wp', array($wp_force_ssl_tools, 'wp_redirect_to_ssl'), 10, 3);
        }
    } // __construct

    function licensing_meta($meta, $action)
    {
        $options = $this->get_options();
        if (strlen($options['cert_expiration_email']) > 0) {
            return array('ssl_monitor_expiration' => $options['cert_expiration_email']);
        } 

        return $meta;
    }

    function ssl_status()
    {
        if (strpos(home_url(), 'https://') === 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Make a request
     * @param url page URL
     * @return string content
     */
    public function make_request($url = null)
    {
        if (empty($url)) {
            $url = home_url(false, 'https');
        }

        $args = array(
            'timeout' => 10,
            'sslverify' => true
        );

        $response = wp_remote_post($url, $args);

        $error = '';
        if (is_wp_error($response)) {
            $error = $response->get_error_message();
        }

        $out  = array(
            'code' => wp_remote_retrieve_response_code($response),
            'body' => wp_remote_retrieve_body($response),
            'error' => $error
        );

        return $out;
    } // make_request

    /**
     * Get plugin version from file header
     *
     * @return string
     */
    function get_plugin_version()
    {
        $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');

        return $plugin_data['version'];
    } // get_plugin_version


    /**
     * Actions to run on load, but init would be too early as not all classes are initialized
     *
     * @return null
     */
    function plugins_loaded()
    {
        global $wp_force_ssl_licensing, $wp_force_ssl_tools;
        $options = $this->get_options();

        $wp_force_ssl_licensing = new WF_Licensing_wpforcessl(array(
            'prefix' => 'wfssl',
            'licensing_servers' => $this->licensing_servers,
            'version' => $this->version,
            'plugin_file' => __FILE__,
            'skip_hooks' => false,
            'debug' => false,
            'js_folder' => plugin_dir_url(__FILE__) . '/js/'
        ));

        if ($options['fix_frontend_mixed_content_fixer']) {
            $wp_force_ssl_tools->fix_mixed_content();
        }

        if (isset($_GET['wp_force_ssl_wl'])) {
            if ($_GET['wp_force_ssl_wl'] == 'true') {
                $options['whitelabel'] = true;
            } else {
                $options['whitelabel'] = false;
            }
            $this->update_options('options', $options);
        }
    } // admin_actions

    /**
     * Load and prepare the options array. If needed create a new DB entry.
     *
     * @return array
     */
    private function load_options()
    {
        $options = get_option('wp-force-ssl', array('options' => array()));

        $change = false;

        if (empty($options['meta'])) {
            $options['meta'] = array('first_version' => $this->version, 'first_install' => current_time('timestamp', true));
            $change = true;
        }

        if (!isset($options['dismissed_notices']) || !is_array($options['dismissed_notices'])) {
            $options['dismissed_notices'] = array();
            $change = true;
        }

        if (array_key_exists('content_security_policy', $options['options'])) {
            unset($options['options']['content_security_policy']);
        }

        if (array_key_exists('upgrade_insecure_content', $options['options'])) {
            unset($options['options']['upgrade_insecure_content']);
        }

        $default_options = array(
            'fix_frontend_mixed_content_fixer' => false,
            'fix_backend_mixed_content_fixer' => false,
            'hsts' => false,
            'force_secure_cookies' => false,
            'htaccess_301_redirect' => false,
            'php_301_redirect' => false,
            'xss_protection' => false,
            'x_content_options' => false,
            'referrer_policy' => false,
            'expect_ct' => false,
            'x_frame_options' => false,
            'permissions_policy' => false,
            'permissions_policy_rules' => '{}',
            'cert_expiration_email' => '',
            'whitelabel' => false,
            'adminbar_menu' => true
        );

        foreach ($options['options'] as $option_key => $option) {
            if (!array_key_exists($option_key, $default_options)) {
                unset($options['options'][$option_key]);
            }
        }

        if (sizeof($options['options']) < sizeof($default_options)) {
            $options['options'] = array_merge($default_options, (array) $options['options']);
            $change = true;
        }

        if ($change) {
            update_option('wp-force-ssl', $options, true);
        }

        $this->options = $options;

        $this->scanner = get_option('wp-force-ssl-scanner', array(
            'files' => array(
                'js' => array(),
                'css' => array(),
                'external' => array()
            ),
            'pages' => array()
        ));

        $this->ssl_certificate = get_option('wp-force-ssl-certificate', array());
        return $options;
    } // load_options


    /**
     * Get meta part of plugin options
     *
     * @return array
     */
    function get_meta()
    {
        return $this->options['meta'];
    } // get_meta


    /**
     * Get license part of plugin options
     *
     * @return array
     */
    function get_license()
    {
        global $wp_force_ssl_licensing;
        return $wp_force_ssl_licensing->get_license();
    } // get_license


    /**
     * Get all dismissed notices, or check for one specific notice
     *
     * @param string  $notice_name  Optional. Check if specified notice is dismissed.
     *
     * @return bool|array
     */
    function get_dismissed_notices($notice_name = '')
    {
        $notices = $this->options['dismissed_notices'];

        if (empty($notice_name)) {
            return $notices;
        } else {
            if (empty($notices[$notice_name])) {
                return false;
            } else {
                return true;
            }
        }
    } // get_dismissed_notices


    /**
     * Get options part of plugin options
     *
     * @return array
     */
    function get_options()
    {
        return $this->options['options'];
    } // get_options


    /**
     * Get all plugin options
     *
     * @return array
     */
    function get_all_options()
    {
        return $this->options;
    } // get_all_options


    /**
     * Update specified plugin options key
     *
     * @param string  $key   Data to save.
     * @param string  $data  Option key.
     *
     * @return bool
     */
    function update_options($key, $data)
    {
        if (false === in_array($key, array('meta', 'license', 'dismissed_notices', 'options'))) {
            user_error('Unknown options key.', E_USER_ERROR);
            return false;
        }

        $this->options[$key] = $data;
        $tmp = update_option('wp-force-ssl', $this->options);

        return $tmp;
    } // set_options


    /**
     * Add plugin menu entry under Tools menu
     *
     * @return null
     */
    function admin_menu()
    {
        global $wp_force_ssl_tests, $wp_force_ssl_licensing;
        $test_results = $wp_force_ssl_tests->count_statuses();

        $page_title = $this->get_rebranding('name');
        if ($page_title === false || empty($page_title)) {
            $page_title = 'WP Force SSL PRO';
        }

        $menu_title = $this->get_rebranding('short_name');
        if ($menu_title === false || empty($menu_title)) {
            $menu_title = 'WP Force SSL';
        }

        add_options_page(
            $page_title,
            $menu_title . ($test_results['fail'] && $wp_force_ssl_licensing->is_active() ? sprintf(' <span class="wfssl-failed-tests awaiting-mod">%d</span>', $test_results['fail']) : ''),
            'administrator',
            'wp-force-ssl',
            array($this, 'plugin_page')
        );
    } // admin_menu


    /**
     * Dismiss notice via AJAX call
     *
     * @return null
     */
    function ajax_dismiss_notice()
    {
        check_ajax_referer('wp-force-ssl_dismiss_notice');

        if (!current_user_can('administrator')) {
            wp_send_json_error('You are not allowed to run this action.');
        }

        $notice_name = trim(@$_GET['notice_name']);
        if (!$this->dismiss_notice($notice_name)) {
            wp_send_json_error('Notice is already dismissed.');
        } else {
            wp_send_json_success();
        }
    } // ajax_dismiss_notice


    /**
     * Dismiss notice by adding it to dismissed_notices options array
     *
     * @param string  $notice_name  Notice to dismiss.
     *
     * @return bool
     */
    function dismiss_notice($notice_name)
    {
        if ($this->get_dismissed_notices($notice_name)) {
            return false;
        } else {
            $notices = $this->get_dismissed_notices();
            $notices[$notice_name] = true;
            $this->update_options('dismissed_notices', $notices);
            return true;
        }
    } // dismiss_notice


    /**
     * Returns all WP pointers
     *
     * @return array
     */
    function get_pointers()
    {
        $pointers = array();

        $plugin_name = $this->get_rebranding('name');
        if ($plugin_name === false) {
            $plugin_name = 'WP Force SSL PRO';
        }

        $pointers['welcome'] = array('target' => '#menu-settings', 'edge' => 'left', 'align' => 'right', 'content' => 'Thank you for installing the <b style="font-weight: 800;">' . $plugin_name . '</b> plugin!<br>Open <a href="' . admin_url('options-general.php?page=wp-force-ssl') . '">Settings - ' . $plugin_name . '</a> to access SSL settings.');

        return $pointers;
    } // get_pointers


    /**
     * Enqueue CSS and JS files
     *
     * @return null
     */
    function admin_enqueue_scripts($hook)
    {
        global $wp_force_ssl_licensing, $wp_force_ssl_tests;

        $pointers = $this->get_pointers();
        $dismissed_notices = $this->get_dismissed_notices();

        foreach ($dismissed_notices as $notice_name => $tmp) {
            if ($tmp) {
                unset($pointers[$notice_name]);
            }
        } // foreach

        if (!empty($pointers) && !$this->is_plugin_page() && current_user_can('administrator')) {
            $pointers['_nonce_dismiss_pointer'] = wp_create_nonce('wp-force-ssl_dismiss_notice');

            wp_enqueue_style('wp-pointer');

            wp_enqueue_script('wp-force-ssl-pointers', $this->plugin_url . 'js/wp-force-ssl-pointers.js', array('jquery'), $this->version, true);
            wp_enqueue_script('wp-pointer');
            wp_localize_script('wp-pointer', 'wp_force_ssl_pointers', $pointers);
        }

        if ($hook == 'plugins.php') {
            $rebranding = $this->get_rebranding();
            if (false === $rebranding) {
                return false;
            }

            wp_enqueue_script('wp-force-ssl-branding', $this->plugin_url . 'js/branding.js', array('jquery'), $this->version, true);
            wp_localize_script('wp-force-ssl-branding', 'wp_force_ssl_rebranding', $rebranding);
        }

        if (!$this->is_plugin_page()) {
            return;
        }

        $current_user = wp_get_current_user();
        $license = $wp_force_ssl_licensing->get_license();
        $support_text = 'My site details: WP ' . get_bloginfo('version') . ', WP Force SSL PRO v' . $this->get_plugin_version() . ', ';
        if (!empty($license['license_key'])) {
            $support_text .= 'license key: ' . $license['license_key'] . '.';
        } else {
            $support_text .= 'no license info.';
        }
        if (strtolower($current_user->display_name) != 'admin' && strtolower($current_user->display_name) != 'administrator') {
            $support_name = $current_user->display_name;
        } else {
            $support_name = '';
        }

        $plugin_name = $this->get_rebranding('name');
        if ($plugin_name === false || empty($plugin_name)) {
            $plugin_name = 'WP Force SSL PRO';
        }

        $js_localize = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'undocumented_error' => 'An undocumented error has occurred. Please refresh the page and try again.',
            'documented_error' => 'An error has occurred.',
            'plugin_name' => $plugin_name,
            'is_plugin_page' => (int) $this->is_plugin_page(),
            'settings_url' => admin_url('options-general.php?page=wp-force-ssl'),
            'icon_url' => $this->plugin_url . 'img/wp-force-ssl-icon.png',
            'nonce_run_tool' => wp_create_nonce('wp_force_ssl_run_tool'),
            'nonce_test_ssl' => wp_create_nonce('test_ssl_nonce_action'),
            'scanner_pages' => $this->get_scanner_pages(),
            'whitelabel' => (int) !WP_Force_SSL_Utility::whitelabel_filter(),
            'support_name' => $support_name,
            'support_text' => $support_text,
            'is_activated' => $wp_force_ssl_licensing->is_active(),
            'is_localhost' => $wp_force_ssl_tests->is_localhost(),
            'rebranding' => $this->get_rebranding() === false ? 0 : $this->get_rebranding()
        );

        if ($this->is_plugin_page()) {
            wp_enqueue_style('wp-force-ssl', $this->plugin_url . 'css/wp-force-ssl.css', array(), $this->version);
            wp_enqueue_style('wp-force-ssl-google-font-inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap', array(), $this->version);
            wp_enqueue_style('wp-force-ssl-sweetalert-style', $this->plugin_url . 'css/sweetalert2.min.css', array(), $this->version);
            wp_enqueue_style('wp-force-ssl-datatables-style', $this->plugin_url . 'css/jquery.dataTables.min.css', array(), $this->version);
            wp_enqueue_style('wp-force-ssl-tooltipster-style', $this->plugin_url . 'css/tooltipster.bundle.min.css', array(), $this->version);

            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-position');

            wp_enqueue_script('wp-force-ssl-libs', $this->plugin_url . 'js/wp-force-ssl-libs.min.js', array('jquery'), $this->version, true);
            wp_enqueue_script('wp-force-ssl', $this->plugin_url . 'js/wp-force-ssl.js', array('jquery'), $this->version, true);
            wp_localize_script('wp-force-ssl', 'wp_force_ssl', $js_localize);

            $this->cleanup_enqueues();
        }
    } // admin_enqueue_scripts

    function cleanup_enqueues()
    {
        // fix for aggressive plugins that include their CSS on all pages
        if ($this->is_plugin_page()) {
            wp_dequeue_style('uiStyleSheet');
            wp_dequeue_style('wpcufpnAdmin');
            wp_dequeue_style('unifStyleSheet');
            wp_dequeue_style('wpcufpn_codemirror');
            wp_dequeue_style('wpcufpn_codemirrorTheme');
            wp_dequeue_style('collapse-admin-css');
            wp_dequeue_style('jquery-ui-css');
            wp_dequeue_style('tribe-common-admin');
            wp_dequeue_style('file-manager__jquery-ui-css');
            wp_dequeue_style('file-manager__jquery-ui-css-theme');
            wp_dequeue_style('wpmegmaps-jqueryui');
            wp_dequeue_style('wp-botwatch-css');
            wp_dequeue_style('njt-filebird-admin');
            wp_dequeue_style('ihc_jquery-ui.min.css');
            wp_dequeue_style('badgeos-juqery-autocomplete-css');
            wp_dequeue_style('mainwp');
            wp_dequeue_style('mainwp-responsive-layouts');
            wp_dequeue_style('jquery-ui-style');
            wp_dequeue_style('additional_style');
            wp_dequeue_style('wobd-jqueryui-style');
            wp_dequeue_style('wpdp-style3');
            wp_dequeue_style('jquery_smoothness_ui');
            wp_dequeue_style('uap_main_admin_style');
            wp_dequeue_style('uap_font_awesome');
            wp_dequeue_style('uap_jquery-ui.min.css');
        }
    }

    function rebrand_css()
    {
        if ($this->get_rebranding() !== false) {
            echo '<style>' . $this->get_rebranding('admin_css_predefined') . $this->get_rebranding('admin_css') . '</style>';
            echo '<style>
            .settings_page_wp-force-ssl .button-primary,
            .settings_page_wp-force-ssl .button-primary:hover,
            input:checked + .wfssl-slider,
            .wfssl-progress .bar{
                background-color:var(--main-brand-color);
            }

            .settings_page_wp-force-ssl .ui-tabs .ui-tabs-nav .ui-state-hover a, 
            .settings_page_wp-force-ssl .ui-tabs .ui-tabs-nav .ui-state-active a, 
            .settings_page_wp-force-ssl .ui-tabs .ui-tabs-nav li.ui-state-hover, 
            .settings_page_wp-force-ssl .ui-tabs .ui-tabs-nav li.ui-state-hover a{
                color:var(--main-brand-color);
            }
            </style>';
        }
    } // rebrand_css

    function admin_bar()
    {
        global $wp_admin_bar, $wp_force_ssl_licensing, $wp_force_ssl_tests;
        $test_results = $wp_force_ssl_tests->count_statuses();

        if (!$wp_force_ssl_licensing->is_active() || !is_admin()) {
            return;
        }

        $plugin_name = $this->get_rebranding('name');
        if ($plugin_name == false) {
            $plugin_name = 'WP Force SSL';
        }

        $plugin_logo = $this->get_rebranding('logo_url');
        if ($plugin_logo == false) {
            $plugin_logo = esc_url($this->plugin_url) . 'img/wp-force-ssl-icon.png';
        }


        $options = $this->get_options();

        if (
            !$options['adminbar_menu'] ||
            !$wp_force_ssl_licensing->is_active() ||
            false === current_user_can('administrator') ||
            false === apply_filters('wp_force_ssl_show_admin_bar', true)
        ) {
            return;
        }

        $title = '<div class="wfssl-adminbar-icon" style="display:inline-block;"><img style="height: 22px; padding: 4px; margin-bottom: -10px;  filter: invert(1) brightness(1.2) grayscale(1);" src="' . $plugin_logo . '" alt="' . $plugin_name . '" title="' . $plugin_name . '"></div> <span class="ab-label">' . $plugin_name . '</span>';
        if ($test_results['fail']) {
            $title .= sprintf(' <span class="wfssl-failed-tests awaiting-mod" style="display: inline-block;vertical-align: top;box-sizing: border-box;margin: 1px 0 -1px 2px;padding: 0 5px;min-width: 18px;height: 18px;border-radius: 9px;background-color: #d63638;color: #fff;font-size: 11px;line-height: 1.6;text-align: center;z-index: 26;vertical-align: text-bottom;">%d</span>', $test_results['fail']);
        }

        $wp_admin_bar->add_node(array(
            'id'    => 'wfssl-ab',
            'title' => $title,
            'href'  => '#',
            'parent' => '',
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wfssl-status',
            'title' => 'Status',
            'href'  => admin_url('options-general.php?page=wp-force-ssl#tab_status'),
            'parent' => 'wfssl-ab'
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wfssl-scanner',
            'title' => 'Content Scanner',
            'href'  => admin_url('options-general.php?page=wp-force-ssl#tab_scanner'),
            'parent' => 'wfssl-ab'
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'wfssl-snapshots',
            'title' => 'SSL Certificate',
            'href'  => admin_url('options-general.php?page=wp-force-ssl#tab_ssl'),
            'parent' => 'wfssl-ab'
        ));
    } // admin_bar

    /**
     * Remove all WP notices on WPSSL page
     *
     * @return null
     */
    function remove_admin_notices()
    {
        if (!$this->is_plugin_page()) {
            return false;
        }

        global $wp_filter;
        unset($wp_filter['user_admin_notices'], $wp_filter['admin_notices']);
    } // remove_admin_notices


    /**
     * Add HelpScout Beacon to footer
     *
     * @return null
     */
    function admin_footer()
    {
        if (!$this->is_plugin_page() || !WP_Force_SSL_Utility::whitelabel_filter()) {
            return;
        }

        echo '<script type="text/javascript">!function(e,t,n){function a(){var e=t.getElementsByTagName("script")[0],n=t.createElement("script");n.type="text/javascript",n.async=!0,n.src="https://beacon-v2.helpscout.net",e.parentNode.insertBefore(n,e)}if(e.Beacon=n=function(t,n,a){e.Beacon.readyQueue.push({method:t,options:n,data:a})},n.readyQueue=[],"complete"===t.readyState)return a();e.attachEvent?e.attachEvent("onload",a):e.addEventListener("load",a,!1)}(window,document,window.Beacon||function(){});</script>';
    } // admin_footer

    /**
     * Run one tool via AJAX call
     *
     * @return null
     */
    function ajax_run_tool()
    {
        global $wp_force_ssl_tests;

        check_ajax_referer('wp_force_ssl_run_tool');
        if (!current_user_can('administrator')) {
            wp_send_json_error('You are not allowed to run this action.');
        }

        if (isset($_REQUEST['tool']) && strlen($_REQUEST['tool']) > 0) {
            $tool = trim($_REQUEST['tool']);
        } else {
            $tool = false;
        }

        if (isset($_REQUEST['extra_data']) && !empty($_REQUEST['extra_data'])) {
            $extra_data = $_REQUEST['extra_data'];
        } else {
            $extra_data = false;
        }

        if (is_string($extra_data)) {
            $extra_data = trim($extra_data);
            $extra_data = substr($extra_data, 0, 255);
        } elseif (is_array($extra_data)) {
            $extra_data = array_slice($extra_data, 0, 20);
        } else {
            $extra_data = false;
        }

        if ($tool == 'ssl_status') {
            if (isset($_REQUEST['force']) && (bool)$_REQUEST['force'] === true) {
                $nocache = true;
            } else {
                $nocache = false;
            }
            $status = $wp_force_ssl_tests->get_ssl_status($nocache);
            wp_send_json_success($status);
        } else if ($tool == 'save_ssl_options') {
            $res = $this->save_ssl_options($extra_data);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success();
            }
        } else if ($tool == 'scanner_start') {
            $this->scanner = array(
                'files' => array(
                    'js' => array(),
                    'css' => array(),
                    'external' => array(),
                    'errors' => array()
                ),
                'pages' => array(),
                'start' => time()
            );

            WP_Force_SSL_Utility::clear_3rd_party_cache();
            update_option('wp-force-ssl-scanner', $this->scanner);
            wp_send_json_success();
        } else if ($tool == 'scanner_results') {
            wp_send_json_success($this->scanner_get_results());
        } else if ($tool == 'scanner_results_dt') {
            $this->scanner_get_results(true);
        } else if ($tool == 'scanner_page') {
            $res = $this->scan_page($_GET['page']);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success();
            }
        } else if ($tool == 'generate_certificate') {
            $res = $this->generate_certificate(filter_var($_POST['reset'], FILTER_VALIDATE_BOOLEAN), filter_var($_POST['return_status'], FILTER_VALIDATE_BOOLEAN));
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } else if ($tool == 'generate_certificate_details') {
            $res = $this->generate_certificate_details();
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } else if ($tool == 'tests_results') {
            if (isset($_REQUEST['force']) && (bool)$_REQUEST['force'] === true) {
                $nocache = true;
            } else {
                $nocache = false;
            }
            $res = $wp_force_ssl_tests->get_tests_results($nocache);
            if (is_wp_error($res)) {
                wp_send_json_error($res->get_error_message());
            } else {
                wp_send_json_success($res);
            }
        } else {
            wp_send_json_error('Unknown tool.');
        }
    } // ajax_run_tool

    function generate_certificate($reset, $return_status)
    {
        $result = array();

        if ($reset == true) {
            WP_Force_SSL_Utility::delete_folder(trailingslashit(WP_CONTENT_DIR) . 'wf_force_ssl_certificates');
            if(!empty($this->ssl_certificate['cert_zip']) && strpos($this->ssl_certificate['cert_zip'], 'letencrypt_') > 0){
                unlink($this->ssl_certificate['cert_zip']);
            }
            $this->ssl_certificate = array();
        }

        if (!array_key_exists('step', $this->ssl_certificate)) {
            $this->ssl_certificate['log'] = array();
            $this->ssl_certificate['step'] = 0;
            $this->ssl_certificate['html'] = 'You can use this tool to generate a free Let\'s Encrypt certificate. The certificate will be renewed automatically as long as WP Force SSL is active on the website. All you need is an email address, no other personal information will be requested.';
            $this->ssl_certificate['continue'] = false;
        }

        if ($return_status) {
            return $this->ssl_certificate;
        }
        usleep(500);
        switch ($this->ssl_certificate['step']) {
            case 0:
                $this->ssl_certificate['step'] = 1;
                $this->ssl_certificate['continue'] = false;
                break;
            case 1:
                $this->generate_certificate_log('Start certificate generation');
                $this->generate_certificate_log('Checking Requirements');
                $this->ssl_certificate['html'] = 'Checking requirements';
                $this->ssl_certificate['step'] = 2;
                $this->ssl_certificate['continue'] = true;
                break;
            case 2:
                // Check Requirements

                //Check PHP version
                if (version_compare(PHP_VERSION, '7.1', '>=')) {
                    $this->generate_certificate_log('PHP version OK');
                } else {
                    $this->generate_certificate_log('PHP version FAILED, you need at least PHP version 7.1', true);
                    $this->ssl_certificate['error'] = true;
                }

                //Create challenge directory
                $root_directory = trailingslashit(ABSPATH);
                if ( !file_exists( $root_directory . '.well-known' ) ) {
                    mkdir( $root_directory . '.well-known' );
                }

                if ( !file_exists( $root_directory . '.well-known/acme-challenge' ) ) {
                    mkdir( $root_directory . '.well-known/acme-challenge' );
                }

                if ( file_exists( $root_directory . '.well-known/acme-challenge' ) ){
                    $this->ssl_certificate['challenge_dir'] = $root_directory . '.well-known/acme-challenge';
                    $this->generate_certificate_log('ACME Challenge directory ' . $root_directory . '.well-known/acme-challenge created succesfully');
                } else {
                    $this->generate_certificate_log('ACME Challenge directory ' . $root_directory . '.well-known/acme-challenge could not be created', true);
                    $this->ssl_certificate['error'] = true;
                }

                //Create certificate directory and htaccess to block HTTP access
                $wp_content_directory = trailingslashit(WP_CONTENT_DIR);
                if ( !file_exists( $wp_content_directory . 'wf_force_ssl_certificates' ) ) {
                    mkdir( $wp_content_directory . 'wf_force_ssl_certificates' );
                }

                if ( file_exists( $wp_content_directory . 'wf_force_ssl_certificates' ) ){
                    $this->generate_certificate_log('Certificate directory ' . $wp_content_directory . 'wf_force_ssl_certificates created successfully');
                    $this->ssl_certificate['cert_dir'] = $wp_content_directory . 'wf_force_ssl_certificates';
                    if(!file_exists($wp_content_directory . 'wf_force_ssl_certificates/.htaccess')){
                        $htaccess = '<ifModule mod_authz_core.c>' . "\n"
                        . '    Require all denied' . "\n"
                        . '</ifModule>' . "\n"
                        . '<ifModule !mod_authz_core.c>' . "\n"
                        . '    Deny from all' . "\n"
                        . '</ifModule>';
                        file_put_contents($wp_content_directory . 'wf_force_ssl_certificates/.htaccess', $htaccess);
                        $this->generate_certificate_log('.htaccess for certificate directory ' . $wp_content_directory . 'wf_force_ssl_certificates created successfully');
                    }                    
                } else {
                    $this->generate_certificate_log('Certificate directory ' . $wp_content_directory . 'wf_force_ssl_certificates could not be created', true);
                    $this->ssl_certificate['error'] = true;
                }

                //Check if all requirements passed
                if(array_key_exists('error', $this->ssl_certificate) && $this->ssl_certificate['error'] === true){
                    //If failed, stop
                    $this->ssl_certificate['html'] = 'Minimum requirements have not passed. See log for details';
                    $this->ssl_certificate['continue'] = false;
                } else {
                    // If passed ask for info
                    $this->generate_certificate_log('Requirements passed');
                    $this->generate_certificate_log('Asking for user information');
                    $this->ssl_certificate['html'] = 'Please enter the email address that you want your Let\'s Encrypt certificate to be associated with:<br /><br />';
                    $this->ssl_certificate['html'] .= '<input type="text" name="email" value="' . get_option('admin_email') . '" /><br /><br />';
                    $this->ssl_certificate['html'] .= '<input type="checkbox" name="le_terms" value="yes" style="position:relative; opacity:1; z-index:99;" /> I agree to the <a href="https://letsencrypt.org/documents/LE-SA-v1.2-November-15-2017.pdf" target="_blank">Let\'s Encrypt Subscriber Agreement</a> (required)<br />';
                    $this->ssl_certificate['step'] = 3;
                    $this->ssl_certificate['continue'] = false;
                }
                break;
            case 3:
                if (isset($_POST['form'])) {
                    if (isset($_POST['form']['email']) && !empty($_POST['form']['email'])) {
                        $this->generate_certificate_log('User email valid');
                        $this->ssl_certificate['email'] = sanitize_text_field($_POST['form']['email']);
                    }

                    if (isset($_POST['form']['le_terms']) && $_POST['form']['le_terms'] == 'yes') {
                        $this->generate_certificate_log('User agreed to Let\'s Encrypt terms');
                        $this->ssl_certificate['le_terms'] = true;
                    }
                }

                // Email invalid or not set ... go back to step 2
                if (empty($this->ssl_certificate['email'])) {
                    $this->generate_certificate_log('User email not valid. Returning to user information form', true);
                    $this->ssl_certificate['step'] = 3;
                    $this->ssl_certificate['continue'] = false;
                    $this->ssl_certificate['html'] = '<span class="generate_ssl_certificate_error"><span class="dashicons dashicons-warning"></span> Please enter a valid email address</span>' . $this->ssl_certificate['html'];
                    break;
                }

                // LE Terms checkbox not checked ... go back to step 2
                if (empty($this->ssl_certificate['le_terms'])) {
                    $this->generate_certificate_log('You need to agree to the Let\'s Encrypt Subscriber Agreement. Returning to user information form', true);
                    $this->ssl_certificate['step'] = 3;
                    $this->ssl_certificate['continue'] = false;
                    $this->ssl_certificate['html'] = '<span class="generate_ssl_certificate_error"><span class="dashicons dashicons-warning"></span> You need to agree to the Let\'s Encrypt Subscriber Agreement</span>' . $this->ssl_certificate['html'];
                    break;
                }

                // We have everything, continue to generate the certificate
                $this->ssl_certificate['html'] = '<p style="text-align:center;">Generating Certificate. This can take up to a few minutes.</p>';
                $this->ssl_certificate['step'] = 4;
                $this->ssl_certificate['continue'] = true;
                break;
            case 4:
                
                if(!array_key_exists('generating_started', $this->ssl_certificate)){
                    $this->ssl_certificate['generating_started'] = time();
                    update_option('wp-force-ssl-certificate', $this->ssl_certificate);
                    
                    //Generate certificate
                    $domain = site_url();
                    $parse = parse_url($domain);
                    $domain = $parse['host'];

                    require_once('inc/wp-force-ssl-le.php');
                    $wp_force_ssl_le = new WP_Force_SSL_LetsEncrypt();
                    $generate_certificate_res = $wp_force_ssl_le->generate_ssl_certificate($this->ssl_certificate['email'], $domain, $this->ssl_certificate['challenge_dir'], $this->ssl_certificate['cert_dir']);
                    unset($this->ssl_certificate['generating_started']);
                    
                    if(is_wp_error($generate_certificate_res)){
                        $this->ssl_certificate['html'] = 'An error occured generating the certificate: ' . $generate_certificate_res->get_error_message();
                        $this->ssl_certificate['continue'] = false;
                        $this->ssl_certificate['error'] = true;
                    } else {
                        $certpath = trailingslashit($this->ssl_certificate['cert_dir']);
                        $certificate = file_get_contents($certpath . 'fullchain.crt');
                        $decoded_certificate = openssl_x509_parse($certificate);
                        
                        
                        if (class_exists('ZipArchive')) {
                            $this->ssl_certificate['cert_zip'] = trailingslashit(WP_CONTENT_DIR) . 'letsencrypt_' . $domain . '_' . md5($domain . time()) . '.zip';
                            $this->ssl_certificate['cert_url'] = content_url('letsencrypt_' . $domain . '_' . md5($domain . time()) . '.zip');
                        
                            $cert_zip = new ZipArchive();
                            $cert_zip->open($this->ssl_certificate['cert_zip'], ZipArchive::CREATE | ZipArchive::OVERWRITE);
                            $cert_zip->addFile($this->ssl_certificate['cert_dir'] . '/certificate.crt', 'certificate.crt');
                            $cert_zip->addFile($this->ssl_certificate['cert_dir'] . '/fullchain.crt', 'fullchain.crt');
                            $cert_zip->addFile($this->ssl_certificate['cert_dir'] . '/order', 'order');
                            $cert_zip->addFile($this->ssl_certificate['cert_dir'] . '/private.pem', 'private.pem');
                            $cert_zip->addFile($this->ssl_certificate['cert_dir'] . '/public.pem', 'public.pem');
                            $cert_zip->close();
                        } else {
                            $this->ssl_certificate['cert_zip'] = 'nozip';
                            $this->ssl_certificate['cert_url'] = 'nozip';
                            $this->generate_certificate_log('PHP ZipArchive extension not installed. Could not create certificate archive for download.', true);
                        }
                        

                        $cert_info['issued_to'] = $decoded_certificate['subject']['CN'];
                        $cert_info['issuer'] = $decoded_certificate['issuer']['CN'] . ', ' . $decoded_certificate['issuer']['O'] . ', ' . $decoded_certificate['issuer']['C'];
                        $cert_info['valid_from'] = date('Y-m-d H:i:s', $decoded_certificate['validFrom_time_t']);
                        $cert_info['valid_to'] = date('Y-m-d H:i:s', $decoded_certificate['validTo_time_t']);
                        
                        $this->ssl_certificate['html'] = '';
                        $this->ssl_certificate['html'] .= '<strong>Issued To:</strong> ' . $cert_info['issued_to'] . '<br />';
                        $this->ssl_certificate['html'] .= '<strong>Issuer:</strong> ' . $cert_info['issuer'] . '<br />';
                        $this->ssl_certificate['html'] .= '<strong>Valid From:</strong> ' . $cert_info['valid_from'] . '<br />';
                        $this->ssl_certificate['html'] .= '<strong>Valid To:</strong> ' . $cert_info['valid_to'] . '<br /><br />';
                        $this->ssl_certificate['html'] .= '<div class="clear"></div>';
                        $this->ssl_certificate['html'] .= '<div class="button button-primary generate-ssl-certificate-view">View Certificate Details</div>';
                        if(!empty($this->ssl_certificate['cert_url']) && $this->ssl_certificate['cert_url'] != 'nozip'){
                            $this->ssl_certificate['html'] .= '<a class="button button-primary download-ssl-certificate-view" href="' . $this->ssl_certificate['cert_url'] . '" target="_blank">Download Certificate</div>';
                        } 

                        $this->ssl_certificate['continue'] = false;
                    }
                } else {
                    if(time() - $this->ssl_certificate['generating_started'] < 180){
                        $this->ssl_certificate['html'] = 'Resuming Generating Certificate. This can take up to a few minutes.';
                        $this->ssl_certificate['step'] = 4;
                        $this->ssl_certificate['continue'] = true;
                    } else {
                        $this->ssl_certificate['html'] = 'Generating Certificate Failed.';
                        $this->ssl_certificate['step'] = 4;
                        $this->ssl_certificate['continue'] = false;
                    }
                }
                break;
            default:
                $this->ssl_certificate['html'] =  'Unknown step';
                $this->ssl_certificate['continue'] = false;
                $this->ssl_certificate['error'] = true;
                break;
        }

        update_option('wp-force-ssl-certificate', $this->ssl_certificate);

        return $this->ssl_certificate;
    }

    function generate_certificate_details(){
        $html = '<div class="generate-ssl-certificate-popup">';
            $certpath = trailingslashit($this->ssl_certificate['cert_dir']);
            $certificate = file_get_contents($certpath . 'fullchain.crt');
            $decoded_certificate = openssl_x509_parse($certificate);
            
            $cert_info['issued_to'] = $decoded_certificate['subject']['CN'];
            $cert_info['issuer'] = $decoded_certificate['issuer']['CN'] . ', ' . $decoded_certificate['issuer']['O'] . ', ' . $decoded_certificate['issuer']['C'];
            $cert_info['valid_from'] = date('Y-m-d H:i:s', $decoded_certificate['validFrom_time_t']);
            $cert_info['valid_to'] = date('Y-m-d H:i:s', $decoded_certificate['validTo_time_t']);

            $html .= '<strong>Issued To:</strong> ' . $cert_info['issued_to'] . '<br />';
            $html .= '<strong>Issuer:</strong> ' . $cert_info['issuer'] . '<br />';
            $html .= '<strong>Valid From:</strong> ' . $cert_info['valid_from'] . '<br />';
            $html .= '<strong>Valid To:</strong> ' . $cert_info['valid_to'] . '<br />';

            $html .= '<h3>Certificate: (CRT)</h3>';
            $html .= '<textarea style="width:100%; height: 200px"> ' . $certificate . '</textarea>';

            $html .= '<h3>Private Key (KEY)</h3>';
            $html .= '<textarea style="width:100%; height: 200px"> ' . file_get_contents($certpath . 'private.pem') . '</textarea>';
        $html .= '</div>';
        return $html;
    }

    function generate_certificate_log($message, $error = false){
        array_unshift($this->ssl_certificate['log'], array('time' => '[' . date('Y-m-d H:i:s', time()) . ']', 'message' => $message, 'error' => $error));
        update_option('wp-force-ssl-certificate', $this->ssl_certificate);
    }
    /**
     * Saves SSL options.
     *
     * @param array $params
     * @return bool
     */
    function save_ssl_options($params)
    {
        global $wp_force_ssl_tools, $wp_force_ssl_licensing;

        $options = $this->get_options();
        $params = shortcode_atts(
            array(
                'fix_frontend_mixed_content_fixer' => false,
                'fix_backend_mixed_content_fixer' => false,
                'hsts' => false,
                'force_secure_cookies' => false,
                'htaccess_301_redirect' => false,
                'php_301_redirect' => false,
                'xss_protection' => false,
                'x_content_options' => false,
                'referrer_policy' => false,
                'expect_ct' => false,
                'x_frame_options' => false,
                'permissions_policy' => false,
                'permissions_policy_rules' => '{}',
                'cert_expiration_email' => '',
                'adminbar_menu' => true
            ),
            (array) $params
        );

        if ($options['cert_expiration_email'] != sanitize_text_field($params['cert_expiration_email'])) {
            $wp_force_ssl_licensing->is_active(false, true);
        }

        $options['fix_frontend_mixed_content_fixer'] = (int) $params['fix_frontend_mixed_content_fixer'];
        $options['fix_backend_mixed_content_fixer'] = (int) $params['fix_backend_mixed_content_fixer'];
        $options['hsts'] = (int) $params['hsts'];
        $options['force_secure_cookies'] = (int) $params['force_secure_cookies'];
        $options['htaccess_301_redirect'] = (int) $params['htaccess_301_redirect'];
        $options['php_301_redirect'] = (int) $params['php_301_redirect'];
        $options['xss_protection'] = (int) $params['xss_protection'];
        $options['x_content_options'] = trim($params['x_content_options']);
        $options['referrer_policy'] = (int) $params['referrer_policy'];
        $options['expect_ct'] = trim($params['expect_ct']);
        $options['x_frame_options'] = (int) $params['x_frame_options'];
        $options['adminbar_menu'] = (int) $params['adminbar_menu'];
        $options['permissions_policy'] = (int) $params['permissions_policy'];
        $options['permissions_policy_rules'] = sanitize_text_field($params['permissions_policy_rules']);
        $options['cert_expiration_email'] = sanitize_text_field($params['cert_expiration_email']);

        if ($options['force_secure_cookies']) {
            $wp_force_ssl_tools->enable_secure_cookies();
        } else {
            $wp_force_ssl_tools->disable_secure_cookies();
        }

        if ($options['htaccess_301_redirect']) {
            $wp_force_ssl_tools->enable_htaccess_redirect();
        } else {
            $wp_force_ssl_tools->disable_htaccess_redirect();
        }

        $this->update_options('options', $options);

        WP_Force_SSL_Utility::clear_3rd_party_cache();

        return true;
    } // save_snapshot_options


    function get_scanner_pages()
    {
        $this->scanner['pages'] = array();
        $this->scanner['pages'][] = array('title' => get_bloginfo('name'), 'url' => get_bloginfo('url'), 'page_id' => 0);

        $menus = get_nav_menu_locations();
        foreach ($menus as $menu_id) {
            $menu_items = wp_get_nav_menu_items($menu_id);

            foreach ((array)$menu_items as $menu_item) {
                if (isset($menu_item->url) && strpos($menu_item->url, home_url()) !== false) {
                    $this->scanner['pages'][] = array('title' => $menu_item->title, 'url' => $menu_item->url, 'page_id' => $menu_item->ID);
                }
            }
        }

        $pages = get_posts(array('post_type' => get_post_types(array('public' => true)), 'numberposts' => 1000));
        foreach ($pages as $page) {
            if (!in_array($page->ID, $this->scanner['pages'])) {
                $this->scanner['pages'][] = array('title' => get_the_title($page->ID), 'url' => get_permalink($page->ID), 'page_id' => $page->ID);
            }
        }

        return $this->scanner['pages'];
    }

    function get_rebranding($key = false)
    {
        $license = $this->get_license();
        if (is_array($license) && array_key_exists('meta', $license) && is_array($license['meta']) && array_key_exists('rebrand', $license['meta']) && !empty($license['meta']['rebrand'])) {
            if (!empty($key)) {
                return $license['meta']['rebrand'][$key];
            }
            return $license['meta']['rebrand'];
        } else {
            return false;
        }
    } // get_rebranding

    function scan_page($page_id)
    {
        $page_url = $page_id == 0 ? home_url() : get_permalink($page_id);
        $page_content = $this->scanner_get_page_content($page_url, true);
        if (is_wp_error($page_content)) {
            $this->scanner['files']['errors'][] = array('url' => $page_url, 'error' => 'Error scanning <strong>' . $page_url . '</strong>(' . $page_id . '):' . $page_content->get_error_message());
        } else {
            $this->scanner_find_js_css_files($page_id, $page_content);
            $this->scanner_find_external_resources($page_id, $page_content);
        }
        $this->scanner['pages'][] = $page_id;
        update_option('wp-force-ssl-scanner', $this->scanner);
    }

    function scanner_get_results($datatable = false)
    {
        if (!$datatable) {
            return array('last_scan' => is_array($this->scanner) && array_key_exists('start', $this->scanner) && $this->scanner['start'] > 0 ? human_time_diff($this->scanner['start'], time()) : false, 'total_pages' => count($this->scanner['pages']));
        }

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        $scanner_results = array();
        foreach ($this->scanner['files'] as $group => $results) {
            if ($group == 'css' || $group == 'js') {
                foreach ($this->scanner['files'][$group] as $file => $file_results) {
                    if (count($file_results['http_files']) > 0) {
                        $scanner_results[] = array(
                            'DT_RowId' => rand(1000, 9999),
                            '<div class="wfssl-badge wfssl-badge-yellow">warning</div>',
                            strtoupper($group) . ' file with mixed content',
                            $this->get_pages_html($file_results['pages']),
                            'File <strong>' . $file . '</strong> contains ' . str_replace(array('http://', 'https://'), '//', implode(', ', $file_results['http_files']))
                        );
                    }
                }
            }

            if ($group == 'external') {
                foreach ($this->scanner['files']['external'] as $file => $file_results) {
                    if ($file_results['ssl'] !== true) {
                        $scanner_results[] = array(
                            'DT_RowId' => rand(1000, 9999),
                            '<div class="wfssl-badge wfssl-badge-yellow">warning</div>',
                            'HTTP file',
                            $this->get_pages_html($file_results['pages']),
                            '<span title="' . $file_results['ssl_error'] . '" class="tooltip">File <strong>' . $file . '</strong> could not be loaded via HTTPS</span>'
                        );
                    }
                }
            }

            if ($group == 'errors') {
                foreach ($this->scanner['files']['errors'] as $file => $file_results) {
                    $scanner_results[] = array(
                        'DT_RowId' => rand(1000, 9999),
                        '<div class="wfssl-badge wfssl-badge-gray">scan error</div>',
                        'Scan error',
                        $file_results['url'],
                        $file_results['error']
                    );
                }
            }
        }

        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $scanner_results = array_slice($scanner_results, (int)$_GET['iDisplayStart'], (int)$_GET['iDisplayLength']);
        }

        if (isset($_GET['iSortCol_0'])) {
            $sorting = array_column($scanner_results, $_GET['iSortCol_0']);
            if ($_GET['sSortDir_0'] == 'asc') {
                array_multisort($sorting, SORT_ASC, $scanner_results);
            } else {
                array_multisort($sorting, SORT_DESC, $scanner_results);
            }
        }

        $output = array(
            "sEcho" => intval(@$_GET['sEcho']),
            "iTotalRecords" => count($scanner_results),
            "iTotalDisplayRecords" => count($scanner_results),
            "aaData" => $scanner_results
        );

        echo json_encode($output);

        die();
    }

    function get_page_title($page_id)
    {
        if ($page_id == 0) {
            return get_bloginfo('name') . ' (front page)';
        } else {
            return get_the_title($page_id);
        }
    }

    function get_page_href($page_id)
    {
        if ($page_id == 0) {
            return get_bloginfo('url');
        } else {
            return get_permalink($page_id);
        }
    }

    function get_pages_html($pages)
    {
        $pages_html = array();

        if (is_array($pages)) {
            foreach ($pages as $page) {
                $pages_html[] = '<a href="' . $this->get_page_href($page) . '" target="_blank" title="' . $this->get_page_title($page) . '">' . $this->get_page_title($page) . '</a>';
            }
        } else {
            $pages_html[] = '<a href="' . $this->get_page_href($pages) . '" target="_blank" title="' . $this->get_page_title($pages) . '">' . $this->get_page_title($pages) . '</a>';
        }

        return implode('<br />', $pages_html);
    }

    function scanner_get_page_content($url, $page = false)
    {

        $request_params = array('sslverify' => false, 'timeout' => 25, 'redirection' => 2);
        $request_data = array('wp_force_ssl_scanner' => true);
        if ($page) {
            $url = rtrim(add_query_arg($request_data, $url, '&'));
        }
        $response = wp_remote_get($url, $request_params);
        if (is_wp_error(($response))) {
            return $response;
        }

        if (wp_remote_retrieve_response_code($response) < 200 && wp_remote_retrieve_response_code($response) > 300) {
            return new WP_Error(1, 'Error: status ' . wp_remote_retrieve_response_code($response));
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return new WP_Error(1, 'Error: empty page content.');
        }

        return wp_remote_retrieve_body($response);
    }

    function scanner_find_js_css_files($page_id, $html)
    {
        $patterns    = array(
            'js' => "/(http:\/\/|https:\/\/|\/\/)([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-]?\.js)(?:((\?.*[\'|\"])|['|\"]))/",
            'css' => "/(http:\/\/|https:\/\/|\/\/)([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-]?\.css)(?:((\?.*[\'|\"])|['|\"]))/"
        );

        foreach ($patterns as $pattern_type => $pattern) {
            if (preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER)) {
                foreach ($matches[1] as $key => $match) {
                    if (empty($matches[2][$key])) {
                        continue;
                    }
                    $file = $matches[1][$key] . $matches[2][$key];

                    if (
                        !array_key_exists($file, $this->scanner['files'][$pattern_type])
                        && !$this->is_plugin_file($file)
                        && !$this->is_wp_core_file($file)
                    ) {
                        if (!in_array($file, $this->scanner['files'][$pattern_type])) {
                            $this->scanner['files'][$pattern_type][$file] = array();
                        }
                        $this->scanner['files'][$pattern_type][$file]['pages'][] = $page_id;
                        $this->scanner['files'][$pattern_type][$file]['http_files'] = $this->parse_for_http($file);
                    }
                }
            }
        }
    }

    function scanner_find_external_resources($page_id, $html)
    {
        foreach ($this->external_domain_patterns() as $pattern) {
            if (preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER)) {
                foreach ($matches[1] as $key => $match) {
                    if (empty($matches[2][$key])) {
                        continue;
                    }
                    $external_resource = $matches[1][$key] . $matches[2][$key];
                    if (!array_key_exists($external_resource, $this->scanner['files']['external'])) {
                        if (!in_array($external_resource, $this->scanner['files']['external'])) {
                            $this->scanner['files']['external'][$external_resource] = array(
                                'pages' => array(),
                                'ssl' => false
                            );

                            $result = $this->scanner_get_page_content(str_replace("http://", "https://", $external_resource));
                            if (is_wp_error($result)) {
                                //$this->scanner['files']['errors'][] = array('url' => ($page_id == 0?home_url():get_permalink($page_id)), 'error' => $result->get_error_message() . ' for file: <strong>' . $external_resource . '</strong>');
                                $this->scanner['files']['external'][$external_resource]['ssl'] = false;
                                $this->scanner['files']['external'][$external_resource]['ssl_error'] = $result->get_error_message() . ' for file: ' . $external_resource;
                            } else {
                                $this->scanner['files']['external'][$external_resource]['ssl'] = true;
                            }
                        }
                        $this->scanner['files']['external'][$external_resource]['pages'] = $page_id;
                    }
                }
            }
        }
    }

    function external_domain_patterns($url_only = false)
    {
        $url_pattern    = '([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-]?)(?:[\'|\"])';
        $image_pattern  = '([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-]?[.jpg|.gif|.jpeg|.png|.svg|.webp])(?:((\?.*[\'|"])|[\'|"]))';
        $script_pattern = '([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-]?[.js])(?:((\?.*[\'|\"])|[\'|\"]))';
        $style_pattern  = '([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-]?[.css])(?:((\?.*[\'|\"])|[\'|\"]))';

        $patterns = array();

        $domain = preg_quote(str_replace(array("http://", "https://"), "", home_url()), "/");
        if (!$url_only) {
            $patterns = array_merge($patterns, array(
                '/url\([\'"]?\K(http:\/\/|https:\/\/)(?!(' . $domain . '))' . $image_pattern . '/i',
                '/<link[^>].*?href=[\'"]\K(http:\/\/|https:\/\/)(?!' . $domain . ')' . $style_pattern . '/i',
                '/<meta property="og:image" .*?content=[\'"]\K(http:\/\/|https:\/\/)(?!' . $domain . ')' . $image_pattern . '/i',
                '/<(?:img)[^>].*?src=[\'"]\K(http:\/\/|https:\/\/)(?!' . $domain . ')' . $image_pattern . '/i',
                '/<(?:iframe)[^>].*?src=[\'"]\K(http:\/\/|https:\/\/)(?!' . $domain . ')' . $url_pattern . '/i',
                '/<script[^>]*?src=[\'"]\K(http:\/\/|https:\/\/)(?!' . $domain . ')' . $script_pattern . '/i',
                '/<form[^>]*?action=[\'"]\K(http:\/\/|https:\/\/)(?!' . $domain . ')' . $url_pattern . '/i',
                '/"url":"\K(http:\/\/|https:\/\/)(?!' . $domain . ')' . $image_pattern . '/i'
            ));
        } else {
            $url_pattern = '([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-]?)';
            $patterns    = array_merge($patterns, array('/\K(http:\/\/|https:\/\/)(?!(' . $domain . '))' . $url_pattern . '/i'));
        }

        return $patterns;
    }

    private function parse_for_http($url)
    {
        $files = array();
        $url_pattern = '([\w.,@?^=%&:\/~+#-]*[\w@?^=%&\/~+#-]?)';
        $patterns    = array(
            '/url\([\'"]?\K(http:\/\/)' . $url_pattern . '/i',
            '/<link [^>].*?href=[\'"]\K(http:\/\/)' . $url_pattern . '/i',
            '/<meta property="og:image" .*?content=[\'"]\K(http:\/\/)' . $url_pattern . '/i',
            '/<(?:img|iframe)[^>].*?src=[\'"]\K(http:\/\/)' . $url_pattern . '/i',
            '/<script [^>]*?src=[\'"]\K(http:\/\/)' . $url_pattern . '/i'
        );
        if(strpos($url,'//') == 0){
            $url = 'http:'.$url;  
        }
        $content = $this->scanner_get_page_content($url);
        if (is_wp_error($content)) {
            $this->scanner['files']['errors'][] = array('url' => $url, 'error' => 'Error scanning file ' . $url . ': ' . $content->get_error_message());
            return $files;
        }

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches, PREG_PATTERN_ORDER)) {
                foreach ($matches[1] as $key => $match) {
                    if (empty($matches[2][$key])) {
                        continue;
                    }
                    $file_with_http = $matches[1][$key] . $matches[2][$key];
                    if (!in_array($file_with_http, $files)) {
                        $files[] = $file_with_http;
                    }
                }
            }
        }
        return $files;
    }

    public function is_plugin_file($file)
    {
        $plugins_url = plugins_url();
        $plugins_url = str_replace(
            array("https://", "http://"),
            "",
            $plugins_url
        );

        if (strpos($file, $plugins_url) === false) {
            return false;
        }

        return true;
    }

    public function is_wp_core_file($file)
    {
        $wp_includes_url = includes_url();
        $wp_includes_url = str_replace(
            array("https://", "http://"),
            "",
            $wp_includes_url
        );

        $wp_admin_url = admin_url();
        $wp_admin_url = str_replace(
            array("https://", "http://"),
            "",
            $wp_admin_url
        );


        if (
            strpos($file, $wp_includes_url) === false
            && strpos($file, $wp_admin_url) === false
        ) {
            return false;
        }

        return true;
    }

    /**
     * Add action links to plugins table, left part
     *
     * @param array  $links  Initial list of links.
     *
     * @return array
     */
    function plugin_action_links($links)
    {
        $settings_link = '<a href="' . admin_url('options-general.php?page=wp-force-ssl') . '" title="Configure SSL settings">Configure SSL</a>';

        array_unshift($links, $settings_link);

        return $links;
    } // plugin_action_links


    /**
     * Add links to plugin's description in plugins table
     *
     * @param array  $links  Initial list of links.
     * @param string $file   Basename of current plugin.
     *
     * @return array
     */
    function plugin_meta_links($links, $file)
    {
        if ($file !== plugin_basename(__FILE__)) {
            return $links;
        }

        if (!WP_Force_SSL_Utility::whitelabel_filter()) {
            unset($links[1]);
            unset($links[2]);
            return $links;
        }

        $support_link = '<a target="_blank" href="' . WP_Force_SSL_Utility::generate_web_link('plugins-table-right', '/support/') . '" title="Get help">Support</a>';
        $home_link = '<a target="_blank" href="' . WP_Force_SSL_Utility::generate_web_link('plugins-table-right') . '" title="Plugin Homepage">Plugin Homepage</a>';

        $links[] = $support_link;
        $links[] = $home_link;

        return $links;
    } // plugin_meta_links


    /**
     * Test if we're on WP Force SSL admin page
     *
     * @return bool
     */
    function is_plugin_page()
    {
        $current_screen = get_current_screen();

        if (!empty($current_screen) && $current_screen->id == 'settings_page_wp-force-ssl') {
            return true;
        } else {
            return false;
        }
    } // is_plugin_page


    /**
     * Add powered by text in admin footer
     *
     * @param string  $text  Default footer text.
     *
     * @return string
     */
    function admin_footer_text($text)
    {
        if (!$this->is_plugin_page() || !WP_Force_SSL_Utility::whitelabel_filter()) {
            return $text;
        }

        if ($this->get_rebranding() !== false) {
            $text = $this->get_rebranding('footer_text');
        } else {
            $text = '<i class="wfssl-footer"><a href="' . WP_Force_SSL_Utility::generate_web_link('admin_footer') . '" title="' . __('Visit WP Force SSL page for more info', 'wp-force-ssl') . '" target="_blank">WP Force SSL PRO</a> v' . $this->version . '</i>';
        }

        return $text;
    } // admin_footer_text


    /**
     * Loads plugin's translated strings
     *
     * @return null
     */
    function load_textdomain()
    {
        load_plugin_textdomain('wp-force-ssl');
    } // load_textdomain

    /**
     * Main/starter function for displaying plugin's admin page
     *
     * @return null
     */
    function plugin_page()
    {
        global $wp_force_ssl_licensing;
        $options = $this->get_options();

        // double check for admin privileges
        if (!current_user_can('administrator')) {
            wp_die('Sorry, you are not allowed to access this page.');
        }

        $logo = $this->get_rebranding('logo_url');
        $plugin_name = $this->get_rebranding('name');

        echo '<header>';
        echo '<div class="wfssl-container">';
        if ($logo === false || empty($logo)) {
            echo '<img id="logo-icon" src="' . esc_url($this->plugin_url) . 'img/wp-force-ssl-logo.png" title="WP Force SSL PRO" alt="WP Force SSL PRO" />';
        } else {
            echo '<img id="logo-icon" src="' . $logo . '" title="' . $plugin_name . '" alt="' . $plugin_name . '"><h1>' . $plugin_name . '</h1>';
        }
        echo '</div>';
        echo '</header>';

        echo '<div id="wfssl-notifications">';
        echo '</div>';

        echo '<div id="wfssl-tabs" class="ui-tabs" style="display: none;">';
        $tabs = array();

        if ($wp_force_ssl_licensing->is_active()) {
            $tabs[] = array('id' => 'tab_status', 'class' => 'wfssl-tab', 'label' => 'Status', 'callback' => 'tab_status');
            $tabs[] = array('id' => 'tab_settings', 'class' => 'wfssl-tab', 'label' => 'Settings', 'callback' => 'tab_settings');
            $tabs[] = array('id' => 'tab_scanner', 'class' => 'wfssl-tab', 'label' => 'Content Scanner', 'callback' => 'tab_scanner');
            $tabs[] = array('id' => 'tab_ssl', 'class' => 'wfssl-tab', 'label' => 'SSL Certificate', 'callback' => 'tab_ssl');
            $tabs[] = array('id' => 'tab_support', 'class' => 'wfssl-tab', 'label' => 'Support', 'callback' => 'tab_support');
        }

        if (WP_Force_SSL_Utility::whitelabel_filter()) {
            $tabs[] = array('id' => 'tab_license', 'class' => 'wfssl-tab', 'label' => 'License', 'callback' => 'tab_license');
        }

        echo '<nav>';
        echo '<div class="wfssl-container">';
        echo '<ul class="wfssl-main-tab">';
        foreach ($tabs as $tab) {
            echo '<li id="button-' . esc_attr($tab['id']) . '" class="' . esc_attr($tab['class']) . '"><a href="#' . esc_attr($tab['id']) . '">' . esc_attr($tab['label']) . '</a></li>';
        }
        echo '</ul>';
        echo '</div>'; // container
        echo '</nav>';


        // tabs
        echo '<div class="wfssl-container">';

        foreach ($tabs as $tab) {
            if (is_callable(array($this, $tab['callback']))) {
                echo '<div id="' . esc_attr($tab['id']) . '" class="wfssl-tab-content">';
                call_user_func(array($this, $tab['callback']));
                echo '</div>';
            }
        }

        echo '</div>'; // wfssl-container

        echo '</div>'; // wrap
    } // plugin_page

    function tab_support()
    {
        if ($this->get_rebranding() === false) {
            echo '<div style="overflow: auto;">
                    <div class="wfssl-box">
                        <span class="dashicons dashicons-format-aside wfssl-support-icon"></span>
                        <h3>Is there any documentation available?</h3>
                        <p>Sure! Detailed documentation is available on our site. If it doesn\'t help <a href="#" class="open-beacon">contact our friendly support</a>.</p>
                        <a href="https://wpforcessl.com/documentation/" target="_blank" class="button button-primary">Read</a>
                    </div>

                    <div class="wfssl-box">
                        <span class="dashicons dashicons-sos wfssl-support-icon"></span>
                        <h3>Support</h3>
                        <p>Something is not working the way it\'s supposed to? Contact our friendly support, they\'ll respond ASAP!</p>';
            if (WP_Force_SSL_Utility::whitelabel_filter()) {
                echo '<div class="button button-primary open-beacon">Contact</div>';
            }
            echo '</div>';
            echo '</div>';
            echo '<h3>FAQ</h3>
                    <div class="wfssl-accordion-wfssl-accordion-tabs">
                        <div class="wfssl-accordion-tab">
                            <input type="checkbox" id="faq1">
                            <label class="wfssl-accordion-tab-label wfssl-fs-18 wfssl-font" for="faq1">Will WP Force SSL slow down my site?</label>
                            <div class="wfssl-accordion-tab-content">
                            Absolutely not. Everything the plugin does happens in the admin. Nothing is loaded, added, or processed on the front-end so you can rest assured there is no impact on the performance of your site.
                            </div>
                        </div>
                        
                        <div class="wfssl-accordion-tab">
                            <input type="checkbox" id="faq2">
                            <label class="wfssl-accordion-tab-label wfssl-fs-18 wfssl-font" for="faq2">Can you install an SSL certificate for me?</label>
                            <div class="wfssl-accordion-tab-content">
                            Sorry, at the moment we can’t. The automatic SSL certificate installation feature is on our to-do and will be available in one of the future versions.
                            </div>
                        </div>

                        <div class="wfssl-accordion-tab">
                            <input type="checkbox" id="faq3">
                            <label class="wfssl-accordion-tab-label wfssl-fs-18 wfssl-font" for="faq3">Can I move my license between sites?</label>
                            <div class="wfssl-accordion-tab-content">
                            Absolutely! Once you deactivate WP Force SSL on a site, the license goes back into your license pool, and you can activate it on another site. At any given time you can see all sites using your licenses when you log in to the WP Force SSL Dashboard.
                            </div>
                        </div>
                        
                        <div class="wfssl-accordion-tab">
                            <input type="checkbox" id="faq4">
                            <label class="wfssl-accordion-tab-label wfssl-fs-18 wfssl-font" for="faq4">Is WP Force SSL dangerous for my site?</label>
                            <div class="wfssl-accordion-tab-content">
                            No, definitely not! The plugin does not make any permanent changes to your site so even if it comes to a worst-case scenario you can just disable the plugin and that will undo all changes.
                            </div>
                        </div>

                        <div class="wfssl-accordion-tab">
                            <input type="checkbox" id="faq5">
                            <label class="wfssl-accordion-tab-label wfssl-fs-18 wfssl-font" for="faq5">Can you generate/get an SSL certificate for me?</label>
                            <div class="wfssl-accordion-tab-content">
                            At the moment no. Sorry. We are already working on a feature that will automatically get a certificate from Let’s Encrypt and install it on your site but it’s not ready yet.
                            </div>
                        </div>

                        <div class="wfssl-accordion-tab">
                            <input type="checkbox" id="faq6">
                            <label class="wfssl-accordion-tab-label wfssl-fs-18 wfssl-font" for="faq6">Where and how do I manage my licenses?</label>
                            <div class="wfssl-accordion-tab-content">
                            Purchases, sites, licenses & SSL monitors are managed in the <a href="' . WP_Force_SSL_Utility::generate_dashboard_link() . '" target="_blank">WP Force SSL Dashboard</a>. It’s a central place to manage all your sites.
                            </div>
                        </div>

                        <div class="wfssl-accordion-tab">
                            <input type="checkbox" id="faq7">
                            <label class="wfssl-accordion-tab-label wfssl-fs-18 wfssl-font" for="faq7">Will WP Force SSL modify my files, database or any content?</label>
                            <div class="wfssl-accordion-tab-content">
                            It will not automatically modify anything. If anything needs permanent changes you’ll be prompted to double-confirm the change. However, on 90% of sites, all changes are done on the fly so they are not permanent. Disabling the plugin undoes all changes.
                            </div>
                        </div>

                        <div class="wfssl-accordion-tab">
                            <input type="checkbox" id="faq8">
                            <label class="wfssl-accordion-tab-label wfssl-fs-18 wfssl-font" for="faq8">I just moved my site to another address, will WP Force SSL help?</label>
                            <div class="wfssl-accordion-tab-content">
                            Definitely! Especially if you moved from HTTP to HTTPS. The plugin will make sure to properly redirect all your content, check your SSL certificate, and add other security features.
                            </div>
                        </div>
                    </div>';
        } else {
            echo $this->get_rebranding('support_content');
        }
    }

    function tab_status()
    {
        echo '<div id="status_progress_wrapper" class="wfssl-progress" style="display:none;">
                <div id="status_progress" class="bar orange" style="width:0%">
                    <div id="status_progress_text" class="wfssl-progress-text"></div>
                </div>
              </div>

              <div id="status_tasks" class="wfssl-labels" style="display:none;">
                <div class="status-tasks status-tasks-selected">All tasks</div>
                <div class="status-tasks-remaining">Remaining tasks</div>
              </div>

              <div id="test-results-wrapper">
                <div class="loading-wrapper">
                    <img class="wfssl_flicker" src="' . esc_url($this->plugin_url) . 'img/wp-force-ssl-icon.png' . '" alt="Loading. Please wait." title="Loading. Please wait.">
                    <p>Loading. Please wait.</p>
                </div>
              </div>';
        echo '<div class="button button-primary run-tests" style="float: right; display:none;">Run Tests Again</div>';
    } // tab_status

    function tab_settings()
    {
        $options = $this->get_options();

        echo '<table class="form-table" id="settings-table">';

        echo '<tr><td>
            <label for="fix_frontend_mixed_content_fixer">Fix mixed content in frontend</label>
            <small>Fix mixed content in the frontend on the fly (files and/or database is not changed) by replacing http:// with https:// for linked resources.</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('fix_frontend_mixed_content_fixer', array('saved_value' => $options['fix_frontend_mixed_content_fixer']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="fix_backend_mixed_content_fixer">Fix mixed content in backend</label>
            <small>Fix mixed content in the backend (wp-admin) on the fly (files and/or database is not changed) by replacing http:// with https:// for linked resources</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('fix_backend_mixed_content_fixer', array('saved_value' => $options['fix_backend_mixed_content_fixer']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="hsts">Enable HSTS</label>
            <small>HSTS (HTTP Strict Transport Security) is a header sent by your website to your visitor\'s browser telling it to only use HTTPS to connect to the website. If someone tried to perform a man-in-the-middle attack and redirect the visitor to their own malicious version of the domain the browser will refuse to load the website via HTTP and force them to show a valid SSL certificate for the domain, which they will not have and thus the attack will fail.</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('hsts', array('saved_value' => $options['hsts']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="force_secure_cookies">Force Secure Cookies</label>
            <small>Cookies are small packets of data stored by the website on your computer so it remembers information about you like your logged in state. But most times this information in sensitive, so you should enable this option to harden the way cookies are interacted with by your browser and to prevent anyone else from reading them.</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('force_secure_cookies', array('saved_value' => $options['force_secure_cookies']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="htaccess_301_redirect">301 Redirect HTTP to HTTPS requests via htaccess</label>
            <small>Redirect all http:// requests to https:// via .htaccess as soon as the request is received. This is slighly faster than PHP redirect but if your server does not use .htaccess you can use the PHP redirect option below.</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('htaccess_301_redirect', array('saved_value' => $options['htaccess_301_redirect']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="php_301_redirect">301 Redirect HTTP to HTTPS requests via PHP</label>
            <small>Redirect all http:// requests to https:// via PHP</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('php_301_redirect', array('saved_value' => $options['php_301_redirect']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="xss_protection">Cross-site scripting (X-XSS) protection</label>
            <small>Protects your site from cross-site scripting attacks. If a cross-site scripting attack is detected, the browser will automatically block these requests.</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('xss_protection', array('saved_value' => $options['xss_protection']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="x_content_options">X Content Type Options</label>
            <small>This header prevents MIME-sniffing, which is used to disguise the content type of malicious files being uploaded to the website.</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('x_content_options', array('saved_value' => $options['x_content_options']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="referrer_policy">Referrer Policy</label>
            <small>To prevent data leakage, only send referrer information when navigating to the same protocol (HTTPS->HTTPS) and not when downgrading (HTTPS->HTTP).</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('referrer_policy', array('saved_value' => $options['referrer_policy']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="expect_ct">Expect CT</label>
            <small>Enables the Expect-CT header, requesting that the browser check that any certificate for that site appears in public CT logs.</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('expect_ct', array('saved_value' => $options['expect_ct']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="x_frame_options">X Frame Options</label>
            <small>This header prevents your site from being loaded in an iFrame on other domains. This is used to prevent clickjacking attacks.</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('x_frame_options', array('saved_value' => $options['x_frame_options']));
        echo '</td></tr>';

        echo '<tr><td>
            <label for="permissions_policy">Permissions Policy</label>
            <small>The Permissions Policy allows you to specify which browser resources to allow on your site (i.e. microphone, webcam, etc.).</small>
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('permissions_policy', array('saved_value' => $options['permissions_policy']));
        echo '<div id="configure_permissions_policy" class="button button-primary" style="' . ($options['permissions_policy'] ? '' : 'display:none;') . ' min-height: 26px; padding: 2px 10px; height: 26px; margin-left: 10px;">Configure Policy</div>';
        echo '<textarea style="display:none;" id="permissions_policy_rules" name="permissions_policy_rules">';
        if (!empty($options['permissions_policy_rules'])) {
            WP_Force_SSL_Utility::wp_kses_wf(wp_unslash($options['permissions_policy_rules']));
        } else {
            echo '{}';
        }
        echo '</textarea>';
        echo '</td></tr>';

        $plugin_name = $this->get_rebranding('name');
        if ($plugin_name == false) {
            $plugin_name = 'WP Force SSL';
        }

        echo '<tr><td>
            <label for="adminbar_menu">Show ' . $plugin_name . ' menu to administrators in admin bar</label>
            
        </td><td>';
        WP_Force_SSL_Utility::create_toogle_switch('adminbar_menu', array('saved_value' => $options['adminbar_menu']));
        echo '</td></tr>';

        echo '</table>';

        echo '<p><a href="#" class="button button-primary save-ssl-options">Save options</a></p>';
    } //tab_settings

    function tab_scanner()
    {
        echo '<div id="scanner_progress_wrapper" class="wfssl-progress" style="display:none;">';
            echo '<div id="scanner_progress" class="bar">';
                echo '<div id="scanner_progress_text" class="wfssl-progress-text"></div>';
            echo '</div>';
        echo '</div>';

        echo '<div class="scanner-stats"></div>';
        echo '<table id="scanner-results">';
            echo '<thead>';
                echo '<tr>';
                    echo '<th>Status</th>';
                    echo '<th>Description</th>';
                    echo '<th>Location</th>';
                    echo '<th>Details</th>';
                echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            echo '</tbody>';
        echo '</table>';

        echo '<div id="start-scanner" class="button button-primary" style="display:none;">Start Scanning</div>';
    } //tab_scanner

    function tab_license()
    {
        global $wp_force_ssl_licensing;
        $options = $this->get_license();
        $rebranding = $this->get_rebranding();

        if ($rebranding === false) {
            echo '<p>License key is visible on the screen, right after purchasing. You can also find it in the confirmation email sent to the email address provided on purchase.<br>
                  If you don\'t have a license - <a target="_blank" href="' . WP_Force_SSL_Utility::generate_web_link('license-tab') . '">purchase one now</a>. In case of problems please <a href="#" class="open-beacon">contact support</a>.</p>';
            echo '<p>You can manage your licenses in the <a target="_blank" href="' . WP_Force_SSL_Utility::generate_dashboard_link('license-tab') . '">WP Force SSL Dashboard</a></p>';
        }
        echo '<table class="form-table">
                <tbody>
                <tr>
                    <th scope="row"><label for="license-key" style="line-height: 42px;">License Key:</label></th>
                    <td><input class="regular-text" type="text" id="license-key" value="" placeholder="' . (empty($options['license_key']) ? '12345678-12345678-12345678-12345678' : esc_attr(substr($options['license_key'], 0, 8)) . '-************************') . '"></td>
                </tr>';

        echo '<tr>
              <th scope="row"><label for="">License Status:</label></th>
              <td style="line-height:22px">';
            if ($wp_force_ssl_licensing->is_active()) {
                $license_formatted = $wp_force_ssl_licensing->get_license_formatted();
                echo '<b style="color: #66b317;">Active</b><br>';
                if ($this->get_rebranding() === false) {
                    echo 'Type: ' . esc_attr($license_formatted['name_long']);
                    echo '<br>Valid ' . esc_attr($license_formatted['valid_until']) . '</td>';
                }
            } else { // not active
                echo '<strong style="color: #ea1919;">Inactive</strong>';
                if (!empty($wp_force_ssl_licensing->get_license('error'))) {
                    echo '<br>Error: ' . esc_attr($wp_force_ssl_licensing->get_license('error'));
                }
            }
            echo '</td>';
        echo '</tr>';

        echo '<tr><td colspan="2"><br>';
        echo '<a href="#" id="save-license" data-text-wait="Validating. Please wait." class="button button-primary">Save &amp; Activate License</a>';

        if ($wp_force_ssl_licensing->is_active()) {
            echo '&nbsp; &nbsp;<a href="#" id="deactivate-license" class="button button-delete">Deactivate License</a>';
        } else {
            echo '&nbsp; &nbsp;<a href="#" class="button button-primary" data-text-wait="Validating. Please wait." id="wfssl_keyless_activation">Keyless Activation</a>';
        }
        echo '</td></tr>';
        echo '</tbody></table>';

        if ($wp_force_ssl_licensing->is_active('white_label')) {
            echo '<hr />';
            echo '<h4><span class="card-name">White-Label License Mode</span><div class="card-header-right"></div></h4>';
            echo '<p>Enabling the white-label license mode hides the License tab and removes all visible mentions of WebFactory Ltd.<br>To disable it append <strong>&amp;wp_force_ssl_wl=false</strong> to the WP Force SSL settings page URL.
                  Or save this URL and open it when you want to disable the white-label license mode:<br> <a href="' . admin_url('options-general.php?page=wp-force-ssl&wp_force_ssl_wl=false') . '">' . admin_url('options-general.php?page=wp-force-ssl&wp_force_ssl_wl=false') . '</a></p>';
            echo '<p><a href="' . admin_url('options-general.php?page=wp-force-ssl&wp_force_ssl_wl=true') . '" class="button button-secondary">Enable White-Label License Mode</a></p>';
        }
    }

    function tab_ssl()
    {
        global $wp_force_ssl_tests;
        $options = $this->get_options();
        echo '<h2>Certificate Information</h2>';
        echo '<div id="ssl_cert_details" class="wfssl-box wfssl-box-gray">';
            echo 'Loading certificate information ...<span class="wfssl-green wfssl_rotating dashicons dashicons-update"></span>';
        echo '</div>';
        echo '<div class="clear"></div>';
        
        if (!$wp_force_ssl_tests->is_localhost()) {
            echo '<h2>Real-Time SSL & Site Monitoring</h2>';
        }
        echo '<div id="wfssl-cert-email" class="wfssl-box wfssl-box-gray" style="display:none;">';
        
        if(!empty($options['cert_expiration_email']) && strlen($options['cert_expiration_email']) > 0){
            echo '<span class="wfssl-green dashicons dashicons-yes-alt"></span>';
        } else {
            echo '<span class="wfssl-red dashicons dashicons-dismiss"></span>';
        }

        if ($wp_force_ssl_tests->is_localhost()) {
            echo '<small class="wfssl-red">Real-time SSL monitoring cannot be enabled on localhost or non-publicly accessible installs</small>';
            echo '<div class="clear"><br /><br /></div>';
        }

        $plugin_name = $this->get_rebranding('name');
        if ($plugin_name == false) {
            $plugin_name = 'WP Force SSL';
        }
        
        echo 'Your websites certificate will be monitored remotely from the ' . $plugin_name . ' Dashboard and you will receive an email notification if any issues are discovered. Enter your email address below or leave empty to disable monitoring.';
        echo '<div class="clear"></div>';
            echo '<div id="wfssl-cert-expiration-email-box">';
                echo '<input id="cert_expiration_email" name="cert_expiration_email" class="wfssl-cert-expiration-email-input" type="text" placeholder="Type your email here..." ' . ($wp_force_ssl_tests->is_localhost() ? 'disabled' : '') . ' value="' . ($wp_force_ssl_tests->is_localhost() ? '' : esc_attr($options['cert_expiration_email'])) . '" style="height:32px; width:353px;">';
                echo '<div class="button ' . ($wp_force_ssl_tests->is_localhost() ? 'disabled' : 'button-primary') . ' save-ssl-options" style="margin:0px;">Save</div>';
            echo '</div>';
        echo '</div>';
        echo '<div class="clear"></div>';

        if (!$wp_force_ssl_tests->is_localhost()) {
            echo '<h2>Generate SSL Certificate</h2>';
            echo '<div class="wfssl-box wfssl-box-gray">';
            echo '<div id="generate_ssl_certificate_loader" class="loading-wrapper" style="display:none;"><img class="wfssl_flicker" src="' . esc_url($this->plugin_url) . 'img/wp-force-ssl-icon.png" alt="Loading. Please wait." title="Loading. Please wait."></div>';
            echo '<form id="generate_ssl_certificate_html"></form>';
            echo '<div class="button ' . ($wp_force_ssl_tests->is_localhost() && 0 ? 'disabled' : 'button-primary') . ' generate-ssl-certificate">Generate SSL Certificate</div>';
            echo '<div class="button ' . ($wp_force_ssl_tests->is_localhost() && 0 ? 'disabled' : '') . ' generate-ssl-certificate-reset" style="float:right; display:none;">Start Over</div>';
            echo '<div class="button ' . ($wp_force_ssl_tests->is_localhost() && 0 ? 'disabled' : '') . ' generate-ssl-certificate-toggle-log" style="float:right;">Show Log</div>';
            echo '<div id="generate_ssl_certificate_log" class="clear" style="display:none;"></div>';
            echo '</div>';
        }
    }

    /**
     * Use when deactivating the plugin to clean up.
     *
     * @return bool
     */
    static function deactivate_plugin()
    {
        global $wp_force_ssl_tools;
        $options = get_option('wp-force-ssl');
        if (array_key_exists('options', $options) && array_key_exists('htaccess_301_redirect', $options['options'])) {
            $options['options']['htaccess_301_redirect'] = false;
            update_option('wp-force-ssl', $options);
        }

        $wp_force_ssl_tools->disable_htaccess_redirect();

        return true;
    } // uninstall_plugin

    /**
     * Use when uninstalling (deleting) the plugin to clean up.
     *
     * @return bool
     */
    static function uninstall_plugin()
    {
        $options = array('wf_licensing_wfssl', 'wp-force-ssl', 'wp-force-ssl-scanner');

        foreach ($options as $option_name) {
            delete_option($option_name);
            // For site options in Multisite
            delete_site_option($option_name);
        }

        return true;
    } // uninstall_plugin



    /**
     * Disabled; we use singleton pattern so magic functions need to be disabled
     *
     * @return null
     */
    public function __clone()
    {
    }


    /**
     * Disabled; we use singleton pattern so magic functions need to be disabled
     *
     * @return null
     */
    public function __sleep()
    {
    }


    /**
     * Disabled; we use singleton pattern so magic functions need to be disabled
     *
     * @return null
     */
    public function __wakeup()
    {
    }
} // WP_Force_SSL class


// Create plugin instance and hook things up
global $wp_force_ssl;
$wp_force_ssl = WP_Force_SSL::getInstance();
add_action('plugins_loaded', array($wp_force_ssl, 'load_textdomain'));

register_deactivation_hook(__FILE__, array('WP_Force_SSL', 'deactivate_plugin'));
register_uninstall_hook(__FILE__, array('WP_Force_SSL', 'uninstall_plugin'));
