<?php
/**
 * Two-step lead form. Posts to the REST lead endpoint.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="lead-form" data-lead-form novalidate>
	<div data-step="1">
		<div class="lead-form__step"><?php esc_html_e( 'Крок 1/2', 'messcut' ); ?></div>
		<div class="lead-form__bar"><i style="width:50%"></i></div>
		<div class="lead-form__field">
			<label for="lead-name"><?php esc_html_e( 'Імʼя', 'messcut' ); ?></label>
			<input id="lead-name" name="name" type="text" autocomplete="name" required>
		</div>
		<div class="lead-form__field">
			<span><?php esc_html_e( 'Тип проєкту', 'messcut' ); ?></span>
			<div class="lead-form__options">
				<label><input type="radio" name="project_type" value="new" checked><span><?php esc_html_e( 'Новий бренд', 'messcut' ); ?></span></label>
				<label><input type="radio" name="project_type" value="existing"><span><?php esc_html_e( 'Існуючий бренд', 'messcut' ); ?></span></label>
			</div>
		</div>
		<div class="lead-form__actions">
			<button class="button" type="button" data-lead-next><?php esc_html_e( 'Далі', 'messcut' ); ?></button>
		</div>
	</div>
	<div data-step="2" hidden>
		<div class="lead-form__step"><?php esc_html_e( 'Крок 2/2', 'messcut' ); ?></div>
		<div class="lead-form__bar"><i style="width:100%"></i></div>
		<div class="lead-form__field">
			<label for="lead-email">Email</label>
			<input id="lead-email" name="email" type="email" autocomplete="email" required>
		</div>
		<div class="lead-form__field">
			<label for="lead-phone"><?php esc_html_e( 'Номер телефону', 'messcut' ); ?></label>
			<input id="lead-phone" name="phone" type="tel" autocomplete="tel" required>
		</div>
		<div class="lead-form__field">
			<span><?php esc_html_e( 'Бажаний спосіб звʼязку', 'messcut' ); ?></span>
			<div class="lead-form__options">
				<label><input type="radio" name="contact_method" value="email" checked><span>Email</span></label>
				<label><input type="radio" name="contact_method" value="telegram"><span>Telegram</span></label>
				<label><input type="radio" name="contact_method" value="whatsapp"><span>WhatsApp</span></label>
			</div>
		</div>
		<div class="lead-form__actions">
			<button class="button button--outline" type="button" data-lead-back><?php esc_html_e( 'Назад', 'messcut' ); ?></button>
			<button class="button" type="submit"><?php esc_html_e( 'Надіслати', 'messcut' ); ?></button>
		</div>
	</div>
	<div data-step="done" hidden>
		<h3><?php esc_html_e( 'Дякуємо!', 'messcut' ); ?></h3>
		<p class="lead-form__step" data-lead-success></p>
	</div>
	<p class="lead-form__error" data-lead-error hidden></p>
	<p class="lead-form__hp" aria-hidden="true">
		<label for="lead-website"><?php esc_html_e( 'Сайт', 'messcut' ); ?></label>
		<input id="lead-website" name="website" type="text" tabindex="-1" autocomplete="off">
	</p>
</form>
