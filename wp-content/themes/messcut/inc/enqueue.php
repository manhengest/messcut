<?php
/**
 * Asset enqueue.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Preconnect to Google Fonts origins.
 *
 * @param array<int, string|array<string, string|bool>> $urls          URLs to print for resource hints.
 * @param string                                         $relation_type The relation type the URLs are printed for.
 * @return array<int, string|array<string, string|bool>>
 */
function messcut_resource_hints( $urls, string $relation_type ): array {
	if ( 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.googleapis.com',
		);
		$urls[] = array(
			'href'        => 'https://fonts.gstatic.com',
			'crossorigin' => 'anonymous',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'messcut_resource_hints', 10, 2 );

/**
 * Enqueue front-end styles and scripts.
 */
function messcut_enqueue_assets(): void {
	$css_path = MESSCUT_DIR . '/assets/css/main.css';
	$js_path  = MESSCUT_DIR . '/assets/js/main.js';

	wp_enqueue_style(
		'messcut-fonts',
		'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Oswald:wght@400;500;600;700&family=Source+Sans+3:ital,wght@0,400;0,600;0,700;1,400&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'messcut-main',
		MESSCUT_URI . '/assets/css/main.css',
		array( 'messcut-fonts' ),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : MESSCUT_VERSION
	);

	wp_enqueue_script(
		'messcut-main',
		MESSCUT_URI . '/assets/js/main.js',
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : MESSCUT_VERSION,
		true
	);

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'messcut-main', 'messcut', MESSCUT_DIR . '/languages' );
	}

	wp_localize_script(
		'messcut-main',
		'messcutData',
		array(
			'restUrl'       => esc_url_raw( rest_url( 'messcut/v1/lead' ) ),
			'nonce'         => wp_create_nonce( 'wp_rest' ),
			'successMsg'    => esc_html__( 'Дякуємо, Валерія найближчим часом звʼяжеться з вами', 'messcut' ),
			'errorRequired' => esc_html__( 'Заповніть обовʼязкові поля.', 'messcut' ),
			'errorSubmit'   => esc_html__( 'Не вдалося надіслати заявку. Спробуйте пізніше.', 'messcut' ),
			'errorNetwork'  => esc_html__( 'Помилка мережі. Спробуйте пізніше.', 'messcut' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'messcut_enqueue_assets' );
