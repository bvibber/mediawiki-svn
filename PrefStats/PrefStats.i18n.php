<?php
/**
 * Internationalisation for Usability Initiative PrefStats extension
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Roan Kattouw
 */
$messages['en'] = array(
	'prefstats' => 'Preference statistics',
	'prefstats-desc' => 'Track statistics about how many users have certain preferences enabled',
	'prefstats-title' => 'Preference statistics',
	'prefstats-list-intro' => 'Currently, the following preferences are being tracked.
Click on one to view statistics about it.',
	'prefstats-list-elem' => '$1 = $2',
	'prefstats-noprefs' => 'No preferences are currently being tracked.
Configure $wgPrefStatsTrackPrefs to track preferences.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|user has|users have}} enabled this preference since preference statistics were activated
** $2 {{PLURAL:$2|user still has|users still have}} it enabled
** $3 {{PLURAL:$3|user has|users have}} disabled it since',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|user has|users have}} enabled this preference since preference statistics were activated
** $2 {{PLURAL:$2|user still has|users still have}} it enabled
** $3 {{PLURAL:$3|user has|users have}} disabled it since
* In total, $4 {{PLURAL:$4|user has|users have}} this preference set',
	'prefstats-xaxis' => 'Duration (hours)',
	'prefstats-factors' => 'View per: $1',
	'prefstats-factor-hour' => 'hour',
	'prefstats-factor-sixhours' => 'six hours',
	'prefstats-factor-day' => 'day',
	'prefstats-factor-week' => 'week',
	'prefstats-factor-twoweeks' => 'two weeks',
	'prefstats-factor-fourweeks' => 'four weeks',
	'prefstats-factor-default' => 'back to default scale',
	'prefstats-legend-out' => 'Opted out',
	'prefstats-legend-in' => 'Opted in',
);

/** Message documentation (Message documentation)
 * @author Purodha
 * @author Siebrand
 */
$messages['qqq'] = array(
	'prefstats-factors' => '$1 is a list of values with a link each, and separated by {{msg-mw|pipe-separator}}.',
	'prefstats-factor-hour' => 'One hour. Used in {{msg-mw|prefstats-factors}} as part of the pipe separated list $1.',
	'prefstats-factor-day' => 'One day. Used in {{msg-mw|prefstats-factors}} as part of the pipe separated list $1.',
	'prefstats-factor-week' => 'One week. Used in {{msg-mw|prefstats-factors}} as part of the pipe separated list $1.',
	'prefstats-factor-twoweeks' => 'Used in {{msg-mw|prefstats-factors}} as part of the pipe separated list $1.',
	'prefstats-factor-fourweeks' => 'Used in {{msg-mw|prefstats-factors}} as part of the pipe separated list $1.',
	'prefstats-factor-default' => 'Used in {{msg-mw|prefstats-factors}} as part of the pipe separated list $1.',
);

/** Afrikaans (Afrikaans)
 * @author Naudefj
 */
$messages['af'] = array(
	'prefstats-xaxis' => 'Tydsduur (ure)',
	'prefstats-factors' => 'Wys per: $1',
	'prefstats-factor-hour' => 'uur',
	'prefstats-factor-day' => 'dag',
	'prefstats-factor-week' => 'week',
	'prefstats-factor-twoweeks' => 'twee weke',
	'prefstats-factor-fourweeks' => 'vier weke',
);

/** Arabic (العربية)
 * @author Meno25
 * @author Orango
 * @author OsamaK
 */
$messages['ar'] = array(
	'prefstats' => 'إحصاءات التفضيلات',
	'prefstats-desc' => 'تتبع الإحصاءات التي تظهر عدد المستخدمين الذين فعّلوا تفضيلات معينة.',
	'prefstats-title' => 'إحصاءات التفضيلات',
	'prefstats-list-intro' => 'يتم حاليًا تتبع التفضيلات التالية.
انقر على أحد التفضيلات لتظهر إحصاءات عنه.',
	'prefstats-noprefs' => 'لا توجد تفضيلات يتم تتبعها. اضبط $wgPrefStatsTrackPrefs لتتبع التفضيلات.',
	'prefstats-counters' => '* فعّل {{PLURAL:$1||مستخدم واحد|مستخدمان|$1 مستخدمين|$1 مستخدمًا|$1 مستخدم}} هذه التفضيلة منذ تنفعيل إحصاءات التفضيلات.
** فعّلها {{PLURAL:$2||مستخدم واحد|مستخدمان|$2 مستخدمين|$2 مستخدمًا|$2 مستخدم}}
** عطّلها {{PLURAL:$3||مستخدم واحد|مستخدمان|$3 مستخدمين|$3 مستخدمًا|$3 مستخدم}}',
	'prefstats-counters-expensive' => '* فعّل {{PLURAL:$1||مستخدم واحد|مستخدمان|$1 مستخدمين|$1 مستخدمًا|$1 مستخدم}} هذه التفضيلة منذ تنفعيل إحصاءات التفضيلات.
** فعّلها {{PLURAL:$2||مستخدم واحد|مستخدمان|$2 مستخدمين|$2 مستخدمًا|$2 مستخدم}}
** عطّلها {{PLURAL:$3||مستخدم واحد|مستخدمان|$3 مستخدمين|$3 مستخدمًا|$3 مستخدم}}
* في المحصلة، ضبط {{PLURAL:$4||مستخدم واحد|مستخدمان|$4 مستخدمين|$4 مستخدمًا|$4 مستخدم}} هذه التفضيلة',
	'prefstats-xaxis' => 'المدة (بالساعات)',
	'prefstats-factors' => 'عرض كل: $1',
	'prefstats-factor-hour' => 'ساعة',
	'prefstats-factor-sixhours' => 'ست ساعات',
	'prefstats-factor-day' => 'يوم',
	'prefstats-factor-week' => 'أسبوع',
	'prefstats-factor-twoweeks' => 'أسبوعين',
	'prefstats-factor-fourweeks' => 'أربعة أسابيع',
	'prefstats-factor-default' => 'عد إلى الجدول الإفتراضي',
	'prefstats-legend-out' => 'اختارت',
	'prefstats-legend-in' => 'مشترك',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'prefstats' => 'Статыстыка ўстановак удзельнікаў',
	'prefstats-desc' => 'Стварае статыстыку пра тое, як многа ўдзельнікаў выкарыстоўваюць ўстаноўкі',
	'prefstats-title' => 'Статыстыка ўстановак',
	'prefstats-list-intro' => 'Зараз адсочваюцца наступныя ўстаноўкі.
Націсьніце на адну зь іх для прагляду яе статыстыкі.',
	'prefstats-noprefs' => 'У цяперашні момант ніякія ўстаноўкі не адсочваюцца. Устанавіце $wgPrefStatsTrackPrefs для сачэньня за ўстаноўкамі.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|удзельнік уключыў|удзельнікі ўключылі|удзельнікаў уключылі}} гэтую магчымасьць з моманту актывізацыі гэтай статыстыкі
** У $2 {{PLURAL:$2|удзельніка|удзельнікаў|удзельнікаў}} яна уключаная
** У $3 {{PLURAL:$3|удзельніка|удзельнікаў|удзельнікаў}} яна выключаная',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|удзельнік уключыў|удзельнікі ўключылі|удзельнікаў уключылі}} гэтую магчымасьць з моманту актывізацыі гэтай статыстыкі
** У $2 {{PLURAL:$2|удзельніка|удзельнікаў|удзельнікаў}} яна уключаная
** У $3 {{PLURAL:$3|удзельніка|удзельнікаў|удзельнікаў}} яна выключаная
* Агулам $4 {{PLURAL:$4|удзельнік устанавіў|удзельнікі устанавілі|удзельнікаў устанавілі}} гэтую магчымасьць',
	'prefstats-xaxis' => 'Працягласьць (у гадзінах)',
	'prefstats-factors' => 'Адзінка шкалы часу: $1',
	'prefstats-factor-hour' => 'гадзіна',
	'prefstats-factor-day' => 'дзень',
	'prefstats-factor-week' => 'тыдзень',
	'prefstats-factor-twoweeks' => 'два тыдні',
	'prefstats-factor-fourweeks' => 'чатыры тыдні',
	'prefstats-factor-default' => 'вярнуцца да маштабу па змоўчваньні',
);

/** Bengali (বাংলা)
 * @author Bellayet
 */
$messages['bn'] = array(
	'prefstats' => 'পছন্দনীয় পরিসংখ্যান',
	'prefstats-title' => 'পছন্দনীয় পরিসংখ্যান',
	'prefstats-xaxis' => 'সময় (ঘন্টা)',
	'prefstats-factor-hour' => 'ঘন্টা',
	'prefstats-factor-sixhours' => 'ছয় ঘন্টা',
	'prefstats-factor-day' => 'দিন',
	'prefstats-factor-week' => 'সপ্তাহ',
	'prefstats-factor-twoweeks' => 'দুই সপ্তাহ',
	'prefstats-factor-fourweeks' => 'চার সপ্তাহ',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'prefstats' => 'Statistike postavki',
	'prefstats-desc' => 'Praćenje statistika o tome kako korisnici imaju postavljene određene postavke',
	'prefstats-title' => 'Statistike postavki',
	'prefstats-list-intro' => 'Trenutno, slijedeće postavke se prate.
Kliknite na jednu od njih da pogledate njene statistike.',
	'prefstats-noprefs' => 'Nijedna postavka se trenutno ne prati. Podesite $wgPrefStatsTrackPrefs za praćenje postavki.',
);

/** Catalan (Català)
 * @author Paucabot
 */
$messages['ca'] = array(
	'prefstats' => 'Estadístiques de les preferències',
	'prefstats-desc' => 'Registra les estadístiques de quants usuaris tenen certes preferències activades',
	'prefstats-title' => 'Estadístiques de les preferències',
	'prefstats-list-intro' => "Actualment s'estan registrant les següents preferències.
Cliqueu sobre una d'elles per veure'n les seves estadístiques.",
	'prefstats-noprefs' => 'No s\'està registrant cap preferència. Configurau $wgPrefStatsTrackPrefs per registrar les preferències.',
	'prefstats-xaxis' => 'Durada (hores)',
	'prefstats-factor-hour' => 'hora',
	'prefstats-factor-sixhours' => 'sis hores',
	'prefstats-factor-day' => 'dia',
	'prefstats-factor-week' => 'setmana',
	'prefstats-factor-twoweeks' => 'dues setmanes',
	'prefstats-factor-fourweeks' => 'quatre setmanes',
);

