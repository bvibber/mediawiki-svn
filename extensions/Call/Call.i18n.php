<?php
/*
 * Internationalization file for Call Extension
 *
 * @addGroup Extension
 */

$messages = array();

$messages['en'] = array(
	'call' => 'Call',
	'call-desc' => 'Create a hyperlink to a template (or to a normal wiki page) with parameter passing. Can be used at the browser’s command line or within wiki text.',
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
	'call'              => 'Извикване',
	'call-save-success' => 'Следният текст беше съхранен на страницата <big>[[$1]]</big> .',
	'call-save-failed'  => 'Следният текст НЕ БЕШЕ съхранен на страницата <big>[[$1]]</big>, тъй като тя вече съществува.',
);

/** Breton (Brezhoneg)
 * @author Fulup
 */
$messages['br'] = array(
	'call' => 'Galv',
);

/** Czech (Česky)
 * @author Matěj Grabovský
 */
$messages['cs'] = array(
	'call-save-success' => 'Následující text byl uložený do stránky <big>[[$1]]</big>',
	'call-save-failed'  => "Následující text NEBYL uložený do stránky ''$1'', protože tato stránka už existuje.",
);

/** Finnish (Suomi)
 * @author Cimon Avaro
 */
$messages['fi'] = array(
	'call-save-success' => 'Tämä teksti on tallennettu sivulle <big>[[$1]]</big> .',
);

