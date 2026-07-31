<?php
/**
 * Content seed on theme activation.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Run seed when content version is outdated.
 */
function messcut_maybe_seed_content(): void {
	$current = (int) get_option( 'messcut_content_version', 0 );
	if ( $current >= MESSCUT_CONTENT_VERSION && get_option( 'messcut_seeded' ) ) {
		return;
	}
	messcut_run_seed();
	update_option( 'messcut_seeded', 1, false );
	update_option( 'messcut_content_version', MESSCUT_CONTENT_VERSION, false );
}
add_action( 'after_switch_theme', 'messcut_maybe_seed_content', 20 );
add_action( 'init', 'messcut_maybe_seed_content', 20 );

/**
 * Seed all MESSCUT content.
 */
function messcut_run_seed(): void {
	messcut_seed_options();
	$service_ids = messcut_seed_services();
	$case_ids    = messcut_seed_cases( $service_ids );
	messcut_seed_comparison( $service_ids );
	messcut_seed_pages( $case_ids );
	messcut_sync_menus();
	flush_rewrite_rules();

	if ( function_exists( 'messcut_assign_uk_language' ) && messcut_is_polylang_active() ) {
		messcut_assign_uk_language( array( 'page', 'case_study', 'service', 'article' ) );
	}
}

/**
 * Update ACF option fields.
 *
 * @param array<string, mixed> $fields Fields.
 */
function messcut_seed_update_options( array $fields ): void {
	if ( function_exists( 'update_field' ) ) {
		foreach ( $fields as $key => $value ) {
			update_field( $key, $value, 'option' );
		}
	}
}

/**
 * Seed global options.
 */
function messcut_seed_options(): void {
	messcut_seed_update_options( array(
		'phone'                => '+38 (095) 477-11-22',
		'telegram'             => '@messcutstrategy',
		'whatsapp'             => '',
		'email'                => 'admin@messcut.com',
		'instagram_1'          => 'https://www.instagram.com/valeria.messcut/',
		'instagram_2'          => 'https://www.instagram.com/messcut.strategy/',
		'footer_tagline'       => 'Стратегічний маркетинг для брендів, які хочуть зростати системно.',
		'footer_about'         => 'Поєднуємо маркетинг, доведений наукою, стратегічне мислення та глибоке розуміння споживача, щоб створювати бренди, які залишаються в пам\'яті та приносять бізнес-результат.',
		'form_recipient_email' => 'admin@messcut.com',
		'cta_discuss_label'    => 'Обговорити проєкт',
		'cta_consult_label'    => 'Отримати ознайомчу консультацію',
		'home_hero_title'      => 'Розвиваємо бренди з науковим підходом',
		'home_hero_subtitle'   => 'ефективність в цифрах з чіткою стратегією',
		'home_tagline'         => 'Розвиваємо бренди, збільшуючи їх ефективність в цифрах',
		'stats'                => array(
			array( 'value' => '', 'label' => 'роки практик та нескінченних навчань для підвищення кваліфікації' ),
			array( 'value' => '30+', 'label' => 'бренд-стратегій' ),
			array( 'value' => '50+', 'label' => 'співпраць' ),
			array( 'value' => '85%', 'label' => 'клієнтів приходять за рекомендацією' ),
		),
		'home_values'          => array(
			array( 'text' => 'етичність' ),
			array( 'text' => 'мотивація' ),
			array( 'text' => 'структура' ),
			array( 'text' => 'любов до справи' ),
		),
	) );
}

/**
 * Upsert a post by slug.
 *
 * @param string               $post_type Post type.
 * @param string               $slug      Slug.
 * @param array<string, mixed> $data      Post data.
 * @return int Post ID or 0.
 */
function messcut_upsert_post( string $post_type, string $slug, array $data ): int {
	$existing = get_page_by_path( $slug, OBJECT, $post_type );
	$args     = array(
		'post_type'    => $post_type,
		'post_status'  => 'publish',
		'post_title'   => $data['title'] ?? $slug,
		'post_name'    => $slug,
		'post_excerpt' => $data['excerpt'] ?? '',
		'menu_order'   => $data['order'] ?? 0,
	);

	if ( $existing ) {
		$args['ID'] = $existing->ID;
		$post_id    = wp_update_post( $args, true );
	} else {
		$post_id = wp_insert_post( $args, true );
	}

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}

	if ( ! empty( $data['fields'] ) ) {
		messcut_seed_update_post_fields( (int) $post_id, $data['fields'] );
	}

	return (int) $post_id;
}

