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
define( 'DB_NAME', 'tk' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'XAkX12kkv_E<BEH4pU?/wkxBBGh/m6j/V2UxaG9qu=4hu73bY7U@/Cg:^&=e0~K&' );
define( 'SECURE_AUTH_KEY',  '&^6,i-n0UWgzbI:nC8*dh8RXHRg0*GTDb*9 4iRElwN-[^f%_Hb.dutcW(W~Bxu.' );
define( 'LOGGED_IN_KEY',    '2fN?gr1FZ{4@#&%/_Y<6$DkDakV+AnYVkINf==]#l}Ftd)HRgo)@0-lAx491N{@:' );
define( 'NONCE_KEY',        'ebe]8]E]9G`nP-7N976xkmS~/Bh#A0O:X7.7H^Iw9?XO+`NtW9+l RdL,xz-UgfE' );
define( 'AUTH_SALT',        '>|&;Bgzo#<UC/2-OxK5BP)2}!aTG?C}5mP;kv{#ygn $O*(:e837QtCX~UJn4`8#' );
define( 'SECURE_AUTH_SALT', 'VQnfhO4,|hRRT#2_`{+^DGN$4 WH;6?CK<_(:7k84Kbm3?>KbIz,-3} JE!p^H,i' );
define( 'LOGGED_IN_SALT',   '}(m0D`ZWUf8IY`C_u{RC3DFY9?#w?uA{;`N5=PR.XL_9ft^oa<Q,9BS9dvx>n%,U' );
define( 'NONCE_SALT',       '{5}jL`N~Yds?!iKNq2htC9U{a}.$NwIhn`hu(i0X>v^Dr/kLoO%Att6:o<oCK|Q9' );

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
