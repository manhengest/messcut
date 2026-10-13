<?php
/**
 * Site footer. Menu and contacts, as in the mobile mock.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$privacy = messcut_page_url( 'polityka-konfidentsiynosti' );
$menu    = array(
	array(
		'url'   => messcut_page_url( 'poslugy' ),
		'label' => __( 'Послуги', 'messcut' ),
	),
	array(
		'url'   => messcut_cases_archive_url(),
		'label' => __( 'Кейси', 'messcut' ),
	),
	array(
		'url'   => messcut_approach_url(),
		'label' => __( 'Досвід та підхід', 'messcut' ),
	),
	array(
		'url'   => messcut_insights_archive_url(),
		'label' => __( 'Інсайти', 'messcut' ),
	),
);
$contacts = array(
	array(
		'url'   => messcut_telegram_url(),
		'label' => 'Telegram',
	),
	array(
		'url'   => messcut_whatsapp_url(),
		'label' => 'WhatsApp',
	),
	array(
		'url'   => 'mailto:' . messcut_email(),
		'label' => 'Email',
	),
	array(
		'url'   => messcut_instagram_url(),
		'label' => 'Instagram',
	),
);
?>
<footer class="site-footer" id="<?php echo ( is_page_template( 'page-approach.php' ) || messcut_is_translated_page( 'dosvid' ) ) ? 'lead' : 'footer'; ?>">
	<div class="container site-footer__inner">
	<?php messcut_render_logo( 'white', array( 'class' => 'site-footer__logo', 'height' => 40, 'width' => 186 ) ); ?>
	<p class="site-footer__tagline"><?php echo esc_html( __( (string) messcut_get_option( 'footer_tagline', 'Стратегічний маркетинг для брендів, які хочуть зростати системно.' ), 'messcut' ) ); ?></p>
	<div class="site-footer__cols">
		<nav class="site-footer__col" aria-label="<?php esc_attr_e( 'Меню', 'messcut' ); ?>">
			<p class="site-footer__label"><?php esc_html_e( 'Меню', 'messcut' ); ?></p>
			<?php foreach ( $menu as $item ) : ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>
		<div class="site-footer__col">
			<p class="site-footer__label"><?php esc_html_e( 'Контакти', 'messcut' ); ?></p>
			<?php foreach ( $contacts as $item ) : ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['label'] ); ?></a>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="site-footer__bar">
		<a href="<?php echo esc_url( $privacy ); ?>"><?php esc_html_e( 'Політика конфіденційності', 'messcut' ); ?></a>
		<a href="<?php echo esc_url( 'tel:' . preg_replace( '/\s+/', '', messcut_phone() ) ); ?>"><?php echo esc_html( messcut_phone() ); ?></a>
	</div>
	</div>
</footer>
