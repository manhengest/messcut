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

$limit      = isset( $args['limit'] ) ? (int) $args['limit'] : -1;
$title      = $args['title'] ?? __( 'Кейси', 'messcut' );
$show_more  = $args['show_more'] ?? false;
$query      = messcut_get_cases_query( $limit );
if ( ! $query->have_posts() ) {
	return;
}
?>
<section class="section cases-grid">
	<div class="container">
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<div class="grid grid--cases">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$subtitle = function_exists( 'get_field' ) ? get_field( 'hero_subtitle' ) : '';
				?>
				<article class="card card--case">
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="card__media" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
					<?php endif; ?>
					<h3 class="card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<?php if ( $subtitle ) : ?>
						<p class="card__excerpt"><?php echo esc_html( $subtitle ); ?></p>
					<?php elseif ( has_excerpt() ) : ?>
						<p class="card__excerpt"><?php the_excerpt(); ?></p>
					<?php endif; ?>
					<a class="card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Більше', 'messcut' ); ?> →</a>
				</article>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
		<?php if ( $show_more ) : ?>
			<p class="cases-grid__more">
				<a class="button button--secondary" href="<?php echo esc_url( messcut_approach_url() ); ?>">
					<?php esc_html_e( 'Дізнатись більше про наш підхід', 'messcut' ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</section>
