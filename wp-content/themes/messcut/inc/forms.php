<?php
/**
 * REST API lead form.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register lead form REST route.
 */
function messcut_register_lead_route(): void {
	register_rest_route(
		'messcut/v1',
		'/lead',
		array(
			'methods'             => 'POST',
			'callback'            => 'messcut_handle_lead_submission',
			'permission_callback' => 'messcut_lead_permission_check',
			'args'                => array(
				'name'            => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => static fn( $v ) => '' !== trim( (string) $v ),
				),
				'project_type'    => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => static fn( $v ) => in_array( (string) $v, array( 'new', 'existing' ), true ),
				),
				'phone'           => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => static fn( $v ) => '' !== trim( (string) $v ),
				),
				'email'           => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_email',
					'validate_callback' => static fn( $v ) => is_email( (string) $v ),
				),
				'contact_method'  => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => static fn( $v ) => in_array( (string) $v, array( 'email', 'telegram', 'whatsapp' ), true ),
				),
				'brand'           => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => '',
				),
				'message'         => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_textarea_field',
					'default'           => '',
				),
				'website'         => array(
					'required'          => false,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => '',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'messcut_register_lead_route' );

/**
 * Permission check: nonce + honeypot.
 *
 * @param WP_REST_Request $request Request.
 */
function messcut_lead_permission_check( WP_REST_Request $request ): bool|WP_Error {
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return new WP_Error(
			'messcut_invalid_nonce',
			__( 'Недійсний запит. Оновіть сторінку та спробуйте знову.', 'messcut' ),
			array( 'status' => 403 )
		);
	}

	if ( '' !== trim( (string) $request->get_param( 'website' ) ) ) {
		return new WP_Error(
			'messcut_spam',
			__( 'Запит відхилено.', 'messcut' ),
			array( 'status' => 400 )
		);
	}

	return true;
}

/**
 * Human-readable project type label.
 */
function messcut_project_type_label( string $type ): string {
	return 'existing' === $type
		? __( 'Існуючий бренд', 'messcut' )
		: __( 'Новий бренд', 'messcut' );
}

/**
 * Human-readable contact method label.
 */
function messcut_contact_method_label( string $method ): string {
	$labels = array(
		'email'    => __( 'Email', 'messcut' ),
		'telegram' => __( 'Telegram', 'messcut' ),
		'whatsapp' => __( 'WhatsApp', 'messcut' ),
	);
	return $labels[ $method ] ?? $method;
}

/**
 * Handle lead submission.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function messcut_handle_lead_submission( WP_REST_Request $request ): WP_REST_Response|WP_Error {
	$name            = (string) $request->get_param( 'name' );
	$project_type    = (string) $request->get_param( 'project_type' );
	$phone           = (string) $request->get_param( 'phone' );
	$email           = (string) $request->get_param( 'email' );
	$contact_method  = (string) $request->get_param( 'contact_method' );
	$brand           = (string) $request->get_param( 'brand' );
	$message         = (string) $request->get_param( 'message' );

	$recipient = messcut_get_option( 'form_recipient_email', messcut_email() );
	if ( ! is_email( $recipient ) ) {
		$recipient = get_option( 'admin_email' );
	}

	$subject = sprintf(
		/* translators: %s: submitter name */
		__( 'Нова заявка з сайту MESSCUT — %s', 'messcut' ),
		$name
	);

	$body_lines = array(
		sprintf( '%s: %s', __( 'Імʼя', 'messcut' ), $name ),
		sprintf( '%s: %s', __( 'Тип проєкту', 'messcut' ), messcut_project_type_label( $project_type ) ),
		sprintf( '%s: %s', __( 'Телефон', 'messcut' ), $phone ),
		sprintf( '%s: %s', __( 'Email', 'messcut' ), $email ),
		sprintf( '%s: %s', __( 'Спосіб звʼязку', 'messcut' ), messcut_contact_method_label( $contact_method ) ),
	);
	if ( '' !== $brand ) {
		$body_lines[] = sprintf( '%s: %s', __( 'Бренд / посилання', 'messcut' ), $brand );
	}
	if ( '' !== $message ) {
		$body_lines[] = sprintf( '%s: %s', __( 'Додаткова інформація', 'messcut' ), $message );
	}

	$sent = wp_mail( $recipient, $subject, implode( "\n", $body_lines ) );

	messcut_store_lead( $name, $project_type, $phone, $email, $contact_method, $brand, $message );

	if ( ! $sent ) {
		return new WP_Error(
			'messcut_mail_failed',
			__( 'Не вдалося надіслати заявку. Спробуйте пізніше або звʼяжіться з нами напряму.', 'messcut' ),
			array( 'status' => 500 )
		);
	}

	return new WP_REST_Response(
		array(
			'success' => true,
			'message' => __( 'Дякуємо, Валерія найближчим часом звʼяжеться з вами', 'messcut' ),
		),
		200
	);
}

/**
 * Store lead as private CPT entry.
 */
function messcut_store_lead( string $name, string $project_type, string $phone, string $email, string $contact_method, string $brand, string $message ): void {
	$content_parts = array(
		sprintf( '<p><strong>%s:</strong> %s</p>', esc_html__( 'Тип проєкту', 'messcut' ), esc_html( messcut_project_type_label( $project_type ) ) ),
		sprintf( '<p><strong>%s:</strong> %s</p>', esc_html__( 'Телефон', 'messcut' ), esc_html( $phone ) ),
		sprintf( '<p><strong>%s:</strong> %s</p>', esc_html__( 'Email', 'messcut' ), esc_html( $email ) ),
		sprintf( '<p><strong>%s:</strong> %s</p>', esc_html__( 'Спосіб звʼязку', 'messcut' ), esc_html( messcut_contact_method_label( $contact_method ) ) ),
	);
	if ( '' !== $brand ) {
		$content_parts[] = sprintf( '<p><strong>%s:</strong> %s</p>', esc_html__( 'Бренд / посилання', 'messcut' ), esc_html( $brand ) );
	}
	if ( '' !== $message ) {
		$content_parts[] = sprintf( '<p><strong>%s:</strong> %s</p>', esc_html__( 'Додаткова інформація', 'messcut' ), esc_html( $message ) );
	}

	wp_insert_post( array(
		'post_type'    => 'lead',
		'post_status'  => 'private',
		'post_title'   => $name . ' — ' . current_time( 'Y-m-d H:i' ),
		'post_content' => implode( "\n", $content_parts ),
	) );
}
