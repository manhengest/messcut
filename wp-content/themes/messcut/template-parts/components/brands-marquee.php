<?php
/**
 * Partner logo marquee.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$brands = messcut_get_partner_brands();
if ( ! $brands ) {
	return;
}
?>
<section class="section brands-marquee" aria-label="<?php esc_attr_e( 'Бренди, з якими працювала наша команда', 'messcut' ); ?>">
	<div class="brands-marquee__label">
		<div class="brands-marquee__label-track">
			<?php for ( $copy = 0; $copy < 10; $copy++ ) : ?>
				<span><?php esc_html_e( 'Бренди, з якими працювала наша команда', 'messcut' ); ?></span>
				<i aria-hidden="true"></i>
			<?php endfor; ?>
		</div>
	</div>
	<div class="brands-marquee__track">
		<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
			<div class="brands-marquee__group">
				<?php foreach ( $brands as $brand ) : ?>
					<span class="brands-marquee__item<?php echo empty( $brand['logo_url'] ) ? ' brands-marquee__item--word' : ''; ?>">
						<?php if ( ! empty( $brand['logo_url'] ) ) : ?>
							<img src="<?php echo esc_url( $brand['logo_url'] ); ?>" alt="<?php echo esc_attr( $brand['name'] ); ?>">
						<?php else : ?>
							<?php echo esc_html( $brand['name'] ); ?>
						<?php endif; ?>
					</span>
				<?php endforeach; ?>
			</div>
		<?php endfor; ?>
	</div>
</section>
