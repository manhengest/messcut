<?php
/**
 * Site-wide FAQ section (home, case study, etc.).
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = messcut_get_faq_items();
if ( ! $items ) {
	return;
}

$section_class = isset( $args['section_class'] ) ? (string) $args['section_class'] : 'section home-faq';
$section_id    = isset( $args['section_id'] ) ? (string) $args['section_id'] : '';
$data_chapter  = ! empty( $args['data_chapter'] );
?>
<section class="<?php echo esc_attr( $section_class ); ?>"<?php echo '' !== $section_id ? ' id="' . esc_attr( $section_id ) . '"' : ''; ?><?php echo $data_chapter ? ' data-chapter' : ''; ?>>
	<div class="home-faq__grid">
		<div>
			<h2><?php echo esc_html( messcut_get_faq_title() ); ?></h2>
			<p><?php echo esc_html( messcut_get_faq_intro() ); ?></p>
		</div>
		<?php
		get_template_part(
			'template-parts/components/faq-list',
			null,
			array(
				'items' => $items,
			)
		);
		?>
	</div>
</section>
