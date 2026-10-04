<?php
/**
 * Custom post types.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register MESSCUT post types.
 */
function messcut_register_post_types(): void {
	$post_types = array(
		'case_study' => array(
			'labels' => array(
				'name'          => __( 'Кейси', 'messcut' ),
				'singular_name' => __( 'Кейс', 'messcut' ),
				'add_new_item'  => __( 'Додати кейс', 'messcut' ),
				'edit_item'     => __( 'Редагувати кейс', 'messcut' ),
			),
			'rewrite' => array( 'slug' => 'cases', 'with_front' => false ),
			'menu_icon' => 'dashicons-portfolio',
		),
		'service' => array(
			'labels' => array(
				'name'          => __( 'Послуги', 'messcut' ),
				'singular_name' => __( 'Послуга', 'messcut' ),
				'add_new_item'  => __( 'Додати послугу', 'messcut' ),
				'edit_item'     => __( 'Редагувати послугу', 'messcut' ),
			),
			'rewrite'            => array( 'slug' => 'services', 'with_front' => false ),
			'menu_icon'          => 'dashicons-admin-tools',
			'public'             => true,
			'publicly_queryable' => true,
			'has_archive'        => false,
		),
		'article' => array(
			'labels' => array(
				'name'          => __( 'Статті', 'messcut' ),
				'singular_name' => __( 'Стаття', 'messcut' ),
				'add_new_item'  => __( 'Додати статтю', 'messcut' ),
				'edit_item'     => __( 'Редагувати статтю', 'messcut' ),
			),
			'rewrite' => array( 'slug' => 'articles', 'with_front' => false ),
			'menu_icon' => 'dashicons-media-text',
		),
		'lead' => array(
			'labels' => array(
				'name'          => __( 'Заявки', 'messcut' ),
				'singular_name' => __( 'Заявка', 'messcut' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => 'edit.php?post_type=case_study',
			'menu_icon'    => 'dashicons-email',
			'supports'     => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		),
	);

	foreach ( $post_types as $slug => $config ) {
		$is_lead = 'lead' === $slug;

		register_post_type( $slug, array(
			'labels'              => $config['labels'],
			'public'              => $config['public'] ?? ! $is_lead,
			'publicly_queryable'  => $config['publicly_queryable'] ?? ! $is_lead,
			'show_ui'             => true,
			'show_in_menu'        => $is_lead ? $config['show_in_menu'] : true,
			'show_in_rest'        => ! $is_lead,
			'has_archive'         => $config['has_archive'] ?? ! $is_lead,
			'rewrite'             => $config['rewrite'] ?? false,
			'menu_icon'           => $config['menu_icon'] ?? 'dashicons-admin-post',
			'supports'            => $config['supports'] ?? array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
			'menu_position'       => 5,
		) );
	}
}
add_action( 'init', 'messcut_register_post_types' );

/**
 * Old service URLs land on the services hub anchor.
 */
function messcut_redirect_service_urls(): void {
	if ( ! is_singular( 'service' ) ) {
		return;
	}

	$slug   = (string) get_post_field( 'post_name', get_queried_object_id() );
	$target = messcut_page_url( 'poslugy' ) . '#svc-' . $slug;
	wp_redirect( $target, 301 );
	exit;
}
add_action( 'template_redirect', 'messcut_redirect_service_urls' );

/**
 * Register article type taxonomy for Insights filtering.
 */
function messcut_register_taxonomies(): void {
	register_taxonomy(
		'article_type',
		'article',
		array(
			'labels'            => array(
				'name'          => __( 'Типи публікацій', 'messcut' ),
				'singular_name' => __( 'Тип публікації', 'messcut' ),
			),
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'article-type', 'with_front' => false ),
			'show_admin_column' => true,
		)
	);
}
add_action( 'init', 'messcut_register_taxonomies' );
