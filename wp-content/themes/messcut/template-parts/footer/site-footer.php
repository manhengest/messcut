<?php
/**
 * Site footer. Columns follow the index mock: contacts and socials.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$privacy = messcut_page_url( 'polityka-konfidentsiynosti' );
?>
<footer class="site-footer" id="<?php echo ( is_page_template( 'page-approach.php' ) || messcut_is_translated_page( 'dosvid' ) ) ? 'lead' : 'footer'; ?>">
	<?php messcut_render_logo( 'white', array( 'class' => 'site-footer__logo', 'height' => 38, 'width' => 160 ) ); ?>
	<p class="site-footer__tagline"><?php echo esc_html( __( (string) messcut_get_option( 'footer_tagline', 'Стратегічний маркетинг для брендів, які хочуть зростати системно.' ), 'messcut' ) ); ?></p>
	<div class="site-footer__cols">
		<div>
			<p class="site-footer__label"><?php esc_html_e( 'Звʼяжіться з нами зручним способом', 'messcut' ); ?></p>
			<a href="<?php echo esc_url( messcut_telegram_url() ); ?>">Telegram</a>
			<a href="<?php echo esc_url( messcut_whatsapp_url() ); ?>">WhatsApp</a>
			<a href="<?php echo esc_url( 'mailto:' . messcut_email() ); ?>">Email</a>
		</div>
		<div>
			<p class="site-footer__label"><?php esc_html_e( 'Слідкуйте за нами в соц.мережах', 'messcut' ); ?></p>
			<a href="<?php echo esc_url( messcut_telegram_url() ); ?>">Telegram</a>
			<a href="<?php echo esc_url( messcut_instagram_url() ); ?>">Instagram</a>
		</div>
	</div>
	<div class="site-footer__bar">
		<a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Політика конфіденційності', 'messcut' ); ?></a>
		<a href="<?php echo esc_url( 'tel:' . preg_replace( '/\s+/', '', messcut_phone() ) ); ?>"><?php echo esc_html( messcut_phone() ); ?></a>
	</div>
</footer>
