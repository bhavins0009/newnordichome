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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
//define( 'WPCACHEHOME', '/var/www/newnordichome.dk/public_html/wp-content/plugins/wp-super-cache/' );
define('WP_CACHE', true);
define('DB_NAME', 'newnordichome_local');
/** MySQL database username */
define('DB_USER', 'root');
/** MySQL database password */
define('DB_PASSWORD', 'root');
/** MySQL hostname */
define('DB_HOST', 'localhost');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');
/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('FORCE_SSL', false);
define('FORCE_SSL_ADMIN', false);

define('WP_DEBUG', true);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'hwupeR!&!Gkiqaz9XKME5o54Xi0AEs@#*LFW9KybJgBITrHx3n3A%wl5GpugFp%Z');
define('SECURE_AUTH_KEY',  'Pa7HTyZQX@wW8&9mhn3pAh&hJ(lXUE&mxs0HkXc5FMg6282@6y!kJ0xjRRumFsBz');
define('LOGGED_IN_KEY',    'tMD2QtaVPZ!&9)g^55VK9m*im#)smxrqQNNovgB@c*BM62lYKnTCA%UNPvI1iA9T');
define('NONCE_KEY',        'a@qyQicT3nYybU4vt1a%)mpdwi(vR0dhEMocy2P4VaorW03YMzXMDReRfV)#3A@@');
define('AUTH_SALT',        'IohCudfHAekq9i5rbmPlGzgRdnbh*oolwmhsw1BPDsTlZ6htYDjS3QG1dEF4slr#');
define('SECURE_AUTH_SALT', '(PEVd%hUd39mK)vXRcF0IoVY4CAcmRjSqlwkE6Z4IkAH6D(&lXrGppTd(K9kbkcK');
define('LOGGED_IN_SALT',   'M)ftOviCb296Y@ln9ze#)TzkcTWh6%uSdX0A2uCJGZRBjhvpPGA76IcrKnEWwBnX');
define('NONCE_SALT',       'xDs(YpD6L5y!Bu1udveI5q7k8YpCkgvi)jWqo(8bXKo2Tw5Z5A9WObvuMU8urn@4');
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'clk_42491aa6f3_wp_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);
/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
define( 'WP_ALLOW_MULTISITE', true );
define ('FS_METHOD', 'direct');
