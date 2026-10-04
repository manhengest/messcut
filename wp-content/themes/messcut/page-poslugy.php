<?php
/**
 * Services hub.
 *
 * Template Name: Послуги
 *
 * @package Messcut
 */

get_header();
?>
<div class="services-orb" data-orb aria-hidden="true"></div>
<nav class="chapter-rail" data-rail aria-label="<?php esc_attr_e( 'Розділи сторінки', 'messcut' ); ?>"></nav>
<?php
get_template_part( 'template-parts/services/hero' );
get_template_part( 'template-parts/services/groups' );
get_template_part( 'template-parts/services/cta' );
get_footer();
