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

$title     = $args['title'] ?? __( 'Потрібна консультація?', 'messcut' );
$text      = $args['text'] ?? __( 'Обговоримо ваш проєкт та знайдемо найкраще рішення', 'messcut' );
$avatar    = $args['avatar'] ?? messcut_consult_cta_avatar_url();
$arrow     = MESSCUT_DIR . '/assets/img/path/cta-arrow.svg';
$arrow_url = is_readable( $arrow ) ? MESSCUT_URI . '/assets/img/path/cta-arrow.svg?v=' . (string) filemtime( $arrow ) : '';
?>
<a class="consult-cta" href="#lead-form">
	<?php if ( $avatar ) : ?>
		<img
			class="consult-cta__avatar"
			src="<?php echo esc_url( $avatar ); ?>"
			alt=""
			width="48"
			height="48"
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
	<?php if ( $arrow_url ) : ?>
		<img
			class="consult-cta__arrow"
			src="<?php echo esc_url( $arrow_url ); ?>"
			alt=""
			width="48"
			height="48"
			decoding="async"
		>
	<?php else : ?>
		<span class="consult-cta__action" aria-hidden="true">→</span>
	<?php endif; ?>
</a>
