<?php
/**
 * Services grid section.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query = messcut_get_services_query( 4 );
if ( ! $query->have_posts() ) {
	return;
}
?>
<section class="section services-grid">
	<div class="container">
		<h2 class="section__title"><?php esc_html_e( 'Послуги', 'messcut' ); ?></h2>
		<div class="grid grid--services">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$short = function_exists( 'get_field' ) ? get_field( 'short_description' ) : '';
				?>
				<article class="card card--service">
					<h3 class="card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<?php if ( $short ) : ?>
						<p class="card__excerpt"><?php echo esc_html( $short ); ?></p>
					<?php elseif ( has_excerpt() ) : ?>
						<p class="card__excerpt"><?php the_excerpt(); ?></p>
					<?php endif; ?>
					<a class="card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Детальніше', 'messcut' ); ?> →</a>
				</article>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
