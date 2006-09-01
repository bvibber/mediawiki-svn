<?php
/**
 * Internationalisation file for ExpandTemplates extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
*/

$wgExpandTemplatesMessages = array();

$wgExpandTemplatesMessages['en'] = array(
	'expandtemplates'                  => 'Expand templates',
	'expand_templates_intro'           => 'This special page takes some text and expands 
all templates in it recursively. It also expands parser functions like 
<nowiki>{{</nowiki>#if:...}}, and variables like 
<nowiki>{{</nowiki>CURRENTDAY}}&mdash;in fact pretty much everything in double-braces.
It does this by calling the relevant parser stage from MediaWiki itself.',
	'expand_templates_title'           => 'Context title, for <nowiki>{{</nowiki>PAGENAME}} etc.:',
	'expand_templates_input'           => 'Input text:',
	'expand_templates_output'          => 'Result:',
	'expand_templates_ok'              => 'OK',
	'expand_templates_remove_comments' => 'Remove comments',
);
$wgExpandTemplatesMessages['he'] = array(
	'expandtemplates'                  => 'פריסת תבניות',
	'expand_templates_intro'           => 'דף זה מקבל כמות מסוימת של טקסט ופורס ומפרש את כל התבניות שבתוכו באופן רקורסיבי. בנוסף, הוא פורס הוראות פירוש כגון <nowiki>{{</nowiki>#תנאי:...}}, ומשתנים כגון <nowiki>{{</nowiki>יום נוכחי}}, ולמעשה בערך כל דבר בסוגריים מסולסלות כפולות. הוא עושה זאת באמצעות קריאה לפונקציות הפענוח המתאימות מתוך תוכנת מדיה־ויקי עצמה.',
	'expand_templates_title'           => 'כותרת ההקשר לפענוח, בשביל משתנים כגון <nowiki>{{</nowiki>שם הדף}} וכדומה:',
	'expand_templates_input'           => 'טקסט:',
	'expand_templates_output'          => 'תוצאה:',
	'expand_templates_ok'              => 'פרוס תבניות',
	'expand_templates_remove_comments' => 'הסר הערות',
);
?>
