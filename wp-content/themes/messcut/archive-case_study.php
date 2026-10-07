<?php
/**
 * Cases archive.
 *
 * @package Messcut
 */

get_header();
?>
<section class="cases-hero">
	<h1><?php esc_html_e( 'Кейси', 'messcut' ); ?></h1>
	<p><?php esc_html_e( 'Різні категорії та задачі, а в основі кожного проєкту — дослідження, система та креатив.', 'messcut' ); ?></p>
</section>
<section class="cases-archive" id="cases-section">
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
</section>
<section class="cases-close">
	<div class="cases-close__band">
		<span class="cases-close__eyebrow"><?php esc_html_e( 'Наступний кейс — ваш', 'messcut' ); ?></span>
		<h2><?php echo wp_kses( __( 'Розкажіть про свій бренд — <em>знайдемо точку росту</em>', 'messcut' ), array( 'em' => array() ) ); ?></h2>
		<a class="cta" href="<?php echo esc_url( messcut_contact_url() ); ?>"><?php esc_html_e( 'Обговорити проєкт', 'messcut' ); ?> <b>→</b></a>
	</div>
</section>
<?php
get_footer();
