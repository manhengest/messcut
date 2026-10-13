<?php
/**
 * Services hero.
 *
 * @package Messcut
 */
?>
<section class="services-hero" id="intro">
	<div class="container">
	<p class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Головна', 'messcut' ); ?></a><span>/</span><span><?php esc_html_e( 'Послуги', 'messcut' ); ?></span></p>
	<h1><?php echo wp_kses( __( 'Наші <em>послуги</em>', 'messcut' ), array( 'em' => array() ) ); ?></h1>
	<p class="services-hero__lead"><?php esc_html_e( 'Два напрями, чотири послуги. Оберіть, з чого почати.', 'messcut' ); ?></p>
	</div>
</section>
