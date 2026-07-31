<?php
/**
 * Language switcher (Polylang).
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! messcut_is_polylang_active() ) {
	return;
}

$languages = pll_the_languages(
	array(
		'raw'          => 1,
		'show_flags'   => 0,
		'show_names'   => 1,
		'hide_current' => 0,
	)
);

if ( empty( $languages ) || ! is_array( $languages ) ) {
	return;
}
?>
<nav class="language-switcher" aria-label="<?php esc_attr_e( 'Мова', 'messcut' ); ?>">
	<ul class="language-switcher__list">
		<?php foreach ( $languages as $language ) : ?>
			<?php
			$is_current = ! empty( $language['current_lang'] );
			$url        = $language['url'] ?? '';
			$name       = $language['name'] ?? $language['slug'] ?? '';
			$slug       = $language['slug'] ?? '';
			?>
			<li class="language-switcher__item<?php echo $is_current ? ' is-current' : ''; ?>">
				<?php if ( $is_current ) : ?>
					<span class="language-switcher__link" aria-current="true" lang="<?php echo esc_attr( $slug ); ?>">
						<?php echo esc_html( $name ); ?>
					</span>
				<?php else : ?>
					<a class="language-switcher__link" href="<?php echo esc_url( $url ); ?>" hreflang="<?php echo esc_attr( $slug ); ?>" lang="<?php echo esc_attr( $slug ); ?>">
						<?php echo esc_html( $name ); ?>
					</a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
