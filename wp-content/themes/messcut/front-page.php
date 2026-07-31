<?php
/**
 * Front page template.
 *
 * @package Messcut
 */

get_header();

$tagline = messcut_get_localized_option( 'home_tagline', '' );
?>
<?php get_template_part( 'template-parts/sections/hero' ); ?>

<?php messcut_render_stats(); ?>

<?php if ( $tagline ) : ?>
<section class="section tagline">
	<div class="container">
		<p class="tagline__text"><?php echo esc_html( $tagline ); ?></p>
		<p><a class="button button--primary" href="#lead-form"><?php echo esc_html( messcut_cta_label( 'discuss' ) ); ?> →</a></p>
	</div>
</section>
<?php endif; ?>

<?php get_template_part( 'template-parts/sections/services-grid' ); ?>

<?php get_template_part( 'template-parts/sections/cta', null, array(
	'title' => __( 'Отримайте конструктивні рекомендації щодо подальшого розвитку вашого проєкту', 'messcut' ),
) ); ?>

<?php
get_footer();
