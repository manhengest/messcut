<?php
/**
 * Request funnel that highlights a starting service.
 *
 * @package Messcut
 */

$rows = array(
	array(
		array( '1', __( 'не прогнозований ріст', 'messcut' ) ),
		array( '0', __( 'нечітке позиціонування', 'messcut' ) ),
		array( '1', __( 'хаотичне просування', 'messcut' ) ),
		array( '0', __( 'відсутність big idea', 'messcut' ) ),
	),
	array(
		array( '1', __( 'нестача часу на маркетинг', 'messcut' ) ),
		array( '0', __( 'злиття з конкурентами', 'messcut' ) ),
		array( '1', __( 'нестача системних знань', 'messcut' ) ),
	),
	array(
		array( '0', __( 'потрібен бренд, а не магазин', 'messcut' ) ),
		array( '1', __( 'нестача креативу', 'messcut' ) ),
		array( '0', __( 'низька впізнаваність бренду', 'messcut' ) ),
	),
);
$services = array(
	'1' => array(
		'id'    => 'svc-marketing-support',
		'title' => __( 'Маркетинговий супровід', 'messcut' ),
		'text'  => __( 'Формуємо структурну систему просування бізнесу, починаючи від дослідження, бренд- та маркетинг-стратегії та завершуючи креативними концепціями комунікації.', 'messcut' ),
	),
	'0' => array(
		'id'    => 'svc-brand-strategy',
		'title' => __( 'Бренд-стратегія', 'messcut' ),
		'text'  => __( 'Формуємо чітку стратегію: суть бренду та головну ідею, його позицію, місію, контексти з аудиторіями, голос, помітність і естетику.', 'messcut' ),
	),
);
$avatar = MESSCUT_URI . '/assets/img/valeria.jpg';
?>
<section class="section home-path">
	<h2><?php esc_html_e( 'Оберіть запит — і ми підсвітимо послугу, з якої варто почати', 'messcut' ); ?></h2>
	<div class="request-funnel" data-funnel>
		<?php foreach ( $rows as $row ) : ?>
			<div class="request-funnel__row">
				<?php foreach ( $row as $item ) : ?>
					<button type="button" data-service="<?php echo esc_attr( $item[0] ); ?>"><?php echo esc_html( $item[1] ); ?></button>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="path-connector" aria-hidden="true"><span></span></div>
	<div class="path-services">
		<?php foreach ( $services as $key => $service ) : ?>
			<article class="path-service path-service--<?php echo 1 === (int) $key ? 'support' : 'strategy'; ?>" id="service-<?php echo esc_attr( (string) $key ); ?>" data-path-service="<?php echo esc_attr( (string) $key ); ?>">
				<div>
					<small><?php esc_html_e( 'Послуга', 'messcut' ); ?></small>
					<h3><?php echo esc_html( $service['title'] ); ?></h3>
				</div>
				<div class="path-service__body">
					<p><?php echo esc_html( $service['text'] ); ?></p>
					<a href="<?php echo esc_url( messcut_page_url( 'poslugy' ) . '#' . $service['id'] ); ?>"><?php esc_html_e( 'Детальніше', 'messcut' ); ?> <i>↗</i></a>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<a class="consult-link" href="#lead">
		<img src="<?php echo esc_url( $avatar ); ?>" alt="" width="56" height="56">
		<div>
			<b><?php esc_html_e( 'Потрібна консультація?', 'messcut' ); ?></b>
			<small><?php esc_html_e( 'Обговоримо ваш проєкт та знайдемо найкраще рішення', 'messcut' ); ?></small>
		</div>
		<span class="consult-link__go" aria-hidden="true">→</span>
	</a>
</section>
