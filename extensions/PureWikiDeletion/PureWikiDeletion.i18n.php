<?php
$messages = array();
 
$messages['en'] = array(
	'purewikideletion' => 'Pure wiki deletion',
	'randomexcludeblank' => 'Random page (exclude blank)',
	'populateblankedpagestable' => 'Populate blanked pages table',
	'purewikideletion-desc' => 'Among other things, causes blanked pages to be redlinked',
	'purewikideletion-pref-watchblank' => 'Add pages I blank to my watchlist',
	'purewikideletion-pref-watchunblank' => 'Add pages I unblank to my watchlist',
	'purewikideletion-blanked' => "A former version of this page was blanked by [[User:$1|$1]] ([[User talk:$1|talk]]) ([[Special:Contributions/$1|contribs]]) on $2
        
The reason given for blanking was: ''<nowiki>$3</nowiki>''.

You may [{{fullurl:{{FULLPAGENAMEE}}|action=history}} view the article's history], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} edit the last version], or type new page into the white space below.",
	'blank-log' => 'blank',
	'blank-log-name' => 'Blank log',
	'blank-log-header' => 'Below is a list of page blankings and unblankings.',
	'blank-log-entry-blank' => 'blanked $1',
	'blank-log-entry-unblank' => 'unblanked $1',
	'blank-log-link' => '[[Special:Log/blank|blank log]]',
	'purewikideletion-blanknologin' => 'Not logged in',
	'purewikideletion-blanknologintext' => 'You must be a registered user and [[Special:UserLogin|logged in]] to blank a page.',
	'purewikideletion-unblanknologintext' => 'You must be a registered user and [[Special:UserLogin|logged in]] to unblank a page.',
	'purewikideletion-blankedtext' => '[[$1]] has been blanked.
See $2 for a record of recent blankings.',
	'purewikideletion-population-done' => 'Done populating blanked_page table.',
	'right-purewikideletion' => '[[Special:PopulateBlankedPagesTable|Populate]] the blanked pages table',
);

/** Arabic (العربية) */
$messages['ar'] = array(
	'purewikideletion' => 'حذف الويكي النقي',
	'randomexcludeblank' => 'صفحة عشوائية (لا يشمل ذلك الفارغة)',
	'populateblankedpagestable' => 'املأ جدول الصفحات الفارغة',
	'purewikideletion-desc' => 'من ضمن أشياء أخرى، يؤدي إلى أن تكون الصفحات الفارغة ذات وصلات حمراء',
	'purewikideletion-pref-watchblank' => 'أضف الفصفحات التي أفرغها إلى قائمة مراقبتي',
	'purewikideletion-pref-watchunblank' => 'أضف الصفحات التي أملؤها إلى قائمة مراقبتي',
	'purewikideletion-blanked' => "نسخة سابقة من هذه الصفحة تم إفراغها بواسطة [[User:$1|$1]] ([[User talk:$1|نقاش]]) ([[Special:Contributions/$1|مساهمات]]) في $2
	
السبب المعطى للإفراغ كان: ''<nowiki>$3</nowiki>''.

يمكنك [{{fullurl:{{FULLPAGENAMEE}}|action=history}} رؤية تاريخ المقالة], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} تعديل آخر نسخة]،
أو كتابة صفحة جديدة في الفراغ الأبيض بالأسفل.",
	'blank-log' => 'فارغة',
	'blank-log-name' => 'سجل الإفراغ',
	'blank-log-header' => 'بالأسفل قائمة بعمليات إفراغ وملأ الصفحات.',
	'blank-log-entry-blank' => 'أفرغ $1',
	'blank-log-entry-unblank' => 'ملأ $1',
	'purewikideletion-blanknologin' => 'غير مسجل الدخول',
	'purewikideletion-blanknologintext' => 'يجب أن تكون مستخدما مسجلا و [[Special:UserLogin|تسجل الدخول]] لإفراغ أو ملأ صفحة.',
);

/** Breton (Brezhoneg)
 * @author Y-M D
 */
$messages['br'] = array(
	'purewikideletion-blanknologin' => 'Digevreet',
);

/** German (Deutsch)
 * @author The Evil IP address
 */
$messages['de'] = array(
	'purewikideletion-desc' => 'Sorgt unter anderem dafür, dass geleerte Seiten als Rotlink erscheinen',
	'purewikideletion-pref-watchblank' => 'Selbst geleerte Seiten automatisch beobachten',
	'purewikideletion-pref-watchunblank' => 'Selbst entleerte Seiten automatisch beobachten',
	'purewikideletion-blanked' => "Eine ehemalige Version dieser Seiten wurde von [[User:$1|$1]] ([[User talk:$1|talk]]) ([[Special:Contributions/$1|contribs]]) am $2 geleert.

Begründung: ''<nowiki>$3</nowiki>''.

Du kannst [{{fullurl:{{FULLPAGENAMEE}}|action=history}} die Versionsgeschichte betrachten], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$2&action=edit}} die letzte Version bearbeiten] oder eine neue Seite unten eingeben.",
	'blank-log' => 'leeren',
	'blank-log-name' => 'Leerungs-Logbuch',
	'blank-log-header' => 'Es folgt eine Liste von Seitenleerungen und -Entleerungen.',
	'blank-log-entry-blank' => 'leerte „$1“',
	'blank-log-entry-unblank' => 'entleerte „$1“',
	'blank-log-link' => '[[Special:Log/blank|Leerungs-Logbuch]]',
	'purewikideletion-blanknologin' => 'Nicht angemeldet',
	'purewikideletion-blanknologintext' => 'Du musst ein registrierter Benutzer und [[Special:UserLogin|angemeldet sein]], um eine Seite zu leeren.',
	'purewikideletion-unblanknologintext' => 'Du musst ein registrierter Benutzer und [[Special:UserLogin|angemeldet sein]], um eine Seite zu entleeren.',
	'purewikideletion-blankedtext' => '„[[$1]]“ wurde geleert.
Siehe das $2 für eine Liste der letzten Leerungen.',
);

