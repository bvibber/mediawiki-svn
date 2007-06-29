<?php
/**
 * Slovak language file for the 'Watchers' extension by helix84
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'watchers_link_title' => "Kto sleduje túto stránku?",
		'watchers_error_article' => "<b>Chyba:</b> Článok neexistuje.",
		'watchers_header' => "Ľudia sledujúci stránku \"$1\"",
		'watchers_noone_watches' => "Nikto nesleduje túto stránku.",
		'watchers_x_or_more' => "$1 alebo viac ľudí sleduje túto stránku.",
		'watchers_less_than_x' => "Menej ako $1 ľudí sleduje túto stránku.",
	)
);

