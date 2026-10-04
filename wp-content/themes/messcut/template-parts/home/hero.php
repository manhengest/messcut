<?php
/**
 * Home hero.
 *
 * @package Messcut
 */
?>
<section class="home-hero">
	<h1><?php echo wp_kses( __( 'Наводимо <em>фокус</em>', 'messcut' ), array( 'em' => array() ) ); ?></h1>
	<p class="home-hero__lead"><?php esc_html_e( 'Бренд-стратегія, маркетинг і креатив – системно та на основі досліджень', 'messcut' ); ?></p>
	<a class="cta" href="#lead"><?php esc_html_e( 'Отримати план розвитку', 'messcut' ); ?> <b>→</b></a>
	<div class="home-hero__stage" aria-hidden="true"><canvas id="home-canvas"></canvas></div>
</section>
