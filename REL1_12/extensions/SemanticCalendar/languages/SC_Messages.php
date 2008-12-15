<?php
/**
 * Internationalization file for the Semantic Calendar extension
 *
 * @addtogroup Extensions
*/

$messages = array();

/** English
 * @author Yaron Koren
 */
$messages['en'] = array(
	// user messages
        'sc_previousmonth' => 'Previous month',
        'sc_nextmonth' => 'Next month',
	'sc_today' => 'Today',
	'sc_gotomonth' => 'Go to month',
	'sc_error_year2038' => 'Error: This system cannot handle dates after 2038, due to the [http://en.wikipedia.org/wiki/Year_2038_problem year 2038 problem]',
	'sc_error_beforeyear' => 'Error: This system cannot handle dates before $1',
);

/** Afrikaans (Afrikaans)
 * @author SPQRobin
 */
$messages['af'] = array(
	'sc_gotomonth' => 'Gaan na maand',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'sc_previousmonth'    => 'الشهر السابق',
	'sc_nextmonth'        => 'الشهر التالي',
	'sc_today'            => 'اليوم',
	'sc_gotomonth'        => 'اذهب إلى شهر',
	'sc_error_year2038'   => 'خطأ: هذا النظام لا يمكنه معالجة التواريخ بعد 2038، بسبب [http://en.wikipedia.org/wiki/Year_2038_problem مشكلة العام 2038]',
	'sc_error_beforeyear' => 'خطأ: هذا النظام لا يمكنه معالجة التواريخ قبل $1',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'sc_previousmonth'    => 'Предходен месец',
	'sc_nextmonth'        => 'Следващ месец',
	'sc_today'            => 'Днес',
	'sc_error_year2038'   => 'Грешка: Системата не може да оперира с дати след 2038 година заради [http://en.wikipedia.org/wiki/Year_2038_problem проблема 2038 година]',
	'sc_error_beforeyear' => 'Грешка: Системата не може да оперира с дати преди $1',
);

/** German (Deutsch)
 * @author Krabina
 */
$messages['de'] = array(
	'sc_previousmonth'    => 'Voriger Monat',
	'sc_nextmonth'        => 'Nächster Monat',
	'sc_today'            => 'Heute',
	'sc_gotomonth'        => 'Gehe zu Monat',
	'sc_error_year2038'   => 'Fehler: Das System kann aufgrund des [http://de.wikipedia.org/wiki/Jahr-2038-Problem Jahr-2038-Problems] Datumsangeben nach dem Jahr 2038 nicht verarbeiten.',
	'sc_error_beforeyear' => 'Fehler: Das System kann Datumsangaben vor $1 nicht verarbeiten.',
);

/** Persian (فارسی)
 * @author Tofighi
 */
$messages['fa'] = array(
	'sc_previousmonth'    => 'ماه گذشته',
	'sc_nextmonth'        => 'ماه آینده',
	'sc_today'            => 'امروز',
	'sc_gotomonth'        => 'برو به ماه',
	'sc_error_year2038'   => 'خطا: این سیستم نمی‌تواند تاریخ‌های بعد از 2038 را به علت [http://en.wikipedia.org/wiki/Year_2038_problem year 2038 problem] به‌کار برد.',
	'sc_error_beforeyear' => 'خطا: این سیستم نمی‌تواند تاریخ‌های قبل از $1 را استفاده کند',
);

/** French (Français)
 * @author Grondin
 * @author Urhixidur
 */
$messages['fr'] = array(
	'sc_previousmonth'    => 'Mois précédent',
	'sc_nextmonth'        => 'Mois suivant',
	'sc_today'            => "Aujourd'hui",
	'sc_gotomonth'        => 'Aller vers le mois',
	'sc_error_year2038'   => 'Erreur : ce système ne supporte pas les dates postérieures à 2038, à cause du [http://fr.wikipedia.org/wiki/Bogue_de_l%27an_2038 Bogue de l’an 2038]',
	'sc_error_beforeyear' => 'Erreur : ce système ne peut supporter les dates antérieures au $1.',
);

/** Galician (Galego)
 * @author Alma
 */
