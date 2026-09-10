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
$text  = $args['text'] ?? __( 'Найпоширеніші запитання про бренд-стратегію та маркетинг', 'messcut' );

if ( empty( $items ) ) {
	return;
}

$folder = get_template_directory_uri() . '/assets/img/faq/folder.svg';
?>
<section class="section faq" id="faq">
	<div class="container">
		<div class="faq__panel">
			<img
				class="faq__folder"
				src="<?php echo esc_url( $folder ); ?>"
				alt=""
				width="327"
				height="344"
			>
			<div class="faq__header">
				<h2 class="faq__title"><?php echo esc_html( $title ); ?></h2>
				<?php if ( $text ) : ?>
					<p class="faq__intro"><?php echo esc_html( $text ); ?></p>
				<?php endif; ?>
			</div>
			<div class="faq__list" data-accordion>
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
	</div>
</section>
