<?php
/**
 * Internationalization and Polylang integration.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Load theme text domain (WP 6.7+ requires init or later).
 */
function messcut_load_textdomain(): void {
	load_theme_textdomain( 'messcut', MESSCUT_DIR . '/languages' );
}
add_action( 'init', 'messcut_load_textdomain' );

/**
 * Whether Polylang is active.
 */
function messcut_is_polylang_active(): bool {
	return function_exists( 'pll_current_language' ) && function_exists( 'pll_the_languages' );
}

/**
 * Current language slug (uk, en) or empty string.
 */
function messcut_current_lang(): string {
	if ( ! messcut_is_polylang_active() ) {
		return '';
	}

	return (string) pll_current_language( 'slug' );
}

/**
 * Whether the current front-end language is English.
 */
function messcut_is_english(): bool {
	return 'en' === messcut_current_lang();
}

/**
 * Register CPTs for Polylang translation.
 *
 * @param array<string, string> $post_types Post types.
 * @param bool                  $is_settings Settings context.
 * @return array<string, string>
 */
function messcut_pll_post_types( array $post_types, bool $is_settings ): array {
	unset( $is_settings );

	$translatable = array(
		'page'        => 'page',
		'case_study'  => 'case_study',
		'service'     => 'service',
		'article'     => 'article',
	);

	return array_merge( $post_types, $translatable );
}
add_filter( 'pll_get_post_types', 'messcut_pll_post_types', 10, 2 );

/**
 * Register taxonomies for Polylang translation.
 *
 * @param array<string, string> $taxonomies Taxonomies.
 * @param bool                  $is_settings Settings context.
 * @return array<string, string>
 */
function messcut_pll_taxonomies( array $taxonomies, bool $is_settings ): array {
	unset( $is_settings );
	$taxonomies['article_type'] = 'article_type';
	return $taxonomies;
}
add_filter( 'pll_get_taxonomies', 'messcut_pll_taxonomies', 10, 2 );

/**
 * Register translatable theme strings for Polylang.
 */
function messcut_pll_register_strings(): void {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}

	$strings = array(
		'footer_tagline'     => messcut_get_option( 'footer_tagline', '' ),
		'cta_discuss_label'  => messcut_get_option( 'cta_discuss_label', '' ),
		'cta_consult_label'  => messcut_get_option( 'cta_consult_label', '' ),
		'home_hero_title'    => messcut_get_option( 'home_hero_title', '' ),
		'home_hero_subtitle' => messcut_get_option( 'home_hero_subtitle', '' ),
		'audience_text'      => messcut_get_option( 'audience_text', '' ),
		'home_faq_title'     => messcut_get_option( 'home_faq_title', '' ),
	);

	foreach ( $strings as $name => $string ) {
		if ( '' !== (string) $string ) {
			pll_register_string( $name, (string) $string, 'messcut', in_array( $name, array( 'home_hero_subtitle', 'audience_text' ), true ) );
		}
	}
}
add_action( 'admin_init', 'messcut_pll_register_strings' );

/**
 * Translate a Polylang-registered string for the current language.
 *
 * @param string $string Default (Ukrainian) string.
 * @param string $name   Registered string name.
 */
function messcut_pll_string( string $string, string $name = '' ): string {
	if ( ! function_exists( 'pll__' ) || '' === $string ) {
		return $string;
	}

	if ( '' !== $name && function_exists( 'pll_translate_string' ) && messcut_is_english() ) {
		$translated = pll_translate_string( $string, 'en' );
		if ( is_string( $translated ) && '' !== $translated ) {
			return $translated;
		}
	}

	$translated = pll__( $string );
	return is_string( $translated ) && '' !== $translated ? $translated : $string;
}

/**
 * Fallback language link when no translation exists.
 *
 * @param string|null $url  Language URL.
 * @param string      $slug Language slug.
 */
function messcut_pll_language_link( ?string $url, string $slug ): ?string {
	if ( null !== $url && '' !== $url ) {
		return $url;
	}

	if ( ! function_exists( 'pll_home_url' ) ) {
		return $url;
	}

	return pll_home_url( $slug );
}
add_filter( 'pll_the_language_link', 'messcut_pll_language_link', 10, 2 );

