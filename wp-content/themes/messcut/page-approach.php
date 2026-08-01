<?php
/**
 * Approach page template.
 *
 * Template Name: Досвід та підхід
 *
 * @package Messcut
 */

get_header();

get_template_part( 'template-parts/sections/hero', null, array(
	'title'    => __( 'Розвиваємо бренди з науковим підходом', 'messcut' ),
	'subtitle' => __( 'ефективність в цифрах з чіткою стратегією', 'messcut' ),
) );

messcut_render_stats();

$values = function_exists( 'get_field' ) ? get_field( 'values_override' ) : array();
if ( ! empty( $values ) ) {
	get_template_part( 'template-parts/sections/values', null, array( 'values' => $values ) );
} else {
	messcut_render_values();
}

$approach = function_exists( 'get_field' ) ? get_field( 'approach_content' ) : '';
?>
<section class="section approach">
	<div class="container container--narrow">
		<h2 class="section__title"><?php esc_html_e( 'Наш підхід', 'messcut' ); ?></h2>
		<?php if ( $approach ) : ?>
			<div class="entry-content"><?php echo wp_kses_post( $approach ); ?></div>
		<?php elseif ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
				<div class="entry-content"><?php the_content(); ?></div>
			<?php endwhile; ?>
		<?php endif; ?>
		<p><a class="button button--primary" href="#lead-form"><?php echo esc_html( messcut_cta_label( 'discuss' ) ); ?></a></p>
	</div>
</section>

<?php
get_template_part( 'template-parts/sections/cases-grid', null, array(
	'title' => __( 'Наші кейси', 'messcut' ),
) );

get_template_part( 'template-parts/sections/services-grid' );

get_template_part( 'template-parts/sections/audience' );

get_template_part( 'template-parts/sections/cta' );

get_footer();
