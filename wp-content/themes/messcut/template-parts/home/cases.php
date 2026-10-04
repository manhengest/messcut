<?php
/**
 * Home case rail.
 *
 * @package Messcut
 */

$query = messcut_get_cases_query( 6 );
?>
<section class="section home-cases" id="cases-section">
	<?php
	get_template_part( 'template-parts/components/section-head', null, array(
		'title' => __( 'Кейси', 'messcut' ),
		'href'  => messcut_cases_archive_url(),
	) );
	?>
	<div class="card-rail">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/components/case-card' );
		}
		wp_reset_postdata();
		?>
	</div>
</section>
