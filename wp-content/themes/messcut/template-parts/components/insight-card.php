<?php
/**
 * Insight card.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$feature = ! empty( $args['feature'] );
$index   = isset( $args['index'] ) ? (int) $args['index'] : 1;
?>
<a class="insight-card<?php echo $feature ? ' insight-card--feature' : ''; ?>" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
	<div class="insight-card__media"><span><?php echo esc_html( sprintf( '%02d', $index ) ); ?></span></div>
	<div class="insight-card__body">
		<h3><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
		<p><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
		<span class="insight-card__more"><?php esc_html_e( 'Читати', 'messcut' ); ?> <i>↗</i></span>
	</div>
</a>
