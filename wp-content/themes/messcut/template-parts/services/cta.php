<?php
/**
 * Services closing CTA.
 *
 * @package Messcut
 */

$photo = MESSCUT_URI . '/assets/img/valeria.jpg';
?>
<section class="services-cta" id="lead-form">
	<div class="services-cta__person">
		<img src="<?php echo esc_url( $photo ); ?>" alt="<?php esc_attr_e( 'Валерія, засновниця Messcut', 'messcut' ); ?>">
		<div><b><?php esc_html_e( 'Валерія', 'messcut' ); ?></b><br><span><?php esc_html_e( 'Засновниця Messcut', 'messcut' ); ?></span></div>
	</div>
	<h2><?php echo wp_kses( __( 'Не знаєте, з чого <em>почати?</em>', 'messcut' ), array( 'em' => array() ) ); ?></h2>
	<p><?php esc_html_e( 'Команда Messcut допоможе визначити, яка послуга підходить вам.', 'messcut' ); ?></p>
	<a class="cta" href="<?php echo esc_url( messcut_telegram_url() ); ?>"><?php esc_html_e( 'Забронювати діагностичну консультацію', 'messcut' ); ?> <b>→</b></a>
</section>
