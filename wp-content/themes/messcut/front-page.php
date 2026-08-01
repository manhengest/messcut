<?php
/**
 * Front page template.
 *
 * @package Messcut
 */

get_header();

get_template_part( 'template-parts/sections/hero', null, array(
	'cta_label' => __( 'Обговорити ваш проєкт', 'messcut' ),
) );

messcut_render_stats( array(
	'title' => __( 'Чому бізнес обирає Messcut?', 'messcut' ),
) );

get_template_part( 'template-parts/sections/services-grid', null, array(
	'title' => __( 'Послуги стратегічного маркетингу', 'messcut' ),
) );

get_template_part( 'template-parts/sections/cta', null, array(
	'title' => __( 'Отримайте конструктивні рекомендації щодо подальшого розвитку вашого бізнесу', 'messcut' ),
) );

get_template_part( 'template-parts/sections/audience' );

get_template_part( 'template-parts/sections/cases-grid', null, array(
	'limit'      => 6,
	'show_more'  => true,
) );

messcut_render_insights_tiles();

messcut_render_faq( array( 'source' => 'home' ) );

get_footer();
