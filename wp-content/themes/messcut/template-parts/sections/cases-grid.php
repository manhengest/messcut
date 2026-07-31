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

$limit = isset( $args['limit'] ) ? (int) $args['limit'] : -1;
$query = messcut_get_cases_query( $limit );
if ( ! $query->have_posts() ) {
	return;
}
?>
<section class="section cases-grid">
	<div class="container">
		<h2 class="section__title"><?php esc_html_e( 'Наші кейси', 'messcut' ); ?></h2>
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
				</article>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
