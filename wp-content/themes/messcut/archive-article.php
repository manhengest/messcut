<?php
/**
 * Insights archive.
 *
 * @package Messcut
 */

get_header();
?>
<section class="listing-hero">
	<h1><?php esc_html_e( 'Інсайти', 'messcut' ); ?></h1>
	<p><?php esc_html_e( 'Про бренди, дослідження та маркетингові системи: без води, з логікою та прикладами.', 'messcut' ); ?></p>
</section>
<section class="section">
	<div class="listing-grid">
		<?php
		$index = 1;
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				get_template_part( 'template-parts/components/insight-card', null, array(
					'index'   => $index,
					'feature' => 1 === $index,
				) );
				$index++;
			}
		}
		?>
	</div>
</section>
<section class="listing-band">
	<span class="eyebrow"><?php esc_html_e( 'Є що обговорити?', 'messcut' ); ?></span>
	<h2><?php echo wp_kses( __( 'Розкажіть про свій бізнес — <em>розберемо разом</em>', 'messcut' ), array( 'em' => array() ) ); ?></h2>
	<a class="cta" href="<?php echo esc_url( messcut_contact_url() ); ?>"><?php esc_html_e( 'Обговорити проєкт', 'messcut' ); ?> <b>→</b></a>
</section>
<?php
get_footer();
