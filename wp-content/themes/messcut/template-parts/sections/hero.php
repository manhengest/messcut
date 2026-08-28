<?php
/**
 * Hero section.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title    = $args['title'] ?? messcut_get_localized_option( 'home_hero_title', __( 'Бренд-стратегія та науковий маркетинг', 'messcut' ) );
$subtitle = $args['subtitle'] ?? messcut_get_localized_option( 'home_hero_subtitle', __( 'Будуємо маркетингові системи та допомагаємо бізнесу масштабуватися на основі досліджень', 'messcut' ) );
$cta      = $args['cta_label'] ?? messcut_cta_label( 'discuss' );
$descriptor = $args['cta_descriptor'] ?? __( '30-хвилинна стратегічна зустріч', 'messcut' );

$video      = messcut_get_option( 'home_hero_video', null );
$poster     = messcut_get_option( 'home_hero_poster', null );
$poster_url = '';
if ( is_array( $poster ) && ! empty( $poster['url'] ) ) {
	$poster_url = (string) $poster['url'];
} elseif ( is_numeric( $poster ) ) {
	$poster_url = (string) wp_get_attachment_image_url( (int) $poster, 'full' );
}

$theme_video_path = MESSCUT_DIR . '/assets/video/mess.mp4';
$video_url        = '';
if ( is_array( $video ) && ! empty( $video['url'] ) ) {
	$video_url = (string) $video['url'];
} elseif ( file_exists( $theme_video_path ) ) {
	$video_url = MESSCUT_URI . '/assets/video/mess.mp4';
	if ( is_readable( $theme_video_path ) ) {
		$video_url .= '?v=' . (string) filemtime( $theme_video_path );
	}
}

$hero_classes = 'section hero hero--band surface--gradient-light';
if ( $video_url ) {
	$hero_classes .= ' hero--has-video';
}
?>
<section class="<?php echo esc_attr( $hero_classes ); ?>">
	<?php if ( $video_url ) : ?>
		<div class="hero__video-wrap" aria-hidden="true">
			<video
				class="hero__video"
				data-hero-video
				data-src="<?php echo esc_url( $video_url ); ?>"
				muted
				loop
				playsinline
				preload="none"
				<?php echo $poster_url ? 'poster="' . esc_url( $poster_url ) . '"' : ''; ?>
			></video>
		</div>
	<?php endif; ?>
	<div class="container">
		<div class="hero__inner">
			<h1 class="hero__title"><?php echo esc_html( $title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="hero__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
			<div class="hero__cta-wrap">
				<a class="button button--accent" href="#lead-form"><?php echo esc_html( $cta ); ?></a>
				<?php if ( $descriptor ) : ?>
					<p class="hero__cta-descriptor"><?php echo esc_html( $descriptor ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
