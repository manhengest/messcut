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
	$js_path  = MESSCUT_DIR . '/assets/js/core.js';

	wp_enqueue_style(
		'messcut-fonts',
		'https://fonts.googleapis.com/css2?family=Golos+Text:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600&family=Playfair+Display:wght@400;500;600;700&display=swap',
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
		MESSCUT_URI . '/assets/js/core.js',
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : MESSCUT_VERSION,
		true
	);

	$extra = array();
	if ( is_front_page() ) {
		$extra['messcut-home-canvas'] = '/assets/js/home-canvas.js';
	}
	if ( messcut_is_translated_page( 'poslugy' ) ) {
		$extra['messcut-services'] = '/assets/js/services.js';
		$extra['messcut-rail'] = '/assets/js/rail.js';
	}
	if ( is_singular( 'case_study' ) ) {
		$extra['messcut-case'] = '/assets/js/case.js';
		$extra['messcut-rail'] = '/assets/js/rail.js';
	}
	foreach ( $extra as $handle => $relative ) {
		$file = MESSCUT_DIR . $relative;
		wp_enqueue_script(
			$handle,
			MESSCUT_URI . $relative,
			array( 'messcut-main' ),
			file_exists( $file ) ? (string) filemtime( $file ) : MESSCUT_VERSION,
			true
		);
	}

	if ( isset( $extra['messcut-case'] ) ) {
		wp_localize_script(
			'messcut-case',
			'messcutCase',
			array(
				'stage' => __( 'Етап %1$d / %2$d', 'messcut' ),
				'prev'  => __( 'Назад', 'messcut' ),
				'next'  => __( 'Далі', 'messcut' ),
			)
		);
	}

	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'messcut-main', 'messcut', MESSCUT_DIR . '/languages' );
	}

	wp_localize_script(
		'messcut-main',
		'messcutData',
		array(
			'restUrl'       => esc_url_raw( rest_url( 'messcut/v1/lead' ) ),
			'nonce'         => wp_create_nonce( 'wp_rest' ),
			'successMsg'    => esc_html__( 'Дякуємо! Звʼяжемось з вами протягом 24 год', 'messcut' ),
			'errorRequired' => esc_html__( 'Заповніть обовʼязкові поля.', 'messcut' ),
			'errorSubmit'   => esc_html__( 'Не вдалося надіслати заявку. Спробуйте пізніше.', 'messcut' ),
			'errorNetwork'  => esc_html__( 'Помилка мережі. Спробуйте пізніше.', 'messcut' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'messcut_enqueue_assets' );

/**
 * Lock mobile viewport height before first paint so sections do not
 * shrink when the browser chrome (top/bottom bars) shows on scroll.
 */
function messcut_print_app_height_script(): void {
	if ( is_admin() ) {
		return;
	}
	?>
<script>
(function () {
	if (!window.matchMedia || !window.matchMedia('(max-width: 767.98px)').matches) {
		return;
	}
	document.documentElement.style.setProperty('--app-height', window.innerHeight + 'px');
})();
</script>
	<?php
}
add_action( 'wp_head', 'messcut_print_app_height_script', 1 );

/**
 * Whether the local BrowserSync server is reachable from this PHP process.
 *
 * Inside Docker, BrowserSync runs on the host (:3000), so we probe
 * host.docker.internal as well as loopback.
 */
function messcut_browsersync_is_running( int $port = 3000 ): bool {
	$hosts = array( 'host.docker.internal', '127.0.0.1', 'localhost' );

	foreach ( $hosts as $host ) {
		$socket = @fsockopen( $host, $port, $errno, $errstr, 0.2 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		if ( $socket ) {
			fclose( $socket );
			return true;
		}
	}

	return false;
}

/**
 * Inject BrowserSync client so CSS hot-reload works on :8080 and :3000.
 * Only when site URL is local and `npm run dev` is up.
 */
function messcut_browsersync_client(): void {
	if ( is_admin() ) {
		return;
	}

	if ( defined( 'MESSCUT_BROWSER_SYNC' ) && ! MESSCUT_BROWSER_SYNC ) {
		return;
	}

	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	if ( ! in_array( $host, array( 'localhost', '127.0.0.1' ), true ) ) {
		return;
	}

	$port = defined( 'MESSCUT_BROWSER_SYNC_PORT' ) ? (int) MESSCUT_BROWSER_SYNC_PORT : 3000;

	if ( ! defined( 'MESSCUT_BROWSER_SYNC' ) || ! MESSCUT_BROWSER_SYNC ) {
		if ( ! messcut_browsersync_is_running( $port ) ) {
			return;
		}
	}

	printf(
		'<script id="__bs_script__" async src="%s"></script>' . "\n",
		esc_url( sprintf( 'http://%s:%d/browser-sync/browser-sync-client.js', $host, $port ) )
	);
}
add_action( 'wp_footer', 'messcut_browsersync_client', 99 );