/** Czech (Česky)
 * @author Mormegil
 */
$messages['cs'] = array(
	'prefstats' => 'Statistika nastavení',
	'prefstats-desc' => 'Statistické sledování toho, kolik uživatelů používá která nastavení',
	'prefstats-title' => 'Statistika nastavení',
	'prefstats-list-intro' => 'V současnosti se sledují následující nastavení.
Kliknutím zobrazíte příslušné statistiky.',
	'prefstats-noprefs' => 'Momentálně se nesleduje žádné nastavení. Sledování musíte nakonfigurovat v $wgPrefStatsTrackPrefs.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|uživatel si aktivoval|uživatelé si aktivovali|uživatelů si aktivovalo}} tuto volbu od zavedení statistik.
** $2 {{PLURAL:$2|uživatel si ji zapnul|uživatelé si ji zapnuli|uživatelů si ji zapnulo}}
** $3 {{PLURAL:$3|uživatel si ji vypnul|uživatelé si ji vypnuli|uživatelů si ji vypnulo}}',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|uživatel si aktivoval|uživatelé si aktivovali|uživatelů si aktivovalo}} tuto volbu od zavedení statistik.
** $2 {{PLURAL:$2|uživatel si ji zapnul|uživatelé si ji zapnuli|uživatelů si ji zapnulo}}
** $3 {{PLURAL:$3|uživatel si ji vypnul|uživatelé si ji vypnuli|uživatelů si ji vypnulo}}
* Celkem {{PLURAL:$4|má|mají|má}} tuto volbu nastavenu $4 {{PLURAL:$4|uživatel|uživatelé|uživatelů}}',
	'prefstats-xaxis' => 'Doba (hodin)',
	'prefstats-factors' => 'Zobrazit po: $1',
	'prefstats-factor-hour' => 'hodinách',
	'prefstats-factor-sixhours' => 'šesti hodinách',
	'prefstats-factor-day' => 'dnech',
	'prefstats-factor-week' => 'týdnech',
	'prefstats-factor-twoweeks' => 'dvou týdnech',
	'prefstats-factor-fourweeks' => 'čtyřech týdnech',
	'prefstats-factor-default' => 'zpět na základní měřítko',
	'prefstats-legend-out' => 'Odhlášení',
	'prefstats-legend-in' => 'Přihlášení',
);

/** Church Slavic (Словѣ́ньскъ / ⰔⰎⰑⰂⰡⰐⰠⰔⰍⰟ)
 * @author Omnipaedista
 */
$messages['cu'] = array(
	'prefstats-factor-hour' => 'часъ',
	'prefstats-factor-day' => 'дьнь',
);

/** Danish (Dansk)
 * @author Byrial
 */
$messages['da'] = array(
	'prefstats' => 'Statistik over indstillinger',
	'prefstats-desc' => 'Statistik over antal brugere som har bestemte indstillinger',
	'prefstats-title' => 'Statistik over indstillinger',
	'prefstats-list-intro' => 'I øjeblikket bliver følgende indstillinger sporet.
Klik på en for at se statistik om den.',
	'prefstats-noprefs' => 'Ingen ingen indstillinger bliver sporet.
Konfigurer $wgPrefStatsTrackPrefs for at spore indstillinger.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|bruger|brugere}} har aktiveret denne indstilling siden sporingen blev startet
** $2 {{PLURAL:$2|bruger|brugere}} har den stadig aktiveret
** $3 {{PLURAL:$3|bruger|brugere}} har deaktiveret den igen',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|bruger|brugere}} har aktiveret denne indstilling siden sporingen blev startet
** $2 {{PLURAL:$2|bruger|brugere}} har den stadig aktiveret
** $3 {{PLURAL:$3|bruger|brugere}} har deaktiveret den igen
* I alt har $4 {{PLURAL:$4|bruger|brugere}} indstillingen aktiv',
	'prefstats-xaxis' => 'Varighed (timer)',
	'prefstats-factors' => 'Vis per: $1',
	'prefstats-factor-hour' => 'time',
	'prefstats-factor-sixhours' => 'seks timer',
	'prefstats-factor-day' => 'dag',
	'prefstats-factor-week' => 'uge',
	'prefstats-factor-twoweeks' => 'to uger',
	'prefstats-factor-fourweeks' => 'fire uger',
	'prefstats-factor-default' => 'tilbage til standardskalering',
	'prefstats-legend-out' => 'Fravalgt',
	'prefstats-legend-in' => 'Tilvalgt',
);

/** German (Deutsch)
 * @author MF-Warburg
 * @author Metalhead64
 * @author Omnipaedista
 * @author Pill
 */
$messages['de'] = array(
	'prefstats' => 'Einstellungsstatistiken',
	'prefstats-desc' => 'Statistiken, wie viele Benutzer bestimmte Einstellungen aktiviert haben',
	'prefstats-title' => 'Einstellungsstatistiken',
	'prefstats-list-intro' => 'Derzeit werden die folgenden Einstellungen aufgezeichnet.
Klicke auf eine, um Statistiken darüber zu erhalten.',
	'prefstats-noprefs' => 'Derzeit werden keine Einstellungen verfolgt. Konfiguriere $wgPrefStatsTrackPrefs, um Einstellungen zu verfolgen.',
	'prefstats-counters' => '* $1 Benutzer {{PLURAL:$1|hat|haben}} diese Einstellung aktiviert seit Statistiken über Einstellungen erhoben werden
** $2 Benutzer {{PLURAL:$2|hat|haben}} sie aktiviert
** $3 Benutzer {{PLURAL:$3|hat|haben}} sie deaktiviert',
	'prefstats-counters-expensive' => '* $1 Benutzer {{PLURAL:$1|hat|haben}} diese Einstellung aktiviert seit Statistiken über Einstellungen erhoben werden
** $2 Benutzer {{PLURAL:$2|hat|haben}} sie aktiviert
** $3 Benutzer {{PLURAL:$3|hat|haben}} sie deaktiviert
* Insgesamt {{PLURAL:$4|hat|haben}} $4 Benutzer diese Einstellung gesetzt',
	'prefstats-xaxis' => 'Dauer (Stunden)',
	'prefstats-factors' => 'Zugriffe pro: $1',
	'prefstats-factor-hour' => 'Stunde',
	'prefstats-factor-sixhours' => 'sechs Stunden',
	'prefstats-factor-day' => 'Tag',
	'prefstats-factor-week' => 'Woche',
	'prefstats-factor-twoweeks' => 'zwei Wochen',
	'prefstats-factor-fourweeks' => 'vier Wochen',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'prefstats' => 'Statistika nastajenjow',
	'prefstats-desc' => 'Statistice slědowaś, wjele wužywarjow jo wěste nastajenja zmóžniło',
	'prefstats-title' => 'Statistika nastajenjow',
	'prefstats-list-intro' => 'Tuchylu se slědujuce nastajenja slěduju.
Klikni na jadne z nich, aby se statistiku wó nim woglědał.',
	'prefstats-noprefs' => 'Tuchylu žedne nastajenja se slěduju. Konfigurěruj $wgPrefStatsTrackPrefs, aby nastajenja slědował.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|wužywaŕ jo|wužywarja stej|wužywarje su|wužywarjow jo}} toś to nastajenje {{PLURAL:$1|zmóžnił|zmóžniłej|zmóžnili|zmóžniło}}, wót togo, až statistika nastajenjow jo se aktiwěrowała
** $2 {{PLURAL:$2|wužywaŕ jo|wužywarja stej|wužywarje su|wužywarjow jo}} jo {{PLURAL:$1|zmóžnił|zmóžniłej|zmóžnili|zmóžniło}}
** $3 {{PLURAL:$2|wužywaŕ jo|wužywarja stej|wužywarje su|wužywarjow jo}} jo {{PLURAL:$1|znjemóžnił|znjemóžniłej|znjemóžnili|znjemóžniło}}',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|wužywaŕ jo|wužywarja stej|wužywarje su|wužywarjow jo}} toś to nastajenje {{PLURAL:$1|zmóžnił|zmóžniłej|zmóžnili|zmóžniło}}, wót togo, až statistika nastajenjow jo se aktiwěrowała
** $2 {{PLURAL:$2|wužywaŕ jo|wužywarja stej|wužywarje su|wužywarjow jo}} jo {{PLURAL:$2|zmóžnił|zmóžniłej|zmóžnili|zmóžniło}}
** $3 {{PLURAL:$2|wužywaŕ jo|wužywarja stej|wužywarje su|wužywarjow jo}} jo {{PLURAL:$3|znjemóžnił|znjemóžniłej|znjemóžnili|znjemóžniło}}
*Dogromady $4 {{PLURAL:$4|wužywaŕ jo|wužywarja stej|wužywarje su|wužywarjow jo}} toś to nastajenje {{PLURAL:$4|stajił|stajiłej|stajili|stajiło}}',
	'prefstats-xaxis' => 'Cas (goźiny)',
	'prefstats-factors' => 'Naglěd za: $1',
	'prefstats-factor-hour' => 'góźinu',
	'prefstats-factor-sixhours' => 'šesć góźinow',
	'prefstats-factor-day' => 'źeń',
	'prefstats-factor-week' => 'tyźeń',
	'prefstats-factor-twoweeks' => 'dwa tyźenja',
	'prefstats-factor-fourweeks' => 'styri tyźenje',
	'prefstats-factor-default' => 'slědk k standardnemu měritkoju',
	'prefstats-legend-out' => 'Wótzjawjony',
	'prefstats-legend-in' => 'Pśizjawjony',
);

/** Greek (Ελληνικά)
 * @author Omnipaedista
 * @author ZaDiak
 */