/**
 * @return array<string, int> Service slug => post ID.
 */
function messcut_seed_services(): array {
	$services = messcut_get_service_seed_data();
	$ids      = array();

	foreach ( $services as $slug => $data ) {
		$ids[ $slug ] = messcut_upsert_post( 'service', $slug, $data );
	}

	return $ids;
}

/**
 * @param array<string, int> $service_ids Service IDs.
 * @return array<string, int> Case slug => post ID.
 */
function messcut_seed_cases( array $service_ids ): array {
	$cases = messcut_get_case_seed_data();
	$ids   = array();

	foreach ( $cases as $slug => $data ) {
		$post_id = messcut_upsert_post( 'case_study', $slug, $data );
		if ( ! $post_id ) {
			continue;
		}

		if ( ! empty( $data['services'] ) ) {
			$related = array();
			foreach ( $data['services'] as $service_slug ) {
				if ( isset( $service_ids[ $service_slug ] ) ) {
					$related[] = $service_ids[ $service_slug ];
				}
			}
			messcut_seed_update_post_fields( $post_id, array( 'services_used' => $related ) );
		}

		$ids[ $slug ] = $post_id;
	}

	return $ids;
}

/**
 * Seed services comparison table in options.
 *
 * @param array<string, int> $service_ids Service IDs.
 */
function messcut_seed_comparison( array $service_ids ): void {
	if ( ! isset( $service_ids['consulting'], $service_ids['mentorship'] ) ) {
		return;
	}

	messcut_seed_update_options( array(
		'services_comparison' => array(
			array(
				'service' => $service_ids['consulting'],
				'bullets' => array(
					array( 'text' => 'Разова сесія' ),
					array( 'text' => 'Конкретне питання або задача' ),
					array( 'text' => 'Швидкий зовнішній погляд' ),
					array( 'text' => 'План наступних кроків' ),
				),
			),
			array(
				'service' => $service_ids['mentorship'],
				'bullets' => array(
					array( 'text' => '3 місяці супроводу' ),
					array( 'text' => 'Побудова бренду та маркетингової системи' ),
					array( 'text' => 'Регулярний зворотний звʼязок' ),
					array( 'text' => 'Перевірка всіх напрацювань' ),
					array( 'text' => 'Допомога на кожному етапі реалізації' ),
				),
			),
		),
	) );
}

/**
 * @param int                  $post_id Post ID.
 * @param array<string, mixed> $fields  Fields.
 */
function messcut_seed_update_post_fields( int $post_id, array $fields ): void {
	if ( ! function_exists( 'update_field' ) ) {
		return;
	}
	foreach ( $fields as $key => $value ) {
		update_field( $key, $value, $post_id );
	}
}

/**
 * @param array<string, int> $case_ids Case IDs.
 */
function messcut_seed_pages( array $case_ids ): void {
	$home_id = messcut_upsert_page( 'home', 'Головна', '' );
	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	messcut_upsert_page(
		'dosvid',
		'Досвід та підхід',
		'',
		'page-approach.php',
		array(
			'approach_content' => '<p>В основі нашої роботи – маркетинг, доведений наукою. Допомагаємо брендам зростати через реальну цінність для людей, системний підхід та стратегії, креатив не заради краси, а заради ефективності.</p><p>Основні драйвери ефективності маркетингу: реальна цінність для людей, системний підхід, креативність заради ефективності та науково обґрунтовані рішення.</p>',
		)
	);

	messcut_upsert_page(
		'poslugy',
		'Послуги',
		'<p>Комплексні послуги для побудови та розвитку брендів: від стратегії до маркетингового супроводу.</p>'
	);

	messcut_upsert_page(
		'publichna-oferta',
		'Публічна оферта',
		messcut_get_legal_offer_content()
	);

	messcut_upsert_page(
		'polityka-konfidentsiynosti',
		'Політика конфіденційності',
		messcut_get_legal_privacy_content()
	);
}

/**
 * Upsert a page by slug.
 *
 * @param string               $slug     Slug.
 * @param string               $title    Title.
 * @param string               $content  Content.
 * @param string               $template Page template.
 * @param array<string, mixed> $fields   ACF fields.
 * @return int Post ID or 0.
 */
