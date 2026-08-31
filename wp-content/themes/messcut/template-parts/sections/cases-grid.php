<?php
/**
 * Cases section — Figma 150:2 (dark band, horizontal logo cards).
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$limit          = isset( $args['limit'] ) ? (int) $args['limit'] : -1;
$title          = $args['title'] ?? __( 'Кейси', 'messcut' );
$show_more      = $args['show_more'] ?? false;
$on_dark        = ! empty( $args['on_dark'] );
$section_class  = 'section cases-grid' . ( $on_dark ? ' cases-grid--on-dark' : '' );
$more_arrow_path = MESSCUT_DIR . '/assets/img/path/more-arrow.svg';
$more_arrow_svg  = is_readable( $more_arrow_path ) ? file_get_contents( $more_arrow_path ) : '';
$query     = messcut_get_cases_query( $limit );
if ( ! $query->have_posts() ) {
	return;
}
?>
<section class="<?php echo esc_attr( $section_class ); ?>">
	<div class="container">
		<div class="cases-grid__header">
			<h2 class="cases-grid__title"><?php echo esc_html( $title ); ?></h2>
			<?php if ( $show_more ) : ?>
				<a class="cases-grid__more" href="<?php echo esc_url( messcut_cases_archive_url() ); ?>">
					<span><?php esc_html_e( 'Більше', 'messcut' ); ?></span>
					<span class="cases-grid__more-arrow" aria-hidden="true">
						<?php
						if ( $more_arrow_svg ) {
							echo $more_arrow_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme SVG asset.
						}
						?>
					</span>
				</a>
			<?php endif; ?>
		</div>
		<div class="cases-grid__track">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();
				$subtitle  = function_exists( 'get_field' ) ? trim( (string) get_field( 'hero_subtitle' ) ) : '';
				$excerpt   = has_excerpt() ? trim( (string) get_the_excerpt() ) : '';
				if ( $excerpt && $subtitle && 0 === strcasecmp( $excerpt, $subtitle ) ) {
					$excerpt = '';
				}
				$has_extra = ( '' !== $subtitle || '' !== $excerpt );
				?>
				<article class="cases-grid__card" <?php echo $has_extra ? 'data-case-expand' : ''; ?>>
					<a class="cases-grid__hit" href="<?php the_permalink(); ?>">
						<span class="cases-grid__brand"><?php the_title(); ?></span>
					</a>
					<?php if ( $has_extra ) : ?>
						<div class="cases-grid__excerpt" data-case-excerpt>
							<?php if ( $subtitle ) : ?>
								<p class="cases-grid__excerpt-text"><?php echo esc_html( $subtitle ); ?></p>
							<?php endif; ?>
							<?php if ( $excerpt ) : ?>
								<p class="cases-grid__excerpt-extra"><?php echo esc_html( $excerpt ); ?></p>
							<?php endif; ?>
							<button
								type="button"
								class="cases-grid__plus"
								data-case-excerpt-toggle
								aria-expanded="false"
								aria-label="<?php esc_attr_e( 'Розгорнути', 'messcut' ); ?>"
							>
								<span class="cases-grid__plus-icon" aria-hidden="true">+</span>
							</button>
						</div>
					<?php else : ?>
						<span class="cases-grid__plus" aria-hidden="true">
							<span class="cases-grid__plus-icon">+</span>
						</span>
					<?php endif; ?>
				</article>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
