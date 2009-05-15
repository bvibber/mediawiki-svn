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
 */
$messages['en'] = array(
	# for Special:Version
	'plotters-desc'      => 'Lets users use custom JavaScript in jsplot tags',

	# for Special:Gadgets
	'plotters'           => 'Plotters',
	'plotters-title'     => 'Plotters',
	'plotters-pagetext'  => "Below is a list of special plotters users can use in their jsplot tags, as defined by [[MediaWiki:Plotters-definition]].
This overview provides easy access to the system message pages that define each plotter's description and code.",
	'plotters-uses'      => 'Uses',
	'plotters-missing-script'      => 'No script was defined.',
	'plotters-missing-arguments'      => 'No arguments specified.',
	'plotters-excessively-long-scriptname'      => 'The script name is too long. Please define a script that is less than 255 characters.',
	'plotters-excessively-long-preprocessorname'      => 'The preprocessor name is too long. Please define a preprocessor that is less than 255 characters.',
	'plotters-excessively-long-helpername'      => 'The helper name is too long. Please define a helper that is less than 255 characters.',
	'plotters-no-data'      => 'No data was provided.',
	'plotters-invalid-renderer'      => 'An invalid renderer was selected.',
	'plotters-errors'      => '<b>Plotters error(s):</b>',
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'plotters-uses' => 'تستخدم',
);

/** Belarusian (Taraškievica orthography) (Беларуская (тарашкевіца))
 * @author EugeneZelenko
 * @author Jim-by
 */
$messages['be-tarask'] = array(
	'plotters-desc' => 'Дазваляе ўдзельнікам выкарыстоўваць уласны JavaScript у тэгах jsplot',
	'plotters' => 'Плотэры',
	'plotters-title' => 'Плотэры',
	'plotters-missing-script' => 'Скрыпт ня вызначаны.',
	'plotters-excessively-long-scriptname' => 'Назва скрыпта занадта доўгая. Калі ласка, вызначце скрыпт, які ўтрымлівае меней 255 сымбаляў.',
	'plotters-no-data' => 'Зьвесткі не пададзеныя.',
	'plotters-invalid-renderer' => 'Выбраны няслушны генэратар выяваў.',
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
	'plotters-excessively-long-scriptname' => 'Naziv skripte je predug. Molimo definirajte naziv skripte koji je manji od 255 znakova.',
	'plotters-no-data' => 'Nisu navedeni podaci.',
	'plotters-invalid-renderer' => 'Odabran je nevaljan renderer.',
	'plotters-errors' => '<b>Greška(e) plotera:</b>',
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
	'plotters-excessively-long-helpername' => 'Mě pomocnika jo pśedłujke. Pšosym definěruj pomocnik, kótaryž ma mjenjej ako 255 znamuškow.',
	'plotters-no-data' => 'Žedne daty njejsu se pódali.',
	'plotters-invalid-renderer' => 'Njepłaśiwy kreslak jo se wubrał.',
	'plotters-errors' => '<b>Ploterowe zmólki:</b>',
);

/** French (Français)
 * @author Crochet.david
 * @author IAlex
 */
