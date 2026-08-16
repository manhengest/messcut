<?php
/**
 * Site header.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<header class="site-header">
	<div class="site-header__inner">
		<div class="site-header__brand">
			<?php messcut_render_logo( 'black', array( 'class' => 'site-logo site-logo--header' ) ); ?>
		</div>

		<div class="site-header__actions">
			<a class="site-header__cta" href="#lead-form"><?php esc_html_e( 'Звʼязатися', 'messcut' ); ?></a>
			<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation" aria-label="<?php esc_attr_e( 'Меню', 'messcut' ); ?>">
				<span class="nav-toggle__icon" aria-hidden="true">
					<span class="nav-toggle__line"></span>
					<span class="nav-toggle__line"></span>
					<span class="nav-toggle__line"></span>
				</span>
			</button>
		</div>

		<div class="site-header__contacts">
			<?php messcut_render_language_switcher( array( 'variant' => 'compact' ) ); ?>
			<?php if ( messcut_phone() ) : ?>
				<a class="site-header__contact" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', messcut_phone() ) ); ?>">
					<?php echo esc_html( messcut_phone() ); ?>
				</a>
			<?php endif; ?>
			<a class="site-header__cta site-header__cta--desktop" href="#lead-form"><?php esc_html_e( 'Звʼязатися', 'messcut' ); ?></a>
		</div>
	</div>

	<nav id="primary-navigation" class="primary-navigation surface--gradient-dark" aria-label="<?php esc_attr_e( 'Головне меню', 'messcut' ); ?>">
		<div class="primary-navigation__inner">
			<?php
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'primary-menu',
				'fallback_cb'    => 'messcut_fallback_primary_menu',
			) );
			?>
			<div class="primary-navigation__meta">
				<?php messcut_render_language_switcher( array( 'variant' => 'compact' ) ); ?>
				<?php if ( messcut_phone() ) : ?>
					<a class="primary-navigation__contact" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', messcut_phone() ) ); ?>">
						<?php echo esc_html( messcut_phone() ); ?>
					</a>
				<?php endif; ?>
				<?php if ( messcut_telegram() ) : ?>
					<a class="primary-navigation__contact" href="https://t.me/<?php echo esc_attr( ltrim( messcut_telegram(), '@' ) ); ?>" target="_blank" rel="noopener noreferrer">
						<?php echo esc_html( messcut_telegram() ); ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</nav>
</header>
