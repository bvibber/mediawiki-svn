<?php
/** Internationalization message file for News Extension.
  * @addtogroup extension
**/

$messages = array();

$messages['en'] = array(
	'newsextension-desc'          => 'Shows customized recent changes on a wiki pages or as RSS or Atom feed',
	'newsextension-unknownformat' => '$1: unknown feed format : $2<br />',	
	'newsextension-feednotfound'  => '$1: feed page not found : $2<br />',
	'newsextension-feedrequest'   => '$1: handling feed request for $2<br />',
	'newsextension-checkok'       => '$1: HTTP cache ok, 304 header sent</br >',
	'newsextension-checkok1'      => '$1: checking cache-ok:  IMS $2 vs. changed $3<br />',
	'newsextension-gotcached'     => '$1: ($2? "got cached" : "no cached")<br />',
	'newsextension-purge'         => '$1: purge, ignoring cache<br />',
	'newsextension-loggin'        => '$1: logged in, ignoring cache<br />',
	'newsextension-outputting'    => '$1: outputting cached copy ($2): $3 < {$4}<br />',
	'newsextension-stale'         => '$1: found stale cache copy ($2): $3 >= {$4}<br />',
	'newsextension-nofoundonpage' => '$1: no feed found on page: $2<br / >',
	'newsextension-renderedfeed'  => '$1: rendered feed<br />',
	'newsextension-cachingfeed'   => '$1: caching feed ($2)<br / >',
	'newsextension-freshfeed'     => '$1: outputting fresh feed<br />',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'newsextension-desc'          => 'يعرض أحدث تغييرات معدلة في صفحات الويكي أو كتلقيم آر إس إس أو أتوم.',
	'newsextension-unknownformat' => '$1: صيغة تلقيم غير معروفة : $2<br />',
	'newsextension-feednotfound'  => '$1: صفحة التلقيم غير موجودة : $2<br />',
	'newsextension-feedrequest'   => '$1: معالجة طلب التلقيم ل $2<br />',
	'newsextension-checkok'       => '$1: كاش HTTP على ما يرام، رأس 304 تم إرسالها</br >',
	'newsextension-checkok1'      => '$1: جاري التحقق الكاش على ما يرام:  IMS $2 vs. $3 التي تم تغييرها<br />',
	'newsextension-gotcached'     => '$1: ($2? "حصلت على كاش" : "لا كاش")<br />',
	'newsextension-purge'         => '$1: إفراغ الكاش، تجاهل الكاش<br />',
	'newsextension-loggin'        => '$1: تم تسجيل الدخول، يتم تجاهل الكاش<br />',
	'newsextension-outputting'    => '$1: ينتج نسخة كاش ($2): $3 < {$4}<br />',
	'newsextension-stale'         => '$1: تم العثور على نسخة كاش قديمة ($2): $3 >= {$4}<br />',
	'newsextension-nofoundonpage' => '$1: لا تلقيم تم العثور عليه في الصفحة: $2<br / >',
	'newsextension-renderedfeed'  => '$1: أنتج التلقيم<br />',
	'newsextension-cachingfeed'   => '$1: تخبئة التلقيم ($2)<br / >',
	'newsextension-freshfeed'     => '$1: ينتج تلقيما جديدا<br />',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'newsextension-desc' => 'Pokazujo pśiměrjone aktualne změny na wikijowych bokach abo ako RSS abo kanal Atom',
);

$messages['fr'] = array(
	'newsextension-desc'          => 'Visionne les modifications récentes spécifiques sur une page wiki ou comme un flux RSS ou Atom.',
	'newsextension-unknownformat' => '$1 : format de flux inconnu : $2<br />',
	'newsextension-feednotfound'  => '$1 : page de flux introuvable : $2<br />',
	'newsextension-feedrequest'   => '$1 : Prise ne charge de la requête pour $2<br />',
	'newsextension-checkok'       => '$1 : cache HTTP correct, en-tête 304 header envoyé</br >',
	'newsextension-checkok1'      => '$1 : vérification du cache correcte :  IMS $2 c/ $3 modifié<br />',
	'newsextension-gotcached'     => '$1 : ($2 ? « obtenu caché » : « non caché »)<br />',
	'newsextension-purge'         => '$1 : purge, cache ignoré<br />',
	'newsextension-loggin'        => '$1 : en session, cache ignoré<br />',
	'newsextension-outputting'    => '$1 : sortie de la copie en cache ($2): $3 < {$4}',
	'newsextension-stale'         => '$1 : trouvé une ancienne copie en cache ($2): $3 >= {$4}<br />',
	'newsextension-nofoundonpage' => '$1 : aucune alimentation de trouvée sur la page : $2<br / >',
	'newsextension-renderedfeed'  => '$1 : alimentation rendue<br />',
	'newsextension-cachingfeed'   => '$1 : cache l’alimentation ($2)<br / >',
	'newsextension-freshfeed'     => '$1 : sortie alimentation récente<br />',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'newsextension-desc' => 'Pokazuje přiměrjene aktualne změny na wikijowych stronach abo jako RSS abo kanal Atom',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 */
$messages['nl'] = array(
	'newsextension-desc'          => 'Aangepaste recente wijzigingen tonen op een wikipagina, of als RSS-feed of Atom-feed',
	'newsextension-unknownformat' => '$1: onbekend feed-formaat: $2<br />',
	'newsextension-feednotfound'  => '$1: feed-pagina niet gevonden: $2<br />',
	'newsextension-feedrequest'   => '$1: bezig met afhandelen van feed-aanvraag voor $2<br />',
	'newsextension-nofoundonpage' => '$1: geen feed gevonden op pagina: $2<br />',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'newsextension-desc' => 'Viser egendefinerte siste endringer på en wikiside, eller en RSS- eller Atom-føde',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'newsextension-desc' => 'Zobrazuje prispôsobené Posledné úpravy na wiki stránkach alebo ako kanál RSS alebo Atom',
);

