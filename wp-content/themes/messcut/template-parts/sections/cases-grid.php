<?php
/**
 * Cases grid section.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$limit     = isset( $args['limit'] ) ? (int) $args['limit'] : -1;
$title     = $args['title'] ?? __( 'Кейси', 'messcut' );
$show_more = $args['show_more'] ?? false;
$query     = messcut_get_cases_query( $limit );
if ( ! $query->have_posts() ) {
	return;
}
?>
<section class="section cases-grid surface--gradient-light surface--dots">
	<div class="container">
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<div class="grid grid--cases">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$subtitle = function_exists( 'get_field' ) ? get_field( 'hero_subtitle' ) : '';
				$excerpt  = has_excerpt() ? get_the_excerpt() : '';
				$has_extra = ( $subtitle || $excerpt );
				?>
				<article class="card card--case" <?php echo $has_extra ? 'data-case-expand' : ''; ?>>
					<a class="card__media" href="<?php the_permalink(); ?>">
						<?php messcut_render_post_thumbnail( 'medium_large' ); ?>
					</a>
					<h3 class="card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<?php if ( $has_extra ) : ?>
						<div class="card__excerpt card__excerpt--collapsible" data-case-excerpt>
							<?php if ( $subtitle ) : ?>
								<p class="card__excerpt-text"><?php echo esc_html( $subtitle ); ?></p>
							<?php endif; ?>
							<?php if ( $excerpt ) : ?>
								<p class="card__excerpt-extra"><?php echo esc_html( $excerpt ); ?></p>
							<?php endif; ?>
							<button
								type="button"
								class="card__excerpt-toggle"
								data-case-excerpt-toggle
								aria-expanded="false"
								aria-label="<?php esc_attr_e( 'Розгорнути', 'messcut' ); ?>"
							>
								<span class="card__excerpt-toggle-icon" aria-hidden="true">+</span>
							</button>
						</div>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
		<?php if ( $show_more ) : ?>
			<p class="cases-grid__more">
				<a class="button button--secondary" href="<?php echo esc_url( messcut_cases_archive_url() ); ?>">
					<?php esc_html_e( 'Усі кейси', 'messcut' ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</section>
