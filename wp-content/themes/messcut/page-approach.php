<?php
/**
 * Approach page.
 *
 * Template Name: Досвід та підхід
 *
 * @package Messcut
 */

add_filter(
	'body_class',
	static function ( array $classes ): array {
		$classes[] = 'page-approach';
		return $classes;
	}
);

get_header();
get_template_part( 'template-parts/approach/hero' );
get_template_part( 'template-parts/approach/principles' );
get_template_part( 'template-parts/approach/team' );
get_template_part( 'template-parts/approach/services' );
get_template_part( 'template-parts/approach/cases' );
get_footer();