$messages['el'] = array(
	'prefstats' => 'Στατιστικά προτιμήσεων',
	'prefstats-desc' => 'Παρακολούθηση στατιστικών για το πόσοι χρήστες έχουν ενεργοποιημένες συγκεκριμένες προτιμήσεις',
	'prefstats-title' => 'Στατιστικά προτιμήσεων',
	'prefstats-list-intro' => 'Τώρα, οι παρακάτω προτιμήσεις παρακολουθούνται.
Κάντε "κλικ" σε μια για να δείτε τα στατιστικά για αυτή.',
	'prefstats-noprefs' => 'Αυτή τη στιγμή δεν παρακολουθούνται καθόλου προτιμήσεις.
Διαμορφώστε το $wgPrefStatsTrackPrefs για να παρακολουθήσετε τις προτιμήσεις.',
	'prefstats-xaxis' => 'Διάρκεια (ώρες)',
	'prefstats-factors' => 'Εμφάνιση ανά: $1',
	'prefstats-factor-hour' => 'ώρα',
	'prefstats-factor-sixhours' => 'έξι ώρες',
	'prefstats-factor-day' => 'ημέρα',
	'prefstats-factor-week' => 'εβδομάδα',
	'prefstats-factor-twoweeks' => 'δύο εβδομάδες',
	'prefstats-factor-fourweeks' => 'τέσσερις εβδομάδες',
	'prefstats-factor-default' => 'πίσω στην προεπιλεγμένη κλίμακα',
	'prefstats-legend-out' => 'Μη συμμετοχή',
	'prefstats-legend-in' => 'Συμμετοχή',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'prefstats' => 'Statistikoj pri preferoj',
	'prefstats-desc' => 'Atenti statistikojn pri kiom da uzantoj ŝaltas certajn agordojn',
	'prefstats-title' => 'Statistikoj pri preferoj',
	'prefstats-list-intro' => 'Nune, la jenaj agordoj estas atentitaj.
Klaku por vidi statistikojn pri ĝi.',
	'prefstats-noprefs' => 'Neniuj preferoj estas nune sekvita.
Konfiguru $wgPrefStatsTrackPrefs por sekvi preferojn.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|uzanto|uzantoj}} ŝaltis ĉi tiun preferon ekde statistikoj pri preferoj aktiviĝis
** $2 {{PLURAL:$2|uzanto|uzantoj}} ŝaltis ĝin
** $3 {{PLURAL:$3|uzanto|uzantoj}} malŝaltis ĝin',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|uzanto|uzantoj}} ŝaltis ĉi tiun preferon ekde statistikoj pri preferoj aktiviĝis
** $2 {{PLURAL:$2|uzanto|uzantoj}} ŝaltis ĝin
** $3 {{PLURAL:$3|uzanto|uzantoj}} malŝaltis ĝin
* Sume, $4 {{PLURAL:$4|uzanto|uzantoj}} uzas ĉi tiun preferon.',
	'prefstats-xaxis' => 'Daŭro (horoj)',
	'prefstats-factors' => 'Vidi laŭ: $1',
	'prefstats-factor-hour' => 'horo',
	'prefstats-factor-day' => 'tago',
	'prefstats-factor-week' => 'semajno',
	'prefstats-factor-twoweeks' => 'du semajnoj',
	'prefstats-factor-fourweeks' => 'kvar semajnoj',
	'prefstats-factor-default' => 'reuzi defaŭltan skalon',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Locos epraix
 * @author Omnipaedista
 */
$messages['es'] = array(
	'prefstats' => 'Estadísticas de preferencia',
	'prefstats-desc' => 'Seguimiento de las estadísticas sobre cuántos usuarios tienen ciertas preferencias habilitadas',
	'prefstats-title' => 'Estadísticas de preferencia',
	'prefstats-list-intro' => 'De momento, las siguientes preferencias están siendo seguidas.
Selecciona una para ver estadísticas acerca de ella.',
	'prefstats-xaxis' => 'Duración (horas)',
	'prefstats-factor-hour' => 'hora',
	'prefstats-factor-day' => 'día',
	'prefstats-factor-week' => 'semana',
);

/** Estonian (Eesti)
 * @author Avjoska
 * @author Pikne
 */
$messages['et'] = array(
	'prefstats-desc' => 'Kogub arvandmeid kindlate eelistuste kasutatavuse kohta.',
	'prefstats-xaxis' => 'Kestvus (tundides)',
	'prefstats-factor-hour' => 'tund',
	'prefstats-factor-day' => 'päev',
	'prefstats-factor-week' => 'nädal',
	'prefstats-factor-twoweeks' => 'kaks nädalat',
	'prefstats-factor-fourweeks' => 'neli nädalat',
);

/** Basque (Euskara)
 * @author An13sa
 */
$messages['eu'] = array(
	'prefstats' => 'Hobespen estatistikak',
	'prefstats-title' => 'Hobespen estatistikak',
	'prefstats-list-intro' => 'Une honetan, ondorengo hobespenak jarraitzen ari dira.
Klikatu batean bere estatistikak ikusteko.',
);

/** Finnish (Suomi)
 * @author Cimon Avaro
 * @author Crt
 * @author Str4nd
 */
