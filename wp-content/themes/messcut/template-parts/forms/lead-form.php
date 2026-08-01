<?php
/**
 * Lead form — 3-step wizard.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="lead-form" data-lead-form novalidate>
	<div class="lead-form__steps" data-lead-steps>
		<div class="lead-form__step is-active" data-lead-step="1">
			<p class="lead-form__step-label"><?php esc_html_e( 'Крок 1 з 3', 'messcut' ); ?></p>
			<div class="lead-form__field">
				<label for="lead-name"><?php esc_html_e( 'Імʼя', 'messcut' ); ?> *</label>
				<input id="lead-name" name="name" type="text" required autocomplete="name">
			</div>
			<fieldset class="lead-form__field">
				<legend><?php esc_html_e( 'Тип проєкту', 'messcut' ); ?> *</legend>
				<div class="lead-form__choices">
					<label class="lead-form__choice">
						<input type="radio" name="project_type" value="new" required>
						<span><?php esc_html_e( 'Новий бренд', 'messcut' ); ?></span>
					</label>
					<label class="lead-form__choice">
						<input type="radio" name="project_type" value="existing">
						<span><?php esc_html_e( 'Існуючий бренд', 'messcut' ); ?></span>
					</label>
				</div>
			</fieldset>
			<button class="button button--primary" type="button" data-lead-next><?php esc_html_e( 'Далі', 'messcut' ); ?></button>
		</div>

		<div class="lead-form__step" data-lead-step="2" hidden>
			<p class="lead-form__step-label"><?php esc_html_e( 'Крок 2 з 3', 'messcut' ); ?></p>
			<div class="lead-form__field">
				<label for="lead-email"><?php esc_html_e( 'Email', 'messcut' ); ?> *</label>
				<input id="lead-email" name="email" type="email" required autocomplete="email">
			</div>
			<div class="lead-form__field">
				<label for="lead-phone"><?php esc_html_e( 'Номер телефону', 'messcut' ); ?> *</label>
				<input id="lead-phone" name="phone" type="tel" required autocomplete="tel">
			</div>
			<fieldset class="lead-form__field">
				<legend><?php esc_html_e( 'Бажаний спосіб звʼязку', 'messcut' ); ?> *</legend>
				<div class="lead-form__choices">
					<label class="lead-form__choice">
						<input type="radio" name="contact_method" value="email" required>
						<span><?php esc_html_e( 'Email', 'messcut' ); ?></span>
					</label>
					<label class="lead-form__choice">
						<input type="radio" name="contact_method" value="telegram">
						<span><?php esc_html_e( 'Telegram', 'messcut' ); ?></span>
					</label>
					<label class="lead-form__choice">
						<input type="radio" name="contact_method" value="whatsapp">
						<span><?php esc_html_e( 'WhatsApp', 'messcut' ); ?></span>
					</label>
				</div>
			</fieldset>
			<div class="lead-form__nav">
				<button class="button button--secondary" type="button" data-lead-prev><?php esc_html_e( 'Назад', 'messcut' ); ?></button>
				<button class="button button--primary" type="button" data-lead-next><?php esc_html_e( 'Далі', 'messcut' ); ?></button>
			</div>
		</div>

		<div class="lead-form__step" data-lead-step="3" hidden>
			<p class="lead-form__step-label"><?php esc_html_e( 'Крок 3 з 3', 'messcut' ); ?></p>
			<div class="lead-form__field" data-lead-brand-field>
				<label for="lead-brand"><?php esc_html_e( 'Посилання або назва акаунту в соцмережах / домен бренду', 'messcut' ); ?></label>
				<input id="lead-brand" name="brand" type="text">
			</div>
			<div class="lead-form__field">
				<label for="lead-message"><?php esc_html_e( 'Додаткова інформація', 'messcut' ); ?></label>
				<textarea id="lead-message" name="message" rows="4"></textarea>
			</div>
			<div class="lead-form__field lead-form__field--hp" aria-hidden="true">
				<label for="lead-website"><?php esc_html_e( 'Website', 'messcut' ); ?></label>
				<input id="lead-website" name="website" type="text" tabindex="-1" autocomplete="off">
			</div>
			<div class="lead-form__nav">
				<button class="button button--secondary" type="button" data-lead-prev><?php esc_html_e( 'Назад', 'messcut' ); ?></button>
				<button class="button button--accent" type="submit"><?php esc_html_e( 'Надіслати', 'messcut' ); ?></button>
			</div>
		</div>

		<div class="lead-form__step lead-form__step--thanks" data-lead-step="thanks" hidden>
			<p class="lead-form__thanks" data-lead-thanks></p>
		</div>
	</div>

	<p class="lead-form__status" data-lead-status hidden></p>
</form>
