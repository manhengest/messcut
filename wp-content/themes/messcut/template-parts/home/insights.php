<?php
/**
 * Home insights.
 *
 * @package Messcut
 */

$query = messcut_get_articles_query( 3 );
?>
<section class="section home-insights">
	<?php
	get_template_part( 'template-parts/components/section-head', null, array(
		'title' => __( 'Інсайти', 'messcut' ),
		'href'  => messcut_insights_archive_url(),
	) );
	?>
	<div class="listing-grid">
		<?php
		$index = 1;
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/components/insight-card', null, array( 'index' => $index ) );
			$index++;
		}
		wp_reset_postdata();
		?>
	</div>
</section>
