<?php
/**
 * Agency vs in-house comparison table.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$embedded = ! empty( $args['embedded'] );
$title    = messcut_get_localized_option(
	'agency_comparison_title',
	__( 'Порівняйте нас з іншими агенціями або власним наймом маркетолога', 'messcut' )
);
$rows = messcut_get_agency_comparison_rows();

if ( empty( $rows ) ) {
	return;
}

$block_class = $embedded
	? 'agency-comparison stats__comparison'
	: 'section agency-comparison';
?>
<<?php echo $embedded ? 'div' : 'section'; ?> class="<?php echo esc_attr( $block_class ); ?>">
	<?php if ( ! $embedded ) : ?>
	<div class="container">
	<?php endif; ?>
		<?php if ( $title ) : ?>
			<h2 class="agency-comparison__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
		<div class="agency-comparison__frame">
			<div
				class="agency-comparison__scroll"
				tabindex="0"
				role="region"
				aria-label="<?php echo esc_attr__( 'Порівняльна таблиця. Перша колонка зафіксована, прокрутіть, щоб побачити решту.', 'messcut' ); ?>"
			>
				<table class="agency-comparison__table">
					<colgroup>
						<col class="agency-comparison__col agency-comparison__col--pin">
						<col class="agency-comparison__col agency-comparison__col--messcut">
						<col class="agency-comparison__col">
						<col class="agency-comparison__col">
					</colgroup>
					<thead>
						<tr>
							<th scope="col" class="agency-comparison__cell agency-comparison__cell--pinned" aria-label="<?php echo esc_attr__( 'Критерій', 'messcut' ); ?>"></th>
							<th scope="col" class="agency-comparison__cell agency-comparison__cell--messcut"><?php esc_html_e( 'Messcut', 'messcut' ); ?></th>
							<th scope="col" class="agency-comparison__cell"><?php esc_html_e( 'Агенція', 'messcut' ); ?></th>
							<th scope="col" class="agency-comparison__cell"><?php esc_html_e( 'In-house', 'messcut' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $rows as $row ) : ?>
							<tr>
								<th scope="row" class="agency-comparison__cell agency-comparison__cell--criterion agency-comparison__cell--pinned">
									<?php echo esc_html( $row['criterion'] ); ?>
								</th>
								<td class="agency-comparison__cell agency-comparison__cell--messcut"><?php echo esc_html( $row['messcut'] ); ?></td>
								<td class="agency-comparison__cell"><?php echo esc_html( $row['agency'] ); ?></td>
								<td class="agency-comparison__cell"><?php echo esc_html( $row['inhouse'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php if ( ! $embedded ) : ?>
	</div>
	<?php endif; ?>
</<?php echo $embedded ? 'div' : 'section'; ?>>
