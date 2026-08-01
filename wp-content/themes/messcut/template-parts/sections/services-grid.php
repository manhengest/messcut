<?php
/**
 * Services grid section.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = $args['title'] ?? __( 'Послуги', 'messcut' );
$query = messcut_get_services_query( 4 );
if ( ! $query->have_posts() ) {
	return;
}
?>
<section class="section services-grid">
	<div class="container">
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<div class="grid grid--services">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$description   = messcut_get_service_card_description();
				$needs_toggle  = mb_strlen( $description ) > 120;
				?>
				<article class="card card--service">
					<h3 class="card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<?php if ( $description ) : ?>
						<div
							class="card__excerpt<?php echo $needs_toggle ? ' card__excerpt--collapsible' : ''; ?>"
							<?php echo $needs_toggle ? ' data-service-excerpt' : ''; ?>
						>
							<p class="card__excerpt-text"><?php echo esc_html( $description ); ?></p>
							<a class="card__link" href="<?php the_permalink(); ?>">
								<?php esc_html_e( 'Перейти на сторінку послуги', 'messcut' ); ?>
							</a>
							<?php if ( $needs_toggle ) : ?>
								<button
									type="button"
									class="card__excerpt-toggle"
									data-service-excerpt-toggle
									aria-expanded="false"
								>
									<span class="card__excerpt-toggle-more"><?php esc_html_e( 'Більше', 'messcut' ); ?></span>
									<span class="card__excerpt-toggle-less" hidden><?php esc_html_e( 'Менше', 'messcut' ); ?></span>
								</button>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
