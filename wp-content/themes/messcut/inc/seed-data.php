<?php
/**
 * Seed payloads for the raw-HTML content model.
 *
 * @package Messcut
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<string, array<string, mixed>>
 */
function messcut_get_service_seed_data(): array {
	return array(
		'brand-strategy' => array(
			'title'   => 'Бренд-стратегія',
			'excerpt' => 'Стратегічний фундамент бізнесу за 1 місяць',
			'order'   => 1,
			'fields'  => array(
				'direction' => 'branding',
				'eyebrow'   => 'Етап 01',
				'teaser'    => 'Стратегічний фундамент бізнесу за 1 місяць',
				'locked'    => 0,
				'cta_label' => 'Отримати стратегію',
				'bullets'   => array(
					array( 'text' => 'Дослідження ринку, конкурентів, аудиторії, трендів' ),
					array( 'text' => 'Позиціонування і ціннісна пропозиція бренду' ),
					array( 'text' => 'Місія, цінності, tone of voice' ),
					array( 'text' => 'Готовий стратегічний документ' ),
				),
				'steps'     => array(
					array( 'title' => 'Дослідження', 'text' => 'Ринок, категорія, конкуренти, аудиторія, глибинні інтервʼю.' ),
					array( 'title' => 'Система бренду', 'text' => 'Позиціонування, ціннісна пропозиція, місія, цінності, характер, Tone of Voice.' ),
					array( 'title' => 'Маркетинг', 'text' => 'Customer Journey Map, комунікаційні та візуальні принципи, пріоритети зростання.' ),
					array( 'title' => 'Результат', 'text' => 'Єдина стратегічна система, яка задає напрям для маркетингу, комунікації та подальшого розвитку.' ),
				),
			),
		),
		'strategy-implementation' => array(
			'title'   => 'Реалізація стратегії',
			'excerpt' => 'Втілюємо стратегію в дію за 2 тижні',
			'order'   => 2,
			'fields'  => array(
				'direction' => 'branding',
				'eyebrow'   => 'Етап 02',
				'teaser'    => 'Втілюємо стратегію в дію за 2 тижні',
				'locked'    => 1,
				'cta_label' => 'Доступно після бренд-стратегії',
				'bullets'   => array(
					array( 'text' => 'Розуміння каналів для реалізації стратегії' ),
					array( 'text' => 'Маркетинг-план на перші 6 місяців' ),
					array( 'text' => 'Приклади креативів по ключових каналах просування' ),
					array( 'text' => 'Настановча сесія для команди' ),
				),
				'steps'     => array(
					array( 'title' => 'Канали', 'text' => 'Визначаємо канали, формати та підхід до контенту для реалізації стратегії.' ),
					array( 'title' => 'Маркетинг-план', 'text' => 'Складаємо маркетинг-план на перші 6 місяців.' ),
					array( 'title' => 'Креативи', 'text' => 'Готуємо приклади креативів по ключових каналах просування згідно зі стратегією.' ),
					array( 'title' => 'Настановча сесія', 'text' => 'Проводимо сесію для команди, передаємо всі матеріали та пояснюємо, як працювати зі стратегією далі.' ),
				),
			),
		),
		'marketing-audit' => array(
			'title'   => 'Маркетинговий аудит',
			'excerpt' => 'Діагностика маркетингу за 2 тижні з готовим планом дій',
			'order'   => 3,
			'fields'  => array(
				'direction' => 'marketing',
				'eyebrow'   => 'Аудит',
				'teaser'    => 'Діагностика маркетингу за 2 тижні з готовим планом дій',
				'locked'    => 0,
				'cta_label' => 'Отримати аудит',
				'bullets'   => array(
					array( 'text' => 'Аудит каналів і рекламних кабінетів' ),
					array( 'text' => 'Аналіз юніт-економіки та ефективності' ),
					array( 'text' => 'Перевірка аналітики і даних' ),
					array( 'text' => 'Пріоритетний roadmap зростання' ),
				),
				'steps'     => array(
					array( 'title' => 'Аудит маркетингу', 'text' => 'Перевіряємо канали залучення, рекламні кампанії, кабінети та ефективність підрядників.' ),
					array( 'title' => 'Економіка маркетингу', 'text' => 'Аналізуємо LTV, CAC, Retention, ROAS, маржинальність і конверсії на ключових етапах воронки.' ),
					array( 'title' => 'План зростання', 'text' => 'Визначаємо точки втрати прибутку, можливості зростання та пріоритетні зміни на найближчі 3 місяці.' ),
				),
			),
		),
		'marketing-support' => array(
			'title'   => 'Маркетинговий супровід',
			'excerpt' => 'Fractional CMO — зовнішній маркетинг-директор, який веде бізнес до зростання в цифрах',
			'order'   => 4,
			'fields'  => array(
				'direction'    => 'marketing',
				'eyebrow'      => 'Супровід',
				'teaser'       => 'Fractional CMO — зовнішній маркетинг-директор, який веде бізнес до зростання в цифрах',
				'locked'       => 0,
				'cta_label'    => 'Почати супровід',
				'proof_stat'   => '+250%',
				'proof_label'  => 'зростання рентабельності маркетингових інвестицій за перші 6 місяців',
				'bullets'      => array(
					array( 'text' => 'Ростимо рентабельність маркетингових інвестицій' ),
					array( 'text' => 'Покращуємо LTV : CAC і частку повторних покупок' ),
					array( 'text' => 'Щотижневі спринти та щомісячна звітність по KPI' ),
					array( 'text' => 'Управління командою та підрядниками' ),
				),
				'steps'        => array(
					array( 'title' => 'Цілі та KPI', 'text' => 'Відповідаємо за маркетингову стратегію, план, бюджет і виконання KPI.' ),
					array( 'title' => 'Масштабування', 'text' => 'Визначаємо точки зростання, тестуємо гіпотези й перерозподіляємо бюджет.' ),
					array( 'title' => 'Планування та контроль', 'text' => 'Проводимо щотижневі спринти та щомісяця аналізуємо результати в цифрах.' ),
					array( 'title' => 'Команда та підрядники', 'text' => 'Координуємо in-house спеціалістів і зовнішніх підрядників.' ),
					array( 'title' => 'Ресурси', 'text' => 'За потреби допомагаємо сформувати команду і підключити спеціалістів.' ),
				),
			),
		),
	);
}

