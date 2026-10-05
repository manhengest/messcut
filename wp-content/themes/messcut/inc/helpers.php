<?php
/**
 * Theme helpers.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read an ACF option with a default.
 */
function messcut_get_option( string $key, mixed $default = '' ): mixed {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, 'option' );
		if ( null !== $value && false !== $value && '' !== $value && array() !== $value ) {
			return $value;
		}
	}

	$stored = get_option( 'messcut_site_options', array() );
	if ( is_array( $stored ) && array_key_exists( $key, $stored ) && null !== $stored[ $key ] && false !== $stored[ $key ] && '' !== $stored[ $key ] && array() !== $stored[ $key ] ) {
		return $stored[ $key ];
	}

	return $default;
}

/**
 * Option in the current language, then the default language.
 */
function messcut_get_localized_option( string $key, mixed $default = '' ): mixed {
	return messcut_get_option( $key, $default );
}

function messcut_phone(): string {
	return (string) messcut_get_option( 'phone', '+38 (095) 477-11-22' );
}

function messcut_telegram(): string {
	return (string) messcut_get_option( 'telegram', '@messcutstrategy' );
}

function messcut_email(): string {
	return (string) messcut_get_option( 'email', 'admin@messcut.com' );
}

function messcut_whatsapp(): string {
	return (string) messcut_get_option( 'whatsapp', '+38 (095) 477-11-22' );
}

function messcut_telegram_url(): string {
	$handle = ltrim( messcut_telegram(), '@' );
	return 'https://t.me/' . rawurlencode( $handle );
}

function messcut_whatsapp_url(): string {
	$digits = preg_replace( '/\D+/', '', messcut_whatsapp() );
	return 'https://wa.me/' . $digits;
}

function messcut_instagram_url(): string {
	$url = (string) messcut_get_option( 'instagram_1', 'https://www.instagram.com/valeria.messcut' );
	return $url ? $url : 'https://www.instagram.com/valeria.messcut';
}

function messcut_contact_url(): string {
	if ( is_front_page() ) {
		return '#lead';
	}
	if ( messcut_is_translated_page( 'poslugy' ) || is_singular( 'case_study' ) ) {
		return '#lead-form';
	}
	$home = function_exists( 'pll_home_url' ) ? pll_home_url() : home_url( '/' );
	return trailingslashit( $home ) . '#lead';
}

/**
 * True when the current page is a translation of the Ukrainian slug.
 */
function messcut_is_translated_page( string $uk_slug ): bool {
	if ( ! is_page() ) {
		return false;
	}

	$id = (int) get_queried_object_id();
	if ( get_post_field( 'post_name', $id ) === $uk_slug ) {
		return true;
	}

	if ( function_exists( 'pll_get_post' ) ) {
		$uk_id = pll_get_post( $id, 'uk' );
		if ( $uk_id && get_post_field( 'post_name', $uk_id ) === $uk_slug ) {
			return true;
		}
	}

	return false;
}

function messcut_page_url( string $slug ): string {
	$pages = get_posts(
		array(
			'name'             => $slug,
			'post_type'        => 'page',
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'lang'             => '',
			'suppress_filters' => true,
		)
	);
	$page  = $pages[0] ?? null;
	if ( $page instanceof WP_Post && function_exists( 'pll_get_post' ) && function_exists( 'pll_current_language' ) ) {
		$lang = pll_current_language();
		if ( $lang ) {
			$translated = pll_get_post( (int) $page->ID, $lang );
			if ( $translated ) {
				return (string) get_permalink( (int) $translated );
			}
		}
	}
	if ( $page instanceof WP_Post ) {
		return (string) get_permalink( $page );
	}
	return home_url( '/' . trim( $slug, '/' ) . '/' );
}

/**
 * Services-hub anchor for a service, in the current language.
 */
function messcut_service_anchor( string $slug ): string {
	$posts = get_posts(
		array(
			'name'             => $slug,
			'post_type'        => 'service',
			'post_status'      => 'publish',
			'posts_per_page'   => 1,
			'lang'             => '',
			'suppress_filters' => true,
		)
	);
	$id    = $posts ? (int) $posts[0]->ID : 0;
	if ( $id && function_exists( 'pll_get_post' ) && function_exists( 'pll_current_language' ) ) {
		$lang        = pll_current_language();
		$translated  = $lang ? pll_get_post( $id, $lang ) : 0;
		if ( $translated ) {
			$id = (int) $translated;
		}
	}
	$name = $id ? (string) get_post_field( 'post_name', $id ) : $slug;
	return messcut_page_url( 'poslugy' ) . '#svc-' . $name;
}

function messcut_approach_url(): string {
	return messcut_page_url( 'dosvid' );
}

