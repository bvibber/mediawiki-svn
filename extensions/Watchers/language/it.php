<?php
/**
 * Italian language file for the 'Watchers' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'watchers_link_title' => "Chi osserva questa pagina?",
		'watchers_error_article' => "<b>Errore:</b> la pagina richiesta non esiste.",
		'watchers_header' => "Utenti che osservano la pagina \"$1\"",
		'watchers_noone_watches' => "La pagina non è osservata da alcun utente.",
		'watchers_x_or_more' => "La pagina è osservata da almeno $1 utenti.",
		'watchers_less_than_x' => "La pagina è osservata da meno di $1 utenti.",
	)
);

