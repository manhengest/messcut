<?php
/**
 * 404 template.
 *
 * @package Messcut
 */

get_header();
?>
<section class="section not-found">
	<div class="container container--narrow">
		<h1><?php esc_html_e( 'Сторінку не знайдено', 'messcut' ); ?></h1>
		<p><?php esc_html_e( 'Можливо, посилання застаріло або сторінку переміщено.', 'messcut' ); ?></p>
		<p><a class="button button--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'На головну', 'messcut' ); ?></a></p>
	</div>
</section>
<?php
get_footer();
