<?php

/**
 * Messages file for the BlockTitles extension
 */
 
/**
 * Fetch extension messages
 *
 * @return array
 */
function efBlockTitlesMessages() {
	$messages = array(
	
/**
 * English (Travis Derouin)
 */
'en' => array(
	'block_title_error_page_title' => 'Blocked Title',
	'block_title_error' => 'Sorry, the title of this article is not allowed to be saved.',
),
 
'de' => array(
	'block_title_error_page_title' => 'Sperre Artikelname',
	'block_title_error'            => 'Entschuldigung, aber ein Artikel mit diesem Namen darf nicht gespeichert werden.',
),

/**
 * French (Bertrand Grondin)
 */
'fr' => array(
	'block_title_error_page_title' => 'Titre invalide',		
	'block_title_error' => 'Désolé, le titre de cet article n’est pas autorisé à être sauvegardé.',
),
	
	);
	return $messages;	
}