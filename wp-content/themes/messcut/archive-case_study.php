<?php
/**
 * Cases archive.
 *
 * @package Messcut
 */

get_header();
?>
<section class="cases-hero">
	<div class="container">
	<h1><?php esc_html_e( 'Кейси', 'messcut' ); ?></h1>
	<p><?php esc_html_e( 'Різні категорії та задачі, а в основі кожного проєкту — дослідження, система та креатив.', 'messcut' ); ?></p>
	</div>
</section>
<section class="cases-archive" id="cases-section">
	<div class="container">
	<div class="cases-grid">
		<?php
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/components/case-card' );
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
		'eyebrow' => __( 'Наступний кейс — ваш', 'messcut' ),
		'title'   => __( 'Розкажіть про свій бренд — <em>знайдемо точку росту</em>', 'messcut' ),
	)
);
get_footer();
