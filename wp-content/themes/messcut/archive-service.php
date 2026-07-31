<?php
/**
 * Services archive.
 *
 * @package Messcut
 */

get_header();

get_template_part( 'template-parts/sections/hero', null, array(
	'title'    => __( 'Послуги', 'messcut' ),
	'subtitle' => __( 'Комплексні рішення для побудови та розвитку брендів', 'messcut' ),
) );

get_template_part( 'template-parts/sections/services-grid' );
get_template_part( 'template-parts/sections/cta' );

get_footer();
