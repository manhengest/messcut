<?php
/**
 * Pain funnel — chaos to structure.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pains    = messcut_get_service_pains();
$services = messcut_get_services_query( 4 );

if ( empty( $pains ) || ! $services->have_posts() ) {
	wp_reset_postdata();
	return;
}

$service_map = array();
$index       = 0;
while ( $services->have_posts() ) {
	$services->the_post();
	$service_map[ get_the_ID() ] = array(
		'index' => $index,
		'title' => get_the_title(),
		'slug'  => 'service-' . $index,
	);
	++$index;
}
wp_reset_postdata();
?>
<section class="section pain-funnel surface--gradient-light" data-pain-funnel>
	<div class="container">
		<p class="section__eyebrow"><?php esc_html_e( 'З чим звертаються', 'messcut' ); ?></p>
		<h2 class="pain-funnel__title"><?php esc_html_e( 'Від запиту до послуги', 'messcut' ); ?></h2>
		<p class="pain-funnel__intro">
			<?php
			esc_html_e(
				'Спершу розкриваємо болі, з якими приходять клієнти: невизначене позиціонування, хаотичний маркетинг, відсутність структури. Кожен такий запит веде до конкретної послуги. Натисніть на свій — знизу підсвітиться напрям, на який можна перейти одразу.',
				'messcut'
			);
			?>
		</p>
	</div>

	<div class="pain-funnel__stage">
		<div class="pain-funnel__vessel" data-pain-vessel>
			<div class="pain-funnel__mesh" aria-hidden="true">
				<span class="pain-funnel__mesh-fill"></span>
			</div>
			<div class="pain-funnel__pills" data-pain-pills>
				<?php foreach ( $pains as $pain ) : ?>
					<?php
					$service_id = (int) ( $pain['service_id'] ?? 0 );
					$target     = $service_map[ $service_id ]['slug'] ?? 'service-0';
					?>
					<button
						type="button"
						class="pain-funnel__pill"
						data-pain-pill
						data-pain-target="<?php echo esc_attr( $target ); ?>"
						aria-pressed="false"
					>
						<?php echo esc_html( $pain['label'] ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="pain-funnel__services" data-pain-services>
			<?php
			$services->rewind_posts();
			$num = 0;
			while ( $services->have_posts() ) :
				$services->the_post();
				$slug = 'service-' . $num;
				?>
				<article
					class="pain-funnel__service"
					id="<?php echo esc_attr( $slug ); ?>"
					data-pain-service="<?php echo esc_attr( $slug ); ?>"
				>
					<span class="pain-funnel__service-num"><?php echo esc_html( sprintf( '%02d', $num + 1 ) ); ?></span>
					<h3 class="pain-funnel__service-title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
				</article>
				<?php
				++$num;
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