$messages['fi'] = array(
	'prefstats' => 'Asetusten tilastot',
	'prefstats-desc' => 'Kerää tilastoja siitä, kuinka moni käyttäjä on ottanut käyttöön erinäiset asetukset.',
	'prefstats-title' => 'Asetusten tilastot',
	'prefstats-list-intro' => 'Tällä hetkellä seuraavia asetuksia seurataan.
Tilastot näkyvät painamalla asetusta.',
	'prefstats-noprefs' => 'Yhtään asetusta ei seurata tällä hetkellä.
Aseta $wgPrefStatsTrackPrefs asetusten seuraamiseksi.',
	'prefstats-xaxis' => 'Kesto (tuntia)',
	'prefstats-factor-hour' => 'tunti',
	'prefstats-factor-sixhours' => 'kuusi tuntia',
	'prefstats-factor-day' => 'päivä',
	'prefstats-factor-week' => 'viikko',
	'prefstats-factor-twoweeks' => 'kaksi viikkoa',
	'prefstats-factor-fourweeks' => 'neljä viikkoa',
	'prefstats-factor-default' => 'takaisin oletusmittakaavaan',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 * @author Kropotkine 113
 * @author Omnipaedista
 * @author Verdy p
 */
$messages['fr'] = array(
	'prefstats' => 'Statistiques des préférences',
	'prefstats-desc' => "Statistiques sur le nombre d'utilisateurs ayant certaines préférences activées",
	'prefstats-title' => 'Statistiques des préférences',
	'prefstats-list-intro' => "En ce moment, les préférences suivantes sont suivies.
Cliquez sur l'une d'entre elles pour voir les statistiques à son propos.",
	'prefstats-noprefs' => 'Aucune préférence n\'est actuellement suivie. Configurer $wgPrefStatsTrackPrefs pour suivre des préférences.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|utilisateur a|utilisateurs ont}} activé cette préférence depuis que les statistiques de préférences ont été activées
** $2 {{PLURAL:$2|utilisateur a|utilisateurs ont}} activé cette préférence
** $3 {{PLURAL:$3|utilisateur a|utilisateurs ont}} désactivé cette préférence',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|utilisateur a|utilisateurs ont}} activé cette préférence depuis que les statistiques de préférences ont été activées
** $2 {{PLURAL:$2|utilisateur a|utilisateurs ont}} activé cette préférence
** $3 {{PLURAL:$3|utilisateur a|utilisateurs ont}} désactivé cette préférence
* Au total, $4 {{PLURAL:$4|utilisateur a|utilisateurs ont}} défini cette préférence',
	'prefstats-xaxis' => 'Durée (heures)',
	'prefstats-factors' => 'Afficher par : $1',
	'prefstats-factor-hour' => 'heure',
	'prefstats-factor-sixhours' => 'six heures',
	'prefstats-factor-day' => 'jour',
	'prefstats-factor-week' => 'semaine',
	'prefstats-factor-twoweeks' => 'deux semaines',
	'prefstats-factor-fourweeks' => 'quatre semaines',
	'prefstats-factor-default' => "revenir à l'échelle par défaut",
);

/** Franco-Provençal (Arpetan)
 * @author Cedric31
 */
$messages['frp'] = array(
	'prefstats-factor-hour' => 'hora',
	'prefstats-factor-sixhours' => 'Siéx hores',
	'prefstats-factor-day' => 'jorn',
	'prefstats-factor-week' => 'semana',
	'prefstats-factor-twoweeks' => 'doux semanes',
	'prefstats-factor-fourweeks' => 'quatro semanes',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'prefstats' => 'Preferencia de estatísticas',
	'prefstats-desc' => 'Segue as estatísticas sobre cantos usuarios teñen determinadas preferencias activadas',
	'prefstats-title' => 'Estatísticas das preferencias',
	'prefstats-list-intro' => 'Actualmente as seguintes preferencias están sendo seguidas.
Prema sobre unha para ver as estatísticas sobre ela.',
	'prefstats-noprefs' => 'Actualmente non se segue preferencia algunha. Configure $wgPrefStatsTrackPrefs para seguir preferencias.',
);

/** Ancient Greek (Ἀρχαία ἑλληνικὴ)
 * @author Omnipaedista
 */
$messages['grc'] = array(
	'prefstats' => 'Στατιστικὰ προτιμήσεων',
	'prefstats-desc' => 'Παρακολουθεῖν τὰ στατιστικὰ περὶ τοῦ ἀριθμοῦ χρωμένων οἵπερ ἔχουσιν ἐνεργοποιημένας συγκεκριμένας προτιμήσεις',
	'prefstats-title' => 'Στατιστικὰ προτιμήσεων',
	'prefstats-xaxis' => 'Διάρκεια (ὧραι)',
	'prefstats-factors' => 'Προβάλλειν ἀνά: $1',
	'prefstats-factor-hour' => 'ὥρα',
	'prefstats-factor-sixhours' => 'ἓξ ὧραι',
	'prefstats-factor-day' => 'ἡμέρα',
	'prefstats-factor-week' => 'ἑβδομάς',
	'prefstats-factor-twoweeks' => 'δύο ἑβδομάδες',
	'prefstats-factor-fourweeks' => 'τέσσαρες ἑβδομάδες',
	'prefstats-factor-default' => 'ὀπίσω εἰς τὴν προεπειλεγμένην κλίμακα',
	'prefstats-legend-out' => 'Μὴ συμμετοχή',
	'prefstats-legend-in' => 'Συμμετοχή',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 * @author Purodha
 */
$messages['gsw'] = array(
	'prefstats' => 'Prioritäte-Statischtik',
	'prefstats-desc' => 'Statischtik wievil Benutzer di sichere Yystellige meglig gmacht hän',
	'prefstats-title' => 'Priorotätestatischtik',
	'prefstats-list-intro' => 'Zur Zyt wäre die Prioritäte verfolgt.
Druck uf eini go Statischtike iber si aaluege.',
	'prefstats-noprefs' => 'Bis jetz wäre kei Yystellige verfolgt. Konfigurier $wgPrefStatsTrackPrefs go Yystellige verfolge.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|Benutzer het|Benutzer hän}} die Yystellig megli gmacht syt d Yystelligsstatischtike aktiviert wore sin
** $2 {{PLURAL:$2|Benutzer het|Benutzer hän}} si megli gmacht
** $3 {{PLURAL:$3|Benutzer het|Benutzer hän}} si abgstellt',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|Benutzer het|Benutzer hän}} die Yystellig megli gmacht syt d Yystelligsstatischtike aktiviert wore sin
** $2 {{PLURAL:$2|Benutzer het|Benutzer hän}} si megli gmacht
** $3 {{PLURAL:$3|Benutzer het|Benutzer hän}} si abgstellt
* $4 {{PLURAL:$4|Benutzer het si insgsamt|Benutzer hän si insgsamt}} die Yystellig megli gmacht',
	'prefstats-xaxis' => 'Duur (Stunde)',
	'prefstats-factors' => 'Aaluege fir: $1',
	'prefstats-factor-hour' => 'Stund',
	'prefstats-factor-sixhours' => 'segs Stunde',
	'prefstats-factor-day' => 'Tag',
	'prefstats-factor-week' => 'Wuche',
	'prefstats-factor-twoweeks' => 'Zwo Wuche',
	'prefstats-factor-fourweeks' => 'Vier Wuche',
	'prefstats-factor-default' => 'Zruck zur dr Standardskala',
	'prefstats-legend-out' => 'Abgmäldet',
	'prefstats-legend-in' => 'Aagmäldet',
);

/** Hebrew (עברית)
 * @author Rotem Liss
 * @author YaronSh
 */
$messages['he'] = array(
	'prefstats' => 'סטטיסטיקת העדפות',
	'prefstats-desc' => 'בדיקה כמה משתמשים הפעילו העדפה מסוימת',
	'prefstats-title' => 'סטטיסטיקת העדפות',
	'prefstats-list-intro' => 'כרגע, ההעדפות הבאות נמצאות במעקב.
לחצו על אחת כדי לצפות בסטטיסטיקות אודותיה.',
	'prefstats-noprefs' => 'נכון לעכשיו לא מתבצע מעקב אחר העדפות. יש להגדיר את $wgPrefStatsTrackPrefs כדי לעקוב אחר העדפות.',
	'prefstats-counters' => '* {{PLURAL:$1|משתמש אחד|$1 משתמשים}} הפעילו העדפה זו מאז שהופעלו סטטיסטיקות ההעדפות
** {{PLURAL:$2|משתמש אחד|$2 משתמשים}} הפעילו אותה
** {{PLURAL:$3|משתמש אחד|$3 משתמשים}} ביטלו אותה',
	'prefstats-counters-expensive' => '* {{PLURAL:$1|משתמש אחד|$1 משתמשים}} הפעילו העדפה זו מאז שהופעלו סטטיסטיקות ההעדפות
** {{PLURAL:$2|משתמש אחד|$2 משתמשים}} הפעילו אותה
** {{PLURAL:$3|משתמש אחד|$3 משתמשים}} ביטלו אותה
* סך הכל, {{PLURAL:$4|משתמש אחד|$4 משתמשים}} הגדירו העדפה זו',
	'prefstats-xaxis' => 'משך (בשעות)',
	'prefstats-factor-hour' => 'שעה',
	'prefstats-factor-sixhours' => 'שש שעות',
	'prefstats-factor-day' => 'יום',
	'prefstats-factor-week' => 'שבוע',
	'prefstats-factor-twoweeks' => 'שבועיים',
	'prefstats-factor-fourweeks' => 'ארבעה שבועות',
	'prefstats-factor-default' => 'חזרה למימדי ברירת המחדל',
);

/** Croatian (Hrvatski)
 * @author Suradnik13
 */
$messages['hr'] = array(
	'prefstats' => 'Statistike postavki',
	'prefstats-desc' => 'Praćenje statistike o tome koliko suradnika ima omogućene određene postavke',
	'prefstats-title' => 'Statistike postavki',
	'prefstats-list-intro' => 'Trenutačno su sljedeće postavke praćene. 
Kliknite na jednu kako biste vidjeli njezinu statistiku.',
	'prefstats-noprefs' => 'Trenutačno se ne prati niti jedna postavka. Podesite $wgPrefStatsTrackPrefss za praćenje postavki.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|suradnik je omogućio|suradnika su omogućili}} ovu postavku od kada je aktivirana statistika postavki
** $2 {{PLURAL:$2|suradnik ju je omogućio|suradnika ju je omogućilo}}
** $3 {{PLURAL:$2|suradnik ju je onemogućio|suradnika ju je onemogućilo}}',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|suradnik je omogućio|suradnika su omogućili}} ovu postavku od kada je aktivirana statistika postavki
** $2 {{PLURAL:$2|suradnik ju je omogućio|suradnika ju je omogućilo}}
** $3 {{PLURAL:$2|suradnik ju je onemogućio|suradnika ju je onemogućilo}}
* Ukupno, $4 {{PLURAL:$4|suradnik je postavio|suradnika je postavilo}} ovu postavku',
	'prefstats-xaxis' => 'Trajanje (sati)',
	'prefstats-factors' => 'Pregled po: $1',
	'prefstats-factor-hour' => 'sat',
	'prefstats-factor-day' => 'dan',
	'prefstats-factor-week' => 'tjedan',
	'prefstats-factor-twoweeks' => 'dva tjedna',
	'prefstats-factor-fourweeks' => 'četiri tjedna',
	'prefstats-factor-default' => 'nazad na zadanu ljestvicu',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'prefstats' => 'Statistika nastajenjow',
	'prefstats-desc' => 'Statistika wo tym, kelko wužiwarjow je wěste nastajenja aktiwizowało',
	'prefstats-title' => 'Statistika nastajenjow',
	'prefstats-list-intro' => 'Tuchwilu so slědowace nastajenja sćěhuja. Klikń na jedne z nich, zo by sej statistiku wo nim wobhladał.',
	'prefstats-noprefs' => 'Tuchwilu so žane nastajenja njesćěhuja. Konfiguruj $wgPrefStatsTrackPrefs, zo by nastajenja sćěhował.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|wužiwar je|wužiwarjej staj|wužiwarjo su|wužiwarjow je}} tute nastajenje {{PLURAL:$1|zmóžnił|zmóžniłoj|zmóžnili|zmóžniło}}, wot toho, zo statistika nastajenjow je so aktiwizowała
** $2 {{PLURAL:$2|wužiwar je|wužiwarjej staj|wužiwarjo su|wužiwarjow je}} jo {{PLURAL:$1|zmóžnił|zmóžniłoj|zmóžnili|zmóžniło}}
** $3 {{PLURAL:$3|wužiwar je|wužiwarjej staj|wužiwarjo su|wužiwarjow je}} jo {{PLURAL:$1|znjemóžnił|znjemóžniłoj|znjemóžnili|znjemóžniło}}',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|wužiwar je|wužiwarjej staj|wužiwarjo su|wužiwarjow je}} tute nastajenje {{PLURAL:$1|zmóžnił|zmóžniłoj|zmóžnili|zmóžniło}}, wot toho, zo statistika nastajenjow je so aktiwizowała
** $2 {{PLURAL:$2|wužiwar je|wužiwarjej staj|wužiwarjo su|wužiwarjow je}} jo {{PLURAL:$1|zmóžnił|zmóžniłoj|zmóžnili|zmóžniło}}
** $3 {{PLURAL:$3|wužiwar je|wužiwarjej staj|wužiwarjo su|wužiwarjow je}} jo {{PLURAL:$1|znjemóžnił|znjemóžniłoj|znjemóžnili|znjemóžniło}}
Dohromady $4 {{PLURAL:$4|wužiwar je|wužiwarjej saj|wužiwarjo su|wužiwarjow je}} tute nastajenje {{PLURAL:$4|stajił|stajiłoj|stajili|stajiło}}',
	'prefstats-xaxis' => 'Traće (hodźiny)',
	'prefstats-factors' => 'Přehlad za: $1',
	'prefstats-factor-hour' => 'hodźinu',
	'prefstats-factor-sixhours' => 'šěsć hodźin',
	'prefstats-factor-day' => 'dźeń',
	'prefstats-factor-week' => 'tydźeń',
	'prefstats-factor-twoweeks' => 'njedźeli',
	'prefstats-factor-fourweeks' => 'štyri njedźele',
	'prefstats-factor-default' => 'wróćo k standardnemu měritku',
	'prefstats-legend-out' => 'Wotzjewjeny',
	'prefstats-legend-in' => 'Přizjewjeny',
);

/** Hungarian (Magyar)
 * @author Dani
 * @author Tgr
 */
$messages['hu'] = array(
	'prefstats' => 'Beállítás-statisztikák',
	'prefstats-desc' => 'Statisztikák készítése arról, hány felhasználó kapcsolt be bizonyos beállításokat',
	'prefstats-title' => 'Beállítás-statisztikák',
	'prefstats-list-intro' => 'Jelenleg az alábbi beállításokról készül statisztika.
Kattints rá valamelyikre a róla gyűjtött adatok megtekintéséhez.',
	'prefstats-noprefs' => 'A beállítások nyomkövetése inaktív. Állítsd be megfelelően a $wgPrefStatsTrackPrefs értékét a beállítások követéséhez.',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'prefstats' => 'Statisticas de preferentias',
	'prefstats-desc' => 'Statistics super le numero de usatores que ha activate certe preferentias',
	'prefstats-title' => 'Statisticas de preferentias',
	'prefstats-list-intro' => 'Actualmente, le sequente preferentias es sequite.
Clicca super un pro vider statisticas super illo.',
	'prefstats-noprefs' => 'Nulle preferentia es ora sequite. Configura $wgPrefStatsTrackPrefs pro sequer preferentias.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|usator|usatores}} ha activate iste preferentia depost le comenciamento del statisticas de preferentias
** $2 {{PLURAL:$2|usator|usatores}} lo ha activate
** $3 {{PLURAL:$3|usator|usatores}} lo ha disactivate',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|usator|usatores}} ha activate iste preferentia depost le comenciamento del statisticas de preferentias
** $2 {{PLURAL:$2|usator|usatores}} lo ha activate
** $3 {{PLURAL:$3|usator|usatores}} lo ha disactivate
* In total, iste preferentia es active pro $4 {{PLURAL:$4|usator|usatores}}',
);

/** Indonesian (Bahasa Indonesia)
 * @author Bennylin
 * @author Kandar
 * @author Rex
 */
$messages['id'] = array(
	'prefstats' => 'Statistik preferensi',
	'prefstats-desc' => 'Statistik mengenai berapa banyak pengguna yang mengaktifkan preferensi tertentu',
	'prefstats-title' => 'Statistik preferensi',
	'prefstats-list-intro' => 'Saat ini, preferensi-preferensi berikut sedang ditelusuri.
Klik pada salah satu untuk melihat statistiknya.',
	'prefstats-noprefs' => 'Tidak ada preferensi yang sedang ditelusuri. Konfigurasikan $wgPrefStatsTrackPrefs untuk menelusuri preferensi.',
	'prefstats-xaxis' => 'Durasi (jam)',
	'prefstats-factor-hour' => 'jam',
	'prefstats-factor-day' => 'hari',
	'prefstats-factor-week' => 'pekan',
	'prefstats-factor-twoweeks' => 'dua pekan',
	'prefstats-factor-fourweeks' => 'empat pekan',
);

/** Italian (Italiano)
 * @author Darth Kule
 * @author Melos
 */
$messages['it'] = array(
	'prefstats' => 'Statistiche delle preferenze',
	'prefstats-desc' => 'Statistiche circa il numero di utenti che hanno attivato alcune preferenze',
	'prefstats-title' => 'Statistiche delle preferenze',
	'prefstats-list-intro' => 'Attualmente, le seguenti preferenze vengono seguite.
Fare clic su una per vedere le statistiche su di essa.',
	'prefstats-xaxis' => 'Durata (ore)',
	'prefstats-factor-hour' => 'ora',
	'prefstats-factor-sixhours' => 'sei ore',
	'prefstats-factor-day' => 'giorno',
	'prefstats-factor-week' => 'settimana',
	'prefstats-factor-twoweeks' => 'due settimane',
	'prefstats-factor-fourweeks' => 'quattro settimane',
	'prefstats-factor-default' => 'ritorna alla scala predefinita',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'prefstats' => '個人設定の統計',
	'prefstats-desc' => 'どの程度の数の利用者が個人設定のある項目を有効にしているかの追跡統計',
	'prefstats-title' => '個人設定に関する統計',
	'prefstats-list-intro' => '現在、以下の個人設定項目について追跡調査を行っています。調査結果を見るにはそれぞれをクリックしてください。',
	'prefstats-noprefs' => '現在、追跡調査の対象となっている個人設定項目はありません。追跡調査を行うには $wgPrefStatsTrackPrefs を設定してください。',
	'prefstats-counters' => '* 個人設定の統計が稼動して以降、$1人の利用者がこの設定を有効にしました
** $2人の利用者がこれを有効にしています
** $3人の利用者がこれを無効にしています',
	'prefstats-counters-expensive' => '* 個人設定の統計が稼動して以降、$1人の利用者がこの設定を有効にしました
** $2人の利用者がこれを有効にしています
** $3人の利用者がこれを無効にしています
* 合計では、$4人の利用者がこの項目を設定しています',
	'prefstats-xaxis' => '期間（単位：時間）',
	'prefstats-factors' => '表示する縮尺: $1',
	'prefstats-factor-hour' => '1時間',
	'prefstats-factor-sixhours' => '6時間',
	'prefstats-factor-day' => '1日',
	'prefstats-factor-week' => '1週間',
	'prefstats-factor-twoweeks' => '2週間',
	'prefstats-factor-fourweeks' => '4週間',
	'prefstats-factor-default' => 'デフォルトの縮尺に戻る',
	'prefstats-legend-out' => '非参加',
	'prefstats-legend-in' => '参加',
);

/** Javanese (Basa Jawa)
 * @author Pras
 */
$messages['jv'] = array(
	'prefstats' => 'Statistik preferensi',
	'prefstats-desc' => 'Statistik ngenani ana pira panganggo sing ngaktifaké preferensi tinamtu',
	'prefstats-title' => 'Statistik preferensi',
	'prefstats-factor-hour' => 'jam',
	'prefstats-factor-sixhours' => 'enem jam',
	'prefstats-factor-day' => 'dina',
	'prefstats-factor-week' => 'minggu',
	'prefstats-factor-twoweeks' => 'rong minggu',
	'prefstats-factor-fourweeks' => 'patang minggu',
);

/** Georgian (ქართული)
 * @author Alsandro
 * @author გიორგიმელა
 */
$messages['ka'] = array(
	'prefstats' => 'კონფიგურაციათა სტატისტიკა',
	'prefstats-desc' => 'გარკვეული კონფიგურაციების ჩამრთველ მომხმარელთა სტატისტიკის კონტროლი',
	'prefstats-title' => 'კონფიგურაციათა სტატისტიკა',
	'prefstats-list-intro' => 'ეხლა მიმდინარეობს შემდეგ კონფიგურაციათა კონტროლი
აირჩიეთ რომელიმე მათგანი სტატისტიკის სანახავად',
	'prefstats-noprefs' => 'რაიმე უპირატესობა ამჟამად კონტროლი არ ეწევა. კონფიგურაციის კონტროლისთვის შეიტანეთ ცვლილებები $wgPrefStatsTrackPrefs გვერდზე.',
);

/** Khmer (ភាសាខ្មែរ)
 * @author វ័ណថារិទ្ធ
 */
$messages['km'] = array(
	'prefstats' => 'ស្ថិតិ​ ចំណូលចិត្ត​',
	'prefstats-title' => 'ស្ថិតិ​ ចំណូលចិត្ត​',
	'prefstats-xaxis' => 'រយៈពេល​ (ម៉ោង​)',
	'prefstats-factors' => 'មើល​ក្នុងមួយ​៖ $1',
	'prefstats-factor-hour' => 'ម៉ោង',
	'prefstats-factor-sixhours' => 'ប្រាំ​មួយ​ម៉ោង​',
	'prefstats-factor-day' => 'ថ្ងៃ',
	'prefstats-factor-week' => 'សប្តាហ៍',
	'prefstats-factor-twoweeks' => '២ សប្តាហ៍',
	'prefstats-factor-fourweeks' => '៤ សប្តាហ៍',
);

/** Korean (한국어)
 * @author Klutzy
 */
$messages['ko'] = array(
	'prefstats' => '환경 설정 통계',
	'prefstats-desc' => '각 환경 설정에 대한 사용자 비율 통계',
	'prefstats-title' => '환경 설정 통계',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'prefstats' => 'Shtatistike övver Enshtellunge',
	'prefstats-desc' => 'Määt Shtatistike doh drövver, wi vill Metmaacher beshtemmpte Enshtellunge för sesch jemaat han.',
	'prefstats-title' => 'Shtatistike övver de Metmaacher ier Enshtellunge',
	'prefstats-list-intro' => 'Em Momang donn mer heh di Enshtellunge vun de Metmaacher biobachte.
Donn op ein dovun drop klecke, öm dä ier Shtatistik ze belooere.',
	'prefstats-noprefs' => 'De Enshtellunge wääde nit Verfollsch. Donn <code lang="en">$wgPrefStatsTrackPrefs</code> opsäze, öm dat ze ändere.',
	'prefstats-counters' => '* {{PLURAL:$1|Eine Metmaacher hät|$1 Metmaacher han|Keine Metmaacher hät}} di Enshtellung aanjemaat zick dämm de Shtatistike ennjeschalldt woode sin.
** {{PLURAL:$2|Eine Metmaacher hät|$2 Metmaacher han|Keine Metmaacher hät}} se jäz noch aanjeschalldt.
** {{PLURAL:$3|Eine Metmaacher hät|$3 Metmaacher han|Keine Metmaacher hät}} se zick dämm ußjeschalldt.',
	'prefstats-counters-expensive' => '* {{PLURAL:$1|Eine Metmaacher hät|$1 Metmaacher han|Keine Metmaacher hät}} di Enshtellung aanjemaat zick dämm de Shtatistike ennjeschalldt woode sin.
** {{PLURAL:$2|Eine Metmaacher hät|$2 Metmaacher han|Keine Metmaacher hät}} se jäz noch aanjeschalldt.
** {{PLURAL:$3|Eine Metmaacher hät|$3 Metmaacher han|Keine Metmaacher hät}} se zick dämm ußjeschalldt.
* Ennsjesamp, {{PLURAL:$4|hät eine Metmaacher|hann_er $4 Metmaacher|keine Metmaacher}} se övverhoub_ens jesaz.',
	'prefstats-xaxis' => 'Duuer en Stunde',
	'prefstats-factors' => 'Beloore för: $1',
	'prefstats-factor-hour' => 'Shtund',
	'prefstats-factor-sixhours' => 'sechs Shtunde',
	'prefstats-factor-day' => 'Daach',
	'prefstats-factor-week' => 'Woch',
	'prefstats-factor-twoweeks' => 'Zwei Woche',
	'prefstats-factor-fourweeks' => 'Vier Woche',
	'prefstats-factor-default' => 'Retuur op der Shtandatt',
	'prefstats-legend-out' => 'Afjemeldt',
	'prefstats-legend-in' => 'Aanjemelldt',
);

/** Latin (Latina)
 * @author Omnipaedista
 */
$messages['la'] = array(
	'prefstats-factor-hour' => 'hora',
	'prefstats-factor-sixhours' => 'sex hores',
	'prefstats-factor-day' => 'dies',
	'prefstats-factor-week' => 'hebdomas',
	'prefstats-factor-twoweeks' => 'duae hebdomades',
	'prefstats-factor-fourweeks' => 'quattuor hebdomades',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'prefstats' => 'Statistike vun den Astellungen',
	'prefstats-desc' => 'Statistiken doriwwer wéivil Benotzer bestëmmten Astellungn aktivéiert hunn',
	'prefstats-title' => 'Statistike vun den Astellungen',
	'prefstats-list-intro' => 'Elo ginn dës Astellungen iwwerwaacht.
Klickt op eng fir Statistiken iwwer hire Gebrauch ze gesinn.',
	'prefstats-noprefs' => "Et ginn elo keng Astellungen iwwerwaacht. Stellt \$wgPrefStatsTrackPrefs an fir d'Astellungen z'iwwerwaachen.",
	'prefstats-counters' => "* $1 {{PLURAL:$1|Benotzer huet|Benotzer hunn}} dës Astellung ageschalt zënter datt d'Statistik vun de Benotzerastellungen aktivéiert gouf
** $2 {{PLURAL:$2|Benotzer huet|Benotzer hunn}} et ageschalt
** $3 {{PLURAL:$3|Benotzer huet|Benotzer hunn}} et ausgeschalt",
	'prefstats-counters-expensive' => "* $1 {{PLURAL:$1|Benotzer huet|Benotzer hunn}} dës Astellung ageschalt zënter datt d'Statistik vun de Benotzerastellungen aktivéiert gouf
** $2 {{PLURAL:$2|Benotzer huet|Benotzer hunn}} et ageschalt
** $3 {{PLURAL:$3|Benotzer huet|Benotzer hunn}} et ausgeschalt
* am Ganzen, $4 {{PLURAL:$3|Benotzer huet|Benotzer hunn}} dës Astellung konfiguréiert",
	'prefstats-xaxis' => 'Dauer (Stonnen)',
	'prefstats-factors' => 'Gekuckt pro: $1',
	'prefstats-factor-hour' => 'Stonn',
	'prefstats-factor-sixhours' => 'sechs Stonnen',
	'prefstats-factor-day' => 'Dag',
	'prefstats-factor-week' => 'Woch',
	'prefstats-factor-twoweeks' => 'zwou Wochen',
	'prefstats-factor-fourweeks' => 'véier Wochen',
	'prefstats-factor-default' => "zréck op d'Standard-Gréisst",
	'prefstats-legend-out' => 'Mécht net mat',
	'prefstats-legend-in' => 'Mécht mat',
);

/** Lithuanian (Lietuvių)
 * @author Matasg
 */
$messages['lt'] = array(
	'prefstats' => 'Nustatymų statistika',
	'prefstats-desc' => 'Rinkite statistiką apie naudotojus, pasirinkusius šiuos nustatymus',
	'prefstats-title' => 'Nustatymų statistika',
	'prefstats-list-intro' => 'Šiuo metu yra sekami šie pasirinkimai.
Pasirinkite vieną iš jų, norėdami pamatyti statistiką.',
);

/** Macedonian (Македонски)
 * @author Bjankuloski06
 */
$messages['mk'] = array(
	'prefstats' => 'Статистики за прилагодувања',
	'prefstats-desc' => 'Следи ги статистиките кои кажуваат колку корисници имаат овозможено извесни прилагодувања',
	'prefstats-title' => 'Статистики за прилагодувања',
	'prefstats-list-intro' => 'Моментално ги следите следниве прилагодувања.
Кликнете на едно од нив за да ги видите статистиките за него.',
	'prefstats-noprefs' => 'Моментално не се следите никакви прилагодувања.
Конфигурирајте го $wgPrefStatsTrackPrefs за да следите прилагодувања.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|корисник ја има|корисници ја имаат}} вклучено оваа можност од кога е активирана статистиката на прилагодувања
** $2 {{PLURAL:$2|корисник сеуште ја чува вклучена|корисници сеуште ја чуваат вклучена}}
** $3 {{PLURAL:$3|корисникот во меѓувреме ја оневозможил|корисниците во меѓувреме ја оневозможиле}}',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|корисник ја има|корисници ја имаат}} вклучено оваа можност од кога е активирана статистиката на прилагодувања
** $2 {{PLURAL:$2|корисник сеуште ја чува вклучена|корисници сеуште ја чуваат вклучена}}
** $3 {{PLURAL:$3|корисникот во меѓувреме ја оневозможил|корисниците во меѓувреме ја оневозможиле}}
* Вкупно $4 {{PLURAL:$4|корисник ја има вклучено оваа можност|корисници ја имаат вклучено оваа можност}}',
	'prefstats-xaxis' => 'Времетрање (часови)',
	'prefstats-factors' => 'Поглед по: $1',
	'prefstats-factor-hour' => 'час',
	'prefstats-factor-sixhours' => 'шест часа',
	'prefstats-factor-day' => 'ден',
	'prefstats-factor-week' => 'седмица',
	'prefstats-factor-twoweeks' => 'две седмици',
	'prefstats-factor-fourweeks' => 'четири седмици',
	'prefstats-factor-default' => 'врати основно зададен размер',
	'prefstats-legend-out' => 'Пристапил',
	'prefstats-legend-in' => 'Напуштил',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'prefstats' => 'Voorkeurenstatistieken',
	'prefstats-desc' => 'Statistieken bijhouden over hoeveel gebruikers bepaalde voorkeuren hebben ingeschakeld',
	'prefstats-title' => 'Voorkeurenstatistieken',
	'prefstats-list-intro' => 'Instellingen voor de onderstaande voorkeuren worden bijgehouden.
Klik op een voorkeur om de statistieken te bekijken.',
	'prefstats-noprefs' => 'Er worden geen voorkeuren bijgehouden.
Stel $wgPrefStatsTrackPrefs in om voorkeuren bij te houden.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|gebruiker heeft|gebruikers hebben}} deze voorkeur ingesteld sinds de voorkeurstatistieken zijn geactiveerd.
** $2 {{PLURAL:$2|gebruiker heeft|gebruikers hebben}} deze nog insteld
** $3 {{PLURAL:$3|gebruiker heeft|gebruikers hebben}} deze weer uitgeschakeld',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|gebruiker heeft|gebruikers hebben}} deze voorkeur ingesteld sinds de voorkeurstatistieken zijn geactiveerd.
** $2 {{PLURAL:$2|gebruiker heeft|gebruikers hebben}} deze nog insteld.
** $3 {{PLURAL:$3|gebruiker heeft|gebruikers hebben}} deze weer uitgeschakeld.
* In totaal {{PLURAL:$4|heeft $4 gebruiker|hebben $4 gebruikers}} deze voorkeur ingesteld.',
	'prefstats-xaxis' => 'Duur (uren)',
	'prefstats-factors' => 'Weergeven per: $1',
	'prefstats-factor-hour' => 'uur',
	'prefstats-factor-sixhours' => 'zes uur',
	'prefstats-factor-day' => 'dag',
	'prefstats-factor-week' => 'week',
	'prefstats-factor-twoweeks' => 'twee weken',
	'prefstats-factor-fourweeks' => 'vier weken',
	'prefstats-factor-default' => 'terug naar de standaardschaal',
	'prefstats-legend-out' => 'Afgemeld',
	'prefstats-legend-in' => 'Aangemeld',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Harald Khan
 */
