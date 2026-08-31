<?php
/**
 * Contact channels: Telegram, WhatsApp, email.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$has_whatsapp = (bool) messcut_whatsapp();
$has_email    = (bool) messcut_email();
$has_telegram = (bool) messcut_telegram();

if ( ! $has_whatsapp && ! $has_email && ! $has_telegram ) {
	return;
}
?>
<div class="contact-channels">
	<p class="contact-channels__label"><?php esc_html_e( 'Або звʼяжіться з нами самостійно', 'messcut' ); ?></p>
	<div class="contact-channels__links">
		<?php if ( $has_telegram ) : ?>
			<a class="contact-channels__link" href="<?php echo esc_url( messcut_telegram_url() ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Telegram', 'messcut' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $has_whatsapp ) : ?>
			<a class="contact-channels__link" href="<?php echo esc_url( messcut_whatsapp_url() ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'WhatsApp', 'messcut' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( $has_email ) : ?>
			<a class="contact-channels__link" href="mailto:<?php echo esc_attr( messcut_email() ); ?>">
				<?php esc_html_e( 'Email', 'messcut' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
