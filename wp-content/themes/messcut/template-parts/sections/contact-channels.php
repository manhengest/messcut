<?php
/**
 * Contact channels: Telegram + WhatsApp.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="contact-channels">
	<p class="contact-channels__label"><?php esc_html_e( 'або звʼяжіться з нами', 'messcut' ); ?></p>
	<div class="contact-channels__links">
		<?php if ( messcut_telegram() ) : ?>
			<a class="button button--outline" href="<?php echo esc_url( messcut_telegram_url() ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Telegram', 'messcut' ); ?>
			</a>
		<?php endif; ?>
		<?php if ( messcut_whatsapp() ) : ?>
			<a class="button button--outline" href="<?php echo esc_url( messcut_whatsapp_url() ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'WhatsApp', 'messcut' ); ?>
			</a>
		<?php endif; ?>
	</div>
</div>
