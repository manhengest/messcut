<?php
/**
 * Link to approach page.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args  = isset( $args ) && is_array( $args ) ? $args : array();
$title = isset( $args['title'] ) ? trim( (string) $args['title'] ) : '';
?>
<section class="section approach-cta">
	<div class="container container--narrow">
		<?php if ( '' !== $title ) : ?>
			<h2 class="approach-cta__title">
				<a href="<?php echo esc_url( messcut_approach_url() ); ?>"><?php echo esc_html( $title ); ?></a>
			</h2>
		<?php else : ?>
			<p>
				<a class="button button--secondary" href="<?php echo esc_url( messcut_approach_url() ); ?>">
					<?php esc_html_e( 'Дізнатись більше про наш підхід', 'messcut' ); ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</section>
