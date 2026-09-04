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

$embed   = ! empty( $args['embed'] );
$title   = array_key_exists( 'title', $args ) ? (string) $args['title'] : __( 'Бренди, з якими працювала наша команда', 'messcut' );
$caption = __( 'Бренди, з якими працювала наша команда', 'messcut' );
$brands  = messcut_get_partner_brands();

if ( empty( $brands ) ) {
	return;
}

$classes = $embed ? 'hero__partners partner-logos partner-logos--hero' : 'section partner-logos';
?>
<?php if ( $embed ) : ?>
<div class="<?php echo esc_attr( $classes ); ?>" data-marquee-root>
<?php else : ?>
<section class="<?php echo esc_attr( $classes ); ?>" data-marquee-root>
	<div class="container">
		<?php if ( $title ) : ?>
			<h3 class="partner-logos__title"><?php echo esc_html( $title ); ?></h3>
		<?php endif; ?>
<?php endif; ?>
		<div class="partner-logos__track-wrap">
			<div class="partner-logos__track" data-marquee aria-hidden="true">
				<?php for ( $copy = 0; $copy < 2; $copy++ ) : ?>
					<div class="partner-logos__group" data-marquee-group>
						<span class="partner-logos__item partner-logos__item--caption">
							<?php echo esc_html( $caption ); ?>
						</span>
						<?php foreach ( $brands as $brand ) : ?>
							<span class="partner-logos__item" data-brand="<?php echo esc_attr( messcut_partner_brand_slug( $brand['name'] ) ); ?>">
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
<?php if ( $embed ) : ?>
</div>
<?php else : ?>
	</div>
</section>
<?php endif; ?>
