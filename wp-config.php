<?php
define( 'WP_CACHE', false ); // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'mabalgar_wp431' );

/** Database username */
define( 'DB_USER', 'mabalgar_wp431' );

/** Database password */
define( 'DB_PASSWORD', 'Sf89[)b9p3' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '~a&ucO6V@35bsh|ce8S|HR(-Exx!:8T*:4!yH:o*WKC6QOP98Auzqk:O6%SP56Ir');
define('SECURE_AUTH_KEY', '1|8ngw~4](f|w+6o]0DzkS7vu-yK6*Qo_I2z42@Q:/O@)oK[@nJRh-39-G78)hVI');
define('LOGGED_IN_KEY', '(550BTUQt5w(56-(u_21le_VXm**[jj#tK_f5*)E0]N1Aq:i3Tmm_4#|]0l~4-~~');
define('NONCE_KEY', 'pZDHG_[r0IF7jV71g#&A7]+C)7HNSYmD4cO@xh1M_H%!73XY&(3DDG_0psJH5]4g');
define('AUTH_SALT', 'd!)*pc18-#%i_p2-5Ia2MSUD-&SW92#4dPa22GU%X6Kaxc5RRQ@(m65H/i!_tm[T');
define('SECURE_AUTH_SALT', 'n/+8%BGaK4q&-_cN7u21L#20K/xwnaC2Rncvfg02(c(]&#[]]48jS81757mmW;[0');
define('LOGGED_IN_SALT', 's8%sDWx+C15adMcigEN1ZKIbC5*Xo4)1_36|RR)4_3zj5kD9]q#!|9K:c#1j0W-J');
define('NONCE_SALT', 'PeY4t4+K[WZ_XFrVw2@P3A/0E9CI:g4oq8h@gbe8#3C#nMAR8af&7JV%4/Sf6q*R');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = '6F73W_';


/* Add any custom values between this line and the "stop editing" line. */

define('WP_ALLOW_MULTISITE', true);
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

	define( 'WP_DEBUG', true );
	define( 'WP_DEBUG_LOG', true );
	define( 'WP_DEBUG_DISPLAY', false );


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
