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
$wgExpandTemplatesMessages['de'] = array(
	'expandtemplates'                  => 'Vorlagen expandieren',
	'expand_templates_intro'           => 'In diese Spezialseite kann Text eingegeben werden und alle Vorlagen in ihr werden rekursiv expandiert. Auch Parserfunkionen wie <nowiki>{{</nowiki>#if:...}} und Variablen wie <nowiki>{{</nowiki>CURRENTDAY}} werden ausgewertet - faktisch alles was in doppelten geschweiften Klammern enthalten ist. Dies geschieht durch den Aufruf der jeweiligen Parser-Phasen in MediaWiki.',
	'expand_templates_title'           => 'Kontexttitel, für <nowiki>{{</nowiki>PAGENAME}} etc.:',
	'expand_templates_input'           => 'Eingabefeld:',
	'expand_templates_output'          => 'Ergebnis:',
	'expand_templates_ok'              => 'Ausführen',
	'expand_templates_remove_comments' => 'Kommentare entfernen',
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
$wgExpandTemplatesMessages['id'] = array(
	'expandtemplates'                  => 'Pengembangan templat',
	'expand_templates_intro'           => 'Halaman istimewa ini menerima teks dan mengembangkan semua templat di dalamnya secara rekursif. Halaman ini juga menerjemahkan semua fungsi parser seperti <nowiki>{{</nowiki>#if:...}}, dan variabel seperti <nowiki>{{</nowiki>CURRENTDAY}}&mdash;bahkan bisa dibilang segala sesuatu yang berada di antara dua tanda kurung. Ini dilakukan dengan memanggil tahapan parser yang sesuai dari MediaWiki.',
	'expand_templates_title'           => 'Judul konteks, untuk <nowiki>{{</nowiki>PAGENAME}} dll.:',
	'expand_templates_input'           => 'Teks sumber:',
	'expand_templates_output'          => 'Hasil:',
	'expand_templates_ok'              => 'Jalankan',
	'expand_templates_remove_comments' => 'Buang komentar',
);
$wgExpandTemplatesMessages['ksh'] = array(
	'expandtemplates'                  => 'Schabloone övverprööfe',
	'expand_templates_intro'           => 'Hee kannß de en Schabloon ußprobėere. Do jiss_enne Oproov_enn, un dann kriß_De dä 
komplädd_oppjelööß, och all di innedren widdo opjeroofene Schabloone, Parrameeter, Funkzjohne, shpezjälle Nahme, 
unn_esu, beß nix mieh övverish eß, wat mer noch oplööse künnt. Wänn jedd_en <nowiki>{{ … }}</nowiki> Klammere 
övverbliet, dann wohr_et unnbikanndt. Do passėet jenau et_sellve wi söns_em Wikki och, nur dat_De hee tirägk_ze 
sinn_krißß wadd_erruß kütt.',
	'expand_templates_title'           => 'Dä Sigge_Tittel, also wat för <nowiki>{{PAGENAME}}</nowiki> uew. ennjefölldt weed:',
	'expand_templates_input'           => 'Wat_De övverprööfe wellß:',
	'expand_templates_output'          => 'Wadd_erruß küdd_eß:',
	'expand_templates_ok'              => 'OK',
	'expand_templates_remove_comments' => 'De ennere Kommëntaare fott_loohße',
);
$wgExpandTemplatesMessages['ru'] = array(
	'expandtemplates'                  => 'Развёртка шаблонов',
	'expand_templates_intro'           => 'Эта служебная страница преобразует текст, рекурсивно разворачивая все шаблоны в нём.
Также развёртке подвергаются все функции парсера (например, <nowiki>{{</nowiki>#if:...}} и переменные (<nowiki>{{</nowiki>CURRENTDAY}} и т.&nbsp;п.) — в общем, всё внутри двойных фигурных скобок.
Это производится корректным образом, с вызовом соответствующего обработчика MediaWiki.',
	'expand_templates_title'           => 'Заголовок страницы для <nowiki>{{</nowiki>PAGENAME}} и т.&nbsp;п.:',
	'expand_templates_input'           => 'Входной текст:',
	'expand_templates_output'          => 'Результат:',
	'expand_templates_ok'              => 'OK',
	'expand_templates_remove_comments' => 'Удалить комментарии',
);
?>
