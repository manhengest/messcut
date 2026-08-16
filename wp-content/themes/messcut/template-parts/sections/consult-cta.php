<?php
/**
 * Compact consultation CTA — avatar, copy, mint arrow to the lead form.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = $args['title'] ?? __( 'Потрібна консультація?', 'messcut' );
$text   = $args['text'] ?? __( 'Обговоримо ваш проєкт та знайдемо найкраще рішення.', 'messcut' );
$avatar = $args['avatar'] ?? messcut_consult_cta_avatar_url();
?>
<a class="consult-cta" href="#lead-form">
	<?php if ( $avatar ) : ?>
		<img
			class="consult-cta__avatar"
			src="<?php echo esc_url( $avatar ); ?>"
			alt=""
			width="320"
			height="320"
			loading="lazy"
			decoding="async"
		>
	<?php endif; ?>
	<span class="consult-cta__copy">
		<span class="consult-cta__title"><?php echo esc_html( $title ); ?></span>
		<?php if ( $text ) : ?>
			<span class="consult-cta__text"><?php echo esc_html( $text ); ?></span>
		<?php endif; ?>
	</span>
	<span class="consult-cta__action" aria-hidden="true">
		<svg class="consult-cta__arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/>
		</svg>
	</span>
</a>
