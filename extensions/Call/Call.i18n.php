<?php
/*
 * Internationalization file for Call Extension
 *
 * @addGroup Extension
 */

$messages = array();

$messages['en'] = array(
	'call' => 'Call',
	'call-text' => 'The Call extension expects a wiki page and optional parameters for that page as an argument.<br><br>
Example 1: &nbsp; <tt>[[Special:Call/My Template,parm1=value1]]</tt><br/>
Example 2: &nbsp; <tt>[[Special:Call/Talk:My Discussion,parm1=value1]]</tt><br/>
Example 3: &nbsp; <tt>[[Special:Call/:My Page,parm1=value1,parm2=value2]]</tt><br/><br/>
Example 4 (Browser URL): &nbsp; <tt>http://mydomain/mywiki/index.php?Special:Call/:My Page,parm1=value1</tt><br/><br/>

The <i>Call extension</i> will call the given page and pass the parameters.<br>You will see the contents of the called page and its title but its \'type\' will be that of a special page,<br>i.e. such a page cannot be edited.<br>The contents you see may vary depending on the value of the parameters you passed.<br><br>
The <i>Call extension</i> is useful to build interactive applications with MediaWiki.<br>For an example see <a href=\'http://semeb.com/dpldemo/Template:Catlist\'>the DPL GUI</a> ..<br/>
In case of problems you can try <b>Special:Call/DebuG</b>',
	'call-save' => 'The output of this call would be saved to a page called \'\'$1\'\'.',
	'call-save-success' => 'The following text has been saved to page <big>[[$1]]</big> .',
	'call-save-failed' => 'The following text has NOT been saved to page <big>[[$1]]</big> because that page already exists.',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'call'              => 'استدعاء',
	'call-text'         => "امتداد الاستدعاء يتوقع صفحة ويكي ومحددات اختيارية لهذه الصفحة كمدخلات.<br><br>
مثال 1: &nbsp; <tt>[[Special:Call/My Template,parm1=value1]]</tt><br/>
مثال 2: &nbsp; <tt>[[Special:Call/Talk:My Discussion,parm1=value1]]</tt><br/>
مثال 3: &nbsp; <tt>[[Special:Call/:My Page,parm1=value1,parm2=value2]]</tt><br/><br/>
مثال 4 (مسار متصفح): &nbsp; <tt>http://mydomain/mywiki/index.php?Special:Call/:My Page,parm1=value1</tt><br/><br/>

<i>امتداد الاستدعاء</i> سيستدعي الصفحة المعطاة ويمرر المحددات.<br>سترى محتويات الصفحة المستدعاة وعنوانها ولكن 'نوعها' سيكون ذلك الخاص بصفحة خاصة,<br>أي أن صفحة مثل هذه لا يمكن تعديلها.<br>المحتويات التي تراها ربما تتغير على حسب قيمة المحددات التي مررتها.<br><br>
<i>امتداد الاستدعاء</i> مفيد في بناء تطبيقات تفاعلية مع الميدياويكي.<br>لمثال انظر <a href='http://semeb.com/dpldemo/Template:Catlist'>DPL GUI</a> ..<br/>
في حالة وجود مشكلات يمكنك محاولة <b>Special:Call/DebuG</b>",
	'call-save'         => "ناتج هذا الاستدعاء سيتم حفظه في صفحة اسمها ''$1''.",
	'call-save-success' => 'النص التالي تم حفظه لصفحة <big>[[$1]]</big> .',
	'call-save-failed'  => 'النص التالي لم يتم حفظه لصفحة <big>[[$1]]</big> لأن هذه الصفحة موجودة بالفعل.',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'call-save-success' => 'Следният текст беше съхранен на страницата <big>[[$1]]</big> .',
	'call-save-failed'  => 'Следният текст НЕ БЕШЕ съхранен на страницата <big>[[$1]]</big>, тъй като тя вече съществува.',
);

