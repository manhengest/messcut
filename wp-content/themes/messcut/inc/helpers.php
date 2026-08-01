<?php
/**
 * Theme helpers.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get ACF option with fallback.
 *
 * @param string $key     Field key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function messcut_get_option( string $key, mixed $default = '' ): mixed {
	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $key, 'option' );
		return ( null === $value || false === $value || '' === $value ) ? $default : $value;
	}
	return $default;
}

/**
 * Get option value for the current language (EN fields prefixed with en_).
 *
 * @param string $key     Field key.
 * @param mixed  $default Default value.
 * @return mixed
 */
function messcut_get_localized_option( string $key, mixed $default = '' ): mixed {
	if ( messcut_is_english() ) {
		$en_value = messcut_get_option( 'en_' . $key, null );
		if ( null !== $en_value && false !== $en_value && '' !== $en_value ) {
			return $en_value;
		}

		$uk_value = messcut_get_option( $key, $default );
		if ( is_string( $uk_value ) && '' !== $uk_value ) {
			return messcut_pll_string( $uk_value, $key );
		}
	}

	return messcut_get_option( $key, $default );
}

/**
 * Get contact phone.
 */
function messcut_phone(): string {
	return (string) messcut_get_option( 'phone', '+38 (095) 477-11-22' );
}

/**
 * Get contact telegram handle.
 */
function messcut_telegram(): string {
	return (string) messcut_get_option( 'telegram', '@messcutstrategy' );
}

/**
 * Get contact email.
 */
function messcut_email(): string {
	return (string) messcut_get_option( 'email', 'admin@messcut.com' );
}

/**
 * Get CTA label.
 *
 * @param string $type discuss|consult.
 */
function messcut_cta_label( string $type = 'discuss' ): string {
	$key = 'discuss' === $type ? 'cta_discuss_label' : 'cta_consult_label';
	$default = 'discuss' === $type
		? __( 'Обговорити проєкт', 'messcut' )
		: __( 'Отримати ознайомчу консультацію', 'messcut' );
	return (string) messcut_get_localized_option( $key, $default );
}

/**
 * Render stats section from options.
 *
 * @param array<string, mixed> $args Template args (stats, title).
 */
function messcut_render_stats( array $args = array() ): void {
	$stats = $args['stats'] ?? messcut_get_localized_option( 'stats', array() );
	if ( empty( $stats ) || ! is_array( $stats ) ) {
		$stats = array(
			array( 'value' => '', 'label' => __( 'роки практик та нескінченних навчань для підвищення кваліфікації', 'messcut' ) ),
			array( 'value' => '30+', 'label' => __( 'бренд-стратегій', 'messcut' ) ),
			array( 'value' => '50+', 'label' => __( 'співпраць', 'messcut' ) ),
			array( 'value' => '85%', 'label' => __( 'клієнтів приходять за рекомендацією', 'messcut' ) ),
		);
	}
	$args['stats'] = $stats;
	get_template_part( 'template-parts/sections/stats', null, $args );
}

/**
 * Render values line from options.
 */
function messcut_render_values(): void {
	$values = messcut_get_localized_option( 'home_values', array() );
	if ( empty( $values ) || ! is_array( $values ) ) {
		$values = array(
			array( 'text' => __( 'етичність', 'messcut' ) ),
			array( 'text' => __( 'мотивація', 'messcut' ) ),
			array( 'text' => __( 'структура', 'messcut' ) ),
			array( 'text' => __( 'любов до справи', 'messcut' ) ),
		);
	}
	get_template_part( 'template-parts/sections/values', null, array( 'values' => $values ) );
}

/**
 * Render flexible case sections.
 */
function messcut_render_case_sections(): void {
	if ( ! function_exists( 'have_rows' ) || ! have_rows( 'case_sections' ) ) {
		return;
	}

	while ( have_rows( 'case_sections' ) ) {
		the_row();
		$layout = get_row_layout();
		get_template_part( 'template-parts/sections/case', $layout );
	}
}

/**
 * Service card description for grid listings.
 *
 * @param int $post_id Post ID.
 */
function messcut_get_service_card_description( int $post_id = 0 ): string {
	$post_id = $post_id ?: get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	$short = function_exists( 'get_field' ) ? get_field( 'short_description', $post_id ) : '';
	if ( is_string( $short ) && '' !== trim( $short ) ) {
		return trim( $short );
	}

	$excerpt = get_post_field( 'post_excerpt', $post_id );
	if ( is_string( $excerpt ) && '' !== trim( $excerpt ) ) {
		return trim( $excerpt );
	}

	return '';
}

/**
 * Get services query.
 *
 * @param int $limit Posts limit.
 * @return WP_Query
 */
function messcut_get_services_query( int $limit = -1 ): WP_Query {
	return new WP_Query( array(
		'post_type'      => 'service',
		'posts_per_page' => $limit,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'post_status'    => 'publish',
	) );
}