$messages['gl'] = array(
	'sc_previousmonth' => 'Mes anterior',
	'sc_nextmonth'     => 'Mes seguinte',
	'sc_today'         => 'Hoxe',
	'sc_gotomonth'     => 'Ir ao mes',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'sc_previousmonth'    => 'Předchadny měsac',
	'sc_nextmonth'        => 'Přichodny měsac',
	'sc_error_year2038'   => 'Zmylk: Tutón system njemóže [http://en.wikipedia.org/wiki/Year_2038_problem problema z lětom 2038] dla z datumami po lěće 2038 wobchadźeć.',
	'sc_error_beforeyear' => 'Zmylk: Tutón system njemóže z datumami do $1 wobchadźeć',
);

/** Hungarian (Magyar)
 * @author Bdanee
 */
$messages['hu'] = array(
	'sc_previousmonth'    => 'Előző hónap',
	'sc_nextmonth'        => 'Következő hónap',
	'sc_error_year2038'   => 'Hiba: A rendszer nem képes a 2038 utáni dátumokat kezelni, [http://en.wikipedia.org/wiki/Year_2038_problem 2038-as év problémája miatt]',
	'sc_error_beforeyear' => 'Hiba: a rendszer nem képes az $1 előtti dátumokat kezelni',
);

/** Japanese (日本語)
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'sc_previousmonth'    => '前の月',
	'sc_nextmonth'        => '次の月',
	'sc_today'            => '今日',
	'sc_gotomonth'        => 'この月を表示',
	'sc_error_year2038'   => 'エラー：このシステムは2038年以降を取り扱うことができません。詳しくは[http://ja.wikipedia.org/wiki/2038%E5%B9%B4%E5%95%8F%E9%A1%8C 2038年問題]をご覧ください。',
	'sc_error_beforeyear' => 'エラー：このシステムは$1以前を取り扱うことができません。',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'sc_previousmonth'    => 'Vireschte Mount',
	'sc_nextmonth'        => 'Nächste Mount',
	'sc_today'            => 'Haut',
	'sc_gotomonth'        => 'Géi op de Mount',
	'sc_error_year2038'   => 'Feeler: Dëse System ka wéint dem [http://de.wikipedia.org/wiki/Jahr-2038-Problem Joer-2038-Problem] Datumen nom Joer 2038 net verschaffen.',
	'sc_error_beforeyear' => 'Feeler: Dëse System kann net mat Datume virum $1 ëmgoen.',
);

/** Lithuanian (Lietuvių)
 * @author Hugo.arg
 */
$messages['lt'] = array(
	'sc_previousmonth'    => 'Praeitas mėnuo',
	'sc_nextmonth'        => 'Ateinantis mėnuo',
	'sc_today'            => 'Šiandien',
	'sc_gotomonth'        => 'Eiti į mėnesį',
	'sc_error_beforeyear' => 'Klaida: Ši sistema negali suprasti datų, ankstesnių nei $1',
);

/** Dutch (Nederlands)
 * @author Siebrand
 * @author GerardM
 */
