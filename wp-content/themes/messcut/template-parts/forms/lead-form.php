<?php
/**
 * Lead form — 2-step wizard.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="lead-form" data-lead-form data-step="1" novalidate>
	<div class="lead-form__steps" data-lead-steps>
		<div class="lead-form__step is-active" data-lead-step="1">
			<p class="lead-form__step-label"><?php esc_html_e( 'Крок 1/2', 'messcut' ); ?></p>
			<div class="lead-form__cluster">
				<div class="lead-form__field">
					<label class="screen-reader-text" for="lead-name"><?php esc_html_e( 'Імʼя', 'messcut' ); ?></label>
					<input id="lead-name" name="name" type="text" required autocomplete="name" placeholder="<?php esc_attr_e( 'Імʼя', 'messcut' ); ?>">
				</div>
				<fieldset class="lead-form__field lead-form__field--split">
					<legend class="screen-reader-text"><?php esc_html_e( 'Тип проєкту', 'messcut' ); ?></legend>
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
				<button class="lead-form__action" type="button" data-lead-next><?php esc_html_e( 'Далі', 'messcut' ); ?></button>
			</div>
		</div>

		<div class="lead-form__step" data-lead-step="2" hidden>
			<p class="lead-form__step-label"><?php esc_html_e( 'Крок 2/2', 'messcut' ); ?></p>
			<div class="lead-form__cluster">
				<div class="lead-form__field">
					<label class="screen-reader-text" for="lead-email"><?php esc_html_e( 'Email', 'messcut' ); ?></label>
					<input id="lead-email" name="email" type="email" required autocomplete="email" placeholder="name@company.com">
				</div>
				<div class="lead-form__field">
					<label class="screen-reader-text" for="lead-phone"><?php esc_html_e( 'Номер телефону', 'messcut' ); ?></label>
					<input id="lead-phone" name="phone" type="tel" required autocomplete="tel" inputmode="tel" placeholder="+380">
				</div>
				<fieldset class="lead-form__field lead-form__field--split">
					<legend class="screen-reader-text"><?php esc_html_e( 'Бажаний спосіб звʼязку', 'messcut' ); ?></legend>
					<div class="lead-form__choices lead-form__choices--triple">
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
				<div class="lead-form__field lead-form__field--hp" aria-hidden="true">
					<label for="lead-website"><?php esc_html_e( 'Website', 'messcut' ); ?></label>
					<input id="lead-website" name="website" type="text" tabindex="-1" autocomplete="off">
				</div>
				<div class="lead-form__nav">
					<button class="lead-form__back" type="button" data-lead-prev><?php esc_html_e( 'Назад', 'messcut' ); ?></button>
					<button class="lead-form__action" type="submit"><?php esc_html_e( 'Надіслати', 'messcut' ); ?></button>
				</div>
			</div>
		</div>

		<div class="lead-form__step lead-form__step--thanks" data-lead-step="thanks" hidden>
			<p class="lead-form__thanks" data-lead-thanks></p>
		</div>
	</div>

	<p class="lead-form__status" data-lead-status hidden></p>
</form>
