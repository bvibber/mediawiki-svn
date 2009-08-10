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
** $2 {{PLURAL:$2|user has|users have}} enabled it
** $3 {{PLURAL:$3|user has|users have}} disabled it',
	'prefstats-counters-expensive' => '* $1 {{PLURAL:$1|user has|users have}} enabled this preference since preference statistics were activated
** $2 {{PLURAL:$2|user has|users have}} enabled it
** $3 {{PLURAL:$3|user has|users have}} disabled it
* In total, $4 {{PLURAL:$4|user has|users have}} this preference set',
	'prefstats-xaxis' => 'Duration (hours)',
	'prefstats-factors' => 'View per: $1',
	'prefstats-factor-hour' => 'hour',
	'prefstats-factor-day' => 'day',
	'prefstats-factor-week' => 'week',
	'prefstats-factor-twoweeks' => 'two weeks',
	'prefstats-factor-fourweeks' => 'four weeks',
	'prefstats-factor-default' => 'back to default scale',
);

/** Arabic (العربية)
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
);

/** German (Deutsch)
 * @author Metalhead64
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
);

/** Greek (Ελληνικά)
 * @author Omnipaedista
 */
$messages['el'] = array(
	'prefstats' => 'Στατιστικά προτίμησης',
	'prefstats-title' => 'Στατιστικά προτίμησης',
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
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Locos epraix
 */
$messages['es'] = array(
	'prefstats' => 'Estadísticas de preferencia',
	'prefstats-desc' => 'Seguimiento de las estadísticas sobre cuántos usuarios tienen ciertas preferencias habilitadas',
	'prefstats-title' => 'Estadísticas de preferencia',
	'prefstats-list-intro' => 'De momento, las siguientes preferencias están siendo seguidas.
Selecciona una para ver estadísticas acerca de ella.',
);

/** Estonian (Eesti)
 * @author Pikne
 */
$messages['et'] = array(
	'prefstats-desc' => 'Kogub arvandmeid kindlate eelistuste kasutatavuse kohta.',
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
 * @author Str4nd
 */
$messages['fi'] = array(
	'prefstats' => 'Asetusten tilastot',
	'prefstats-desc' => 'Kerää tilastoja siitä, kuinka moni käyttäjä on ottanut käyttöön erinäiset asetukset.',
	'prefstats-title' => 'Asetusten tilastot',
	'prefstats-list-intro' => 'Tällä hetkellä seuraavia asetuksia seurataan.
Tilastot näkyvät painamalla asetusta.',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 * @author Kropotkine 113
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
 * @author Rex
 */
$messages['id'] = array(
	'prefstats' => 'Statistik preferensi',
	'prefstats-desc' => 'Statistik mengenai berapa banyak pengguna yang mengaktifkan preferensi tertentu',
	'prefstats-title' => 'Statistik preferensi',
	'prefstats-list-intro' => 'Saat ini, preferensi-preferensi berikut sedang ditelusuri.
Klik pada salah satu untuk melihat statistiknya.',
	'prefstats-noprefs' => 'Tidak ada preferensi yang sedang ditelusuri. Konfigurasikan $wgPrefStatsTrackPrefs untuk menelusuri preferensi.',
);

/** Italian (Italiano)
 * @author Darth Kule
 */
$messages['it'] = array(
	'prefstats' => 'Statistiche delle preferenze',
	'prefstats-desc' => 'Statistiche circa il numero di utenti che hanno attivato alcune preferenze',
	'prefstats-title' => 'Statistiche delle preferenze',
	'prefstats-list-intro' => 'Attualmente, le seguenti preferenze vengono seguite.
Fare clic su una per vedere le statistiche su di essa.',
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
 */
$messages['no'] = array(
	'prefstats' => 'Statistikk over innstillinger',
	'prefstats-desc' => 'Statistikk over tallet på brukere som har visse innstillinger',
	'prefstats-title' => 'Statistikk over innstillinger',
	'prefstats-list-intro' => 'For tiden blir følgende innstillinger sporet.
Klikk på en for å se statistikk om den.',
	'prefstats-noprefs' => 'Ingen preferanser blir sporet. Konfigurer $wgPrefStatsTrackPrefs for å spore preferanser',
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
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'prefstats' => 'అభిరుచుల గణాంకాలు',
	'prefstats-title' => 'అభిరుచుల గణాంకాలు',
	'prefstats-list-intro' => 'ప్రస్తుతం, ఈ క్రింది అభిరుచులను గమనిస్తున్నాం.
ఒక్కోదాని గణాంకాలు చూడడానికి దానిపై నొక్కండి.',
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

