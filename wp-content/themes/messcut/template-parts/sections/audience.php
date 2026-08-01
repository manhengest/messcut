<?php
/**
 * Audience section — "Для кого наші послуги?"
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="section audience">
	<div class="container container--narrow">
		<h2 class="section__title"><?php esc_html_e( 'Для кого наші послуги?', 'messcut' ); ?></h2>
		<p class="audience__text">
			<?php
			echo esc_html(
				messcut_get_localized_option(
					'audience_text',
					__(
						'Для підприємців, які обирають шлях ефективного розвитку бренду зі зниженням ризиків інвестувати гроші і час в непрацюючі механіки, маючи чітку стратегію розвитку, що базується на наукових принципах.',
						'messcut'
					)
				)
			);
			?>
		</p>
	</div>
</section>
