<?php
/**
 * TNS Theme-One functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage TNS_Theme_One
 * @since TNS Theme-One 1.0
 */


if ( ! function_exists( 'tnsthemeone_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since TNS Theme-One 1.0
	 *
	 * @return void
	 */
	function tnsthemeone_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

	}

endif;

add_action( 'after_setup_theme', 'tnsthemeone_support' );

if ( ! function_exists( 'tnsthemeone_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since TNS Theme-One 1.0
	 *
	 * @return void
	 */
	function tnsthemeone_styles() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'tnsthemeone-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Add styles inline.
		wp_add_inline_style( 'tnsthemeone-style', tnsthemeone_get_font_face_styles() );

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'tnsthemeone-style' );

	}

endif;

add_action( 'wp_enqueue_scripts', 'tnsthemeone_styles' );

if ( ! function_exists( 'tnsthemeone_editor_styles' ) ) :

	/**
	 * Enqueue editor styles.
	 *
	 * @since TNS Theme-One 1.0
	 *
	 * @return void
	 */
	function tnsthemeone_editor_styles() {

		// Add styles inline.
		wp_add_inline_style( 'wp-block-library', tnsthemeone_get_font_face_styles() );

	}

endif;

add_action( 'admin_init', 'tnsthemeone_editor_styles' );


if ( ! function_exists( 'tnsthemeone_get_font_face_styles' ) ) :

	/**
	 * Get font face styles.
	 * Called by functions tnsthemeone_styles() and tnsthemeone_editor_styles() above.
	 *
	 * @since TNS Theme-One 1.0
	 *
	 * @return string
	 */
	function tnsthemeone_get_font_face_styles() {

		return "
		@font-face{
			font-family: 'Archivo', sans-serif;
			font-weight: 300 400 500 600 700 800 900;
			font-style: normal;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/Archivo/Archivo-VariableFont_wdth,wght.ttf' ) . "') format('woff2');
		}

		@font-face{
			font-family: 'Archivo', sans-serif;
			font-weight: 300 400 500 600 700 800 900;
			font-style: italic;
			font-stretch: normal;
			font-display: swap;
			src: url('" . get_theme_file_uri( 'assets/fonts/Archivo/Archivo-Italic-VariableFont_wdth,wght.ttf' ) . "') format('woff2');
		}
		";

	}

endif;

if ( ! function_exists( 'tnsthemeone_preload_webfonts' ) ) :

	/**
	 * Preloads the main web font to improve performance.
	 *
	 * Only the main web font (font-style: normal) is preloaded here since that font is always relevant (it is used
	 * on every heading, for example). The other font is only needed if there is any applicable content in italic style,
	 * and therefore preloading it would in most cases regress performance when that font would otherwise not be loaded
	 * at all.
	 *
	 * @since TNS Theme-One 1.0
	 *
	 * @return void
	 */
	function tnsthemeone_preload_webfonts() {
		?>
		<link rel="preload" href="<?php echo esc_url( get_theme_file_uri( 'assets/fonts/Archivo/Archivo-VariableFont_wdth,wght.ttf' ) ); ?>" as="font" type="font/woff2" crossorigin>
		<?php
	}

endif;

add_action( 'wp_head', 'tnsthemeone_preload_webfonts' );

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';


define('TEMPLATE_URL', get_stylesheet_directory_uri());
define('ASSET_URL', TEMPLATE_URL . '/assets/');
require_once('includes/aq_resizer.php');
require_once('includes/theme-options.php');
require_once('includes/customize-dashboard.php');
require_once('includes/custom-post-types.php');
//require_once('includes/custom-blocks.php');
//require_once('includes/wp-bootstrap-navwalker.php');

