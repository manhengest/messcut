<?php
/**
 * Combined path section — funnel + service cards + audience + ticker.
 * Figma 131:104 (cards) + 131:283 (active card) + 131:133 / 131:148 (audience + running line).
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pains    = messcut_get_service_pains();
$services = messcut_get_services_query( 2 );
$ticker   = messcut_get_ticker_items();

if ( ! $services->have_posts() ) {
	wp_reset_postdata();
	return;
}

$title          = $args['title'] ?? __( 'Від запиту до рішення', 'messcut' );
$audience_title = $args['audience_title'] ?? __( 'Хто нас обирає', 'messcut' );
$audience_text  = $args['audience_text'] ?? messcut_get_localized_option(
	'audience_text',
	__(
		'Підприємці, які хочуть розвивати бренд системно, менше ризикувати й приймати рішення на основі досліджень, даних і наукових принципів',
		'messcut'
	)
);

$service_map = array();
$index       = 0;
while ( $services->have_posts() ) {
	$services->the_post();
	$service_map[ get_the_ID() ] = 'service-' . $index;
	++$index;
}
wp_reset_postdata();

$mark_path  = MESSCUT_DIR . '/assets/img/path/mark.png';
$arrow_path = MESSCUT_DIR . '/assets/img/path/card-arrow.svg';
$mark_url   = is_readable( $mark_path ) ? MESSCUT_URI . '/assets/img/path/mark.png?v=' . (string) filemtime( $mark_path ) : '';
$arrow_url  = is_readable( $arrow_path ) ? MESSCUT_URI . '/assets/img/path/card-arrow.svg?v=' . (string) filemtime( $arrow_path ) : '';
?>
<section class="section path" data-pain-funnel>
	<div class="container">
		<h2 class="path__title"><?php echo esc_html( $title ); ?></h2>
	</div>

	<div class="path__stage">
		<div class="path__vessel" data-pain-vessel>
			<div class="path__mesh" aria-hidden="true">
				<span class="path__mesh-fill"></span>
			</div>
			<div class="path__pills" data-pain-pills>
				<?php foreach ( $pains as $pain ) : ?>
					<?php
					$service_id = (int) ( $pain['service_id'] ?? 0 );
					$target     = $service_map[ $service_id ] ?? '';
					if ( '' === $target ) {
						continue;
					}
					?>
					<button
						type="button"
						class="path__pill"
						data-pain-pill
						data-pain-target="<?php echo esc_attr( $target ); ?>"
						aria-pressed="false"
					>
						<?php echo esc_html( $pain['label'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="path__cards container" data-accordion>
			<?php
			$services->rewind_posts();
			$num = 0;
			while ( $services->have_posts() ) :
				$services->the_post();
				$slug        = 'service-' . $num;
				$description = messcut_get_service_card_description();
				if ( $description ) {
					$description = wp_trim_words( $description, 28, '…' );
				}
				?>
				<details
					class="path-card"
					data-pain-service="<?php echo esc_attr( $slug ); ?>"
				>
					<summary class="path-card__header">
						<h3 class="path-card__title"><?php the_title(); ?></h3>
						<span class="path-card__toggle" aria-hidden="true">+</span>
					</summary>
					<?php if ( $description ) : ?>
						<div class="path-card__panel">
							<p class="path-card__excerpt"><?php echo esc_html( $description ); ?></p>
							<a class="path-card__link" href="<?php the_permalink(); ?>">
								<?php esc_html_e( 'Детальніше', 'messcut' ); ?>
								<?php if ( $arrow_url ) : ?>
									<img
										class="path-card__arrow"
										src="<?php echo esc_url( $arrow_url ); ?>"
										alt=""
										width="10"
										height="10"
										decoding="async"
									>
								<?php endif; ?>
							</a>
						</div>
					<?php endif; ?>
				</details>
				<?php
				++$num;
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>

	<div class="path__audience container">
		<?php messcut_render_consult_cta(); ?>

		<div class="path__audience-copy">
			<h2 class="path__audience-title"><?php echo esc_html( $audience_title ); ?></h2>
			<?php if ( $audience_text ) : ?>
				<p class="path__audience-text"><?php echo esc_html( $audience_text ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( ! empty( $ticker ) ) : ?>
		<div class="path__ticker" data-marquee-root>
			<div class="path__ticker-wrap">
				<div class="path__ticker-track" data-marquee aria-hidden="true">
					<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
						<div class="path__ticker-group" data-marquee-group>
							<?php foreach ( $ticker as $item ) : ?>
								<span class="path__ticker-item"><?php echo esc_html( $item ); ?></span>
								<?php if ( $mark_url ) : ?>
									<img
										class="path__ticker-mark"
										src="<?php echo esc_url( $mark_url ); ?>"
										alt=""
										width="8"
										height="15"
										decoding="async"
										draggable="false"
									>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endfor; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
