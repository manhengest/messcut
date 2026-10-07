<?php
/**
 * Case study document renderer.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Bundled or ACF case document.
 *
 * @return array<string, mixed>
 */
function messcut_get_case_document( int $post_id ): array {
	$raw = messcut_get_acf( 'case_document', $post_id );
	if ( is_string( $raw ) && '' !== trim( $raw ) ) {
		$decoded = json_decode( $raw, true );
		if ( is_array( $decoded ) ) {
			return $decoded;
		}
	}

	$slugs = array( (string) get_post_field( 'post_name', $post_id ) );
	if ( function_exists( 'pll_get_post' ) ) {
		$uk_id = pll_get_post( $post_id, 'uk' );
		if ( $uk_id && (int) $uk_id !== $post_id ) {
			$slugs[] = (string) get_post_field( 'post_name', (int) $uk_id );
		}
	}
	$path = MESSCUT_DIR . '/inc/data/cases-uk.json';
	if ( ! is_readable( $path ) ) {
		return array();
	}
	$all = json_decode( (string) file_get_contents( $path ), true );
	if ( ! is_array( $all ) || empty( $all['cases'] ) || ! is_array( $all['cases'] ) ) {
		return array();
	}
	$doc = array();
	foreach ( $slugs as $slug ) {
		if ( isset( $all['cases'][ $slug ] ) && is_array( $all['cases'][ $slug ] ) ) {
			$doc = $all['cases'][ $slug ];
			break;
		}
	}
	if ( ! $doc ) {
		return array();
	}
	if ( empty( $doc['expert'] ) && ! empty( $all['expert'] ) ) {
		$doc['expert'] = $all['expert'];
	}
	return $doc;
}

/**
 * @param mixed $value Paragraphs.
 */
function messcut_case_paragraphs( mixed $value ): string {
	$parts = is_array( $value ) ? $value : array( (string) $value );
	$html  = '';
	foreach ( $parts as $part ) {
		$text = trim( (string) $part );
		if ( '' === $text ) {
			continue;
		}
		$text = str_replace( 'https://messcut.com/services/', messcut_page_url( 'poslugy' ) . '#svc-', $text );
		$html .= '<p class="case-copy">' . wp_kses( $text, array( 'a' => array( 'href' => array(), 'class' => array() ), 'b' => array(), 'strong' => array(), 'em' => array() ) ) . '</p>';
	}
	return $html;
}

/**
 * Split stored HTML into paragraph inners the case renderer can escape.
 *
 * @return array<int, string>
 */
function messcut_case_html_parts( mixed $value ): array {
	if ( ! is_string( $value ) ) {
		return array();
	}
	$html = trim( $value );
	if ( '' === $html ) {
		return array();
	}
	if ( preg_match_all( '/<p[^>]*>(.*?)<\/p>/is', $html, $matches ) ) {
		$parts = array();
		foreach ( $matches[1] as $part ) {
			$part = trim( (string) $part );
			if ( '' !== $part ) {
				$parts[] = $part;
			}
		}
		if ( $parts ) {
			return $parts;
		}
	}
	return array( $html );
}

/**
 * Escape a line and wrap {highlighted} words in mark.
 */
function messcut_case_marked_html( string $text ): string {
	$safe = esc_html( $text );
	return (string) preg_replace( '/\{(.+?)\}/', '<mark>$1</mark>', $safe );
}

/**
 * Plain paragraphs for blocks that print with esc_html().
 *
 * @return array<int, string>
 */
function messcut_case_plain_parts( mixed $value ): array {
	$parts = array();
	foreach ( messcut_case_html_parts( $value ) as $part ) {
		$text = trim( wp_strip_all_tags( $part ) );
		if ( '' !== $text ) {
			$parts[] = $text;
		}
	}
	return $parts;
}

/**
 * Case document built from the per-field meta used before case_document.
 *
 * @return array<string, mixed>
 */
