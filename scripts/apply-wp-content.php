<?php
/**
 * Apply MESSCUT content JSON on a WordPress install (production).
 *
 * Usage: php apply-wp-content.php /path/to/content.json
 *
 * Does not touch leads, users, uploads, or media fields.
 */

$json_path = $argv[1] ?? '';
if ( '' === $json_path || ! is_readable( $json_path ) ) {
	fwrite( STDERR, "usage: php apply-wp-content.php content.json\n" );
	exit( 1 );
}

$wp_load = getenv( 'WP_LOAD' ) ?: dirname( __DIR__ ) . '/wp-load.php';
if ( ! is_readable( $wp_load ) ) {
	fwrite( STDERR, "wp-load.php not found at {$wp_load}\n" );
	exit( 1 );
}

require $wp_load;

const MESSCUT_SYNC_SKIP_OPTION_KEYS = array(
	'home_hero_video',
	'home_hero_poster',
	'partner_brands',
	'consult_cta_avatar',
	'form_recipient_email',
);

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

$payload = json_decode( (string) file_get_contents( $json_path ), true );
if ( ! is_array( $payload ) || empty( $payload['posts'] ) ) {
	fwrite( STDERR, "invalid content JSON\n" );
	exit( 1 );
}

/**
 * @param string $type Type.
 * @param string $slug Slug.
 * @param string $lang Lang.
 * @return WP_Post|null
 */
function messcut_sync_find_post( string $type, string $slug, string $lang ) {
	$posts = get_posts(
		array(
			'post_type'      => $type,
			'name'           => $slug,
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => -1,
			'lang'           => '',
		)
	);
	if ( ! $posts ) {
		return null;
	}
	if ( '' === $lang || ! function_exists( 'pll_get_post_language' ) ) {
		return $posts[0];
	}
	foreach ( $posts as $post ) {
		if ( (string) pll_get_post_language( $post->ID ) === $lang ) {
			return $post;
		}
	}
	return null;
}

/**
 * @param array<string, string> $ref Ref.
 * @return int
 */
function messcut_sync_resolve_post_ref( array $ref ): int {
	if ( ( $ref['__ref'] ?? '' ) !== 'post' ) {
		return 0;
	}
	$found = messcut_sync_find_post(
		(string) ( $ref['post_type'] ?? '' ),
		(string) ( $ref['post_name'] ?? '' ),
		(string) ( $ref['lang'] ?? '' )
	);
	return $found ? (int) $found->ID : 0;
}

/**
 * @param array<string, string> $ref Ref.
 * @return int
 */
function messcut_sync_resolve_term_ref( array $ref ): int {
	if ( ( $ref['__ref'] ?? '' ) !== 'term' ) {
		return 0;
	}
	$term = get_term_by( 'slug', (string) ( $ref['slug'] ?? '' ), (string) ( $ref['taxonomy'] ?? '' ) );
	return ( $term && ! is_wp_error( $term ) ) ? (int) $term->term_id : 0;
}

/**
 * @param mixed $value Value.
 * @return mixed
 */
function messcut_sync_resolve( mixed $value ): mixed {
	if ( ! is_array( $value ) ) {
		return $value;
	}
	if ( isset( $value['__ref'] ) ) {
		if ( 'post' === $value['__ref'] ) {
			$id = messcut_sync_resolve_post_ref( $value );
			return $id > 0 ? $id : null;
		}
		if ( 'term' === $value['__ref'] ) {
			$id = messcut_sync_resolve_term_ref( $value );
			return $id > 0 ? $id : null;
		}
	}
	$out = array();
	foreach ( $value as $key => $item ) {
		$resolved = messcut_sync_resolve( $item );
		if ( null !== $resolved ) {
			$out[ $key ] = $resolved;
		}
	}
	return $out;
}

/**
 * Link UK/EN (and any other) posts that belong to the same translation group.
 *
 * @param array<string, mixed> $row Row.
 */
