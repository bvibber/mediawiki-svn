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
	'contribstats-contribution-range-average' => 'Average size of contributions under $1 USD (from $2 to $3)',
	'contribstats-contribution-range-breakdown' => 'Breakdown of contributions by value (from $1 to $2)',
	'contribstats-currency-totals' => 'Currency totals for the fiscal year of $1',
	'contribstats-contribution-average' => 'Average size of contributions under $1 USD for the fiscal year of $1',
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
);

/** Message documentation (Message documentation)
 * @author Darth Kule
 */
$messages['qqq'] = array(
	'contrib-hist-name' => '{{Identical|Name}}',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'contrib-hist-name' => 'Име',
	'contrib-hist-date' => 'Час и дата',
);

/** Czech (Česky)
 * @author Danny B.
 */
$messages['cs'] = array(
	'contributionreporting-desc' => 'Živé monitorování sbírky Wikimedia',
	'contributiontotal' => 'Celková suma darů',
	'contributionhistory' => 'Historie darů',
	'contrib-hist-header' => 'Komentáře dárců z celého světa v reálném čase',
	'contrib-hist-name' => 'Jméno',
	'contrib-hist-date' => 'Čas a datum',
	'contrib-hist-amount' => 'Výše',
	'contrib-hist-anonymous' => 'Anonym',
);

/** German (Deutsch) */
$messages['de'] = array(
	'contributionreporting-desc' => 'Live-Berichterstattung von der Wikimedia-Spendenkampagne',
	'contributiontotal' => 'Spenden insgesamt',
	'contributionhistory' => 'Spendenverlauf',
	'contrib-hist-header' => 'Spendenkommentare in Echtzeit von der ganzen Welt',
	'contrib-hist-name' => 'Name',
	'contrib-hist-date' => 'Zeit und Datum',
	'contrib-hist-amount' => 'Betrag',
	'contrib-hist-anonymous' => 'Anonym',
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
);

/** Persian (فارسی)
 * @author Komeil 4life
 */
$messages['fa'] = array(
	'contributionhistory' => 'تاریخچه مشارکت',
	'contrib-hist-name' => 'نام',
	'contrib-hist-date' => 'تاریخ و زمان',
	'contrib-hist-amount' => 'مبلغ',
);

/** Finnish (Suomi)
 * @author Silvonen
 */
$messages['fi'] = array(
	'contrib-hist-name' => 'Nimi',
);

/** French (Français)
 * @author Grondin
 * @author IAlex
 */
$messages['fr'] = array(
	'contributionreporting-desc' => 'Rapport en direct concernant la collecte de fonds de Wikimedia',
	'contributiontotal' => 'Contributions totales',
	'contributionhistory' => 'Historique des contributions',
	'contrib-hist-header' => 'Commentaires en direct des donateurs à travers le monde',
	'contrib-hist-name' => 'Nom',
	'contrib-hist-date' => 'Date et heure',
	'contrib-hist-amount' => 'Quantité',
	'contrib-hist-anonymous' => 'Anonymes',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'contrib-hist-name' => 'Nome',
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
	
	'contrib-hist-anonymous' => 'אנונימי',
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
	'contrib-hist-anonymous' => 'Anonyme',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'contrib-hist-name' => 'Nome',
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
	'contrib-hist-anonymous' => 'Namelos',
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
	'contrib-hist-anonymous' => 'Anonym',
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
	'contrib-hist-anonymous' => 'Anoniem',
);

/** Polish (Polski)
 * @author Leinad
 */
$messages['pl'] = array(
	'contributionreporting-desc' => 'Bieżące sprawozdanie na temat zebranych funduszy dla Wikimedia',
	'contributiontotal' => 'Łączna suma darowizn',
	'contributionhistory' => 'Historia darowizn',
	'contrib-hist-header' => 'Wpłaty darowizn z całego świata w czasie rzeczywistym',
	'contrib-hist-name' => 'Nazwa',
	'contrib-hist-date' => 'Godzina i data',
	'contrib-hist-amount' => 'Kwota',
	'contrib-hist-anonymous' => 'Anonimowy',
);

/** Portuguese (Português)
 * @author 555
 */
$messages['pt'] = array(
	'contributiontotal' => 'Total de contribuições',
	'contributionhistory' => 'Histórico de contribuições',
	'contrib-hist-header' => 'Comentários em tempo real de doadores de todo o mundo',
	'contrib-hist-name' => 'Nome',
	'contrib-hist-date' => 'Hora e data',
	'contrib-hist-amount' => 'Quantia',
	'contrib-hist-anonymous' => 'Anónimo',
);

/** Swedish (Svenska)
 * @author Boivie
 */
$messages['sv'] = array(
	'contributionreporting-desc' => 'Liverapportering av Wikimedias insamling',
	'contributiontotal' => 'Totalt bidrag',
	'contributionhistory' => 'Bidragshistorik',
	'contrib-hist-header' => 'Realtids-kommentarer från bidragsgivare världen runt',
	'contrib-hist-name' => 'Namn',
	'contrib-hist-date' => 'Tid och datum',
	'contrib-hist-amount' => 'Belopp',
	'contrib-hist-anonymous' => 'Anonym',
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
	'contrib-hist-anonymous' => 'Vô danh',
);

