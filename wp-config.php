<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'dbs14549584' );
// define( 'DB_NAME', 'picknprint_db' );

/** Database username */
// define( 'DB_USER', 'master_admin' );
define( 'DB_USER', 'root' );

/** Database password */
// define( 'DB_PASSWORD', 'Jhtalks@2025' );
define( 'DB_PASSWORD', '' );

/** Database hostname */
// define( 'DB_HOST', '87.106.61.147' );
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
define('AUTH_KEY',         'EzV8XGnIdsL1PhFewYCFqrCPSENICUaE0TNSiOjODqFNNYgWuFYHcppPiGL3XzoB');
define('SECURE_AUTH_KEY',  'hAIMAunMJxs0ekynEgqJz8rpdGS6CmNZMFzZoFppX2hUsfZfNFdqlZjdBRWSW6d2');
define('LOGGED_IN_KEY',    'ku92HLT7MFpIFnf08pP3H0TXarL5ZobTswTdSRkmwoxl1C89wQiz2akZ0LJOvLjT');
define('NONCE_KEY',        'LRiHuYgs71QURJU45b4imZBJczxQ7BX037ITSy0JLPLRNre819Qc4AJ92Lp2rapx');
define('AUTH_SALT',        'qrvSLcq3Z2r44g7Bdxd5dhl5Oyo3DvS5Tqy4PhCbQwFi9FRk3LKOhinR8c9zKz4a');
define('SECURE_AUTH_SALT', 'R4VBgrCn1zStwIXwVnnGoJU3yW5W2zmawgRzSlSJARp4jvCDKRFkD5Irw5MqzxeH');
define('LOGGED_IN_SALT',   'PP3sul1op1X9TgBx5YGEQIKcFF3q15sT3ch285rcnfgEgRPVmBezH3xuBgJDwKbk');
define('NONCE_SALT',       'A5fTpETUiiVzTLkhDfk1kBO2QxP5cSnHXwBFFIYQELZOmhOAGJtihlUAg7JkLMnI');

/**
 * Other customizations.
 */
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'lcqe_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
 //define( 'WP_DEBUG', false );

define( 'WP_DEBUG', true );

define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);


// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
define( 'SCRIPT_DEBUG', true );


/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