$messages['fr'] = array(
	'call' => 'Appel',
	'call-desc' => 'Crée un lien hypertexte vers un modèle ou un article wiki normaux tout en y passant des paramètres. Elle peut être utilisée en ligne de commande depuis un navigateur ou à travers un texte wiki.',
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

/** Galician (Galego)
 * @author Xosé
 */
$messages['gl'] = array(
	'call-save'         => "A saída desta chamada gardaríase nunha páxina chamada ''$1''.",
	'call-save-success' => 'O texto seguinte gardouse na páxina <big>[[$1]]</big>.',
	'call-save-failed'  => 'O texto seguinte NON se gardou na páxina <big>[[$1]]</big> porque xa existe esa páxina.',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'call'              => 'Wołać',
	'call-desc'         => 'Wotkaz k předłoze (abo k normalnej wikijowej stronje) z přepodaću parametrow wutworić. Da so w přikazowej lince wobhladowaka abo znutřka wikijoweho teksta wužiwać.',
	'call-text'         => "Rozšěrjenja Call wočakuje wiki-stronu a opcionalne parametry za tutu stronu jako argument.<br><br>
Přikład 1: &nbsp; <tt>[[Special:Call/My Template,parm1=value1]]</tt><br/>
Přikład 2: &nbsp; <tt>[[Special:Call/Talk:My Discussion,parm1=value1]]</tt><br/>
Přikład 3: &nbsp; <tt>[[Special:Call/:My Page,parm1=value1,parm2=value2]]</tt><br/><br/>
Přikład 4 (URL wobhladowaka): &nbsp; <tt>http://mydomain/mywiki/index.php?Special:Call/:My Page,parm1=value1</tt><br/><br/>

<i>Rozšěrjenje Call</i> budźe datu stronu podać a parametry přepodać.<br>Budźeš wobsah zwołaneje strony a jeje titul widźeć, ale jeje 'typ' budźe tón specialneje strony, <br>t.r. tajka strona njeda so wobdźěłować.<br>Wobsah, kotryž widźiš, móže, wotwisujo wot hódnoty parametrow, kotruž sy přepodał, wariěrować.<br><br>
<i>Rozšěrjenje Call</i> je wužitne, zo bychu so interaktiwne aplikacije z MediaWiki tworili.<br> Za přikład hlej <a href='http://semeb.com/dpldemo/Template:Catlist'>DPL GUI</a> ..<br/> W padźe problemow móžeš <b>Special:Call/DebuG</b> spytać.",
	'call-save'         => "Wudaće tutoho zwołanja by so na stronu z mjenom ''$1'' składowało.",
	'call-save-success' => 'Slědowacy tekst bu na stronu <big>[[$1]]</big> składował.',
	'call-save-failed'  => 'Slědowacy tekst NJEje so na stronu <big>[[$1]]</big> składował, dokelž ta strona hižo eksistuje.',
);

/** Hungarian (Magyar)
 * @author Bdanee
 */
$messages['hu'] = array(
	'call'              => 'Hívás',
	'call-text'         => "A kiegészítőnek meg kell adni egy wiki oldalt és kiegészítő paramétereket ahhoz az oldalhoz.<br><br>
1. példa: &nbsp; <tt>[[Special:Call/Sablon neve,parm1=érték1]]</tt><br/>
2. példa: &nbsp; <tt>[[Special:Call/Vita:Vitalapom,parm1=érték1]]</tt><br/>
3. példa: &nbsp; <tt>[[Special:Call/:Az én lapom,parm1=érték1,parm2=érték2]]</tt><br/><br/>
4. példa (URL a böngészőben): &nbsp; <tt>http://mydomain/mywiki/index.php?Special:Call/:Az én lapom,parm1=érték1</tt><br/><br/>

A kiegészítő meghívja az adott oldalt, és átadja neki a megadott paramétereket.<br>Láthatod a lap tartalmát, és a címét is, de a 'típusa' speciális lap lesz,<br>amit nem lehet szerkeszteni.<br>A lap tartalma változhat az általad megadott paraméterektől függően.<br><br>
Hasznos lehet interaktív alkalmazások építésére a MediaWikivel.<br>Példának lásd <a href='http://semeb.com/dpldemo/Template:Catlist'>a DPL GUI</a>-t.<br/>
Probléma esetén megpróbálhatod a <b>Special:Call/DebuG</b> használatát",
	'call-save'         => "A hívás kimenetét el lehet menteni egy ''$1'' nevű lapra.",
	'call-save-success' => 'A következő szöveg el lett mentve <big>[[$1]]</big> néven.',
	'call-save-failed'  => 'A következő szöveg NEM lett elmentve, mert már létezik <big>[[$1]]</big> nevű lap.',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'call'              => 'Opruff',
	'call-save-success' => 'Dësen Text gouf op der Säit <big>[[$1]]</big> gespäichert.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'call'              => 'Aanroepen',
	'call-desc'         => 'Maak een hyperlink naar een sjabloon (of naar een normale wikipagina) met gebruik van parameters. Kan gebruikt worden in de adresregel van de browser of in wikitekst.',
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

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'call'              => 'Apèl',
	'call-text'         => "L’extension Apèl a besonh d’una pagina wiki e de paramètres facultatius per aquesta darrièra.<br><br>
Exemple 1: &nbsp; <tt>[[Special:Call/Mon modèl,parm1=value1]]</tt><br/>
Exemple 2: &nbsp; <tt>[[Special:Call/Discussion:Ma discussion,parm1=value1]]</tt><br/>
Exemple 3: &nbsp; <tt>[[Special:Call/:Ma pagina,parm1=value1,parm2=value2]]</tt><br/><br/>
Exemple 4 (Adreça per navegaire) : &nbsp; <tt>http://mondomeni/monwiki/index.php?Special:Call/:Ma_Pagina,parm1=value1</tt><br/><br/>

L’extension <i>Apèl</i> apelarà la pagina indicada en i passant los paramètres.<br>Veiretz las informacions d'aquesta pagina, son títol, mas son « tipe » serà lo d’una pagina especiala mas poirà pas èsser editada.<br>Las informacions que veiretz variaràn en foncion dels paramètres qu'auretz indicats.<br>Aquesta extension es fòrt practica per crear d'aplicacions interactivas amb MediaWiki.<br>A títol d’exemple, vejatz <a href='http://semeb.com/dpldemo/Template:Catlist'>the DPL GUI</a> ..<br/>En cas de problèmas, podètz ensajar <b>Special:Call/DebuG</b>",
	'call-save'         => "Çò qu'es indicat per aqueste apèl poiriá èsser salvat vèrs una pagina intitolada ''$1''.",
	'call-save-success' => 'Lo tèxt seguent es estat salvagardat vèrs la pagina <big>[[$1]]</big> .',
	'call-save-failed'  => 'Lo tèxt seguent a pogut èsser salvargardat vèrs la pagina <big>[[$1]]</big> del fach qu’existís ja.',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'call'              => 'Вызов',
	'call-text'         => "Расширение «Вызов» (Call) принимает в качестве входных данных название страницы и значения параметров.<br><br>
Пример 1: &nbsp; <tt>[[Special:Call/My Template,parm1=value1]]</tt><br/>
Пример 2: &nbsp; <tt>[[Special:Call/Talk:My Discussion,parm1=value1]]</tt><br/>
Пример 3: &nbsp; <tt>[[Special:Call/:My Page,parm1=value1,parm2=value2]]</tt><br/><br/>
Пример 4 (URL для браузера): &nbsp; <tt>http://mydomain/mywiki/index.php?Special:Call/:My Page,parm1=value1</tt><br/><br/>

Данное расширение вызовет указанную страницу и передаёт ей параметры.<br>Вы увидите сожержимое страницы, её заголовок, но её тип будет типом служебной страницы,<br>т. е .содержимое нельзя будет редактировать.<br>Отображаемое содержимое страницы может изменяться, в зависимости от переданных параметров.<br><br>Расширение «Вызов» полезно для построения интерактивных приложений с помощью MediaWiki.<br>См. например <a href='http://semeb.com/dpldemo/Template:Catlist'>DPL GUI</a>.<br/>
В случае возникновения проблем, вы можете использовать <b>Special:Call/DebuG</b>",
	'call-save'         => "Вывод этого вызова будет сохранён на страницу ''$1''.",
	'call-save-success' => 'Следующий текст был сохранён на страницу <big>[[$1]]</big>.',
	'call-save-failed'  => 'Следующий текст НЕ был сохранён на страницу <big>[[$1]]</big>, так как данная страница уже существует.',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'call'              => 'Call',
	'call-desc'         => 'Vytvorí hyperodkaz na šablónu (alebo na bežnú wiki stránku) s odovzdávaním parametrov. Je možné použiť z riadka s adresou v prehliadači alebo v rámci wiki textu.',
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

/** Volapük (Volapük)
 * @author Smeira
 */
$messages['vo'] = array(
	'call-save-success' => 'Vödem fovik pedakipon su pad: <big>[[$1]]</big>.',
	'call-save-failed'  => 'Vödem fovik NO pedakipon su pad: <big>[[$1]]</big> bi pad at ya dabinon.',
);

