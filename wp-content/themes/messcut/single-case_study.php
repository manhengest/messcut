<?php
/**
 * Single case study.
 *
 * @package Messcut
 */

get_header();

while ( have_posts() ) :
	the_post();

	$subtitle          = trim( (string) messcut_get_acf( 'hero_subtitle' ) );
	$intro             = messcut_get_acf( 'intro' );
	$results           = messcut_get_acf( 'results' ) ?: array();
	$mid_cta_title     = trim( (string) messcut_get_acf( 'mid_cta_title' ) );
	$mid_cta_text      = trim( (string) messcut_get_acf( 'mid_cta_text' ) );
	$client_task       = messcut_get_acf( 'client_task' );
	$collaboration     = messcut_get_acf( 'collaboration_process' );
	$process_steps     = messcut_get_acf( 'process_steps' ) ?: array();
	$difficulties      = messcut_get_acf( 'difficulties' );
	$challenge         = messcut_get_acf( 'challenge' );
	$research          = messcut_get_acf( 'research' );
	$insight           = messcut_get_acf( 'insight' );
	$brand_strategy    = messcut_get_acf( 'brand_strategy' );
	$brand_mission     = messcut_get_acf( 'brand_mission' );
	$positioning       = messcut_get_acf( 'positioning' );
	$brand_message     = messcut_get_acf( 'brand_message' );
	$visual_identity   = messcut_get_acf( 'visual_identity' );
	$marketing_strategy = messcut_get_acf( 'marketing_strategy' );
	$results_detail    = messcut_get_acf( 'results_detail' );
	$marketing_support = messcut_get_acf( 'marketing_support' );
	$case_demonstrates = messcut_get_acf( 'case_demonstrates' );
	$specialist_name   = trim( (string) messcut_get_acf( 'specialist_name' ) );
	$specialist_role   = trim( (string) messcut_get_acf( 'specialist_role' ) );
	$specialist_photo  = messcut_acf_media( messcut_get_acf( 'specialist_photo' ), 'medium' );
	$cta_title         = messcut_get_acf( 'cta_title' );
	$cta_text          = messcut_get_acf( 'cta_text' );
	$related_articles  = messcut_get_acf( 'related_articles' );
	$headline          = '' !== $subtitle ? $subtitle : get_the_title();
	$has_metrics       = false;

	if ( ! is_array( $results ) ) {
		$results = array();
	}
	if ( ! is_array( $process_steps ) ) {
		$process_steps = array();
	}
	if ( ! is_array( $related_articles ) ) {
		$related_articles = array();
	}

	foreach ( $results as $row ) {
		if ( is_array( $row ) && '' !== trim( (string) ( $row['value'] ?? '' ) ) ) {
			$has_metrics = true;
			break;
		}
	}

	if ( '' === $specialist_photo['url'] && '' !== $specialist_name && preg_match( '/валер|valeri/iu', $specialist_name ) ) {
		$specialist_photo['url'] = messcut_consult_cta_avatar_url();
		$specialist_photo['alt'] = $specialist_name;
	}

	$strategy_subs = array(
		__( 'Місія бренду', 'messcut' )              => is_string( $brand_mission ) ? $brand_mission : '',
		__( 'Позиціонування', 'messcut' )            => is_string( $positioning ) ? $positioning : '',
		__( 'Візуальна складова бренду', 'messcut' ) => is_string( $visual_identity ) ? $visual_identity : '',
		__( 'Маркетингова стратегія', 'messcut' )    => is_string( $marketing_strategy ) ? $marketing_strategy : '',
		__( 'Меседж бренду', 'messcut' )             => is_string( $brand_message ) ? $brand_message : '',
	);
	$nest_strategy = is_string( $brand_strategy ) && '' !== trim( wp_strip_all_tags( $brand_strategy ) );
	?>
	<article <?php post_class( 'case-single' ); ?>>
		<header class="section case-single__header">
			<div class="container">
				<div class="case-single__media">
					<?php messcut_render_post_thumbnail( 'large' ); ?>
				</div>
				<?php if ( '' !== $subtitle ) : ?>
					<p class="case-single__brand"><?php the_title(); ?></p>
				<?php endif; ?>
				<h1><?php echo esc_html( $headline ); ?></h1>
				<?php if ( $specialist_name || $specialist_role || '' !== $specialist_photo['url'] ) : ?>
					<section class="specialist-badge">
						<h2><?php esc_html_e( 'Експерт проєкту', 'messcut' ); ?></h2>
						<div class="specialist-badge__layout">
							<?php if ( '' !== $specialist_photo['url'] ) : ?>
								<img
									class="specialist-badge__photo"
									src="<?php echo esc_url( $specialist_photo['url'] ); ?>"
									alt="<?php echo esc_attr( $specialist_photo['alt'] ?: $specialist_name ); ?>"
									width="<?php echo esc_attr( (string) ( $specialist_photo['width'] ?: 240 ) ); ?>"
									height="<?php echo esc_attr( (string) ( $specialist_photo['height'] ?: 240 ) ); ?>"
									decoding="async"
								>
							<?php endif; ?>
							<div class="specialist-badge__inner">
								<?php if ( $specialist_name ) : ?>
									<strong class="specialist-badge__name"><?php echo esc_html( $specialist_name ); ?></strong>
								<?php endif; ?>
								<?php if ( $specialist_role ) : ?>
									<span class="specialist-badge__role"><?php echo esc_html( $specialist_role ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</section>
				<?php endif; ?>
			</div>
		</header>

		<?php messcut_render_content_block( '', $intro, array(), 'content-block--lead' ); ?>

		<?php if ( ! empty( $results ) ) : ?>
		<section class="section case-results">
			<div class="container">
				<h2><?php echo esc_html( $has_metrics ? __( 'Ключові результати', 'messcut' ) : __( 'Результати', 'messcut' ) ); ?></h2>
				<?php if ( $has_metrics ) : ?>
					<ul class="case-results__grid">
						<?php
						$feature_placed = false;
						foreach ( $results as $row ) :
							?>
							<?php
							if ( ! is_array( $row ) ) {
								continue;
							}
							$value = trim( (string) ( $row['value'] ?? '' ) );
							$text  = trim( (string) ( $row['text'] ?? '' ) );
							if ( '' === $value && '' === $text ) {
								continue;
							}
							$is_feature = ! $feature_placed && (bool) preg_match( '/\d/u', $value );
							if ( $is_feature ) {
								$feature_placed = true;
							}
							?>
							<li class="case-results__card<?php echo $is_feature ? ' case-results__card--feature' : ''; ?>">
								<?php if ( '' !== $value ) : ?>
									<p class="case-results__value"><?php echo esc_html( $value ); ?></p>
								<?php endif; ?>
								<?php if ( '' !== $text ) : ?>
									<p class="case-results__text"><?php echo esc_html( $text ); ?></p>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<ul>
						<?php foreach ( $results as $row ) : ?>
							<?php if ( ! empty( $row['text'] ) ) : ?>
								<li><?php echo esc_html( $row['text'] ); ?></li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</section>
		<?php endif; ?>

		<?php
		if ( '' !== $mid_cta_title || '' !== $mid_cta_text ) {
			messcut_render_mid_cta( __( 'Обговорити проєкт', 'messcut' ), $mid_cta_title, $mid_cta_text );
		} else {
			messcut_render_mid_cta( is_string( $cta_title ) && '' !== trim( $cta_title ) ? $cta_title : __( 'Обговорити проєкт', 'messcut' ) );
		}

		messcut_render_content_block( __( 'Завдання', 'messcut' ), $client_task );

		$steps = array();
		foreach ( $process_steps as $step ) {
			if ( ! is_array( $step ) ) {
				continue;
			}
			$title = trim( (string) ( $step['title'] ?? '' ) );
			$text  = trim( (string) ( $step['text'] ?? '' ) );
			if ( '' === $title && '' === $text ) {
				continue;
			}
			$steps[] = array(
				'title' => $title,
				'text'  => $text,
			);
		}

		if ( $steps ) :
			?>
			<section class="section case-process">
				<div class="container">
					<h2><?php esc_html_e( 'Процес співпраці', 'messcut' ); ?></h2>
					<ol class="case-process__list">
						<?php foreach ( $steps as $step ) : ?>
							<li class="case-process__step">
								<span class="case-process__mark" aria-hidden="true"></span>
								<div class="case-process__body">
									<?php if ( '' !== $step['title'] ) : ?>
										<h3 class="case-process__title"><?php echo esc_html( $step['title'] ); ?></h3>
									<?php endif; ?>
									<?php if ( '' !== $step['text'] ) : ?>
										<p class="case-process__text"><?php echo esc_html( $step['text'] ); ?></p>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ol>
				</div>
			</section>
			<?php
		else :
			messcut_render_content_block( __( 'Процес співпраці', 'messcut' ), $collaboration );
		endif;

		messcut_render_content_block( __( 'Виклики проєкту', 'messcut' ), $challenge ?: $difficulties );
		messcut_render_content_block( __( 'Дослідження', 'messcut' ), $research );
		messcut_render_content_block( __( 'Стратегічний інсайт', 'messcut' ), $insight, array(), 'content-block--quote' );

		if ( $nest_strategy ) {
			messcut_render_content_block( __( 'Стратегія бренду', 'messcut' ), $brand_strategy, $strategy_subs, 'content-block--strategy' );
		} else {
			messcut_render_content_block( __( 'Місія бренду', 'messcut' ), $brand_mission );
			messcut_render_content_block( __( 'Позиціонування', 'messcut' ), $positioning );
			messcut_render_content_block( __( 'Візуальна складова бренду', 'messcut' ), $visual_identity );
			messcut_render_content_block( __( 'Маркетингова стратегія', 'messcut' ), $marketing_strategy );
			messcut_render_content_block( __( 'Меседж бренду', 'messcut' ), $brand_message );
		}

		messcut_render_content_block( __( 'Результати', 'messcut' ), $results_detail, array(), 'content-block--results' );
		messcut_render_content_block( __( 'Marketing Support', 'messcut' ), $marketing_support );
		messcut_render_content_block( __( 'Ключові висновки', 'messcut' ), $case_demonstrates, array(), 'content-block--note' );

		messcut_render_case_sections();
		?>

		<?php
		get_template_part( 'template-parts/sections/cta', null, array(
			'title' => is_string( $cta_title ) && '' !== trim( $cta_title ) ? $cta_title : __( 'Обговоримо ваш проєкт', 'messcut' ),
			'text'  => $cta_text,
		) );
		?>

		<?php
		get_template_part(
			'template-parts/sections/cases-grid',
			null,
			array(
				'title'      => __( 'Інші кейси', 'messcut' ),
				'limit'      => 3,
				'exclude'    => get_the_ID(),
				'show_more'  => true,
				'more_label' => __( 'Переглянути всі кейси', 'messcut' ),
			)
		);

		$article_ids = array();
		foreach ( $related_articles as $article ) {
			$id = is_object( $article ) ? (int) $article->ID : (int) $article;
			if ( $id > 0 ) {
				$article_ids[] = $id;
			}
		}

		if ( $article_ids ) {
			messcut_render_insights_tiles(
				array(
					'title'     => __( 'Корисні матеріали', 'messcut' ),
					'post_ids'  => $article_ids,
					'show_more' => false,
					'limit'     => 3,
				)
			);
		}
		messcut_render_faq();
		?>
	</article>
	<?php
endwhile;

get_footer();
