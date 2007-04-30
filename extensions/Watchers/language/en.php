<?php
/**
 * English language file for the 'Watchers' extension
 */

// We will add messages to the global cache
global $wgMessageCache;

// Add messages
$wgMessageCache->addMessages(
	array(
		'watchers_link_title' => "Who watches this page?",
		'watchers_error_article' => "<b>Error:</b> Article does not exist.",
		'watchers_header' => "People who are watching \"$1\"",
		'watchers_noone_watches' => "Noone watches this page.",
		'watchers_x_or_more' => "$1 or more people are watching this page.",
		'watchers_less_than_x' => "Less than $1 people watch this page.",
	)
);
?>
