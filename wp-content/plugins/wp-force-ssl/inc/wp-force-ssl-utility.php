<?php
/*
 * WP Force SSL PRO
 * Utility & Helper functions
 * (c) WebFactory Ltd, 2015 - 2022
 */

// include only file
if (!defined('ABSPATH')) {
    die('Do not open this file directly.');
}

class WP_Force_SSL_Utility
{
    /**
     * Helper function for generating UTM tagged links
     *
     * @param string  $placement  Optional. UTM content param.
     * @param string  $page       Optional. Page to link to.
     * @param array   $params     Optional. Extra URL params.
     * @param string  $anchor     Optional. URL anchor part.
     *
     * @return string
     */
    private static $delete_count = 0;

    static function generate_web_link($placement = '', $page = '/', $params = array(), $anchor = '')
    {
        global $wp_force_ssl;
        $base_url = 'https://wpforcessl.com';

        if ('/' != $page) {
            $page = '/' . trim($page, '/') . '/';
        }
        if ($page == '//') {
            $page = '/';
        }

        $parts = array_merge(array('utm_source' => 'wp-force-ssl-pro', 'utm_medium' => 'plugin', 'utm_content' => $placement, 'utm_campaign' => 'wp-force-ssl-pro-v' . $wp_force_ssl->version), $params);

        if (!empty($anchor)) {
            $anchor = '#' . trim($anchor, '#');
        }

        $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

        return $out;
    } // generate_web_link


    /**
     * Helper function for generating dashboard UTM tagged links
     *
     * @param string  $placement  Optional. UTM content param.
     * @param string  $page       Optional. Page to link to.
     * @param array   $params     Optional. Extra URL params.
     * @param string  $anchor     Optional. URL anchor part.
     *
     * @return string
     */
    static function generate_dashboard_link($placement = '', $page = '/', $params = array(), $anchor = '')
    {
        global $wp_force_ssl;
        $base_url = 'https://dashboard.wpforcessl.com';

        if ('/' != $page) {
            $page = '/' . trim($page, '/') . '/';
        }
        if ($page == '//') {
            $page = '/';
        }

        $parts = array_merge(array('utm_source' => 'wp-force-ssl-pro', 'utm_medium' => 'plugin', 'utm_content' => $placement, 'utm_campaign' => 'wp-force-ssl-pro-v' . $wp_force_ssl->version), $params);

        if (!empty($anchor)) {
            $anchor = '#' . trim($anchor, '#');
        }

        $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

        return $out;
    } // generate_dashboard_link


    /**
     * Whitelabel filter
     *
     * @return bool display contents if whitelabel is not enabled or not hidden
     */
    static function whitelabel_filter()
    {
        global $wp_force_ssl, $wp_force_ssl_licensing;
        $options = $wp_force_ssl->get_options();

        if (!$wp_force_ssl_licensing->is_active('white_label')) {
            return true;
        }

        if (!$options['whitelabel']) {
            return true;
        }

        return false;
    } // whitelabel_filter


    /**
     * Create select options for select
     *
     * @param array $options options
     * @param string $selected selected value
     * @param bool $output echo, if false return html as string
     * @return string html with options
     */
    static function create_select_options($options, $selected = null, $output = true)
    {
        $out = "\n";

        if (is_array($options) && !empty($options) && !isset($options[0]['val'])) {
            $tmp = array();
            foreach ($options as $val => $label) {
                $tmp[] = array('val' => $val, 'label' => $label);
            } // foreach
            $options = $tmp;
        }

        foreach ($options as $tmp) {
            if ($selected == $tmp['val']) {
                $out .= "<option selected=\"selected\" value=\"{$tmp['val']}\">{$tmp['label']}&nbsp;</option>\n";
            } else {
                $out .= "<option value=\"{$tmp['val']}\">{$tmp['label']}&nbsp;</option>\n";
            }
        }

        if ($output) {
            self::wp_kses_wf($out);
        } else {
            return $out;
        }
    } //  create_select_options


