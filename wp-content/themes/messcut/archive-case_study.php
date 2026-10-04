<?php
/**
 * Cases archive. Uses the insights listing with case cards.
 *
 * @package Messcut
 */

get_header();
?>
<section class="listing-hero">
	<h1><?php esc_html_e( 'Кейси', 'messcut' ); ?></h1>
	<p><?php esc_html_e( 'Як дослідження і стратегія змінюють бізнес у цифрах.', 'messcut' ); ?></p>
</section>
<section class="section">
	<div class="card-rail">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/components/case-card' );
			}
		}
		?>
	</div>
</section>
<?php
get_footer();
