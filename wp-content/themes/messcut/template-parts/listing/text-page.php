<?php
/**
 * Shared text page: articles, pages, 404.
 *
 * @package Messcut
 */

$title = isset( $args['title'] ) ? (string) $args['title'] : get_the_title();
?>
<section class="listing-hero">
	<h1><?php echo esc_html( $title ); ?></h1>
</section>
<section class="section prose">
	<?php
	if ( isset( $args['content'] ) ) {
		echo wp_kses_post( (string) $args['content'] );
	} else {
		the_content();
	}
	?>
</section>
