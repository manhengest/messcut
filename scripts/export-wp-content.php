<?php
/**
 * Export MESSCUT posts + ACF text content for production sync.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const MESSCUT_SYNC_SKIP_PAGES = array( 'sample-page', 'privacy-policy' );
const MESSCUT_SYNC_SKIP_OPTION_KEYS = array(
	'home_hero_video',
	'home_hero_poster',
	'partner_brands',
	'consult_cta_avatar',
	'form_recipient_email',
);

/**
 * @param mixed $value Value.
 * @return mixed
 */
function messcut_sync_replace_local_urls( mixed $value ): mixed {
	if ( is_string( $value ) ) {
		return str_replace(
			array( 'http://localhost:3000', 'http://localhost:8080' ),
			'https://messcut.com',
			$value
		);
	}
	if ( is_array( $value ) ) {
		foreach ( $value as $key => $item ) {
			$value[ $key ] = messcut_sync_replace_local_urls( $item );
		}
	}
	return $value;
}

/**
 * @param mixed $value Value.
 * @return bool
 */
function messcut_sync_is_media( mixed $value ): bool {
	if ( ! is_array( $value ) ) {
		return false;
	}
	if ( isset( $value['ID'], $value['url'] ) && ( isset( $value['filename'] ) || isset( $value['mime_type'] ) || isset( $value['sizes'] ) ) ) {
		return true;
	}
	return false;
}

/**
 * @param WP_Post|int $post Post.
 * @return array<string, string>
 */
function messcut_sync_post_ref( $post ): array {
	$obj = $post instanceof WP_Post ? $post : get_post( (int) $post );
	if ( ! $obj ) {
		return array();
	}
	return array(
		'__ref'     => 'post',
		'post_type' => $obj->post_type,
		'post_name' => $obj->post_name,
		'lang'      => function_exists( 'pll_get_post_language' ) ? (string) pll_get_post_language( $obj->ID ) : '',
	);
}

/**
 * @param mixed $value Value.
 * @return mixed
 */
function messcut_sync_sanitize( mixed $value ): mixed {
	if ( $value instanceof WP_Post ) {
		return messcut_sync_post_ref( $value );
	}
	if ( $value instanceof WP_Term ) {
		return array(
			'__ref'     => 'term',
			'taxonomy'  => $value->taxonomy,
			'slug'      => $value->slug,
		);
	}
	if ( messcut_sync_is_media( $value ) ) {
		return null;
	}
	if ( is_array( $value ) ) {
		$out = array();
		foreach ( $value as $key => $item ) {
			if ( in_array( (string) $key, array( 'logo', 'image', 'photo', 'avatar', 'video', 'poster', 'file' ), true ) ) {
				continue;
			}
			$clean = messcut_sync_sanitize( $item );
			if ( null !== $clean ) {
				$out[ $key ] = $clean;
			}
		}
		return $out;
	}
	if ( is_int( $value ) && $value > 0 ) {
		$post = get_post( $value );
		if ( $post && in_array( $post->post_type, array( 'service', 'case_study', 'article', 'page' ), true ) ) {
			return messcut_sync_post_ref( $post );
		}
		$term = get_term( $value );
		if ( $term && ! is_wp_error( $term ) && 'article_type' === $term->taxonomy ) {
			return array(
				'__ref'    => 'term',
				'taxonomy' => $term->taxonomy,
				'slug'     => $term->slug,
			);
		}
	}
	return messcut_sync_replace_local_urls( $value );
}

/**
 * @param string $key Option key.
 * @return bool
 */
function messcut_sync_is_skipped_option_key( string $key ): bool {
	if ( in_array( $key, MESSCUT_SYNC_SKIP_OPTION_KEYS, true ) ) {
		return true;
	}
	if ( str_starts_with( $key, 'en_' ) ) {
		return in_array( substr( $key, 3 ), MESSCUT_SYNC_SKIP_OPTION_KEYS, true );
	}
	return false;
}

/**
 * Read an ACF option, including en_* keys that are not in the field group.
 *
 * @param string $key Option key.
 * @return mixed
 */
function messcut_sync_read_option_field( string $key ): mixed {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, 'option' );
		if ( null !== $value && false !== $value ) {
			return $value;
		}
	}
	$stored = get_option( 'options_' . $key, null );
	return ( false === $stored ) ? null : $stored;
}

$posts_out = array();
$types     = array( 'service', 'case_study', 'article', 'page' );

foreach ( $types as $type ) {
	$posts = get_posts(
		array(
			'post_type'      => $type,
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'lang'           => '',
		)
	);
	foreach ( $posts as $post ) {
		if ( 'page' === $type && in_array( $post->post_name, MESSCUT_SYNC_SKIP_PAGES, true ) ) {
			continue;
		}
		$acf = function_exists( 'get_fields' ) ? get_fields( $post->ID ) : array();
		$translations = array();
		if ( function_exists( 'pll_get_post_translations' ) ) {
			foreach ( pll_get_post_translations( $post->ID ) as $tlang => $tid ) {
				$tpost = get_post( (int) $tid );
				if ( $tpost && $tpost->post_name ) {
					$translations[ (string) $tlang ] = $tpost->post_name;
				}
			}
		}
		$posts_out[] = array(
			'post_type'    => $post->post_type,
			'post_name'    => $post->post_name,
			'post_status'  => $post->post_status,
			'post_title'   => $post->post_title,
			'post_excerpt' => $post->post_excerpt,
			'post_content' => messcut_sync_replace_local_urls( $post->post_content ),
			'menu_order'   => (int) $post->menu_order,
			'lang'         => function_exists( 'pll_get_post_language' ) ? (string) pll_get_post_language( $post->ID ) : '',
			'translations' => $translations,
			'acf'          => messcut_sync_sanitize( is_array( $acf ) ? $acf : array() ),
		);
	}
}

$options = array();
$raw     = ( function_exists( 'get_fields' ) ) ? get_fields( 'option' ) : array();
$raw     = is_array( $raw ) ? $raw : array();

foreach ( $raw as $key => $value ) {
	if ( messcut_sync_is_skipped_option_key( (string) $key ) ) {
		continue;
	}
	$options[ $key ] = messcut_sync_sanitize( $value );
}

foreach ( array_keys( $raw ) as $key ) {
	$key = (string) $key;
	if ( str_starts_with( $key, 'en_' ) || messcut_sync_is_skipped_option_key( $key ) ) {
		continue;
	}
	$en_key   = 'en_' . $key;
	$en_value = messcut_sync_read_option_field( $en_key );
	if ( null === $en_value || false === $en_value || '' === $en_value ) {
		continue;
	}
	$options[ $en_key ] = messcut_sync_sanitize( $en_value );
}

$payload = array(
	'exported_at'     => gmdate( 'c' ),
	'content_version' => defined( 'MESSCUT_CONTENT_VERSION' ) ? MESSCUT_CONTENT_VERSION : 0,
	'posts'           => $posts_out,
	'options'         => $options,
);

echo wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
