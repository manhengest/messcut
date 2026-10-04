<?php
/**
 * Theme setup.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme supports and menus.
 */
function messcut_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );

	register_nav_menus( array(
		'primary' => esc_html__( 'Головне меню', 'messcut' ),
		'footer'  => esc_html__( 'Меню в підвалі', 'messcut' ),
	) );
}
add_action( 'after_setup_theme', 'messcut_setup' );

/**
 * English translations of poslugy and dosvid keep the same page templates.
 *
 * @param string $template Resolved template path.
 */
function messcut_translated_page_template( string $template ): string {
	$map = array(
		'poslugy' => 'page-poslugy.php',
		'dosvid'  => 'page-approach.php',
	);

	foreach ( $map as $slug => $file ) {
		if ( ! messcut_is_translated_page( $slug ) ) {
			continue;
		}
		$path = get_theme_file_path( $file );
		if ( is_readable( $path ) ) {
			return $path;
		}
	}

	return $template;
}
add_filter( 'template_include', 'messcut_translated_page_template' );

/**
 * Local installs sometimes come up with plain permalinks after a core restore.
 */
function messcut_ensure_permalinks(): void {
	if ( get_option( 'permalink_structure' ) ) {
		return;
	}

	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules( false );
}
add_action( 'init', 'messcut_ensure_permalinks', 99 );

/**
 * Flush rewrite rules on theme switch.
 */
function messcut_after_switch_theme(): void {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'messcut_after_switch_theme' );
