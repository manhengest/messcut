<?php
/**
 * Insights archive.
 *
 * @package Messcut
 */

get_header();
?>
<section class="insights-hero">
	<div class="container">
	<h1><?php esc_html_e( 'Інсайти', 'messcut' ); ?></h1>
	<p><?php esc_html_e( 'Про бренди, дослідження та маркетингові системи: без води, з логікою та прикладами.', 'messcut' ); ?></p>
	</div>
</section>
<section class="insights-archive">
	<div class="container">
	<div class="insights-grid">
		<?php
		$index = 1;
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part(
					'template-parts/components/insight-card',
					null,
					array(
						'index'   => $index,
						'feature' => 1 === $index,
					)
				);
				$index++;
			}
		}
		?>
	</div>
	</div>
</section>
<?php
get_template_part(
	'template-parts/components/close-band',
	null,
	array(
		'eyebrow' => __( 'Є що обговорити?', 'messcut' ),
		'title'   => __( 'Розкажіть про свій бізнес — <em>розберемо разом</em>', 'messcut' ),
	)
);
get_footer();
