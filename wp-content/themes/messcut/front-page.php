<?php
/**
 * Front page.
 *
 * @package Messcut
 */

get_header();
get_template_part( 'template-parts/home/hero' );
get_template_part( 'template-parts/components/brands-marquee' );
get_template_part( 'template-parts/home/stats' );
get_template_part( 'template-parts/home/difference' );
get_template_part( 'template-parts/home/path' );
get_template_part( 'template-parts/home/lead' );
get_template_part( 'template-parts/home/audience' );
get_template_part( 'template-parts/home/cases' );
get_template_part( 'template-parts/home/faq' );
get_template_part( 'template-parts/home/insights' );
get_footer();
