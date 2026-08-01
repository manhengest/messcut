<?php
/**
 * FAQ accordion section.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = $args['items'] ?? array();
$title = $args['title'] ?? __( 'FAQ', 'messcut' );

if ( empty( $items ) ) {
	return;
}
?>
<section class="section faq" id="faq">
	<div class="container container--narrow">
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<div class="faq__list" data-faq-accordion>
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$question = trim( (string) ( $item['question'] ?? '' ) );
				$answer   = (string) ( $item['answer'] ?? '' );
				if ( '' === $question ) {
					continue;
				}
				$item_id = 'faq-item-' . (int) $index;
				?>
				<details class="faq__item">
					<summary class="faq__question" id="<?php echo esc_attr( $item_id ); ?>-summary">
						<span class="faq__question-text"><?php echo esc_html( $question ); ?></span>
						<span class="faq__icon" aria-hidden="true"></span>
					</summary>
					<?php if ( '' !== trim( wp_strip_all_tags( $answer ) ) ) : ?>
						<div class="faq__answer entry-content" id="<?php echo esc_attr( $item_id ); ?>-answer" role="region" aria-labelledby="<?php echo esc_attr( $item_id ); ?>-summary">
							<?php echo wp_kses_post( $answer ); ?>
						</div>
					<?php endif; ?>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
