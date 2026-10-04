<?php
/**
 * Site header.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_progress = messcut_is_translated_page( 'poslugy' ) || is_singular( 'case_study' );
?>
<header class="site-header" data-header>
	<?php messcut_render_logo( 'black', array( 'class' => 'site-header__logo', 'height' => 32, 'width' => 140 ) ); ?>
	<nav class="site-header__nav" aria-label="<?php esc_attr_e( 'Головне меню', 'messcut' ); ?>">
		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'site-header__menu',
			'fallback_cb'    => 'messcut_fallback_primary_menu',
			'depth'          => 1,
		) );
		?>
	</nav>
	<div class="site-header__tools">
		<div class="site-header__lang">
			<?php messcut_render_language_switcher( array( 'variant' => 'compact' ) ); ?>
		</div>
		<a class="button button--ghost site-header__cta" href="<?php echo esc_url( messcut_contact_url() ); ?>">
			<?php esc_html_e( 'Звʼязатися', 'messcut' ); ?>
		</a>
		<button class="site-header__burger" type="button" data-menu-open aria-expanded="false" aria-controls="mobile-menu" aria-label="<?php esc_attr_e( 'Відкрити меню', 'messcut' ); ?>">
			<svg width="18" height="14" viewBox="0 0 26 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M1 2h24M1 9h24M1 16h24"/></svg>
		</button>
	</div>
	<?php if ( $show_progress ) : ?>
		<div class="site-header__progress" data-progress></div>
	<?php endif; ?>
</header>
<?php get_template_part( 'template-parts/header/mobile-menu' ); ?>
