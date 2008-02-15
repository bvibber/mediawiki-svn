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
