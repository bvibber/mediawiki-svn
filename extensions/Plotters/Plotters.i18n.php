<?php
/**
 * Internationalisation file for extension Plotters. Based on the Gadgets extension.
 *
 * @addtogroup Extensions
 * @author Ryan Lane, rlane32+mwext@gmail.com
 * @copyright © 2009 Ryan Lane
 * @license GNU General Public Licence 2.0 or later
 */

$messages = array();

/** English
 * @author Ryan Lane, rlane32+mwext@gmail.com
 * @author Purodha
 */
$messages['en'] = array(
	# for Special:Version
	'plotters-desc'      => 'Lets users use custom JavaScript in jsplot tags',

	# for Special:Plotters
	'plotters'           => 'Plotters',
	'plotters-title'     => 'Plotters',
	'plotters-pagetext'  => "Below is a list of special plotters users can use in their jsplot tags, as defined by [[MediaWiki:Plotters-definition]].
This overview provides easy access to the system message pages that define each plotter's description and code.",

	# for Special:Plotters-definition
	'plotters-uses'      => 'Uses',
	'plotters-missing-script'      => 'No script was defined.',
	'plotters-missing-arguments'      => 'No arguments specified.',
	'plotters-excessively-long-scriptname'      => 'The script name is too long.
Please define a script, the name of which is 255 characters long at most.',
	'plotters-excessively-long-preprocessorname'      => 'The preprocessor name is too long.
Please define a preprocessor, the name of which is 255 characters long at most.',
	'plotters-excessively-long-name'      => 'The plot name is too long.
Please define a plot name that has 255 characters at most.',
	'plotters-excessively-long-tableclass'      => 'The tableclass is too long.
Please define a tableclass that has 255 characters at most.',
	'plotters-no-data'      => 'No data was provided.',
	'plotters-invalid-renderer'      => 'An invalid renderer was selected.',
	'plotters-errors'      => 'Plotters {{PLURAL:$1|error|errors}}:',
);

/** Message documentation (Message documentation)
 * @author Purodha
 */
$messages['qqq'] = array(
	'plotters-desc' => 'Short description of the Plotters extension. Used on [[Special:Version]]. Do not translate link targets.',
	'plotters-errors' => 'Parameters:
$1 = number of messages following. Can be used with PLURAL.',
);

/** Arabic (العربية)
 * @author Meno25
 * @author OsamaK
 */
$messages['ar'] = array(
	'plotters-uses' => 'تستخدم',
	'plotters-missing-script' => 'لا سكربت مُعرّف.',
	'plotters-missing-arguments' => 'لا معطى محدّد.',
	'plotters-excessively-long-scriptname' => 'اسم السكربت طويل جدًا.
من فضلك عرّف سكربتًا اسمه لا يتجاوز 255 حرفًا.',
	'plotters-no-data' => 'لا بيانات متوفرة.',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'plotters-desc' => 'Дазваляе ўдзельнікам выкарыстоўваць уласны JavaScript у тэгах jsplot',
	'plotters' => 'Плотары',
	'plotters-title' => 'Плотары',
	'plotters-pagetext' => 'Ніжэй пададзены сьпіс спэцыяльных плотараў, якія ўдзельнікі могуць выкарыстоўваць у тэгах jsplot, як вызначана ў [[MediaWiki:Plotters-definition]].
Гэта дазваляе атрымаць доступ да старонак з сыстэмнымі паведамленьнямі, якія вызначаюць апісаньне і код кожнага плотара.',
	'plotters-uses' => 'Выкарыстаньні',
	'plotters-missing-script' => 'Скрыпт ня вызначаны.',
	'plotters-missing-arguments' => 'Аргумэнты не пазначаныя.',
	'plotters-excessively-long-scriptname' => 'Назва скрыпта занадта доўгая.
Калі ласка, вызначце скрыпт, назва якога ня больш за 255 сымбаляў.',
	'plotters-excessively-long-preprocessorname' => 'Назва прэпрацэсара занадта доўгая.
Калі ласка, вызначце прэпрацэсар, назва якога ня больш за 255 сымбаляў.',
	'plotters-excessively-long-name' => 'Назва плотара занадта доўгая.
Калі ласка, прызначце назву плотара даўжынёй ня болей 255 сымбаляў.',
	'plotters-excessively-long-tableclass' => 'Назва клясы табліцы занадта доўгая.
Калі ласка, вызначце клясу табліцы, назва якой ня больш за 255 сымбаляў.',
	'plotters-no-data' => 'Зьвесткі не пададзеныя.',
	'plotters-invalid-renderer' => 'Выбраны няслушны генэратар выяваў.',
	'plotters-errors' => '{{PLURAL:$1|Памылка плотара|Памылкі плотара}}:',
);

