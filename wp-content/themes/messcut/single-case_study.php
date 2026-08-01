<?php
/**
 * Single case study.
 *
 * @package Messcut
 */

get_header();

while ( have_posts() ) :
	the_post();

	$subtitle          = messcut_get_acf( 'hero_subtitle' );
	$intro             = messcut_get_acf( 'intro' );
	$results           = messcut_get_acf( 'results' ) ?: array();
	$client_task       = messcut_get_acf( 'client_task' );
	$collaboration     = messcut_get_acf( 'collaboration_process' );
	$difficulties      = messcut_get_acf( 'difficulties' );
	$challenge         = messcut_get_acf( 'challenge' );
	$research          = messcut_get_acf( 'research' );
	$insight           = messcut_get_acf( 'insight' );
	$brand_strategy    = messcut_get_acf( 'brand_strategy' );
	$brand_mission     = messcut_get_acf( 'brand_mission' );
	$positioning       = messcut_get_acf( 'positioning' );
	$brand_message     = messcut_get_acf( 'brand_message' );
	$visual_identity   = messcut_get_acf( 'visual_identity' );
	$case_demonstrates = messcut_get_acf( 'case_demonstrates' );
	$specialist_name   = messcut_get_acf( 'specialist_name' );
	$specialist_role   = messcut_get_acf( 'specialist_role' );
	$cta_title         = messcut_get_acf( 'cta_title' );
	$cta_text          = messcut_get_acf( 'cta_text' );
	?>
	<article <?php post_class( 'case-single' ); ?>>
		<header class="section case-single__header">
			<div class="container container--narrow">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="case-single__media"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php endif; ?>
				<h1><?php the_title(); ?></h1>
				<?php if ( $subtitle ) : ?>
					<p class="case-single__subtitle"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
			</div>
		</header>

		<?php messcut_render_content_block( '', $intro ); ?>

		<?php if ( ! empty( $results ) ) : ?>
		<section class="section case-results">
			<div class="container container--narrow">
				<h2><?php esc_html_e( 'Результати', 'messcut' ); ?></h2>
				<ul>
					<?php foreach ( $results as $row ) : ?>
						<?php if ( ! empty( $row['text'] ) ) : ?>
							<li><?php echo esc_html( $row['text'] ); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php endif; ?>

		<?php messcut_render_mid_cta( $cta_title ?: messcut_cta_label( 'discuss' ) ); ?>

		<?php
		$blocks = array(
			__( 'Завдання від замовника', 'messcut' ) => $client_task,
			__( 'Процес співпраці', 'messcut' )       => $collaboration,
			__( 'Виклики проєкту', 'messcut' )        => $challenge ?: $difficulties,
			__( 'Дослідження', 'messcut' )            => $research,
			__( 'Стратегічний інсайт', 'messcut' )    => $insight,
			__( 'Стратегія бренду', 'messcut' )       => $brand_strategy,
			__( 'Місія бренду', 'messcut' )           => $brand_mission,
			__( 'Позиціонування', 'messcut' )         => $positioning,
			__( 'Меседж бренду', 'messcut' )          => $brand_message,
			__( 'Візуальна складова бренду', 'messcut' ) => $visual_identity,
			__( 'Ключові висновки', 'messcut' )       => $case_demonstrates,
		);
		foreach ( $blocks as $heading => $content ) {
			messcut_render_content_block( $heading, $content );
		}
		?>

		<?php messcut_render_case_sections(); ?>

		<?php if ( $specialist_name || $specialist_role ) : ?>
		<section class="section specialist-badge">
			<div class="container container--narrow">
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

		<?php
		get_template_part( 'template-parts/sections/cta', null, array(
			'title' => $cta_title ?: messcut_cta_label( 'discuss' ),
			'text'  => $cta_text,
		) );
		messcut_render_approach_cta();
		messcut_render_insights_tiles();
		messcut_render_faq();
		?>
	</article>
	<?php
endwhile;

get_footer();