// Defind Page (EN)
define('OLIVE_ABOUT_PG', 29);
define('OLIVE_ADVANTAGE_PG', 1183);
define('OLIVE_BLOG_PG', 279);
define('OLIVE_CONTACT_PG', 33);
define('OLIVE_CSR_PG', 340);
define('OLIVE_INDUSTRIAL_PG', 1107);
define('OLIVE_INVESTMENT_PG', 559);
define('OLIVE_INVESTMENT_INCENTIVE_PG', 1062);
define('OLIVE_MM_INVESTMENT_CLIMATE_PG', 1061);
define('OLIVE_NEWS_PG', 82);
define('OLIVE_OUR_CLIENT_PG', 423);
define('OLIVE_GALLERY_PG', 520);
define('OLIVE_TEAM_PG', 80);
define('OLIVE_OVERVIEW_MYANMAR_PG', 1060);
define('OLIVE_SERVICE_PG', 71);
define('OLIVE_STRATEGRY_PG', 78);
define('OLIVE_TESTIMONIAL_PG', 598);

// Defind Page (CN)
define('OLIVE_ABOUT_CN_PG', 764);
define('OLIVE_ADVANTAGE_CN_PG', 1433);
define('OLIVE_BLOG_CN_PG', 768);
define('OLIVE_CONTACT_CN_PG', 862);
define('OLIVE_CSR_CN_PG', 793);
define('OLIVE_INDUSTRIAL_CN_PG', 1436);
define('OLIVE_INVESTMENT_CN_PG', 815);
define('OLIVE_INVESTMENT_INCENTIVE_CN_PG', 1439);
define('OLIVE_MM_INVESTMENT_CLIMATE_CN_PG', 1449);
define('OLIVE_NEWS_CN_PG', 772);
define('OLIVE_OUR_CLIENT_CN_PG', 859);
define('OLIVE_GALLERY_CN_PG', 1608);
define('OLIVE_TEAM_CN_PG', 915);
define('OLIVE_OVERVIEW_MYANMAR_CN_PG', 1442);
define('OLIVE_SERVICE_CN_PG', 905);
define('OLIVE_STRATEGRY_CN_PG', 911);
define('OLIVE_TESTIMONIAL_CN_PG', 913);

// Define post type
define('OLIVE_CSR_PT','csr');
define('OLIVE_NEWS_PT', 'news');
define('OLIVE_BLOG_PT', 'post');
// define('OLIVE_INVESTMENT_PT', 'investment');
define('OLIVE_GALLERY_PT', 'gallery');
define('OLIVE_SERVICE_PT', 'services');
define('OLIVE_TESTIMONIAL_PT', 'testimonials');
define('OLIVE_INDUSTRIAL_PT', 'industrial-park');
define('OLIVE_ADVANTAGE_PT', 'advantage');

// Define post type
define('OLIVE_LOCATION_TAXO', 'location');

function the_template_url() {
    echo TEMPLATE_URL;
}

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Enqueue Scripts
|-------------------------------------------------------------------------------------------------------------------------------
*/
function init_scripts() {
    wp_deregister_script('wp-embed');
    wp_deregister_script('jquery');
    wp_deregister_script('comment-reply');
}

