<?php
/**
 * Mint close CTA band (archives + single article).
 *
 * @package Messcut
 *
 * @param array $args {
 *     @type string $eyebrow Eyebrow label (plain text).
 *     @type string $title   Headline HTML; allows <em>.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = isset( $args['eyebrow'] ) ? (string) $args['eyebrow'] : '';
$title   = isset( $args['title'] ) ? (string) $args['title'] : '';
?>
<section class="close-band">
	<div class="container">
		<div class="close-band__inner">
			<?php if ( '' !== $eyebrow ) : ?>
				<span class="close-band__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $title ) : ?>
				<h2><?php echo wp_kses( $title, array( 'em' => array() ) ); ?></h2>
			<?php endif; ?>
			<a class="cta" href="<?php echo esc_url( messcut_contact_url() ); ?>"><?php esc_html_e( 'Обговорити проєкт', 'messcut' ); ?> <b>→</b></a>
		</div>
	</div>
</section>
