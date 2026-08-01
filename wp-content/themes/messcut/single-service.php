<?php
/**
 * Single service.
 *
 * @package Messcut
 */

get_header();

while ( have_posts() ) :
	the_post();

	$headline       = messcut_get_acf( 'headline' );
	$short          = messcut_get_acf( 'short_description' );
	$problems       = messcut_get_acf( 'problems_solved' );
	$for_whom       = messcut_get_acf( 'for_whom' );
	$stages         = messcut_get_acf( 'process_stages' ) ?: array();
	$result         = messcut_get_acf( 'result' );
	$questions      = messcut_get_acf( 'questions' ) ?: array();
	$interaction    = messcut_get_acf( 'interaction_format' );
	$show_compare   = messcut_get_acf( 'show_services_comparison' );
	if ( null === $show_compare || '' === $show_compare ) {
		$show_compare = true;
	}
	$cta_title      = messcut_get_acf( 'cta_title' );
	?>
	<article <?php post_class( 'service-single' ); ?>>
		<header class="section service-single__header">
			<div class="container container--narrow">
				<p class="service-single__label"><?php the_title(); ?></p>
				<?php if ( $headline ) : ?>
					<h1 class="service-single__headline"><?php echo esc_html( $headline ); ?></h1>
				<?php else : ?>
					<h1><?php the_title(); ?></h1>
				<?php endif; ?>
				<?php if ( $short ) : ?>
					<p class="service-single__lead"><?php echo esc_html( $short ); ?></p>
				<?php endif; ?>
				<p><a class="button button--primary" href="#lead-form"><?php echo esc_html( messcut_cta_label( 'discuss' ) ); ?></a></p>
			</div>
		</header>

		<?php messcut_render_content_block( __( 'Які питання та проблеми вирішує', 'messcut' ), $problems ); ?>

		<?php if ( ! empty( $questions ) ) : ?>
		<section class="section">
			<div class="container container--narrow">
				<ul>
					<?php foreach ( $questions as $row ) : ?>
						<?php if ( ! empty( $row['text'] ) ) : ?>
							<li><?php echo esc_html( $row['text'] ); ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			</div>
		</section>
		<?php endif; ?>

		<?php messcut_render_content_block( __( 'Результат послуги', 'messcut' ), $result ); ?>
		<?php messcut_render_content_block( __( 'Для кого підійде', 'messcut' ), $for_whom ); ?>

		<?php messcut_render_mid_cta(); ?>

		<?php if ( ! empty( $stages ) ) : ?>
		<section class="section">
			<div class="container container--narrow">
				<h2><?php esc_html_e( 'Етапність роботи', 'messcut' ); ?></h2>
				<?php foreach ( $stages as $stage ) : ?>
					<div class="service-stage">
						<?php if ( ! empty( $stage['title'] ) ) : ?>
							<h3><?php echo esc_html( $stage['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( ! empty( $stage['content'] ) ) : ?>
							<div class="entry-content"><?php echo wp_kses_post( $stage['content'] ); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

		<?php messcut_render_content_block( __( 'Формат взаємодії', 'messcut' ), $interaction ); ?>

		<?php if ( $show_compare ) : ?>
			<?php messcut_render_services_comparison(); ?>
		<?php endif; ?>

		<?php
		get_template_part( 'template-parts/sections/cta', null, array(
			'title' => $cta_title ?: messcut_cta_label( 'discuss' ),
		) );
		?>

		<section class="section service-links">
			<div class="container container--narrow">
				<p>
					<a class="button button--secondary" href="<?php echo esc_url( messcut_cases_archive_url() ); ?>">
						<?php esc_html_e( 'Переглянути кейси', 'messcut' ); ?>
					</a>
				</p>
			</div>
		</section>

		<?php messcut_render_approach_cta(); ?>
		<?php messcut_render_insights_tiles(); ?>
		<?php messcut_render_faq(); ?>
	</article>
	<?php
endwhile;

get_footer();
