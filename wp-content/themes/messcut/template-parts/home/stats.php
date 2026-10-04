<?php
/**
 * Why-us stat bento.
 *
 * @package Messcut
 */

$cells = array(
	array( 'recommend', '94%', __( 'клієнтів рекомендують нас колегам', 'messcut' ) ),
	array( 'projects', '50+', __( 'стратегічних проєктів для брендів у різних нішах', 'messcut' ) ),
	array( 'years', '6+', __( 'років роботи', 'messcut' ) ),
	array( 'nonstop', 'NON-STOP', __( 'підвищуємо кваліфікацію, аналізуємо світові дослідження та best practices', 'messcut' ) ),
	array( 'ratio', '1:2', __( 'до двох проєктів на маркетолога для глибокого занурення у ваш бізнес', 'messcut' ) ),
);
?>
<section class="section home-stats">
	<h2><?php esc_html_e( 'Чому нас обирають?', 'messcut' ); ?></h2>
	<div class="home-stats__grid">
		<?php foreach ( $cells as $cell ) : ?>
			<div class="stat-cell stat-cell--<?php echo esc_attr( $cell[0] ); ?>">
				<b><?php echo esc_html( $cell[1] ); ?></b>
				<p><?php echo esc_html( $cell[2] ); ?></p>
			</div>
		<?php endforeach; ?>
	</div>
</section>