    /**
     * Creates a fancy, iOS alike toggle switch
     *
     * @param string $name ID used for checkbox.
     * @param array $options Various options: value, saved_value, option_key, class
     * @param boolean $echo Default: true.
     * @return void
     */
    static function create_toogle_switch($name, $options = array(), $echo = true)
    {
        $default_options = array('value' => '1', 'saved_value' => '', 'option_key' => $name, 'class' => '');
        $options = array_merge($default_options, $options);

        $out = '<label for="' . $name . '" class="wfssl-switch tooltip ' . $options['class'] . '">';
        $out .= '<input type="checkbox" id="' . $name . '" ' . self::checked($options['value'], $options['saved_value']) . ' type="checkbox" value="' . $options['value'] . '" name="' . $options['option_key'] . '">';
        $out .= '<span class="wfssl-slider wfssl-round"></span>';
        $out .= '</label>';

        if ($echo) {
            self::wp_kses_wf($out);
        } else {
            return $out;
        }
    } // create_toggle_switch


    /**
     * Helper for creating checkboxes.
     *
     * @param string $value Checkbox value, in HTML.
     * @param array $current Current, saved value of checkbox.
     * @param boolean $echo Default: false.
     *
     * @return void|string
     */
    static function checked($value, $current, $echo = false)
    {
        $out = '';

        if (!is_array($current)) {
            $current = (array) $current;
        }

        if (in_array($value, $current)) {
            $out = ' checked="checked" ';
        }

        if ($echo) {
            self::wp_kses_wf($out);
        } else {
            return $out;
        }
    } // checked



