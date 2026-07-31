<?php
/**
 * Flexible content: text section.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title   = get_sub_field( 'title' );
$content = get_sub_field( 'content' );
if ( ! $title && ! $content ) {
	return;
}
?>
<section class="case-section case-section--text">
	<div class="container container--narrow">
		<?php if ( $title ) : ?>
			<h2><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<?php if ( $content ) : ?>
			<div class="entry-content"><?php echo wp_kses_post( $content ); ?></div>
		<?php endif; ?>
	</div>
</section>
