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
		? __( 'Отримати план розвитку', 'messcut' )
		: __( 'Отримати ознайомчу консультацію', 'messcut' );
	return (string) messcut_get_localized_option( $key, $default );
}

/**
 * Default "why us" mosaic items (value, label, optional image).
 *
 * @return array<string, array<string, mixed>>
 */
function messcut_get_why_stats_defaults(): array {
	return array(
		'recommend' => array(
			'value'     => '94%',
			'label'     => __( 'клієнтів радять нас своїм колегам', 'messcut' ),
			'image'     => 'why-1.jpg',
			'width'     => 206,
			'height'    => 290,
			'image_pos' => 'after',
		),
		'partners'  => array(
			'value'     => '50+',
			'label'     => __( 'стратегічних співпраць з великими та малими брендами в різних нішах', 'messcut' ),
			'image'     => 'why-2.jpg',
			'width'     => 288,
			'height'    => 102,
			'image_pos' => 'after',
		),
		'nonstop'   => array(
			'value'     => 'NON-STOP',
			'label'     => __( 'NON-STOP підвищення кваліфікації та вивчення досліджень', 'messcut' ),
			'image'     => 'why-3.jpg',
			'width'     => 204,
			'height'    => 184,
			'image_pos' => 'after',
		),
		'years'     => array(
			'value'     => '6+',
			'label'     => __( 'років практики', 'messcut' ),
			'image'     => 'why-4.jpg',
			'width'     => 220,
			'height'    => 126,
			'image_pos' => 'before',
		),
		'ratio'     => array(
			'value'     => '1:2',
			'label'     => __( '1 маркетолог = до 2-х проєктів для глибокого занурення у ваш бізнес', 'messcut' ),
			'image'     => '',
			'image_pos' => '',
		),
	);
}

/**
 * Map a CMS stat row onto a mosaic slot key.
 *
 * @param array<string, mixed> $row Repeater row.
 */
function messcut_match_stat_slot( array $row ): string {
	$value = strtoupper( (string) preg_replace( '/\s+/', '', (string) ( $row['value'] ?? '' ) ) );
	$label = (string) ( $row['label'] ?? '' );

	if ( str_contains( $value, '94' ) ) {
		return 'recommend';
	}
	if ( str_contains( $value, '50' ) ) {
		return 'partners';
	}
	if ( str_contains( $value, 'NONSTOP' ) || str_contains( $value, 'NON-STOP' ) ) {
		return 'nonstop';
	}
	if ( str_contains( $value, '6+' ) || '6' === $value ) {
		return 'years';
	}
	if ( str_contains( $value, '1:2' ) ) {
		return 'ratio';
	}

	$label_l = function_exists( 'mb_strtolower' ) ? mb_strtolower( $label ) : strtolower( $label );
	if ( '' === $value && (
		str_contains( $label_l, 'non-stop' )
		|| str_contains( $label_l, 'nonstop' )
		|| str_contains( $label_l, 'підвищення кваліфікації' )
		|| str_contains( $label_l, 'professional development' )
	) ) {
		return 'nonstop';
	}

	return '';
}

/**
 * Skip stale CMS labels that predate the designed mosaic copy.
 *
 * @param string $slot  Mosaic slot key.
 * @param string $label CMS label.
 */
function messcut_is_legacy_stat_label( string $slot, string $label ): bool {
	if ( 'ratio' !== $slot ) {
		return false;
	}

	$legacy = array(
		'маркетолог = до 2-х проєктів для глибокого занурення у ваш бізнес',
		'marketer to up to 2 projects for deep business immersion',
	);

	return in_array( $label, $legacy, true );
}

/**
 * Mosaic items: design defaults, overlaid with CMS stats when present.
 *
 * @param array<int, array<string, mixed>> $stats Optional CMS rows.
 * @return array<string, array<string, mixed>>
 */
