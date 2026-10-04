<?php
/**
 * Article.
 *
 * @package Messcut
 */

get_header();
while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/listing/text-page' );
}
get_footer();
