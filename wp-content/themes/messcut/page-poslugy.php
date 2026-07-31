<?php
/**
 * Services hub page.
 *
 * @package Messcut
 */

get_header();

get_template_part( 'template-parts/sections/hero', null, array(
	'title'    => __( 'Послуги', 'messcut' ),
	'subtitle' => __( 'ефективність в цифрах з чіткою стратегією', 'messcut' ),
) );

get_template_part( 'template-parts/sections/services-grid' );

messcut_render_stats();

get_template_part( 'template-parts/sections/cta', null, array(
	'title' => messcut_cta_label( 'discuss' ),
) );

get_footer();
