<?php
/**
 * One service card.
 *
 * @package Messcut
 */

$service = isset( $args['service'] ) && is_array( $args['service'] ) ? $args['service'] : array();
if ( ! $service ) {
	return;
}
$locked = ! empty( $service['locked'] );
?>
<article class="service-card<?php echo $locked ? ' is-locked' : ''; ?>" id="<?php echo esc_attr( $service['anchor'] ); ?>">
	<span class="service-card__eyebrow"><?php echo esc_html( $service['eyebrow'] ); ?></span>
	<h3><?php echo esc_html( $service['title'] ); ?></h3>
	<p class="service-card__teaser"><?php echo esc_html( $service['teaser'] ); ?></p>
	<?php if ( ! empty( $service['proof'] ) ) : ?>
		<a class="service-card__proof" href="<?php echo esc_url( $service['proof']['url'] ); ?>">
			<b><?php echo esc_html( $service['proof']['stat'] ); ?></b>
			<span><?php echo esc_html( $service['proof']['label'] ); ?></span>
		</a>
	<?php endif; ?>
	<?php if ( ! empty( $service['bullets'] ) ) : ?>
		<ul>
			<?php foreach ( $service['bullets'] as $bullet ) : ?>
				<li><?php echo esc_html( $bullet ); ?></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php if ( ! empty( $service['steps'] ) ) : ?>
		<button class="service-card__more" type="button" aria-expanded="false" data-details><span><?php esc_html_e( 'Детальніше', 'messcut' ); ?></span><i>↓</i></button>
		<div class="service-card__details">
			<ol>
				<?php foreach ( $service['steps'] as $step_index => $step ) : ?>
					<li>
						<b><span><?php echo esc_html( sprintf( '%02d', $step_index + 1 ) ); ?></span> <?php echo esc_html( $step['title'] ); ?></b>
						<p><?php echo esc_html( $step['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	<?php endif; ?>
	<?php if ( $locked ) : ?>
		<span class="button button--outline"><?php esc_html_e( 'Доступно після бренд-стратегії', 'messcut' ); ?></span>
	<?php else : ?>
		<a class="cta" href="#lead-form"><?php echo esc_html( $service['cta'] ); ?> <b>→</b></a>
	<?php endif; ?>
</article>
