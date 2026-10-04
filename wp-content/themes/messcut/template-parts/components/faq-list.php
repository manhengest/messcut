<?php
/**
 * FAQ accordion.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : messcut_get_faq_items();
?>
<div class="accordion" data-accordion>
	<?php foreach ( $items as $index => $item ) : ?>
		<details<?php echo 0 === $index ? ' open' : ''; ?>>
			<summary><?php echo esc_html( $item['q'] ); ?><i>+</i></summary>
			<p><?php echo esc_html( $item['a'] ); ?></p>
		</details>
	<?php endforeach; ?>
</div>
