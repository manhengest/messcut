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
$title = $args['title'] ?? __( 'Що стоїть за нашою роботою', 'messcut' );
if ( empty( $stats ) ) {
	return;
}

$intro    = null;
$featured = null;
$orbit    = array();

foreach ( $stats as $stat ) {
	if ( empty( $stat['value'] ) ) {
		$intro = $stat;
		continue;
	}

	if ( null === $featured && str_contains( (string) $stat['value'], '%' ) ) {
		$featured = $stat;
		continue;
	}

	$orbit[] = $stat;
}

if ( null === $featured && ! empty( $orbit ) ) {
	$featured = array_shift( $orbit );
}

$tore_url = MESSCUT_URI . '/assets/img/tore.png';
?>
<section class="section stats">
	<div class="container">
		<div class="stats__layout">
			<header class="stats__header">
				<?php if ( $title ) : ?>
					<h2 class="stats__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $featured ) : ?>
					<div class="stats__item stats__item--featured">
						<strong class="stats__value"><?php echo esc_html( $featured['value'] ); ?></strong>
						<span class="stats__label"><?php echo esc_html( $featured['label'] ?? '' ); ?></span>
					</div>
				<?php endif; ?>
			</header>

			<div class="stats__visual" aria-hidden="true">
				<img
					class="stats__tore"
					src="<?php echo esc_url( $tore_url ); ?>"
					alt=""
					width="640"
					height="640"
					loading="lazy"
					decoding="async"
				/>
			</div>

			<?php if ( ! empty( $orbit[0] ) ) : ?>
				<div class="stats__item stats__item--tr">
					<strong class="stats__value"><?php echo esc_html( $orbit[0]['value'] ); ?></strong>
					<span class="stats__label"><?php echo esc_html( $orbit[0]['label'] ?? '' ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $orbit[1] ) ) : ?>
				<div class="stats__item stats__item--bl">
					<strong class="stats__value"><?php echo esc_html( $orbit[1]['value'] ); ?></strong>
					<span class="stats__label"><?php echo esc_html( $orbit[1]['label'] ?? '' ); ?></span>
				</div>
			<?php endif; ?>

			<?php if ( $intro ) : ?>
				<div class="stats__item stats__item--br">
					<p class="stats__narrative"><?php echo esc_html( $intro['label'] ?? '' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
