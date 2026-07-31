<?php
/**
 * Messcut theme bootstrap.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MESSCUT_VERSION', '1.1.0' );
define( 'MESSCUT_CONTENT_VERSION', 2 );
define( 'MESSCUT_DIR', get_template_directory() );
define( 'MESSCUT_URI', get_template_directory_uri() );

$messcut_includes = array(
	'setup',
	'i18n',
	'enqueue',
	'helpers',
	'cpt',
	'acf',
	'forms',
	'seed',
	'seed-data',
	'seed-i18n',
);

foreach ( $messcut_includes as $file ) {
	$path = MESSCUT_DIR . '/inc/' . $file . '.php';
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}