/** Bosnian (Bosanski)
 * @author CERminator
 */
$messages['bs'] = array(
	'plotters-desc' => 'Omogućuje korisnicima prilagođene JavaScript u jsplot oznakama',
	'plotters' => 'Ploteri',
	'plotters-title' => 'Ploteri',
	'plotters-pagetext' => 'Ispod je spisak posebnih plotera koje korisici mogu upotrebljavati u svojim jsplot oznakama, kako je definirano u [[MediaWiki:Plotters-definition]].
Ovaj pregled omogućuje jednostavni pristup na stranice sistemskih poruka koje definiraju svaki opis i kod plotera.',
	'plotters-uses' => 'Korištenja',
	'plotters-missing-script' => 'Nije definirana nijedna skirpta.',
	'plotters-missing-arguments' => 'Nisu navedeni argumenti.',
	'plotters-excessively-long-scriptname' => 'Naziv skripte je predug. Molimo definirajte naziv skripte koji je manji od 255 znakova.',
	'plotters-excessively-long-preprocessorname' => 'Naziv preprocesora je predug.
Molimo definirajte preprocesor, tako da ime ne bude duže od 255 znakova.',
	'plotters-excessively-long-name' => 'Naziv plota je predug.
Molimo definirajte naziv plota da ne bude duži od 255 znakova.',
	'plotters-excessively-long-tableclass' => 'Klasa tabele je preduga.
Molimo definirajte klasu tabele tako da ima najviše 255 znakova.',
	'plotters-no-data' => 'Nisu navedeni podaci.',
	'plotters-invalid-renderer' => 'Odabran je nevaljan renderer.',
	'plotters-errors' => '{{PLURAL:$1|Greška|Greške}} plotera:',
);

/** German (Deutsch)
 * @author Sebastian Wallroth
 */
$messages['de'] = array(
	'plotters' => 'Plotter',
	'plotters-title' => 'Plotter',
);

/** Lower Sorbian (Dolnoserbski)
 * @author Michawiki
 */
$messages['dsb'] = array(
	'plotters-desc' => 'Zmóžnja wužywarjam swójski JavaScript w toflickach jsplot wužywaś',
	'plotters' => 'Plotery',
	'plotters-title' => 'Plotery',
	'plotters-pagetext' => 'Dołojce jo lisćina specialnych ploterow, kótarež wužywarje mógu w swójich toflickach jsplot wužywaś, kaž pśez [[MediaWiki:Plotters-definition]] definěrowane.
Toś ten pśeglěd dawa lažki pśistup k bokam systenmowych powěźeńkow, kótarež definěruju wopisanje a kode kuždego plotera.',
	'plotters-uses' => 'Wužywa',
	'plotters-missing-script' => 'Žeden skript njejo se definěrował.',
	'plotters-missing-arguments' => 'Žedne argumenty pódane.',
	'plotters-excessively-long-scriptname' => 'Mě skripta jo pśedłujke. Pšosym definěruj skript, kótaryž ma mjenjej ako 255 znamuškow.',
	'plotters-excessively-long-preprocessorname' => 'Mě preprocesora jo pśedłujke. Pšosym definěruj preprocesor, kótaryž ma mjenjej ako 255 znamuškow.',
	'plotters-excessively-long-name' => 'Mě plota jo pśedłujke.
Pšosym definěruj plotowe mě, kótarež ma maksimalnje 255 znamuškow.',
	'plotters-excessively-long-tableclass' => 'Tabelowa klasa jo pśedłujka. Pšosym definěruj tabelowu klasu, kótaraž ma jano 255 znamuškow.',
	'plotters-no-data' => 'Žedne daty njejsu se pódali.',
	'plotters-invalid-renderer' => 'Njepłaśiwy kreslak jo se wubrał.',
	'plotters-errors' => '{{PLURAL:$1|Ploterowa zmólka|Ploterowej zmólce|Ploterowe zmólki|Ploterowe zmólki}}:',
);

/** Spanish (Español)
 * @author Crazymadlover
 * @author Locos epraix
 */