function messcut_cases_archive_url(): string {
	$link = get_post_type_archive_link( 'case_study' );
	return $link ? (string) $link : home_url( '/cases/' );
}

function messcut_insights_archive_url(): string {
	$link = get_post_type_archive_link( 'article' );
	return $link ? (string) $link : home_url( '/articles/' );
}

/**
 * @return array<int, array{url: string, label: string}>
 */
function messcut_nav_items(): array {
	return array(
		array( 'url' => messcut_page_url( 'poslugy' ), 'label' => __( 'Послуги', 'messcut' ) ),
		array( 'url' => messcut_cases_archive_url(), 'label' => __( 'Кейси', 'messcut' ) ),
		array( 'url' => messcut_approach_url(), 'label' => __( 'Досвід та підхід', 'messcut' ) ),
		array( 'url' => messcut_insights_archive_url(), 'label' => __( 'Інсайти', 'messcut' ) ),
	);
}

function messcut_fallback_primary_menu(): void {
	echo '<ul class="site-header__menu">';
	foreach ( messcut_nav_items() as $item ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $item['url'] ), esc_html( $item['label'] ) );
	}
	echo '</ul>';
}

function messcut_get_acf( string $key, ?int $post_id = null ): mixed {
	$id = $post_id ?? get_the_ID();
	if ( function_exists( 'get_field' ) && $id ) {
		$value = get_field( $key, $id );
		if ( null !== $value && false !== $value && '' !== $value && array() !== $value ) {
			return $value;
		}
	}
	if ( ! $id ) {
		return null;
	}
	$meta = get_post_meta( $id, $key, true );
	if ( false === $meta || '' === $meta || array() === $meta ) {
		return null;
	}
	return $meta;
}

function messcut_get_cases_query( int $limit = -1, int $exclude = 0 ): WP_Query {
	$args = array(
		'post_type'      => 'case_study',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
	);
	if ( $exclude ) {
		$args['post__not_in'] = array( $exclude );
	}
	return new WP_Query( $args );
}

function messcut_get_articles_query( int $limit = 3 ): WP_Query {
	return new WP_Query( array(
		'post_type'      => 'article',
		'posts_per_page' => $limit,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
	) );
}

/**
 * @return array<int, array{q: string, a: string}>
 */
function messcut_get_faq_items(): array {
	$rows  = messcut_get_option( 'home_faq', array() );
	$items = messcut_normalize_faq_rows( is_array( $rows ) ? $rows : array() );
	if ( $items ) {
		return $items;
	}
	$seed = function_exists( 'messcut_get_faq_seed_data' ) ? messcut_get_faq_seed_data() : array();
	return messcut_normalize_faq_rows( is_array( $seed ) ? $seed : array() );
}

/**
 * Flatten home_faq rows, including a grouped list, into question/answer pairs.
 *
 * @param array<int, mixed> $rows Raw FAQ rows.
 * @return array<int, array{q: string, a: string}>
 */
function messcut_normalize_faq_rows( array $rows ): array {
	$items = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		if ( ! empty( $row['items'] ) && is_array( $row['items'] ) ) {
			foreach ( messcut_normalize_faq_rows( $row['items'] ) as $item ) {
				$items[] = $item;
			}
			continue;
		}
		$q = trim( wp_strip_all_tags( (string) ( $row['question'] ?? $row['q'] ?? '' ) ) );
		$a = trim( wp_strip_all_tags( (string) ( $row['answer'] ?? $row['a'] ?? '' ) ) );
		if ( '' === $q || '' === $a ) {
			continue;
		}
		$items[] = array(
			'q' => __( $q, 'messcut' ),
			'a' => __( $a, 'messcut' ),
		);
	}
	return $items;
}

/**
 * @return array<string, string>
 */
function messcut_get_partner_brand_logo_files(): array {
	return array(
		'Puma'       => 'puma.png',
		'McDonald\'s'=> 'mcdonalds.png',
		'Comfy'      => 'comfy.png',
		'Toyota'     => 'toyota.png',
		'Lexus'      => 'lexus.png',
		'Silpo'      => 'silpo.png',
		'Binance'    => 'binance.png',
		'MD Fashion' => 'md-fashion.png',
		'Inzhur'     => 'inzhur.png',
		'Prom'       => 'prom.png',
		'Uklon'      => 'uklon.png',
		'Sweet.tv'   => 'sweet-tv.png',
		'Pepsi'      => 'pepsi.png',
		'Lifecell'   => 'lifecell.png',
		'Lamic'      => 'lamic.png',
		'Prostor'    => 'prostor.png',
		'Socar'      => 'socar.png',
		'Flint'      => 'flint.png',
		'Chipsters'  => 'chipsters.png',
	);
}

