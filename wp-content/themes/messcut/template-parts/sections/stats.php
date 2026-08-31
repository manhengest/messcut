<?php
/**
 * Stats section — Figma why-choose-us mosaic (node 131:53).
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = $args['items'] ?? array();
$title = $args['title'] ?? __( 'Чому нас обирають?', 'messcut' );

if ( empty( $items ) ) {
	$items = messcut_get_why_stats();
}

$copy_slots = array(
	'recommend' => '94',
	'partners'  => '50',
	'nonstop'   => 'nonstop',
	'years'     => '6',
	'ratio'     => 'ratio',
);

$media_slots = array(
	'recommend' => '1',
	'partners'  => '2',
	'nonstop'   => '3',
	'years'     => '4',
);

/**
 * @param array<string, mixed> $item Mosaic item.
 * @param string               $mod Media modifier (1–4).
 */
$messcut_stats_figure = static function ( array $item, string $mod ): void {
	$filename = (string) ( $item['image'] ?? '' );
	if ( '' === $filename ) {
		return;
	}
	$path = MESSCUT_DIR . '/assets/img/' . $filename;
	if ( ! file_exists( $path ) ) {
		return;
	}
	$src    = MESSCUT_URI . '/assets/img/' . $filename;
	$width  = isset( $item['width'] ) ? (int) $item['width'] : 0;
	$height = isset( $item['height'] ) ? (int) $item['height'] : 0;
	?>
	<figure class="stats__media stats__media--<?php echo esc_attr( $mod ); ?>" aria-hidden="true">
		<img
			class="stats__img"
			src="<?php echo esc_url( $src ); ?>"
			alt=""
			<?php if ( $width ) : ?>width="<?php echo esc_attr( (string) $width ); ?>"<?php endif; ?>
			<?php if ( $height ) : ?>height="<?php echo esc_attr( (string) $height ); ?>"<?php endif; ?>
			decoding="async"
			loading="lazy"
		>
	</figure>
	<?php
};
?>
<section class="section stats">
	<div class="stats__mosaic">
		<?php if ( $title ) : ?>
			<h2 class="stats__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>

		<?php foreach ( $copy_slots as $slot => $mod ) : ?>
			<?php
			$item  = $items[ $slot ] ?? array();
			$value = trim( (string) ( $item['value'] ?? '' ) );
			$label = trim( (string) ( $item['label'] ?? '' ) );
			if ( '' === $value && '' === $label ) {
				continue;
			}
			?>
			<article class="stats__block stats__block--<?php echo esc_attr( $mod ); ?>">
				<?php if ( $value ) : ?>
					<strong class="stats__value"><?php echo esc_html( $value ); ?></strong>
				<?php endif; ?>
				<?php if ( $label ) : ?>
					<span class="stats__label"><?php echo esc_html( $label ); ?></span>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>

		<?php foreach ( $media_slots as $slot => $mod ) : ?>
			<?php $messcut_stats_figure( $items[ $slot ] ?? array(), $mod ); ?>
		<?php endforeach; ?>
	</div>
</section>
<?php
get_template_part( 'template-parts/sections/agency-comparison' );