$messages['fr'] = array(
	'call' => 'Appel',
	'call-text' => 'L’extension Appel a besoin d’une page wiki et des paramètres facultatifs pour cette dernière.<br><br>
Example 1: &nbsp; <tt>[[Special:Call/Mon modèle,parm1=value1]]</tt><br/>
Example 2: &nbsp; <tt>[[Special:Call/Discussion:Ma discussion,parm1=value1]]</tt><br/>
Example 3: &nbsp; <tt>[[Special:Call/:Ma page,parm1=value1,parm2=value2]]</tt><br/><br/>
Example 4 (Adresse pour navigateur) : &nbsp; <tt>http://mondomaine/monwiki/index.php?Special:Call/:Ma_Page,parm1=value1</tt><br/><br/>

L’extension <i>Appel</i> appellera la page indiquée en y passant les paramètres.<br>Vous verrez les informations de cette page, son titre, mais son « type » sera celui d’une page spéciale mais ne pourra pas être éditée.<br>Les informations que vous verrez varierons en fonction des paramètres que vous aurez indiqués.<br>Cette extension est très pratique pour créer des applications interactives avec MediaWiki.<br>À titre d’exemple, voyez <a href=\'http://semeb.com/dpldemo/Template:Catlist\'>the DPL GUI</a> ..<br/>En cas de problèmes, vous pouvez essayer <b>Special:Call/DebuG</b>',
	'call-save' => 'Ce qui est indiqué par cet appel pourrait être sauvé vers une page intitulée \'\'$1\'\'.',
	'call-save-success' => 'Le texte suivant a été sauvegardé vers la page <big>[[$1]]</big> .',
	'call-save-failed' => 'Le texte suivant n’a pu être sauvergardé vers la page <big>[[$1]]</big> du fait qu’elle existe déjà.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'call'              => 'Aanroepen',
	'call-text'         => "De extensie Aanroepen (Call) verwacht een wikipagina en optioneel parameters voor die pagina.<br /><br />
Voorbeeld 1: &nbsp; <tt>[[Special:Call/Mijn sjabloon,parm1=value1]]</tt><br />
Voorbeeld 2: &nbsp; <tt>[[Special:Call/Overleg:Een overleg,parm1=value1]]</tt><br />
Voorbeeld 3: &nbsp; <tt>[[Special:Call/:My Page,parm1=value1,parm2=value2]]</tt><br /><br />
Voorbeeld 4 (Browser URL): &nbsp; <tt>http://mydomain/mywiki/index.php?Special:Call/:My Page,parm1=value1</tt><br /><br />

De <i>extensie Aanroepen</i> roept de opgegeven pagina aan en geeft de parameters door.<br />U krijgt de inhoud van de aangeroepen pagina te zien en de naam, maar deze is van het 'type' speciale pagina, dat wil zeggen dat de pagina niet bewerkt kan worden.<br />De inhoud die u te zien krijgt kan verschillen, afhankelijk van de parameters die u heeft meegegeven.<br /><br />
De <i>extensie Aanroepen</i> kan behulpzaam zijn bij het bouwen van interactieve applicaties met MediaWiki. De <a href='http://semeb.com/dpldemo/Template:Catlist'>DPL GUI</a> is daar een voorbeeld van.<br />
Bij problemen kan u gebruik maken van <b>Special:Call/DebuG</b>",
	'call-save'         => "De uitvoer van deze aanroep zou opgeslagen zijn in de pagina ''$1''.",
	'call-save-success' => 'De volgende tekst is opgeslagen in pagina <big>[[$1]]</big>.',
	'call-save-failed'  => 'De volgende tekst is NIET opgeslagen in pagina <big>[[$1]]</big> omdat die pagina al bestaat.',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'call'              => 'Call',
	'call-text'         => "Rozšírenie Call očakáva ako argumenty stránku wiki a voliteľné parametre danej stránky.<br><br>
Príklad 1: &nbsp; <tt>[[Special:Call/Moja šablóna,parm1=value1]]</tt><br/>
Príklad 2: &nbsp; <tt>[[Special:Call/Diskusia:Moja diskusia,parm1=value1]]</tt><br/>
Príklad 3: &nbsp; <tt>[[Special:Call/:Moja stránka,parm1=value1,parm2=value2]]</tt><br/><br/>
Príklad 4 (URL prehliadača): &nbsp; <tt>http://mojadoména/mojawiki/index.php?Special:Call/:Moja stránka,parm1=value1</tt><br/><br/>

<i>Rozšírenie Call</i> zavolá danú stránku a odovzdá jej parametre.<br>
Uvidiíte obsah zavolanej stránky a jej názov, ale jej ''typ'' bude špeciálna stránka,<br>
t.j. takú stránku nie je možné upravovať.<br>
Obsah, ktorý uvidíte sa môže líšiť v závislosti od parametrov, ktoré ste odovzdali.<br><br>
<i>Rozšírenie Call</i> je užitočné pri budovaní interaktívnych aplikácií pomocou MediaWiki.<br>
Ako príklad si môžete pozrieť <a href='http://semeb.com/dpldemo/Template:Catlist'>GUI DPL</a> ..<br/>
V prípade problémov môžete skúsuť <b>Special:Call/DebuG</b>",
	'call-save'         => "Výstup tejto stránky by bol uložený na stránku s názvom ''$1''.",
	'call-save-success' => 'Nasledovný text bol uložený na stránku <big>[[$1]]</big>.',
	'call-save-failed'  => "Nasledovný text NEBOL uložený na stránku ''$1'', pretože taká stránka už existuje.",
);