function add_scripts() {
    $js_path = ASSET_URL . 'js';
    $css_path = ASSET_URL . 'css';
    if ($_SERVER['HTTP_HOST'] == 'olive.b360' || strpos($_SERVER['HTTP_HOST'], '192') !== false) {
        $js_libs = array(
            array(
                'name' => 'jquery',
                'src' => 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js',
                'dep' => null,
                'ver' => null,
                'is_footer' => false
            ),
            array(
                'name' => 'bootstrap',
                'src' => $js_path . '/bootstrap.min.js',
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
            array(
                'name' => 'fancybox',
                'src' => "https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js",
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
            // array(
            //     'name' => 'hc-offcanvas-nav',
            //     'src' => $js_path . '/hc-offcanvas-nav.js',
            //     'dep' => 'jquery',
            //     'ver' => null,
            //     'is_footer' => true
            // ),
            array(
                'name' => 'masonrygrid',
                'src' => $js_path . '/jquery.masonryGrid.js',
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
            array(
                'name' => 'stellarnav',
                'src' => $js_path . '/stellarnav.min.js',
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
            array(
                'name' => 'swiper',
                'src' =>'https://unpkg.com/swiper@7/swiper-bundle.min.js',
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
            array(
                'name' => 'script',
                'src' => $js_path . '/script.js',
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
        );

        $css_libs = array(
            // array(
            //     'name' => 'font',
            //     'src' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,100;1,200;1,300;1,400;1,500;1,600&display=swap',
            //     'dep' => null,
            //     'ver' => null,
            //     'media' => 'screen'
            // ),
            array(
                'name' => 'bootstrap',
                'src' => $css_path . '/bootstrap.min.css',
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            array(
                'name' => 'fontawesome',
                'src' => $css_path . '/all.min.css',
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            array(
                'name' => 'swiper',
                'src' => 'https://unpkg.com/swiper@7/swiper-bundle.min.css',
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            array(
                'name' => 'google-font',
                'src' => '//fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;700;800&display=swap',
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            array(
                'name' => 'fancybox',
                'src' => "https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css",
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            // array(
            //     'name' => 'hc-offcanvas-nav-css',
            //     'src' => $css_path . '/hc-offcanvas-nav.css',
            //     'dep' => null,
            //     'ver' => null,
            //     'media' => 'screen'
            // ),
            array(
                'name' => 'stellarnav',
                'src' => $css_path . '/stellarnav.css',
                // 'src' => $css_path . '/stellarnav.min.css',
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            array(
                'name' => 'style',
                'src' => TEMPLATE_URL . '/style.css',
                'dep' => null,
                'ver' => ASSET_VERSION,
                'media' => 'screen'
            ),
        );
    } else {
        $js_libs = array(
            array(
                'name' => 'jquery',
                'src' => 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js',
                'dep' => null,
                'ver' => null,
                'is_footer' => false
            ),
            array(
                'name' => 'bootstrap',
                'src' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js',
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
            array(
                'name' => 'swiper',
                'src' => 'https://unpkg.com/swiper@7/swiper-bundle.min.js',
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
            array(
                'name' => 'stellarnav',
                'src' => $js_path . '/stellarnav.min.js',
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
            // array(
            //     'name' => 'hc-offcanvas-nav',
            //     'src' => $js_path . '/hc-offcanvas-nav.js',
            //     'dep' => 'jquery',
            //     'ver' => null,
            //     'is_footer' => true
            // ),
            array(
                'name' => 'masonrygrid',
                'src' => $js_path . '/jquery.masonryGrid.js',
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
            array(
                'name' => 'fancybox',
                'src' => "https://cdn.jsdelivr.net/npm/@fancyapps/ui@4.0/dist/fancybox.umd.js",
                'dep' => 'jquery',
                'ver' => null,
                'is_footer' => true
            ),
           
            array(
                'name' => 'script',
                'src' => $js_path . '/script.js',
                'dep' => 'jquery',
                'ver' => ASSET_VERSION,
                'is_footer' => true
            ),
        );

        $css_libs = array(
            // array(
            //     'name' => 'font',
            //     'src' => 'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,100;1,200;1,300;1,400;1,500;1,600&display=swap',
            //     'dep' => null,
            //     'ver' => null,
            //     'media' => 'screen'
            // ),
            array(
                'name' => 'bootstrap',
                'src' => 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css',
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            array(
                'name' => 'fontawesome',
                'src' => $css_path . '/all.min.css',
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            array(
                'name' => 'swiper',
                'src' => 'https://unpkg.com/swiper@7/swiper-bundle.min.css',
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            array(
                'name' => 'stellarnav',
                'src' => $css_path . '/stellarnav.min.css',
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
            // array(
            //     'name' => 'hc-offcanvas-nav-css',
            //     'src' => $css_path . '/hc-offcanvas-nav.css',
            //     'dep' => null,
            //     'ver' => null,
            //     'media' => 'screen'
            // ),
            array(
                'name' => 'fancybox',
                'src' => "https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css",
                'dep' => null,
                'ver' => null,
                'media' => 'screen'
            ),
           
            array(
                'name' => 'style',
                'src' => TEMPLATE_URL . '/style.css',
                'dep' => null,
                'ver' => ASSET_VERSION,
                'media' => 'all'
            ),
        );
    }

    foreach ($js_libs as $lib) {
        wp_enqueue_script($lib['name'], $lib['src'], $lib['dep'], $lib['ver'], $lib['is_footer']);
    }

    foreach ($css_libs as $lib) {
        wp_enqueue_style($lib['name'], $lib['src'], $lib['dep'], $lib['ver'], $lib['media']);
    }
}

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Admin Panel Style
|-------------------------------------------------------------------------------------------------------------------------------
| If you don't like style, please use comment.
|
*/
// add_action('admin_head', 'maybe_modify_admin_css');
function maybe_modify_admin_css() {
    global $pagenow;
    if ('post.php' === $pagenow || 'post-new.php' === $pagenow){
    ?>
		<style>
        .edit-post-layout__metaboxes {
            background: #e0e0e0;
        }
        .acf-fields > .acf-field {
            margin-bottom: 10px;
        }
		#wpadminbar {
            background: #212a32;
        }
        #adminmenu .wp-has-current-submenu .wp-submenu .wp-submenu-head, 
        #adminmenu .wp-menu-arrow, 
        #adminmenu .wp-menu-arrow div, 
        #adminmenu li.current a.menu-top, 
        #adminmenu li.wp-has-current-submenu a.wp-has-current-submenu, 
        .folded #adminmenu li.current.menu-top, 
        .folded #adminmenu li.wp-has-current-submenu {
            background: #23282d;
        }
        #adminmenu .wp-submenu a:focus, 
        #adminmenu .wp-submenu a:hover, 
        #adminmenu a:hover, 
        #adminmenu li.menu-top>a:focus {
            background: #23282d;
        }
        .edit-post-sidebar {
            background: #2c3135;
        }
        .edit-post-sidebar > .components-panel {
            background: #2c3135;
            color: white;
        }
        .components-panel__body-toggle.components-button {
            color: #fff;
        }
        .components-panel__body>.components-panel__body-title:hover, 
        .edit-post-last-revision__panel>.components-icon-button:not(:disabled):not([aria-disabled=true]):not(.is-default):hover {
            background: #2c3135;
        }
        .components-panel__body {
            border-top: 1px solid #2c3135;
            border-bottom: 1px solid #2c3135;
        }
        .edit-post-sidebar__panel-tab.is-active {
            box-shadow: inset 0 -3px #2c3135;
            background: #2c3135;
            color: #fff;
        }
        .components-panel__body-toggle.components-button .components-panel__arrow {
            color: #edeff0;
        }
        .acf-fields>.acf-field {
            padding: 15px;
        }
        .edit-post-meta-boxes-area #poststuff .stuffbox>h3, .edit-post-meta-boxes-area #poststuff h2.hndle, .edit-post-meta-boxes-area #poststuff h3.hndle {
            border-bottom: 5px solid #2c3135;
        }
        .block-editor-block-inspector__no-blocks {
            background: #2c3135;
        }
        .components-panel {
            background: #fff;
            border: 1px solid #2c3135;
        }
        .components-button.is-primary {
            background: #212a32;
            border-color: #212a32;
        }
        .components-button.is-primary:focus:enabled, .components-button.is-primary:hover {
            background: #212a32;
            border-color: #212a32;
        }
        .components-button.is-large, .components-button.is-button {
            height: auto;
        }
        .components-button.is-default:hover {
            background: #212a32;
            border-color: #212a32;
            color: #fff;
            text-decoration: none;
        }
        .components-button.is-default, 
        .editor-post-featured-image__toggle {
            border: 1px solid #a2aab2;
            background-color: rgb(128, 128, 128);
            border-color: #909090;
            border-radius: 4px;
            display: inline-block;
            font-weight: 400;
            color: #fff;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            padding: 0.375rem 0.75rem;
            text-transform: uppercase;
            font-size: 13px;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .editor-post-featured-image__toggle:hover {
            background-color: #212a32;
        }
		</style>
        <?php
    }
}

function my_deregister_scripts() {
    global $post_type;

    if (!is_front_page() && !is_home()) {
    }
    if (!is_page('contact-us')) { //only include in contact us page
    }
}

function my_deregister_styles() {
    global $post_type;
    if (!is_page('contact-us')) { //only include in contact us page
    }
}

if (!is_admin()) {
    add_action('init', 'init_scripts', 10);
}
add_action('wp_enqueue_scripts', 'add_scripts', 10);
add_action('wp_enqueue_scripts', 'my_deregister_scripts', 100);
add_action('wp_enqueue_scripts', 'my_deregister_styles', 100);

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Add theme support
|-------------------------------------------------------------------------------------------------------------------------------
*/
add_theme_support('automatic-feed-links');
add_theme_support('nav-menus');
add_theme_support( 'post-thumbnails' );
add_post_type_support('page', 'excerpt');

if (function_exists('register_nav_menus')) {
    register_nav_menus(
        array(
            'main' => 'Main',
            'footer_nav_1' => 'Footer Nav 1',
            'footer_nav_2' => 'Footer Nav 2',
            'helpful' => 'Helpful Info',
            'privacy' => 'Privacy Info',
        )
    );
}
/*
|-------------------------------------------------------------------------------------------------------------------------------
|   Add theme sidebars
|-------------------------------------------------------------------------------------------------------------------------------
*/
if (function_exists('register_sidebar')) {
    register_sidebar(array(
        'name' => __('Main - Sidebar'),
        'id' => 'main-sidebar-widget-area',
        'description' => 'Widgets in this area will be shown on the right sidebar of default page',
        'before_widget' => '<aside class="widget">',
        'after_widget' => '</aside>',
        'before_title' => '',
        'after_title' => '',
    ));
}
/*
|-------------------------------------------------------------------------------------------------------------------------------
|   Comment formatting
|-------------------------------------------------------------------------------------------------------------------------------
*/
function theme_comments($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li>
        <article <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
            <header class="comment-author vcard">
    <?php echo get_avatar($comment, $size = '48', $default = '<path_to_url>'); ?>
    <?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
                <time><a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(), get_comment_time()) ?></a></time>
                <?php edit_comment_link(__('(Edit)'), '  ', '') ?>
            </header>
                <?php if ($comment->comment_approved == '0') : ?>
                <em><?php _e('Your comment is awaiting moderation.') ?></em>
                <br />
            <?php endif; ?>

    <?php comment_text() ?>

            <nav>
            <?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
            </nav>
        </article>
        <!-- </li> is added by wordpress automatically -->
                <?php
}
/*
|-------------------------------------------------------------------------------------------------------------------------------
|   Map_via_acf
|-------------------------------------------------------------------------------------------------------------------------------
*/
function my_acf_init()
{
   acf_update_setting('google_api_key', 'AIzaSyC44n4EJxputPRoWzorOaszqW-dFoVN8UE');
}
add_action('acf/init', 'my_acf_init');

/*
|-----------------------------------------------------------------------------------
| Pagination
|-----------------------------------------------------------------------------------
|
*/

// Define post type
add_action('pre_get_posts', 'posts_per_page_archive');
function posts_per_page_archive( $query ) { 
    
    if ($query->is_main_query() && (!is_admin()) && is_post_type_archive(OLIVE_NEWS_PT) ) {
        $query->set('posts_per_page', 6);
    }
    elseif ($query->is_main_query() && (!is_admin()) && is_post_type_archive(OLIVE_BLOG_PT)) {
        $query->set('posts_per_page', 6);
    } 
    elseif ($query->is_main_query() && (!is_admin()) && is_post_type_archive(OLIVE_CSR_PT)) {
        $query->set('posts_per_page', 6);
    } 
    elseif ($query->is_main_query() && (!is_admin()) && is_post_type_archive(OLIVE_TESTIMONIAL_PT)) {
        $query->set('posts_per_page', 6);
    } 
    elseif ($query->is_main_query() && (!is_admin()) && is_post_type_archive(OLIVE_INDUSTRIAL_PT)) {
        $query->set('posts_per_page', 9);
    } 
    elseif ($query->is_main_query() && (!is_admin()) && is_post_type_archive(OLIVE_ADVANTAGE_PT)) {
        $query->set('posts_per_page', 6);
    } 
    elseif ($query->is_main_query() && (!is_admin()) && is_tax(OLIVE_LOCATION_TAXO)) {
        $query->set('posts_per_page', 12);
    }
}

if ( ! function_exists('pagination_widget') ) :
    function pagination_widget() {
        global $wp_query;

        $big = 999999999; // need an unlikely integer

        echo paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $wp_query->max_num_pages,
                'show_all' => false,
                'end_size' => 1,
                'mid_size' => 2,
                'prev_next' => true,
                'prev_text' => __('PREV'),
                'next_text' => __('NEXT'),
        ) );
    }
endif;

/*
|-----------------------------------------------------------------------------------
| WPML (Multi Language)
|-----------------------------------------------------------------------------------
|
*/
function language_selector_flags() {
    $languages = icl_get_languages('skip_missing=0&orderby=');
    if (!empty($languages)) {
        foreach ($languages as $l) {
            echo '<a href="' . $l['url'] . '">';
            echo '<img src="' . $l['country_flag_url'] . '" width="16" height="11" alt="' . $l['language_code'] . '" />';
            echo '</a>';
        }
    }
}

add_action('body_class', 'wpml_add_classes');
function wpml_add_classes($classes) {
    global $sitepress;
    if (is_a($sitepress, 'SitePress') && method_exists($sitepress, 'get_current_language')) {
        $classes[] = 'wpml-lang-' . $sitepress->get_current_language();
    }
    return $classes;
}

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Contact Information
|-------------------------------------------------------------------------------------------------------------------------------
| Phone, 
| Email, 
| Website Url
|-------------------------------------------------------------------------------------------------------------------------------
|
*/
function contact_link($input_info = null, $attribute_name = null) {
    $explode_info = explode(',', $input_info);
    $explode_count = count($explode_info);
    $oup_key = 1;

    foreach ($explode_info as $output_data) {
        $output_link = "<a href='".$attribute_name.":".trim( $output_data )."'>";
        $output_link .= trim($output_data);
        $output_link .= "</a>";
        $output_link .= ($oup_key < $explode_count) ? ', ' : '';
        echo $output_link;
        $oup_key++;
    }
}

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Remove type attribute for Javascript and style in WordPress
|-------------------------------------------------------------------------------------------------------------------------------
|
*/
add_filter('style_loader_tag', 'codeless_remove_type_attr', 10, 2);
add_filter('script_loader_tag', 'codeless_remove_type_attr', 10, 2);
function codeless_remove_type_attr($tag, $handle) {
    return preg_replace( "/type=['\"]text\/(javascript|css)['\"]/", '', $tag );
}

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Check handler name
|-------------------------------------------------------------------------------------------------------------------------------
| Ref: https://crunchify.com/how-to-print-all-loaded-java-scripts-and-css-stylesheets-handle-for-your-wordpress-blog/
|
*/
// add_action('wp_print_scripts', 'crunchify_print_scripts_styles');
function crunchify_print_scripts_styles() {
    // Print all loaded Scripts
    global $wp_scripts;
    foreach( $wp_scripts->queue as $script ) {
        echo $script . '  **  ';
    }
 
    // Print all loaded Styles (CSS)
    global $wp_styles;
    foreach( $wp_styles->queue as $style ) {
        echo $style . '  ||  ';
    }
}

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Adding an [async] tag to enqueued scripts with custom handler name
|-------------------------------------------------------------------------------------------------------------------------------
|
*/
// add_filter('script_loader_tag', 'custom_add_async_attribute', 10, 2);
function custom_add_async_attribute($tag, $handle) {

    // Add async attribute to these scripts
    $custom_scripts = array('my-js-handle-1', 'my-js-handle-2', 'my-js-handle-etc');
    
	// if ($_GET['debug'] == 'yes') {
	// 	echo $handle;
    // }

	if (in_array($handle, $custom_scripts)) {
		return str_replace(' src', ' async src', $tag);	
    }
	else {
		return $tag;	
	}
}

/*
|-------------------------------------------------------------------------------------------------------------------------------
| Adding an [async] tag to enqueued scripts except handler name
|-------------------------------------------------------------------------------------------------------------------------------
| Ref: https://matthewhorne.me/defer-async-wordpress-scripts/
|
*/
// add_filter('script_loader_tag', 'add_async_attribute', 10, 2);
function add_async_attribute($tag, $handle) {
    // Do not add async attribute to these scripts
    $scripts_to_exclude = array('my-js-handle-1', 'my-js-handle-2', 'my-js-handle-etc');
    
    foreach($scripts_to_exclude as $exclude_script) {
        if ($exclude_script === $handle) {
            return $tag;
        }
    }
    
    // Async all remaining scripts not excluded above
    return str_replace(' src', ' async src', $tag);
}