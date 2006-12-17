<?php

/**
 * Internationalisation file for the MakeBot extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Rob Church <robchur@gmail.com>
 * @copyright © 2006 Rob Church
 * @licence GNU General Public Licence 2.0 or later
 */

function efMakeBotMessages() {
	$messages = array(
	
'en' => array(
'makebot' => 'Grant or revoke bot status',
'makebot-header' => "'''A local bureaucrat can use this page to grant or revoke [[Help:Bot|bot status]] to another user account.'''<br />Bot status hides a user's edits from [[Special:Recentchanges|recent changes]] and similar lists, and is useful for flagging users who make automated edits. This should be done in accordance with applicable policies.",
'makebot-username' => 'Username:',
'makebot-search' => 'Go',
'makebot-isbot' => '[[User:$1|$1]] has bot status.',
'makebot-notbot' => '[[User:$1|$1]] does not have bot status.',
'makebot-privileged' => '[[User:$1|$1]] has [[Special:Listadmins|administrator or bureaucrat privileges]], and cannot be granted bot status.',
'makebot-change' => 'Change status:',
'makebot-grant' => 'Grant',
'makebot-revoke' => 'Revoke',
'makebot-comment' => 'Comment:',
'makebot-granted' => '[[User:$1|$1]] now has bot status.',
'makebot-revoked' => '[[User:$1|$1]] no longer has bot status.',
'makebot-logpage' => 'Bot status log',
'makebot-logpagetext' => 'This is a log of changes to users\' [[Help:Bot|bot]] status.',
'makebot-logentrygrant' => 'granted bot status to [[$1]]',
'makebot-logentryrevoke' => 'removed bot status from [[$1]]',
),

'he' => array(
'makebot'          => 'הענק או בטל הרשאת בוט',
'makebot-header'   => "'''ביורוקרט מקומי יכול להשתמש בדף זה כדי להעניק או לבטל [[{{ns:help}}:בוט|הרשאת בוט]] למשתמש אחר.'''<br />הרשאת בוט מסתירה את עריכותיו של המשתמש מ[[{{ns:special}}:Recentchanges|השינויים האחרונים]] ורשימות דומות, ושימושי למשתמשים המבצעים עריכות אוטומטיות. יש להעניק הרשאת בוט אך ורק לפי הנהלים המתאימים.",
'makebot-username' => 'שם משתמש:',
'makebot-search'   => 'עבור',
'makebot-isbot'      => 'למשתמש [[{{ns:user}}:$1|$1]] יש הרשאת בוט.',
'makebot-notbot'     => 'למשתמש [[{{ns:user}}:$1|$1]] אין הרשאת בוט.',
'makebot-privileged' => 'למשתמש [[{{ns:user}}:$1|$1]] יש כבר [[{{ns:special}}:Listadmins|הרשאות מפעיל מערכת או ביורוקרט]], ולפיכך אי אפשר להעניק לו דגל בוט.',
'makebot-change'     => 'מה לבצע:',
'makebot-grant'      => 'הענקת הרשאה',
'makebot-revoke'     => 'ביטול הרשאה',
'makebot-comment'    => 'סיבה:',
'makebot-granted'    => 'המשתמש [[{{ns:user}}:$1|$1]] קיבל הרשאת בוט.',
'makebot-revoked'    => 'הרשאת הבוט של המשתמש [[{{ns:user}}:$1|$1]] הוסרה בהצלחה.',
'makebot-logpage'        => 'יומן הרשאות בוט',
'makebot-logpagetext'    => 'זהו יומן השינויים בהרשאות ה[[{{ns:help}}:בוט|בוט]] של המשתמשים.',
'makebot-logentrygrant'  => 'העניק הרשאת בוט למשתמש [[$1]]',
'makebot-logentryrevoke' => 'ביטל את הרשאת הבוט למשתמש [[$1]]',
),
	);
	return $messages;
}

?>