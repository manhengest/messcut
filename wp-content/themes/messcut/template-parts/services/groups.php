<?php
/**
 * Service direction groups.
 *
 * @package Messcut
 */

$groups = messcut_get_service_groups();
?>
<?php foreach ( $groups as $index => $group ) : ?>
	<section class="service-group<?php echo 0 === $index ? ' is-open' : ''; ?>" data-service-group id="<?php echo esc_attr( $group['id'] ); ?>">
		<button class="service-group__toggle" type="button" aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>">
			<span>
				<span class="eyebrow"><?php echo esc_html( $group['eyebrow'] ); ?></span>
				<h2><?php echo esc_html( $group['title'] ); ?></h2>
				<p><?php echo esc_html( $group['text'] ); ?></p>
			</span>
			<span class="service-group__chevron" aria-hidden="true">↓</span>
		</button>
		<div class="service-group__body">
			<div class="service-group__grid">
				<?php foreach ( $group['services'] as $service ) : ?>
					<?php get_template_part( 'template-parts/services/card', null, array( 'service' => $service ) ); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endforeach; ?>
