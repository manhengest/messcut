<?php
/**
 * Section heading with an optional trailing link.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = isset( $args['title'] ) ? (string) $args['title'] : '';
$href  = isset( $args['href'] ) ? (string) $args['href'] : '';
$label = isset( $args['label'] ) ? (string) $args['label'] : __( 'Більше →', 'messcut' );
?>
<div class="section-head">
	<h2><?php echo esc_html( $title ); ?></h2>
	<?php if ( $href ) : ?>
		<a class="button button--outline" href="<?php echo esc_url( $href ); ?>"><?php echo esc_html( $label ); ?></a>
	<?php endif; ?>
</div>
