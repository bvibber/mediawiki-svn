<?php
/**
 * Internationalisation for ContributionReporting extension
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Trevor Parscal
 */
$messages['en'] = array(
	// ContributionReporting and ContributionTotal
	'contributionreporting-desc' => 'Live reporting on the Wikimedia fundraiser',
	'contributiontotal' => 'Contribution total',
	'contributionhistory' => 'Contribution history',
	
	'contrib-hist-header' => 'Real-time donor comments from around the world',
	
	'contrib-hist-name' => 'Name',
	'contrib-hist-date' => 'Time and date',
	'contrib-hist-amount' => 'Amount',
	
	'contrib-hist-next' => 'Earlier donations',
	'contrib-hist-previous' => 'Newer donations',
	
	'contrib-hist-anonymous' => 'Anonymous',

	// ContributionStatistics
	'contributionstatistics' => 'Contribution statistics',
	'contribstats-desc' => 'Displays statistics for contributions made to the Wikimedia Foundation',
	'contribstats-daily-totals' => '{{PLURAL:$1|Total for today|Daily totals for the past $1 days}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Total for this month|Monthly totals for the past $1 months}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Total for $1 month|Monthly totals for $1 months}}',
	'contribstats-currency-range-totals' => 'Currency totals (from $1 to $2)',
	'contribstats-contribution-range-breakdown' => 'Breakdown of contributions by value (from $1 to $2)',
	'contribstats-currency-totals' => 'Currency totals for the fiscal year of $1',
	'contribstats-contribution-breakdown' => 'Breakdown of contributions by value for the fiscal year of $1',
	'contribstats-day' => 'Day',
	'contribstats-month' => 'Month',
	'contribstats-currency' => 'Currency',
	'contribstats-amount' => 'Amount (USD)',
	'contribstats-contributions' => 'Contributions',
	'contribstats-total' => 'Total (USD)',
	'contribstats-avg' => 'Average (USD)',
	'contribstats-min' => 'Minimum (USD)',
	'contribstats-max' => 'Maximum (USD)',
	'contribstats-percentage-ytd' => 'Percentage (YTD)',
	'contribstats-total-ytd' => 'Total (YTD)',
	
	// Fundraiser statistics
	'fundraiserstatistics' => 'Fundraiser statistics',
	'fundraiserstats-date' => 'Date',
	'fundraiserstats-total' => 'Total (USD)',
	'fundraiserstats-avg' => 'Average (USD)',
	'fundraiserstats-min' => 'Minimum (USD)',
	'fundraiserstats-max' => 'Maximum (USD)',
);

/** Message documentation (Message documentation)
 * @author Darth Kule
 */
$messages['qqq'] = array(
	'contrib-hist-name' => '{{Identical|Name}}',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'contrib-hist-date' => 'Datum en tyd',
	'contrib-hist-amount' => 'Bedrag',
	'contrib-hist-anonymous' => 'Anoniem',
	'contribstats-day' => 'Dag',
	'contribstats-month' => 'Maand',
	'contribstats-currency' => 'Geldeenheid',
	'contribstats-amount' => 'Bedrag (in USD)',
	'contribstats-contributions' => 'Skenkings',
	'contribstats-total' => 'Totaal (in USD)',
	'contribstats-avg' => 'Gemiddeld (in USD)',
	'contribstats-min' => 'Minimum (in USD)',
	'contribstats-max' => 'Maksimum (in USD)',
	'contribstats-percentage-ytd' => 'Persentasie (die jaar)',
	'contribstats-total-ytd' => 'Totaal (die jaar)',
);

/** Arabic (العربية)
 * @author OsamaK
 */
$messages['ar'] = array(
	'contributionreporting-desc' => 'تقرير حي على جامع تبرعات ويكيميديا',
	'contributiontotal' => 'مجموع المساهمة',
	'contributionhistory' => 'تاريخ المساهمة',
	'contrib-hist-header' => 'تعليقات المانحين في الوقت الحقيقي حول العالم',
	'contrib-hist-name' => 'الاسم',
	'contrib-hist-date' => 'الوقت والتاريخ',
	'contrib-hist-amount' => 'الكمية',
	'contrib-hist-anonymous' => 'مجهول',
	'contributionstatistics' => 'إحصاءات المساهمة',
	'contribstats-desc' => 'يعرض إحصاءات المساهمات لمؤسسة ويكيميديا',
	'contribstats-currency-range-totals' => 'مجموع العملات (من $1 إلى $2)',
	'contribstats-currency-totals' => 'مجموع العملات للسنة المالية ل$1',
	'contribstats-day' => 'اليوم',
	'contribstats-month' => 'الشهر',
	'contribstats-currency' => 'العملة',
	'contribstats-amount' => 'الكمية (دولار أمريكي)',
	'contribstats-contributions' => 'المساهمات',
	'contribstats-total' => 'المجموع (دولار أمريكي)',
	'contribstats-avg' => 'المعدل (دولار أمريكي)',
	'contribstats-min' => 'الأدنى (دولار أمريكي)',
	'contribstats-max' => 'الأقصى (دولار أمريكي)',
	'contribstats-percentage-ytd' => 'النسبة المئوية (منذ بداية السنة)',
	'contribstats-total-ytd' => 'المجموع  (منذ بداية السنة)',
);

/** Egyptian Spoken Arabic (مصرى)
 * @author Ghaly
 * @author Ramsis II
 */
$messages['arz'] = array(
	'contribstats-day' => 'اليوم',
	'contribstats-month' => 'الشهر',
	'contribstats-currency' => 'العملة',
	'contribstats-amount' => 'الكمية (بالدولار الامريكاني)',
	'contribstats-contributions' => 'المساهمات',
	'contribstats-total' => 'الاجمالي(بالدولار الامريكاني)',
	'contribstats-avg' => 'المتوسط(بالدولار الامريكاني)',
	'contribstats-min' => 'الحد الادنى(بالدولار الامريكاني)',
	'contribstats-max' => 'الحد الاقصى(بالدولار الامريكاني)',
	'contribstats-percentage-ytd' => 'النسبه المئويه (من اول السنه)',
	'contribstats-total-ytd' => 'الاجمالي(من اول السنه)',
	'fundraiserstatistics' => 'احصائيات جمع التبرعات',
	'fundraiserstats-date' => 'تاريخ',
	'fundraiserstats-total' => 'الاجمالي(بالدولار الامريكاني)',
	'fundraiserstats-avg' => 'المتوسط (بالدولار الامريكاني)',
);

/** Bulgarian (Български)
 * @author DCLXVI
 * @author Spiritia
 */
