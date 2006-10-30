<?php
/**
 * German language file for the 'Watchers' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'watchers_link_title' => "Wer beobachtet diese Seite?",
		'watchers_error_article' => "<b>Fehler:</b> Seite existiert nicht.",
		'watchers_header' => "Benutzer die \"$1\" beobachten",
		'watchers_noone_watches' => "Es gibt keine Benutzer die diese Seite beobachten.",
		'watchers_x_or_more' => "$1 oder mehr Benutzer beobachten diese Seite.",
		'watchers_less_than_x' => "Weniger als $1 Benutzer beobachten diese Seite.",
	)
);
?>
