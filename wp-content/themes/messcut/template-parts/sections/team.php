<?php
/**
 * Approach-page team tiles. "Більше" opens a dialog with the specialist profile.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$team = messcut_get_approach_team();
if ( empty( $team ) ) {
	return;
}
?>
<section class="section team" id="team">
	<div class="container">
		<h2 class="section__title"><?php esc_html_e( 'Команда', 'messcut' ); ?></h2>
		<ul class="team__grid">
			<?php foreach ( $team as $index => $member ) : ?>
				<?php
				$dialog_id = 'team-dialog-' . ( $index + 1 );
				$photo     = is_array( $member['photo'] ?? null ) ? $member['photo'] : array();
				$photo_url = (string) ( $photo['url'] ?? '' );
				$name      = (string) ( $member['name'] ?? '' );
				$summary   = (string) ( $member['summary'] ?? '' );
				$logos     = is_array( $member['logos'] ?? null ) ? $member['logos'] : array();
				?>
				<li class="team-card">
					<div class="team-card__photo">
						<?php if ( $photo_url ) : ?>
							<img
								src="<?php echo esc_url( $photo_url ); ?>"
								alt="<?php echo esc_attr( ! empty( $photo['alt'] ) ? (string) $photo['alt'] : $name ); ?>"
								<?php echo ! empty( $photo['width'] ) ? 'width="' . (int) $photo['width'] . '"' : ''; ?>
								<?php echo ! empty( $photo['height'] ) ? 'height="' . (int) $photo['height'] . '"' : ''; ?>
								loading="lazy"
								decoding="async"
							>
						<?php else : ?>
							<span class="team-card__initial" aria-hidden="true"><?php echo esc_html( messcut_name_initial( $name ) ); ?></span>
						<?php endif; ?>
					</div>
					<div class="team-card__body">
						<h3 class="team-card__name"><?php echo esc_html( $name ); ?></h3>
						<?php if ( $summary ) : ?>
							<p class="team-card__summary"><?php echo esc_html( $summary ); ?></p>
						<?php endif; ?>
						<?php if ( $logos ) : ?>
							<ul class="team-card__logos" aria-label="<?php esc_attr_e( 'Бренди, з якими працював спеціаліст', 'messcut' ); ?>">
								<?php foreach ( $logos as $logo ) : ?>
									<li class="team-card__logo">
										<img
											src="<?php echo esc_url( (string) $logo['url'] ); ?>"
											alt="<?php echo esc_attr( (string) ( $logo['alt'] ?? '' ) ); ?>"
											<?php echo ! empty( $logo['width'] ) ? 'width="' . (int) $logo['width'] . '"' : ''; ?>
											<?php echo ! empty( $logo['height'] ) ? 'height="' . (int) $logo['height'] . '"' : ''; ?>
											loading="lazy"
											decoding="async"
										>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
					<button
						class="button button--outline team-card__more"
						type="button"
						data-team-open
						aria-haspopup="dialog"
						aria-expanded="false"
						aria-controls="<?php echo esc_attr( $dialog_id ); ?>"
					>
						<?php esc_html_e( 'Більше', 'messcut' ); ?>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<?php foreach ( $team as $index => $member ) : ?>
		<?php
		$dialog_id  = 'team-dialog-' . ( $index + 1 );
		$name       = (string) ( $member['name'] ?? '' );
		$summary    = (string) ( $member['summary'] ?? '' );
		$facts    = array(
			__( 'Суперсила', 'messcut' )     => (string) ( $member['superpower'] ?? '' ),
			__( 'Років досвіду', 'messcut' ) => (string) ( $member['years'] ?? '' ),
			__( 'Навчання', 'messcut' )      => (string) ( $member['education'] ?? '' ),
		);
		$facts    = array_filter( $facts, static fn( $value ) => '' !== $value );
		$title_id = $dialog_id . '-title';
		?>
		<dialog class="team-dialog" id="<?php echo esc_attr( $dialog_id ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
			<button class="team-dialog__close" type="button" data-team-close>
				<span class="screen-reader-text"><?php esc_html_e( 'Закрити', 'messcut' ); ?></span>
				<span aria-hidden="true">×</span>
			</button>
			<h2 class="team-dialog__name" id="<?php echo esc_attr( $title_id ); ?>"><?php echo esc_html( $name ); ?></h2>
			<?php if ( $summary ) : ?>
				<p class="team-dialog__summary"><?php echo esc_html( $summary ); ?></p>
			<?php endif; ?>
			<?php if ( $facts ) : ?>
				<dl class="team-dialog__facts">
					<?php foreach ( $facts as $label => $value ) : ?>
						<div class="team-dialog__fact">
							<dt class="team-dialog__label"><?php echo esc_html( $label ); ?></dt>
							<dd class="team-dialog__value"><?php echo esc_html( $value ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>
		</dialog>
	<?php endforeach; ?>
</section>