$messages['bg'] = array(
	'contributionreporting-desc' => 'Репортаж наживо от дарителската акция на Уикимедия',
	'contributiontotal' => 'Обща стойност на дарението',
	'contributionhistory' => 'История на дарението',
	'contrib-hist-header' => 'Коментари наживо от дарители по целия свят',
	'contrib-hist-name' => 'Име',
	'contrib-hist-date' => 'Час и дата',
	'contrib-hist-amount' => 'Сума',
	'contrib-hist-next' => 'По-стари дарения',
	'contrib-hist-previous' => 'По-нови дарения',
	'contrib-hist-anonymous' => 'Анонимно дарение',
	'contributionstatistics' => 'Статистики за даренията',
	'contribstats-desc' => 'Показване на статистиките за даренията, направени за Фондация Уикимедия',
	'contribstats-contribution-range-breakdown' => 'Разбивка на даренията по стойност (от $1 до $2)',
	'contribstats-contribution-breakdown' => 'Разбивка на даренията по стойност за фискалната година $1',
	'contribstats-day' => 'Ден',
	'contribstats-month' => 'Месец',
	'contribstats-currency' => 'Валута',
	'contribstats-amount' => 'Сума (USD)',
	'contribstats-contributions' => 'Дарения',
	'contribstats-total' => 'Общо (USD)',
	'contribstats-avg' => 'Средно (USD)',
	'contribstats-min' => 'Минимум (USD)',
	'contribstats-max' => 'Максимум (USD)',
	'contribstats-percentage-ytd' => 'Процент (от началото на годината)',
	'contribstats-total-ytd' => 'Общо (от началото на годината)',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'contrib-hist-date' => 'Vrijeme i datum',
	'contribstats-percentage-ytd' => 'Procenat (YTD)',
	'contribstats-total-ytd' => 'Ukupno (YTD)',
);

/** Czech (Česky)
 * @author Danny B.
 * @author Mormegil
 */
$messages['cs'] = array(
	'contributionreporting-desc' => 'Živé monitorování sbírky Wikimedia',
	'contributiontotal' => 'Celková suma darů',
	'contributionhistory' => 'Historie darů',
	'contrib-hist-header' => 'Komentáře dárců z celého světa v reálném čase',
	'contrib-hist-name' => 'Jméno',
	'contrib-hist-date' => 'Čas a datum',
	'contrib-hist-amount' => 'Výše',
	'contrib-hist-next' => 'Starší příspěvky',
	'contrib-hist-previous' => 'Novější příspěvky',
	'contrib-hist-anonymous' => 'Anonym',
	'contributionstatistics' => 'Statistika příspěvků',
	'contribstats-desc' => 'Zobrazuje statistiku finančních příspěvků pro nadaci Wikimedia Foundation',
	'contribstats-daily-totals' => '{{PLURAL:$1|Souhrn za dnešek|Souhrny pro poslední $1 dny|Souhrny pro posledních $1 dní}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Souhrn za tento měsíc|Souhrny pro poslední $1 měsíce|Souhrny pro posledních $1 měsíců}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Souhrn pro daný měsíc|Souhrny pro $1 zadané měsíce|Souhrny pro $1 zadaných měsíců}}',
	'contribstats-currency-range-totals' => 'Souhrny pro jednotlivé měny (od $1 do $2)',
	'contribstats-contribution-range-breakdown' => 'Rozdělení příspěvků podle hodnoty (od $1 do $2)',
	'contribstats-currency-totals' => 'Souhrny pro jednotlivé měny za fiskální rok $1',
	'contribstats-contribution-breakdown' => 'Rozdělení příspěvků za fiskální rok $1 podle hodnoty',
	'contribstats-day' => 'Den',
	'contribstats-month' => 'Měsíc',
	'contribstats-currency' => 'Měna',
	'contribstats-amount' => 'Částka (USD)',
	'contribstats-contributions' => 'Počet příspěvků',
	'contribstats-total' => 'Celkem (USD)',
	'contribstats-avg' => 'Průměr (USD)',
	'contribstats-min' => 'Minimum (USD)',
	'contribstats-max' => 'Maximum (USD)',
	'contribstats-percentage-ytd' => 'Podíl (za letošní rok)',
	'contribstats-total-ytd' => 'Celkem (od začátku roku)',
	'fundraiserstatistics' => 'Statistika příspěvkové kampaně',
	'fundraiserstats-date' => 'Datum',
	'fundraiserstats-total' => 'Celkem (USD)',
	'fundraiserstats-avg' => 'Průměr (USD)',
	'fundraiserstats-min' => 'Minimum (USD)',
	'fundraiserstats-max' => 'Maximum (USD)',
);

/** German (Deutsch)
 * @author Metalhead64
 */
$messages['de'] = array(
	'contributionreporting-desc' => 'Live-Berichterstattung von der Wikimedia-Spendenkampagne',
	'contributiontotal' => 'Spenden insgesamt',
	'contributionhistory' => 'Spendenverlauf',
	'contrib-hist-header' => 'Spendenkommentare in Echtzeit aus der ganzen Welt',
	'contrib-hist-name' => 'Name',
	'contrib-hist-date' => 'Zeit und Datum',
	'contrib-hist-amount' => 'Betrag',
	'contrib-hist-next' => 'Ältere Spenden',
	'contrib-hist-previous' => 'Neuere Spenden',
	'contrib-hist-anonymous' => 'Anonym',
	'contributionstatistics' => 'Spendenstatistik',
	'contribstats-desc' => 'Statistik über die Spenden an die Wikimedia Foundation',
	'contribstats-daily-totals' => '{{PLURAL:$1|Gesamtsumme für den Tag|Gesamtsumme für die letzten $1 Tage}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Gesamtsumme für diesen Monat|Gesamtsumme für die letzten $1 Monate}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Gesamtsumme für einen Monat|Gesamtsumme für $1 Monate}}',
	'contribstats-currency-range-totals' => 'Währungen gesamt (von $1 bis $2)',
	'contribstats-contribution-range-breakdown' => 'Spendenübersicht nach Wert (von $1 bis $2)',
	'contribstats-currency-totals' => 'Gesamtsumme für das fiskalische Jahr $1',
	'contribstats-contribution-breakdown' => 'Spendenübersicht nach Wert für das fiskalische Jahr $1',
	'contribstats-day' => 'Tag',
	'contribstats-month' => 'Monat',
	'contribstats-currency' => 'Währung',
	'contribstats-amount' => 'Betrag (USD)',
	'contribstats-contributions' => 'Spenden',
	'contribstats-total' => 'Gesamt (USD)',
	'contribstats-avg' => 'Durchschnitt (USD)',
	'contribstats-min' => 'Minimum (USD)',
	'contribstats-max' => 'Maximum (USD)',
	'contribstats-percentage-ytd' => 'Prozentsatz (YTD)',
	'contribstats-total-ytd' => 'Gesamt (YTD)',
	'fundraiserstatistics' => 'Spendenstatistiken',
	'fundraiserstats-date' => 'Datum',
	'fundraiserstats-total' => 'Gesamt (USD)',
	'fundraiserstats-avg' => 'Durchschnitt (USD)',
	'fundraiserstats-min' => 'Minimum (USD)',
	'fundraiserstats-max' => 'Maximum (USD)',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'contributiontotal' => 'Kontribua tuto',
	'contributionhistory' => 'Kontribua historio',
	'contrib-hist-header' => 'Aktualaj komentoj de donacantoj ĉirkaŭ la mondo',
	'contrib-hist-name' => 'Nomo',
	'contrib-hist-date' => 'Tempo kaj dato',
	'contrib-hist-amount' => 'Iom',
	'contrib-hist-anonymous' => 'Anonimulo',
	'contribstats-day' => 'Tago',
	'contribstats-month' => 'Monato',
	'contribstats-currency' => 'Valuto',
	'contribstats-contributions' => 'Kontribuoj',
	'contribstats-total' => 'Tuto (USD)',
	'contribstats-min' => 'Minimuma (USD)',
	'contribstats-max' => 'Maksimumo (USD)',
);

