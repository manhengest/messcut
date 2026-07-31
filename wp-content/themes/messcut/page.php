<?php
/**
 * Default page template.
 *
 * @package Messcut
 */

get_header();
?>
<section class="section page-content">
	<div class="container container--narrow">
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class(); ?>>
				<h1 class="page-title"><?php the_title(); ?></h1>
				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</article>
		<?php endwhile; ?>
	</div>
</section>
<?php
get_footer();
