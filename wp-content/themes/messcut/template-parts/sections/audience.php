<?php
/**
 * Audience section — "Для кого наші послуги?" plus method values.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = messcut_get_ticker_items();
?>
<section class="section audience surface--gradient-light">
	<div class="container">
		<div class="audience__layout">
			<h2 class="audience__title"><?php echo wp_kses( __( 'Для кого<br>наші послуги?', 'messcut' ), array( 'br' => array() ) ); ?></h2>
			<div class="audience__copy">
				<p class="audience__text">
					<?php
					echo esc_html(
						messcut_get_localized_option(
							'audience_text',
							__(
								'Підприємці, які хочуть розвивати бренд системно, менше ризикувати й приймати рішення на основі досліджень, даних і наукових принципів',
								'messcut'
							)
						)
					);
					?>
				</p>
				<?php if ( ! empty( $items ) ) : ?>
					<ul class="audience__values" aria-label="<?php esc_attr_e( 'Наші цінності', 'messcut' ); ?>">
						<?php foreach ( $items as $item ) : ?>
							<li class="audience__value"><?php echo esc_html( $item ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
