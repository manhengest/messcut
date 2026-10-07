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
	messcut_seed_en_articles();
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
			'en_cta_discuss_label'  => 'Get your growth plan',
			'en_cta_consult_label'  => 'Get an introductory consultation',
			'en_home_hero_title'    => 'Brand strategy and scientific marketing',
			'en_home_hero_subtitle' => 'We build marketing systems and help businesses scale based on research',
			'en_audience_text'      => 'Entrepreneurs who want to grow a brand systematically, take fewer risks, and decide from research, data, and scientific principles',
			'en_stats'              => array(
				array( 'value' => '94%', 'label' => 'of clients recommend us to colleagues' ),
				array( 'value' => '50+', 'label' => 'strategic partnerships with large and small brands across niches' ),
				array( 'value' => 'NON-STOP', 'label' => 'NON-STOP professional development and research' ),
				array( 'value' => '6+', 'label' => 'years of practice' ),
				array( 'value' => '1:2', 'label' => '1 marketer = up to 2 projects for deep immersion in your business' ),
			),
			'en_home_ticker'        => array(
				array( 'text' => 'research' ),
				array( 'text' => 'strategy' ),
				array( 'text' => 'business metrics' ),
				array( 'text' => 'structure' ),
			),
			'en_agency_comparison_title' => 'Compare',
			'en_home_values'        => array(
				array( 'text' => 'ethics' ),
				array( 'text' => 'motivation' ),
				array( 'text' => 'structure' ),
				array( 'text' => 'passion for the craft' ),
			),
			'en_home_faq_title'     => 'FAQ',
			'en_home_faq_intro'     => 'Common questions about brand strategy and marketing',
			'en_home_faq'           => messcut_get_faq_seed_data( 'en' ),
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
		$uk_post = messcut_get_uk_post_by_slug( $slug, 'service' );
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
		$uk_post = messcut_get_uk_post_by_slug( $slug, 'case_study' );
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
 * Seed English insight translations.
 */
function messcut_seed_en_articles(): void {
	$translations = array(
		'strong-brand-positioning' => array(
			'title'   => 'How to create a strong brand position',
			'excerpt' => 'Positioning is not about pretty words. It is a clear role for the brand in people’s lives and in the market. Here is how to form a position the audience understands and chooses.',
		),
		'research-foundation'      => array(
			'title'   => 'Why research is the foundation of brand strategy',
			'excerpt' => 'Strategy without data is a set of hypotheses. These are the research types that matter most at the start of a project, and how to avoid spending the budget on pretty presentations.',
		),
		'fractional-cmo'           => array(
			'title'   => 'What a fractional CMO is, and when a business needs one',
			'excerpt' => 'A fractional CMO is an external strategist responsible for the system, the team, and the results, without hiring a full-time CMO. When it works, and what to expect from the partnership.',
		),
	);

	foreach ( $translations as $slug => $data ) {
		$uk_post = messcut_get_uk_post_by_slug( $slug, 'article' );
		if ( ! $uk_post ) {
			continue;
		}

		messcut_create_post_translation(
			(int) $uk_post->ID,
			'en',
			array(
				'post_title'   => $data['title'],
				'post_excerpt' => $data['excerpt'],
				'post_content' => (string) $uk_post->post_content,
			)
		);
	}
}

/**
 * Ukrainian source post for a slug, even when the English translation shares it.
 */
function messcut_get_uk_post_by_slug( string $slug, string $post_type ): ?WP_Post {
	$posts = get_posts(
		array(
			'name'             => $slug,
			'post_type'        => $post_type,
			'post_status'      => 'any',
			'posts_per_page'   => 5,
			'lang'             => '',
			'suppress_filters' => true,
		)
	);

	foreach ( $posts as $post ) {
		if ( ! function_exists( 'pll_get_post_language' ) || 'uk' === pll_get_post_language( (int) $post->ID ) ) {
			return $post;
		}
	}

	return $posts[0] ?? null;
}

/**
 * Seed English pages.
 */