$messages['nl'] = array(
	'sc_previousmonth'    => 'Vorige maand',
	'sc_nextmonth'        => 'Volgende maand',
	'sc_today'            => 'Vandaag',
	'sc_gotomonth'        => 'Ga naar maand',
	'sc_error_year2038'   => 'Fout: dit systeem kan geen datums verwerken na 2038 vanwege het [http://en.wikipedia.org/wiki/Year_2038_problem 2038-probleem]',
	'sc_error_beforeyear' => 'Fout: dit systeem kan geen datums verwerken voor $1',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Eirik
 */
$messages['nn'] = array(
	'sc_previousmonth'    => 'Førre månad',
	'sc_nextmonth'        => 'Neste månad',
	'sc_today'            => 'I dag',
	'sc_gotomonth'        => 'Gå til månad',
	'sc_error_year2038'   => 'Feil: Dette systemet taklar ikkje datoar etter 2038, grunna [http://en.wikipedia.org/wiki/Year_2038_problem år 2038-problemet].',
	'sc_error_beforeyear' => 'Feil: Dette systemet taklar ikkje datoar før $1.',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'sc_previousmonth'    => 'Forrige måned',
	'sc_nextmonth'        => 'Neste måned',
	'sc_today'            => 'I dag',
	'sc_gotomonth'        => 'Gå til måned',
	'sc_error_year2038'   => 'Feil: Systemet kan ikke behandle datoer etter 2038, på grunn av [http://en.wikipedia.org/wiki/Year_2038_problem År 2038-problemet]',
	'sc_error_beforeyear' => 'Feil: Systemet kan ikke behandle datoer før $1',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'sc_previousmonth'    => 'Mes precedent',
	'sc_nextmonth'        => 'Mes seguent',
	'sc_today'            => 'Uèi',
	'sc_gotomonth'        => 'Anar vèrs lo mes',
	'sc_error_year2038'   => "Error : aqueste sistèma supòrta pas las datas aprèp 2038, es degut al [http://fr.wikipedia.org/wiki/Bogue_de_l%27an_2038 Bòg de l'an 2038]",
	'sc_error_beforeyear' => 'Error : aqueste sistèma pòt pas suportar las datas anterioras al $1.',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'sc_previousmonth'    => 'Mês anterior',
	'sc_nextmonth'        => 'Mês seguinte',
	'sc_today'            => 'Hoje',
	'sc_error_year2038'   => 'Erro: Este sistema não consegue tratar datas posteriores a 2038 devido ao [http://pt.wikipedia.org/wiki/Problema_do_ano_2038 problema do ano 2038]',
	'sc_error_beforeyear' => 'Erro: Este sistema não consegue tratar datas anteriores a $1',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'sc_previousmonth'    => 'Predošlý mesiac',
	'sc_nextmonth'        => 'Ďalší mesiac',
	'sc_today'            => 'Dnes',
	'sc_gotomonth'        => 'Prejsť na mesiac',
	'sc_error_year2038'   => 'Chyba: Tento systém nedokáže pracovať s dátumami po roku 2038, kvôli [http://en.wikipedia.org/wiki/Year_2038_problem problému roku 2038]',
	'sc_error_beforeyear' => 'Chyba: Tento systém nedokáže pracovať s dátumami pred $1',
);

/** Swedish (Svenska)
 * @author M.M.S.
 */
$messages['sv'] = array(
	'sc_previousmonth'    => 'Föregående månad',
	'sc_nextmonth'        => 'Nästa månad',
	'sc_today'            => 'Idag',
	'sc_gotomonth'        => 'Gå till månad',
	'sc_error_year2038'   => 'Error: Detta system kan inte hantera datum efter år 2038, vederbörlig till [http://sv.wikipedia.org/wiki/År 2038-problemet år 2038 problemet]',
	'sc_error_beforeyear' => 'Error: Detta system kan inte hantera datum före $1',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'sc_previousmonth' => 'క్రితం నెల',
	'sc_nextmonth'     => 'తర్వాతి నెల',
	'sc_today'         => 'ఈరోజు',
	'sc_gotomonth'     => 'నెలకి వెళ్ళండి',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'sc_previousmonth' => 'Tháng trước',
	'sc_nextmonth'     => 'Tháng sau',
);

/** Volapük (Volapük)
 * @author Malafaya
 */
$messages['vo'] = array(
	'sc_previousmonth' => 'Mul büik',
	'sc_nextmonth'     => 'Mul sököl',
	'sc_today'         => 'Adelo',
);

/** ‪中文(台灣)‬ (‪中文(台灣)‬)
 * @author Roc michael
 */
$messages['zh-tw'] = array(
	'sc_previousmonth'    => '前一月',
	'sc_nextmonth'        => '次一月',
	'sc_today'            => '今日',
	'sc_gotomonth'        => '前往',
	'sc_error_year2038'   => '錯誤：此系統無法處理2038之後的日期：其原因為 [http://en.wikipedia.org/wiki/Year_2038_problem year 2038 problem]',
	'sc_error_beforeyear' => '錯誤：此系統無法處理 $1 之前的日期',
);

