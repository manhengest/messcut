<?php
/**
 * Insights tiles — same card band as cases (Figma 150:2).
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id    = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$type_ids   = isset( $args['type_ids'] ) ? (array) $args['type_ids'] : messcut_get_insights_type_ids( $post_id );
$limit      = isset( $args['limit'] ) ? (int) $args['limit'] : 3;
$title      = $args['title'] ?? __( 'Інсайти', 'messcut' );
$show_title = $args['show_title'] ?? true;
$show_more       = $args['show_more'] ?? true;
$more_arrow_path = MESSCUT_DIR . '/assets/img/path/more-arrow.svg';
$more_arrow_svg  = is_readable( $more_arrow_path ) ? file_get_contents( $more_arrow_path ) : '';

$query_args = array(
	'post_type'      => 'article',
	'posts_per_page' => $limit,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
);

if ( ! empty( $type_ids ) ) {
	$query_args['tax_query'] = array(
		array(
			'taxonomy' => 'article_type',
			'field'    => 'term_id',
			'terms'    => array_map( 'intval', $type_ids ),
		),
	);
}

$query = new WP_Query( $query_args );
if ( ! $query->have_posts() ) {
	wp_reset_postdata();
	return;
}
?>
<section class="section insights-tiles">
	<div class="container">
		<?php if ( $show_title || $show_more ) : ?>
			<div class="insights-tiles__header">
				<?php if ( $show_title ) : ?>
					<h2 class="insights-tiles__title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( $show_more ) : ?>
					<a class="insights-tiles__more" href="<?php echo esc_url( messcut_insights_archive_url() ); ?>">
						<span><?php esc_html_e( 'Більше', 'messcut' ); ?></span>
						<span class="insights-tiles__more-arrow" aria-hidden="true">
							<?php
							if ( $more_arrow_svg ) {
								echo $more_arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG asset.
							}
							?>
						</span>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="insights-tiles__track">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$excerpt   = has_excerpt() ? trim( (string) get_the_excerpt() ) : '';
				$has_extra = ( '' !== $excerpt );
				?>
				<article class="insights-tiles__card" <?php echo $has_extra ? 'data-case-expand' : ''; ?>>
					<a class="insights-tiles__hit" href="<?php the_permalink(); ?>">
						<span class="insights-tiles__brand"><?php the_title(); ?></span>
					</a>
					<?php if ( $has_extra ) : ?>
						<div class="insights-tiles__excerpt" data-case-excerpt>
							<p class="insights-tiles__excerpt-text"><?php echo esc_html( $excerpt ); ?></p>
							<button
								type="button"
								class="insights-tiles__plus"
								data-case-excerpt-toggle
								aria-expanded="false"
								aria-label="<?php esc_attr_e( 'Розгорнути', 'messcut' ); ?>"
							>
								<span class="insights-tiles__plus-icon" aria-hidden="true">+</span>
							</button>
						</div>
					<?php else : ?>
						<span class="insights-tiles__plus" aria-hidden="true">
							<span class="insights-tiles__plus-icon">+</span>
						</span>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
		</div>
	</div>
</section>
<?php
wp_reset_postdata();
