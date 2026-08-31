<?php
/**
 * Agency vs in-house comparison tabs (Figma 131:74).
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = messcut_get_agency_comparison_title();
$tabs  = messcut_get_agency_comparison_tabs();

if ( empty( $tabs ) ) {
	return;
}

$uid     = function_exists( 'wp_unique_id' ) ? wp_unique_id( 'compare-' ) : 'compare-';
$tablist = $uid . 'tabs';
?>
<section class="section agency-comparison" data-compare-tabs>
	<div class="agency-comparison__inner">
		<?php if ( $title ) : ?>
			<h2 class="agency-comparison__title" id="<?php echo esc_attr( $uid . 'title' ); ?>">
				<?php echo esc_html( $title ); ?>
			</h2>
		<?php endif; ?>

		<div
			class="agency-comparison__tabs"
			role="tablist"
			id="<?php echo esc_attr( $tablist ); ?>"
			aria-labelledby="<?php echo esc_attr( $uid . 'title' ); ?>"
		>
			<?php foreach ( $tabs as $index => $tab ) : ?>
				<?php
				$tab_id    = $uid . 'tab-' . $tab['id'];
				$panel_id  = $uid . 'panel-' . $tab['id'];
				$is_active = 0 === $index;
				?>
				<button
					class="agency-comparison__tab"
					type="button"
					role="tab"
					id="<?php echo esc_attr( $tab_id ); ?>"
					aria-controls="<?php echo esc_attr( $panel_id ); ?>"
					aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
					tabindex="<?php echo $is_active ? '0' : '-1'; ?>"
					data-compare-tab="<?php echo esc_attr( $tab['id'] ); ?>"
				>
					<span class="agency-comparison__tab-label"><?php echo esc_html( $tab['label'] ); ?></span>
					<span class="agency-comparison__plus" aria-hidden="true">+</span>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ( $tabs as $index => $tab ) : ?>
			<?php
			$tab_id    = $uid . 'tab-' . $tab['id'];
			$panel_id  = $uid . 'panel-' . $tab['id'];
			$is_active = 0 === $index;
			$points    = isset( $tab['points'] ) && is_array( $tab['points'] ) ? $tab['points'] : array();
			?>
			<div
				class="agency-comparison__panel"
				role="tabpanel"
				id="<?php echo esc_attr( $panel_id ); ?>"
				aria-labelledby="<?php echo esc_attr( $tab_id ); ?>"
				<?php echo $is_active ? '' : 'hidden'; ?>
			>
				<?php if ( ! empty( $tab['logo'] ) ) : ?>
					<div class="agency-comparison__brand" aria-hidden="true">
						<?php
						messcut_render_logo(
							'black',
							array(
								'class'  => 'agency-comparison__logo',
								'width'  => 160,
								'height' => 35,
								'linked' => false,
							)
						);
						?>
					</div>
				<?php elseif ( ! empty( $tab['heading'] ) ) : ?>
					<p class="agency-comparison__heading"><?php echo esc_html( $tab['heading'] ); ?></p>
				<?php endif; ?>

				<?php if ( ! empty( $points ) ) : ?>
					<ol class="agency-comparison__points">
						<?php foreach ( $points as $i => $point ) : ?>
							<?php
							$point = trim( (string) $point );
							if ( '' === $point ) {
								continue;
							}
							?>
							<li class="agency-comparison__point">
								<span class="agency-comparison__num"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span>
								<span class="agency-comparison__text"><?php echo nl2br( esc_html( $point ), false ); ?></span>
							</li>
						<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
