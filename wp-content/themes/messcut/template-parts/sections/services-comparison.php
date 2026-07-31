<?php
/**
 * Services comparison table from options.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rows = messcut_get_option( 'services_comparison', array() );
if ( empty( $rows ) || ! is_array( $rows ) ) {
	return;
}
?>
<section class="section services-comparison">
	<div class="container">
		<h2 class="section__title"><?php esc_html_e( 'Не впевнені, яка послуга вам підійде?', 'messcut' ); ?></h2>
		<div class="comparison-grid">
			<?php foreach ( $rows as $row ) : ?>
				<?php
				$service = $row['service'] ?? null;
				if ( is_numeric( $service ) ) {
					$service = get_post( (int) $service );
				}
				if ( ! $service instanceof WP_Post ) {
					continue;
				}
				$bullets = $row['bullets'] ?? array();
				?>
				<div class="comparison-column">
					<h3 class="comparison-column__title"><?php echo esc_html( $service->post_title ); ?></h3>
					<?php if ( ! empty( $bullets ) ) : ?>
						<ul class="comparison-column__list">
							<?php foreach ( $bullets as $bullet ) : ?>
								<?php if ( ! empty( $bullet['text'] ) ) : ?>
									<li><?php echo esc_html( $bullet['text'] ); ?></li>
								<?php endif; ?>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="services-comparison__help">
			<a class="button button--primary" href="#lead-form"><?php esc_html_e( 'Або звʼяжіться з нами і ми допоможемо', 'messcut' ); ?> →</a>
		</p>
	</div>
</section>