$messages['nn'] = array(
	'prefstats' => 'Statistikk over innstillingar',
	'prefstats-desc' => 'Statistikk over talet på brukarar som har visse innstillingar',
	'prefstats-title' => 'Statistikk over innstillingar',
	'prefstats-list-intro' => 'For tida vert dei fylgjande innstillingane spora.
Trykk på éi for å sjå statistikk for ho.',
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬)
 * @author Laaknor
 * @author Simny
 * @author Stigmj
 */
$messages['no'] = array(
	'prefstats' => 'Statistikk over innstillinger',
	'prefstats-desc' => 'Statistikk over tallet på brukere som har visse innstillinger',
	'prefstats-title' => 'Statistikk over innstillinger',
	'prefstats-list-intro' => 'For tiden blir følgende innstillinger sporet.
Klikk på en for å se statistikk om den.',
	'prefstats-noprefs' => 'Ingen preferanser blir sporet. Konfigurer $wgPrefStatsTrackPrefs for å spore preferanser',
	'prefstats-counters' => '* {{PLURAL:$1|Én bruker|$1 brukere}} har aktivert denne innstillingen siden sporingen ble startet
** {{PLURAL:$2|Én bruker|$2 brukere}} har den fortsatt aktivert
** {{PLURAL:$3|Én bruker|$3 brukere}} har deaktivert den igjen',
	'prefstats-counters-expensive' => '* {{PLURAL:$1|Én bruker|$1 brukere}} har aktivert denne innstillingen siden sporingen ble startet
** {{PLURAL:$2|Én bruker|$2 brukere}} har den fortsatt aktivert
** {{PLURAL:$3|Én bruker|$3 brukere}} har deaktivert den igjen
* Sammenlagt har {{PLURAL:$4|én bruker|$4 brukere}} innstillingen aktivert',
	'prefstats-xaxis' => 'Varighet (timer)',
	'prefstats-factors' => 'Vis etter $1',
	'prefstats-factor-hour' => 'time',
	'prefstats-factor-sixhours' => 'seks timer',
	'prefstats-factor-day' => 'dag',
	'prefstats-factor-week' => 'uke',
	'prefstats-factor-twoweeks' => 'to uker',
	'prefstats-factor-fourweeks' => 'fire uker',
	'prefstats-factor-default' => 'tilbake til standardskalering',
	'prefstats-legend-out' => 'Valgt vekk',
	'prefstats-legend-in' => 'Valgt',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'prefstats' => 'Preferéncia de las estatisticas',
	'prefstats-desc' => "Estatisticas sul nombre d'utilizaires qu'an cèrtas preferéncias activadas",
	'prefstats-title' => 'Estatisticas de las preferéncias',
	'prefstats-list-intro' => "En aqueste moment, las preferéncias seguentas son seguidas.
Clicatz sus una d'entre elas per veire las estatisticas a son prepaus.",
	'prefstats-noprefs' => 'Cap de preferéncia es pas seguida actualament. Configuratz $wgPrefStatsTrackPrefs per seguir de preferéncias.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|utilizaire a|utilizaires an}} activat aquesta preferéncia dempuèi que las estatisticas de preferéncias son estadas activadas
** $2 {{PLURAL:$2|utilizaire a|utilizaires an}} activat aquesta preferéncia
** $3 {{PLURAL:$3|utilizaire a|utilizaires an}} desactivat aquesta preferéncia',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|utilizaire a|utilizaires an}} activat aquesta preferéncia dempuèi que las estatisticas de preferéncias son estadas activadas
** $2 {{PLURAL:$2|utilizaire a|utilizaires an}} activat aquesta preferéncia
** $3 {{PLURAL:$3|utilizaire a|utilizaires an}} desactivat aquesta preferéncia
* Al total, $4 {{PLURAL:$4|utilizaire a|utilizaires an}} definit aquesta preferéncia',
	'prefstats-xaxis' => 'Durada (oras)',
	'prefstats-factors' => 'Afichar per : $1',
	'prefstats-factor-hour' => 'ora',
	'prefstats-factor-sixhours' => 'sièis oras',
	'prefstats-factor-day' => 'jorn',
	'prefstats-factor-week' => 'setmana',
	'prefstats-factor-twoweeks' => 'doas setmanas',
	'prefstats-factor-fourweeks' => 'quatre setmanas',
	'prefstats-factor-default' => "tornar a l'escala per defaut",
);