/**
 * Create UK + EN languages if Polylang is active but not configured.
 */
function messcut_polylang_maybe_create_languages(): void {
	if ( ! function_exists( 'PLL' ) || ! is_admin() ) {
		return;
	}

	$model = PLL()->model;
	if ( ! $model ) {
		return;
	}

	if ( ! $model->get_language( 'uk' ) ) {
		$model->add_language(
			array(
				'name'   => 'Українська',
				'slug'   => 'uk',
				'locale' => 'uk',
				'rtl'    => 0,
				'flag'   => 'ua',
			)
		);
	}

	if ( ! $model->get_language( 'en' ) ) {
		$model->add_language(
			array(
				'name'   => 'English',
				'slug'   => 'en',
				'locale' => 'en_US',
				'rtl'    => 0,
				'flag'   => 'us',
			)
		);
	}

	$options = get_option( 'polylang' );
	if ( ! is_array( $options ) ) {
		return;
	}

	$changed = false;

	if ( empty( $options['default_lang'] ) ) {
		$options['default_lang'] = 'uk';
		$changed                 = true;
	}

	if ( ! isset( $options['hide_default'] ) || ! $options['hide_default'] ) {
		$options['hide_default'] = 1;
		$changed                 = true;
	}

	if ( ! isset( $options['browser'] ) || $options['browser'] ) {
		$options['browser'] = 0;
		$changed            = true;
	}

	$post_types = array( 'page', 'case_study', 'service', 'article' );
	if ( empty( $options['post_types'] ) || count( array_diff( $post_types, (array) $options['post_types'] ) ) > 0 ) {
		$options['post_types'] = array_values( array_unique( array_merge( (array) ( $options['post_types'] ?? array() ), $post_types ) ) );
		$changed               = true;
	}

	if ( $changed ) {
		update_option( 'polylang', $options );
	}
}
add_action( 'admin_init', 'messcut_polylang_maybe_create_languages', 5 );

/**
 * Admin notice when Polylang is missing.
 */
function messcut_polylang_admin_notice(): void {
	if ( messcut_is_polylang_active() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%s</p></div>',
		esc_html__( 'Тема Messcut: для мультимовності (UK + EN) встановіть і активуйте плагін Polylang.', 'messcut' )
	);
}
add_action( 'admin_notices', 'messcut_polylang_admin_notice' );

/**
 * Whether required Polylang languages exist.
 *
 * @param array<int, string> $slugs Language slugs.
 */
function messcut_polylang_has_languages( array $slugs ): bool {
	if ( ! function_exists( 'pll_languages_list' ) ) {
		return false;
	}

	$available = pll_languages_list( array( 'fields' => 'slug' ) );
	if ( ! is_array( $available ) ) {
		return false;
	}

	foreach ( $slugs as $slug ) {
		if ( ! in_array( $slug, $available, true ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Assign default Ukrainian language to posts without one.
 *
 * @param array<int, string> $post_types Post types.
 */
function messcut_assign_uk_language( array $post_types ): void {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_get_post_language' ) ) {
		return;
	}

	foreach ( $post_types as $post_type ) {
		$posts = get_posts(
			array(
				'post_type'      => $post_type,
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'fields'         => 'ids',
			)
		);

		foreach ( $posts as $post_id ) {
			if ( ! pll_get_post_language( (int) $post_id ) ) {
				pll_set_post_language( (int) $post_id, 'uk' );
			}
		}
	}
}

/**
 * Short display label for a Polylang language slug (e.g. uk → UA).
 */
function messcut_language_code_label( string $slug ): string {
	$labels = array(
		'uk' => 'UA',
		'en' => 'EN',
	);

	return $labels[ $slug ] ?? strtoupper( $slug );
}

/**
 * Render language switcher markup.
 *
 * @param array<string, mixed> $args Optional: variant (default|compact).
 */
function messcut_render_language_switcher( array $args = array() ): void {
	get_template_part( 'template-parts/header/language-switcher', null, $args );
}
