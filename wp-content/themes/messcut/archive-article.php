<?php
/**
 * Articles archive stub.
 *
 * @package Messcut
 */

get_header();
?>
<section class="section articles-archive">
	<div class="container container--narrow">
		<h1><?php esc_html_e( 'Інсайти', 'messcut' ); ?></h1>
		<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?>>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</article>
			<?php endwhile; ?>
			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'Публікацій поки немає.', 'messcut' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
