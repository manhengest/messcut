<?php
/**
 * Result detail sheet.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="bottom-sheet" data-sheet hidden aria-hidden="true">
	<div class="bottom-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="sheet-title">
		<button class="bottom-sheet__close" type="button" data-sheet-close aria-label="<?php esc_attr_e( 'Закрити', 'messcut' ); ?>">✕</button>
		<span class="eyebrow"><?php esc_html_e( 'Ключовий результат', 'messcut' ); ?></span>
		<h3 id="sheet-title"></h3>
		<p data-sheet-body></p>
	</div>
</div>
