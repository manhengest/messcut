<?php
/**
 * Services accordion — numbered rows with excerpt, quote, permalink.
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
$num = 0;
?>
<section class="section services-grid">
	<div class="container">
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<div class="services-list" data-accordion>
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				++$num;
				$description = messcut_get_service_card_description();
				$quote       = messcut_get_acf( 'testimonial_quote' );
				$author      = messcut_get_acf( 'testimonial_author' );
				$role        = messcut_get_acf( 'testimonial_role' );
				?>
				<details
					class="services-list__item"
					data-pain-service="<?php echo esc_attr( 'service-' . ( $num - 1 ) ); ?>"
				>
					<summary class="services-list__header">
						<span class="services-list__num"><?php echo esc_html( sprintf( '%02d', $num ) ); ?></span>
						<h3 class="services-list__title"><?php the_title(); ?></h3>
						<span class="services-list__toggle" aria-hidden="true">
							<span class="services-list__toggle-icon">+</span>
						</span>
					</summary>
					<div class="services-list__panel">
						<?php if ( $description ) : ?>
							<div class="services-list__copy">
								<p class="services-list__excerpt"><?php echo esc_html( $description ); ?></p>
							</div>
						<?php endif; ?>
						<?php if ( $quote ) : ?>
							<blockquote class="services-list__testimonial">
								<p class="services-list__quote"><?php echo esc_html( $quote ); ?></p>
								<?php if ( $author || $role ) : ?>
									<footer class="services-list__attribution">
										<?php if ( $author ) : ?>
											<cite><?php echo esc_html( $author ); ?></cite>
										<?php endif; ?>
										<?php if ( $role ) : ?>
											<span class="services-list__role"><?php echo esc_html( $role ); ?></span>
										<?php endif; ?>
									</footer>
								<?php endif; ?>
							</blockquote>
						<?php endif; ?>
						<a class="services-list__link" href="<?php the_permalink(); ?>">
							<?php esc_html_e( 'Детальніше', 'messcut' ); ?>
						</a>
					</div>
				</details>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
		<?php messcut_render_consult_cta(); ?>
	</div>
</section>