function messcut_upsert_page( string $slug, string $title, string $content, string $template = '', array $fields = array() ): int {
	$existing = get_page_by_path( $slug );
	$args = array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_content' => $content,
	);

	if ( $existing ) {
		$args['ID'] = $existing->ID;
		$post_id    = wp_update_post( $args, true );
	} else {
		$post_id = wp_insert_post( $args, true );
	}

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}

	if ( $template ) {
		update_post_meta( (int) $post_id, '_wp_page_template', $template );
	}

	messcut_seed_update_post_fields( (int) $post_id, $fields );

	return (int) $post_id;
}

/**
 * Sync navigation menus to match current IA.
 */
function messcut_sync_menus(): void {
	$primary = wp_get_nav_menu_object( 'primary' );
	if ( ! $primary ) {
		$primary_id = wp_create_nav_menu( 'primary' );
	} else {
		$primary_id = (int) $primary->term_id;
	}

	$footer = wp_get_nav_menu_object( 'footer' );
	if ( ! $footer ) {
		$footer_id = wp_create_nav_menu( 'footer' );
	} else {
		$footer_id = (int) $footer->term_id;
	}

	messcut_clear_nav_menu( $primary_id );
	messcut_clear_nav_menu( $footer_id );

	$primary_items = array(
		array( 'type' => 'page', 'slug' => 'home', 'title' => 'Головна' ),
		array( 'type' => 'page', 'slug' => 'poslugy', 'title' => 'Послуги' ),
		array( 'type' => 'archive', 'url' => get_post_type_archive_link( 'case_study' ), 'title' => 'Кейси' ),
		array( 'type' => 'page', 'slug' => 'dosvid', 'title' => 'Досвід та підхід' ),
		array( 'type' => 'archive', 'url' => get_post_type_archive_link( 'article' ), 'title' => 'Інсайти' ),
	);

	foreach ( $primary_items as $item ) {
		if ( 'page' === $item['type'] ) {
			$page = get_page_by_path( $item['slug'] );
			if ( ! $page ) {
				continue;
			}
			wp_update_nav_menu_item( $primary_id, 0, array(
				'menu-item-title'     => $item['title'],
				'menu-item-object'    => 'page',
				'menu-item-object-id' => $page->ID,
				'menu-item-type'      => 'post_type',
				'menu-item-status'    => 'publish',
			) );
			continue;
		}

		wp_update_nav_menu_item( $primary_id, 0, array(
			'menu-item-title'  => $item['title'],
			'menu-item-url'    => $item['url'],
			'menu-item-type'   => 'custom',
			'menu-item-status' => 'publish',
		) );
	}

	$legal_items = array(
		array( 'slug' => 'publichna-oferta', 'title' => 'Публічна оферта' ),
		array( 'slug' => 'polityka-konfidentsiynosti', 'title' => 'Політика конфіденційності' ),
	);

	foreach ( $legal_items as $item ) {
		$page = get_page_by_path( $item['slug'] );
		if ( ! $page ) {
			continue;
		}
		wp_update_nav_menu_item( $footer_id, 0, array(
			'menu-item-title'     => $item['title'],
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $page->ID,
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
		) );
	}

	set_theme_mod( 'nav_menu_locations', array(
		'primary' => $primary_id,
		'footer'  => $footer_id,
	) );

	if ( function_exists( 'pll_set_term_language' ) ) {
		pll_set_term_language( $primary_id, 'uk' );
		pll_set_term_language( $footer_id, 'uk' );

		$theme   = get_stylesheet();
		$options = get_option( 'polylang' );
		if ( is_array( $options ) ) {
			if ( ! isset( $options['nav_menus'][ $theme ] ) ) {
				$options['nav_menus'][ $theme ] = array();
			}
			$options['nav_menus'][ $theme ]['primary']['uk'] = $primary_id;
			$options['nav_menus'][ $theme ]['footer']['uk']  = $footer_id;
			update_option( 'polylang', $options );
		}
	}
}

/**
 * Remove all items from a nav menu.
 *
 * @param int $menu_id Menu term ID.
 */
function messcut_clear_nav_menu( int $menu_id ): void {
	$items = wp_get_nav_menu_items( $menu_id );
	if ( ! $items ) {
		return;
	}
	foreach ( $items as $item ) {
		wp_delete_post( (int) $item->ID, true );
	}
}

/**
 * Legal offer HTML.
 */
