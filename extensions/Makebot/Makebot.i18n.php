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
	
/* English (Rob Church) */
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

/* Italian (BrokenArrow) */
'it' => array(
'makebot' => 'Assegna o revoca lo status di bot',
'makebot-header' => "'''Questa pagina consente ai burocrati di assegnare o revocare lo [[{{ns:help}}:Bot|status di bot]] a un'altra utenza.'''<br /> Tale status nasconde le modifiche effettuate dall'utenza nell'elenco delle [[{{ns:special}}:Recentchanges|ultime modifiche]] e nelle liste simili; è utile per contrassegnare le utenze che effettuano modifiche in automatico. Tale operazione dev'essere effettuata in conformità con le policy del sito.",
'makebot-username' => 'Nome utente:',
'makebot-search' => 'Vai',
'makebot-isbot' => 'L\'utente [[{{ns:user}}:$1|$1]] ha lo status di bot.',
'makebot-notbot' => 'L\'utente [[{{ns:user}}:$1|$1]] non ha lo status di bot.',
'makebot-privileged' => 'L\'utente [[{{ns:user}}:$1|$1]] possiede i privilegi di [[Special:Listadmins|amministratore o burocrate privileges]], che sono incompatibili con lo status di bot.',
'makebot-change' => 'Modifica lo status:',
'makebot-grant' => 'Concedi',
'makebot-revoke' => 'Revoca',
'makebot-comment' => 'Commento:',
'makebot-granted' => 'L\'utente [[{{ns:user}}:$1|$1]] ha ora lo status di bot.',
'makebot-revoked' => 'L\'utente [[{{ns:user}}:$1|$1]] non ha più lo status di bot.',
'makebot-logpage' => 'Registro dei bot',
'makebot-logpagetext' => 'Qui di seguito viene riportata la lista dei cambiamenti di status dei [[{{ns:help}}:bot]].',
'makebot-logentrygrant' => 'ha concesso lo status di bot a [[$1]]',
'makebot-logentryrevoke' => 'ha revocato lo status di bot a [[$1]]',
),

/* Hebrew (Rotem Liss) */
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

/* German (Raymond) */
'de' => array(
'makebot' => 'Botstatus erteilen oder entziehen',
'makebot-header' => "'''Ein Bürokrat dieses Projektes kann anderen Benutzern – in Übereinstimmung mit den lokalen Richtlinien – [[Help:Bot|Botstatus]] erteilen oder entziehen.'''<br /> Mit Botstatus werden die Bearbeitungen eines Bot-Benutzerkontos in den [[Special:Recentchanges|Letzten Änderungen]] und ähnlichen Listen versteckt. Die Botmarkierung ist darüberhinaus zur Feststellung automatischer Bearbeitungen nützlich.",
'makebot-username' => 'Benutzername:',
'makebot-search' => 'Ausführen',
'makebot-isbot' => '[[User:$1|$1]] hat Botstatus.',
'makebot-notbot' => '[[User:$1|$1]] hat keinen Botstatus.',
'makebot-privileged' => '[[User:$1|$1]] hat [[Special:Listusers/sysop|Administrator- oder Bürokratenrechte]], Botstatus kann nicht erteilt werden.',
'makebot-change' => 'Status ändern:',
'makebot-grant' => 'Erteilen',
'makebot-revoke' => 'Zurücknehmen',
'makebot-comment' => 'Kommentar:',
'makebot-granted' => '[[User:$1|$1]] hat nun Botstatus.',
'makebot-revoked' => '[[User:$1|$1]] hat keinen Botstatus mehr.',
'makebot-logpage' => 'Botstatus-Logbuch',
'makebot-logpagetext' => 'Dieses Logbuch protokolliert alle [[Help:Bot|Botstatus]]-Änderungen.',
'makebot-logentrygrant' => 'erteilte Botstatus für [[$1]]',
'makebot-logentryrevoke' => 'entfernte den Botstatus von [[$1]]',
),

	);
	return $messages;
}
?>

