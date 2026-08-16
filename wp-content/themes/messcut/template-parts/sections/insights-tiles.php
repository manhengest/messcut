<?php
/**
 * Insights (blog) tiles — horizontal scroll carousel.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id   = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$type_ids  = isset( $args['type_ids'] ) ? (array) $args['type_ids'] : messcut_get_insights_type_ids( $post_id );
$limit     = isset( $args['limit'] ) ? (int) $args['limit'] : 3;
$show_title = $args['show_title'] ?? false;

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
		<?php if ( $show_title ) : ?>
			<h2 class="section__title"><?php esc_html_e( 'Інсайти', 'messcut' ); ?></h2>
		<?php endif; ?>
		<div class="insights-tiles__track">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				?>
				<article <?php post_class( 'card card--insight' ); ?>>
					<a class="card__media" href="<?php the_permalink(); ?>">
						<?php messcut_render_post_thumbnail( 'medium' ); ?>
					</a>
					<h3 class="card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<a class="card__link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'читати більше', 'messcut' ); ?></a>
				</article>
			<?php endwhile; ?>
		</div>
		<p class="insights-tiles__more">
			<a href="<?php echo esc_url( messcut_insights_archive_url() ); ?>"><?php esc_html_e( 'Усі інсайти', 'messcut' ); ?> →</a>
		</p>
	</div>
</section>
<?php
wp_reset_postdata();
