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
define('DB_NAME', 'starlea_wp467');

/** MySQL database username */
define('DB_USER', 'starlea_wp467');

/** MySQL database password */
define('DB_PASSWORD', 'sSt(p]2574');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'zkxesu89fpoffnzjtpevsqacob4i76a6qbridlc33hqygtamcqfehm6lsymhufao');
define('SECURE_AUTH_KEY',  'evguwgmqvlwkeduttetbdj1nyy9nas5hhbraawl4rvtpxpdyq7fgrgz1mj22gpax');
define('LOGGED_IN_KEY',    '708cef0f854qwnm0hadjha4motzkppgeotz87vi2thq9hi0qa1xj2hagdv0acdiu');
define('NONCE_KEY',        'lxst6prl3tgfw219vepb7tvxuceptniociyakeziapfafchmeb8q6obglr38fb5r');
define('AUTH_SALT',        'gzx6s5gaqvz5hzkhikuovrplchpatj5qay2xakxxuyadah5nuwn6juesown8ns5w');
define('SECURE_AUTH_SALT', 'pxqz42skbgmqguaddrvnjssvpnhfh6tir5fa4kgnv3fkbydcxgbxmgu6jaddqrxg');
define('LOGGED_IN_SALT',   'rr7sl2znmdpovbvyzi3qmvqifrrde8gmhphfqnzpxf0z65pbppawhvfhfdhmpars');
define('NONCE_SALT',       'frec5pxdwbolojgw7frorplqybatvfcifgxr87zazri7gzv7gy6hbdvgsvl0juea');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpu7_';

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
