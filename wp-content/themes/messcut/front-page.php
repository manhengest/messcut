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
) );
?>
<div class="chapter-proof">
	<?php
	messcut_render_stats( array(
		'title' => __( 'Чому бізнес обирає Messcut?', 'messcut' ),
	) );

	messcut_render_partner_logos();
	?>
</div>
<?php

messcut_render_pain_funnel();

get_template_part( 'template-parts/sections/services-grid', null, array(
	'title' => __( 'Послуги стратегічного маркетингу', 'messcut' ),
) );

get_template_part( 'template-parts/sections/audience' );

get_template_part( 'template-parts/sections/cta' );

get_template_part( 'template-parts/sections/cases-grid', null, array(
	'limit'     => 6,
	'show_more' => true,
) );

messcut_render_insights_tiles();

messcut_render_faq( array( 'source' => 'home' ) );

get_footer();
