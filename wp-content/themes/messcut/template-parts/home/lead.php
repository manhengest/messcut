<?php
/**
 * Home lead section. Channel links sit under the form on small screens.
 *
 * @package Messcut
 */
?>
<section class="section lead-section" id="lead">
	<div class="lead-section__copy">
		<h2><?php esc_html_e( 'Отримайте план розвитку вашого бренду', 'messcut' ); ?></h2>
		<p class="lead-section__format"><span><?php esc_html_e( 'У форматі 30-хв стратегічної зустрічі', 'messcut' ); ?></span></p>
	</div>
	<?php get_template_part( 'template-parts/components/lead-form' ); ?>
	<div class="lead-section__channels">
		<div class="channel-list__label"><?php esc_html_e( 'Або звʼяжіться з нами самостійно', 'messcut' ); ?></div>
		<div class="channel-list">
			<a href="<?php echo esc_url( messcut_telegram_url() ); ?>">Telegram</a>
			<a href="<?php echo esc_url( messcut_whatsapp_url() ); ?>">WhatsApp</a>
			<a href="<?php echo esc_url( 'mailto:' . messcut_email() ); ?>">Email</a>
		</div>
	</div>
</section>
