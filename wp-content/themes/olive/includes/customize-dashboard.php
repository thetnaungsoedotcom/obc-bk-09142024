<?php

// customize URL

define('THEME_URL', get_stylesheet_directory_uri());
define('INCLUDE_URL', THEME_URL. '/includes/');
define('ASSET_VERSION', '0.1');

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Actions + Filters
|-------------------------------------------------------------------------------------------------------------------------------
|
*/
// Remove admin bar
add_filter('show_admin_bar', '__return_false');
// Remove links to the extra feeds (e.g. category feeds)
remove_action('wp_head', 'feed_links_extra', 3);
// Remove links to the general feeds (e.g. posts and comments)
remove_action('wp_head', 'feed_links', 2);
// Remove link to the RSD service endpoint, EditURI link
remove_action('wp_head', 'rsd_link');
// Remove link to the Windows Live Writer manifest file
remove_action('wp_head', 'wlwmanifest_link');
// Remove index link
remove_action('wp_head', 'index_rel_link');
// Remove prev link
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
// Remove start link
remove_action('wp_head', 'start_post_rel_link', 10, 0);
// Display relational links for adjacent posts
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);
// Remove XHTML generator showing WP version
remove_action('wp_head', 'wp_generator');
// Remove shortlink from the head
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Allow HTML in descriptions
$html_filters = array('pre_term_description', 'pre_link_description', 'pre_link_notes', 'pre_user_description');
foreach ($html_filters as $filter) {
    remove_filter($filter, 'wp_filter_kses');
}

/* Turn off wpemoji */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

/* remove REST API lines */
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Admin customisation
|-------------------------------------------------------------------------------------------------------------------------------
| give clean admin look for the client
|-------------------------------------------------------------------------------------------------------------------------------
|
*/
add_action('init', 'customise_dashboard');

// function customise_dashboard() {
//     if (is_user_logged_in()) {
//         global $current_user;

//         if ($current_user->user_login != 'b360mm') {
//             add_action('admin_menu', 'remove_menu_items', 999);
//             add_action('admin_menu', 'remove_submenus');
//             add_action('wp_dashboard_setup', 'remove_dashboard_widgets');
//             add_action('admin_menu', 'remove_menu_for_low_level_user');
//         } else {
//             add_action('admin_menu', 'remove_superadmin_menu');
//         }
//         if (!current_user_can('administrator')) {
//             add_action('admin_bar_menu', 'remove_admin_bar', 100);
//         }
//     }
// }

function customise_dashboard() {
    if (is_user_logged_in()) {
        global $current_user;

        if (($current_user->user_login === 'b360mm') || ($current_user->user_login === 'b360mm2')) {
            add_action('admin_menu', 'remove_superadmin_menu');
        } else {
            add_action('admin_menu', 'remove_menu_items', 999);
            add_action('admin_menu', 'remove_submenus');
            add_action('wp_dashboard_setup', 'remove_dashboard_widgets');
            add_action('admin_menu', 'remove_menu_for_low_level_user');
        }

        if (!current_user_can('administrator')) {
            add_action('admin_bar_menu', 'remove_admin_bar', 100);
        }
    }
}

function remove_superadmin_menu() {
    remove_menu_page('ot-settings');
}

function remove_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('my-sites');
    $wp_admin_bar->remove_menu('wpseo-menu');
    $wp_admin_bar->remove_menu('new-content');
}

function remove_menu_for_low_level_user() {
    remove_menu_page('index.php');
    $ban_post_type_menu = array("acf-field-group");
    foreach ($ban_post_type_menu as $m) {
        remove_menu_page('edit.php?post_type=' . $m);
    }
}

//flush_rewrite_rules( true );
function remove_menu_items() {
    global $menu;
    $restricted = array(__('Links'), __('Plugins'),__('Tools'), __('SEO'), __('Option Tree'),__('Users'),__('Settings'),__('Comments'),__('Options'),__('Currencyr'));
    end($menu);
    while (prev($menu)) {
        $value = explode(' ', $menu[key($menu)][0]);
        if (in_array($value[0] != NULL ? $value[0] : "", $restricted)) {
            unset($menu[key($menu)]);
        }
    }
    //remove custom plugin menu
    $restricted_plug_menu = array(
        'wpseo_dashboard', 'ot-settings', 'wpcf7','toolset-dashboard','Wordfence','wpfastestcacheoptions'
    );
    foreach ($restricted_plug_menu as $m) {
        remove_menu_page($m);
    }
}

function remove_submenus() {
    global $submenu;

    unset($submenu['index.php'][10]); // Removes 'Updates'.
    unset($submenu['themes.php'][5]); // Removes 'Themes'.
    unset($submenu['themes.php'][7]); // Removes 'Widgets'.
    unset($submenu['edit.php'][16]); // Removes 'Tags'.
    remove_action('admin_menu', '_add_themes_utility_last', 101); //remove editor under appearance
}

function remove_dashboard_widgets() {
    global$wp_meta_boxes;
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    //unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
}

add_filter('admin_footer_text', 'my_admin_footer_text');

function my_admin_footer_text($default_text) {
    return '<span id="footer-thankyou">Website managed and developed by <a href="https://www.b360mm.com" target="_blank" rel="noopener" title="B360 Marketing Consulting Firm in Yangon, Myanmar">B360 Digital Marketing</a><span> | Powered by <a href="http://www.wordpress.org">WordPress</a>';
}

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Customise the login screen
|-------------------------------------------------------------------------------------------------------------------------------
|
*/
function change_wp_login_url() {
	return WP_HOME;
}
add_filter('login_headerurl', 'change_wp_login_url');

function change_wp_login_title() {
	return get_option('blogname');
}
add_filter('login_headertitle', 'change_wp_login_title');

function custom_login_js_css() {

?>
	<link rel='stylesheet' id='custom-login-css-css'  href='<?php echo TEMPLATE_URL ?>/includes/assets/css/custom-login.css' />
	<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.0/jquery.min.js'></script>
	<script>
	    $(document).ready(function(){
			var link = '<a class="ce" href="https://www.b360mm.com" target="_blank" rel="noopener" title="B360 Marketing Consulting Firm in Yangon, Myanmar">B360 Digital Marketing</a>';
			$("#login #nav").append(link);
		});
	</script>
<?php
}
add_action('login_head', 'custom_login_js_css');

function custom_admin_js_css() {
    // wp_enqueue_script('custom-admin-js', INCLUDE_URL . 'assets/js/custom-admin.js', 'jquery', '', true);
    //wp_enqueue_style('custom-admin-css', INCLUDE_URL . 'assets/css/custom-admin.css');
}

add_action('admin_head', 'custom_admin_js_css');

// footer_scripts
function footer_scripts() {
	//fallback jquery
}
add_action('wp_footer', 'footer_scripts', 10);
