<?php
/**
 * Case card.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$tone    = messcut_get_acf( 'tone', $post_id );
$tone    = is_string( $tone ) && '' !== $tone ? $tone : '#c7f2e1,#8fdcbc';
$colors  = array_map( 'trim', explode( ',', $tone ) );
$bg      = 'linear-gradient(145deg,' . ( $colors[0] ?? '#c7f2e1' ) . ',' . ( $colors[1] ?? '#8fdcbc' ) . ')';
$thumb   = get_the_post_thumbnail_url( $post_id, 'large' );
?>
<a class="case-card" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
	<div class="case-card__media" style="background:<?php echo esc_attr( $bg ); ?>">
		<?php if ( $thumb ) : ?>
			<img src="<?php echo esc_url( $thumb ); ?>" alt="">
		<?php endif; ?>
	</div>
	<h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
	<p><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
	<span class="case-card__more"><?php esc_html_e( 'Детальніше', 'messcut' ); ?> <i>↗</i></span>
</a>
