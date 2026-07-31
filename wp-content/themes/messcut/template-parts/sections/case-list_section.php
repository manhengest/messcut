<?php
/**
 * Flexible content: list section.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = get_sub_field( 'title' );
$items = get_sub_field( 'items' );
if ( empty( $items ) ) {
	return;
}
?>
<section class="case-section case-section--list">
	<div class="container container--narrow">
		<?php if ( $title ) : ?>
			<h2><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<ul>
			<?php foreach ( $items as $row ) : ?>
				<?php if ( ! empty( $row['item'] ) ) : ?>
					<li><?php echo esc_html( $row['item'] ); ?></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
