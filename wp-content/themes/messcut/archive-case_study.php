<?php
/**
 * Case studies archive.
 *
 * @package Messcut
 */

get_header();

get_template_part( 'template-parts/sections/hero', null, array(
	'title'    => __( 'Кейси', 'messcut' ),
	'subtitle' => __( 'Поєднуємо маркетинг, доведений наукою, стратегічне мислення та глибоке розуміння споживача, щоб створювати бренди, які залишаються в пам\'яті та приносять бізнес-результат.', 'messcut' ),
) );

get_template_part( 'template-parts/sections/cases-grid' );

get_template_part( 'template-parts/sections/cta', null, array(
	'title' => __( 'Отримайте конструктивні рекомендації щодо подальшого розвитку вашого проєкту', 'messcut' ),
) );

get_template_part( 'template-parts/sections/approach-cta' );
get_template_part( 'template-parts/sections/insights-tiles' );

get_footer();
