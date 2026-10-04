<?php
/**
 * Index fallback.
 *
 * @package Messcut
 */

get_header();
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		get_template_part( 'template-parts/listing/text-page' );
	}
} else {
	get_template_part( 'template-parts/listing/text-page', null, array(
		'title'   => __( 'Нічого не знайдено', 'messcut' ),
		'content' => '',
	) );
}
get_footer();