function messcut_get_why_stats( array $stats = array() ): array {
	$items = messcut_get_why_stats_defaults();

	if ( empty( $stats ) ) {
		$stats = messcut_get_localized_option( 'stats', array() );
	}

	if ( ! is_array( $stats ) ) {
		return $items;
	}

	foreach ( $stats as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$slot = messcut_match_stat_slot( $row );
		if ( '' === $slot || ! isset( $items[ $slot ] ) ) {
			continue;
		}
		$value = trim( (string) ( $row['value'] ?? '' ) );
		$label = trim( (string) ( $row['label'] ?? '' ) );
		if ( '' !== $value ) {
			$items[ $slot ]['value'] = $value;
		}
		if ( '' !== $label && ! messcut_is_legacy_stat_label( $slot, $label ) ) {
			$items[ $slot ]['label'] = $label;
		}
	}

	return $items;
}

/**
 * Render stats section from options.
 *
 * @param array<string, mixed> $args Template args (stats, title, items).
 */
function messcut_render_stats( array $args = array() ): void {
	$args['items'] = $args['items'] ?? messcut_get_why_stats( isset( $args['stats'] ) && is_array( $args['stats'] ) ? $args['stats'] : array() );
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
 * Theme mock image URL (until client photography arrives).
 */
function messcut_get_mock_image_url(): string {
	$path = get_template_directory() . '/assets/img/tore.png';
	if ( ! is_readable( $path ) ) {
		return '';
	}

	return get_template_directory_uri() . '/assets/img/tore.png';
}

/**
 * Render a post thumbnail or mock fallback image.
 *
 * @param string               $size    Registered image size.
 * @param int|null             $post_id Post ID. Defaults to the current post.
 * @param array<string, mixed> $args    Optional img attributes.
 */
function messcut_render_post_thumbnail( string $size = 'medium_large', ?int $post_id = null, array $args = array() ): void {
	$post_id = $post_id ?: (int) get_the_ID();

	if ( $post_id && has_post_thumbnail( $post_id ) ) {
		echo get_the_post_thumbnail(
			$post_id,
			$size,
			array_merge(
				array(
					'loading'  => 'lazy',
					'decoding' => 'async',
				),
				$args
			)
		);
		return;
	}

	$url = messcut_get_mock_image_url();
	if ( '' === $url ) {
		echo '<span class="card__media--placeholder" aria-hidden="true"></span>';
		return;
	}

	$alt     = $args['alt'] ?? ( $post_id ? get_the_title( $post_id ) : '' );
	$class   = isset( $args['class'] ) ? ' class="' . esc_attr( (string) $args['class'] ) . '"' : '';
	$loading = isset( $args['loading'] ) ? ' loading="' . esc_attr( (string) $args['loading'] ) . '"' : ' loading="lazy"';

	printf(
		'<img src="%s" alt="%s" width="1200" height="750" decoding="async"%s%s />',
		esc_url( $url ),
		esc_attr( (string) $alt ),
		$class,
		$loading
	);
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
	$value = (string) messcut_get_option( 'whatsapp', '' );
	if ( '' !== $value ) {
		return $value;
	}

	return messcut_phone();
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
 * Avatar URL for the compact consultation CTA.
 */
function messcut_consult_cta_avatar_url(): string {
	$image = messcut_get_option( 'consult_cta_avatar' );
	if ( is_array( $image ) && ! empty( $image['url'] ) ) {
		return (string) $image['url'];
	}
	if ( is_string( $image ) && '' !== $image ) {
		return $image;
	}

	$path = MESSCUT_DIR . '/assets/img/consult-avatar.png';
	if ( ! is_readable( $path ) ) {
		return '';
	}

	return MESSCUT_URI . '/assets/img/consult-avatar.png?v=' . (string) filemtime( $path );
}

/**
 * Render compact consultation CTA linking to the lead form.
 *
 * @param array<string, mixed> $args Template args (title, text, avatar).
 */
function messcut_render_consult_cta( array $args = array() ): void {
	get_template_part( 'template-parts/sections/consult-cta', null, $args );
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
 * Render partner logos marquee.
 *
 * @param array<string, mixed> $args Template args.
 */
function messcut_render_partner_logos( array $args = array() ): void {
	get_template_part( 'template-parts/sections/partner-logos', null, $args );
}

/**
 * Resolve a bundled partner logo URL from theme assets.
 *
 * @param string $logo_file Filename inside assets/img/partners/.
 */
function messcut_get_partner_logo_asset_url( string $logo_file ): string {
	$logo_file = ltrim( $logo_file, '/' );
	if ( '' === $logo_file || str_contains( $logo_file, '..' ) ) {
		return '';
	}

	$path = get_template_directory() . '/assets/img/partners/' . $logo_file;
	if ( ! is_readable( $path ) ) {
		return '';
	}

	return get_template_directory_uri() . '/assets/img/partners/' . $logo_file;
}

/**
 * Slug for a partner brand name (marquee data-brand + mobile hide list).
 */
function messcut_partner_brand_slug( string $name ): string {
	$normalized = mb_strtolower( trim( $name ), 'UTF-8' );
	$normalized = str_replace( array( '\'', '’', '.' ), '', $normalized );
	$aliases    = array(
		'аквамарин'  => 'akvamarin',
		'aquamarine' => 'akvamarin',
		'akvamarin'  => 'akvamarin',
		'md fashion' => 'md-fashion',
		'inshur'     => 'inzhur',
		'inzhur'     => 'inzhur',
	);

	if ( isset( $aliases[ $normalized ] ) ) {
		return $aliases[ $normalized ];
	}

	$slug = sanitize_title( $name );
	return '' !== $slug ? $slug : 'brand';
}

/**
 * Get partner brands for marquee.
 *
 * @return array<int, array{name: string, logo_url: string}>
 */
function messcut_get_partner_brands(): array {
	$rows = messcut_get_localized_option( 'partner_brands', array() );
	if ( empty( $rows ) || ! is_array( $rows ) ) {
		$rows = messcut_get_partner_brands_seed();
	}

	$theme_logos = messcut_get_partner_brand_logo_files();
	$brands      = array();

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$name = trim( (string) ( $row['name'] ?? '' ) );
		if ( '' === $name ) {
			continue;
		}
		$logo_url = '';
		$logo     = $row['logo'] ?? null;
		if ( is_array( $logo ) && ! empty( $logo['url'] ) ) {
			$logo_url = (string) $logo['url'];
		} elseif ( is_numeric( $logo ) ) {
			$logo_url = (string) wp_get_attachment_image_url( (int) $logo, 'medium' );
		}

		if ( '' === $logo_url ) {
			$logo_file = (string) ( $row['logo_file'] ?? ( $theme_logos[ $name ] ?? '' ) );
			if ( '' !== $logo_file ) {
				$logo_url = messcut_get_partner_logo_asset_url( $logo_file );
			}
		}

		$brands[] = array(
			'name'     => $name,
			'logo_url' => $logo_url,
		);
	}

	return $brands;
}

/**
 * Agency comparison section title. Ignores the pre-redesign long headline.
 */
function messcut_get_agency_comparison_title(): string {
	$default = __( 'Порівняйте', 'messcut' );
	$title   = messcut_get_localized_option( 'agency_comparison_title', $default );
	if ( ! is_string( $title ) || '' === trim( $title ) ) {
		return $default;
	}

	$title  = trim( $title );
	$legacy = array(
		'Порівняйте нас з іншими агенціями або власним наймом маркетолога',
		'Compare us with other agencies or hiring in-house',
	);

	return in_array( $title, $legacy, true ) ? $default : $title;
}

/**
 * Agency comparison tab panels (Figma node 131:74).
 *
 * @return array<int, array{id: string, label: string, logo: bool, heading: string, points: array<int, string>}>
 */
function messcut_get_agency_comparison_tabs(): array {
	return array(
		array(
			'id'      => 'messcut',
			'label'   => __( 'Messcut', 'messcut' ),
			'logo'    => true,
			'heading' => '',
			'points'  => array(
				__( 'Максимум 2 проєкти на спеціаліста', 'messcut' ),
				__( 'Результат і бізнес-KPI, а не години', 'messcut' ),
				__( "Багаторівневий контроль якості.\nВнутрішня ревізія + регулярні зовнішні аудити", 'messcut' ),
				__( 'Рішення на основі досліджень і перевірених методологій', 'messcut' ),
			),
		),
		array(
			'id'      => 'inhouse',
			'label'   => __( 'Власний найм', 'messcut' ),
			'logo'    => false,
			'heading' => __( 'Власний найм', 'messcut' ),
			'points'  => array(
				__( 'Один бренд — залежить від досвіду команди', 'messcut' ),
				__( 'Зарплата, податки й внутрішні процеси', 'messcut' ),
				__( 'Знання залишаються в компанії', 'messcut' ),
				__( 'Рішення з внутрішніми упередженнями', 'messcut' ),
			),
		),
		array(
			'id'      => 'agency',
			'label'   => __( 'Інші агенції', 'messcut' ),
			'logo'    => false,
			'heading' => __( 'Інші агенції', 'messcut' ),
			'points'  => array(
				__( 'Багато клієнтів на спеціаліста', 'messcut' ),
				__( 'Фокус на кампаніях і креативі', 'messcut' ),
				__( 'Дослідження опційно / додатково', 'messcut' ),
				__( 'Знання залишаються в агенції', 'messcut' ),
			),
		),
	);
}

/**
 * Get agency comparison rows.
 *
 * @return array<int, array{criterion: string, messcut: string, agency: string, inhouse: string}>
 */
function messcut_get_agency_comparison_rows(): array {
	$rows = messcut_get_localized_option( 'agency_comparison_rows', array() );
	if ( empty( $rows ) || ! is_array( $rows ) ) {
		$rows = messcut_get_agency_comparison_seed();
	}

	$normalized = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$criterion = trim( (string) ( $row['criterion'] ?? '' ) );
		if ( '' === $criterion ) {
			continue;
		}
		$normalized[] = array(
			'criterion' => $criterion,
			'messcut'   => (string) ( $row['messcut'] ?? '' ),
			'agency'    => (string) ( $row['agency'] ?? '' ),
			'inhouse'   => (string) ( $row['inhouse'] ?? '' ),
		);
	}

	return $normalized;
}

/**
 * Render agency comparison tabs.
 */
function messcut_render_agency_comparison(): void {
	get_template_part( 'template-parts/sections/agency-comparison' );
}

/**
 * Get service pain points for funnel.
 *
 * @return array<int, array{label: string, service_id: int}>
 */
function messcut_get_service_pains(): array {
	$rows = messcut_get_localized_option( 'service_pains', array() );
	if ( empty( $rows ) || ! is_array( $rows ) ) {
		$rows = messcut_get_service_pains_seed();
	}

	$pains = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$label = trim( (string) ( $row['label'] ?? '' ) );
		if ( '' === $label ) {
			continue;
		}

		$service_id = 0;
		if ( ! empty( $row['service'] ) ) {
			$service_id = is_object( $row['service'] ) ? (int) $row['service']->ID : (int) $row['service'];
		} elseif ( ! empty( $row['service_slug'] ) ) {
			$post = get_page_by_path( (string) $row['service_slug'], OBJECT, 'service' );
			$service_id = $post ? (int) $post->ID : 0;
		}

		$pains[] = array(
			'label'      => $label,
			'service_id' => $service_id,
		);
	}

	return $pains;
}

