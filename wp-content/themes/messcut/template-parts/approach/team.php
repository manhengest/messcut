<?php
/**
 * Team tiles. No modal.
 *
 * @package Messcut
 */

$team = messcut_get_approach_team();
?>
<section class="section approach-team" id="team">
	<h2><?php esc_html_e( 'Команда', 'messcut' ); ?></h2>
	<div class="card-rail">
		<?php foreach ( $team as $member ) : ?>
			<article class="team-tile">
				<div class="team-tile__photo">
					<?php if ( ! empty( $member['photo']['url'] ) ) : ?>
						<img src="<?php echo esc_url( $member['photo']['url'] ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>">
					<?php else : ?>
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: team member name */
								__( 'фото · %s', 'messcut' ),
								$member['name']
							)
						);
						?>
					<?php endif; ?>
				</div>
				<h3><?php echo esc_html( $member['name'] ); ?></h3>
				<p class="team-tile__role"><?php echo esc_html( $member['role'] ); ?></p>
				<div class="team-tile__meta">
					<div><small><?php esc_html_e( 'Досвід', 'messcut' ); ?></small><b><?php echo esc_html( $member['years'] ); ?></b></div>
					<div class="team-tile__power"><small><?php esc_html_e( 'Супер-сила', 'messcut' ); ?></small><b><?php echo esc_html( $member['superpower'] ); ?></b></div>
				</div>
				<?php if ( ! empty( $member['logos'] ) ) : ?>
					<?php
					$logo_count = count( $member['logos'] );
					$logo_run   = $logo_count > 4;
					$logo_mono  = false;
					foreach ( $member['logos'] as $logo ) {
						if ( ! empty( $logo['mono'] ) ) {
							$logo_mono = true;
							break;
						}
					}
					$logo_class = 'team-logos';
					if ( $logo_mono ) {
						$logo_class .= ' team-logos--mono';
					}
					if ( $logo_run ) {
						$logo_class .= ' team-logos--run';
					}
					?>
					<div class="<?php echo esc_attr( $logo_class ); ?>"<?php echo $logo_run ? '' : ' style="' . esc_attr( '--team-logos: ' . $logo_count ) . '"'; ?>>
						<?php if ( $logo_run ) : ?>
							<div class="team-logos__track">
						<?php endif; ?>
						<?php for ( $copy = 0; $copy < ( $logo_run ? 2 : 1 ); $copy++ ) : ?>
							<?php if ( $logo_run ) : ?>
								<div class="team-logos__group"<?php echo $copy ? ' aria-hidden="true"' : ''; ?>>
							<?php endif; ?>
							<?php foreach ( $member['logos'] as $logo ) : ?>
								<span>
									<?php if ( ! empty( $logo['url'] ) ) : ?>
										<img src="<?php echo esc_url( $logo['url'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>">
									<?php elseif ( ! empty( $logo['svg'] ) ) : ?>
										<svg viewBox="0 0 24 24" role="img" aria-label="<?php echo esc_attr( $logo['alt'] ); ?>"><path fill="#111" d="<?php echo esc_attr( $logo['svg'] ); ?>"/></svg>
									<?php else : ?>
										<?php echo esc_html( (string) ( $logo['label'] ?? '' ) ); ?>
									<?php endif; ?>
								</span>
							<?php endforeach; ?>
							<?php if ( $logo_run ) : ?>
								</div>
							<?php endif; ?>
						<?php endfor; ?>
						<?php if ( $logo_run ) : ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</section>
