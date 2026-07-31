<?php
/**
 * English content seed and Polylang translation linking.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Seed English translations once Polylang is ready.
 */
function messcut_maybe_seed_i18n(): void {
	if ( get_option( 'messcut_seeded_en' ) || ! get_option( 'messcut_seeded' ) ) {
		return;
	}

	if ( ! messcut_is_polylang_active() ) {
		return;
	}

	if ( ! messcut_polylang_has_languages( array( 'uk', 'en' ) ) ) {
		if ( is_admin() ) {
			messcut_polylang_maybe_create_languages();
		}
		if ( ! messcut_polylang_has_languages( array( 'uk', 'en' ) ) ) {
			return;
		}
	}

	messcut_run_seed_i18n();
	update_option( 'messcut_seeded_en', 1, false );
}
add_action( 'pll_init', 'messcut_maybe_seed_i18n', 20 );
add_action( 'after_switch_theme', 'messcut_maybe_seed_i18n', 40 );

/**
 * Run English seed workflow.
 */
function messcut_run_seed_i18n(): void {
	messcut_assign_uk_language( array( 'page', 'case_study', 'service', 'article' ) );

	messcut_seed_en_options();
	$service_map = messcut_seed_en_services();
	messcut_seed_en_cases( $service_map );
	messcut_seed_en_pages();
	messcut_seed_en_menus();
	flush_rewrite_rules();
}

/**
 * Seed English ACF options.
 */
function messcut_seed_en_options(): void {
	messcut_seed_update_options(
		array(
			'en_footer_tagline'     => 'We build brands with a scientific approach',
			'en_cta_discuss_label'  => 'Discuss your project',
			'en_cta_consult_label'  => 'Get an introductory consultation',
			'en_home_hero_title'    => 'We build brands with a scientific approach',
			'en_home_hero_subtitle' => 'Structured, inspiring, and free of manipulation',
			'en_home_tagline'       => 'We launch brands from scratch — from positioning to first sales — and grow existing brands by improving measurable performance.',
			'en_stats'              => array(
				array( 'value' => '6 years', 'label' => 'of hands-on experience' ),
				array( 'value' => '30+', 'label' => 'brand strategies developed' ),
				array( 'value' => '50+', 'label' => 'brands consulted' ),
				array( 'value' => '85%', 'label' => 'of clients come by referral' ),
			),
			'en_home_values'        => array(
				array( 'text' => 'ethics' ),
				array( 'text' => 'motivation' ),
				array( 'text' => 'structure' ),
				array( 'text' => 'passion for the craft' ),
			),
		)
	);
}

/**
 * @return array<string, int> Service slug => EN post ID.
 */
function messcut_seed_en_services(): array {
	$translations = messcut_get_en_service_translations();
	$map          = array();

	foreach ( $translations as $slug => $data ) {
		$uk_post = get_page_by_path( $slug, OBJECT, 'service' );
		if ( ! $uk_post ) {
			continue;
		}

		$en_id = messcut_create_post_translation(
			(int) $uk_post->ID,
			'en',
			array(
				'post_title'   => $data['title'],
				'post_excerpt' => $data['excerpt'],
				'fields'       => $data['fields'],
			)
		);

		if ( $en_id ) {
			$map[ $slug ] = $en_id;
		}
	}

	return $map;
}

/**
 * @param array<string, int> $service_map EN service IDs by slug.
 */
function messcut_seed_en_cases( array $service_map ): void {
	$translations = messcut_get_en_case_translations();

	foreach ( $translations as $slug => $data ) {
		$uk_post = get_page_by_path( $slug, OBJECT, 'case_study' );
		if ( ! $uk_post ) {
			continue;
		}

		$fields = $data['fields'];
		if ( ! empty( $data['services'] ) ) {
			$related = array();
			foreach ( $data['services'] as $service_slug ) {
				if ( isset( $service_map[ $service_slug ] ) ) {
					$related[] = $service_map[ $service_slug ];
				}
			}
			$fields['services_used'] = $related;
		}

		messcut_create_post_translation(
			(int) $uk_post->ID,
			'en',
			array(
				'post_title'   => $data['title'],
				'post_excerpt' => $data['excerpt'],
				'fields'       => $fields,
			)
		);
	}
}

