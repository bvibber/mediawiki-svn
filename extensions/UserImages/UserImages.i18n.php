<?php

/**
 * Internationalisation file for User Image Gallery extension
*
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 */

function efUserImagesMessages() {
	$messages = array(

/* English */
'en' => array(
'userimages-caption' => 'Images uploaded by $1',
'userimages-noname' => 'Invalid username or none provided.',
'userimages-noimages' => '$1 has no image uploads.',
),
/* French */
'fr' => array(
'userimages-caption' => 'Images importées par $1',
'userimages-noname' => 'Nom d’utilisateur invalide ou manquant.',
'userimages-noimages' => '$1 n’a importé aucune image.',
),

	);
	return $messages;
}

?>