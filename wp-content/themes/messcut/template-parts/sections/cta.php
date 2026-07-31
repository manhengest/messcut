<?php
/**
 * CTA section with lead form.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = $args['title'] ?? messcut_cta_label( 'consult' );
$text  = $args['text'] ?? __( 'Заповніть коротку форму — ми звʼяжемось з вами найближчим часом.', 'messcut' );
$show_contacts = $args['show_contacts'] ?? true;
?>
<section class="section cta" id="lead-form">
	<div class="container">
		<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
		<?php if ( $text ) : ?>
			<p class="cta__text"><?php echo esc_html( $text ); ?></p>
		<?php endif; ?>
		<?php messcut_render_lead_form(); ?>
		<?php if ( $show_contacts ) : ?>
			<?php messcut_render_contact_channels(); ?>
		<?php endif; ?>
	</div>
</section>
