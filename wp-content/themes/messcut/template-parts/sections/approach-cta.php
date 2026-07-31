<?php
/**
 * Link to approach page.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="section approach-cta">
	<div class="container container--narrow">
		<p>
			<a class="button button--secondary" href="<?php echo esc_url( messcut_approach_url() ); ?>">
				<?php esc_html_e( 'Дізнатись більше про наш підхід', 'messcut' ); ?> →
			</a>
		</p>
	</div>
</section>
