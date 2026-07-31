<?php
/**
 * ACF integration.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set ACF JSON save/load paths.
 *
 * @param string $path Default path.
 * @return string
 */
function messcut_acf_json_save_path( string $path ): string {
	return MESSCUT_DIR . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'messcut_acf_json_save_path' );

/**
 * @param array<int, string> $paths Load paths.
 * @return array<int, string>
 */
function messcut_acf_json_load_paths( array $paths ): array {
	unset( $paths[0] );
	$paths[] = MESSCUT_DIR . '/acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'messcut_acf_json_load_paths' );

/**
 * Register options page.
 */
function messcut_acf_options_page(): void {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( array(
		'page_title' => __( 'Налаштування MESSCUT', 'messcut' ),
		'menu_title' => 'MESSCUT',
		'menu_slug'  => 'messcut-settings',
		'capability' => 'edit_theme_options',
		'redirect'   => false,
	) );
}
add_action( 'acf/init', 'messcut_acf_options_page' );

/**
 * Admin notice when ACF Pro is missing.
 */
function messcut_acf_admin_notice(): void {
	if ( function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		esc_html__( 'Тема Messcut потребує ACF Pro для Options Page, Flexible Content та Relationship полів. Встановіть і активуйте плагін.', 'messcut' )
	);
}
add_action( 'admin_notices', 'messcut_acf_admin_notice' );
