<?php
/**
 * Site footer.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tagline = messcut_get_localized_option( 'footer_tagline', __( 'Стратегічний маркетинг для брендів, які хочуть зростати системно.', 'messcut' ) );
$about   = messcut_get_localized_option( 'footer_about', '' );
$instagram_1 = messcut_get_option( 'instagram_1', '' );
$instagram_2 = messcut_get_option( 'instagram_2', '' );
?>
<footer class="site-footer">
	<div class="container site-footer__inner">
		<div class="site-footer__brand">
			<?php messcut_render_logo( 'white', array( 'class' => 'site-logo site-logo--footer', 'width' => 140, 'height' => 31 ) ); ?>
			<p class="site-footer__tagline"><?php echo esc_html( $tagline ); ?></p>
			<?php if ( $about ) : ?>
				<p class="site-footer__about"><?php echo esc_html( $about ); ?></p>
			<?php endif; ?>
		</div>

		<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Меню в підвалі', 'messcut' ); ?>">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'menu_class'     => 'footer-menu',
				'depth'          => 1,
				'fallback_cb'    => false,
			) );
			?>
		</nav>

		<div class="site-footer__contacts">
			<h3 class="site-footer__contacts-title"><?php esc_html_e( 'Контакти', 'messcut' ); ?></h3>
			<?php if ( messcut_telegram() ) : ?>
				<p>Telegram: <a href="<?php echo esc_url( messcut_telegram_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( messcut_telegram() ); ?></a></p>
			<?php endif; ?>
			<?php if ( messcut_whatsapp() ) : ?>
				<p>WhatsApp: <a href="<?php echo esc_url( messcut_whatsapp_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( messcut_whatsapp() ); ?></a></p>
			<?php endif; ?>
			<?php if ( messcut_phone() ) : ?>
				<p><?php esc_html_e( 'Телефон', 'messcut' ); ?>: <a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', messcut_phone() ) ); ?>"><?php echo esc_html( messcut_phone() ); ?></a></p>
			<?php endif; ?>
			<?php if ( messcut_email() ) : ?>
				<p>Email: <a href="mailto:<?php echo esc_attr( messcut_email() ); ?>"><?php echo esc_html( messcut_email() ); ?></a></p>
			<?php endif; ?>
			<?php if ( $instagram_1 || $instagram_2 ) : ?>
				<p>Instagram:
					<?php if ( $instagram_1 ) : ?>
						<a href="<?php echo esc_url( $instagram_1 ); ?>" target="_blank" rel="noopener noreferrer">@valeria.messcut</a>
					<?php endif; ?>
					<?php if ( $instagram_1 && $instagram_2 ) : ?>
						<span aria-hidden="true"> · </span>
					<?php endif; ?>
					<?php if ( $instagram_2 ) : ?>
						<a href="<?php echo esc_url( $instagram_2 ); ?>" target="_blank" rel="noopener noreferrer">@messcut.strategy</a>
					<?php endif; ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
</footer>
