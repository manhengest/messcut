<?php
/**
 * Mobile menu overlay.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="mobile-menu" id="mobile-menu" data-mobile-menu aria-hidden="true">
	<div class="mobile-menu__top">
		<?php messcut_render_logo( 'white', array( 'class' => 'mobile-menu__logo', 'linked' => false, 'height' => 28, 'width' => 120 ) ); ?>
		<button class="mobile-menu__close" type="button" data-menu-close aria-label="<?php esc_attr_e( 'Закрити меню', 'messcut' ); ?>">✕</button>
	</div>
	<nav class="mobile-menu__nav" aria-label="<?php esc_attr_e( 'Головне меню', 'messcut' ); ?>">
		<?php foreach ( messcut_nav_items() as $item ) : ?>
			<a href="<?php echo esc_url( $item['url'] ); ?>">
				<?php echo esc_html( $item['label'] ); ?>
				<?php messcut_render_link_arrow_icon( array( 'class' => 'mobile-menu__nav-icon' ) ); ?>
			</a>
		<?php endforeach; ?>
	</nav>
	<div class="mobile-menu__cta">
		<a class="button button--light mobile-menu__telegram" href="<?php echo esc_url( messcut_telegram_url() ); ?>">
			<?php esc_html_e( 'Написати в Telegram', 'messcut' ); ?>
			<b>→</b>
		</a>
	</div>
</div>
