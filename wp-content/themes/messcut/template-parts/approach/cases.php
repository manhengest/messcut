<?php
/**
 * Approach cases.
 *
 * @package Messcut
 */

$query = messcut_get_cases_query( 6 );
?>
<section class="section approach-cases" id="cases-section">
	<h2><?php esc_html_e( 'Кейси', 'messcut' ); ?></h2>
	<div class="card-rail">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			get_template_part( 'template-parts/components/case-card' );
		}
		wp_reset_postdata();
		?>
	</div>
	<div class="approach-cases__more">
		<a class="button button--outline" href="<?php echo esc_url( messcut_cases_archive_url() ); ?>"><?php esc_html_e( 'Більше кейсів →', 'messcut' ); ?></a>
	</div>
</section>