/** Persian (فارسی)
 * @author Huji
 * @author Komeil 4life
 */
$messages['fa'] = array(
	'contributionreporting-desc' => 'گزارش زنده از جذب سرمایهٔ ویکی‌مدیا',
	'contributiontotal' => 'جمع کمک‌ها',
	'contributionhistory' => 'تاریخچه مشارکت',
	'contrib-hist-header' => 'نظرات کمک‌کنندگان سراسر جهان به طور زنده',
	'contrib-hist-name' => 'نام',
	'contrib-hist-date' => 'تاریخ و زمان',
	'contrib-hist-amount' => 'مبلغ',
	'contrib-hist-next' => 'کمک‌های قدیمی‌تر',
	'contrib-hist-previous' => 'کمک‌های جدیدتر',
	'contrib-hist-anonymous' => 'گمنام',
	'contributionstatistics' => 'آمار کمک‌ها',
	'contribstats-desc' => 'نمایش آمار کمک‌های اهدا شده به بنیاد ویکی‌مدیا',
	'contribstats-daily-totals' => '{{PLURAL:$1|جمع برای امروز|جمع روزانه برای $1 روز اخیر}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|جمع برای این ماه|جمع ماهانه برای $1 ماه قبل}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|جمع برای $1 ماه|جمع ماهانه برای $1 ماه}}',
	'contribstats-currency-range-totals' => 'جمع درآمدها (از $1 تا $2)',
	'contribstats-contribution-range-breakdown' => 'تفکیک کمک‌ها بر اساس ارزش (از $1 تا $2)',
	'contribstats-currency-totals' => 'جمع درآمدها برای سال مالی $1',
	'contribstats-contribution-breakdown' => 'تفکیک کمک‌ها بر اساس ارزش در سال مالی $1',
	'contribstats-day' => 'روز',
	'contribstats-month' => 'ماه',
	'contribstats-currency' => 'واحد پول',
	'contribstats-amount' => 'مقدار (دلار آمریکا)',
	'contribstats-contributions' => 'مشارکت‌ها',
	'contribstats-total' => 'جمع (دلار آمریکا)',
	'contribstats-avg' => 'میانگین (دلار آمریکا)',
	'contribstats-min' => 'حداقل (USD)',
	'contribstats-max' => 'حداکثر (USD)',
	'contribstats-percentage-ytd' => 'درصد (از ابتدای سال)',
	'contribstats-total-ytd' => 'جمع (از ابتدای سال)',
	'fundraiserstatistics' => 'آمار جذب سرمایه',
	'fundraiserstats-date' => 'تاریخ',
	'fundraiserstats-total' => 'جمع (دلار آمریکا)',
	'fundraiserstats-avg' => 'میانگین (دلار آمریکا)',
	'fundraiserstats-min' => 'حداقل (دلار آمریکا)',
	'fundraiserstats-max' => 'حداکثر (دلار آمریکا)',
);

/** Finnish (Suomi)
 * @author Crt
 * @author Silvonen
 * @author Str4nd
 */
$messages['fi'] = array(
	'contributionreporting-desc' => 'Reaaliaikainen raportti Wikimedian varainkeruusta.',
	'contributiontotal' => 'Lahjoitukset yhteensä',
	'contributionhistory' => 'Lahjoitushistoria',
	'contrib-hist-header' => 'Reaaliaikaiset lahjoittajien kommentit ympäri maailmaa',
	'contrib-hist-name' => 'Nimi',
	'contrib-hist-date' => 'Aika ja päiväys',
	'contrib-hist-amount' => 'Summa',
	'contrib-hist-anonymous' => 'Nimetön',
);

/** French (Français)
 * @author Grondin
 * @author IAlex
 * @author Korrigan
 */
$messages['fr'] = array(
	'contributionreporting-desc' => 'Rapport en direct concernant la collecte de fonds de Wikimedia',
	'contributiontotal' => 'Contributions totales',
	'contributionhistory' => 'Historique des contributions',
	'contrib-hist-header' => 'Commentaires en direct des donateurs à travers le monde',
	'contrib-hist-name' => 'Nom',
	'contrib-hist-date' => 'Date et heure',
	'contrib-hist-amount' => 'Quantité',
	'contrib-hist-next' => 'Dons plus anciens',
	'contrib-hist-previous' => 'Dons plus récents',
	'contrib-hist-anonymous' => 'Anonyme',
	'contributionstatistics' => 'Statistiques de contributions',
	'contribstats-desc' => 'Afficher les statistiques des contributions faites à la Wikimedia Foundation',
	'contribstats-daily-totals' => "{{PLURAL:$1|Total pour aujourd'hui|Totaux journaliers pour les derniers $1 jours}}",
	'contribstats-monthly-totals' => '{{PLURAL:$1|Total pour ce mois|Totaux mensuels pour les derniers $1 mois}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Total pour $1 mois|Totaux mensuels pour $1 mois}}',
	'contribstats-currency-range-totals' => 'Total (de $1 à $2)',
	'contribstats-contribution-range-breakdown' => 'Détail des contributions par montant (de $1 à $2)',
	'contribstats-currency-totals' => "Total pour l'année fiscale $1",
	'contribstats-contribution-breakdown' => "Détail des contributions par montant pour l'année fiscale $1",
	'contribstats-day' => 'Jour',
	'contribstats-month' => 'Mois',
	'contribstats-currency' => 'Devise',
	'contribstats-amount' => 'Montant (dollars US)',
	'contribstats-contributions' => 'Contributions',
	'contribstats-total' => 'Total (dollars US)',
	'contribstats-avg' => 'Moyenne (dollars US)',
	'contribstats-min' => 'Minimum (dollars US)',
	'contribstats-max' => 'Maximum (dollars US)',
	'contribstats-percentage-ytd' => 'Pourcentage (cette année)',
	'contribstats-total-ytd' => 'Total (cette année)',
	'fundraiserstatistics' => 'Statistiques de la levée de fonds',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'contributiontotal' => 'Contribucións totais',
	'contributionhistory' => 'Historial das contribucións',
	'contrib-hist-name' => 'Nome',
	'contrib-hist-date' => 'Data e hora',
	'contrib-hist-amount' => 'Cantidade',
	'contrib-hist-next' => 'Doazóns máis antigas',
	'contrib-hist-previous' => 'Doazóns máis novas',
	'contrib-hist-anonymous' => 'Anónimo',
	'contributionstatistics' => 'Estatísticas das contribucións',
	'contribstats-day' => 'Día',
	'contribstats-month' => 'Mes',
	'contribstats-currency' => 'Moeda',
	'contribstats-amount' => 'Cantidade (dólar estadounidense)',
	'contribstats-contributions' => 'Contribucións',
	'contribstats-total' => 'Total (dólar estadounidense)',
	'contribstats-avg' => 'Promedio (dólar estadounidense)',
	'contribstats-min' => 'Mínimo (dólar estadounidense)',
	'contribstats-max' => 'Máximo (dólar estadounidense)',
	'contribstats-percentage-ytd' => 'Porcentaxe (ata hoxe)',
	'contribstats-total-ytd' => 'Total (ata hoxe)',
);