function messcut_get_legal_offer_content(): string {
	return '<h2>1. Загальні положення</h2>
<p>Цей документ є офіційною публічною пропозицією (офертою) щодо надання маркетингових, стратегічних, консультаційних та суміжних послуг під брендом MESSCUT.</p>
<p>Замовлення послуг, здійснення оплати або підтвердження співпраці будь-яким способом означає повне та безумовне прийняття цієї оферти.</p>
<h2>2. Предмет договору</h2>
<p>Виконавець надає Замовнику послуги, що можуть включати: розробку бренд-стратегії, маркетингові дослідження, консультації, менторство, маркетинг-сапорт, розробку маркетингових планів, стратегічний супровід бізнесу та інші послуги відповідно до погодженого обсягу робіт.</p>
<h2>3. Формат надання послуг</h2>
<p>Послуги можуть надаватися онлайн, офлайн, у вигляді консультацій, презентацій та стратегічних документів, у форматі регулярного супроводу. Конкретний перелік робіт визначається індивідуально перед початком співпраці.</p>
<h2>4. Вартість послуг</h2>
<p>Вартість визначається індивідуально для кожного проєкту. Інформація про ціни, комерційні пропозиції та рахунки є невід\'ємною частиною домовленостей між сторонами.</p>
<h2>5. Порядок оплати</h2>
<p>Оплата здійснюється у формі передоплати, повної або часткової, відповідно до погоджених умов співпраці. Початок виконання робіт можливий після отримання оплати або першого платежу.</p>
<h2>6. Інтелектуальна власність</h2>
<p>Усі стратегічні матеріали, документи, презентації, дослідження та інші результати робіт передаються Замовнику після повної оплати послуг. До моменту повної оплати всі матеріали залишаються інтелектуальною власністю Виконавця.</p>
<h2>7. Відповідальність сторін</h2>
<p>MESSCUT не гарантує конкретних фінансових результатів, обсягів продажів або показників прибутку. Результати впровадження рекомендацій залежать від багатьох факторів, включаючи якість реалізації, ринок, продукт, конкуренцію, бюджет та управлінські рішення Замовника.</p>
<h2>8. Конфіденційність</h2>
<p>Сторони зобов\'язуються не розголошувати конфіденційну інформацію, отриману під час співпраці.</p>
<h2>9. Форс-мажор</h2>
<p>Сторони звільняються від відповідальності за невиконання зобов\'язань у випадках дії обставин непереборної сили.</p>
<h2>10. Контактна інформація</h2>
<p>MESSCUT<br>Email: admin@messcut.com<br>Telegram: @messcutstrategy<br>Телефон: +38 (095) 477-11-22</p>';
}

/**
 * Legal privacy HTML.
 */
function messcut_get_legal_privacy_content(): string {
	return '<p>MESSCUT поважає право кожного користувача на конфіденційність та захист персональних даних.</p>
<h2>1. Загальні положення</h2>
<p>Ця Політика конфіденційності визначає порядок збору, використання та зберігання персональних даних користувачів сайту MESSCUT. Користуючись сайтом, ви погоджуєтеся з умовами цієї Політики.</p>
<h2>2. Які дані ми можемо збирати</h2>
<p>Під час використання сайту ми можемо отримувати: ім\'я та прізвище, номер телефону, адресу електронної пошти, назву компанії, посилання на соціальні мережі або сайт, інформацію з форм зворотного зв\'язку, технічні дані про використання сайту.</p>
<h2>3. Для чого ми використовуємо дані</h2>
<p>Персональні дані можуть використовуватися для зв\'язку з користувачем, обробки заявок, надання консультацій та послуг, покращення роботи сайту, маркетингової аналітики та надсилання інформації щодо послуг MESSCUT.</p>
<h2>4. Передача даних третім особам</h2>
<p>Ми не продаємо та не передаємо персональні дані третім особам, окрім випадків, коли це необхідно для надання послуг, коли цього вимагає закон, або за згодою користувача.</p>
<h2>5. Зберігання даних</h2>
<p>Ми вживаємо необхідних організаційних та технічних заходів для захисту персональних даних від несанкціонованого доступу, втрати або розголошення.</p>
<h2>6. Cookies</h2>
<p>Сайт може використовувати файли cookie для аналітики та покращення користувацького досвіду. Користувач може самостійно змінити налаштування використання cookie у своєму браузері.</p>
<h2>7. Права користувача</h2>
<p>Користувач має право отримувати інформацію про свої персональні дані, вимагати виправлення або видалення даних, відкликати згоду на обробку даних та звертатися із запитами щодо обробки персональних даних.</p>
<h2>8. Контакти</h2>
<p>Email: admin@messcut.com<br>Telegram: @messcutstrategy<br>Телефон: +38 (095) 477-11-22</p>';
}
