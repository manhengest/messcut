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
?>
<nav class="chapter-rail" data-rail=".approach-hero, .approach-principles, .approach-team, .approach-services, .approach-cases" aria-label="<?php esc_attr_e( 'Розділи сторінки', 'messcut' ); ?>"></nav>
<?php
get_template_part( 'template-parts/approach/hero' );
get_template_part( 'template-parts/approach/principles' );
get_template_part( 'template-parts/approach/team' );
get_template_part( 'template-parts/approach/services' );
get_template_part( 'template-parts/approach/cases' );
get_footer();
