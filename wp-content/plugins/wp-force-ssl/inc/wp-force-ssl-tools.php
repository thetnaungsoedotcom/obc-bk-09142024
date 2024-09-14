<?php

/**
 * WP Force SSL
 * https://wpforcessl.com/
 * (c) WebFactory Ltd, 2019 - 2022
 */

class WFSSL_tools
{
    public function add_security_headers()
    {
        global $wp_force_ssl;
        $options = $wp_force_ssl->get_options();
        
        if($options['hsts']){
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }

        if($options['xss_protection']) {
            header('X-XSS-Protection: 1; mode=block');
        }

        if($options['x_content_options']) {
            header('X-Content-Type-Options: nosniff');
        }

        if($options['referrer_policy']) {
            header('Referrer-Policy: no-referrer-when-downgrade');
        }

        if($options['expect_ct']) {
            header('Expect-CT: max-age=5184000, enforce');
        }

        if($options['x_frame_options']) {
            header('X-Frame-Options: sameorigin');
        }

        if($options['permissions_policy']) {
            header($this->permissions_policy_header());
        }

    } // add_security_headers

    function permissions_policy_header() {
        global $wp_force_ssl;
        $options = $wp_force_ssl->get_options();
        
        $permissions_policy_rules = json_decode(wp_unslash($options['permissions_policy_rules']));
	    
        $rules = '';
	    foreach ( $permissions_policy_rules as $policy => $value ) {
		    switch ($value) {
			    case '*':
				    //skip when allow
				    break;
			    case 'none':
				    $rules .= $policy ."=()" .", ";
				    break;
			    case 'self':
				    $rules .= $policy ."=(self)" .", ";
				    break;
		    }
	    }
	    // Remove last space and , from string
	    $rules = substr_replace($rules ,"",-2);
        return 'Permissions-Policy: ' . $rules;
    }

    public function wp_redirect_to_ssl()
    {
        if (!array_key_exists('HTTP_HOST', $_SERVER)) return;

        if (!is_ssl() && !(defined("WFSSL_NO_WP_REDIRECT") && WFSSL_NO_WP_REDIRECT)) {
            $redirect_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $redirect_url = apply_filters("WFSSL_NO_WP_REDIRECT_URL", $redirect_url);
            wp_safe_redirect($redirect_url, 301);
            exit;
        }
    }

    public function enable_secure_cookies()
    {
        $wp_config_path = WP_Force_SSL_Utility::wp_config_path();
        if ($wp_config_path === false) {
            return false;
        }

        $wp_config = file_get_contents($wp_config_path);

        if ((strpos($wp_config, "//START WP Force SSL secure cookie set") !== false) || (strpos($wp_config, "cookie_httponly") !== false)) {
            return false;
        }

        if ((strlen($wp_config) != 0) && is_writable($wp_config_path)) {
            $cookie_init  = "\n" . "//START WP Force SSL secure cookie set" . "\n";
            $cookie_init .= "@ini_set('session.cookie_httponly', true);" . "\n";
            $cookie_init .= "@ini_set('session.cookie_secure', true);" . "\n";
            $cookie_init .= "@ini_set('session.use_only_cookies', true);" . "\n";
            $cookie_init .= "//END WP Force SSL secure cookie set" . "\n";

            $insert_after = "<?php";
            $pos = strpos($wp_config, $insert_after);
            if ($pos !== false) {
                $wp_config = substr_replace($wp_config, $cookie_init, $pos + 1 + strlen($insert_after), 0);
            }

            file_put_contents($wp_config_path, $wp_config);
        }

        return true;
    }

    public function disable_secure_cookies()
    {
        $wp_config_path = WP_Force_SSL_Utility::wp_config_path();
        if ($wp_config_path === false) {
            return false;
        }

        $wp_config = file_get_contents($wp_config_path);

        if ((strpos($wp_config, "//START WP Force SSL secure cookie set") === false)) {
            return false;
        }

        $wp_config_before = explode('//START WP Force SSL secure cookie set', $wp_config);
        $wp_config_after = explode('//END WP Force SSL secure cookie set', $wp_config);

        if (strlen($wp_config_before[0]) == false || $wp_config_before[1]  == false) {
            return false;
        }

        $wp_config = $wp_config_before[0] . $wp_config_after[1];
        file_put_contents($wp_config_path, $wp_config);

        return true;
    }


