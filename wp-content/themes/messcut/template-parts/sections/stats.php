<?php
/**
 * Stats section.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = $args['stats'] ?? array();
if ( empty( $stats ) ) {
	return;
}
?>
<section class="section stats">
	<div class="container">
		<h2 class="section__title"><?php esc_html_e( 'Що стоїть за нашою роботою', 'messcut' ); ?></h2>
		<ul class="stats__grid">
			<?php foreach ( $stats as $stat ) : ?>
				<li class="stats__item">
					<?php if ( ! empty( $stat['value'] ) ) : ?>
						<strong class="stats__value"><?php echo esc_html( $stat['value'] ); ?></strong>
						<span class="stats__label"><?php echo esc_html( $stat['label'] ?? '' ); ?></span>
					<?php else : ?>
						<span class="stats__label stats__label--solo"><?php echo esc_html( $stat['label'] ?? '' ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
