<?php
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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'demo' );

/** Database username */
define( 'DB_USER', 'demo' );

/** Database password */
//define( 'DB_PASSWORD', 'Demo@123' );
define( 'DB_PASSWORD', 'Admin@123demo!!' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '.8f&>l`US^}#n.9MZM%Hy_?g_fyw3OO7;.hYBlZ)LB%C_yq>ag7S*0iJ^d8[>05>' );
define( 'SECURE_AUTH_KEY',  'C#ae*9huLwg}{]a%6w|iIeWp7eB^/$1EsP5?OY[ve@w|!;O+iY7__;)N6;UTiF? ' );
define( 'LOGGED_IN_KEY',    '-wtd%^~ ;pw,M_Ks_#p{NiJVJj?~s4!;9Y$f:p)9*~xfYL-y)$A}/!pC_r{B8!UJ' );
define( 'NONCE_KEY',        '3j71BG^Xa~$H@at|+d-EPR{O@{5X]ofQxS*+n3N@]wr0y.Es8y/%.kIL+{Tv|5[5' );
define( 'AUTH_SALT',        '|O25X>8T5@#vkB/% _2taE~;!#>$619>>gn@*JX,]}XB<N-8CqRHM#|>zpq#`DBD' );
define( 'SECURE_AUTH_SALT', ' ?Zwphk~RqWIl|w5gD*NDrVz]opkgLX+%d%UV:nu_|Cz-v1yW^-r*;B4Y?v[T~_]' );
define( 'LOGGED_IN_SALT',   '=C4W%*l9TWaWbP:(!F6 )&WEuDEpW?[3GX@oK*;oM=IFI1/(Jt2f-|!cr>vv|A{%' );
define( 'NONCE_SALT',       'Mi!(H?tB>G~Sov4Z$]jtoL>lGp3IE<5N=1G14NdXU9U9blF}91#FvEY|y00ksI7D' );

/**#@-*/

/**
 * WordPress database table prefix.
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

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
