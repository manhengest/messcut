<?php
/**
 * Approach service links.
 *
 * @package Messcut
 */
?>
<section class="section approach-services">
	<div class="container">
	<h2><?php esc_html_e( 'Послуги', 'messcut' ); ?></h2>
	<a href="<?php echo esc_url( messcut_service_anchor( 'brand-strategy' ) ); ?>"><?php esc_html_e( 'Бренд-стратегія', 'messcut' ); ?> <?php messcut_render_link_arrow_icon(); ?></a>
	<a href="<?php echo esc_url( messcut_service_anchor( 'marketing-support' ) ); ?>"><?php esc_html_e( 'Маркетинг-супровід', 'messcut' ); ?> <?php messcut_render_link_arrow_icon(); ?></a>
	</div>
</section>