/** Hebrew (עברית)
 * @author Rotem Liss
 */
$messages['he'] = array(
	'contributionreporting-desc' => 'עדכון חי מההתרמה לוויקימדיה',
	'contributiontotal' => 'סיכום התרומות',
	'contributionhistory' => 'היסטוריית התרומות',
	'contrib-hist-header' => 'הערות של תורמים מרחבי העולם מוצגות בשידור חי',
	'contrib-hist-name' => 'שם',
	'contrib-hist-date' => 'תאריך ושעה',
	'contrib-hist-amount' => 'סכום',
	'contrib-hist-next' => 'תרומות מוקדמות יותר',
	'contrib-hist-previous' => 'תרומות מאוחרות יותר',
	'contrib-hist-anonymous' => 'אנונימי',
	'contributionstatistics' => 'סטטיסטיקות התרומה',
	'contribstats-desc' => 'הצגת סטטיסטיקות לתרומות שבוצעו לקרן ויקימדיה',
	'contribstats-daily-totals' => 'סך הכל ל{{PLURAL:$1|יום זה|־$1 הימים האחרונים}}',
	'contribstats-monthly-totals' => 'סך הכל ל{{PLURAL:$1|חודש זה|־$1 החודשים האחרונים}}',
	'contribstats-month-range-totals' => 'סך הכל ל{{PLURAL:$1|חודש אחד|$1 חודשים}}',
	'contribstats-currency-range-totals' => 'סכומי מטבעות (בין $1 ל־$2)',
	'contribstats-contribution-range-breakdown' => 'פילוג התרומות לפי ערך (בין $1 ל־$2)',
	'contribstats-currency-totals' => 'סכומי המטבעות לשנת הכספים $1',
	'contribstats-contribution-breakdown' => 'פילוג התרומות לפי ערך בשנת הכספים $1',
	'contribstats-day' => 'יום',
	'contribstats-month' => 'חודש',
	'contribstats-currency' => 'מטבע',
	'contribstats-amount' => 'סכום (בדולרים)',
	'contribstats-contributions' => 'תורמים',
	'contribstats-total' => 'סך הכל (בדולרים)',
	'contribstats-avg' => 'ממוצע (בדולרים)',
	'contribstats-min' => 'מינימום (בדולרים)',
	'contribstats-max' => 'מקסימום (בדולרים)',
	'contribstats-percentage-ytd' => 'אחוז (מתחילת השנה)',
	'contribstats-total-ytd' => 'סך הכל (מתחילת השנה)',
	'fundraiserstatistics' => 'סטטיסטיקות ההתרמה',
	'fundraiserstats-date' => 'תאריך',
	'fundraiserstats-total' => 'סך הכל (בדולרים)',
	'fundraiserstats-avg' => 'ממוצע (בדולרים)',
	'fundraiserstats-min' => 'מינימום (בדולרים)',
	'fundraiserstats-max' => 'מקסימום (בדולרים)',
);

/** Croatian (Hrvatski)
 * @author Dalibor Bosits
 */
$messages['hr'] = array(
	'contrib-hist-name' => 'Ime',
	'contrib-hist-date' => 'Vrijeme i datum',
	'contrib-hist-amount' => 'Iznos',
	'contrib-hist-next' => 'Ranije donacije',
	'contrib-hist-previous' => 'Novije donacije',
	'contrib-hist-anonymous' => 'Anoniman',
	'contribstats-day' => 'Dan',
	'contribstats-month' => 'Mjesec',
	'contribstats-currency' => 'Valuta',
	'contribstats-amount' => 'Iznos (USD)',
	'contribstats-total' => 'Ukupno (USD)',
	'contribstats-avg' => 'Prosječno (USD)',
	'contribstats-min' => 'Najmanje (USD)',
	'contribstats-max' => 'Najviše (USD)',
	'contribstats-percentage-ytd' => 'Postotak (YTD)',
	'contribstats-total-ytd' => 'Ukupno (YTD)',
);

/** Hungarian (Magyar)
 * @author Bdamokos
 */
