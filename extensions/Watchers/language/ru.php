<?php
/**
 * Russian language file for the 'Watchers' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'watchers_link_title' => "Кто следит за этой страницей?",
		'watchers_error_article' => "<b>ошибка:</b> статьи не существует.",
		'watchers_header' => "Луди надлюдающие за «$1»",
		'watchers_noone_watches' => "Никто не следит за этой страницей.",
		'watchers_x_or_more' => "$1 или более человек наблюдают за этой страницей.",
		'watchers_less_than_x' => "Менее чем $1 человек наблюдают за этой страницей.",
	)
);
?>