function messcut_seed_en_pages(): void {
	$pages = messcut_get_en_page_translations();

	foreach ( $pages as $slug => $data ) {
		$uk_post = messcut_get_uk_post_by_slug( $slug, 'page' );
		if ( ! $uk_post ) {
			continue;
		}

		$overrides = array(
			'post_title'   => $data['title'],
			'post_content' => $data['content'] ?? '',
			'post_name'    => $data['slug'] ?? '',
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

	if ( function_exists( 'messcut_clear_nav_menu' ) ) {
		messcut_clear_nav_menu( $primary_en );
	}

	$menu_items = array(
		array( 'type' => 'page', 'slug' => 'poslugy', 'title' => 'Services' ),
		array( 'type' => 'archive', 'post_type' => 'case_study', 'title' => 'Case studies' ),
		array( 'type' => 'page', 'slug' => 'dosvid', 'title' => 'Experience' ),
		array( 'type' => 'archive', 'post_type' => 'article', 'title' => 'Insights' ),
	);

	foreach ( $menu_items as $item ) {
		if ( 'page' === $item['type'] ) {
			$uk_page = messcut_get_uk_post_by_slug( $item['slug'], 'page' );
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
			continue;
		}

		$archive_slug = 'case_study' === $item['post_type'] ? 'cases' : 'articles';
		$archive      = function_exists( 'pll_home_url' )
			? trailingslashit( pll_home_url( 'en' ) ) . $archive_slug . '/'
			: home_url( '/en/' . $archive_slug . '/' );
		wp_update_nav_menu_item(
			$primary_en,
			0,
			array(
				'menu-item-title'  => $item['title'],
				'menu-item-url'    => $archive,
				'menu-item-type'   => 'custom',
				'menu-item-status' => 'publish',
			)
		);
	}

	$legal_items = array(
		array( 'slug' => 'publichna-oferta', 'title' => 'Terms of Service' ),
		array( 'slug' => 'polityka-konfidentsiynosti', 'title' => 'Privacy Policy' ),
	);

	if ( 0 === count( (array) wp_get_nav_menu_items( $footer_en ) ) ) {
		foreach ( $legal_items as $item ) {
			$uk_page = messcut_get_uk_post_by_slug( $item['slug'], 'page' );
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

	if ( ! empty( $overrides['fields'] ) ) {
		messcut_seed_update_post_fields( (int) $post_id, $overrides['fields'] );
	}

	$uk_post = get_post( $uk_id );
	$slug    = (string) ( $overrides['post_name'] ?? '' );
	if ( '' === $slug && $uk_post ) {
		$slug = $uk_post->post_name . '-en';
	}
	if ( '' !== $slug && get_post_field( 'post_name', $post_id ) !== $slug ) {
		wp_update_post(
			array(
				'ID'        => (int) $post_id,
				'post_name' => $slug,
			)
		);
	}

	return (int) $post_id;
}

/**
 * English service translations.
 *
 * @return array<string, array<string, mixed>>
 */
function messcut_get_en_service_translations(): array {
	$rows = array(
		'brand-strategy'    => array(
			'title'   => 'Brand Strategy',
			'excerpt' => 'We uncover a brand’s essence, position, mission, contexts, voice, visibility, and aesthetics — the foundation for all further brand promotion.',
			'fields'  => array(
				'direction'         => 'branding',
				'eyebrow'           => 'Stage 01',
				'teaser'            => 'A strategic foundation for the business in one month',
				'cta_label'         => 'Get the strategy',
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
				'direction'         => 'marketing',
				'eyebrow'           => 'Support',
				'teaser'            => 'A fractional CMO who runs marketing toward measurable growth',
				'cta_label'         => 'Start support',
				'short_description' => 'A marketing director fully embedded in your business, building the team and executing strategy to reach business goals.',
				'for_whom'          => '<p>For new brands — building the right system from day one.</p><p>For existing brands — higher marketing efficiency and scalable results.</p>',
				'result'            => '<p>A managed marketing system: the team works to a shared plan and decisions are data-driven.</p>',
				'cta_title'         => 'Discuss Marketing Support',
			),
		),
		'strategy-implementation' => array(
			'title'   => 'Strategy implementation',
			'excerpt' => 'We turn the strategy into a 6-month plan, channel examples, and a handover session in two weeks.',
			'fields'  => array(
				'teaser'    => 'We turn the strategy into action in two weeks',
				'eyebrow'   => 'Stage 02',
				'direction' => 'branding',
				'cta_label' => 'Available after brand strategy',
				'locked'    => 1,
			),
		),
		'marketing-audit' => array(
			'title'   => 'Marketing audit',
			'excerpt' => 'A two-week diagnosis of channels, unit economics, and a priority growth plan.',
			'fields'  => array(
				'teaser'    => 'A two-week marketing diagnosis with a ready action plan',
				'eyebrow'   => 'Audit',
				'direction' => 'marketing',
				'cta_label' => 'Get the audit',
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

	if ( function_exists( 'messcut_get_service_seed_data' ) ) {
		$uk = messcut_get_service_seed_data();
		foreach ( $rows as $slug => $data ) {
			if ( empty( $uk[ $slug ]['fields'] ) || ! is_array( $uk[ $slug ]['fields'] ) ) {
				continue;
			}
			foreach ( array( 'bullets', 'steps' ) as $key ) {
				if ( empty( $data['fields'][ $key ] ) && ! empty( $uk[ $slug ]['fields'][ $key ] ) ) {
					$rows[ $slug ]['fields'][ $key ] = $uk[ $slug ]['fields'][ $key ];
				}
			}
		}
	}

	return $rows;
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
			'excerpt'  => 'How we built the CHOOZY brand in a category where everyone says the same thing',
			'services' => array( 'brand-strategy', 'marketing-support' ),
			'fields'   => array(
				'hero_subtitle' => 'How we built the CHOOZY brand in a category where everyone says the same thing',
				'intro'         => '<p>CHOOZY came to us before the business launched. There was no brand, positioning, marketing strategy, or communication system — only an idea to create a modern children’s goods space for parents who choose quality, aesthetics, and mindful parenting.</p><p>We had to find a place for the brand in a market where most companies use the same messages about safety, care, and quality.</p><p>The work started with strategic research and grew into a full collaboration: from building the brand platform to developing the company as an external marketing director (Fractional CMO).</p>',
				'results'       => array(
					array(
						'value' => '+250%',
						'text'  => 'growth in marketing ROI over the first 6 months',
					),
					array(
						'value' => '↑ LTV : CAC',
						'text'  => 'improved ratio of customer lifetime value to acquisition cost',
					),
					array(
						'value' => '↑ Repeat Purchase Rate',
						'text'  => 'steady growth in the share of repeat purchases',
					),
					array(
						'value' => 'Earned Media',
						'text'  => 'the brand started receiving organic influencer mentions',
					),
					array(
						'value' => 'Brand Platform',
						'text'  => 'a brand foundation built for further business scaling',
					),
				),
				'mid_cta_title' => 'Planning a new brand launch, or ready to rethink an existing one?',
				'mid_cta_text'  => 'We help businesses find competitive advantages, build strong brands, and create marketing systems that work over the long term.',
				'client_task'   => '<p>The main challenge was to build a brand with its own territory in the market — one that would not compete only on price, assortment, or standard messages about product quality.</p><p>Together with the client we defined the key strategic goals:</p><ul><li>form a strong brand positioning;</li><li>build a brand platform for long-term development;</li><li>create a marketing system that can scale with the business;</li><li>lay the foundation for ongoing marketing leadership as a Fractional CMO.</li></ul>',
				'process_steps' => array(
					array(
						'title' => 'Stage 1. Research',
						'text'  => 'A full analysis of the market, category, competitors, and consumer behavior.',
					),
					array(
						'title' => 'Stage 2. Finding the strategic insight',
						'text'  => 'Identifying an unmet audience need and the brand’s role in customers’ lives.',
					),
					array(
						'title' => 'Stage 3. Building the brand strategy',
						'text'  => 'Shaping the mission, positioning, key messages, and brand platform.',
					),
					array(
						'title' => 'Stage 4. Developing the marketing system',
						'text'  => 'Creating the go-to-market strategy, KPI system, marketing plan, and analytics.',
					),
					array(
						'title' => 'Stage 5. Marketing Support',
						'text'  => 'Ongoing work as an external marketing director: managing marketing processes, optimizing investment, and finding new growth points.',
					),
				),
				'challenge'     => '<p>The children’s goods market is one of the most competitive. Most brands use the same arguments: quality, safety, care, natural ingredients. For a new player that is an even bigger challenge: without a clear strategy the brand risks dissolving among dozens of similar offers and competing on price alone.</p><p>That is why we deliberately rejected an approach that starts with a logo or identity. The first task was to define the brand’s role in the lives of modern parents and find a strategic advantage competitors did not occupy.</p><p>Only after that did we move on to the brand platform, positioning, and marketing system that became the basis for CHOOZY’s further growth.</p>',
				'research'      => '<p>Every project starts with research. It shows how the audience thinks, which factors shape choice, and where there is room to build a competitive advantage.</p><p>For CHOOZY we ran a full strategic analysis to find a non-obvious opportunity in a crowded children’s goods category.</p><p>Within the research we analyzed:</p><ul><li>market structure and the main category segments;</li><li>competitor positioning and communication;</li><li>behavioral models of modern parents;</li><li>factors that influence the choice of children’s goods;</li><li>barriers at the point of purchase;</li><li>international and local market trends;</li><li>opportunities for brand differentiation.</li></ul>',
				'insight'       => '<p>During the research we noticed a pattern.</p><p><strong>Parents want to raise independent children.</strong></p><p>They want to let a child make their own choice, while staying confident that the choice happens within safe boundaries.</p><p>That is where the strategic opportunity appeared.</p><p>CHOOZY could help with more than buying things.</p><p><strong>CHOOZY could help a child learn to choose.</strong></p><p>This insight became the foundation of the entire brand strategy that followed.</p>',
				'brand_strategy'=> '<p>We built the brand around the idea of developing a child’s independence through choice.</p><p>That made it possible to form:</p><ul><li>clear positioning;</li><li>a difference from competitors;</li><li>a communication space of its own;</li><li>an emotional connection with parents;</li><li>a platform for long-term development.</li></ul><p>Instead of competing on the category’s rational attributes, the brand gained its own territory of meaning.</p>',
				'brand_mission' => '<p>Help children learn to make their own choices, and help parents support that process through a well-built brand and environment.</p>',
				'positioning'   => '<p>We built the brand around the idea of developing a child’s independence through choice.</p><p>This positioning let CHOOZY move beyond standard category communication and form a space of meaning of its own.</p>',
				'visual_identity' => '<p>One of the core principles of modern scientific marketing is creating and reinforcing brand associations.</p><p>After the positioning was set, we worked on distinctive brand assets:</p><ul><li>the name CHOOZY;</li><li>the brand character Choozik;</li><li>a system of visual codes;</li><li>key messages;</li><li>communication scenarios.</li></ul><p>Every element served one job — to lock in the connection between the brand and the audience.</p>',
				'marketing_strategy' => '<p>After the brand platform was in place, we moved on to the marketing system.</p><p>We developed:</p><ul><li>a go-to-market strategy;</li><li>a marketing plan;</li><li>a KPI system;</li><li>a channel structure;</li><li>an analytics system;</li><li>brand development priorities.</li></ul><p>The goal was not only to drive the first sales, but to create a system that would scale with the business.</p>',
				'results_detail' => '<p>The brand strategy became the foundation for CHOOZY’s further development. After launch we kept working on the marketing system, optimizing processes and testing new growth points.</p><p>In the first six months of collaboration we achieved:</p><ul><li><strong>+250%</strong> Overall growth in marketing ROI over 6 months.</li><li><strong>Higher LTV:CAC</strong> The ratio of customer lifetime value to acquisition cost improved, and acquisition pays back several times over.</li><li><strong>More repeat purchases</strong> Repeat purchase frequency increased — one of the signs of long-term customer relationships.</li><li><strong>Organic mentions</strong> The brand started receiving organic influencer mentions without separate campaigns to recruit them.</li><li><strong>A platform for scale</strong> A brand platform and marketing system that became the basis for further business growth.</li></ul>',
				'marketing_support' => '<p>After launch we continued as an external marketing director.</p><p>The role included:</p><ul><li>managing marketing processes;</li><li>coordinating contractors;</li><li>analyzing channel performance;</li><li>testing new hypotheses;</li><li>optimizing marketing investment;</li><li>finding new sources of growth.</li></ul><p>This format made it possible not only to deliver the brand strategy, but to embed it in how the business actually works.</p>',
				'case_demonstrates' => '<p>Competitive advantage comes from a clear understanding of the brand’s role in the customer’s life.</p><p>Strategic research, the insight it produced, and the brand platform built around that insight created the basis for the company’s long-term development and a marketing system that scales with the business.</p>',
				'specialist_name' => 'Valeriia Chemerys',
				'faq_title'     => 'FAQ',
				'cta_title'     => 'Let’s discuss your project',
				'cta_text'      => 'If you are launching a new brand or looking for new growth points in an existing business, we start with what matters: what actually influences your customers’ choice. Fill out a short form and we will discuss your project and possible development scenarios.',
			),
		),
		'sloway'    => array(
			'title'    => 'Sloway',
			'excerpt'  => 'How we turned a mattress from a sleep product into a platform for modern living',
			'services' => array( 'brand-strategy' ),
			'fields'   => array(
				'hero_subtitle' => 'How we turned a mattress from a sleep product into a platform for modern living',
				'intro'         => '<p>What do mattress brands sound like? They talk about orthopedic features, materials, back support, and comfort.</p><p>When SLOWAY came to us, we saw a chance to build a brand that would rethink the role of the bed in modern life.</p>',
				'results'       => array(
					array(
						'value' => 'Brand platform',
						'text'  => 'A new brand platform that moves beyond the mattress category.',
					),
					array(
						'value' => 'Associations',
						'text'  => 'The brand gained a wider system of mental associations.',
					),
					array(
						'value' => 'Demand',
						'text'  => 'The brand can shape demand beyond people who are shopping for a mattress right now.',
					),
				),
				'mid_cta_title' => 'Let’s discuss your project',
				'mid_cta_text'  => 'If your product has become one more similar player in the market, the brand may need a new role in people’s lives.',
				'client_task'   => '<p>Create a brand that stands out in the mattress category.</p>',
				'process_steps' => array(
					array(
						'title' => 'Category analysis',
						'text'  => '',
					),
					array(
						'title' => 'Competitor research',
						'text'  => '',
					),
					array(
						'title' => 'Consumer behavior analysis',
						'text'  => '',
					),
					array(
						'title' => 'Cultural shifts in how people live',
						'text'  => '',
					),
					array(
						'title' => 'Strategic insight',
						'text'  => '',
					),
					array(
						'title' => 'Brand platform and new positioning',
						'text'  => '',
					),
				),
				'challenge'     => '<p>Mattresses are one of the most rational home-goods categories.</p><p>In that setting, one more brand with good specifications has almost no chance to stand out. We had to find a new angle on the category.</p>',
				'research'      => '<p>We studied the category, competitors, and consumer behavior.</p><p>Today people:</p><ul><li>work in bed;</li><li>watch series;</li><li>take online meetings;</li><li>read;</li><li>drink coffee;</li><li>plan the future;</li><li>recover after heavy news;</li><li>look for a sense of safety.</li></ul>',
				'insight'       => '<p>The analysis made it clear that the modern bed stopped being only a place to sleep a long time ago.</p><p><strong>The bed became a person’s private space, not only a place to rest at night.</strong></p>',
				'brand_strategy'=> '<p>Instead of competing for a better mattress, the brand took the territory of slow living, rest without guilt, and a person’s right to slow down.</p>',
				'brand_mission' => '<p>Rethink the role of the bed in modern life and make it a symbol of a comfortable space for living, rest, and psychological recovery.</p>',
				'positioning'   => '<p>The bed is a place for living, not only for sleep.</p><p>Instead of competing for a better mattress, the brand took the territory of slow living, rest without guilt, and a person’s right to slow down.</p>',
				'brand_message' => '<p><strong>“We are in no hurry”</strong></p>',
				'visual_identity' => '<p>The strategy was built around this territory:</p><ul><li>slow living;</li><li>home comfort;</li><li>psychological recovery;</li><li>personal space;</li><li>comfort.</li></ul>',
				'marketing_strategy' => '',
				'case_sections' => array(),
				'results_detail' => '<p>SLOWAY became a brand about a way of living, not only mattresses. That made it possible to:</p><ul><li>widen the category in which the brand is recalled;</li><li>shape demand outside the moment of purchase;</li><li>build a stronger emotional territory for the brand.</li></ul>',
				'marketing_support' => '',
				'case_demonstrates' => '<p>We start with people: their behavior, cultural shifts, and the contexts in which they consume.</p><p>Only then do we build a brand that fits naturally into the audience’s life and has a better chance of long-term growth.</p>',
				'cta_title'     => 'Let’s discuss your project',
				'cta_text'      => 'If you are launching a new brand, or your product has become one more player among many similar ones, the problem may not be marketing. The brand may need a new role in people’s lives. Fill out a short form and we will help find growth points for your business.',
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
			'slug'    => 'home-en',
		),
		'dosvid' => array(
			'title'    => 'Experience',
			'slug'     => 'experience',
			'content'  => '',
			'template' => 'page-approach.php',
			'fields'   => array(
				'approach_content' => '<p>We study how people make decisions, what shapes their behavior, and how that works in a specific business. From that, we build marketing that has logic, a system, and a clear goal.</p>',
				'values_override'  => array(
					array( 'text' => 'structure' ),
					array( 'text' => 'continuous learning' ),
					array( 'text' => 'inspiration' ),
					array( 'text' => 'a deep idea' ),
				),
			),
		),
		'poslugy' => array(
			'title'    => 'Services',
			'slug'     => 'services',
			'content'  => '',
			'template' => 'page-poslugy.php',
		),
		'publichna-oferta' => array(
			'title'   => 'Terms of Service',
			'slug'    => 'terms',
			'content' => messcut_get_en_legal_offer_content(),
		),
		'polityka-konfidentsiynosti' => array(
			'title'   => 'Privacy Policy',
			'slug'    => 'privacy',
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