$messages['hu'] = array(
	'contributionreporting-desc' => 'Élő jelentés a Wikimédia adománygyűjtéséről',
	'contributiontotal' => 'Adományok összege',
	'contributionhistory' => 'Adományok története',
	'contrib-hist-header' => 'Adományozói megjegyzések a világ minden tájáról élőben',
	'contrib-hist-name' => 'Név',
	'contrib-hist-date' => 'Időpont',
	'contrib-hist-amount' => 'Összeg',
	'contrib-hist-next' => 'Korábbi adományok',
	'contrib-hist-previous' => 'Frissebb adományok',
	'contrib-hist-anonymous' => 'Névtelen',
	'contributionstatistics' => 'Adományok statisztikája',
	'contribstats-desc' => 'A Wikimédia Alapítvány részére nyújtott adományok statisztikáját mutatja',
	'contribstats-daily-totals' => '{{PLURAL:$1|Összesített adatok mára|Összesített napi adatok az elmúlt $1 napra}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Összesített adatok erre a hónapra|Összesített havi adatok az elmúlt $1 hónapra}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Összesített adatok $1 hónapra|Havi összesítések $1 hónapra}}',
	'contribstats-currency-range-totals' => 'Pénznem összesített adatai ($1–$2)',
	'contribstats-contribution-range-breakdown' => 'Az adományok összesítése érték alapján ($1–$2)',
	'contribstats-currency-totals' => 'Pénznem összesítések a $1 pénzügyi évre',
	'contribstats-contribution-breakdown' => 'Az adományok összesítése érték alapján a $1 pénzügyi évben',
	'contribstats-day' => 'Nap',
	'contribstats-month' => 'Hónap',
	'contribstats-currency' => 'Pénznem',
	'contribstats-amount' => 'Összeg (USD)',
	'contribstats-contributions' => 'Adományok',
	'contribstats-total' => 'Összesen (USD)',
	'contribstats-avg' => 'Átlag (USD)',
	'contribstats-min' => 'Minimum (USD)',
	'contribstats-max' => 'Maximum (USD)',
	'contribstats-percentage-ytd' => 'Százalék (YTD)',
	'contribstats-total-ytd' => 'Összesen (YTD)',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'contributionreporting-desc' => 'Reporto in directo super le collecta de fundos de Wikimedia',
	'contributiontotal' => 'Total de contributiones',
	'contributionhistory' => 'Historia de contributiones',
	'contrib-hist-header' => 'Commentos in directo de donatores in tote le mundo',
	'contrib-hist-name' => 'Nomine',
	'contrib-hist-date' => 'Hora e data',
	'contrib-hist-amount' => 'Quantitate',
	'contrib-hist-next' => 'Donationes plus ancian',
	'contrib-hist-previous' => 'Donationes plus recente',
	'contrib-hist-anonymous' => 'Anonyme',
	'contributionstatistics' => 'Statisticas de contributiones',
	'contribstats-desc' => 'Monstra le statisticas del contributiones donate al Fundation Wikimedia',
	'contribstats-daily-totals' => '{{PLURAL:$1|Total de hodie|Totales quotidian del passate $1 dies}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Total de iste mense|Totales mensual del passate $1 menses}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Total de $1 mense|Totales mensual de $1 menses}}',
	'contribstats-currency-range-totals' => 'Totales per valuta (de $1 a $2)',
	'contribstats-contribution-range-breakdown' => 'Separation del contributiones per valor (de $1 a $2)',
	'contribstats-currency-totals' => 'Totales de valutas pro le anno fiscal $1',
	'contribstats-contribution-breakdown' => 'Separation del contributiones per valor pro le anno fiscal $1',
	'contribstats-day' => 'Die',
	'contribstats-month' => 'Mense',
	'contribstats-currency' => 'Valuta',
	'contribstats-amount' => 'Amonta (USD)',
	'contribstats-contributions' => 'Contributiones',
	'contribstats-total' => 'Total (USD)',
	'contribstats-avg' => 'Media (USD)',
	'contribstats-min' => 'Minimo (USD)',
	'contribstats-max' => 'Maximo (USD)',
	'contribstats-percentage-ytd' => 'Percentage (iste anno)',
	'contribstats-total-ytd' => 'Total (iste anno)',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'contrib-hist-name' => 'Nome',
	'contributionstatistics' => 'Statistiche dei contributi',
	'contribstats-desc' => 'Mostra le statistiche dei contributi dati alla WikiMedia Foundation',
	'contribstats-day' => 'Giorno',
	'contribstats-month' => 'Mese',
	'contribstats-contributions' => 'Contributi',
	'contribstats-total' => 'Totale (USD)',
	'contribstats-avg' => 'Media (USD)',
	'contribstats-min' => 'Minimo (USD)',
	'contribstats-max' => 'Massimo (USD)',
	'contribstats-percentage-ytd' => 'Percentuale (YTD)',
	'contribstats-total-ytd' => 'Totale (YTD)',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'contributionreporting-desc' => 'Tirek övver de Wikimedia Shtefftung ier Spende-Sammlong bereeschte',
	'contributiontotal' => 'De Spende zosamme jeräshnet',
	'contributionhistory' => 'De Spende bes jäz',
	'contrib-hist-header' => 'De Spender uß alle Welt ier Annmerkunge tirek aanzeije',
	'contrib-hist-name' => 'Name',
	'contrib-hist-date' => 'Uhrzick un Dattum',
	'contrib-hist-amount' => 'Betraach',
	'contrib-hist-next' => 'Fröjer Spende',
	'contrib-hist-previous' => 'Neuer Spende',
	'contrib-hist-anonymous' => 'Namelos',
	'contributionstatistics' => 'Spendeshtatistik',
	'contribstats-desc' => 'Shtatistike över de Spende aan de Wikimedija-Shtefftung',
	'contribstats-daily-totals' => '{{PLURAL:$1|Jesamp för hück|Däächleshe Jesampzahle för de letzte $1 Dääsch|Kein Jesampzahle ze hann.}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Jesamp för diß Mohnd|Jesampzahle för de letzte $1 Moohnd|Kein Jesampzahle ze hann.}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Jesamp för eine Mohnd|Jesampzahle för $1 Mohnde|Kein Jesampzahle ze hann.}}',
	'contribstats-currency-range-totals' => 'Jesamp en Jeld-Zoote, fun $1 beß $2',
	'contribstats-contribution-range-breakdown' => 'Övverseech övver de Spende noh_m Wäät, fun $1 beß $2',
	'contribstats-currency-totals' => 'Jesamp en Jeld-Zoote. för et Jeschäffsjohr $1',
	'contribstats-contribution-breakdown' => 'Spendeövverssech noh_m Wäät, för et Jeschäffsjohr $1',
	'contribstats-day' => 'Daach',
	'contribstats-month' => 'Moohnd',
	'contribstats-currency' => 'Jeld-Zoot',
	'contribstats-amount' => 'Bedraach (en Dollar us de USA)',
	'contribstats-contributions' => 'Spende-Beidrääch',
	'contribstats-total' => 'Zosamme (en Dollar us de USA)',
	'contribstats-avg' => 'Schnett (en Dollar us de USA)',
	'contribstats-min' => 'Kleinste Spend (en Dollar us de USA)',
	'contribstats-max' => 'Deckste Spend (en Dollar us de USA)',
	'contribstats-percentage-ytd' => 'Prozent (zigg_et Johr aanjefange hät)',
	'contribstats-total-ytd' => 'Jesamp (zigg_et Johr aanjefange hät)',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'contributionreporting-desc' => 'Live-Reportage vun der Wikimedia-Spendenaktioun',
	'contributiontotal' => 'Total vun den Donen',
	'contributionhistory' => 'Evolutioun vun den Donen',
	'contrib-hist-header' => 'Bemierkungen vun Donateuren aus der ganzer Welt (real-time)',
	'contrib-hist-name' => 'Numm',
	'contrib-hist-date' => 'Zäit an Datum',
	'contrib-hist-amount' => 'Héicht vum Don',
	'contrib-hist-next' => 'Méi al Donen',
	'contrib-hist-previous' => 'Méi nei Donen',
	'contrib-hist-anonymous' => 'Anonym',
	'contributionstatistics' => 'Statistik vun den Donen',
	'contribstats-desc' => "Weist d'Statistike vun den Donen déi un d'WikiMedia Foundation gemaach gouen",
	'contribstats-daily-totals' => '{{PLURAL:$1|Total fir haut|Total pro Dag vun de läschte(n) $1 Deeg}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Total fir dëse Mount|Totaler pro Mount fir déi lescht $1 Méint}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Total fir $1 Mount|Total pro Mount fir $1 Méint}}',
	'contribstats-currency-range-totals' => 'Währungen Total (vum $1 bis de(n) $2)',
	'contribstats-contribution-range-breakdown' => 'Detail vun den Donen nom Betrag (vun $1 bis $2)',
	'contribstats-currency-totals' => "Gesamtbetraag fir d'Steierjoer $1",
	'contribstats-day' => 'Dag',
	'contribstats-month' => 'Mount',
	'contribstats-currency' => 'Währung',
	'contribstats-amount' => 'Betrag (USD)',
	'contribstats-contributions' => 'Donen',
	'contribstats-total' => 'Total (USD)',
	'contribstats-avg' => 'Duerchschnëtt (USD)',
	'contribstats-min' => 'Minimum (USD)',
	'contribstats-max' => 'Maximum (USD)',
	'contribstats-percentage-ytd' => 'Prozentsaatz (YTD)',
	'contribstats-total-ytd' => 'Total (YTD)',
);

/** Malay (Bahasa Melayu)
 * @author Aviator
 */
