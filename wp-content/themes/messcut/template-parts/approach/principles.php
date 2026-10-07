<?php
/**
 * Approach principles.
 *
 * @package Messcut
 */

$principles = array(
	array( '01', __( 'Фокус', 'messcut' ), __( 'Вирішуємо головне, і бачимо всю картину бізнесу цілісно.', 'messcut' ) ),
	array( '02', __( 'Доказовість', 'messcut' ), __( 'Різкість вимірюємо дослідженнями, а не відчуттями.', 'messcut' ) ),
	array( '03', __( 'Сміливість', 'messcut' ), __( 'Не боїмося складного.', 'messcut' ) ),
);
?>
<section class="section approach-principles">
	<h2><?php esc_html_e( 'Наш підхід', 'messcut' ); ?></h2>
	<p class="approach-principles__lead"><?php esc_html_e( 'Досліджуємо, як люди приймають рішення, що впливає на їхню поведінку та як це працює в конкретному бізнесі. На цій основі будуємо маркетинг, який має логіку, систему й зрозумілу ціль.', 'messcut' ); ?></p>
	<?php foreach ( $principles as $index => $principle ) : ?>
		<div class="principle<?php echo 0 === $index ? ' principle--lead' : ''; ?>">
			<i><?php echo esc_html( $principle[0] ); ?></i>
			<h3><?php echo esc_html( $principle[1] ); ?></h3>
			<p><?php echo esc_html( $principle[2] ); ?></p>
		</div>
	<?php endforeach; ?>
</section>
