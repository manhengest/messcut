<?php
/**
 * Approach method: lead copy and three pillars.
 *
 * @package Messcut
 *
 * @var array<string, mixed> $args Template args.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$content = isset( $args['content'] ) ? (string) $args['content'] : '';
$pillars = array(
	array(
		'title' => __( 'Цінність для людей', 'messcut' ),
		'text'  => __( 'Створюємо те, що має значення для аудиторії.', 'messcut' ),
	),
	array(
		'title' => __( 'Системний підхід', 'messcut' ),
		'text'  => __( 'Повʼязуємо бренд, маркетинг і бізнес-цілі в одну систему.', 'messcut' ),
	),
	array(
		'title' => __( 'Креатив для результату', 'messcut' ),
		'text'  => __( 'Створюємо креатив, який вирішує конкретні цілі бізнесу.', 'messcut' ),
	),
);
?>
<section class="section approach">
	<div class="container">
		<h2 class="section__title"><?php esc_html_e( 'Наш підхід', 'messcut' ); ?></h2>
		<?php if ( $content ) : ?>
			<div class="entry-content approach__lead"><?php echo wp_kses_post( $content ); ?></div>
		<?php else : ?>
			<p class="approach__lead"><?php esc_html_e( 'Досліджуємо, як люди приймають рішення, що впливає на їхню поведінку та як це працює в конкретному бізнесі. На цій основі будуємо маркетинг, який має логіку, систему й зрозумілу ціль.', 'messcut' ); ?></p>
		<?php endif; ?>
		<ul class="approach-pillars">
			<?php foreach ( $pillars as $pillar ) : ?>
				<li class="approach-pillars__card">
					<h3 class="approach-pillars__title"><?php echo esc_html( $pillar['title'] ); ?></h3>
					<p class="approach-pillars__text"><?php echo esc_html( $pillar['text'] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
