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
 * Flush rewrite rules on theme switch.
 */
function messcut_after_switch_theme(): void {
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'messcut_after_switch_theme' );
