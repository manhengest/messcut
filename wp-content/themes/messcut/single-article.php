<?php
/**
 * Article.
 *
 * @package Messcut
 */

get_header();
while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/article/single' );
}
get_footer();
