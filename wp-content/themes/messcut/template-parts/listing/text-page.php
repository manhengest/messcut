<?php
/**
 * Shared text page: articles, pages, 404.
 *
 * @package Messcut
 */

$title = isset( $args['title'] ) ? (string) $args['title'] : get_the_title();
?>
<section class="listing-hero">
	<div class="container">
	<h1><?php echo esc_html( $title ); ?></h1>
	</div>
</section>
<section class="section prose">
	<div class="container">
	<?php
	if ( isset( $args['content'] ) ) {
		echo wp_kses_post( (string) $args['content'] );
	} else {
		the_content();
	}
	?>
	</div>
</section>
