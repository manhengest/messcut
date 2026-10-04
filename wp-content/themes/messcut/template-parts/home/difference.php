<?php
/**
 * Comparison tabs.
 *
 * @package Messcut
 */

$tabs = array(
	'ms'  => __( 'Messcut', 'messcut' ),
	'own' => __( 'Найм маркетолога', 'messcut' ),
	'ag'  => __( 'Інші агенції', 'messcut' ),
);
$cards = array(
	'ms'  => array(
		__( 'Максимум 2 проєкти на одного спеціаліста', 'messcut' ),
		__( 'Фокус на результат і бізнес-KPI, а не на відпрацьовані години', 'messcut' ),
		__( 'Багаторівневий контроль якості: внутрішня ревізія та регулярні зовнішні аудити', 'messcut' ),
		__( 'Рішення на основі досліджень і перевірених методологій', 'messcut' ),
	),
	'own' => array(
		__( 'Один спеціаліст – обмежена точка зору', 'messcut' ),
		__( 'Відсутність зовнішньої ревізії та незалежного погляду', 'messcut' ),
		__( 'Стратегічні задачі легко поступаються операційним', 'messcut' ),
	),
	'ag'  => array(
		__( 'Велика кількість проєктів на одного спеціаліста', 'messcut' ),
		__( 'Поверхневе занурення через велику кількість проєктів', 'messcut' ),
		__( 'Відсутність системної ревізії та постійного професійного розвитку', 'messcut' ),
		__( 'Senior-експертиза не завжди залучена до роботи', 'messcut' ),
	),
);
?>
<section class="section home-diff">
	<h2><?php esc_html_e( 'У чому різниця?', 'messcut' ); ?></h2>
	<div class="tabs" data-tabs role="tablist">
		<?php foreach ( $tabs as $key => $label ) : ?>
			<button type="button" data-tab="<?php echo esc_attr( $key ); ?>" aria-selected="<?php echo 'ms' === $key ? 'true' : 'false'; ?>"><?php echo esc_html( $label ); ?></button>
		<?php endforeach; ?>
	</div>
	<div class="comparison-grid">
		<?php foreach ( $cards as $key => $items ) : ?>
			<article class="comparison-card comparison-card--<?php echo esc_attr( $key ); ?><?php echo 'ms' === $key ? ' is-active' : ''; ?>" data-panel="<?php echo esc_attr( $key ); ?>">
				<div class="comparison-card__title">
					<?php if ( 'ms' === $key ) : ?>
						<?php messcut_render_logo( 'white', array( 'linked' => false, 'height' => 30, 'width' => 135 ) ); ?>
					<?php else : ?>
						<?php echo esc_html( $tabs[ $key ] ); ?>
					<?php endif; ?>
				</div>
				<ol>
					<?php foreach ( $items as $i => $item ) : ?>
						<li><i><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></i><?php echo esc_html( $item ); ?></li>
					<?php endforeach; ?>
				</ol>
			</article>
		<?php endforeach; ?>
	</div>
</section>
