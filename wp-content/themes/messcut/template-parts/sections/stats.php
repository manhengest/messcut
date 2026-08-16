<?php
/**
 * Stats section — 2-column tile grid on dark surface.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = $args['stats'] ?? array();
$title = $args['title'] ?? __( 'Чому бізнес обирає Messcut?', 'messcut' );

$grid_stats = array();
$hints      = array();

foreach ( $stats as $stat ) {
	$value = trim( (string) ( $stat['value'] ?? '' ) );
	$label = trim( (string) ( $stat['label'] ?? '' ) );

	if ( '' === $label && '' === $value ) {
		continue;
	}

	if ( '' === $value ) {
		$hints[] = $stat;
	} else {
		$grid_stats[] = $stat;
	}
}

if ( empty( $grid_stats ) && empty( $hints ) ) {
	return;
}
?>
<section class="section stats surface--gradient-dark">
	<div class="container">
		<?php if ( $title ) : ?>
			<h2 class="stats__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( ! empty( $grid_stats ) ) : ?>
			<div class="stats__grid">
				<?php foreach ( $grid_stats as $stat ) : ?>
					<?php
					$value = trim( (string) ( $stat['value'] ?? '' ) );
					$label = trim( (string) ( $stat['label'] ?? '' ) );
					?>
					<div class="stats__tile">
						<?php if ( $value ) : ?>
							<strong class="stats__value"><?php echo esc_html( $value ); ?></strong>
						<?php endif; ?>
						<?php if ( $label ) : ?>
							<span class="stats__label"><?php echo esc_html( $label ); ?></span>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<?php foreach ( $hints as $hint ) : ?>
			<?php
			$hint_text = trim( (string) ( $hint['label'] ?? '' ) );
			if ( '' === $hint_text ) {
				continue;
			}
			?>
			<p class="stats__hint"><?php echo esc_html( $hint_text ); ?></p>
		<?php endforeach; ?>
		<?php
		get_template_part(
			'template-parts/sections/agency-comparison',
			null,
			array( 'embedded' => true )
		);
		?>
	</div>
</section>
