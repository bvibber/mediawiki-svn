<?php
/**
 * French language file for the 'Watchers' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'watchers_link_title' => "Qui suit cette page ?",
		'watchers_error_article' => "<b>Erreur :</b> La page n’existe pas.",
		'watchers_header' => "Personnes qui suivent « $1 »",
		'watchers_noone_watches' => "Personne ne suit cette page.",
		'watchers_x_or_more' => "Au moins $1 utilisateur(s) suit cette page.",
		'watchers_less_than_x' => "Moins de $1 utilisateurs suivent cette page.",
	)
);