$messages['ms'] = array(
	'contributionreporting-desc' => 'Laporan langsung kempen dana Wikimedia',
	'contributiontotal' => 'Jumlah sumbangan',
	'contributionhistory' => 'Sejarah sumbangan',
	'contrib-hist-header' => 'Ulasan penderma dari seluruh dunia',
	'contrib-hist-name' => 'Nama',
	'contrib-hist-date' => 'Waktu dan tarikh',
	'contrib-hist-amount' => 'Jumlah',
	'contrib-hist-next' => 'Derma sebelumnya',
	'contrib-hist-previous' => 'Derma berikutnya',
	'contrib-hist-anonymous' => 'Tanpa nama',
	'contributionstatistics' => 'Statistik sumbangan',
	'contribstats-desc' => 'Statistik harian sumbangan wang yang dihulurkan kepada Yayasan Wikimedia',
	'contribstats-daily-totals' => '{{PLURAL:$1|Jumlah pada hari ini|Jumlah harian sejak $1 hari yang lalu}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Jumlah pada bulan ini|Jumlah bulanan sejak $1 bulan yang lalu}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Jumlah bagi $1 bulan|Jumlah bulanan bagi $1 bulan}}',
	'contribstats-currency-totals' => 'Jumlah mata wang bagi tahun kewangan $1',
	'contribstats-contribution-breakdown' => 'Pecahan mengikut nilai bagi tahun kewangan $1',
	'contribstats-day' => 'Hari',
	'contribstats-month' => 'Bulan',
	'contribstats-currency' => 'Mata wang',
	'contribstats-amount' => 'Amaun (USD)',
	'contribstats-contributions' => 'Sumbangan',
	'contribstats-total' => 'Jumlah (USD)',
	'contribstats-avg' => 'Purata (USD)',
	'contribstats-min' => 'Minimum (USD)',
	'contribstats-max' => 'Maksimum (USD)',
	'contribstats-percentage-ytd' => 'Peratusan (YTD)',
	'contribstats-total-ytd' => 'Jumlah (YTD)',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'contributionreporting-desc' => 'Liverapportage voor de fondswervingsactie van Wikimedia',
	'contributiontotal' => 'Totaal donaties',
	'contributionhistory' => 'Giftenhistorie',
	'contrib-hist-header' => 'Opmerkingen van donoren vanuit de hele wereld',
	'contrib-hist-name' => 'Naam',
	'contrib-hist-date' => 'Datum en tijd',
	'contrib-hist-amount' => 'Bedrag',
	'contrib-hist-next' => 'Eerdere donaties',
	'contrib-hist-previous' => 'Latere donaties',
	'contrib-hist-anonymous' => 'Anoniem',
	'contributionstatistics' => 'Donatiestatistieken',
	'contribstats-desc' => 'Statistieken weergeven voor donaties aan de Wikimedia Foundation',
	'contribstats-daily-totals' => '{{PLURAL:$1|Totaal voor vandaag|Dagelijkse totalen voor de afgelopen $1 dagen}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Totaal voor deze maand|Maandelijkse totalen voor de afgelopen $1 maanden}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Totaal voor $1 maand|Maandelijkse totalen voor $1 maanden}}',
	'contribstats-currency-range-totals' => 'Totalen munteenheden (van $1 naar $2)',
	'contribstats-contribution-range-breakdown' => 'Details van donaties (van $1 naar $2)',
	'contribstats-currency-totals' => 'Totalen munteenheden voor het fiscale jaar van $1',
	'contribstats-contribution-breakdown' => 'Details van donaties voor het fiscale jaar $1',
	'contribstats-day' => 'Dag',
	'contribstats-month' => 'Maand',
	'contribstats-currency' => 'Munteenheid',
	'contribstats-amount' => 'Bedrag (in USD)',
	'contribstats-contributions' => 'Donaties',
	'contribstats-total' => 'Totaal (in USD)',
	'contribstats-avg' => 'Gemiddeld (in USD)',
	'contribstats-min' => 'Minimaal (in USD)',
	'contribstats-max' => 'Maximaal (in USD)',
	'contribstats-percentage-ytd' => 'Percentage (dit jaar)',
	'contribstats-total-ytd' => 'Totaal (dit jaar)',
	'fundraiserstatistics' => 'Fondswervingstatistieken',
	'fundraiserstats-date' => 'Datum',
	'fundraiserstats-total' => 'Totaal (in USD)',
	'fundraiserstats-avg' => 'Gemiddeld (in USD)',
	'fundraiserstats-min' => 'Minimum (in USD)',
	'fundraiserstats-max' => 'Maximum (in USD)',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'contributionreporting-desc' => 'Rapòrt en dirècte a prepaus de la collècta de fonses de Wikimèdia',
	'contributiontotal' => 'Contribucions totalas',
	'contributionhistory' => 'Istoric de las contribucions',
	'contrib-hist-header' => 'Comentaris en dirècte dels donators a travèrs lo mond',
	'contrib-hist-name' => 'Nom',
	'contrib-hist-date' => 'Data e ora',
	'contrib-hist-amount' => 'Quantitat',
	'contrib-hist-next' => 'Dons mai ancians',
	'contrib-hist-previous' => 'Dons mai recents',
	'contrib-hist-anonymous' => 'Anonims',
	'contributionstatistics' => 'Estatisticas de contribucions',
	'contribstats-desc' => 'Afichar las estatisticas de las contribucions fachas a la Wikimedia Foundation',
	'contribstats-daily-totals' => '{{PLURAL:$1|Total per uèi|Totals jornalièrs pels darrièrs $1 jorns}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Total per aqueste mes|Totals mesadièrs pels darrièrs $1 meses}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Total per $1 mes|Totals mesadièrs per $1 meses}}',
	'contribstats-currency-range-totals' => 'Total (de $1 a $2)',
	'contribstats-contribution-range-breakdown' => 'Detalh de las contribucions per montant (de $1 a $2)',
	'contribstats-currency-totals' => "Total per l'annada fiscala $1",
	'contribstats-contribution-breakdown' => "Detalh de las contribucions per montant per l'annada fiscala $1",
	'contribstats-day' => 'Jorn',
	'contribstats-month' => 'Mes',
	'contribstats-currency' => 'Devisa',
	'contribstats-amount' => 'Montant (dolars US)',
	'contribstats-contributions' => 'Contribucions',
	'contribstats-total' => 'Total (dolars US)',
	'contribstats-avg' => 'Mejana (dolars US)',
	'contribstats-min' => 'Minimum (dolars US)',
	'contribstats-max' => 'Maximum (dolars US)',
	'contribstats-percentage-ytd' => 'Percentatge (ongan)',
	'contribstats-total-ytd' => 'Total (ongan)',
);

/** Polish (Polski)
 * @author Leinad
 * @author Maikking
 * @author Qblik
 */