function messcut_legacy_case_document( int $post_id ): array {
	$field = static function ( string $key ) use ( $post_id ): mixed {
		return messcut_get_acf( $key, $post_id );
	};

	$intro = messcut_case_html_parts( $field( 'intro' ) );
	$tasks = messcut_case_html_parts( $field( 'client_task' ) );
	$challenge = messcut_case_html_parts( $field( 'challenge' ) ?: $field( 'difficulties' ) );
	$research = messcut_case_html_parts( $field( 'research' ) );
	$insight = messcut_case_html_parts( $field( 'insight' ) );
	$support = messcut_case_html_parts( $field( 'marketing_support' ) );
	$outcome = messcut_case_html_parts( $field( 'results_detail' ) ?: $field( 'case_demonstrates' ) );
	$cta_text = messcut_case_plain_parts( $field( 'cta_text' ) );

	$results = array();
	$rows = $field( 'results' );
	if ( is_array( $rows ) ) {
		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$text = trim( (string) ( $row['text'] ?? '' ) );
			$value = trim( (string) ( $row['value'] ?? '' ) );
			if ( '' === $text && '' === $value ) {
				continue;
			}
			$results[] = array(
				'value'  => $value,
				'label'  => $text,
				'title'  => '' !== $value ? $value : $text,
				'detail' => $text,
			);
		}
	}

	$steps = array();
	$process = $field( 'process_steps' );
	if ( is_array( $process ) ) {
		foreach ( $process as $step ) {
			if ( ! is_array( $step ) ) {
				continue;
			}
			$title = trim( (string) ( $step['title'] ?? '' ) );
			$text = trim( wp_strip_all_tags( (string) ( $step['text'] ?? '' ) ) );
			if ( '' === $title && '' === $text ) {
				continue;
			}
			$steps[] = array(
				'title' => $title,
				'text'  => $text,
			);
		}
	}

	$strategy_fields = array(
		__( 'Місія бренду', 'messcut' )              => $field( 'brand_mission' ),
		__( 'Позиціонування', 'messcut' )            => $field( 'positioning' ),
		__( 'Візуальна складова бренду', 'messcut' ) => $field( 'visual_identity' ),
		__( 'Маркетингова стратегія', 'messcut' )    => $field( 'marketing_strategy' ),
		__( 'Меседж бренду', 'messcut' )             => $field( 'brand_message' ),
	);
	$strategy_items = array();
	foreach ( $strategy_fields as $label => $value ) {
		$blocks = array();
		foreach ( messcut_case_plain_parts( $value ) as $part ) {
			$blocks[] = array(
				't' => 'text',
				'v' => $part,
			);
		}
		if ( ! $blocks ) {
			continue;
		}
		$strategy_items[] = array(
			'title'  => $label,
			'open'   => ! $strategy_items,
			'blocks' => $blocks,
		);
	}

	$doc = array(
		'brand' => get_the_title( $post_id ),
		'hero'  => array(
			'em'       => get_the_title( $post_id ),
			'subtitle' => trim( (string) $field( 'hero_subtitle' ) ),
			'intro'    => $intro,
		),
	);
	if ( $results ) {
		$doc['results'] = array( 'items' => $results );
	}
	if ( $tasks ) {
		$doc['tasks'] = array( 'lead' => $tasks );
	}
	if ( $steps ) {
		$doc['process'] = $steps;
	}
	if ( $challenge ) {
		$doc['challenges'] = array( 'text' => $challenge );
	}
	if ( $research ) {
		$doc['research'] = array( 'lead' => $research );
	}
	if ( $insight ) {
		$doc['insight'] = array( 'text' => $insight );
	}
	$strategy_lead = messcut_case_html_parts( $field( 'brand_strategy' ) );
	if ( $strategy_lead || $strategy_items ) {
		$doc['strategy'] = array(
			'lead'  => $strategy_lead,
			'items' => $strategy_items,
		);
	}
	if ( $support ) {
		$doc['support'] = array( 'lead' => $support );
	}
	if ( $outcome ) {
		$doc['outcome'] = array( 'text' => $outcome );
	}
	$cta_title = trim( (string) $field( 'cta_title' ) );
	if ( '' !== $cta_title || $cta_text ) {
		$doc['cta'] = array(
			'title' => $cta_title,
			'text'  => $cta_text,
		);
	}

	$filled = array_diff( array_keys( $doc ), array( 'brand', 'hero' ) );
	$hero_filled = '' !== $doc['hero']['subtitle'] || $intro;
	if ( ! $filled && ! $hero_filled ) {
		return array();
	}
	return $doc;
}

