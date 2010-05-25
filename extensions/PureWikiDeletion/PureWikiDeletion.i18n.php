<source lang="php">
<?php
$messages = array();
 
$messages['en'] = array(
	'purewikideletion' => 'Pure wiki deletion',
        'randomexcludeblank' => 'Random page (exclude blank)',
        'populateblankedpagestable' => 'Populate blanked pages table',
        'purewikideletion-desc' => 'Among other things, causes blanked pages to be redlinked',
        'purewikideletion-pref-watchblank' => 'Add pages I blank to my watchlist',
        'purewikideletion-pref-watchunblank' => 'Add pages I unblank to my watchlist',
        'purewikideletion-blanked' => "A former version of this page was blanked by [[User:$1|$1]] ([[User talk:$1|talk]]) "
            . "([[Special:Contributions/$1|contribs]]) on $2 <br /> The reason given for blanking was: "
            . "''<nowiki>$3</nowiki>''.<br /> You may [{{fullurl:{{FULLPAGENAMEE}}|action=history}} view the article's "
            . "history], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} edit the last version], or type new "
            . "page into the white space below.",
        'blank-log'      =>  'blank',
        'blank-log-name' => 'Blank log',
        'blank-log-header' => 'Below is a list of page blankings and unblankings.',
        'blank-log-entry-blank' => 'blanked $1',
        'blank-log-entry-unblank' => 'unblanked $1',
        'blank-log-link' => '[[Special:Log/blank|blank log]]',
        'blanknologin' => 'Not logged in',
        'blanknologintext' => 'You must be a registered user and '
        . '[[Special:UserLogin|logged in]] to blank or unblank a page.',
        'blankedtext' => '[[$1]] has been blanked. See $2 for a record of recent blankings.'
);

$messages['ar'] = array(
	'purewikideletion' => 'حذف الويكي النقي',
        'randomexcludeblank' => 'صفحة عشوائية (لا يشمل ذلك الفارغة)',
        'populateblankedpagestable' => 'املأ جدول الصفحات الفارغة',
        'purewikideletion-desc' => 'من ضمن أشياء أخرى، يؤدي إلى أن تكون الصفحات الفارغة ذات وصلات حمراء',
        'purewikideletion-pref-watchblank' => 'أضف الفصفحات التي أفرغها إلى قائمة مراقبتي',
        'purewikideletion-pref-watchunblank' => 'أضف الصفحات التي أملؤها إلى قائمة مراقبتي',
        'purewikideletion-blanked' => "نسخة سابقة من هذه الصفحة تم إفراغها بواسطة [[User:$1|$1]] ([[User talk:$1|نقاش]]) "
            . "([[Special:Contributions/$1|مساهمات]]) في $2 <br /> السبب المعطى للإفراغ كان: "
            . "''<nowiki>$3</nowiki>''.<br /> يمكنك [{{fullurl:{{FULLPAGENAMEE}}|action=history}} رؤية تاريخ المقالة "
            . "], [{{fullurl:{{FULLPAGENAMEE}}|oldid=$4&action=edit}} تعديل آخر نسخة]، أو كتابة صفحة جديدة في الفراغ الأبيض بالأسفل.",
        'blank-log'      =>  'فارغة',
        'blank-log-name' => 'سجل الإفراغ',
        'blank-log-header' => 'بالأسفل قائمة بعمليات إفراغ وملأ الصفحات.',
        'blank-log-entry-blank' => 'أفرغ $1',
        'blank-log-entry-unblank' => 'ملأ $1',
        'blanknologin' => 'غير مسجل الدخول',
        'blanknologintext' => 'يجب أن تكون مستخدما مسجلا و '
        . '[[Special:UserLogin|تسجل الدخول]] لإفراغ أو ملأ صفحة.'
);