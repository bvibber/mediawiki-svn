<?php
/**
 * Dutch language file for the 'Watchers' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'watchers_link_title' => "Wie volgt deze pagina?",
		'watchers_error_article' => "<b>Fout:</b> pagina bestaat niet.",
		'watchers_header' => "Gebruikers die \"$1\" volgen",
		'watchers_noone_watches' => "Niemand volgt deze pagina.",
		'watchers_x_or_more' => "$1 of meer gebruikers volgen deze pagina.",
		'watchers_less_than_x' => "Minder dan $1 gebruikers volgen deze pagina.",
	)
);
?>
