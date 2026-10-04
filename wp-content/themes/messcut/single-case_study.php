<?php
/**
 * Case study.
 *
 * @package Messcut
 */

get_header();
while ( have_posts() ) {
	the_post();
	messcut_render_case_study( get_the_ID() );
}
get_footer();
