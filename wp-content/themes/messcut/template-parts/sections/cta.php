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

$title = $args['title'] ?? __( 'Отримайте план розвитку вашого бренду', 'messcut' );
$text  = $args['text'] ?? __( 'в форматі 30-хв стратегічної зустрічі', 'messcut' );
$show_contacts = $args['show_contacts'] ?? true;
?>
<section class="section cta surface--gradient-dark" id="lead-form">
	<div class="container">
		<div class="cta__layout">
			<div class="cta__intro">
				<?php if ( $text ) : ?>
					<p class="cta__descriptor"><?php echo esc_html( $text ); ?></p>
				<?php endif; ?>
				<h2 class="section__title"><?php echo esc_html( $title ); ?></h2>
			</div>
			<div class="cta__panel">
				<?php messcut_render_lead_form(); ?>
			</div>
			<?php if ( $show_contacts ) : ?>
				<?php messcut_render_contact_channels(); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