    /**
     * Format file size to human readable string
     *
     * @param int  $bytes  Size in bytes to format.
     *
     * @return string
     */
    static function format_size($bytes)
    {
        if ($bytes > 1073741824) {
            return number_format_i18n($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes > 1048576) {
            return number_format_i18n($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes > 1024) {
            return number_format_i18n($bytes / 1024, 1) . ' KB';
        } else {
            return number_format_i18n($bytes, 0) . ' bytes';
        }
    } // format_size

    static function wp_config_path()
    {
        $i = 0;
        $maxiterations = 10;
        $dir = dirname(__FILE__);
        do {
            $i++;
            if (file_exists($dir . "/wp-config.php") && is_writable($dir . "/wp-config.php")) {
                return $dir . "/wp-config.php";
            }
        } while (($dir = realpath("$dir/..")) && ($i < $maxiterations));

        return false;
    }

    static function htaccess_path()
    {
        $htaccess_file = ABSPATH . ".htaccess";
        if (file_exists($htaccess_file)) {
            return $htaccess_file;
        }

        return false;
    }

    static function wp_kses_wf($html)
    {
        add_filter('safe_style_css', function ($styles) {
            $styles_wf = array(
                'text-align',
                'margin',
                'color',
                'float',
                'border',
                'background',
                'background-color',
                'border-bottom',
                'border-bottom-color',
                'border-bottom-style',
                'border-bottom-width',
                'border-collapse',
                'border-color',
                'border-left',
                'border-left-color',
                'border-left-style',
                'border-left-width',
                'border-right',
                'border-right-color',
                'border-right-style',
                'border-right-width',
                'border-spacing',
                'border-style',
                'border-top',
                'border-top-color',
                'border-top-style',
                'border-top-width',
                'border-width',
                'caption-side',
                'clear',
                'cursor',
                'direction',
                'font',
                'font-family',
                'font-size',
                'font-style',
                'font-variant',
                'font-weight',
                'height',
                'letter-spacing',
                'line-height',
                'margin-bottom',
                'margin-left',
                'margin-right',
                'margin-top',
                'overflow',
                'padding',
                'padding-bottom',
                'padding-left',
                'padding-right',
                'padding-top',
                'text-decoration',
                'text-indent',
                'vertical-align',
                'width',
                'display',
            );

            foreach ($styles_wf as $style_wf) {
                $styles[] = $style_wf;
            }
            return $styles;
        });

        $allowed_tags = wp_kses_allowed_html('post');
        $allowed_tags['input'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'data-*' => true,
            'size' => true,
            'disabled' => true
        );

        $allowed_tags['textarea'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'data-*' => true,
            'cols' => true,
            'rows' => true,
            'disabled' => true
        );

        $allowed_tags['select'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'data-*' => true,
            'multiple' => true,
            'disabled' => true
        );

        $allowed_tags['option'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'selected' => true,
            'data-*' => true
        );
        $allowed_tags['optgroup'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'selected' => true,
            'data-*' => true,
            'label' => true
        );

        $allowed_tags['a'] = array(
            'href' => true,
            'data-*' => true,
            'class' => true,
            'style' => true,
            'id' => true,
            'target' => true,
            'data-*' => true,
            'role' => true,
            'aria-controls' => true,
            'aria-selected' => true,
            'disabled' => true
        );

        $allowed_tags['div'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'data-*' => true,
            'role' => true,
            'aria-labelledby' => true,
            'value' => true,
            'aria-modal' => true,
            'tabindex' => true
        );

        $allowed_tags['li'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'data-*' => true,
            'role' => true,
            'aria-labelledby' => true,
            'value' => true,
            'aria-modal' => true,
            'tabindex' => true
        );

        $allowed_tags['span'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'data-*' => true,
            'aria-hidden' => true
        );

        $allowed_tags['form'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'method' => true,
            'action' => true,
            'data-*' => true
        );

        echo wp_kses($html, $allowed_tags);

        add_filter('safe_style_css', function ($styles) {

            $styles_wf = array(
                'text-align',
                'margin',
                'color',
                'float',
                'border',
                'background',
                'background-color',
                'border-bottom',
                'border-bottom-color',
                'border-bottom-style',
                'border-bottom-width',
                'border-collapse',
                'border-color',
                'border-left',
                'border-left-color',
                'border-left-style',
                'border-left-width',
                'border-right',
                'border-right-color',
                'border-right-style',
                'border-right-width',
                'border-spacing',
                'border-style',
                'border-top',
                'border-top-color',
                'border-top-style',
                'border-top-width',
                'border-width',
                'caption-side',
                'clear',
                'cursor',
                'direction',
                'font',
                'font-family',
                'font-size',
                'font-style',
                'font-variant',
                'font-weight',
                'height',
                'letter-spacing',
                'line-height',
                'margin-bottom',
                'margin-left',
                'margin-right',
                'margin-top',
                'overflow',
                'padding',
                'padding-bottom',
                'padding-left',
                'padding-right',
                'padding-top',
                'text-decoration',
                'text-indent',
                'vertical-align',
                'width'
            );

            foreach ($styles_wf as $style_wf) {
                if (($key = array_search($style_wf, $styles)) !== false) {
                    unset($styles[$key]);
                }
            }
            return $styles;
        });
    }


    static function clear_3rd_party_cache()
    {
        wp_cache_flush();

        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }

        if (function_exists('w3tc_pgcache_flush')) {
            w3tc_pgcache_flush();
        }

        if (function_exists('wpfc_clear_all_cache')) {
            wpfc_clear_all_cache();
        }
        if (function_exists('w3tc_flush_all')) {
            w3tc_flush_all();
        }
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        if (method_exists('LiteSpeed_Cache_API', 'purge_all')) {
            LiteSpeed_Cache_API::purge_all();
        }
        if (class_exists('Endurance_Page_Cache')) {
            $epc = new Endurance_Page_Cache;
            $epc->purge_all();
        }
        if (class_exists('SG_CachePress_Supercacher') && method_exists('SG_CachePress_Supercacher', 'purge_cache')) {
            SG_CachePress_Supercacher::purge_cache(true);
        }
        if (class_exists('SiteGround_Optimizer\Supercacher\Supercacher')) {
            SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
        }
        if (isset($GLOBALS['wp_fastest_cache']) && method_exists($GLOBALS['wp_fastest_cache'], 'deleteCache')) {
            $GLOBALS['wp_fastest_cache']->deleteCache(true);
        }
        if (is_callable(array('Swift_Performance_Cache', 'clear_all_cache'))) {
            Swift_Performance_Cache::clear_all_cache();
        }
        if (is_callable(array('Hummingbird\WP_Hummingbird', 'flush_cache'))) {
            Hummingbird\WP_Hummingbird::flush_cache(true, false);
        }
    }

    /**
     * Recursively deletes a folder
     *
     * @param string $folder  Recursive param.
     *
     * @return bool
     */
    static function delete_folder($folder)
    {
        if (!file_exists($folder)) {
            return true;
        }

        $files = array_diff(scandir($folder), array('.', '..'));

        foreach ($files as $file) {
            if (is_dir($folder . DIRECTORY_SEPARATOR . $file)) {
                self::delete_folder($folder . DIRECTORY_SEPARATOR . $file);
            } else {
                $tmp = @unlink($folder . DIRECTORY_SEPARATOR . $file);
                self::$delete_count += (int) $tmp;
            }
        } // foreach

        $tmp = @rmdir($folder);
        self::$delete_count += (int) $tmp;
        return $tmp;
    } // delete_folder
} // WP_Force_SSL_Utility
