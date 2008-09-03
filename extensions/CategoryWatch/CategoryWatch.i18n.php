<?php
/**
 * Internationalisation file for extension CategoryWatch.
 *
 * @addtogroup Extensions
*/

$messages = array();

/** English
 * @author Nad
 */
$messages['en'] = array(
	'categorywatch-desc' => 'Extends watchlist functionality to include notification about membership changes of watched categories',
	'categorywatch-emailbody' => "Hi $1, you have received this message because you are watching the \"$2\" category.
This message is to notify you that at $3 user $4 $5.",
	'categorywatch-emailsubject' => "Activity involving watched category \"$1\"",
	'categorywatch-catmovein' => "moved $1 into category $2 from $3",
	'categorywatch-catmoveout' => "moved $1 out of category $2 into $3",
	'categorywatch-catadd' => "added $1 to category $2",
	'categorywatch-catsub' => "removed $1 from category $2",
);