/**
 * Get case studies query.
 *
 * @param int $limit Posts limit.
 * @return WP_Query
 */
function messcut_get_cases_query( int $limit = -1 ): WP_Query {
	return new WP_Query( array(
		'post_type'      => 'case_study',
		'posts_per_page' => $limit,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'post_status'    => 'publish',
	) );
}

/**
 * Render lead form.
 *
 * @param array<string, mixed> $args Form args.
 */
function messcut_render_lead_form( array $args = array() ): void {
	get_template_part( 'template-parts/forms/lead-form', null, $args );
}

/**
 * Get contact whatsapp number or link.
 */
function messcut_whatsapp(): string {
	return (string) messcut_get_option( 'whatsapp', '' );
}

/**
 * Build telegram URL from handle.
 */
function messcut_telegram_url(): string {
	$handle = messcut_telegram();
	$handle = ltrim( str_replace( 'https://t.me/', '', $handle ), '@' );
	return 'https://t.me/' . rawurlencode( $handle );
}

/**
 * Build whatsapp URL from phone.
 */
function messcut_whatsapp_url(): string {
	$phone = preg_replace( '/\D+/', '', messcut_whatsapp() );
	if ( '' === $phone ) {
		return '';
	}
	return 'https://wa.me/' . $phone;
}

/**
 * Get page URL by slug.
 */
function messcut_page_url( string $slug ): string {
	$page = get_page_by_path( $slug );
	if ( $page ) {
		return (string) get_permalink( $page );
	}
	return home_url( '/' . $slug . '/' );
}

/**
 * Approach page URL.
 */
function messcut_approach_url(): string {
	return messcut_page_url( 'dosvid' );
}

/**
 * Cases archive URL.
 */
function messcut_cases_archive_url(): string {
	return (string) get_post_type_archive_link( 'case_study' );
}

/**
 * Insights (articles) archive URL.
 */
function messcut_insights_archive_url(): string {
	return (string) get_post_type_archive_link( 'article' );
}

/**
 * Get ACF field for current or given post.
 *
 * @param string   $key     Field name.
 * @param int|null $post_id Post ID.
 * @return mixed
 */
function messcut_get_acf( string $key, ?int $post_id = null ): mixed {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}
	return get_field( $key, $post_id ?: get_the_ID() );
}

/**
 * Render a titled WYSIWYG block if content exists.
 */
function messcut_render_content_block( string $title, mixed $content ): void {
	if ( empty( $content ) ) {
		return;
	}
	?>
	<section class="section content-block">
		<div class="container container--narrow">
			<?php if ( $title ) : ?>
				<h2><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<div class="entry-content"><?php echo wp_kses_post( $content ); ?></div>
		</div>
	</section>
	<?php
}

/**
 * Render mid-page CTA button linking to lead form.
 */
function messcut_render_mid_cta( string $label = '' ): void {
	$label = $label ?: messcut_cta_label( 'discuss' );
	?>
	<section class="section mid-cta">
		<div class="container container--narrow">
			<p><a class="button button--primary" href="#lead-form"><?php echo esc_html( $label ); ?></a></p>
		</div>
	</section>
	<?php
}

/**
 * Get article type term IDs for insights block.
 *
 * @param int|null $post_id Post ID.
 * @return int[]
 */
function messcut_get_insights_type_ids( ?int $post_id = null ): array {
	$types = messcut_get_acf( 'insights_article_types', $post_id );
	if ( empty( $types ) || ! is_array( $types ) ) {
		return array();
	}
	return array_map( 'intval', $types );
}

/**
 * Render insights tiles section.
 *
 * @param array<string, mixed> $args Args.
 */
function messcut_render_insights_tiles( array $args = array() ): void {
	get_template_part( 'template-parts/sections/insights-tiles', null, $args );
}

/**
 * Render contact channels block.
 */
function messcut_render_contact_channels(): void {
	get_template_part( 'template-parts/sections/contact-channels' );
}

/**
 * Render services comparison table.
 */
function messcut_render_services_comparison(): void {
	get_template_part( 'template-parts/sections/services-comparison' );
}

/**
 * Normalize FAQ repeater rows.
 *
 * @param mixed $items Raw repeater rows.
 * @return array<int, array{question: string, answer: string}>
 */
function messcut_normalize_faq_items( mixed $items ): array {
	if ( empty( $items ) || ! is_array( $items ) ) {
		return array();
	}

	$normalized = array();

	foreach ( $items as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$question = trim( (string) ( $row['question'] ?? '' ) );
		$answer   = (string) ( $row['answer'] ?? '' );

		if ( '' === $question ) {
			continue;
		}

		$normalized[] = array(
			'question' => $question,
			'answer'   => $answer,
		);
	}

	return $normalized;
}