    public function enable_htaccess_redirect()
    {
        if (!current_user_can('administrator')) return;

        $htaccess_file = WP_Force_SSL_Utility::htaccess_path();

        if ($htaccess_file === false) {
            return false;
        }

        $htaccess = file_get_contents($htaccess_file);

        if ((strpos($htaccess, "# START WFSSL REDIRECT") !== false)) {
            return false;
        }
        
        $htaccess = file_get_contents(WP_Force_SSL_Utility::htaccess_path());
        $is_siteground = $this->is_siteground();

        $rules = "# START WFSSL REDIRECT" . "\n";
        if(!$is_siteground){
            $rules .= "<IfModule mod_rewrite.c>" . "\n";
        }
        $rules .= "RewriteEngine On" . "\n";
        $rules .= "RewriteCond %{HTTPS} off [NC]" . "\n";
        $rules .= "RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]" . "\n";
        if(!$is_siteground){
            $rules .= "</IfModule>" . "\n";
        }
        $rules .= "# END WFSSL REDIRECT" . "\n";

        $wptag = "# BEGIN WordPress";
        if (strpos($htaccess, $wptag) !== false) {
            $htaccess = str_replace($wptag, $rules . $wptag, $htaccess);
        } else {
            $htaccess = $htaccess . $rules;
        }
        file_put_contents($htaccess_file, $htaccess);

        return true;
    }

    public function is_siteground(){
        ob_start();
        phpinfo();
        $phpinfo = ob_get_clean();
        
        if(strpos($phpinfo, 'siteground')){
            return true;
        }

        return false;
    }


    public function disable_htaccess_redirect()
    {
        if (!current_user_can('administrator')) return;

        $htaccess_file = WP_Force_SSL_Utility::htaccess_path();

        if ($htaccess_file === false) {
            return false;
        }

        $htaccess = file_get_contents($htaccess_file);

        if ((strpos($htaccess, "# START WFSSL REDIRECT") === false)) {
            return false;
        }

        $htaccess_before = explode('# START WFSSL REDIRECT', $htaccess);
        $htaccess_after = explode('# END WFSSL REDIRECT', $htaccess);

        $htaccess = $htaccess_before[0] . $htaccess_after[1];
        file_put_contents($htaccess_file, $htaccess);

        return true;
    }


    public function fix_mixed_content()
    {
        global $wp_force_ssl;
        $options = $wp_force_ssl->get_options();

        if (defined('JSON_REQUEST') && JSON_REQUEST) return;
        if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) return;

        $this->build_url_list();

        if (is_admin()) {
            if ($options['fix_backend_mixed_content_fixer']) {
                add_action("admin_init", array($this, "start_buffer"), 100);
                add_action("shutdown", array($this, "end_buffer"), 999);
            }
        } else if ($options['fix_frontend_mixed_content_fixer']) {
            add_action("init", array($this, "start_buffer"));
            add_action("shutdown", array($this, "end_buffer"), 999);
        }
    }

    public function filter_buffer($buffer)
    {
        $buffer = $this->replace_insecure_links($buffer);
        return $buffer;
    }

    public function start_buffer()
    {
        ob_start(array($this, "filter_buffer"));
    }

    public function end_buffer()
    {
        if (ob_get_length()) ob_end_flush();
    }

    public function build_url_list()
    {
        $home = str_replace("https://", "http://", get_option('home'));
        $home_no_www = str_replace("://www.", "://", $home);
        $home_yes_www = str_replace("://", "://www.", $home_no_www);
        $escaped_home = str_replace("/", "\/", $home);

        $this->http_urls = array(
            $home_yes_www,
            $home_no_www,
            $escaped_home,
            "src='http://",
            'src="http://',
        );
    }

    public function replace_insecure_links($str)
    {
        if (substr($str, 0, 5) == "<?xml") return $str;

        $search_array = apply_filters('rlrsssl_replace_url_args', $this->http_urls);
        $ssl_array = str_replace(array("http://", "http:\/\/"), array("https://", "https:\/\/"), $search_array);

        $str = str_replace($search_array, $ssl_array, $str);

        $pattern = array(
            '/url\([\'"]?\K(http:\/\/)(?=[^)]+)/i',
            '/<link [^>]*?href=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
            '/<meta property="og:image" [^>]*?content=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
            '/<form [^>]*?action=[\'"]\K(http:\/\/)(?=[^\'"]+)/i',
        );

        $str = preg_replace($pattern, 'https://', $str);
        $str = preg_replace_callback('/<img[^\>]*[^\>\S]+srcset=[\'"]\K((?:[^"\'\s,]+\s*(?:\s+\d+[wx])(?:,\s*)?)+)["\']/', array($this, 'replace_src_set'), $str);
        $str = str_replace("<body", '<body data-rsssl=1', $str);

        return apply_filters("rsssl_fixer_output", $str);
    }

    public function replace_src_set($matches)
    {
        return str_replace("http://", "https://", $matches[0]);
    }
} // class WFSSL_tools

global $wp_force_ssl_tools;
$wp_force_ssl_tools = new WFSSL_tools();
