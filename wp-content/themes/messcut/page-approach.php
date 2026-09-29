<?php
/**
 * Approach page template.
 *
 * Template Name: Досвід та підхід
 *
 * @package Messcut
 */

get_header();

get_template_part(
	'template-parts/sections/hero',
	null,
	array(
		'title'     => __( 'Розвиваємо бренди з науковим підходом', 'messcut' ),
		'subtitle' => __( 'ефективність в цифрах з чіткою стратегією', 'messcut' ),
		'cta_label' => __( 'Обговорити проєкт', 'messcut' ),
		'cta_arrow' => true,
	)
);

$approach = function_exists( 'get_field' ) ? (string) get_field( 'approach_content' ) : '';
$plain    = trim( wp_strip_all_tags( $approach ) );
if (
	str_starts_with( $plain, 'В основі нашої роботи' )
	|| str_starts_with( $plain, 'Our work is grounded in science-based marketing' )
) {
	$approach = '';
}

get_template_part(
	'template-parts/sections/approach-method',
	null,
	array(
		'content' => $approach,
	)
);

get_template_part( 'template-parts/sections/team' );

get_template_part( 'template-parts/sections/cta' );

get_template_part( 'template-parts/sections/services-grid' );

get_footer();
