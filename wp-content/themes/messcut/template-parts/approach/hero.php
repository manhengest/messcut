<?php
/**
 * Approach hero.
 *
 * @package Messcut
 */
?>
<section class="approach-hero">
	<h1><?php echo wp_kses( __( 'Розвиваємо бренди з <em>науковим</em> підходом', 'messcut' ), array( 'em' => array() ) ); ?></h1>
	<p class="approach-hero__lead"><?php esc_html_e( 'Ефективність у цифрах з чіткою стратегією', 'messcut' ); ?></p>
	<a class="cta" href="#lead"><?php esc_html_e( 'Обговорити проєкт', 'messcut' ); ?> <b>→</b></a>
	<div class="approach-hero__stage" aria-hidden="true">
		<div class="glass-chart">
			<div class="glass-chart__meta"><span><?php esc_html_e( 'Зростання бренду', 'messcut' ); ?></span><span><?php esc_html_e( '12 міс', 'messcut' ); ?></span></div>
			<svg viewBox="0 0 300 170" preserveAspectRatio="none" aria-hidden="true">
				<defs><linearGradient id="approach-fill" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#5fd3a6" stop-opacity=".55"/><stop offset="1" stop-color="#c7f2e1" stop-opacity="0"/></linearGradient></defs>
				<g stroke="rgba(0,0,0,.08)"><path d="M0 40H300M0 85H300M0 130H300"/></g>
				<path d="M0 150 C40 140 60 120 95 115 S150 95 185 70 S250 40 300 14 V170 H0Z" fill="url(#approach-fill)"/>
				<path d="M0 150 C40 140 60 120 95 115 S150 95 185 70 S250 40 300 14" fill="none" stroke="#0a0a0a" stroke-width="2.2" stroke-linecap="round"/>
				<circle cx="185" cy="70" r="11" fill="#2fbf8a" fill-opacity=".25"/><circle cx="185" cy="70" r="5" fill="#0a0a0a"/>
			</svg>
		</div>
		<div class="glass-chip glass-chip--one"><b>94%</b><span><?php esc_html_e( 'клієнтів радять нас колегам', 'messcut' ); ?></span></div>
		<div class="glass-chip glass-chip--two"><b>50+</b><span><?php esc_html_e( 'стратегічних співпраць', 'messcut' ); ?></span></div>
	</div>
</section>
