<?php
/**
 * Portuguese language file for the 'Watchers' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'watchers_link_title' => "Quem está vigiando esta página?",
		'watchers_error_article' => "<b>Erro:</b> Página inexistente.",
		'watchers_header' => "Pessoas que estão vigiando \"$1\"",
		'watchers_noone_watches' => "Ninguém está vigiando esta página.",
		'watchers_x_or_more' => "$1 ou mais pessoas estão vigiando esta página.",
		'watchers_less_than_x' => "Menos de $1 pessoa está vigiando esta página.",
	)
);

