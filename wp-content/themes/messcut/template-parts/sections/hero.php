<?php
/**
 * Hero section.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = $args['title'] ?? messcut_get_localized_option( 'home_hero_title', __( 'Розвиваємо бренди з науковим підходом', 'messcut' ) );
$subtitle = $args['subtitle'] ?? messcut_get_localized_option( 'home_hero_subtitle', __( 'ефективність в цифрах з чіткою стратегією', 'messcut' ) );
$cta      = $args['cta_label'] ?? messcut_cta_label( 'discuss' );
?>
<section class="section hero hero--band">
	<div class="container">
		<h1 class="hero__title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $subtitle ) : ?>
			<p class="hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
		<?php endif; ?>
		<a class="button button--accent" href="#lead-form"><?php echo esc_html( $cta ); ?> →</a>
	</div>
</section>
