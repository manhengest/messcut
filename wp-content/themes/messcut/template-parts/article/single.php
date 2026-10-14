<?php
/**
 * Single article layout (design/raw/article-1, without site header/footer).
 *
 * @package Messcut
 */

$post_id      = get_the_ID();
$title_html   = (string) ( messcut_get_acf( 'title_html', $post_id ) ?: '' );
$topic        = (string) ( messcut_get_acf( 'topic', $post_id ) ?: '' );
$reading_time = (string) ( messcut_get_acf( 'reading_time', $post_id ) ?: '' );
$hero_tone    = (string) ( messcut_get_acf( 'hero_tone', $post_id ) ?: 'mint' );
$lead         = has_excerpt() ? get_the_excerpt() : '';
$archive_url  = messcut_insights_archive_url();
$hero_class   = 'article-hero';

if ( 'beige' === $hero_tone ) {
	$hero_class .= ' article-hero--beige';
}

if ( '' === $title_html ) {
	$title_html = esc_html( get_the_title() );
}
?>
<section class="<?php echo esc_attr( $hero_class ); ?>">
	<div class="article-hero__inner">
		<div class="article-hero__crumbs">
			<a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Інсайти', 'messcut' ); ?></a>
			<?php if ( $topic ) : ?>
				<span aria-hidden="true"> / </span>
				<span><?php echo esc_html( $topic ); ?></span>
			<?php endif; ?>
		</div>
		<h1><?php echo wp_kses( $title_html, array( 'em' => array() ) ); ?></h1>
		<?php if ( $lead ) : ?>
			<p class="article-hero__lead"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
		<?php if ( $reading_time ) : ?>
			<div class="article-hero__meta">
				<div>
					<small><?php esc_html_e( 'Час читання', 'messcut' ); ?></small>
					<b><?php echo esc_html( $reading_time ); ?></b>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<div class="article-layout">
	<article class="article-body" id="article">
		<?php echo messcut_get_article_content_html( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- kses inside helper. ?>
		<div class="article-fin">
			<span class="article-fin__brand"><?php esc_html_e( 'Messcut · Інсайти', 'messcut' ); ?></span>
			<div class="article-fin__share">
				<button type="button" data-article-copy><?php esc_html_e( 'Скопіювати посилання', 'messcut' ); ?></button>
				<a href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'Усі статті', 'messcut' ); ?> <?php messcut_render_link_arrow_icon(); ?></a>
			</div>
		</div>
	</article>
</div>

<?php
get_template_part(
	'template-parts/components/close-band',
	null,
	array(
		'eyebrow' => __( 'Наступний крок — ваш', 'messcut' ),
		'title'   => __( 'Розкажіть про свій бренд — <em>знайдемо точку росту</em>', 'messcut' ),
	)
);
?>
