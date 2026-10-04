<?php
/**
 * Team tiles. No modal.
 *
 * @package Messcut
 */

$team = messcut_get_approach_team();
?>
<section class="section" id="team">
	<h2><?php esc_html_e( 'Команда', 'messcut' ); ?></h2>
	<div class="card-rail">
		<?php foreach ( $team as $member ) : ?>
			<article class="team-tile">
				<div class="team-tile__photo">
					<?php if ( ! empty( $member['photo']['url'] ) ) : ?>
						<img src="<?php echo esc_url( $member['photo']['url'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>">
					<?php else : ?>
						<?php echo esc_html( $member['name'] ); ?>
					<?php endif; ?>
				</div>
				<h3><?php echo esc_html( $member['name'] ); ?></h3>
				<p class="team-tile__role"><?php echo esc_html( $member['role'] ); ?></p>
				<div class="team-tile__meta">
					<div><small><?php esc_html_e( 'Досвід', 'messcut' ); ?></small><b><?php echo esc_html( $member['years'] ); ?></b></div>
					<div><small><?php esc_html_e( 'Супер-сила', 'messcut' ); ?></small><b><?php echo esc_html( $member['superpower'] ); ?></b></div>
				</div>
				<?php if ( ! empty( $member['logos'] ) ) : ?>
					<div class="team-logos">
						<?php foreach ( $member['logos'] as $logo ) : ?>
							<span><?php if ( ! empty( $logo['url'] ) ) : ?><img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>"><?php endif; ?></span>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</section>