$messages['es'] = array(
	'plotters-desc' => 'Deja a los usuarios usar JavaScript personalizada en etiquetas jsplot',
	'plotters' => 'Plotters',
	'plotters-title' => 'Plotters',
	'plotters-uses' => 'Usos',
	'plotters-missing-script' => 'No se definió el script.',
	'plotters-missing-arguments' => 'No se especificaron argumentos.',
	'plotters-excessively-long-scriptname' => 'El nombre del script es demasiado largo.
Por favor define un script, cuyo nombre tenga 255 caracteres de longitud como máximo.',
	'plotters-excessively-long-preprocessorname' => 'El nombre del preprocesador es demasiado largo.
Por favor define un preprocesador, cuyo nombre tenga 255 caracteres de longitud como máximo.',
	'plotters-no-data' => 'sin datos proveídos',
	'plotters-invalid-renderer' => 'Se seleccionó un renderizador inválido.',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 */
$messages['fr'] = array(
	'plotters-desc' => 'Permet aux utilisateurs d’utiliser du javascript personnalisé dans les balises jsplot',
	'plotters' => 'traceurs',
	'plotters-title' => 'traceurs',
	'plotters-pagetext' => 'Ci-dessou s la liste des traceurs spéciaux que les utilisateurs peuvent utiliser dans leurs balises jsplot, comme définies sur [[MediaWiki:Plotters-definition]].
Cette vue d’ensemble permet d’accéder facilement aux messages système qui définissent le code et la description de chaque traceur.',
	'plotters-uses' => 'Utilise',
	'plotters-missing-script' => 'Aucun script n’a été défini.',
	'plotters-missing-arguments' => 'Aucun argument n’a été spécifié.',
	'plotters-excessively-long-scriptname' => 'Le nom du script est trop long. Veuillez définir un script qui a de moins de 255 caractères.',
	'plotters-excessively-long-preprocessorname' => 'Le nom du préprocesseur est trop long. Définissez un préprocesseur qui fait moins de 255 caractères.',
	'plotters-excessively-long-name' => 'Le nom du graphe est trop long. Définissez un nom de graphe qui fait moins de 255 caractères.',
	'plotters-excessively-long-tableclass' => 'La classe du tableau est trop longue. Définissez une classe de tableau qui fait moins de 255 caractères.',
	'plotters-no-data' => 'Aucune donnée n’a été fournie.',
	'plotters-invalid-renderer' => 'Un moteur de rendu invalide a été sélectionné.',
	'plotters-errors' => '{{PLURAL:$1|Erreur|Erreurs}} de traceurs :',
);

/** Galician (Galego)
 * @author Toliño
 */
$messages['gl'] = array(
	'plotters-desc' => 'Permite que os usuarios empreguen JavaScript personalizado nas súas etiquetas jsplot',
	'plotters' => 'Plotters',
	'plotters-title' => 'Plotters',
	'plotters-pagetext' => 'A continuación está a lista dos plotters especiais que os usuarios poden empregar nas súas etiquetas jsplot, tal e como está definido pola páxina [[MediaWiki:Plotters-definition]].
Esta vista xeral proporciona un acceso doado ás páxinas de mensaxes do sistema que definen a descrición e o código de cada plotter.',
	'plotters-uses' => 'Usos',
	'plotters-missing-script' => 'Non foi definida ningunha escritura.',
	'plotters-missing-arguments' => 'Non foi especificado ningún argumento.',
	'plotters-excessively-long-scriptname' => 'O nome da escritura é moi longo. Por favor, defina unha escritura que sexa inferior a 255 caracteres.',
	'plotters-excessively-long-preprocessorname' => 'O nome do preprocesador é moi longo. Por favor, defina un preprocesador que sexa inferior a 255 caracteres.',
	'plotters-excessively-long-name' => 'O nome do plot é moi longo. Por favor, defina un nome que sexa inferior a 255 caracteres.',
	'plotters-excessively-long-tableclass' => 'A clase de táboa é moi longa. Por favor, defina unha clase de táboa que sexa inferior a 255 caracteres.',
	'plotters-no-data' => 'Non se proporcionou ningún dato.',
	'plotters-invalid-renderer' => 'Seleccionouse un renderizador inválido.',
	'plotters-errors' => '{{PLURAL:$1|Erro|Erros}} de plotters:',
);

/** Swiss German (Alemannisch)
 * @author Als-Holder
 */
$messages['gsw'] = array(
	'plotters-desc' => 'Macht s Benutzer megli, e aapasst Javaskripkt in ihre jsplot-Markierige z bruche',
	'plotters' => 'Plotter',
	'plotters-title' => 'Plotter',
	'plotters-pagetext' => 'Unte sin spezielli Plotter usglischtet, wu Benutzer chenne verwände in ihre jsplot-Markierige, wie s dur [[MediaWiki:Plotters-definition]] definiert isch.
Die Ibersicht isch e eifach Zuegang zue dr Syschtemnochrichte, wu d Bschrybig un dr Code vu jedem Plotter definiere.',
	'plotters-uses' => 'Brucht',
	'plotters-missing-script' => 'Kei Skript isch definiert wore.',
	'plotters-missing-arguments' => 'Kei Argumänt definiert.',
	'plotters-excessively-long-scriptname' => 'Dr Skriptname isch z lang. Bitte definier e Skript, wu weniger wie 255 Zeiche het.',
	'plotters-excessively-long-preprocessorname' => 'Dr Name vum Vorprozessor isch z lang. Bitte definier e Vorprozessorname mit weniger wie 255 Zeiche.',
	'plotters-excessively-long-name' => 'Dr Plotname isch z lang. Bitte definer e Plotname mit weniger wie 255 Zeiche.',
	'plotters-excessively-long-tableclass' => 'D Tabälleklasse isch z lang. Bitte definier e Tabälleklasse mit weniger wie 255 Zeiche.',
	'plotters-no-data' => 'Kei Date botte.',
	'plotters-invalid-renderer' => 'E nit giltige Renderer isch uusgwehlt wore.',
	'plotters-errors' => '{{PLURAL:$1|Plotterfähler|Plotterfähler}}:',
);

/** Hebrew (עברית)
 * @author YaronSh
 */
$messages['he'] = array(
	'plotters-desc' => 'מתן אפשרות לשימוש ב־JavaScript מותאם אישית בתגיות jsplot',
	'plotters-missing-script' => 'לא הוגדר סקריפט.',
	'plotters-missing-arguments' => 'לא צויינו ארגומנטים.',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'plotters-desc' => 'Zmóžnja wužiwarjam swójske javaskripty w jich tafličkach jsplot wužiwać',
	'plotters' => 'Plotery',
	'plotters-title' => 'Plotery',
	'plotters-pagetext' => 'Deleka je lisćina specielnych ploterow, kotrež wužiwarjo móžeja w swojich tafličkach jsplot wužiwać, kaž su přez [[MediaWiki:Plotters-definition]] definowane.
Tutón přehlad dodawa lochki přistup na strony systemowych zdźělenkow, kotrež wopisanje a kod kóždeho plotera definuja.',
	'plotters-uses' => 'Wužiwa',
	'plotters-missing-script' => 'Žadyn skript njeje so definował.',
	'plotters-missing-arguments' => 'Žane argumenty podate.',
	'plotters-excessively-long-scriptname' => 'Mjeno skripta je předołhe. Prošu definuj skript, kotryž ma mjenje hač 255 znamješkow.',
	'plotters-excessively-long-preprocessorname' => 'Mjeno preprocesora je předołhe. Prošu definuj preprocesor, kotryž ma mjenje hač 255 znamješkow.',
	'plotters-excessively-long-name' => 'Mjeno plota je předołhe. Prošu definuj plotowe mjeno, kotrež ma mjenje hač 255 znamješkow.',
	'plotters-excessively-long-tableclass' => 'Tabelowa klasa je předołha. Prošu definuj tabelowu klasu, kotraž ma mjenje hač 255 znamješkow.',
	'plotters-no-data' => 'Žane daty njejsu so podali.',
	'plotters-invalid-renderer' => 'Njepłaćiwy rysowak je so wubrał.',
	'plotters-errors' => '{{PLURAL:$1|Ploterowy zmylk|Ploterowej zmylkaj|Ploterowe zmylki|Ploterowe zmylki}}:',
);

/** Interlingua (Interlingua)
 * @author McDutchie
 */
$messages['ia'] = array(
	'plotters-desc' => 'Permitte a usatores inserer JavaScript personalisate per medio de etiquettas "jsplot"',
	'plotters' => 'Plotters',
	'plotters-title' => 'Plotters',
	'plotters-pagetext' => 'In basso es un lista de plotters special que le usatores pote usar in lor etiquettas jsplot, como definite per [[MediaWiki:Plotters-definition]].
Iste summario permitte acceder facilemente al messages de systema que defini le description e codice de cata plotter.',
	'plotters-uses' => 'Usa',
	'plotters-missing-script' => 'Nulle script esseva definite.',
	'plotters-missing-arguments' => 'Nulle parametro specificate.',
	'plotters-excessively-long-scriptname' => 'Le nomine del script es troppo longe. Per favor defini un script que ha minus de 255 characteres.',
	'plotters-excessively-long-preprocessorname' => 'Le nomine del preprocessator es troppo longe. Per favor defini un preprocessator que ha minus de 255 characteres.',
	'plotters-excessively-long-name' => 'Le nomine del plot es troppo longe.
Per favor defini un nomine de plot que ha 255 characteres al maximo.',
	'plotters-excessively-long-tableclass' => 'Le classe de tabula es troppo longe.
Per favor defini un classe de tabula que ha 255 characteres al maximo.',
	'plotters-no-data' => ' Nulle dato esseva providite.',
	'plotters-invalid-renderer' => 'Un renditor invalide esseva seligite.',
	'plotters-errors' => '{{PLURAL:$1|Error|Errores}} de plotter:',
);

/** Japanese (日本語)
 * @author Aotake
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'plotters-desc' => '利用者が jsplot タグ内で独自の JavaScript を使えるようにする',
	'plotters' => 'プロッター',
	'plotters-title' => 'プロッター',
	'plotters-pagetext' => '以下は利用者が jsplot タグ内で使用できる、[[MediaWiki:Plotters-definition]]で定義された特別なプロッターの一覧です。この一覧から各プロッターの説明およびコードを定義している各システムメッセージのページにアクセスできます。',
	'plotters-uses' => '使用',
	'plotters-missing-script' => '定義済みのスクリプトはありません。',
	'plotters-missing-arguments' => '引数が指定されていません。',
	'plotters-excessively-long-scriptname' => 'スクリプトの名前が長すぎます。スクリプト名は255文字以内に収めてください。',
	'plotters-excessively-long-preprocessorname' => 'プリプロセッサの名前が長すぎます。プリプロセッサ名は255文字以内に収めてください。',
	'plotters-excessively-long-name' => 'プロットの名前が長すぎます。プロット名は255文字以内に収めてください。',
	'plotters-excessively-long-tableclass' => 'テーブルのクラスが長すぎます。クラスは255文字以内に収めてください。',
	'plotters-no-data' => 'データが与えられていません。',
	'plotters-invalid-renderer' => '無効なレンダラーが選択されました。',
	'plotters-errors' => 'プロッターの{{PLURAL:$1|エラー}}:',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'plotters-desc' => 'Jitt de Metmaacher selfs zerääsch jemaate JavaSkrepte för se en <code lang="en">jsplot</code>-Befähle ze bruche.',
	'plotters' => 'Plottere',
	'plotters-title' => 'Plotter',
	'plotters-pagetext' => 'Hee dronger es en Leß met beshtemmpte Plotter, di mer em Wiki en <code lang="en">jsplot</code>-Befähle bruche kann. Se sin en dä Sigg <code lang="en">[[MediaWiki:Plotters-definition]]</code> faßjelaat woode. Hee di Övverseesch jitt Der eine eijfache Zohjang op di Sigge, woh dä Plottere ier Eijeschaffte un dänne ier Koodes faßjehallde sin.',
	'plotters-uses' => 'Bruch',
	'plotters-missing-script' => 'Kei Skrep faßjelaat.',
	'plotters-missing-arguments' => 'Kei Parrameetere aanjejovve.',
	'plotters-excessively-long-scriptname' => 'Dä Name för dat Skrep es ze lang, jivv en Skrep aan, woh dä Name kööter wi 255 Zeijsche eß.',
	'plotters-excessively-long-preprocessorname' => 'Dä Name för dat Förloufprojramm es ze lang, jivv e Förloufprojramm aan, woh dä Name kööter wi 255 Zeijsche eß.',
	'plotters-excessively-long-name' => 'Dä Name för di Datteijh met dä Daate för ze plotte es ze lang, jivv ene Name aan, dä kööter wi 255 Zeijsche eß.',
	'plotters-excessively-long-tableclass' => 'Dä Name för di <i lang="en">CSS</i>-KLaß fö de Tabälle es ze lang, jivv ene Name aan, dä kööter wi 255 Zeijsche eß.',
	'plotters-no-data' => 'Kei Date aanjejovve.',
	'plotters-invalid-renderer' => 'En onjöltsch Aanzeijeprojramm wood ußjesohk.',
	'plotters-errors' => '{{PLURAL:$1|Eine|$1|Keine}} Fähler en de <i lang="en">Plotters</i>:',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'plotters-desc' => 'Laat gebruikers aangepaste JavaScript gebruiken in jsplot-tags',
	'plotters' => 'Plotters',
	'plotters-title' => 'Plotters',
	'plotters-pagetext' => "Hieronder worden de speciale plotters weergegeven die gebruikt kunnen worden in jsplot-tags, zoals is ingesteld in [[MediaWiki:Plotters-definition]].
Dit overzicht geeft eenvoudig toegang tot de pagina's met systeemteksten waarin iedere plotter wordt beschreven en de code van de plotter.",
	'plotters-uses' => 'Gebruikt',
	'plotters-missing-script' => 'Er is geen script gedefinieerd.',
	'plotters-missing-arguments' => 'Er zijn geen argumenten opgegeven.',
	'plotters-excessively-long-scriptname' => 'De scriptnaam is te lang.
Geef een scriptnaam op die korter is dan 255 karakters.',
	'plotters-excessively-long-preprocessorname' => 'De preprocessornaam is te lang.
Geef een preprocessornaam op die korter is dan 255 karakters.',
	'plotters-excessively-long-name' => 'De plotternaam is te lang.
Geeft aan plotternaam op die korter is dan 255 karakters.',
	'plotters-excessively-long-tableclass' => 'De tabelklassenaam is te lang.
Geef een tabelklassenaam op die korter is dan 255 karakters.',
	'plotters-no-data' => 'Er zijn geen gegevens ingevoerd.',
	'plotters-invalid-renderer' => 'Er is een ongeldige renderer geselecteerd.',
	'plotters-errors' => 'Er {{PLURAL:$1|is een plotterfout|zijn plotterfouten}} aangetroffen:',
);

/** Norwegian Nynorsk (‪Norsk (nynorsk)‬)
 * @author Gunnernett
 * @author Harald Khan
 */
$messages['nn'] = array(
	'plotters-desc' => 'Lèt brukarar nytta eigendefinerte JavaScript i jsplot-merke.',
	'plotters-missing-script' => 'Inkje skript var definert.',
	'plotters-missing-arguments' => 'Ingen argument spesifiserte.',
	'plotters-no-data' => 'Ingen data var tilrettelagt.',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'plotters-desc' => "Permet als utilizaires d'utilizar de javascript personalizat dins las balisas jsplot",
	'plotters' => 'Traçaires',
	'plotters-title' => 'Traçaires',
	'plotters-pagetext' => "Çaijós la lista dels traçaires especials que los utilizaires pòdon utilizar dins lors balisas jsplot, coma definidas sua [[MediaWiki:Plotters-definition]].
Aquesta vista d'ensemble permet d'accedir aididament als messatges del sistèma que definisson lo còde e la descripcion de cada traçaire.",
	'plotters-uses' => 'Utiliza',
	'plotters-missing-script' => "Cap d'escripte es pas estat definit.",
	'plotters-missing-arguments' => "Cap d'argument es pas estat especificat.",
	'plotters-excessively-long-scriptname' => "Lo nom de l'escript es tròp long.
Definissètz un escript qu'a mens de 255 caractèrs.",
	'plotters-excessively-long-preprocessorname' => 'Lo nom del preprocessor es tròp long.
Definissètz un preprocessor que fa mens de 255 caractèrs.',
	'plotters-excessively-long-name' => 'Lo nom del graf es tròp long.
Definissètz un nom de graf que fa mens de 255 caractèrs.',
	'plotters-excessively-long-tableclass' => 'La classa del tablèu es tròp longa.
Definissètz una classa de tablèu que fa mens de 255 caractèrs.',
	'plotters-no-data' => 'Cap de donada es pas estada provesida.',
	'plotters-invalid-renderer' => 'Un motor de rendut invalid es estat seleccionat.',
	'plotters-errors' => '{{PLURAL:$1|Error|Errors}} de traçadors :',
);

/** Portuguese (Português)
 * @author Lijealso
 */
$messages['pt'] = array(
	'plotters-uses' => 'Usos',
	'plotters-no-data' => 'Não foram providenciados dados.',
);

/** Romanian (Română)
 * @author Firilacroco
 */
$messages['ro'] = array(
	'plotters-uses' => 'Utilizări',
);

/** Russian (Русский)
 * @author Ferrer
 * @author Lockal
 * @author Александр Сигачёв
 */
$messages['ru'] = array(
	'plotters-desc' => 'Позволяет участникам использовать свой JavaScript в тегах jsplot',
	'plotters' => 'Плоттеры',
	'plotters-title' => 'Плоттеры',
	'plotters-pagetext' => 'Ниже приведён список служебных плоттеров, которые могут использовать участники в тегах jsplot, как определено в [[MediaWiki:Plotters-definition]].
Это позволяет получить простой доступ к страницам системных сообщений, которые определяют описание и код каждого плоттера.',
	'plotters-uses' => 'Использования',
	'plotters-missing-script' => 'Не определено ни одного скрипта.',
	'plotters-missing-arguments' => 'Аргументы не указаны.',
	'plotters-excessively-long-scriptname' => 'Имя скрипта слишком длинное.
Пожалуйста, укажите скрипт, чьё имя не превышает 255 символов.',
	'plotters-excessively-long-preprocessorname' => 'Имя препроцессора слишком длинное.
Пожалуйста, укажите препроцессор, чьё имя не превышает 255 символов.',
	'plotters-excessively-long-name' => 'Название плоттера слишком длинное.
Пожалуйста, определите название плоттера не больше 255 символов.',
	'plotters-excessively-long-tableclass' => 'Класс таблицы слишком длинный.
Пожалуйста, определите класс таблицы, содержащий не больше 255 символов.',
	'plotters-no-data' => 'Данные не указаны.',
	'plotters-invalid-renderer' => 'Выбран неверный рендерер.',
	'plotters-errors' => '{{PLURAL:$1|Ошибка плоттеров|Ошибки плоттеров}}:',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'plotters-desc' => 'Umožňuje používateľom používať vlastný JavaScript v jsplot',
	'plotters' => 'Plotre',
	'plotters-title' => 'Plotre',
	'plotters-pagetext' => 'Toto je zoznam špeciálnych plotrov, ktoré používatelia môžu použiť vo svojich značkách jsplot podľa definície na stránke [[MediaWiki:Plotters-definition]].
Tento prehľad poskytuje jednoduchý prístup k stránkam systémových správ, ktoré definujú popis a kód každého plotra.',
	'plotters-uses' => 'Použitia',
	'plotters-missing-script' => 'Nebol definovaný žiadny skript.',
	'plotters-missing-arguments' => 'Neboli zadané argumenty.',
	'plotters-excessively-long-scriptname' => 'Názov skriptu bol príliš dlhý. Prosím definujte skript, ktorého názov má menej ako 255 znakov.',
	'plotters-excessively-long-preprocessorname' => 'Názov preprocesora je príliš dlhý. Definujte prosím preprocesor, ktorého názov je kratší ako 255 znakov.',
	'plotters-excessively-long-name' => 'Názov diagramu je príliš dlhý. Definujte prosím preprocesor, ktorého názov je kratší ako 255 znakov.',
	'plotters-excessively-long-tableclass' => 'Názov triedy tabuľky je príliš dlhý. Definujte prosím preprocesor, ktorého názov je kratší ako 255 znakov.',
	'plotters-no-data' => 'Neboli poskytnuté žiadne údaje.',
	'plotters-invalid-renderer' => 'Bol vybraný neplatný vykresľovač.',
	'plotters-errors' => '<b>{{PLURAL:$1|Chyba|Chyby}} plotrov:</b>',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'plotters-desc' => 'Cho phép người dùng sử dụng JavaScript tùy biến trong thẻ jsplot',
	'plotters' => 'Bộ vẽ',
	'plotters-title' => 'Bộ vẽ',
	'plotters-pagetext' => 'Đây là danh sách các bộ vẽ biểu đồ để cho những người dùng sử dụng trong các thẻ jsplot, theo định nghĩa tại [[MediaWiki:Plotters-definition]].
Từ trang này, bạn có thể truy cập những trang thông báo hệ thống miêu tả và định rõ mã nguồn của các bộ vẽ.',
	'plotters-uses' => 'Lần sử dụng',
);

