<?php
/**
 * Article.
 *
 * @package Messcut
 */

get_header();
?>
<nav class="chapter-rail" data-rail=".article-hero, .article-body > h2" aria-label="<?php esc_attr_e( 'Розділи статті', 'messcut' ); ?>"></nav>
<?php
while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/article/single' );
}
get_footer();