/**
 * Render a case study from its document.
 */
function messcut_render_case_study( int $post_id ): void {
	$doc = messcut_get_case_document( $post_id );
	if ( ! $doc ) {
		$doc = messcut_legacy_case_document( $post_id );
	}
	if ( ! $doc ) {
		echo '<section class="section prose">';
		the_content();
		echo '</section>';
		return;
	}

	$hero  = is_array( $doc['hero'] ?? null ) ? $doc['hero'] : array();
	$brand = (string) ( $doc['brand'] ?? get_the_title( $post_id ) );
	$photo = messcut_media_url( (string) ( $doc['photo'] ?? '' ) );
	$logo  = messcut_media_url( (string) ( $doc['logo'] ?? '' ) );
	?>
	<nav class="chapter-rail" data-rail="[data-chapter]" aria-label="<?php esc_attr_e( 'Розділи кейсу', 'messcut' ); ?>"></nav>
	<section class="case-hero" id="intro" data-chapter>
		<p class="crumb"><a href="<?php echo esc_url( messcut_cases_archive_url() ); ?>"><?php esc_html_e( 'Кейси', 'messcut' ); ?></a><span>/</span><span><?php echo esc_html( $brand ); ?></span></p>
		<h1><?php echo esc_html( (string) ( $hero['titleBefore'] ?? '' ) ); ?> <em><?php echo esc_html( (string) ( $hero['em'] ?? $brand ) ); ?></em> <?php echo esc_html( (string) ( $hero['titleAfter'] ?? '' ) ); ?></h1>
		<?php if ( ! empty( $hero['subtitle'] ) ) : ?>
			<p class="case-hero__sub"><?php echo esc_html( (string) $hero['subtitle'] ); ?></p>
		<?php endif; ?>
		<div class="case-hero__photo">
			<?php if ( $photo ) : ?>
				<img src="<?php echo esc_url( $photo ); ?>" alt="">
			<?php endif; ?>
			<?php if ( $logo ) : ?>
				<img class="case-hero__logo" src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( $brand ); ?>">
			<?php else : ?>
				<div class="case-hero__word"><?php echo esc_html( $brand ); ?></div>
			<?php endif; ?>
			<?php if ( ! empty( $hero['caption']['text'] ) ) : ?>
				<div class="case-hero__caption">
					<span class="eyebrow"><?php echo esc_html( (string) ( $hero['caption']['label'] ?? '' ) ); ?></span>
					<b><?php echo esc_html( (string) $hero['caption']['text'] ); ?></b>
				</div>
			<?php endif; ?>
		</div>
		<?php echo messcut_case_paragraphs( $hero['intro'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</section>
	<?php
	messcut_render_case_results( $doc );
	$n = 0;
	$chapters = array( 'tasks', 'process', 'challenges', 'research', 'insight', 'strategy', 'visual', 'support', 'outcome' );
	$total = 0;
	foreach ( $chapters as $key ) {
		if ( ! empty( $doc[ $key ] ) ) {
			$total++;
		}
	}
	foreach ( $chapters as $key ) {
		if ( empty( $doc[ $key ] ) ) {
			continue;
		}
		$n++;
		messcut_render_case_chapter( $key, $doc[ $key ], $n, $total );
	}
	messcut_render_case_conclusion( $doc );
	messcut_render_case_lead( $doc );
	messcut_render_case_related( $post_id );
	messcut_render_case_materials();
	messcut_render_case_faq();
}

/**
 * @param array<string, mixed> $doc Document.
 */
function messcut_render_case_results( array $doc ): void {
	$results = $doc['results'] ?? null;
	if ( ! is_array( $results ) || empty( $results['items'] ) || ! is_array( $results['items'] ) ) {
		return;
	}
	$items = array_values( array_filter( $results['items'], 'is_array' ) );
	$list  = ! array_filter(
		$items,
		static function ( array $item ): bool {
			return '' !== trim( (string) ( $item['value'] ?? '' ) );
		}
	);
	?>
	<section class="case-section" id="key" data-chapter>
		<h2><?php esc_html_e( 'Ключові результати', 'messcut' ); ?></h2>
		<div class="result-grid<?php echo $list ? ' result-grid--list' : ''; ?>">
			<?php foreach ( $items as $item ) : ?>
				<?php
				$value = trim( (string) ( $item['value'] ?? '' ) );
				$label = (string) ( $item['label'] ?? '' );
				if ( $list || '' === $value ) :
					?>
					<div class="result-card result-card--plain"><span><?php echo esc_html( $label ); ?></span></div>
					<?php
					continue;
				endif;
				$classes = 'result-card';
				if ( ! empty( $item['big'] ) ) {
					$classes .= ' result-card--big';
				}
				?>
				<button class="<?php echo esc_attr( $classes ); ?>" type="button" data-result-title="<?php echo esc_attr( (string) ( $item['title'] ?? $value ) ); ?>" data-result-detail="<?php echo esc_attr( (string) ( $item['detail'] ?? '' ) ); ?>">
					<i class="result-card__plus" aria-hidden="true">+</i>
					<b<?php echo ! empty( $item['up'] ) ? ' class="is-up"' : ''; ?>><?php echo esc_html( $value ); ?></b>
					<span><?php echo esc_html( $label ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>
		<?php if ( ! empty( $results['cta'] ) ) : ?>
			<div class="case-cta-band">
				<h3><?php echo esc_html( (string) $results['cta'] ); ?></h3>
				<a class="cta" href="#lead-form"><?php esc_html_e( 'Обговорити проєкт', 'messcut' ); ?> <b>→</b></a>
			</div>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * @param mixed $data Chapter payload.
 */
function messcut_render_case_chapter( string $key, mixed $data, int $n, int $total ): void {
	if ( ! is_array( $data ) ) {
		return;
	}
	$titles = array(
		'tasks'      => __( 'Завдання', 'messcut' ),
		'process'    => __( 'Процес співпраці', 'messcut' ),
		'challenges' => __( 'Виклики проєкту', 'messcut' ),
		'research'   => __( 'Дослідження', 'messcut' ),
		'insight'    => __( 'Стратегічний інсайт', 'messcut' ),
		'strategy'   => __( 'Стратегія бренду', 'messcut' ),
		'visual'     => __( 'Візуальна складова', 'messcut' ),
		'support'    => __( 'Маркетингова підтримка', 'messcut' ),
		'outcome'    => __( 'Результат', 'messcut' ),
	);
	$title = (string) ( $titles[ $key ] ?? '' );
	if ( 'outcome' === $key && ! empty( $data['title'] ) ) {
		$title = (string) $data['title'];
	}
	?>
	<section class="case-section case-section--split" id="<?php echo esc_attr( $key ); ?>" data-chapter>
		<div class="case-section__aside">
			<div class="eyebrow"><?php echo esc_html( sprintf( '%02d / %02d', $n, $total ) ); ?></div>
			<h2><?php echo esc_html( $title ); ?></h2>
		</div>
		<div>
			<?php
			if ( 'tasks' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$items = $data['items'] ?? array();
				if ( is_array( $items ) && $items ) {
					echo '<div class="task-list">';
					foreach ( array_values( $items ) as $i => $item ) {
						if ( ! is_array( $item ) ) {
							continue;
						}
						echo '<details><summary><span class="task-list__n">' . esc_html( sprintf( '%02d', $i + 1 ) ) . '</span><span class="task-list__title">' . esc_html( (string) ( $item['title'] ?? '' ) ) . '<i>+</i></span></summary><div class="task-list__body">' . esc_html( (string) ( $item['text'] ?? '' ) ) . '</div></details>';
					}
					echo '</div>';
				}
			} elseif ( 'process' === $key ) {
				echo '<div class="stepper" data-stepper><div class="stepper__track" role="tablist" aria-label="' . esc_attr__( 'Етапи співпраці', 'messcut' ) . '"><span class="stepper__fill"></span>';
				foreach ( array_values( $data ) as $i => $step ) {
					if ( ! is_array( $step ) ) {
						continue;
					}
					printf(
						'<button type="button" aria-selected="%s" aria-label="%s">%d</button>',
						0 === $i ? 'true' : 'false',
						esc_attr( sprintf( __( 'Етап %1$d: %2$s', 'messcut' ), $i + 1, (string) ( $step['title'] ?? '' ) ) ),
						$i + 1
					);
				}
				echo '</div><div class="stepper__panel" data-step-panel></div>';
				echo '<template data-steps>' . wp_json_encode( array_values( $data ), JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ) . '</template></div>';
			} elseif ( 'challenges' === $key ) {
				echo messcut_case_paragraphs( $data['text'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( ! empty( $data['callout'] ) ) {
					echo '<div class="callout">' . esc_html( (string) $data['callout'] ) . '</div>';
				}
			} elseif ( 'research' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( ! empty( $data['list'] ) && is_array( $data['list'] ) ) {
					echo '<div class="case-acc"><details' . ( ! empty( $data['listOpen'] ) ? ' open' : '' ) . '><summary>' . esc_html( (string) ( $data['listTitle'] ?? '' ) ) . '<i>+</i></summary><div class="case-acc__body"><ul class="case-bullets">';
					foreach ( $data['list'] as $line ) {
						echo '<li>' . esc_html( (string) $line ) . '</li>';
					}
					echo '</ul></div></details></div>';
				}
				if ( ! empty( $data['outro'] ) ) {
					echo '<p class="case-copy case-copy--after">' . esc_html( (string) $data['outro'] ) . '</p>';
				}
			} elseif ( 'insight' === $key ) {
				if ( ! empty( $data['text'] ) ) {
					echo messcut_case_paragraphs( $data['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '<div class="insight" data-insight><p class="insight__line">' . esc_html( (string) ( $data['line1'] ?? '' ) ) . '</p><p class="insight__sub">' . esc_html( (string) ( $data['sub1'] ?? '' ) ) . '</p><hr class="insight__rule"><p class="insight__line">' . messcut_case_marked_html( (string) ( $data['line2'] ?? '' ) ) . '</p><p class="insight__sub">' . esc_html( (string) ( $data['sub2'] ?? '' ) ) . '</p></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
			} elseif ( 'strategy' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$items = $data['items'] ?? array();
				if ( is_array( $items ) && $items ) {
					echo '<div class="case-acc">';
					foreach ( $items as $item ) {
						if ( ! is_array( $item ) ) {
							continue;
						}
						echo '<details' . ( ! empty( $item['open'] ) ? ' open' : '' ) . '><summary>' . esc_html( (string) ( $item['title'] ?? '' ) ) . '<i>+</i></summary><div class="case-acc__body">';
						foreach ( $item['blocks'] ?? array() as $block ) {
							messcut_render_case_block( $block );
						}
						echo '</div></details>';
					}
					echo '</div>';
				}
			} elseif ( 'visual' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$cards = $data['items'] ?? array();
				if ( is_array( $cards ) && $cards ) {
					echo '<div class="visual-cards">';
					foreach ( array_values( $cards ) as $i => $item ) {
						if ( ! is_array( $item ) ) {
							continue;
						}
						echo '<div><span>' . esc_html( sprintf( '%02d', $i + 1 ) ) . '</span><b>' . esc_html( (string) ( $item['title'] ?? '' ) ) . '</b><p>' . esc_html( (string) ( $item['text'] ?? '' ) ) . '</p></div>';
					}
					echo '</div>';
				}
				if ( ! empty( $data['list'] ) && is_array( $data['list'] ) ) {
					messcut_render_case_block( array( 't' => 'list', 'v' => $data['list'] ) );
				}
				if ( ! empty( $data['gallery'] ) && is_array( $data['gallery'] ) ) {
					echo '<div class="case-gallery">';
					foreach ( $data['gallery'] as $figure ) {
						if ( ! is_array( $figure ) ) {
							continue;
						}
						$url = messcut_media_url( (string) ( $figure['src'] ?? '' ) );
						if ( ! $url ) {
							continue;
						}
						echo '<figure' . ( ! empty( $figure['tall'] ) ? ' class="is-tall"' : '' ) . '><img src="' . esc_url( $url ) . '" alt="' . esc_attr( (string) ( $figure['alt'] ?? '' ) ) . '"></figure>';
					}
					echo '</div>';
				}
			} elseif ( 'support' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( ! empty( $data['tags'] ) && is_array( $data['tags'] ) ) {
					echo '<div class="tag-list">';
					foreach ( $data['tags'] as $tag ) {
						echo '<span>' . esc_html( (string) $tag ) . '</span>';
					}
					echo '</div>';
				}
				$link = $data['link'] ?? null;
				if ( is_array( $link ) && ! empty( $link['url'] ) ) {
					$url = str_replace( 'https://messcut.com/services/', messcut_page_url( 'poslugy' ) . '#svc-', (string) $link['url'] );
					echo '<p class="case-support__link"><a class="button button--ghost" href="' . esc_url( $url ) . '">' . esc_html( (string) ( $link['label'] ?? '' ) ) . '</a></p>';
				}
			} elseif ( 'outcome' === $key ) {
				if ( ! empty( $data['text'] ) ) {
					echo messcut_case_paragraphs( $data['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					if ( ! empty( $data['list'] ) && is_array( $data['list'] ) ) {
						messcut_render_case_block( array( 't' => 'list', 'v' => $data['list'] ) );
					}
				}
			}
			?>
		</div>
	</section>
	<?php
}

/**
 * @param array<string, mixed> $doc Document.
 */
function messcut_render_case_conclusion( array $doc ): void {
	$conclusion = $doc['conclusion'] ?? null;
	if ( ! is_array( $conclusion ) ) {
		return;
	}
	?>
	<section class="case-section" id="conclusion" data-chapter>
		<div class="conclusion">
			<span class="eyebrow"><?php echo esc_html( (string) ( $conclusion['label'] ?? __( 'Ключові висновки', 'messcut' ) ) ); ?></span>
			<h2><?php echo esc_html( (string) ( $conclusion['headline'] ?? '' ) ); ?></h2>
			<?php
			foreach ( (array) ( $conclusion['text'] ?? array() ) as $line ) {
				$line = trim( (string) $line );
				if ( '' === $line ) {
					continue;
				}
				echo '<p>' . esc_html( $line ) . '</p>';
			}
			?>
		</div>
	</section>
	<?php
}

/**
 * One strategy or list block.
 *
 * @param mixed $block Block.
 */
function messcut_render_case_block( mixed $block ): void {
	if ( ! is_array( $block ) ) {
		return;
	}
	$type  = (string) ( $block['t'] ?? 'text' );
	$value = $block['v'] ?? '';
	if ( 'list' === $type && is_array( $value ) ) {
		echo '<ul class="case-bullets">';
		foreach ( $value as $line ) {
			echo '<li>' . wp_kses( (string) $line, array( 'b' => array(), 'strong' => array() ) ) . '</li>';
		}
		echo '</ul>';
		return;
	}
	if ( 'quote' === $type ) {
		echo '<p class="case-quote">' . esc_html( (string) $value ) . '</p>';
		return;
	}
	if ( 'message' === $type ) {
		echo '<p class="case-message">' . esc_html( (string) $value ) . '</p>';
		return;
	}
	if ( '' === trim( (string) $value ) ) {
		return;
	}
	echo '<p>' . esc_html( (string) $value ) . '</p>';
}

/**
 * @param array<string, mixed> $doc Document.
 */
function messcut_render_case_lead( array $doc ): void {
	$cta    = is_array( $doc['cta'] ?? null ) ? $doc['cta'] : array();
	$expert = is_array( $doc['expert'] ?? null ) ? $doc['expert'] : array( 'name' => 'Валерія', 'photo' => 'assets/img/valeria.webp' );
	$photo  = messcut_media_url( (string) ( $expert['photo'] ?? '' ) );
	?>
	<section class="case-section case-lead" id="lead-form" data-chapter>
		<h2><?php echo esc_html( (string) ( $cta['title'] ?? __( 'Обговоримо ваш проєкт', 'messcut' ) ) ); ?></h2>
		<?php echo messcut_case_paragraphs( $cta['text'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php get_template_part( 'template-parts/components/lead-form' ); ?>
		<div class="case-expert">
			<?php if ( $photo ) : ?>
				<img src="<?php echo esc_url( $photo ); ?>" alt="">
			<?php endif; ?>
			<div>
				<span class="eyebrow"><?php esc_html_e( 'Експерт проєкту', 'messcut' ); ?></span>
				<b><?php echo esc_html( (string) ( $expert['name'] ?? '' ) ); ?></b>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Other published cases.
 */
function messcut_render_case_related( int $post_id ): void {
	$query = messcut_get_cases_query( 3, $post_id );
	if ( ! $query->have_posts() ) {
		return;
	}
	?>
	<section class="case-section case-more" id="more" data-chapter>
		<h2><?php esc_html_e( 'Інші кейси', 'messcut' ); ?></h2>
		<div class="card-rail">
			<?php
			foreach ( $query->posts as $case ) {
				if ( ! $case instanceof WP_Post ) {
					continue;
				}
				get_template_part( 'template-parts/components/case-card', null, array( 'post_id' => $case->ID ) );
			}
			?>
		</div>
		<div class="case-more__all"><a class="button button--ghost" href="<?php echo esc_url( messcut_cases_archive_url() ); ?>"><?php esc_html_e( 'Переглянути всі кейси →', 'messcut' ); ?></a></div>
	</section>
	<?php
}

/**
 * Articles that continue the case.
 */
function messcut_render_case_materials(): void {
	$query = messcut_get_articles_query( 3 );
	if ( ! $query->have_posts() ) {
		return;
	}
	?>
	<section class="case-section case-reading" id="reading" data-chapter>
		<h2><?php esc_html_e( 'Корисні матеріали', 'messcut' ); ?></h2>
		<p class="case-copy"><?php esc_html_e( 'Статті, які логічно продовжують тему:', 'messcut' ); ?></p>
		<div class="case-materials">
			<?php foreach ( $query->posts as $article ) : ?>
				<?php if ( ! $article instanceof WP_Post ) { continue; } ?>
				<a href="<?php echo esc_url( get_permalink( $article ) ); ?>">
					<h3><?php echo esc_html( get_the_title( $article ) ); ?></h3>
					<i>↗</i>
				</a>
			<?php endforeach; ?>
		</div>
		<a class="case-approach" href="<?php echo esc_url( messcut_approach_url() ); ?>">
			<span><b><?php esc_html_e( 'Про наш підхід', 'messcut' ); ?></b><small><?php esc_html_e( 'Досвід та підхід Messcut', 'messcut' ); ?></small></span>
			<i>→</i>
		</a>
	</section>
	<?php
}

/**
 * Site FAQ (shared with the home page).
 */
function messcut_render_case_faq(): void {
	get_template_part(
		'template-parts/components/faq-section',
		null,
		array(
			'section_class' => 'case-section home-faq case-faq',
			'section_id'    => 'faq',
			'data_chapter'  => true,
		)
	);
}

/**
 * Resolve a theme-relative or absolute media path.
 */
function messcut_media_url( string $path ): string {
	if ( '' === $path ) {
		return '';
	}
	if ( str_starts_with( $path, 'http' ) || str_starts_with( $path, '/' ) ) {
		return $path;
	}
	if ( str_starts_with( $path, 'seed-media/' ) ) {
		$basename = substr( $path, strlen( 'seed-media/' ) );
		$path = 'valeria.jpg' === $basename ? 'assets/img/valeria.webp' : 'assets/img/cases/' . $basename;
	}
	if ( str_ends_with( strtolower( $path ), 'valeria.jpg' ) ) {
		$path = 'assets/img/valeria.webp';
	}
	$file = MESSCUT_DIR . '/' . ltrim( $path, '/' );
	if ( ! is_readable( $file ) ) {
		return '';
	}
	return MESSCUT_URI . '/' . ltrim( $path, '/' );
}