$messages['pl'] = array(
	'contributionreporting-desc' => 'Bieżące sprawozdanie na temat zebranych funduszy dla Wikimedia',
	'contributiontotal' => 'Łączna suma darowizn',
	'contributionhistory' => 'Historia darowizn',
	'contrib-hist-header' => 'Bieżąca lista wpłat od darczyńców z całego świata',
	'contrib-hist-name' => 'Nazwa',
	'contrib-hist-date' => 'Godzina i data',
	'contrib-hist-amount' => 'Kwota',
	'contrib-hist-next' => 'Wcześniejsze wpłaty',
	'contrib-hist-previous' => 'Nowsze wpłaty',
	'contrib-hist-anonymous' => 'Anonimowy',
	'contributionstatistics' => 'Statystyki darowizn',
	'contribstats-desc' => 'Wyświetla statystyki darowizn na rzecz Fundacji Wikimedia',
	'contribstats-daily-totals' => '{{PLURAL:$1|Dzisiejsza suma|Dzienna suma w ostatnich $1 dniach}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Suma w tym miesiącu|Miesięczna suma w ostatnich $1 miesiącach}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Suma za $1 miesiąc|Miesięczne sumy w ostatnich $1 miesiącach}}',
	'contribstats-currency-range-totals' => 'Suma walut (od $1 do $2)',
	'contribstats-contribution-range-breakdown' => 'Rozkład darowizn według wartości (od $1 do $2)',
	'contribstats-currency-totals' => 'Sumy według walut w roku fiskalnym $1',
	'contribstats-contribution-breakdown' => 'Rozkład darowizn według wartości w roku fiskalnym $1',
	'contribstats-day' => 'Dzień',
	'contribstats-month' => 'Miesiąc',
	'contribstats-currency' => 'Waluta',
	'contribstats-amount' => 'Kwota (USD)',
	'contribstats-contributions' => 'Darowizny',
	'contribstats-total' => 'Suma (USD)',
	'contribstats-avg' => 'Średnia (USD)',
	'contribstats-min' => 'Minimum (USD)',
	'contribstats-max' => 'Maksimum (USD)',
	'contribstats-percentage-ytd' => 'Procent (od początku roku)',
	'contribstats-total-ytd' => 'Suma (od początku roku)',
	'fundraiserstatistics' => 'Statystyka zebranych funduszy',
	'fundraiserstats-date' => 'Data',
	'fundraiserstats-total' => 'Suma (USD)',
	'fundraiserstats-avg' => 'Średnia (USD)',
	'fundraiserstats-min' => 'Minimum (USD)',
	'fundraiserstats-max' => 'Maksimum (USD)',
);

/** Portuguese (Português)
 * @author 555
 * @author Malafaya
 */
$messages['pt'] = array(
	'contributiontotal' => 'Total de contribuições',
	'contributionhistory' => 'Histórico de contribuições',
	'contrib-hist-header' => 'Comentários em tempo real de doadores de todo o mundo',
	'contrib-hist-name' => 'Nome',
	'contrib-hist-date' => 'Hora e data',
	'contrib-hist-amount' => 'Quantia',
	'contrib-hist-anonymous' => 'Anónimo',
	'contributionstatistics' => 'Estatísticas de contribuições',
	'contribstats-desc' => 'Apresenta estatísticas das contribuições feitas à Fundação Wikimedia',
	'contribstats-daily-totals' => '{{PLURAL:$1|Total de hoje|Totais diários dos passados $1 dias}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Total deste mês|Totais mensais dos passados $1 meses}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Total de $1 mês|Totais mensais de $1 meses}}',
	'contribstats-currency-range-totals' => 'Totais por divisa (de $1 a $2)',
	'contribstats-contribution-range-breakdown' => 'Separação das contribuições por valor (de $1 a $2)',
	'contribstats-day' => 'Dia',
	'contribstats-month' => 'Mês',
	'contribstats-currency' => 'Divisa',
	'contribstats-amount' => 'Montante (USD)',
	'contribstats-contributions' => 'Contribuições',
	'contribstats-total' => 'Total (USD)',
	'contribstats-avg' => 'Média (USD)',
	'contribstats-min' => 'Mínimo (USD)',
	'contribstats-max' => 'Máximo (USD)',
	'contribstats-percentage-ytd' => 'Percentagem (do início do ano até hoje)',
	'contribstats-total-ytd' => 'Total (do início do ano até hoje)',
);

/** Russian (Русский)
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'contributionreporting-desc' => 'Текущие сведения о сборе пожертвований в пользу Викимедии',
	'contributiontotal' => 'Всего пожертвований',
	'contributionhistory' => 'История пожертвований',
	'contrib-hist-header' => 'Комментарии жертвователей со всего мира в реальном времени',
	'contrib-hist-name' => 'Имя',
	'contrib-hist-date' => 'Время и дата',
	'contrib-hist-amount' => 'Сумма',
	'contrib-hist-next' => 'Более раннее пожертвования',
	'contrib-hist-previous' => 'Более поздние пожертвования',
	'contrib-hist-anonymous' => 'Аноним',
	'contributionstatistics' => 'Статистика пожертвований',
	'contribstats-desc' => 'Показывает статистику пожертвований в пользу Фонда Викимедиа',
	'contribstats-daily-totals' => '{{PLURAL:$1|Всего за последний $1 день|Всего за последние $1 дня|Всего за последние $1 дней}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Всего за этот месяц|Всего за последние несколько месяцев}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Всего за $1 месяц|Всего за $1 месяца|Всего за $1 месяцев}}',
	'contribstats-currency-range-totals' => 'Итого (с $1 по $2)',
	'contribstats-contribution-range-breakdown' => 'Распределение размеров пожертвований (с $1 по $2)',
	'contribstats-currency-totals' => 'Итого для $1 финансового года',
	'contribstats-contribution-breakdown' => 'Распределение размеров пожертвований за $1 финансовый год',
	'contribstats-day' => 'День',
	'contribstats-month' => 'Месяц',
	'contribstats-currency' => 'Валюта',
	'contribstats-amount' => 'Сумма (USD)',
	'contribstats-contributions' => 'Пожертвования',
	'contribstats-total' => 'Всего (USD)',
	'contribstats-avg' => 'Среднее (USD)',
	'contribstats-min' => 'Минимальное (USD)',
	'contribstats-max' => 'Максимальное (USD)',
	'contribstats-percentage-ytd' => 'Процент (с начала года)',
	'contribstats-total-ytd' => 'Всего (с начала года)',
	'fundraiserstatistics' => 'Статистика сбора средств',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'contributionreporting-desc' => 'Správa o priebehu fundraisingu nadácie Wikimedia',
	'contributiontotal' => 'Príspevky celkom',
	'contributionhistory' => 'História príspevkov',
	'contrib-hist-header' => 'Komentáre od prispievateľov z celého sveta v reálnom čase',
	'contrib-hist-name' => 'Meno',
	'contrib-hist-date' => 'Čas a dátum',
	'contrib-hist-amount' => 'Čiastka',
	'contrib-hist-next' => 'Skoršie príspevky',
	'contrib-hist-previous' => 'Novšie príspevky',
	'contrib-hist-anonymous' => 'Anonym',
	'contributionstatistics' => 'Štatistika príspevkov',
	'contribstats-desc' => 'Zobrazuje štatistiku príspevkov nadácii Wikimedia',
	'contribstats-daily-totals' => '{{PLURAL:$1|Súhrn za dnešok|Denné súhrny za posledné $1 dni|Denné súhrny za posledných $1 dní}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Súhrn za tento mesiac|Denné súhrny za posledné $1 mesiace|Denné súhrny za posledných $1 mesiacov}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Súhrn za $1 mesiac|Denné súhrny za $1 mesiace|Denné súhrny za $1 mesiacov}}',
	'contribstats-currency-range-totals' => 'Súhrny v menách (od $1 do $2)',
	'contribstats-contribution-range-breakdown' => 'Rozdelenie príspevkov podľa hodnoty (od $1 do $2)',
	'contribstats-currency-totals' => 'Celkové sumy v menách za fiškálny rok $1',
	'contribstats-contribution-breakdown' => 'Rozdelenie príspevkov podľa hodnoty za fiškálny rok $1',
	'contribstats-day' => 'Deň',
	'contribstats-month' => 'Mesiac',
	'contribstats-currency' => 'Mena',
	'contribstats-amount' => 'Čiastka (USD)',
	'contribstats-contributions' => 'Príspevky',
	'contribstats-total' => 'Celkom (USD)',
	'contribstats-avg' => 'Priemer (USD)',
	'contribstats-min' => 'Minimum (USD)',
	'contribstats-max' => 'Maximum (USD)',
	'contribstats-percentage-ytd' => 'Percentá (YTD)',
	'contribstats-total-ytd' => 'Celkom (YTD)',
);

/** Swedish (Svenska)
 * @author Boivie
 * @author Najami
 */
