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
$wgExpandTemplatesMessages['cs'] = array(
	'expandtemplates'                  => 'Substituovat šablony',
	'expand_templates_intro'           => 'Pomocí této speciální stránky můžete nechat v textu substituovat všechny šablony a funkce parseru jako <code><nowiki>{{</nowiki>#if:…...}}</code> či proměnné jako <code><nowiki>{{</nowiki>CURRENTDAY}} – tzn. prakticky všechno v dvojitých složených závorkách. K tomu se používají přímo odpovídající funkce parseru MediaWiki.',
	'expand_templates_title'           => 'Název stránky kvůli kontextu pro <code><nowiki>{{</nowiki>PAGENAME}}</code> apod.:',
	'expand_templates_input'           => 'Vstupní text:',
	'expand_templates_output'          => 'Výstup:',
	'expand_templates_ok'              => 'OK',
	'expand_templates_remove_comments' => 'Odstranit komentáře',
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
$wgExpandTemplatesMessages['kk-kz'] = array(
	'expandtemplates'                  => 'Үлгілерді ұлғайту',
	'expand_templates_intro'           => 'Осы құрал арнайы беті әлдебір мәтінді алады да,
бұның ішіндегі барлық кіріктелген үлгілерді мейлінше ұлғайтады.
Мына <nowiki>{{</nowiki>#if:...}} сияқты жөңдету функцияларын да, және <nowiki>{{</nowiki>CURRENTDAY}}
сияқты айнамалыларын да ұлғайтады (нақты айтқанда, қос қабат садақ жақшалар арасындағы барлығын).
Бұны өз MediaWiki бағдарламасынан қатысты жөңдету сатын шақырып істелінеді.',
	'expand_templates_title'           => '<nowiki>{{</nowiki>PAGENAME}} т.б. беттер үшін мәтін аралық атауы:',
	'expand_templates_input'           => 'Кіріс мәтіні:',
	'expand_templates_output'          => 'Нәтижесі:',
	'expand_templates_ok'              => 'Жарайды',
	'expand_templates_remove_comments' => 'Мәндемелерін аластатып?',
);
$wgExpandTemplatesMessages['kk-tr'] = array(
	'expandtemplates'                  => 'Ülgilerdi ulğaýtw',
	'expand_templates_intro'           => 'Osı qural arnaýı beti äldebir mätindi aladı da,
bunıñ işindegi barlıq kiriktelgen ülgilerdi meýlinşe ulğaýtadı.
Mına <nowiki>{{</nowiki>#if:...}} sïyaqtı jöñdetw fwnkcïyaların da, jäne <nowiki>{{</nowiki>CURRENTDAY}}
sïyaqtı aýnamalıların da ulğaýtadı (naqtı aýtqanda, qos qabat sadaq jaqşalar arasındağı barlığın).
Bunı öz MediaWiki bağdarlamasınan qatıstı jöñdetw satın şaqırıp istelinedi.',
	'expand_templates_title'           => '<nowiki>{{</nowiki>PAGENAME}} t.b. better üşin mätin aralıq atawı:',
	'expand_templates_input'           => 'Kiris mätini:',
	'expand_templates_output'          => 'Nätïjesi:',
	'expand_templates_ok'              => 'Jaraýdı',
	'expand_templates_remove_comments' => 'Mändemelerin alastatıp?',
);
$wgExpandTemplatesMessages['kk-cn'] = array(
	'expandtemplates'                  => 'ٴۇلگٴىلەردٴى ۇلعايتۋ',
	'expand_templates_intro'           => 'وسى قۇرال ارنايى بەتٴى ٴالدەبٴىر مٴاتٴىندٴى الادى دا,
بۇنىڭ ٴىشٴىندەگٴى بارلىق كٴىرٴىكتەلگەن ٴۇلگٴىلەردٴى مەيلٴىنشە ۇلعايتادى.
مىنا <nowiki>{{</nowiki>#if:...}} سيياقتى جٴوڭدەتۋ فۋنكتسييالارىن دا, جٴانە <nowiki>{{</nowiki>CURRENTDAY}}
سيياقتى اينامالىلارىن دا ۇلعايتادى (ناقتى ايتقاندا, قوس قابات ساداق جاقشالار اراسىنداعى بارلىعىن).
بۇنى ٴوز MediaWiki باعدارلاماسىنان قاتىستى جٴوڭدەتۋ ساتىن شاقىرىپ ٴىستەلٴىنەدٴى.',
	'expand_templates_title'           => '<nowiki>{{</nowiki>PAGENAME}} ت.ب. بەتتەر ٴۇشٴىن مٴاتٴىن ارالىق اتاۋى:',
	'expand_templates_input'           => 'كٴىرٴىس مٴاتٴىنٴى:',
	'expand_templates_output'          => 'نٴاتيجەسٴى:',
	'expand_templates_ok'              => 'جارايدى',
	'expand_templates_remove_comments' => 'مٴاندەمەلەرٴىن الاستاتىپ?',
);
$wgExpandTemplatesMessages['kk'] = $wgExpandTemplatesMessages['kk-kz'];
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
$wgExpandTemplatesMessages['nl'] = array(
	'expandtemplates'                  => 'Sjablonen substitueren',
	'expand_templates_intro'           => 'Deze speciale pagina leest de ingegeven tekst in en
substitueert recursief alle sjablonen in de tekst.
Het substitueert ook alle parserfuncties zoals <nowiki>{{</nowiki>#if:...}} en
variabelen als <nowiki>{{</nowiki>CURRENTDAY}} &mdash; vrijwel alles tussen dubbele accolades.
Hiervoor worden de relevante functies van de MediaWiki-parser gebruikt.',
	'expand_templates_title'           => 'Contexttitel, voor <nowiki>{{</nowiki>PAGENAME}}, enzovoort:',
	'expand_templates_input'           => 'Inputtekst:',
	'expand_templates_output'          => 'Resultaat:',
	'expand_templates_remove_comments' => 'Verwijder opmerkingen',
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
$wgExpandTemplatesMessages['sk'] = array(
	'expandtemplates'                  => 'Substituovať šablóny',
	'expand_templates_intro'           => 'Táto špeciálna stránka prijme na
vstup text a rekurzívne substituuje všetky šablóny,
ktoré sú v ňom použité. Tiež expanduje funkcie parsera
ako <nowiki>{{</nowiki>#if:...}} a premenné ako
<nowiki>{{</nowiki>CURRENTDAY}}&mdash;v podstate
takmer všetko v zložených zátvorkách. Robí to pomocou
volania relevantnej fázy parsera samotného MediaWiki.',
	'expand_templates_title'           => 'Názov kontextu pre <nowiki>{{</nowiki>PAGENAME}} atď.:',
	'expand_templates_input'           => 'Vstupný text:',
	'expand_templates_output'          => 'Výsledok:',
	'expand_templates_ok'              => 'OK',
	'expand_templates_remove_comments' => 'Odstrániť komentáre',
);
?>
