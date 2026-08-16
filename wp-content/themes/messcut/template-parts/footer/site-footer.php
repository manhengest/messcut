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
$instagram_2 = messcut_get_option( 'instagram_2', '' );
?>
<footer class="site-footer surface--gradient-dark">
	<div class="container site-footer__inner">
		<div class="site-footer__brand">
			<?php messcut_render_logo( 'white', array( 'class' => 'site-logo site-logo--footer', 'width' => 140, 'height' => 31 ) ); ?>
			<?php if ( $tagline ) : ?>
				<p class="site-footer__tagline"><?php echo esc_html( $tagline ); ?></p>
			<?php endif; ?>
		</div>

		<div class="site-footer__contacts">
			<?php if ( messcut_phone() ) : ?>
				<a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', messcut_phone() ) ); ?>"><?php echo esc_html( messcut_phone() ); ?></a>
			<?php endif; ?>
			<?php if ( messcut_email() ) : ?>
				<a href="mailto:<?php echo esc_attr( messcut_email() ); ?>"><?php echo esc_html( messcut_email() ); ?></a>
			<?php endif; ?>
			<?php if ( messcut_telegram() ) : ?>
				<a href="<?php echo esc_url( messcut_telegram_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( messcut_telegram() ); ?></a>
			<?php endif; ?>
		</div>

		<div class="site-footer__socials">
			<?php if ( $linkedin ) : ?>
				<a href="<?php echo esc_url( $linkedin ); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
			<?php endif; ?>
			<?php if ( $instagram_1 ) : ?>
				<a href="<?php echo esc_url( $instagram_1 ); ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
			<?php endif; ?>
			<?php if ( $facebook ) : ?>
				<a href="<?php echo esc_url( $facebook ); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
			<?php endif; ?>
			<?php if ( messcut_whatsapp() ) : ?>
				<a href="<?php echo esc_url( messcut_whatsapp_url() ); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
			<?php endif; ?>
		</div>
	</div>
</footer>
