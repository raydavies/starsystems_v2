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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'starlea_wp' );

/** MySQL database username */
define( 'DB_USER', 'starlea_wp' );

/** MySQL database password */
define( 'DB_PASSWORD', '6khUnZQLIlL9' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '^y3lUz:c5ZT}~] !Q(,irayGWY#gNYLG_o=RMlr^.Ie~zG,4%*LJSd OtZ;*0zjH' );
define( 'SECURE_AUTH_KEY',   '?=tq=m@=h_w[X_I/+l8Hq<4Cz<<,<nv~`6ZmB?yxilZ1Pi@Szn.6Z`MQ+R$kR?Zu' );
define( 'LOGGED_IN_KEY',     'mVyge>Jww`s)_oIN_<XuK!@!*hnXcx.t&w,87.Y!lW!_:W(Yt!{3-.k0.-vwSL<!' );
define( 'NONCE_KEY',         'ucC,X.v[.%C,v)*cE^RENs[Q~E||Fu9on_FKn-pRsod=*|7Y5kmj2pDV6XY@}.Cn' );
define( 'AUTH_SALT',         '$W1Qj<0&SWMF@]qQr|exiG<@a|(FZ-#xE{#Ri@O_<|OOQm;_/{I1jkujE&&AYdv_' );
define( 'SECURE_AUTH_SALT',  'u (Dhrg}Gilce5>,}?*6N-xTnV Li_bji$9z97|{&*oM+`nUA`5>ME^.6&++gWOc' );
define( 'LOGGED_IN_SALT',    'ljG6X#!VDYNO#X=}(3u[8:?l:Qf5sKBjdl>wpaC,ZP5Tz`c YNPLDLB4Z@&cbJk`' );
define( 'NONCE_SALT',        '8zh|,Tr8!TW&f0#}OXja/$}#?I 6S*5<O41$cq8K|`ZDj;3YBtN|h]OH07z6V({K' );
define( 'WP_CACHE_KEY_SALT', 'C=N;Oh{<8Nb6 SCu#W=E/|I{V,Z5tP/K{[wa5i>>7OUHj`}&$b=@tTrB]iNG1D7P' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
