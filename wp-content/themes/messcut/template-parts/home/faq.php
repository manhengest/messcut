<?php
/**
 * Home FAQ.
 *
 * @package Messcut
 */
?>
<section class="section home-faq">
	<div class="home-faq__grid">
		<div>
			<h2><?php esc_html_e( 'FAQ', 'messcut' ); ?></h2>
			<p><?php esc_html_e( 'Найпоширеніші запитання про бренд-стратегію та маркетинг', 'messcut' ); ?></p>
		</div>
		<?php get_template_part( 'template-parts/components/faq-list' ); ?>
	</div>
</section>