/**
 * @return array<int, array{name: string, logo_file: string}>
 */
function messcut_get_partner_brands_seed(): array {
	$brands = array();
	foreach ( messcut_get_partner_brand_logo_files() as $name => $file ) {
		$brands[] = array( 'name' => $name, 'logo_file' => $file );
	}
	return $brands;
}

function messcut_get_partner_logo_asset_url( string $logo_file ): string {
	$logo_file = ltrim( $logo_file, '/' );
	$path      = MESSCUT_DIR . '/assets/img/partners/' . $logo_file;
	if ( '' === $logo_file || str_contains( $logo_file, '..' ) || ! is_readable( $path ) ) {
		return '';
	}
	return MESSCUT_URI . '/assets/img/partners/' . $logo_file;
}

/**
 * White wordmarks on a transparent canvas disappear on the white pill.
 *
 * @param string $logo_file Filename or URL path.
 */
function messcut_partner_logo_is_light( string $logo_file ): bool {
	$path = (string) parse_url( $logo_file, PHP_URL_PATH );
	$base = strtolower( basename( '' !== $path ? $path : $logo_file ) );
	return in_array( $base, array( 'comfy.png', 'chipsters.png' ), true );
}

/**
 * @return array<int, array{name: string, logo_url: string, light: bool}>
 */
function messcut_get_partner_brands(): array {
	$rows = messcut_get_option( 'partner_brands', array() );
	if ( ! is_array( $rows ) || ! $rows ) {
		$rows = messcut_get_partner_brands_seed();
	}
	$files  = messcut_get_partner_brand_logo_files();
	$brands = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$name = trim( (string) ( $row['name'] ?? '' ) );
		if ( '' === $name ) {
			continue;
		}
		$url  = '';
		$file = '';
		if ( is_array( $row['logo'] ?? null ) && ! empty( $row['logo']['url'] ) ) {
			$url  = (string) $row['logo']['url'];
			$file = $url;
		}
		if ( '' === $url ) {
			$file = (string) ( $row['logo_file'] ?? ( $files[ $name ] ?? '' ) );
			$url  = messcut_get_partner_logo_asset_url( $file );
		}
		$brands[] = array(
			'name'     => $name,
			'logo_url' => $url,
			'light'    => messcut_partner_logo_is_light( $file ),
		);
	}
	return $brands;
}

/**
 * @param mixed $image ACF image.
 * @return array{url: string, alt: string}
 */
function messcut_acf_media( mixed $image, string $size = 'large' ): array {
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return array(
			'url' => (string) ( $image['sizes'][ $size ] ?? $image['url'] ),
			'alt' => (string) ( $image['alt'] ?? '' ),
		);
	}
	if ( is_numeric( $image ) ) {
		$url = wp_get_attachment_image_url( (int) $image, $size );
		return array( 'url' => $url ? (string) $url : '', 'alt' => '' );
	}
	return array( 'url' => '', 'alt' => '' );
}

/**
 * @param mixed $images Gallery.
 * @return array<int, array{url: string, alt: string}>
 */
function messcut_acf_gallery( mixed $images, string $size = 'medium' ): array {
	if ( ! is_array( $images ) ) {
		return array();
	}
	$out = array();
	foreach ( $images as $image ) {
		$media = messcut_acf_media( $image, $size );
		if ( $media['url'] ) {
			$out[] = $media;
		}
	}
	return $out;
}

/**
 * @return array<int, array<string, mixed>>
 */
function messcut_get_approach_team(): array {
	$rows = messcut_get_acf( 'team_members' );
	$team = array();
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$name = trim( (string) ( $row['name'] ?? '' ) );
			if ( '' === $name ) {
				continue;
			}
			$role = trim( (string) ( $row['role'] ?? $row['summary'] ?? '' ) );
			$team[] = array(
				'name'       => $name,
				'role'       => '' !== $role ? __( $role, 'messcut' ) : __( 'Роль / посада', 'messcut' ),
				'years'      => __( trim( (string) ( $row['years'] ?? '' ) ), 'messcut' ),
				'superpower' => __( trim( (string) ( $row['superpower'] ?? '' ) ), 'messcut' ),
				'photo'      => messcut_acf_media( $row['photo'] ?? null ),
				'logos'      => messcut_acf_gallery( $row['brand_logos'] ?? array() ),
			);
		}
	}
	if ( $team ) {
		return $team;
	}
	return array(
		array( 'name' => 'Валерія', 'role' => __( 'Роль / посада', 'messcut' ), 'years' => __( 'X років', 'messcut' ), 'superpower' => __( 'Супер-сила спеціаліста', 'messcut' ), 'photo' => array( 'url' => '', 'alt' => '' ), 'logos' => array() ),
		array( 'name' => 'Марія', 'role' => __( 'Роль / посада', 'messcut' ), 'years' => __( 'X років', 'messcut' ), 'superpower' => __( 'Супер-сила спеціаліста', 'messcut' ), 'photo' => array( 'url' => '', 'alt' => '' ), 'logos' => array() ),
		array( 'name' => 'Аліна', 'role' => __( 'Роль / посада', 'messcut' ), 'years' => __( 'X років', 'messcut' ), 'superpower' => __( 'Супер-сила спеціаліста', 'messcut' ), 'photo' => array( 'url' => '', 'alt' => '' ), 'logos' => array() ),
	);
}