$messages['sv'] = array(
	'contributionreporting-desc' => 'Liverapportering av Wikimedias insamling',
	'contributiontotal' => 'Totalt bidrag',
	'contributionhistory' => 'Bidragshistorik',
	'contrib-hist-header' => 'Realtids-kommentarer från bidragsgivare världen runt',
	'contrib-hist-name' => 'Namn',
	'contrib-hist-date' => 'Tid och datum',
	'contrib-hist-amount' => 'Belopp',
	'contrib-hist-next' => 'Tidigare donationer',
	'contrib-hist-previous' => 'Nyare donationer',
	'contrib-hist-anonymous' => 'Anonym',
	'contributionstatistics' => 'Bidragstatistik',
	'contribstats-desc' => 'Visar statistik för lämnade bidrag till Wikimedia Foundation',
	'contribstats-daily-totals' => '{{PLURAL:$1|Totalt idag|Dygnstotaler för de senaste $1 dagarna}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Totalt den här månaden|Månadstotaler för de senaste $1 månaderna}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Totalt för $1 månad|Månadstotaler för $1 månader}}',
	'contribstats-currency-range-totals' => 'Valutatotaler (från $1 till $2)',
	'contribstats-contribution-range-breakdown' => 'Bidrag ordnade efter värde (från $1 till $2)',
	'contribstats-currency-totals' => 'Valutatotaler för bokföringsåret $1',
	'contribstats-contribution-breakdown' => 'Bidrag ordnade efter värde i bokföringsåret $1',
	'contribstats-day' => 'Dag',
	'contribstats-month' => 'Månad',
	'contribstats-currency' => 'Valuta',
	'contribstats-amount' => 'Belopp (USD)',
	'contribstats-contributions' => 'Bidrag',
	'contribstats-total' => 'Totalt (USD)',
	'contribstats-avg' => 'Genomsnitt (USD)',
	'contribstats-min' => 'Minimum (USD)',
	'contribstats-max' => 'Maximum (USD)',
	'contribstats-percentage-ytd' => 'Procentandel (hittills i år)',
	'contribstats-total-ytd' => 'Totalt (hittills i år)',
	'fundraiserstatistics' => 'Insamlingsstatistik',
	'fundraiserstats-date' => 'Datum',
	'fundraiserstats-total' => 'Totalt (USD)',
	'fundraiserstats-avg' => 'Genomsnitt (USD)',
	'fundraiserstats-min' => 'Minimum (USD)',
	'fundraiserstats-max' => 'Maximum (USD)',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'contributiontotal' => 'Contributi totali',
	'contributionhistory' => 'Storia dei contributi',
	'contrib-hist-header' => 'Comenti in tenpo reàle dai donatori de tuto el mondo',
	'contrib-hist-name' => 'Nome',
	'contrib-hist-date' => 'Ora e data',
	'contrib-hist-amount' => 'Inporto',
	'contrib-hist-anonymous' => 'Anonimo',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'contributionreporting-desc' => 'Báo cáo tức thời về cuộc vận động gây quỹ Wikimedia',
	'contributiontotal' => 'Tổng số quyên góp',
	'contributionhistory' => 'Lịch sử quyên góp',
	'contrib-hist-header' => 'Danh sách tức thời các lời ghi từ khắp thế giới',
	'contrib-hist-name' => 'Tên',
	'contrib-hist-date' => 'Ngày giờ',
	'contrib-hist-amount' => 'Số tiền',
	'contrib-hist-next' => 'Khoản quyên góp trước',
	'contrib-hist-previous' => 'Khoản quyên góp sau',
	'contrib-hist-anonymous' => 'Vô danh',
	'contributionstatistics' => 'Thống kê quyên góp',
	'contribstats-desc' => 'Hiển thị thống kê về các khoản quyên góp cho Quỹ hỗ trợ Wikimedia',
	'contribstats-daily-totals' => '{{PLURAL:$1|Tổng cộng hôm nay|Tổng cộng hàng ngày cho $1 ngày trước đây}}',
	'contribstats-monthly-totals' => '{{PLURAL:$1|Tổng cộng tháng này|Tổng cộng hàng tháng cho $1 tháng trước đây}}',
	'contribstats-month-range-totals' => '{{PLURAL:$1|Tổng cộng $1 tháng|Tổng cộng hàng tháng cho $1 tháng}}',
	'contribstats-currency-range-totals' => 'Tổng cộng theo đơn vị tiền tệ (từ $1 đến $2)',
	'contribstats-contribution-range-breakdown' => 'Các khoản quyên góp theo số tiền (từ $1 đến $2)',
	'contribstats-currency-totals' => 'Tổng cộng theo đơn vị tiền tệ vào năm tài chính $1',
	'contribstats-contribution-breakdown' => 'Các khoản quyên góp theo số tiền vào năm tài chính $1',
	'contribstats-day' => 'Ngày',
	'contribstats-month' => 'Tháng',
	'contribstats-currency' => 'Đơn vị tiền tệ',
	'contribstats-amount' => 'Số tiền (USD)',
	'contribstats-contributions' => 'Khoản quyên góp',
	'contribstats-total' => 'Tổng cộng (USD)',
	'contribstats-avg' => 'Trung bình (USD)',
	'contribstats-min' => 'Tối thiểu (USD)',
	'contribstats-max' => 'Tối đa (USD)',
	'contribstats-percentage-ytd' => 'Phần trăm (YTD)',
	'contribstats-total-ytd' => 'Tổng cộng (YTD)',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'contribstats-day' => 'Del',
	'contribstats-month' => 'Mul',
	'contribstats-currency' => 'Völäd',
);

