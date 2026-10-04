<?php
/**
 * Audience band and keyword marquee.
 *
 * @package Messcut
 */

$words = array(
	__( 'дослідження', 'messcut' ),
	__( 'стратегія', 'messcut' ),
	__( 'бізнес-показники', 'messcut' ),
	__( 'структура', 'messcut' ),
);
?>
<section class="section home-audience">
	<div class="audience">
		<div class="audience__copy">
			<h2><?php esc_html_e( 'Хто нас обирає', 'messcut' ); ?></h2>
			<p class="audience__text"><?php esc_html_e( 'Підприємці, які хочуть розвивати бренд системно, менше ризикувати й приймати рішення на основі досліджень, даних і наукових принципів', 'messcut' ); ?></p>
		</div>
	</div>
</section>
<div class="keyword-marquee">
	<div class="keyword-marquee__track">
		<?php for ( $half = 0; $half < 2; $half++ ) : ?>
			<div class="keyword-marquee__group">
				<?php for ( $copy = 0; $copy < 4; $copy++ ) : ?>
					<?php foreach ( $words as $word ) : ?>
						<span><?php echo esc_html( $word ); ?></span>
					<?php endforeach; ?>
				<?php endfor; ?>
			</div>
		<?php endfor; ?>
	</div>
</div>
