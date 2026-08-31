<?php
/**
 * Site footer.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tagline     = messcut_get_localized_option( 'footer_tagline', __( 'Стратегічний маркетинг для брендів, які хочуть зростати системно.', 'messcut' ) );
$linkedin    = messcut_get_option( 'linkedin', '' );
$facebook    = messcut_get_option( 'facebook', '' );
$instagram_1 = messcut_get_option( 'instagram_1', '' );
$privacy_url = messcut_page_url( 'polityka-konfidentsiynosti' );

$has_telegram = (bool) messcut_telegram();
$has_whatsapp = (bool) messcut_whatsapp();
$has_email    = (bool) messcut_email();
$has_contact  = $has_telegram || $has_whatsapp || $has_email;
$has_social   = $has_telegram || $facebook || $linkedin || $instagram_1;
?>
<footer class="site-footer surface--gradient-dark">
	<div class="container site-footer__inner">
		<div class="site-footer__brand">
			<?php messcut_render_logo( 'footer', array( 'class' => 'site-logo site-logo--footer', 'width' => 329, 'height' => 326 ) ); ?>
			<?php if ( $tagline ) : ?>
				<p class="site-footer__tagline"><?php echo esc_html( $tagline ); ?></p>
			<?php endif; ?>
		</div>

		<?php if ( $has_contact ) : ?>
			<div class="contact-channels site-footer__group">
				<p class="contact-channels__label"><?php esc_html_e( 'Звʼяжіться з нами зручним способом', 'messcut' ); ?></p>
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
		<?php endif; ?>

		<?php if ( $has_social ) : ?>
			<div class="contact-channels site-footer__group">
				<p class="contact-channels__label"><?php esc_html_e( 'Слідкуйте за нами в соц.мережах', 'messcut' ); ?></p>
				<div class="contact-channels__links">
					<?php if ( $has_telegram ) : ?>
						<a class="contact-channels__link" href="<?php echo esc_url( messcut_telegram_url() ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Telegram', 'messcut' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $facebook ) : ?>
						<a class="contact-channels__link" href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Facebook', 'messcut' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $linkedin ) : ?>
						<a class="contact-channels__link" href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'LinkedIn', 'messcut' ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $instagram_1 ) : ?>
						<a class="contact-channels__link" href="<?php echo esc_url( $instagram_1 ); ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Instagram', 'messcut' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<a class="site-footer__legal" href="<?php echo esc_url( $privacy_url ); ?>">
			<?php esc_html_e( 'Політика конфіденційності', 'messcut' ); ?>
		</a>
	</div>
</footer>