/** Polish (Polski)
 * @author Sp5uhe
 */
$messages['pl'] = array(
	'prefstats' => 'Statystyki dla preferencji',
	'prefstats-desc' => 'Dane statystyczne na temat liczby użytkowników, którzy korzystają z poszczególnych preferencji',
	'prefstats-title' => 'Statystyki dla preferencji',
	'prefstats-list-intro' => 'Obecnie następujące preferencje są analizowane.
Kliknij na jednej aby zobaczyć statystyki jej dotyczące.',
	'prefstats-noprefs' => 'Żadne preferencje nie są obecnie śledzone. Skonfiguruj $wgPrefStatsTrackPrefs aby śledzić preferencje.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|użytkownik włączał|użytkowników włączało}} tę opcję od momentu aktywowania tej statystyki
** $2 {{PLURAL:$2|użytkownik|użytkowników}} ma tę opcję włączoną
** $3 {{PLURAL:$3|użytkownik|użytkowników}} ma tę opcję wyłączoną',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|użytkownik włączał|użytkowników włączało}} tę opcję od momentu aktywowania tej statystyki
** $2 {{PLURAL:$2|użytkownik|użytkowników}} ma tę opcję włączoną
** $3 {{PLURAL:$3|użytkownik|użytkowników}} ma tę opcję wyłączoną
* Ogólnie $4  {{PLURAL:$4|użytkownik|użytkowników}} ustawiło tę opcję',
	'prefstats-xaxis' => 'Czas trwania (godz.)',
	'prefstats-factors' => 'Widoczny okres – $1',
	'prefstats-factor-hour' => 'godzina',
	'prefstats-factor-sixhours' => 'sześć godzin',
	'prefstats-factor-day' => 'dzień',
	'prefstats-factor-week' => 'tydzień',
	'prefstats-factor-twoweeks' => 'dwa tygodnie',
	'prefstats-factor-fourweeks' => 'cztery tygodnie',
	'prefstats-factor-default' => 'powrót do domyślnej skali',
	'prefstats-legend-out' => 'Wycofane',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'prefstats' => 'Estatísticas de preferências',
	'prefstats-desc' => 'Monitorize estatísticas sobre quantos utilizadores têm certas preferências ativadas',
	'prefstats-title' => 'Estatísticas de preferências',
	'prefstats-list-intro' => 'Atualmente, as seguintes preferência estão a ser monitorizadas.
Clique numa para ver as estatísticas sobre ela.',
);