/**
 * Seed English pages.
 */
function messcut_seed_en_pages(): void {
	$pages = messcut_get_en_page_translations();

	foreach ( $pages as $slug => $data ) {
		$uk_post = get_page_by_path( $slug );
		if ( ! $uk_post ) {
			continue;
		}

		$overrides = array(
			'post_title'   => $data['title'],
			'post_content' => $data['content'] ?? '',
			'fields'       => $data['fields'] ?? array(),
		);

		if ( ! empty( $data['template'] ) ) {
			$overrides['page_template'] = $data['template'];
		}

		messcut_create_post_translation( (int) $uk_post->ID, 'en', $overrides );
	}
}

/**
 * Create EN navigation menus and assign Polylang locations.
 */
function messcut_seed_en_menus(): void {
	if ( ! function_exists( 'pll_set_term_language' ) ) {
		return;
	}

	$primary_en = messcut_get_or_create_menu( 'primary-en' );
	$footer_en  = messcut_get_or_create_menu( 'footer-en' );

	pll_set_term_language( $primary_en, 'en' );
	pll_set_term_language( $footer_en, 'en' );

	if ( 0 === count( (array) wp_get_nav_menu_items( $primary_en ) ) ) {
		$menu_items = array(
			array( 'slug' => 'home', 'title' => 'Home' ),
			array( 'slug' => 'poslugy', 'title' => 'Services' ),
			array( 'slug' => 'dosvid', 'title' => 'Experience' ),
		);

		foreach ( $menu_items as $item ) {
			$uk_page = get_page_by_path( $item['slug'] );
			if ( ! $uk_page || ! function_exists( 'pll_get_post' ) ) {
				continue;
			}
			$en_page_id = pll_get_post( (int) $uk_page->ID, 'en' );
			if ( ! $en_page_id ) {
				continue;
			}
			wp_update_nav_menu_item(
				$primary_en,
				0,
				array(
					'menu-item-title'     => $item['title'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $en_page_id,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}
	}

	$legal_items = array(
		array( 'slug' => 'publichna-oferta', 'title' => 'Terms of Service' ),
		array( 'slug' => 'polityka-konfidentsiynosti', 'title' => 'Privacy Policy' ),
	);

	if ( 0 === count( (array) wp_get_nav_menu_items( $footer_en ) ) ) {
		foreach ( $legal_items as $item ) {
			$uk_page = get_page_by_path( $item['slug'] );
			if ( ! $uk_page || ! function_exists( 'pll_get_post' ) ) {
				continue;
			}
			$en_page_id = pll_get_post( (int) $uk_page->ID, 'en' );
			if ( ! $en_page_id ) {
				continue;
			}
			wp_update_nav_menu_item(
				$footer_en,
				0,
				array(
					'menu-item-title'     => $item['title'],
					'menu-item-object'    => 'page',
					'menu-item-object-id' => $en_page_id,
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}
	}

	$theme   = get_stylesheet();
	$options = get_option( 'polylang' );
	if ( ! is_array( $options ) ) {
		return;
	}

	if ( ! isset( $options['nav_menus'][ $theme ] ) ) {
		$options['nav_menus'][ $theme ] = array();
	}

	$options['nav_menus'][ $theme ]['primary']['en'] = $primary_en;
	$options['nav_menus'][ $theme ]['footer']['en']  = $footer_en;
	update_option( 'polylang', $options );
}

/**
 * Get or create a nav menu term ID.
 *
 * @param string $slug Menu slug.
 */
function messcut_get_or_create_menu( string $slug ): int {
	$menu = wp_get_nav_menu_object( $slug );
	if ( $menu ) {
		return (int) $menu->term_id;
	}

	$menu_id = wp_create_nav_menu( $slug );
	return is_wp_error( $menu_id ) ? 0 : (int) $menu_id;
}

/**
 * Create or update a post translation and link it to the UK source.
 *
 * @param int                  $uk_id     Source post ID.
 * @param string               $lang      Target language slug.
 * @param array<string, mixed> $overrides Post overrides.
 */
function messcut_create_post_translation( int $uk_id, string $lang, array $overrides ): int {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		return 0;
	}

	$existing = function_exists( 'pll_get_post' ) ? pll_get_post( $uk_id, $lang ) : 0;
	if ( $existing ) {
		$post_id = (int) $existing;
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_title'   => $overrides['post_title'] ?? get_the_title( $post_id ),
				'post_excerpt' => $overrides['post_excerpt'] ?? '',
				'post_content' => $overrides['post_content'] ?? get_post_field( 'post_content', $post_id ),
			)
		);
	} else {
		$uk_post = get_post( $uk_id );
		if ( ! $uk_post ) {
			return 0;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'    => $uk_post->post_type,
				'post_status'  => 'publish',
				'post_title'   => $overrides['post_title'] ?? $uk_post->post_title,
				'post_name'    => $uk_post->post_name,
				'post_excerpt' => $overrides['post_excerpt'] ?? $uk_post->post_excerpt,
				'post_content' => $overrides['post_content'] ?? $uk_post->post_content,
				'menu_order'   => (int) $uk_post->menu_order,
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return 0;
		}

		pll_set_post_language( (int) $post_id, $lang );

		$translations = function_exists( 'pll_get_post_translations' )
			? pll_get_post_translations( $uk_id )
			: array();

		if ( empty( $translations ) && function_exists( 'pll_get_post_language' ) ) {
			$source_lang = pll_get_post_language( $uk_id ) ?: 'uk';
			$translations[ $source_lang ] = $uk_id;
		}

		$translations[ $lang ] = (int) $post_id;
		pll_save_post_translations( $translations );
	}

	if ( ! empty( $overrides['page_template'] ) ) {
		update_post_meta( (int) $post_id, '_wp_page_template', $overrides['page_template'] );
	}

	if ( ! empty( $overrides['fields'] ) && function_exists( 'update_field' ) ) {
		messcut_seed_update_post_fields( (int) $post_id, $overrides['fields'] );
	}

	return (int) $post_id;
}

/**
 * English service translations.
 *
 * @return array<string, array<string, mixed>>
 */
function messcut_get_en_service_translations(): array {
	return array(
		'brand-strategy'    => array(
			'title'   => 'Brand Strategy',
			'excerpt' => 'We uncover a brand’s essence, position, mission, contexts, voice, visibility, and aesthetics — the foundation for all further brand promotion.',
			'fields'  => array(
				'short_description' => 'We uncover a brand’s essence, position, mission, contexts, voice, visibility, and aesthetics — the foundation for all further brand promotion.',
				'for_whom'          => '<p>For new brands — a clear path to market with audience, competitive landscape, and role defined.</p><p>For existing brands — fewer chaotic decisions, focused resources, and growth built on validated data.</p>',
				'result'            => '<p>A strategic foundation for marketing, communication, design, content, and business development.</p>',
				'cta_title'         => 'Discuss brand strategy',
			),
		),
		'marketing-support' => array(
			'title'   => 'Marketing Support',
			'excerpt' => 'A marketing director fully embedded in your business, building the team and executing strategy to reach business goals.',
			'fields'  => array(
				'short_description' => 'A marketing director fully embedded in your business, building the team and executing strategy to reach business goals.',
				'for_whom'          => '<p>For new brands — building the right system from day one.</p><p>For existing brands — higher marketing efficiency and scalable results.</p>',
				'result'            => '<p>A managed marketing system: the team works to a shared plan and decisions are data-driven.</p>',
				'cta_title'         => 'Discuss Marketing Support',
			),
		),
		'consulting'        => array(
			'title'   => 'Consulting',
			'excerpt' => 'Fast help with your challenges, an effective action plan, and marketing, creative, or business solutions.',
			'fields'  => array(
				'short_description' => 'Strategic consulting for founders, executives, and startups who need an outside view on a specific challenge.',
				'for_whom'          => '<p>Business owners, startups, marketing teams, and companies launching a new product or brand.</p>',
				'result'            => '<p>After the session you get a clear view of the situation and concrete next steps.</p>',
				'cta_title'         => 'Book a consultation',
			),
		),
		'mentorship'        => array(
			'title'   => 'Mentorship',
			'excerpt' => 'Strategic mentorship for entrepreneurs who want to build a brand on scientific marketing and deep expertise.',
			'fields'  => array(
				'short_description' => 'One-on-one work where we build the brand, marketing system, and business growth plan together.',
				'for_whom'          => '<p>For those planning to launch a business and for small businesses with a limited marketing budget.</p>',
				'result'            => '<p>Brand strategy, positioning, marketing plan, and a system for marketing decisions.</p>',
				'cta_title'         => 'Learn about mentorship',
			),
		),
	);
}

/**
 * English case study translations.
 *
 * @return array<string, array<string, mixed>>
 */
function messcut_get_en_case_translations(): array {
	return array(
		'choozy'    => array(
			'title'    => 'Choozy',
			'excerpt'  => 'How we built a children’s brand in a category where everyone says the same thing',
			'services' => array( 'brand-strategy', 'marketing-support' ),
			'fields'   => array(
				'hero_subtitle' => 'How we built a children’s brand in a category where everyone says the same thing',
				'intro'         => '<p>CHOOZY came to us at the idea stage. There was no brand, positioning, communication platform, or marketing system yet.</p>',
				'results'       => array(
					array( 'text' => '30% increase in marketing ROI over 6 months' ),
					array( 'text' => 'Improved LTV:CAC ratio' ),
					array( 'text' => 'Higher average order value and repeat purchase rate' ),
					array( 'text' => 'Brand platform ready for scaling' ),
				),
				'challenge'     => '<p>The market did not need another children’s brand. We had to find new value for parents and children.</p>',
				'research'      => '<p>Category analysis: market, competitors, parent behavior, purchase motivations, and choice barriers.</p>',
				'insight'       => '<p>Parents want to raise independent children. CHOOZY could help kids learn to choose within safe boundaries.</p>',
				'case_sections' => array(
					array(
						'acf_fc_layout' => 'text_section',
						'title'         => 'Brand strategy',
						'content'       => '<p>The brand was built around developing children’s independence through choice.</p>',
					),
				),
				'cta_title'     => 'Facing a similar challenge?',
				'cta_text'      => 'Fill out a short form and we will discuss your project and possible growth scenarios.',
			),
		),
		'sloway'    => array(
			'title'    => 'Sloway',
			'excerpt'  => 'How we turned a mattress from a sleep product into a platform for modern living',
			'services' => array( 'brand-strategy' ),
			'fields'   => array(
				'hero_subtitle' => 'How we turned a mattress from a sleep product into a platform for modern living',
				'intro'         => '<p>Most mattress brands sell sleep. SLOWAY — the chance to rethink the role of the bed in modern life.</p>',
				'challenge'     => '<p>The mattress category is rational: firmness, materials, technology. We needed a new way to see the category.</p>',
				'research'      => '<p>Category and consumer behavior analysis: how people use the bed today.</p>',
				'insight'       => '<p>People do not buy a mattress — they buy a comfortable space for living. The bed is not only for sleep.</p>',
				'case_sections' => array(
					array(
						'acf_fc_layout' => 'text_section',
						'title'         => 'Brand strategy',
						'content'       => '<p><strong>“We are in no hurry.”</strong> — a cultural territory around slow living and rest without guilt.</p>',
					),
				),
				'cta_title'     => 'Let’s discuss your project',
				'cta_text'      => 'We will help find growth opportunities for your brand.',
			),
		),
		'payen'     => array(
			'title'    => 'Payen',
			'excerpt'  => 'Payen — strategic positioning and brand development',
			'services' => array( 'brand-strategy', 'marketing-support' ),
			'fields'   => array(
				'hero_subtitle' => 'Strategic positioning for Payen',
				'intro'         => '<p>Payen — a case of building a brand focused on long-term competitive advantage.</p>',
				'cta_title'     => 'Facing a similar challenge?',
				'cta_text'      => 'Fill out the form — we will discuss your project.',
			),
		),
		'hottier'   => array(
			'title'    => 'Hottier',
			'excerpt'  => 'Hottier — brand strategy and marketing system',
			'services' => array( 'marketing-support' ),
			'fields'   => array(
				'hero_subtitle' => 'Brand strategy and marketing system for Hottier',
				'intro'         => '<p>Hottier — an example of a systematic approach to brand and marketing development.</p>',
				'cta_title'     => 'Discuss your project',
				'cta_text'      => 'Tell us about your business — we will find the best collaboration format.',
			),
		),
		'antytezys' => array(
			'title'    => 'Antytezys',
			'excerpt'  => 'Antytezys — redefining the brand’s position in the market',
			'services' => array( 'brand-strategy' ),
			'fields'   => array(
				'hero_subtitle' => 'Redefining Antytezys’s position in the market',
				'intro'         => '<p>Antytezys — a case of finding new territory in a competitive category.</p>',
				'cta_title'     => 'Facing a similar challenge?',
				'cta_text'      => 'We start by understanding what drives your customers’ choices.',
			),
		),
		'boostera'  => array(
			'title'    => 'Boostera',
			'excerpt'  => 'Boostera — strategic growth and scaling',
			'services' => array( 'marketing-support', 'consulting' ),
			'fields'   => array(
				'hero_subtitle' => 'Strategic growth and scaling for Boostera',
				'intro'         => '<p>Boostera — a case of scaling a brand through systematic marketing.</p>',
				'cta_title'     => 'Let’s discuss your project',
				'cta_text'      => 'We will help find growth opportunities for the business.',
			),
		),
	);
}

/**
 * English page translations.
 *
 * @return array<string, array<string, mixed>>
 */
function messcut_get_en_page_translations(): array {
	return array(
		'home' => array(
			'title'   => 'Home',
			'content' => '',
		),
		'dosvid' => array(
			'title'    => 'Experience',
			'content'  => '',
			'template' => 'page-approach.php',
			'fields'   => array(
				'approach_content' => '<p>Our work is grounded in science-based marketing. We help brands grow through real value for people, a systematic approach, and strategies built for the long term.</p>',
				'values_override'  => array(
					array( 'text' => 'structure' ),
					array( 'text' => 'continuous learning' ),
					array( 'text' => 'inspiration' ),
					array( 'text' => 'a deep idea' ),
				),
			),
		),
		'poslugy' => array(
			'title'   => 'Services',
			'content' => '<p>End-to-end services for building and growing brands: from strategy to marketing support.</p>',
		),
		'publichna-oferta' => array(
			'title'   => 'Terms of Service',
			'content' => messcut_get_en_legal_offer_content(),
		),
		'polityka-konfidentsiynosti' => array(
			'title'   => 'Privacy Policy',
			'content' => messcut_get_en_legal_privacy_content(),
		),
	);
}

/**
 * English terms of service HTML.
 */
function messcut_get_en_legal_offer_content(): string {
	return '<h2>1. General</h2>
<p>This document is an official public offer for marketing, strategic, consulting, and related services under the MESSCUT brand.</p>
<h2>2. Subject of the agreement</h2>
<p>The contractor provides the client with services including brand strategy, marketing research, consulting, mentorship, marketing support, and other services as agreed.</p>
<h2>3. Contact</h2>
<p>Email: admin@messcut.com<br>Telegram: @messcutstrategy<br>Phone: +38 (095) 477-11-22</p>';
}

/**
 * English privacy policy HTML.
 */
function messcut_get_en_legal_privacy_content(): string {
	return '<p>MESSCUT respects every user’s right to privacy and personal data protection.</p>
<h2>1. General</h2>
<p>This Privacy Policy defines how we collect, use, and store personal data of MESSCUT website users.</p>
<h2>2. Data we may collect</h2>
<p>Name, phone number, email, company name, social or website links, and information submitted through contact forms.</p>
<h2>8. Contact</h2>
<p>Email: admin@messcut.com<br>Telegram: @messcutstrategy<br>Phone: +38 (095) 477-11-22</p>';
}
