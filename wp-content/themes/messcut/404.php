<?php
/**
 * 404.
 *
 * @package Messcut
 */

get_header();
get_template_part( 'template-parts/listing/text-page', null, array(
	'title'   => __( 'Сторінку не знайдено', 'messcut' ),
	'content' => '<p>' . esc_html__( 'Перейдіть на головну або оберіть розділ у меню.', 'messcut' ) . '</p>',
) );
get_footer();
