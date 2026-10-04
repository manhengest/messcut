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
	<nav class="chapter-rail" data-rail aria-label="<?php esc_attr_e( 'Розділи кейсу', 'messcut' ); ?>"></nav>
	<section class="case-hero">
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
}

/**
 * @param array<string, mixed> $doc Document.
 */
function messcut_render_case_results( array $doc ): void {
	$results = $doc['results'] ?? null;
	if ( ! is_array( $results ) || empty( $results['items'] ) ) {
		return;
	}
	?>
	<section class="case-section" id="key" data-chapter>
		<h2><?php esc_html_e( 'Ключові результати', 'messcut' ); ?></h2>
		<div class="result-grid">
			<?php foreach ( $results['items'] as $item ) : ?>
				<?php if ( ! is_array( $item ) ) { continue; } ?>
				<button class="result-card<?php echo ! empty( $item['big'] ) ? ' result-card--big' : ''; ?>" type="button" data-result-title="<?php echo esc_attr( (string) ( $item['title'] ?? $item['value'] ?? '' ) ); ?>" data-result-detail="<?php echo esc_attr( (string) ( $item['detail'] ?? '' ) ); ?>">
					<?php if ( '' !== trim( (string) ( $item['value'] ?? '' ) ) ) : ?>
						<b><?php echo esc_html( (string) $item['value'] ); ?></b>
					<?php endif; ?>
					<span><?php echo esc_html( (string) ( $item['label'] ?? '' ) ); ?></span>
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
		'visual'     => __( 'Візуальна складова бренду', 'messcut' ),
		'support'    => __( 'Маркетинговий супровід', 'messcut' ),
		'outcome'    => __( 'Результат', 'messcut' ),
	);
	?>
	<section class="case-section case-section--split" id="<?php echo esc_attr( $key ); ?>" data-chapter>
		<div class="case-section__aside">
			<div class="eyebrow"><?php echo esc_html( sprintf( '%02d / %02d', $n, $total ) ); ?></div>
			<h2><?php echo esc_html( $titles[ $key ] ?? '' ); ?></h2>
		</div>
		<div>
			<?php
			if ( 'tasks' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				$items = $data['items'] ?? array();
				if ( $items ) {
					echo '<div class="task-list">';
					foreach ( $items as $i => $item ) {
						if ( ! is_array( $item ) ) {
							continue;
						}
						echo '<details><summary><span class="eyebrow">' . esc_html( sprintf( '%02d', $i + 1 ) ) . '</span><span>' . esc_html( (string) ( $item['title'] ?? '' ) ) . '</span></summary><div class="task-list__body">' . esc_html( (string) ( $item['text'] ?? '' ) ) . '</div></details>';
					}
					echo '</div>';
				}
			} elseif ( 'process' === $key ) {
				echo '<div class="stepper" data-stepper><div class="stepper__track" role="tablist">';
				foreach ( array_values( $data ) as $i => $step ) {
					if ( ! is_array( $step ) ) {
						continue;
					}
					printf( '<button type="button" aria-selected="%s">%02d</button>', 0 === $i ? 'true' : 'false', $i + 1 );
				}
				echo '</div><div class="stepper__panel" data-step-panel></div>';
				echo '<template data-steps>' . wp_json_encode( array_values( $data ), JSON_HEX_TAG | JSON_HEX_AMP ) . '</template></div>';
			} elseif ( 'challenges' === $key ) {
				echo messcut_case_paragraphs( $data['text'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( ! empty( $data['callout'] ) ) {
					echo '<div class="callout">' . esc_html( (string) $data['callout'] ) . '</div>';
				}
			} elseif ( 'research' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( ! empty( $data['list'] ) && is_array( $data['list'] ) ) {
					echo '<div class="accordion"><details open><summary>' . esc_html( (string) ( $data['listTitle'] ?? '' ) ) . ' <i>+</i></summary><ul>';
					foreach ( $data['list'] as $line ) {
						echo '<li>' . esc_html( (string) $line ) . '</li>';
					}
					echo '</ul></details></div>';
				}
			} elseif ( 'insight' === $key ) {
				if ( ! empty( $data['text'] ) ) {
					echo messcut_case_paragraphs( $data['text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					echo '<div class="insight-block"><p class="insight-block__line">' . esc_html( (string) ( $data['line1'] ?? '' ) ) . '</p><p>' . esc_html( (string) ( $data['sub1'] ?? '' ) ) . '</p><p class="insight-block__line">' . esc_html( str_replace( array( '{', '}' ), '', (string) ( $data['line2'] ?? '' ) ) ) . '</p><p>' . esc_html( (string) ( $data['sub2'] ?? '' ) ) . '</p></div>';
				}
			} elseif ( 'strategy' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				foreach ( $data['items'] ?? array() as $item ) {
					if ( ! is_array( $item ) ) {
						continue;
					}
					echo '<details class="accordion" ' . ( ! empty( $item['open'] ) ? 'open' : '' ) . '><summary>' . esc_html( (string) ( $item['title'] ?? '' ) ) . ' <i>+</i></summary>';
					foreach ( $item['blocks'] ?? array() as $block ) {
						if ( ! is_array( $block ) ) {
							continue;
						}
						$type = (string) ( $block['t'] ?? 'text' );
						$value = $block['v'] ?? '';
						if ( 'list' === $type && is_array( $value ) ) {
							echo '<ul>';
							foreach ( $value as $line ) {
								echo '<li>' . wp_kses( (string) $line, array( 'b' => array(), 'strong' => array() ) ) . '</li>';
							}
							echo '</ul>';
						} else {
							echo '<p class="case-copy">' . esc_html( (string) $value ) . '</p>';
						}
					}
					echo '</details>';
				}
			} elseif ( 'visual' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<div class="task-list">';
				foreach ( $data['items'] ?? array() as $item ) {
					if ( ! is_array( $item ) ) {
						continue;
					}
					echo '<div class="principle"><h3>' . esc_html( (string) ( $item['title'] ?? '' ) ) . '</h3><p>' . esc_html( (string) ( $item['text'] ?? '' ) ) . '</p></div>';
				}
				echo '</div>';
				if ( ! empty( $data['gallery'] ) ) {
					echo '<div class="case-gallery">';
					foreach ( $data['gallery'] as $figure ) {
						if ( ! is_array( $figure ) ) {
							continue;
						}
						$url = messcut_media_url( (string) ( $figure['src'] ?? '' ) );
						if ( $url ) {
							echo '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( (string) ( $figure['alt'] ?? '' ) ) . '">';
						}
					}
					echo '</div>';
				}
			} elseif ( 'support' === $key ) {
				echo messcut_case_paragraphs( $data['lead'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				if ( ! empty( $data['tags'] ) ) {
					echo '<div class="tag-list">';
					foreach ( $data['tags'] as $tag ) {
						echo '<span>' . esc_html( (string) $tag ) . '</span>';
					}
					echo '</div>';
				}
			} elseif ( 'outcome' === $key ) {
				echo messcut_case_paragraphs( $data['text'] ?? $data['lead'] ?? $data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
	<section class="case-section">
		<div class="conclusion">
			<span class="eyebrow"><?php echo esc_html( (string) ( $conclusion['label'] ?? __( 'Ключові висновки', 'messcut' ) ) ); ?></span>
			<h2><?php echo esc_html( (string) ( $conclusion['headline'] ?? '' ) ); ?></h2>
		</div>
	</section>
	<?php
}

/**
 * @param array<string, mixed> $doc Document.
 */
function messcut_render_case_lead( array $doc ): void {
	$cta    = is_array( $doc['cta'] ?? null ) ? $doc['cta'] : array();
	$expert = is_array( $doc['expert'] ?? null ) ? $doc['expert'] : array( 'name' => 'Валерія', 'photo' => 'assets/img/valeria.jpg' );
	$photo  = messcut_media_url( (string) ( $expert['photo'] ?? '' ) );
	?>
	<section class="section case-lead" id="lead-form">
		<div class="case-expert">
			<?php if ( $photo ) : ?><img src="<?php echo esc_url( $photo ); ?>" alt=""><?php endif; ?>
			<div>
				<span class="eyebrow"><?php esc_html_e( 'Експерт проєкту', 'messcut' ); ?></span>
				<b><?php echo esc_html( (string) ( $expert['name'] ?? '' ) ); ?></b>
			</div>
		</div>
		<h2><?php echo esc_html( (string) ( $cta['title'] ?? __( 'Обговоримо ваш проєкт', 'messcut' ) ) ); ?></h2>
		<?php echo messcut_case_paragraphs( $cta['text'] ?? '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php get_template_part( 'template-parts/components/lead-form' ); ?>
	</section>
	<?php
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
		$path = 'valeria.jpg' === $basename ? 'assets/img/valeria.jpg' : 'assets/img/cases/' . $basename;
	}
	$file = MESSCUT_DIR . '/' . ltrim( $path, '/' );
	if ( ! is_readable( $file ) ) {
		return '';
	}
	return MESSCUT_URI . '/' . ltrim( $path, '/' );
}