$messages['fr'] = array(
	'plotters-desc' => "Permet aux utilisateurs d'utiliser du javascript personnalisé dans les balises jsplot",
	'plotters' => 'traceurs',
	'plotters-title' => 'traceurs',
	'plotters-pagetext' => "Ci-dessou s la liste des traceurs spéciaux que les utilisateurs peuvent utiliser dans leurs balises jsplot, comme définies sur [[MediaWiki:Plotters-definition]].
Cette vue d'ensemble permet d'accéder facilement aux messages système qui définissent le code et la description de chaque traceur.",
	'plotters-uses' => 'Utilise',
	'plotters-missing-script' => 'Aucun script n’a été définie.',
	'plotters-missing-arguments' => "Aucun argument n'a été spécifié.",
	'plotters-excessively-long-scriptname' => 'Le nom du script est trop long. Veuillez définir un script qui a de moins de 255 caractères.',
	'plotters-excessively-long-preprocessorname' => 'Le nom du préprocesseur est trop long. Définissez un préprocesseur qui fait moins de 255 caractères.',
	'plotters-excessively-long-helpername' => "Le nom de l'auxiliaire est trop long. Définissez un auxiliaire qui fait moins de 255 caractères.",
	'plotters-no-data' => 'Aucune donnée n’a été fournie.',
	'plotters-invalid-renderer' => 'Un moteur de rendu invalide a été sélectionné.',
	'plotters-errors' => '<b>Erreur(s) de traceurs:</b>',
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
	'plotters-excessively-long-helpername' => 'O nome do axudante é moi longo. Por favor, defina un axudante que sexa inferior a 255 caracteres.',
	'plotters-no-data' => 'Non se proporcionou ningún dato.',
	'plotters-invalid-renderer' => 'Seleccionouse un renderizador inválido.',
	'plotters-errors' => '<b>Erro(s) de plotters:</b>',
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
	'plotters-excessively-long-scriptname' => 'Dr Skriptname isch z lang. Bitte definier e Skript, wu weniger wie 255 Zeiche het.',
	'plotters-no-data' => 'Kei Date botte.',
	'plotters-invalid-renderer' => 'E nit giltige Renderer isch uusgwehlt wore.',
	'plotters-errors' => '<b>Plotterfähler:</b>',
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
	'plotters-excessively-long-helpername' => 'Mjeno pomocnika je předołhe. Prošu definuj pomocnik, kotryž ma mjenje hač 255 znamješkow.',
	'plotters-no-data' => 'Žane daty njejsu so podali.',
	'plotters-invalid-renderer' => 'Njepłaćiwy rysowak je so wubrał.',
	'plotters-errors' => '<b>Ploterowe zmylki:</b>',
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
	'plotters-excessively-long-scriptname' => 'Le nomine del script es troppo longe. Per favor defini un script que ha minus de 255 characteres.',
	'plotters-no-data' => ' Nulle dato esseva providite.',
	'plotters-invalid-renderer' => 'Un renditor invalide esseva seligite.',
	'plotters-errors' => '<b>Error(es) de plotter:</b>',
);

/** Japanese (日本語)
 * @author Fryed-peach
 */
$messages['ja'] = array(
	'plotters-desc' => '利用者が jsplot タグ内で独自の JavaScript を使えるようにする',
	'plotters' => 'プロッター',
	'plotters-title' => 'プロッター',
	'plotters-pagetext' => '以下は利用者が jsplot タグ内で使用できる、[[MediaWiki:Plotters-definition]]で定義された特別なプロッターの一覧です。この一覧から各プロッターの説明およびコードを定義している各システムメッセージのページにアクセスできます。',
	'plotters-uses' => '使用',
	'plotters-missing-script' => '定義済みのスクリプトはありません。',
	'plotters-excessively-long-scriptname' => 'スクリプトの名前が長すぎます。255文字未満に収めてください。',
	'plotters-no-data' => 'データが与えられていません。',
	'plotters-invalid-renderer' => '無効なレンダラーが選択されました。',
	'plotters-errors' => '<b>プロッターのエラー:</b>',
);

/** Ripoarisch (Ripoarisch)
 * @author Purodha
 */
$messages['ksh'] = array(
	'plotters-uses' => 'Bruch',
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
	'plotters-excessively-long-scriptname' => 'Názov skriptu bol príliš dlhý. Prosím definujte skript, ktorého názov má menej ako 255 znakov.',
	'plotters-no-data' => 'Neboli poskytnuté žiadne údaje.',
	'plotters-invalid-renderer' => 'Bol vybraný neplatný vykresľovač.',
	'plotters-errors' => '<b>Chyby plotrov:</b>',
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

