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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'tarifas' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'IBoFDMd8hk9hN1eH10NDGTVIvVK3fiOdFVtX6j5aEUkHKUie2BMR8FSqSukEP7Mz' );
define( 'SECURE_AUTH_KEY',  'Sp4sPpniBZojnUUS1W4r01E4z0FE9Z4asctacFBirF6MLHb4OOTMx0YeoA4FelY9' );
define( 'LOGGED_IN_KEY',    'MjzRbWVk9JG4FRXon62KZdU6twyzrkENKFafpCAciEP1m7dDayWCUYVbyD3Sqc50' );
define( 'NONCE_KEY',        '4TFGdsuCX2XoFE67KtNblR0sRWCXDlcrZmsZrFIW2p17Iuz7HYTfNjK4bCpLBcr8' );
define( 'AUTH_SALT',        'auH5Q7ZDsmRa48P4rrepPhRGgw1S89LJK9xE85HXJ34LziFRCiY1dsrOm15VFRuj' );
define( 'SECURE_AUTH_SALT', 'oq8L5kRvajkkvtuvU2T8TaVljFv7oKYVaRqnmersr6bI50g6h2DFg5TYv8dfup73' );
define( 'LOGGED_IN_SALT',   'VDW9i2aupQKrG1vcUG6wrOTz3aO9dnSnHrlgzN8acHxnLFCwjfM6ridG8zys0bz3' );
define( 'NONCE_SALT',       'Uzp1BceSOzM3VCJz34rLoBKyNk2joOPnsTapCCRi9cvBWPjitIwzKOg3RKdjnma3' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