/** Brazilian Portuguese (Português do Brasil)
 * @author Heldergeovane
 */
$messages['pt-br'] = array(
	'prefstats' => 'Estatísticas de preferências',
	'prefstats-desc' => 'Monitore estatísticas sobre quantos usuários têm certas preferências ativadas',
	'prefstats-title' => 'Estatísticas de preferências',
	'prefstats-list-intro' => 'Atualmente, as seguintes preferência estão sendo monitoradas.
Clique em uma para ver as estatísticas sobre ela.',
	'prefstats-noprefs' => 'Nenhuma preferência está sendo monitorada no momento.
Configure $wgPrefStatsTrackPrefs para monitorar preferências.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|usuário habilitou|usuários habilitaram}} esta preferência desde que as estatísticas foram ativadas
** $2 Ela foi habilitada por {{PLURAL:$2|usuário|usuários}}
** $3 Ela foi desabilitada por {{PLURAL:$3|usuário|usuários}}',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|habilitou|habilitaram}} esta preferência desde que as estatísticas de preferências foram habilitadas
** $2 Ela foi habilitada por {{PLURAL:$2|usuário|usuários}}
** $3 Ela foi desabilitada por {{PLURAL:$3|usuário|usuários}}
* Ao todo, $4 {{PLURAL:$4|usuário|usuários}} definiram esta preferência',
	'prefstats-xaxis' => 'Duração (horas)',
	'prefstats-factors' => 'Visualizar por: $1',
	'prefstats-factor-hour' => 'Hora',
	'prefstats-factor-sixhours' => 'seis horas',
	'prefstats-factor-day' => 'dia',
	'prefstats-factor-week' => 'semana',
	'prefstats-factor-twoweeks' => 'duas semanas',
	'prefstats-factor-fourweeks' => 'quatro semanas',
	'prefstats-factor-default' => 'retornar à escala padrão',
);

/** Quechua (Runa Simi)
 * @author AlimanRuna
 */
$messages['qu'] = array(
	'prefstats' => 'Munakusqa ranuy (kanchachani)',
	'prefstats-desc' => "Kaypiqa rukunki, hayk'a ruraqkuna ima munakusqankunata allinkachina nisqapi akllarqan",
	'prefstats-title' => 'Munakusqa ranuy (kanchachani)',
);

/** Romanian (Română)
 * @author Firilacroco
 */
$messages['ro'] = array(
	'prefstats' => 'Statistici despre preferinţe',
	'prefstats-desc' => 'Urmăiţi statistici despre câţi utilizatori au o anumită preferinţă activată',
	'prefstats-title' => 'Statistici despre preferinţe',
	'prefstats-list-intro' => 'În prezent, sunt urmărite următoarele preferinţe.
Apăsaţi pe ele pentru a vizualiza statistici despre ele.',
);

/** Tarandíne (Tarandíne)
 * @author Joetaras
 */
$messages['roa-tara'] = array(
	'prefstats' => 'Statistece de preferenze',
	'prefstats-desc' => 'Traccie le statisteche sus a quanda utinde onne certe preferenze abbilitate',
	'prefstats-title' => 'Statisteche de le preferenze',
	'prefstats-list-intro' => 'Pe mò, le seguende preferenze stonne avènene tracciate.
Cazze sus a une de le statisteche da vedè.',
	'prefstats-noprefs' => 'Nisciuna preferenze ha state tracciate pe mò. Configure $wgPrefStatsTrackPrefs pe traccià le preferenze.',
);

/** Russian (Русский)
 * @author Ferrer
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'prefstats' => 'Статистика настроек',
	'prefstats-desc' => 'Отслеживание статистики о том, сколько пользователей включили у себя те или иные настройки',
	'prefstats-title' => 'Статистика настроек',
	'prefstats-list-intro' => 'Сейчас отслеживаются следующие настройки.
Выберите одну из них для просмотра статистики.',
	'prefstats-noprefs' => 'В настоящее время настройки не отслеживаются.
Установите $wgPrefStatsTrackPrefs для отслеживания настроек.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|участник включил|участника включили|участников включили}} этот параметр, с момента начала работы статистики по параметрам
** $2 {{PLURAL:$2|участник включил|участника включили|участников включили}} параметр
** $3 {{PLURAL:$3|участник выключил|участника выключили|участников выключили}} параметр',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|участник включил|участника включили|участников включили}} этот параметр, с момента начала работы статистики по параметрам
** $2 {{PLURAL:$2|участник включил|участника включили|участников включили}} параметр
** $3 {{PLURAL:$3|участник выключил|участника выключили|участников выключили}} параметр
* Всего этот параметр установлен у $4 {{PLURAL:$4|участника|участников|участников}}',
	'prefstats-xaxis' => 'Продолжительность (в часах)',
	'prefstats-factors' => 'Просмотр по: $1',
	'prefstats-factor-hour' => 'час',
	'prefstats-factor-sixhours' => 'шесть часов',
	'prefstats-factor-day' => 'день',
	'prefstats-factor-week' => 'неделя',
	'prefstats-factor-twoweeks' => 'две недели',
	'prefstats-factor-fourweeks' => 'четыре недели',
	'prefstats-factor-default' => 'назад к масштабу по умолчанию',
	'prefstats-legend-out' => 'Отключиться',
	'prefstats-legend-in' => 'Включиться',
);

/** Yakut (Саха тыла)
 * @author HalanTul
 */
$messages['sah'] = array(
	'prefstats' => 'Туруоруулар статиистикалара',
	'prefstats-desc' => 'Хас киһи ханнык эмит туруорууну талбыттарын көрүү',
	'prefstats-title' => 'Туруоруулар статиистикалара',
	'prefstats-list-intro' => 'Билигин маннык туруоруулар ааҕыллыахтарын сөп.
Статиистикатын көрөргө ханныгы эмит биири тал.',
);

/** Sicilian (Sicilianu)
 * @author Melos
 */