/**
 * @return array<string, array<string, mixed>>
 */
function messcut_get_case_seed_data(): array {
	return array(
		'choozy' => array(
			'title'   => 'Choozy',
			'excerpt' => 'Як ми створили бренд CHOOZY у категорії, де майже всі говорять про одне й те саме',
			'order'   => 1,
			'tone'    => '#d9f7ea,#a8e6cb',
		),
		'sloway' => array(
			'title'   => 'Sloway',
			'excerpt' => 'Як ми перетворили матрац із товару для сну на платформу для сучасного способу життя',
			'order'   => 2,
			'tone'    => '#e3efe9,#9fc9b6',
		),
		'boostera' => array(
			'title'   => 'Boostera',
			'excerpt' => 'Як ми перетворили мовну школу на бренд для людей, які хочуть більшого від життя',
			'order'   => 3,
			'tone'    => '#c7f2e1,#8fdcbc',
		),
		'antytezys' => array(
			'title'   => 'Antytezys',
			'excerpt' => 'Як ми створили бренд, який переосмислює жіночність через силу, а не через слабкість',
			'order'   => 4,
			'tone'    => '#eadccf,#c9a891',
		),
		'payen' => array(
			'title'   => 'Payen',
			'excerpt' => 'Як ми запустили бренд доглядової косметики з нуля та побудували систему для його масштабування',
			'order'   => 5,
			'tone'    => '#f1e6dc,#d7bca6',
		),
		'hottier' => array(
			'title'   => 'Hottier',
			'excerpt' => 'Як ми знайшли вільну позицію між культом ідеального тіла та body positivity і перетворили її на бренд спортивного одягу',
			'order'   => 6,
			'tone'    => '#c7f2e1,#b7a79b',
		),
	);
}

/**
 * Full chapter document extracted from the case mock, when one exists.
 *
 * @return array<string, mixed>
 */
function messcut_bundled_case_document( string $slug ): array {
	$path = MESSCUT_DIR . '/inc/data/cases-uk.json';
	if ( ! is_readable( $path ) ) {
		return array();
	}
	$all = json_decode( (string) file_get_contents( $path ), true );
	if ( ! is_array( $all ) || empty( $all['cases'][ $slug ] ) || ! is_array( $all['cases'][ $slug ] ) ) {
		return array();
	}
	$doc = $all['cases'][ $slug ];
	if ( ! empty( $all['expert'] ) ) {
		$doc['expert'] = $all['expert'];
	}
	return $doc;
}

