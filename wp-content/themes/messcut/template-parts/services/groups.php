<?php
/**
 * Service direction groups.
 *
 * @package Messcut
 */

$groups = messcut_get_service_groups();
?>
<?php foreach ( $groups as $index => $group ) : ?>
	<?php
	$is_branding = 'direction-branding' === $group['id'];
	$tone        = $is_branding ? 'service-group--branding' : 'service-group--marketing';
	$services    = $group['services'];
	?>
	<section class="service-group <?php echo esc_attr( $tone ); ?><?php echo 0 === $index ? ' is-open' : ''; ?>" data-service-group id="<?php echo esc_attr( $group['id'] ); ?>">
		<button class="service-group__toggle" type="button" aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>">
			<span class="service-group__intro">
				<span class="eyebrow"><?php echo esc_html( $group['eyebrow'] ); ?></span>
				<h2><?php echo esc_html( $group['title'] ); ?></h2>
				<p><?php echo esc_html( $group['text'] ); ?></p>
			</span>
			<span class="service-group__chevron" aria-hidden="true">
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="#000" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6l5 5 5-5"/></svg>
			</span>
		</button>
		<div class="service-group__body">
			<div class="service-group__grid">
				<?php foreach ( $services as $service_index => $service ) : ?>
					<?php
					if ( $is_branding && 1 === $service_index ) {
						echo '<div class="service-link-note"><i>↓</i>' . esc_html__( 'Другий етап можливий лише після першого', 'messcut' ) . '</div>';
					}
					get_template_part( 'template-parts/services/card', null, array( 'service' => $service ) );
					?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endforeach; ?>