$messages['scn'] = array(
	'prefstats' => 'Statistichi dê prifirenzi',
	'prefstats-title' => 'Statistichi dê prifirenzi',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'prefstats' => 'Štatistika nastavení',
	'prefstats-desc' => 'Umožňuje sledovať štatistiku, koľko ľudí má zapnutú určitú voľbu v nastaveniach',
	'prefstats-title' => 'Štatistika nastavení',
	'prefstats-list-intro' => 'Momentálne sa sledujú nasledovné nastavenia.
Po kliknutí na niektoré z nich zobrazíte štatistiku o ňom.',
	'prefstats-noprefs' => 'Momentálne sa nesledujú žiadne nastavenia. Ak chcete sledovať nastavenia, nastavte $wgPrefStatsTrackPrefs.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|používateľ zapol|používatelia zapli|používateľov zaplo}} túto voľbu od aktivácie štatistiky nastavení
** $2 {{PLURAL:$2|používateľ ju zapol|používatelia ju zapli|používateľov ju zaplo}}
** $3 {{PLURAL:$3|používateľ ju vypol|používatelia ju vypli|používateľov ju vyplo}}',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|používateľ zapol|používatelia zapli|používateľov zaplo}} túto voľbu od aktivácie štatistiky nastavení
** $2 {{PLURAL:$2|používateľ ju zapol|používatelia ju zapli|používateľov ju zaplo}}
** $3 {{PLURAL:$3|používateľ ju vypol|používatelia ju vypli|používateľov ju vyplo}}
* Celkom {{PLURAL:$4|má|majú}} túto voľbu zapnutú $4 {{PLURAL:$4|používateľ|používatelia|používateľov}}',
	'prefstats-xaxis' => 'Trvanie (hodín)',
	'prefstats-factors' => 'Zobrazenie za: $1',
	'prefstats-factor-hour' => 'hodina',
	'prefstats-factor-sixhours' => 'šesť hodín',
	'prefstats-factor-day' => 'deň',
	'prefstats-factor-week' => 'týždeň',
	'prefstats-factor-twoweeks' => 'dva týždne',
	'prefstats-factor-fourweeks' => 'štyri týždne',
	'prefstats-factor-default' => 'späť na predvolenú mierku',
	'prefstats-legend-out' => 'Odhlásený',
	'prefstats-legend-in' => 'Prihlásený',
);

/** Slovenian (Slovenščina)
 * @author Smihael
 */
$messages['sl'] = array(
	'prefstats' => 'Statistika nastavitev',
	'prefstats-desc' => 'Spremlja statistike o tem, koliko uporabnikov ima omogočene določene nastavitve',
	'prefstats-title' => 'Statistika nastavitev',
);

/** Swedish (Svenska)
 * @author Fluff
 * @author Ozp
 * @author Rotsee
 */
$messages['sv'] = array(
	'prefstats' => 'Statistik över inställningar',
	'prefstats-desc' => 'Statistik över hur många användare som har vissa inställningar',
	'prefstats-title' => 'Statistik över inställningar',
	'prefstats-list-intro' => 'För närvarande spåras följande inställningar.
Klicka på en inställning för att visa statistik om den.',
	'prefstats-noprefs' => 'Inga inställningar spåras för närvarande. Ändra $wgPrefStatsTrackPrefs för att spåra inställningar.',
	'prefstats-counters' => '* $1 {{PLURAL:$1|användare|användare}} har slagit på den här inställningen sedan spårningen av inställningar inleddes.
** $2 {{PLURAL:$2|användare|användare}} har slagit på den
** $3 {{PLURAL:$3|användare|användare}} har slagit av den',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|användare|användare}} har slagit på den här inställningen sedan spårningen av inställningar inleddes
** $2 {{PLURAL:$2|användare|användare}} har slagit på den
** $3 {{PLURAL:$3|användare|användare}} har slagit av den
* Sammanlagt har $4 {{PLURAL:$4|användare|användare}} den här inställningen påslagen',
	'prefstats-xaxis' => 'Varaktighet (timmar)',
	'prefstats-factors' => 'Visa efter $1',
	'prefstats-factor-hour' => 'timme',
	'prefstats-factor-sixhours' => 'sex timmar',
	'prefstats-factor-day' => 'dag',
	'prefstats-factor-week' => 'vecka',
	'prefstats-factor-twoweeks' => 'två veckor',
	'prefstats-factor-fourweeks' => 'fyra veckor',
	'prefstats-factor-default' => 'återgå till standardskala',
	'prefstats-legend-out' => 'Lämnat',
	'prefstats-legend-in' => 'Deltar',
);

/** Telugu (తెలుగు)
 * @author Ravichandra
 * @author Veeven
 */
$messages['te'] = array(
	'prefstats' => 'అభిరుచుల గణాంకాలు',
	'prefstats-title' => 'అభిరుచుల గణాంకాలు',
	'prefstats-list-intro' => 'ప్రస్తుతం, ఈ క్రింది అభిరుచులను గమనిస్తున్నాం.
ఒక్కోదాని గణాంకాలు చూడడానికి దానిపై నొక్కండి.',
	'prefstats-xaxis' => 'సమయం (గంటల్లో)',
	'prefstats-factor-hour' => 'గంట',
	'prefstats-factor-day' => 'రోజు',
	'prefstats-factor-week' => 'వారం',
	'prefstats-factor-twoweeks' => 'రెండు వారాలు',
	'prefstats-factor-fourweeks' => 'నాలుగు వారాలు',
);

/** Turkish (Türkçe)
 * @author Joseph
 */
$messages['tr'] = array(
	'prefstats' => 'Tercih istatistikleri',
	'prefstats-desc' => 'Belirli tercihlerin kaç kullanıcı tarafından etkinleştirildiği hakkında istatistikleri izle',
	'prefstats-title' => 'Tercih istatistikleri',
	'prefstats-list-intro' => 'Şu anda, aşağıdaki tercihler izleniyor.
İlgili istatistikleri görmek için birine tıklayın.',
	'prefstats-noprefs' => 'Şu anda hiçbir tercih izlenmiyor.
Tercihleri izlemek için $wgPrefStatsTrackPrefs\'i yapılandırın.',
	'prefstats-counters' => '* Tercih istatistikleri etkinleşirildiğinden beri $1 {{PLURAL:$1|kullanıcı|kullanıcı}} bu tercihi etkinleştirdi.
** $2 {{PLURAL:$2|kullancı|kullanıcı}} hala etkin olarak kullanıyor
** $3 {{PLURAL:$3|kullanıcı|kullanıcı}} şimdiye kadar devre dışı bıraktı',
	'prefstats-counters-expensive' => '* Tercih istatistikleri etkinleşirildiğinden beri $1 {{PLURAL:$1|kullanıcı|kullanıcı}} bu tercihi etkinleştirdi.
** $2 {{PLURAL:$2|kullancı|kullanıcı}} hala etkin olarak kullanıyor
** $3 {{PLURAL:$3|kullanıcı|kullanıcı}} şimdiye kadar devre dışı bıraktı
* Toplamda, $4 {{PLURAL:$4|kullanıcı|kullanıcı}} bu tercihi ayarladı',
	'prefstats-xaxis' => 'Süre (saat)',
	'prefstats-factors' => 'Görüntüleme sıklığı: $1',
	'prefstats-factor-hour' => 'saat',
	'prefstats-factor-day' => 'gün',
	'prefstats-factor-week' => 'hafta',
	'prefstats-factor-twoweeks' => 'iki hafta',
	'prefstats-factor-fourweeks' => 'dört hafta',
	'prefstats-factor-default' => 'varsayılan ölçeğe dön',
);

/** Ukrainian (Українська)
 * @author AS
 */
$messages['uk'] = array(
	'prefstats' => 'Статистика налаштувань',
	'prefstats-desc' => 'Творення статистики про популярність тих чи інших налаштувань',
	'prefstats-title' => 'Статистика налаштувань',
	'prefstats-list-intro' => 'Зараз відстежуються такі налаштування.
Натисніть на якомусь, щоб побачити його статистику.',
);

/** Vèneto (Vèneto)
 * @author Candalua
 */
$messages['vec'] = array(
	'prefstats' => 'Statìsteghe de le preferense',
	'prefstats-desc' => 'Statìsteghe sirca el nùmaro de utenti che ga ativà serte preferense',
	'prefstats-title' => 'Statìsteghe de le preferense',
	'prefstats-list-intro' => 'Al momento, vien tegnù tràcia de le seguenti preferense.
Strucando su de una te vedi le so statìsteghe.',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 * @author Vinhtantran
 */
$messages['vi'] = array(
	'prefstats' => 'Thống kê tùy chọn',
	'prefstats-desc' => 'Theo dõi số người dùng đã bật lên những tùy chọn',
	'prefstats-title' => 'Thống kê tùy chọn',
	'prefstats-list-intro' => 'Hiện đang theo dõi các tùy chọn sau.
Hãy nhấn vào một tùy chọn để xem thống kê về nó.',
	'prefstats-noprefs' => 'Không có tùy chọn nào được theo dõi. Hãy cấu hình $wgPrefStatsTrackPrefs để theo dõi tùy chọn.',
);

/** Yue (粵語)
 * @author Shinjiman
 */
$messages['yue'] = array(
	'prefstats' => '喜好統計',
	'prefstats-desc' => '追蹤統計，有幾多用戶開咗特定設定',
	'prefstats-title' => '喜好統計',
	'prefstats-list-intro' => '直到而家，下面嘅喜好設定會追蹤落來。
撳其中一樣去睇有關佢嘅統計。',
	'prefstats-noprefs' => '無喜好可以追蹤得到。設定 $wgPrefStatsTrackPrefs 去追蹤喜好。',
);

/** Simplified Chinese (‪中文(简体)‬)
 * @author Jimmy xu wrk
 * @author Shinjiman
 */
$messages['zh-hans'] = array(
	'prefstats' => '喜好统计',
	'prefstats-desc' => '追踪统计，有多少用户启用了特定的设置',
	'prefstats-title' => '喜好统计',
	'prefstats-list-intro' => '直到现时，以下的喜好设置会追踪下来。
	点击其中一种设置去查看有关它的统计。',
	'prefstats-noprefs' => '无喜好可供追踪。设置 $wgPrefStatsTrackPrefs 去追踪喜好。',
	'prefstats-counters' => '* $1名用户在统计启用之后启用了此选项
** $2名用户启用了它
** $3名用户禁用了它',
	'prefstats-counters-expensive' => '* $1名用户在统计启用之后启用了此选项
** $2名用户启用了它
** $3名用户禁用了它
* 总的来说，$4名用户设置了此选项',
);

/** Traditional Chinese (‪中文(繁體)‬)
 * @author Shinjiman
 */
$messages['zh-hant'] = array(
	'prefstats' => '喜好統計',
	'prefstats-desc' => '追蹤統計，有多少用戶啟用了特定的設定',
	'prefstats-title' => '喜好統計',
	'prefstats-list-intro' => '直到現時，以下的喜好設定會追蹤下來。
點擊其中一種設定去查看有關它的統計。',
	'prefstats-noprefs' => '無喜好可供追蹤。設定 $wgPrefStatsTrackPrefs 去追蹤喜好。',
);

