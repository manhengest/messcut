<?php
/**
 * Partner brands marquee.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title  = $args['title'] ?? __( 'Бренди, з якими працювала наша команда', 'messcut' );
$brands = messcut_get_partner_brands();

if ( empty( $brands ) ) {
	return;
}
?>
<section class="section partner-logos">
	<div class="container">
		<?php if ( $title ) : ?>
			<h3 class="partner-logos__title"><?php echo esc_html( $title ); ?></h3>
		<?php endif; ?>
		<div class="partner-logos__track-wrap">
			<div class="partner-logos__track" data-marquee aria-hidden="true">
				<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
					<div class="partner-logos__group">
						<?php foreach ( $brands as $brand ) : ?>
							<span class="partner-logos__item">
								<?php if ( ! empty( $brand['logo_url'] ) ) : ?>
									<img class="partner-logos__logo" src="<?php echo esc_url( $brand['logo_url'] ); ?>" alt="" loading="eager" decoding="async" draggable="false">
								<?php else : ?>
									<?php echo esc_html( $brand['name'] ); ?>
								<?php endif; ?>
							</span>
						<?php endforeach; ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	</div>
</section>
