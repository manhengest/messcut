<?php
/**
 * Values line section.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$values = $args['values'] ?? array();
if ( empty( $values ) ) {
	return;
}
?>
<section class="section values">
	<div class="container">
		<p class="values__line">
			<?php
			$texts = array_map(
				static fn( $row ) => $row['text'] ?? '',
				$values
			);
			echo esc_html( implode( '. ', array_filter( $texts ) ) );
			?>
		</p>
	</div>
</section>