/**
 * Services grouped into the two directions from the raw page.
 *
 * @return array<int, array<string, mixed>>
 */
function messcut_get_service_groups(): array {
	$query = new WP_Query( array(
		'post_type'      => 'service',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
	) );
	$by_direction = array( 'branding' => array(), 'marketing' => array() );
	while ( $query->have_posts() ) {
		$query->the_post();
		$id        = get_the_ID();
		$direction = (string) ( messcut_get_acf( 'direction', $id ) ?: 'marketing' );
		if ( ! isset( $by_direction[ $direction ] ) ) {
			$direction = 'marketing';
		}
		$bullets = messcut_get_acf( 'bullets', $id );
		$steps   = messcut_get_acf( 'steps', $id );
		$proof_id = (int) messcut_get_acf( 'proof_case', $id );
		$proof    = array();
		if ( $proof_id ) {
			$proof = array(
				'url'   => get_permalink( $proof_id ),
				'stat'  => (string) messcut_get_acf( 'proof_stat', $id ),
				'label' => (string) messcut_get_acf( 'proof_label', $id ),
			);
		}
		$by_direction[ $direction ][] = array(
			'anchor'  => 'svc-' . get_post_field( 'post_name', $id ),
			'title'   => get_the_title(),
			'eyebrow' => (string) messcut_get_acf( 'eyebrow', $id ),
			'teaser'  => (string) ( messcut_get_acf( 'teaser', $id ) ?: get_the_excerpt() ),
			'bullets' => is_array( $bullets ) ? array_map( static fn( $row ) => (string) ( $row['text'] ?? '' ), $bullets ) : array(),
			'steps'   => is_array( $steps ) ? $steps : array(),
			'locked'  => (bool) messcut_get_acf( 'locked', $id ),
			'cta'     => (string) ( messcut_get_acf( 'cta_label', $id ) ?: __( 'Обговорити', 'messcut' ) ),
			'proof'   => $proof,
		);
	}
	wp_reset_postdata();

	return array(
		array(
			'id'       => 'direction-branding',
			'eyebrow'  => __( 'Напрям 01', 'messcut' ),
			'title'    => __( 'Брендинг', 'messcut' ),
			'text'     => __( 'Визначаємо, хто ви, для кого працюєте і чому вас мають обрати', 'messcut' ),
			'services' => $by_direction['branding'],
		),
		array(
			'id'       => 'direction-marketing',
			'eyebrow'  => __( 'Напрям 02', 'messcut' ),
			'title'    => __( 'Маркетинг', 'messcut' ),
			'text'     => __( 'Наводимо фокус у маркетингу: визначаємо пріоритети та наступні кроки', 'messcut' ),
			'services' => $by_direction['marketing'],
		),
	);
}

function messcut_render_logo( string $variant = 'black', array $args = array() ): void {
	$class  = isset( $args['class'] ) ? (string) $args['class'] : 'site-logo';
	$width  = isset( $args['width'] ) ? (int) $args['width'] : 160;
	$height = isset( $args['height'] ) ? (int) $args['height'] : 32;
	$linked = ! array_key_exists( 'linked', $args ) || false !== $args['linked'];
	$variant = 'white' === $variant ? 'white' : 'black';
	$file    = 'logo-' . $variant . '.svg';
	if ( ! file_exists( MESSCUT_DIR . '/assets/img/' . $file ) ) {
		$file = 'logo-' . $variant . '.png';
	}
	$url = MESSCUT_URI . '/assets/img/' . $file;
	$img = sprintf( '<img src="%s" alt="%s" width="%d" height="%d" decoding="async">', esc_url( $url ), esc_attr( get_bloginfo( 'name' ) ), $width, $height );
	if ( $linked ) {
		printf( '<a class="%s" href="%s">%s</a>', esc_attr( $class ), esc_url( home_url( '/' ) ), $img );
		return;
	}
	printf( '<span class="%s">%s</span>', esc_attr( $class ), $img );
}
