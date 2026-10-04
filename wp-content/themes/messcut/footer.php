</main>

<?php get_template_part( 'template-parts/footer/site-footer' ); ?>

<?php
if ( messcut_is_translated_page( 'poslugy' ) || is_singular( 'case_study' ) ) {
	get_template_part( 'template-parts/components/sticky-cta' );
	get_template_part( 'template-parts/components/bottom-sheet' );
}
?>

<?php wp_footer(); ?>
</body>
</html>
