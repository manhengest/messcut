<?php
/**
 * Front page template.
 *
 * @package Messcut
 */

get_header();

get_template_part( 'template-parts/sections/hero', null, array(
	'title'     => __( 'Бренд-стратегія та науковий маркетинг', 'messcut' ),
	'subtitle'  => __( 'Будуємо маркетингові системи та допомагаємо бізнесу масштабуватися на основі досліджень', 'messcut' ),
	'cta_label' => messcut_cta_label( 'discuss' ),
	'partners'  => true,
) );

messcut_render_stats( array(
	'title' => __( 'Чому нас обирають?', 'messcut' ),
) );

messcut_render_path();

get_template_part( 'template-parts/sections/cta' );

get_template_part( 'template-parts/sections/cases-grid', null, array(
	'limit'     => 6,
	'show_more' => true,
	'on_dark'   => true,
) );

messcut_render_faq( array( 'source' => 'home' ) );

messcut_render_insights_tiles();

get_footer();
