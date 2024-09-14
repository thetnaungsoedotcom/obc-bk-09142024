<?php

/**
 * WP Force SSL
 * https://wpforcessl.com/
 * (c) WebFactory Ltd, 2019 - 2022
 */

class WPSSL_status_tests
{
    var $tests_cache_hours = 12;
    var $ssl_status_cache_hours = 24;
    var $ssl_expiry_days_limit = 30;

    function get_tests()
    {
        global $wp_force_ssl;
        $tests = array();

        $plugin_name = $wp_force_ssl->get_rebranding('name');
        if ($plugin_name == false) {
            $plugin_name = 'WP Force SSL';
        }

        $tests['localhost'] = array(
            'callback' => 'localhost',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'The site is publicly available (not on a localhost).',
                ),
                'fail' => array(
                    'title' => 'The site is NOT publicly available. It\'s on a localhost.',
                    'description' => 'A lot of ' . $plugin_name . ' functions are not available for localhost servers because we can\'t test your site or certificate from our servers since the site is only available to you, and not to the rest of the internet.'
                ),
            )
        );

        $tests['checkssl'] = array(
            'callback' => 'checkssl',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'Your SSL certificate is valid!',
                ),
                'fail' => array(
                    'title' => 'Your SSL certificate is NOT valid.',
                    'description' => 'While testing your SSL certificate our monitoring service returned the following error: %1$s'
                ),
            )
        );

        $tests['pluginver'] = array(
            'callback' => 'pluginver',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'You\'re using the latest version of ' . $plugin_name . '.'
                ),
                'fail' => array(
                    'title' => 'You\'re NOT using the latest version of ' . $plugin_name . '.',
                    'description' => 'Please update to the latest version to enjoy the benefits of all the latest features.'
                ),
            )
        );

        $tests['conflictingplugins'] = array(
            'callback' => 'conflictingplugins',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'You\'re not using any plugins that conflict with ' . $plugin_name . '.'
                ),
                'fail' => array(
                    'title' => 'You\'re using %1$s plugin(s) that conflict with ' . $plugin_name . '.',
                    'description' => 'Please disable the following plugin(s) to ensure there are no conflicts: %2$s.'
                ),
            )
        );

        $tests['wpurl'] = array(
            'callback' => 'wpurl',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'WordPress address URL is properly configured with HTTPS.'
                ),
                'fail' => array(
                    'title' => 'WordPress address URL is NOT properly configured.',
                    'description' => 'WordPress address URL is configured with HTTP instead of HTTPS. Please change the URL in <a href="#">Settings - General</a>.'
                ),
            )
        );

        $tests['url'] = array(
            'callback' => 'url',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'Site address URL is properly configured with HTTPS.'
                ),
                'fail' => array(
                    'title' => 'Site address URL is NOT properly configured.',
                    'description' => 'Site address URL is configured with HTTP instead of HTTPS. Please change the URL in <a href="#">Settings - General</a>.'
                ),
            )
        );

        $tests['sslexpiry'] = array(
            'callback' => 'sslexpiry',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'Your SSL certificate will expire in %1$s days. No need to renew it yet.'
                ),
                'warning' => array(
                    'title' => 'Your SSL certificate will expire in %1$s days. Renew it as soon as possible.',
                ),
                'fail' => array(
                    'title' => '%1$s',
                ),
            )
        );

        $tests['sslmonitoring'] = array(
            'callback' => 'sslmonitoring',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'Realtime SSL monitoring is enabled.',
                    'description' => 'You\'ll be immediately emailed when the status of your SSL changes.'
                ),
                'fail' => array(
                    'title' => 'Realtime SSL monitoring is DISABLED.',
                    'description' => 'Enable monitoring to get instant notifications about your SSL status.'
                ),
            )
        );

        $tests['httpsredirectwp'] = array(
            'callback' => 'httpsredirectwp',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'WP URLs\' are properly redirected from HTTP to HTTPS',
                ),
                'fail' => array(
                    'title' => 'WP URLs\' are NOT properly redirected from HTTP to HTTPS',
                    'description' => 'While testing the redirect we got the following error: %1$s'
                ),
            )
        );

        $tests['httpsredirectfile'] = array(
            'callback' => 'httpsredirectfile',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'Non-WP URLs\' are properly redirected from HTTP to HTTPS',
                ),
                'fail' => array(
                    'title' => 'Non-WP URLs\' are NOT properly redirected from HTTP to HTTPS',
                    'description' => 'While testing the redirect we got the following error: %1$s'
                ),
            )
        );

        $tests['hsts'] = array(
            'callback' => 'hsts',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'HTTP Strict Transport Security (HSTS) is enabled',
                ),
                'fail' => array(
                    'title' => 'HTTP Strict Transport Security (HSTS) is NOT enabled',
                ),
            )
        );

        /*$tests['securityheaders'] = array(
      'callback' => 'securityheaders',
      'description' => '',
      'output' => array(
        'pass' => array(
          'title' => 'Security headers are enabled',
        ),
        'fail' => array(
          'title' => 'Security Headers are NOT enabled',
        ),
      )
    );*/

        $tests['contentscanner'] = array(
            'callback' => 'contentscanner',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'Content scanner was recently run',
                    'description' => 'Check detailed <a href="#tab_scanner" class="change-tab" data-tab="2">content scanner results</a>.',
                ),
                'fail' => array(
                    'title' => 'Content scanner wasn\'t run recently',
                    'description' => 'Make sure to run content scanner frequently to check for mixed content errors on the entire site.',
                ),
            )
        );

        $tests['htaccess'] = array(
            'callback' => 'htaccess',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'Your server uses .htaccess and it\'s writable',
                ),
                'warning' => array(
                    'title' => 'Your server uses .htaccess but it\'s NOT writable',
                ),
                'fail' => array(
                    'title' => 'Your server doesn\'t use htaccess',
                ),
            )
        );

        $tests['404home'] = array(
            'callback' => '404home',
            'description' => '',
            'output' => array(
                'pass' => array(
                    'title' => 'Your 404 errors are not redirected to the home page',
                ),
                'warning' => array(
                    'title' => 'Could not test if your 404 errors are redirected to the home page',
                    'description' => 'While performing the test the following error occured: %1$s',
                ),
                'fail' => array(
                    'title' => 'Your 404 errors are redirected to the home page',
                    'description' => 'Having 404s redirected to the home page can cause redirect loops and SEO issues. %1$s',
                ),
            )
        );

        $tests['_rand'] = array(
            'callback' => 'rand',
            'description' => 'This test returns a new random value and random result every time.',
            'output' => array(
                'pass' => array(
                    'title' => 'Test passed.',
                    'description' => 'Returned value is %1$s.',
                ),
                'warning' => array(
                    'title' => 'Test sort of passed.',
                    'description' => 'Returned value is %1$s.',
                ),
                'fail' => array(
                    'title' => 'Test did NOT pass.',
                    'description' => 'Returned value is %1$s.',
                ),
            )
        );

        $tests = apply_filters('wpssl_tests', $tests);
        return $tests;
    } // get_tests


    private function process_tests()
    {
        $results = array();

        WP_Force_SSL_Utility::clear_3rd_party_cache();

        $tests = $this->get_tests();

        foreach ($tests as $test_name => $test_details) {
            if ($test_name[0] == '_') {
                continue;
            }

            $result = call_user_func(array($this, 'test_' . $test_details['callback']));
            if (is_bool($result)) {
                if ($result === true) {
                    $result = array('status' => 'pass', 'data' => array());
                } else {
                    $result = array('status' => 'fail', 'data' => array());
                }
            }
            if (!isset($result['data'])) {
                $result['data'] = array();
            }
            if (isset($result['data']) && !is_array($result['data'])) {
                $result['data'] = array($result['data']);
            }

            $result['status'] = strtolower($result['status']);
            if ($result['status'] != 'pass' && $result['status'] != 'warning' && $result['status'] != 'fail') {
                user_error('Unknown test status result (' . esc_attr($result['status']) . ') for ' . esc_attr($test_name), E_USER_ERROR);
                die();
            }

            $tmp = $test_details['output'][$result['status']];
            $tmp = array_merge(array('title' => '', 'description' => ''), $tmp);

            $results[] = array(
                'test' => $test_name,
                'status' => $result['status'],
                'title' => $this->sprintfn($tmp['title'], $result['data']),
                'description' => $this->sprintfn($tmp['description'], $result['data']),
            );
        } // foreach $tests

        usort($results, function ($a, $b) {
            $values = array('pass' => 1, 'warning' => 2, 'fail' => 3);

            if ($values[$a['status']] == $values[$b['status']]) {
                return 0;
            }
            return ($values[$a['status']] < $values[$b['status']]) ? 1 : -1;
            //var_dump($a, $b);
        });
        set_transient('wpssl_tests_results', $results, $this->tests_cache_hours * HOUR_IN_SECONDS);
        return $results;
    } // process_tests


    function count_statuses()
    {
        $out = array('pass' => 0, 'warning' => 0, 'fail' => 0);
        $results = $this->get_tests_results();

        foreach ($results as $result) {
            $out[$result['status']]++;
        } // foreach $results

        return $out;
    } // count_statuses


    /**
     * Version of sprintf with named arguments
     *
     * with sprintf: sprintf('second: %2$s ; first: %1$s', '1st', '2nd');
     *
     * with sprintfn: sprintfn('second: %second$s ; first: %first$s', array(
     *  'first' => '1st',
     *  'second'=> '2nd'
     * ));
     *
     * @param string $format sprintf format string, with any number of named arguments
     * @param array $args array of [ 'arg_name' => 'arg value', ... ] replacements to be made
     * @return string|false result of sprintf call, or bool false on error
     */
    function sprintfn($format, array $args = array())
    {
        // map of argument names to their corresponding sprintf numeric argument value
        $arg_nums = array_slice(array_flip(array_keys(array(0 => 0) + $args)), 1);

        // find the next named argument. each search starts at the end of the previous replacement.
        for ($pos = 0; preg_match('/(?<=%)([a-zA-Z_]\w*)(?=\$)/', $format, $match, PREG_OFFSET_CAPTURE, $pos);) {
            $arg_pos = $match[0][1];
            $arg_len = strlen($match[0][0]);
            $arg_key = $match[1][0];

            // no value named argument
            if (!array_key_exists($arg_key, $arg_nums)) {
                user_error("sprintfn(): Missing argument '" . esc_attr($arg_key) . "'", E_USER_WARNING);
                return false;
            }

            // replace the named argument with the corresponding numeric one
            $format = substr_replace($format, $replace = $arg_nums[$arg_key], $arg_pos, $arg_len);
            $pos = $arg_pos + strlen($replace); // skip to end of replacement for next iteration
        }


        if (!is_array($args) || count($args) == 0) {
            return $format;
        }

        return vsprintf($format, array_values($args));
    } // sprintfn


    function get_tests_results($skip_cache = false)
    {
        if ($skip_cache || !($results = get_transient('wpssl_tests_results'))) {
            $results = $this->process_tests();
        }

        return $results;
    } // get_tests_results

    function is_localhost()
    {
        if (($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1') && stripos($_SERVER['SERVER_SOFTWARE'], 'Flywheel') === false) {
            return true;
        }

        $ssl_status = $this->get_ssl_status(false);
        if ($ssl_status['error']) {
            if (stripos($ssl_status['data'], 'unable to retrieve') !== false || stripos($ssl_status['data'], 'unable to resolve') !== false) {
                return true;
            }
        }

        return false;
    } // is_localhost

    function get_ssl_status($skip_cache = false)
    {
        if ($skip_cache || !($status = get_transient('wpssl_ssl_status'))) {
            $get = wp_remote_get('https://dashboard.wpforcessl.com/api/v2/test-ssl.php?domain=' . get_bloginfo('url'), array(
                'timeout'     => 30
            ));

            if ($get === false || is_wp_error($get)) {
                $status = array('error' => true, 'data' => 'Unable to retrieve SSL status.');
                set_transient('wpssl_ssl_status', $status, $this->ssl_status_cache_hours * HOUR_IN_SECONDS);
                return $status;
            }
            $get = wp_remote_retrieve_body($get);
            $get = json_decode($get, true);

            if (!isset($get['success'])) {
                $status = array('error' => true, 'data' => 'Unable to retrieve SSL status.');
            } elseif ($get['success'] == false) {
                $status = array('error' => true, 'data' => $get['data']['data']);
            } else {
                $status = array('error' => $get['data']['error'], 'data' => $get['data']['data']);
            }
            set_transient('wpssl_ssl_status', $status, $this->ssl_status_cache_hours * HOUR_IN_SECONDS);
        }

        return $status;
    } // get_ssl_status


    function test_rand()
    {
        $rand = rand(0, 100);

        if ($rand > 66) {
            return array('status' => 'pass', 'data' => $rand);
        } elseif ($rand > 33) {
            return array('status' => 'warning', 'data' => $rand);
        } else {
            return array('status' => 'fail', 'data' => $rand);
        }
    } // test_rand


    function test_wpurl()
    {
        $tmp = get_bloginfo('wpurl');
        if (stripos($tmp, 'https://') === 0) {
            return true;
        } else {
            return false;
        }
    } // test_wpurl


    function test_url()
    {
        $tmp = get_bloginfo('url');
        if (stripos($tmp, 'https://') === 0) {
            return true;
        } else {
            return false;
        }
    } // test_url


    function test_checkssl()
    {
        $ssl_status = $this->get_ssl_status();
        if ($ssl_status['error']) {
            return array('status' => 'fail', 'data' => $ssl_status['data']);
        } else {
            return true;
        }
    } // test_checkssl


    function test_localhost()
    {
        return !$this->is_localhost();
    } // test_localhost


    function test_pluginver()
    {
        global $wp_force_ssl;
        $updates = get_site_transient('update_plugins');

        if (isset($updates->response['wp-force-ssl/wp-force-ssl.php']) && version_compare($wp_force_ssl->version, $updates->response['wp-force-ssl/wp-force-ssl.php']->new_version, '<')) {
            return false;
        } else {
            return true;
        }
    } // test_pluginver

    function test_conflictingplugins()
    {
        $plugins = array();

        if (defined('WPLE_BASE')) {
            $plugins[] = 'WP Encryption';
        }
        if (defined('WPSSL_VER')) {
            $plugins[] = 'WP Free SSL';
        }
        if (defined('SSL_ZEN_PLUGIN_VERSION')) {
            $plugins[] = 'SSL Zen';
        }
        if (defined('WPSSL_VER')) {
            $plugins[] = 'WP Free SSL';
        }
        if (defined('SSLFIX_PLUGIN_VERSION')) {
            $plugins[] = 'SSL Insecure Content Fixer';
        }
        if (class_exists('OCSSL', false)) {
            $plugins[] = 'One Click SSL';
        }
        if (class_exists('JSM_Force_SSL', false)) {
            $plugins[] = 'JSM\'s Force HTTP to HTTPS (SSL)';
        }
        if (function_exists('httpsrdrctn_plugin_init')) {
            $plugins[] = 'Easy HTTPS (SSL) Redirection';
        }
        if (defined('WPSSL_VER')) {
            $plugins[] = 'WP Free SSL';
        }
        if (class_exists('REALLY_SIMPLE_SSL')) {
            $plugins[] = 'Really Simple SSL';
        }
        if (defined('ESSL_REQUIRED_PHP_VERSION')) {
            $plugins[] = 'EasySSL';
        }
        if (class_exists('ICWP_Cloudflare_Flexible_SSL')) {
            $plugins[] = 'Flexible SSL for CloudFlare';
        }

        if ($plugins) {
            return array('status' => 'fail', 'data' => array(sizeof($plugins), implode(', ', $plugins)));
        } else {
            return true;
        }
    } // test_conflictingplugins


    function test_sslexpiry()
    {
        $ssl_status = $this->get_ssl_status();

        if ($ssl_status['error']) {
            return array('status' => 'fail', 'data' => 'We were not able to test your SSL certificate\'s expiry date.');
        } else {
            $days_valid = round((strtotime($ssl_status['data']['valid_to']) - time()) / DAY_IN_SECONDS);
            if ($days_valid <= 1) {
                return array('status' => 'fail', 'data' => 'Your SSL certificate has expired! Please renew it immediately.');
            } elseif ($days_valid <= $this->ssl_expiry_days_limit) {
                return array('status' => 'warning', 'data' => $days_valid);
            } else {
                return array('status' => 'pass', 'data' => $days_valid);
            }
        }
    } // test_sslexpiry


    function test_sslmonitoring()
    {
        global $wp_force_ssl;
        $options = $wp_force_ssl->get_options();
        if (strlen($options['cert_expiration_email']) > 0) {
            return true;
        } else {
            return false;
        }
    } // test_sslmonitoring


    function test_httpsredirectwp()
    {
        $query = new WP_Query(array('orderby' => 'rand', 'post_status' => 'publish', 'posts_per_page' => '1'));
        if (!$query->posts) {
            $query = new WP_Query(array('orderby' => 'rand', 'post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => '1'));
        }
        $query->the_post();
        $url = get_the_permalink();
        wp_reset_postdata();

        if (!$url) {
            $url = get_bloginfo('url');
        }
        $url = str_replace('https://', 'http://', $url);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36');

        curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            return array('status' => 'fail', 'data' => $error_msg);
        }
        $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        if (substr($target, 0, 8) == 'https://') {
            return true;
        } else {
            return array('status' => 'fail', 'data' => 'URL was not redirected to an HTTPS URL. ' . $url . '=>' . $target);
        }
    } // test_httpsredirectwp


    function test_httpsredirectfile()
    {
        global $wp_force_ssl;

        $url = $wp_force_ssl->plugin_url . 'css/wp-force-ssl.css';
        $url = str_replace('https://', 'http://', $url);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36');

        curl_exec($ch);
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            return array('status' => 'fail', 'data' => $url . ': ' . $error_msg);
        }
        $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        if (substr($target, 0, 8) == 'https://') {
            return true;
        } else {
            return array('status' => 'fail', 'data' => 'URL was not redirected to an HTTPS URL.');
        }
    } // test_httpsredirectfile


    function test_hsts()
    {
        global $wp_force_ssl;
        $options = $wp_force_ssl->get_options();
        if ($options['hsts']) {
            return true;
        } else {
            return false;
        }
    } // test_hsts


    function test_securityheaders()
    {
        global $wp_force_ssl;
        $options = $wp_force_ssl->get_options();

        return true;
    } // test_securityheaders


    function test_contentscanner()
    {
        // check if last run is in last 30ish days
        $scanner = get_option('wp-force-ssl-scanner');
        if ($scanner === false || !array_key_exists('start', $scanner) || $scanner['start'] < (time() - 2592000)) {
            return false;
        }
        return true;
    } // test_contentscanner


    function test_htaccess()
    {
        $server = strtolower(filter_var($_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING));

        if (strpos($server, 'apache') === false && strpos($server, 'litespeed') === false) {
            return false;
        }

        if (!is_writable(get_home_path() . '.htaccess')) {
            return array('status' => 'warning');
        }

        return true;
    } // test_htaccess


    function test_404home()
    {
        $url = trim(get_bloginfo('url'), '/');
        $url .= '/random-404-url-' . rand(1, 100) . '/';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36');

        curl_exec($ch);

        $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $response_code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

        if ($response_code == 404 && $target == $url) {
            return true;
        } elseif (trim($target, '/') == trim(get_bloginfo('url'), '/')) {
            return false;
        }

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            return array('status' => 'warning', 'data' => $error_msg);
        } else {
            return array('status' => 'warning', 'data' => 'Unknown status.');
        }
    } // test_404home
} // class WFSSL_status_tests

global $wp_force_ssl_tests;
$wp_force_ssl_tests = new WPSSL_status_tests();
