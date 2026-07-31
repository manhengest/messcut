<?php
/**
 * Main index fallback.
 *
 * @package Messcut
 */

get_header();
?>
<section class="section">
	<div class="container container--narrow">
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Записів не знайдено.', 'messcut' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