/**
 * Render pain funnel section.
 */
function messcut_render_pain_funnel(): void {
	get_template_part( 'template-parts/sections/pain-funnel' );
}

/**
 * Render combined path section (funnel + service cards + audience + ticker).
 *
 * @param array<string, mixed> $args Template args.
 */
function messcut_render_path( array $args = array() ): void {
	get_template_part( 'template-parts/sections/path', null, $args );
}

/**
 * Get audience method / value chips (formerly homepage ticker).
 *
 * @return string[]
 */
function messcut_get_ticker_items(): array {
	$rows = messcut_get_localized_option( 'home_ticker', array() );
	if ( empty( $rows ) || ! is_array( $rows ) ) {
		$rows = array(
			array( 'text' => __( 'дослідження', 'messcut' ) ),
			array( 'text' => __( 'стратегія', 'messcut' ) ),
			array( 'text' => __( 'бізнес-показники', 'messcut' ) ),
			array( 'text' => __( 'структура', 'messcut' ) ),
		);
	}

	$items = array();
	foreach ( $rows as $row ) {
		if ( is_array( $row ) && ! empty( $row['text'] ) ) {
			$items[] = (string) $row['text'];
		} elseif ( is_string( $row ) && '' !== trim( $row ) ) {
			$items[] = trim( $row );
		}
	}

	return $items;
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
		return array_slice( $items, 0, 7 );
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
 * @param array<string, mixed> $args Args: source, post_id, title, items, text.
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
			'text'  => $args['text'] ?? __( 'Відповідаємо на найпоширеніші запитання про бренд-стратегію та маркетинг.', 'messcut' ),
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
 * @param string               $variant black|white|footer.
 * @param array<string, mixed> $args    Optional: class, width, height, linked.
 */
function messcut_render_logo( string $variant = 'black', array $args = array() ): void {
	$class  = isset( $args['class'] ) ? (string) $args['class'] : 'site-logo';
	$width  = isset( $args['width'] ) ? (int) $args['width'] : 160;
	$height = isset( $args['height'] ) ? (int) $args['height'] : 35;
	$linked = ! array_key_exists( 'linked', $args ) || false !== $args['linked'];

	if ( 'footer' === $variant ) {
		$filename = 'footer-logo.png';
	} else {
		$variant  = 'white' === $variant ? 'white' : 'black';
		$filename = 'logo-' . $variant . '.svg';
	}

	$path = MESSCUT_DIR . '/assets/img/' . $filename;
	$url  = MESSCUT_URI . '/assets/img/' . $filename;

	if ( 'footer' !== $variant && ! file_exists( $path ) ) {
		$filename = 'logo-' . $variant . '.png';
		$path     = MESSCUT_DIR . '/assets/img/' . $filename;
		$url      = MESSCUT_URI . '/assets/img/' . $filename;
	}

	if ( ! file_exists( $path ) ) {
		$fallback = esc_html( get_bloginfo( 'name' ) );
		if ( $linked ) {
			printf(
				'<a class="%1$s site-title" href="%2$s">%3$s</a>',
				esc_attr( $class ),
				esc_url( home_url( '/' ) ),
				$fallback
			);
			return;
		}
		printf( '<span class="%1$s site-title">%2$s</span>', esc_attr( $class ), $fallback );
		return;
	}

	$img = sprintf(
		'<img class="site-logo__img" src="%1$s" alt="%2$s" width="%3$d" height="%4$d" decoding="async" />',
		esc_url( $url ),
		esc_attr( get_bloginfo( 'name' ) ),
		$width,
		$height
	);

	if ( $linked ) {
		printf(
			'<a class="%1$s" href="%2$s" rel="home">%3$s</a>',
			esc_attr( $class ),
			esc_url( home_url( '/' ) ),
			$img
		);
		return;
	}

	printf( '<span class="%1$s">%2$s</span>', esc_attr( $class ), $img );
}
