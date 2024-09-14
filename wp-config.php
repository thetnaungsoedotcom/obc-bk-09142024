<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

switch ($_SERVER['HTTP_HOST']) {    
    case 'olive.b360':    
        define('DB_NAME', 'olive_dev');
        define('DB_USER', 'root');
        define('DB_PASSWORD', '');
        define('DB_HOST', 'localhost');
        break;
    case 'demo.staging.com':
        define('DB_NAME', 'db_staging');
        define('DB_USER', 'username_staging');
        define('DB_PASSWORD', '_Tb@B360{{DM}}&marketing');
        define('DB_HOST', 'localhost');
        break;
    default :
        define('DB_NAME', 'obcmm_b360_production');
        define('DB_USER', 'obcmm_admin_b360');
        define('DB_PASSWORD', 'x-Us?CD]5A~3');
        define('DB_HOST', 'localhost');
        break;
}

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'olive_';

/** 
 * Protocol
 * 
 */
$protocol = (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) ? 'https://' : 'http://';

/* Turn HTTPS 'on' if HTTP_X_FORWARDED_PROTO matches 'https' */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
	if (strpos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false) {
		$_SERVER['HTTPS']='on';
	}
}

define('WP_SITEURL', $protocol . 'obcmm.com');
define('WP_HOME', $protocol . 'obcmm.com');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

/** 
 * Custom define
 * 
 */
define('B360_RD_URL', 'https://www.b360mm.com/contact/');
define('B360_BRAND_TXT', 'B360 Website Development Service in Yangon, Myanmar');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '4PyJJRA{ABmk39(O{- k8I@L=I+Xc(#(lk/xZdW_}XERp-fM_rsS}{tu5WItN !X');
define('SECURE_AUTH_KEY',  'lir3 O4Z.v<Dh4>Pt%_.hB3o]hl{~GbZV_Fr p$ ls9zCVHWXn#+SFc/M9$g$^--');
define('LOGGED_IN_KEY',    '`uSv+-7CZ|^5D:_G[d!1Kf4yH~wC|{3+[aGu[|4h^4)*Dfv3$fg;*8%/;P-O72pt');
define('NONCE_KEY',        '&|EV@-MlIr7JJt<=UCV)Bpu!JGO=W{.mk[W|y|6+9SHc1o/NtJ[pFOP@ `!$[s4%');
define('AUTH_SALT',        'J=I:/TiP i0{=7zO#T+4$7C,PSmrd^e_47IkL}+Uf 4-_GNK-2E6Q^.+K&//=}98');
define('SECURE_AUTH_SALT', '+epi[-AA$]8iESKY)NB0|3R2e.`#/lci*{Sr9>$1x~!Lx5I$f5aL6-|BGWGmZrDU');
define('LOGGED_IN_SALT',   '7tbCwcJko|!!(TO*(R8@.J;>BBc]UJX)O$X|_mHv$Ch^+6a+ht:JXZ9iG2~YR)Sp');
define('NONCE_SALT',       '>rxjFrxm[_RF #W)_G);BB%0F,~8U`3UvHnHV*isS]{jmqxTZ%:#/m?Ss@QI+w+&');

/**#@-*/

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define('WP_DEBUG', false);

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */

define('AUTOSAVE_INTERVAL', 180);  
define('WP_AUTO_UPDATE_CORE', false);
define('WP_POST_REVISIONS', 3);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define('ABSPATH', dirname(__FILE__) . '/');
}	

/** Enables page caching for Cache Enabler. */
if ( ! defined( 'WP_CACHE' ) ) {
	define( 'WP_CACHE', true );
}
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