function messcut_sync_link_translations( array $row ): void {
	if ( ! function_exists( 'pll_save_post_translations' ) ) {
		return;
	}

	$type = (string) ( $row['post_type'] ?? '' );
	$slug = (string) ( $row['post_name'] ?? '' );
	$lang = (string) ( $row['lang'] ?? '' );
	if ( '' === $type || '' === $slug ) {
		return;
	}

	$translations = array();
	$map          = is_array( $row['translations'] ?? null ) ? $row['translations'] : array();
	foreach ( $map as $tlang => $tslug ) {
		$found = messcut_sync_find_post( $type, (string) $tslug, (string) $tlang );
		if ( $found ) {
			$translations[ (string) $tlang ] = (int) $found->ID;
		}
	}

	$self = messcut_sync_find_post( $type, $slug, $lang );
	if ( $self && '' !== $lang ) {
		$translations[ $lang ] = (int) $self->ID;
	}

	$siblings = get_posts(
		array(
			'post_type'      => $type,
			'name'           => $slug,
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => -1,
			'lang'           => '',
		)
	);
	foreach ( $siblings as $sibling ) {
		$sibling_lang = function_exists( 'pll_get_post_language' )
			? (string) pll_get_post_language( $sibling->ID )
			: '';
		if ( '' !== $sibling_lang ) {
			$translations[ $sibling_lang ] = (int) $sibling->ID;
		}
	}

	if ( count( $translations ) < 2 ) {
		return;
	}

	pll_save_post_translations( $translations );
}

$changed = array();
$applied = 0;
$failed  = 0;

foreach ( $payload['posts'] as $row ) {
	$type   = (string) ( $row['post_type'] ?? '' );
	$slug   = (string) ( $row['post_name'] ?? '' );
	$lang   = (string) ( $row['lang'] ?? '' );
	$title  = (string) ( $row['post_title'] ?? '' );
	if ( '' === $type || '' === $slug ) {
		fwrite( STDERR, "failed: missing post_type or post_name\n" );
		++$failed;
		continue;
	}

	$existing = messcut_sync_find_post( $type, $slug, $lang );
	$postarr  = array(
		'post_type'    => $type,
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_excerpt' => (string) ( $row['post_excerpt'] ?? '' ),
		'post_content' => (string) ( $row['post_content'] ?? '' ),
		'menu_order'   => (int) ( $row['menu_order'] ?? 0 ),
	);

	$before_title = $existing ? $existing->post_title : '';
	if ( $existing ) {
		$postarr['ID'] = $existing->ID;
		$post_id       = wp_update_post( $postarr, true );
	} else {
		$postarr['post_status'] = 'publish';
		$post_id                = wp_insert_post( $postarr, true );
	}

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		$message = is_wp_error( $post_id ) ? $post_id->get_error_message() : 'unknown error';
		fwrite( STDERR, "failed {$type}/{$slug}: {$message}\n" );
		++$failed;
		continue;
	}

	if ( $lang && function_exists( 'pll_set_post_language' ) ) {
		pll_set_post_language( (int) $post_id, $lang );
	}

	$acf = messcut_sync_resolve( $row['acf'] ?? array() );
	if ( function_exists( 'update_field' ) && is_array( $acf ) ) {
		foreach ( $acf as $key => $value ) {
			update_field( $key, $value, (int) $post_id );
		}
	}

	++$applied;
	if ( $before_title !== $title ) {
		$changed[] = "{$type}/{$slug}: \"{$before_title}\" → \"{$title}\"";
	} else {
		$changed[] = "{$type}/{$slug}: updated fields";
	}
}

foreach ( $payload['posts'] as $row ) {
	messcut_sync_link_translations( $row );
}

if ( function_exists( 'update_field' ) && ! empty( $payload['options'] ) && is_array( $payload['options'] ) ) {
	$options = messcut_sync_resolve( $payload['options'] );
	foreach ( $options as $key => $value ) {
		if ( messcut_sync_is_skipped_option_key( (string) $key ) ) {
			continue;
		}
		update_field( $key, $value, 'option' );
	}
	$changed[] = 'acf options updated';
}

$version = (int) ( $payload['content_version'] ?? 0 );
if ( 0 === $failed && $version > 0 ) {
	update_option( 'messcut_content_version', $version, false );
	update_option( 'messcut_seeded', 1, false );
}

echo "Applied {$applied} posts.\n";
foreach ( $changed as $line ) {
	echo $line . "\n";
}

if ( $failed > 0 ) {
	fwrite( STDERR, "failed {$failed} posts.\n" );
	exit( 1 );
}