/**
 * Flat home_faq rows saved on the options page.
 *
 * @return array<int, array<string, string>>
 */
function messcut_get_home_faq_seed(): array {
	$items = array();
	foreach ( messcut_get_faq_seed_data() as $group ) {
		foreach ( $group['items'] ?? array() as $item ) {
			if ( is_array( $item ) ) {
				$items[] = $item;
			}
		}
	}
	return $items;
}

/**
 * @return array<int, array<string, mixed>>
 */
function messcut_get_faq_seed_data( string $lang = 'uk' ): array {
	unset( $lang );
	return array(
		array(
			'title' => 'FAQ',
			'items' => array(
				array( 'question' => 'Що таке бренд-стратегія і навіщо вона бізнесу?', 'answer' => 'Бренд-стратегія — це система рішень про те, хто ви як бренд, для кого існуєте, у чому ваша відмінність і як комунікуєте з аудиторією. Вона дає основу для маркетингу, продукту та зростання без хаотичних експериментів.' ),
				array( 'question' => 'Чим Messcut відрізняється від звичайного рекламного агентства?', 'answer' => 'Ми — boutique-агенція стратегічного маркетингу. Фокусуємось на дослідженнях, позиціонуванні та системній побудові бренду, а не на разових рекламних кампаніях без стратегічного фундаменту.' ),
				array( 'question' => 'Які послуги ви надаєте?', 'answer' => 'Бренд-стратегія, реалізація стратегії, маркетинговий аудит і маркетинговий супровід (Fractional CMO). Формат і глибина співпраці підбираються під задачі бізнесу.' ),
				array( 'question' => 'Скільки часу займає розробка бренд-стратегії?', 'answer' => 'Тривалість залежить від масштабу бізнесу та глибини досліджень. Зазвичай проєкт триває від кількох тижнів до кількох місяців. Точні терміни узгоджуємо після короткого брифінгу.' ),
				array( 'question' => 'З якими бізнесами ви працюєте?', 'answer' => 'З підприємцями та компаніями, які хочуть будувати бренд системно: від запуску нового продукту до масштабування вже існуючого бізнесу в Україні та на міжнародних ринках.' ),
				array( 'question' => 'Чи потрібні дослідження на старті проєкту?', 'answer' => 'Так. Дослідження — основа стратегії: вони допомагають зрозуміти аудиторію, конкурентів і контекст категорії. Без даних рішення базуються на припущеннях, а не на перевірених інсайтах.' ),
				array( 'question' => 'Що таке Fractional CMO?', 'answer' => 'Це формат, коли зовнішній стратегічний маркетолог відповідає за маркетингову систему, команду та KPI без найму штатного CMO.' ),
			),
		),
	);
}

/**
 * @return array<int, array<string, string>>
 */
function messcut_get_team_seed_data(): array {
	$people = array( 'Валерія', 'Марія', 'Аліна' );
	$rows   = array();
	foreach ( $people as $name ) {
		$rows[] = array(
			'name'       => $name,
			'role'       => 'Роль / посада',
			'years'      => 'X років',
			'superpower' => 'Супер-сила спеціаліста',
		);
	}
	return $rows;
}

/**
 * @return array<int, array<string, string>>
 */
function messcut_get_article_seed_data(): array {
	return array(
		'strong-brand-positioning' => array(
			'title'   => 'Як створити сильне позиціонування бренду',
			'excerpt' => 'Позиціонування — не про красиві слова, а про чітку роль бренду в житті людей і на ринку. Розбираємо, як сформувати позицію, яку аудиторія розуміє і обирає.',
		),
		'research-foundation' => array(
			'title'   => 'Чому дослідження — основа бренд-стратегії',
			'excerpt' => 'Стратегія без даних — це гіпотези. Пояснюємо, які типи досліджень дають найбільший ефект на старті проєкту і як не витратити бюджет на «красиві презентації».',
		),
		'fractional-cmo' => array(
			'title'   => 'Що таке Fractional CMO і коли бізнесу потрібен директор з маркетингу на аутсорсі',
			'excerpt' => 'Fractional CMO — формат, коли зовнішній стратегічний маркетолог відповідає за систему, команду та результати без найму штатного CMO. Коли це працює і що очікувати від партнерства.',
		),
	);
}