/**
 * Get FAQ items for the current context.
 *
 * @param array<string, mixed> $args Args: source (home|post|auto), post_id.
 * @return array<int, array{question: string, answer: string}>
 */
function messcut_get_faq_items( array $args = array() ): array {
	$source  = $args['source'] ?? 'auto';
	$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : 0;

	if ( 'home' === $source || ( 'auto' === $source && is_front_page() ) ) {
		$items = messcut_normalize_faq_items( messcut_get_localized_option( 'home_faq', array() ) );
		if ( empty( $items ) ) {
			$items = messcut_get_faq_seed_data( messcut_is_english() ? 'en' : 'uk' );
		}
		return $items;
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( $post_id ) {
		$items = messcut_normalize_faq_items( messcut_get_acf( 'faq_items', $post_id ) );
		if ( ! empty( $items ) ) {
			return $items;
		}
	}

	return array();
}

/**
 * Get FAQ section title for the current context.
 *
 * @param array<string, mixed> $args Args: source (home|post|auto), post_id, title.
 */
function messcut_get_faq_title( array $args = array() ): string {
	$default = __( 'FAQ', 'messcut' );

	if ( ! empty( $args['title'] ) ) {
		return (string) $args['title'];
	}

	$source  = $args['source'] ?? 'auto';
	$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : 0;

	if ( 'home' === $source || ( 'auto' === $source && is_front_page() ) ) {
		$title = messcut_get_localized_option( 'home_faq_title', $default );
		return is_string( $title ) && '' !== trim( $title ) ? $title : $default;
	}

	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( $post_id ) {
		$title = messcut_get_acf( 'faq_title', $post_id );
		if ( is_string( $title ) && '' !== trim( $title ) ) {
			return $title;
		}
	}

	return $default;
}

/**
 * Render FAQ accordion (hidden when no items).
 *
 * @param array<string, mixed> $args Args: source, post_id, title, items.
 */
function messcut_render_faq( array $args = array() ): void {
	$items = $args['items'] ?? messcut_get_faq_items( $args );
	if ( empty( $items ) ) {
		return;
	}

	get_template_part(
		'template-parts/sections/faq',
		null,
		array(
			'items' => $items,
			'title' => messcut_get_faq_title( $args ),
		)
	);
}

/**
 * Render approach CTA link.
 */
function messcut_render_approach_cta(): void {
	get_template_part( 'template-parts/sections/approach-cta' );
}

/**
 * Fallback primary menu.
 */
function messcut_fallback_primary_menu(): void {
	$items = array(
		home_url( '/' )                    => __( 'Головна', 'messcut' ),
		messcut_page_url( 'poslugy' )      => __( 'Послуги', 'messcut' ),
		messcut_cases_archive_url()        => __( 'Кейси', 'messcut' ),
		messcut_approach_url()             => __( 'Досвід та підхід', 'messcut' ),
		messcut_insights_archive_url()     => __( 'Інсайти', 'messcut' ),
	);
	echo '<ul class="primary-menu">';
	foreach ( $items as $url => $label ) {
		printf(
			'<li><a href="%s">%s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}
	echo '</ul>';
}

/**
 * Render the Messcut logo.
 *
 * @param string               $variant black|white.
 * @param array<string, mixed> $args    Optional: class, width, height.
 */
function messcut_render_logo( string $variant = 'black', array $args = array() ): void {
	$variant = 'white' === $variant ? 'white' : 'black';
	$class   = isset( $args['class'] ) ? (string) $args['class'] : 'site-logo';
	$width   = isset( $args['width'] ) ? (int) $args['width'] : 160;
	$height  = isset( $args['height'] ) ? (int) $args['height'] : 35;

	$filename = 'logo-' . $variant . '.svg';
	$path     = MESSCUT_DIR . '/assets/img/' . $filename;
	$url      = MESSCUT_URI . '/assets/img/' . $filename;

	if ( ! file_exists( $path ) ) {
		$filename = 'logo-' . $variant . '.png';
		$path     = MESSCUT_DIR . '/assets/img/' . $filename;
		$url      = MESSCUT_URI . '/assets/img/' . $filename;
	}

	if ( ! file_exists( $path ) ) {
		printf(
			'<a class="%1$s site-title" href="%2$s">%3$s</a>',
			esc_attr( $class ),
			esc_url( home_url( '/' ) ),
			esc_html( get_bloginfo( 'name' ) )
		);
		return;
	}

	printf(
		'<a class="%1$s" href="%2$s" rel="home"><img class="site-logo__img" src="%3$s" alt="%4$s" width="%5$d" height="%6$d" decoding="async" /></a>',
		esc_attr( $class ),
		esc_url( home_url( '/' ) ),
		esc_url( $url ),
		esc_attr( get_bloginfo( 'name' ) ),
		$width,
		$height
	);
}
