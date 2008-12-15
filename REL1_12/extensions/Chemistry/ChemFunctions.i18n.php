<?php

/**
 * MediaWiki Internationalisation file for ChemFunctions.php and SpecialChemicalsources.php.
 *
 * @addtogroup Extensions
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$messages = array();

$messages['en'] = array(
	'chemicalsources' => 'Chemical sources',
	'chemicalsource-desc' => 'Adds the tag <nowiki><chemform></nowiki>, for chemical formulae',
	'chemFunctions_ListPage' => 'Chemical sources',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Below is a list of links to sites that may provide information about the chemical substance you are looking for.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula at NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS at the Oxford University (UK) (not searchable)</a><br />',
	'chemFunctions_CAS' => 'CAS number',
	'chemFunctions_EINECS' => 'EINECS',
	'chemFunctions_CHEBI' => 'CHEBI',
	'chemFunctions_PubChem' => 'PubChem',
	'chemFunctions_SMILES' => 'SMILES',
	'chemFunctions_InChI' => 'InChI',
	'chemFunctions_RTECS' => 'RTECS',
	'chemFunctions_KEGG' => 'KEGG',
	'chemFunctions_ATCCode' => 'ATCCode',
	'chemFunctions_DrugBank' => 'DrugBank',
	'chemFunctions_ECNumber' => 'ECNumber',
	'chemFunctions_Formula' => 'Formula',
	'chemFunctions_Name' => 'IUPAC Name',

	'chemFunctions_ChemFormInputError' => 'Chemform: Input error!'
);

/** Arabic (العربية)
 * @author Meno25
 */
$messages['ar'] = array(
	'chemicalsources'                  => 'مصادر كيميائية',
	'chemicalsource-desc'              => 'يضيف الوسم <nowiki><chemform></nowiki>، للصيغ الكيميائية',
	'chemFunctions_ListPage'           => 'مصادر كيميائية',
	'chemFunctions_DataList'           => 'بالأسفل قائمة بوصلات إلى مواقع قد تحتوي على معلومات عن المادة الكيميائية التي تبحث عنها.<br /><br /> * <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula at NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS في جامعة أكسفورد (UK) (غير قابل للبحث)</a><br />',
	'chemFunctions_CAS'                => 'رقم CAS',
	'chemFunctions_EINECS'             => 'إينكس',
	'chemFunctions_CHEBI'              => 'شيبي',
	'chemFunctions_PubChem'            => 'ببكيم',
	'chemFunctions_SMILES'             => 'سمايلز',
	'chemFunctions_InChI'              => 'إنشل',
	'chemFunctions_RTECS'              => 'رتكس',
	'chemFunctions_KEGG'               => 'كيج',
	'chemFunctions_ATCCode'            => 'كود ATC',
	'chemFunctions_DrugBank'           => 'بنك المخدرات',
	'chemFunctions_ECNumber'           => 'رقم EC',
	'chemFunctions_Formula'            => 'الصيغة',
	'chemFunctions_Name'               => 'اسم IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: خطأ إدخال!',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$messages['bg'] = array(
	'chemicalsource-desc'   => 'Добавя етикета <nowiki><chemform></nowiki> за химични формули',
	'chemFunctions_Formula' => 'Формула',
);

/** Bengali (বাংলা)
 * @author Zaheen
 */
$messages['bn'] = array(
	'chemicalsources'                  => 'রাসায়নিক উৎসসমূহ',
	'chemicalsource-desc'              => 'রাসায়নিক সংকেতের জন্য <nowiki><chemform></nowiki> ট্যাগটি যোগ করে',
	'chemFunctions_ListPage'           => 'রাসায়নিক উৎসসমূহ',
	'chemFunctions_DataList'           => 'আপনি যে রাসায়নিক পদার্থটি খুঁজছেন, তার সম্পর্কে তথ্য দিতে পারে এমন কতগুলি সাইটের প্রতি সংযোগসমূহের একটি তালিকা নিচে দেওয়া হল।<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">NIST-এ Compound $MIXCASNameFormula</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">যুক্তরাজ্যের অক্সফোর্ড বিশ্ববিদ্যালয়ে MSDS (অনুসন্ধানযোগ্য নয়)</a><br />',
	'chemFunctions_CAS'                => 'CAS সংখ্যা',
	'chemFunctions_ATCCode'            => 'ATCকোড',
	'chemFunctions_ECNumber'           => 'ECNumber',
	'chemFunctions_Formula'            => 'সংকেত',
	'chemFunctions_Name'               => 'IUPAC নাম',
	'chemFunctions_ChemFormInputError' => 'রাসায়নিক ফর্ম: ইনপুট ত্রুটি!',
);

/** Czech (Česky)
 * @author Matěj Grabovský
 */
$messages['cs'] = array(
	'chemicalsources'                  => 'Chemické zdroje',
	'chemicalsource-desc'              => 'Přidává značku &lt;chemform&gt; pro chemické vzorce',
	'chemFunctions_ListPage'           => 'Chemické zdroje',
	'chemFunctions_DataList'           => 'Níže je seznam odkazů na stránky, které můžou poskytnout informace o chemikálii, kterou hledáte.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Sloučenina $MIXCASNameFormula na NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Oxfordské Universitě (nelze vyhledávat)</a><br />',
	'chemFunctions_CAS'                => 'CAS číslo',
	'chemFunctions_ATCCode'            => 'ATCCode',
	'chemFunctions_ECNumber'           => 'ECNumber',
	'chemFunctions_Formula'            => 'Formule',
	'chemFunctions_Name'               => 'IUPAC název',
	'chemFunctions_ChemFormInputError' => 'Chemform: Vstupní chyba!',
);

/** Danish (Dansk)
 * @author M.M.S.
 */
$messages['da'] = array(
	'chemFunctions_Name' => 'IUPAC Navn',
);

$messages['de'] = array(
	'chemicalsources' => 'Chemische Quellen',
	'chemFunctions_ListPage' => 'Chemische Quellen',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Nachfolgend finden Sie Links zu Seiten, die eventuell Informationen über chemische Substanzen anbieten, nach denen Sie suchen:<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Verbindung $MIXCASNameFormula auf NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS an der Oxford University (UK) (nicht durchsuchbar)</a><br />',
	'chemFunctions_CAS' => 'CAS Nummer',
	'chemFunctions_EINECS' => 'EINECS',
	'chemFunctions_CHEBI' => 'CHEBI',
	'chemFunctions_PubChem' => 'PubChem',
	'chemFunctions_SMILES' => 'SMILES',
	'chemFunctions_InChI' => 'InChI',
	'chemFunctions_RTECS' => 'RTECS',
	'chemFunctions_KEGG' => 'KEGG',
	'chemFunctions_ATCCode' => 'ATCCode',
	'chemFunctions_DrugBank' => 'DrugBank',
	'chemFunctions_ECNumber' => 'ECNumber',
	'chemFunctions_Formula' => 'Formula',
	'chemFunctions_Name' => 'IUPAC Name',
	'chemFunctions_ChemFormInputError' => 'Chemform: Eingabe Fehler!'
);

$messages['el'] = array(
	'chemicalsources' => 'Χημικές πηγές',
	'chemFunctions_ListPage' => 'Χημικές πηγές',
);

/** Esperanto (Esperanto)
 * @author Yekrats
 */
$messages['eo'] = array(
	'chemicalsources'                  => 'Kemiaj fontoj',
	'chemicalsource-desc'              => 'Aldonas la vikimarko <nowiki><chemform></nowiki> por kemiaj formuloj',
	'chemFunctions_ListPage'           => 'Kemiaj fontoj',
	'chemFunctions_CAS'                => 'CAS-nombro',
	'chemFunctions_ATCCode'            => 'ATC-kodo',
	'chemFunctions_ECNumber'           => 'ECNombro',
	'chemFunctions_Formula'            => 'Formulo',
	'chemFunctions_Name'               => 'IUPAC Nomo',
	'chemFunctions_ChemFormInputError' => 'Chemform: Eraro de enigo!',
);

/** French (Français)
 * @author Sherbrooke
 * @author Urhixidur
 * @author Grondin
 */
$messages['fr'] = array(
	'chemicalsources'                  => 'Sources pour la chimie',
	'chemicalsource-desc'              => 'Ajoute la balise <nowiki><chemform></nowiki>, pour les formules chimiques',
	'chemFunctions_ListPage'           => 'Sources pour la chimie',
	'chemFunctions_DataList'           => 'Voici, ci-dessous, une liste de liens vers des sites qui peuvent apporter des informations à propos des substances chimiques que vous recherchez.

* [http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI Composé $MIXCASNameFormula], NIST
* [http://ptcl.chem.ox.ac.uk/MSDS/ MSDS], Université d\'Oxford',
	'chemFunctions_CAS'                => 'Numéro CAS',
	'chemFunctions_EINECS'             => 'Numéro EINECS',
	'chemFunctions_CHEBI'              => 'ChEBI',
	'chemFunctions_SMILES'             => 'SMILE',
	'chemFunctions_InChI'              => 'InChl',
	'chemFunctions_ATCCode'            => 'Code ATC',
	'chemFunctions_ECNumber'           => 'Nomenclature EC',
	'chemFunctions_Formula'            => 'Formule',
	'chemFunctions_Name'               => 'Nom UICPA',
	'chemFunctions_ChemFormInputError' => 'Chemform, intrant erroné !',
);

/** Franco-Provençal (Arpetan)
 * @author ChrisPtDe
 */
$messages['frp'] = array(
	'chemicalsources'                  => 'Sôrses por la ch·imie',
	'chemFunctions_ListPage'           => 'Sôrses por la ch·imie',
	'chemFunctions_DataList'           => 'Siut una lista de lims vers des setos que pôvont aduire des enformacions a propôs de les substances ch·imiques que vos rechèrchiéd.

* [http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI <span title="« NIST » : pâge en anglès" style="text-decoration:none">Composâ $MIXCASNameFormula</span>], NIST
* [http://ptcl.chem.ox.ac.uk/MSDS/ <span title="« MSDS » : pâge en anglès" style="text-decoration:none">MSDS</span>], Univèrsitât d’Oxford',
	'chemFunctions_CAS'                => 'Numerô CAS',
	'chemFunctions_EINECS'             => 'Numerô EINECS',
	'chemFunctions_CHEBI'              => 'ChEBI',
	'chemFunctions_ATCCode'            => 'Code ATC',
	'chemFunctions_ECNumber'           => 'Numerô CE',
	'chemFunctions_Name'               => 'Nom IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform, entrent fôtif !',
);

/** Galician (Galego)
 * @author Alma
 * @author Xosé
 */
$messages['gl'] = array(
	'chemicalsources'        => 'Fontes químicas',
	'chemicalsource-desc'    => 'Engada a etiqueta <nowiki><chemform></nowiki>, para fórmulas químicas',
	'chemFunctions_ListPage' => 'Fontes químicas',
	'chemFunctions_DataList' => 'Embaixo hai unha listaxe das ligazóns aos sitios que poden proporcionar información acerca da sustancia química que procura.<br /><br /> * <a 
href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Composto $MIXCASNameFormula en NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Universidade de Oxford (UK) (non consultábel)</a><br />',
	'chemFunctions_CAS'      => 'número CAS',
	'chemFunctions_Formula'  => 'Fórmula',
);

/** Croatian (Hrvatski)
 * @author Dnik
 */
$messages['hr'] = array(
	'chemicalsources'        => 'Kemijski izvori',
	'chemFunctions_ListPage' => 'Kemijski izvori',
	'chemFunctions_Formula'  => 'Formula',
);

/** Upper Sorbian (Hornjoserbsce)
 * @author Michawiki
 */
$messages['hsb'] = array(
	'chemicalsources'                  => 'Chemiske žórła',
	'chemicalsource-desc'              => 'Přidawa tafličku <nowiki><chemform></nowiki> za chemiske formle',
	'chemFunctions_ListPage'           => 'Chemiske žórła',
	'chemFunctions_DataList'           => 'Deleka je lisćina websydłow, kotrež poskića dalše informacije wo substancy za kotrejž pytaš: * <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula at NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS uniwersity Oxford (UK) (njepřepytajomne)</a><br />',
	'chemFunctions_CAS'                => 'Ličba CAS',
	'chemFunctions_Formula'            => 'Formla',
	'chemFunctions_Name'               => 'Mjeno IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: Zapodatny zmylk',
);

/** Hungarian (Magyar)
 * @author Bdanee
 * @author Dorgan
 */
$messages['hu'] = array(
	'chemicalsources'                  => 'Kémiával kapcsolatos források',
	'chemFunctions_ListPage'           => 'Kémiával kapcsolatos források',
	'chemFunctions_DataList'           => 'Lenn azon oldalak listája található, amelyek információval szolgálhatnak az általad keresett kémiai anyagról.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula az NIST-nél</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS az Oxfordi Egyetemen (Egyesült Királyság) (nem kereshető)</a><br />',
	'chemFunctions_CAS'                => 'CAS-szám',
	'chemFunctions_ATCCode'            => 'ATC-kód',
	'chemFunctions_ECNumber'           => 'ECN-szám',
	'chemFunctions_Formula'            => 'Képlet',
	'chemFunctions_Name'               => 'IUPAC név',
	'chemFunctions_ChemFormInputError' => 'Chemform: bemeneti hiba!',
);

$messages['id'] = array(
	'chemicalsources' => 'Rujukan kimia',
	'chemFunctions_ListPage' => 'Rujukan kimia',
	'chemFunctions_DataList' => 'Berikut adalah daftar pranala ke situs yang mungkin menyediakan informasi mengenai zat kimia yang Anda cari.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Senyawa $MIXCASNameFormula di NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS di Oxford University (UK) (tanpa fasilitas pencarian)</a><br />',
	'chemFunctions_CAS' => 'Nomor CAS',
	'chemFunctions_EINECS' => 'EINECS',
	'chemFunctions_CHEBI' => 'CHEBI',
	'chemFunctions_PubChem' => 'PubChem',
	'chemFunctions_SMILES' => 'SMILES',
	'chemFunctions_InChI' => 'InChI',
	'chemFunctions_RTECS' => 'RTECS',
	'chemFunctions_KEGG' => 'KEGG',
	'chemFunctions_ATCCode' => 'ATCCode',
	'chemFunctions_DrugBank' => 'DrugBank',
	'chemFunctions_ECNumber' => 'ECNumber',
	'chemFunctions_Formula' => 'Formula',
	'chemFunctions_Name' => 'Nama IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: Kesalahan pada masukan!',
);

/** Icelandic (Íslenska)
 * @author S.Örvarr.S
 */
$messages['is'] = array(
	'chemFunctions_Formula' => 'Formúla',
);

$messages['it'] = array(
	'chemicalsources' => 'Informazioni sui composti chimici',
	'chemFunctions_ListPage' => 'Informazioni sui composti chimici',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Di seguito viene presentato un elenco di collegamenti a siti presso i quali si possono referire informazioni sui composti chimici cercati.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Scheda del composto $MIXCASNameFormula presso il NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">Scheda di sicurezza MSDS presso la Oxford University (UK) (ricerca non attiva)</a><br />',
	'chemFunctions_CAS' => 'Numero CAS',
	'chemFunctions_EINECS' => 'EINECS',
	'chemFunctions_CHEBI' => 'CHEBI',
	'chemFunctions_PubChem' => 'PubChem',
	'chemFunctions_SMILES' => 'SMILES',
	'chemFunctions_InChI' => 'InChI',
	'chemFunctions_RTECS' => 'RTECS',
	'chemFunctions_KEGG' => 'KEGG',
	'chemFunctions_ATCCode' => 'ATCCode',
	'chemFunctions_DrugBank' => 'DrugBank',
	'chemFunctions_ECNumber' => 'ECNumber',
	'chemFunctions_Formula' => 'Formula',
	'chemFunctions_Name' => 'Nome IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: Input non corretto.'
);

/** Japanese (日本語)
 * @author JtFuruhata
 */
$messages['ja'] = array(
	'chemicalsources'                  => '化学関連の検索',
	'chemicalsource-desc'              => '化学式のためのタグ <nowiki><chemform></nowiki> を追加する',
	'chemFunctions_ListPage'           => '化学関連の検索',
	'chemFunctions_DataList'           => '以下のリストは、あなたが検索した化学物質に関する情報を提供しているサイトです。

* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">$MIXCASNameFormula 化合物（アメリカ国立標準技術研究所）</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">化学物質安全性データシート（英オックスフォード大学、検索できません）</a><br />',
	'chemFunctions_CAS'                => 'CAS登録番号',
	'chemFunctions_ATCCode'            => 'ATC分類',
	'chemFunctions_ECNumber'           => 'EC番号',
	'chemFunctions_Formula'            => '化学式',
	'chemFunctions_Name'               => 'IUPAC名',
	'chemFunctions_ChemFormInputError' => 'Chemform: 入力エラー！',
);

/** Khmer (ភាសាខ្មែរ)
 * @author Chhorran
 */
$messages['km'] = array(
	'chemFunctions_Formula' => 'រូបមន្ត',
	'chemFunctions_Name'    => 'ឈ្មោះ IUPAC',
);

/** Kurdish (Latin) (Kurdî / كوردی (Latin))
 * @author Bangin
 */
$messages['ku-latn'] = array(
	'chemFunctions_Name' => 'Nava IUPAC',
);

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$messages['lb'] = array(
	'chemicalsources'        => 'Chemesch Quellen',
	'chemFunctions_CAS'      => 'CAS Nummer',
	'chemFunctions_ATCCode'  => 'ATCCode',
	'chemFunctions_ECNumber' => 'ECNummer',
	'chemFunctions_Formula'  => 'Formel',
	'chemFunctions_Name'     => 'IUPAC Numm',
);

/** Low German (Plattdüütsch)
 * @author Slomox
 */
$messages['nds'] = array(
	'chemFunctions_Formula' => 'Formel',
	'chemFunctions_Name'    => 'IUPAC-Naam',
);

/** Dutch (Nederlands)
 * @author SPQRobin
 * @author Siebrand
 */
$messages['nl'] = array(
	'chemicalsources'                  => 'Scheikundige bronnen',
	'chemicalsource-desc'              => 'Voegt de tag <nowiki><chemform></nowiki> toe voor scheikundige formules',
	'chemFunctions_ListPage'           => 'Scheikundige bronnen',
	'chemFunctions_DataList'           => 'Hieronder staat een lijst van pagina\'s die meer informatie over de scheikundige verbinding kunnen verschaffen.

* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Verbinding $MIXCASNameFormula op de pagina van het NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS op de pagina van de Oxford University (UK) (niet doorzoekbaar)</a><br />',
	'chemFunctions_CAS'                => 'CAS-nummer',
	'chemFunctions_ATCCode'            => 'ATC-code',
	'chemFunctions_ECNumber'           => 'EC-nummer',
	'chemFunctions_Formula'            => 'Formule',
	'chemFunctions_Name'               => 'IUPAC-naam',
	'chemFunctions_ChemFormInputError' => 'Chemform: Invoerfout!',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$messages['no'] = array(
	'chemicalsources'                  => 'Kjemiske kilder',
	'chemicalsource-desc'              => 'Legger til taggen <nowiki><chemform></nowiki> for kjemiske formler',
	'chemFunctions_ListPage'           => 'Kjemiske kilder',
	'chemFunctions_DataList'           => 'Nedenunder er en liste over lenker til sider som kan gi nyttig informasjon om den kjemiske substansen du leter etter.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula ved NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS ved Oxford University (UK) (ikke søkbar)</a><br />',
	'chemFunctions_CAS'                => 'CAS-nummer',
	'chemFunctions_ATCCode'            => 'ATCCode',
	'chemFunctions_ECNumber'           => 'ECNumber',
	'chemFunctions_Formula'            => 'Formel',
	'chemFunctions_Name'               => 'IUPAC-navn',
	'chemFunctions_ChemFormInputError' => 'Chemform: Input-feil!',
);

/** Occitan (Occitan)
 * @author Cedric31
 */
$messages['oc'] = array(
	'chemicalsources'                  => 'Fonts per la quimia',
	'chemicalsource-desc'              => 'Ajusta la balisa <nowiki><chemform></nowiki>, per las formulas quimicas',
	'chemFunctions_ListPage'           => 'Fonts per la quimia',
	'chemFunctions_DataList'           => 'Seguís una lista de ligams vèrs de sites que pòdon aportar d\'informacions a prepaus de las substçncias quimicas que recercatz. * [http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI Compausat $MIXCASNameFormula], NIST * [http://ptcl.chem.ox.ac.uk/MSDS/ MSDS], Universitat d\'Oxford',
	'chemFunctions_CAS'                => 'Numèro CAS',
	'chemFunctions_EINECS'             => 'Numèro EINECS',
	'chemFunctions_ATCCode'            => 'Còde ATC',
	'chemFunctions_ECNumber'           => 'Nomenclatura EC',
	'chemFunctions_Formula'            => 'Formula',
	'chemFunctions_Name'               => 'Nom UICPA',
	'chemFunctions_ChemFormInputError' => 'Chemform, dintrant erronèu!',
);

/** Piemontèis (Piemontèis)
 * @author Bèrto 'd Sèra
 * @author Siebrand
 */
$messages['pms'] = array(
	'chemicalsources'                  => 'Sorgiss Chìmiche',
	'chemFunctions_ListPage'           => 'Sorgiss Chìmiche',
	'chemFunctions_DataList'           => 'Di seguito viene presentato un elenco di collegamenti a siti presso i quali si possono referire informazioni sui composti chimici cercati.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Scheda del composto $MIXCASNameFormula presso il NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">Scheda di sicurezza MSDS presso la Oxford University (UK) (ricerca non attiva)</a><br />',
	'chemFunctions_CAS'                => 'Nùmer dël CAS',
	'chemFunctions_Formula'            => 'Fòrmula',
	'chemFunctions_Name'               => 'Nòm IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: eror ant ij dat!',
);

/** Pashto (پښتو)
 * @author Ahmed-Najib-Biabani-Ibrahimkhel
 */
$messages['ps'] = array(
	'chemicalsources'        => 'کيميايي سرچينې',
	'chemFunctions_ListPage' => 'کيميايي سرچينې',
	'chemFunctions_CAS'      => 'CAS ګڼ',
	'chemFunctions_Formula'  => 'فورمول',
	'chemFunctions_Name'     => 'IUPAC نوم',
);

/** Portuguese (Português)
 * @author Malafaya
 */
$messages['pt'] = array(
	'chemicalsources'                  => 'Fontes de química',
	'chemicalsource-desc'              => 'Adiciona a marca <nowiki><chemform></nowiki> para fórmulas químicas',
	'chemFunctions_ListPage'           => 'Fontes de química',
	'chemFunctions_DataList'           => 'Abaixo está uma lista de ligações para sítios que oferecem informação sobre a substância química que procura.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Composto $MIXCASNameFormula no NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Universidade de Oxford (Reino Unido) (não pesquisável)</a><br />',
	'chemFunctions_CAS'                => 'Número CAS',
	'chemFunctions_ATCCode'            => 'Código ATC',
	'chemFunctions_ECNumber'           => 'Número EC',
	'chemFunctions_Formula'            => 'Fórmula',
	'chemFunctions_Name'               => 'Nome IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: Erro nos dados introduzidos!',
);

/** Romanian (Română)
 * @author KlaudiuMihaila
 */
$messages['ro'] = array(
	'chemFunctions_Formula' => 'Formulă',
);

/** Russian (Русский)
 * @author .:Ajvol:.
 */
$messages['ru'] = array(
	'chemicalsources'                  => 'Источники по химии',
	'chemicalsource-desc'              => 'Добавляет тег <nowiki><chemform></nowiki> для химических формул',
	'chemFunctions_ListPage'           => 'Источники по химии',
	'chemFunctions_DataList'           => 'Ниже представлен список ссылок на сайты, которые могут содержать информацию об интересующем вас веществе.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Соединение $MIXCASNameFormula на сайте NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS на сайте Оксфордского университета (Великобритания) (поиск отсутствует)</a><br />',
	'chemFunctions_CAS'                => 'CAS-число',
	'chemFunctions_ATCCode'            => 'ATCCode',
	'chemFunctions_ECNumber'           => 'ECNumber',
	'chemFunctions_Formula'            => 'Формула',
	'chemFunctions_Name'               => 'IUPAC-название',
	'chemFunctions_ChemFormInputError' => 'Chemform: ошибка ввода!',
);

/** Slovak (Slovenčina)
 * @author Helix84
 */
$messages['sk'] = array(
	'chemicalsources'                  => 'Chemické zdroje',
	'chemicalsource-desc'              => 'Pridáva značku <nowiki><chemform></nowiki> pre chemické vzorce',
	'chemFunctions_ListPage'           => 'Chemické zdroje',
	'chemFunctions_DataList'           => 'Nižšie je zoznam odkazov na stránky, ktoré môžu poskytnúť informácie o chemikálii, ktorú používate.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Zlúčenina $MIXCASNameFormula na NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Oxford University (UK) (nedá sa vyhľadávať)</a><br />',
	'chemFunctions_CAS'                => 'CAS číslo',
	'chemFunctions_ATCCode'            => 'ATCCode',
	'chemFunctions_ECNumber'           => 'ECNumber',
	'chemFunctions_Formula'            => 'Vzorec',
	'chemFunctions_Name'               => 'IUPAC názov',
	'chemFunctions_ChemFormInputError' => 'Chemform: Chybný vstup!',
);

$messages['sr-ec'] = array(
	'chemicalsources' => 'Хемијски извори',
	'chemFunctions_ListPage' => 'Хемијски извори',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Овде се налази списак веза ка сајтовима који прожају информације о хемојском једињењу коју тражите.

* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Једињење $MIXCASNameFormula на NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS на Оксфордском универзитету (УК) (немогућа претрага)</a><br />',
	'chemFunctions_CAS' => 'CAS број',
	'chemFunctions_EINECS' => 'EINECS',
	'chemFunctions_CHEBI' => 'CHEBI',
	'chemFunctions_PubChem' => 'PubChem',
	'chemFunctions_SMILES' => 'SMILES',
	'chemFunctions_InChI' => 'InChI',
	'chemFunctions_RTECS' => 'RTECS',
	'chemFunctions_KEGG' => 'KEGG',
	'chemFunctions_ATCCode' => 'ATCCode',
	'chemFunctions_DrugBank' => 'DrugBank',
	'chemFunctions_ECNumber' => 'ECNumber',
	'chemFunctions_Formula' => 'Formula',
	'chemFunctions_Name' => 'IUPAC Name',
	'chemFunctions_ChemFormInputError' => 'ХемФормула: грешка!'
);

$messages['sr-el'] = array(
	'chemicalsources' => 'Hemijski izvori',
	'chemFunctions_ListPage' => 'Hemijski izvori',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Ovde se nalazi spisak veza ka sajtovima koji prožaju informacije o hemojskom jedinjenju koju tražite.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Jedinjenje $MIXCASNameFormula na NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Oksfordskom univerzitetu (UK) (nemoguća pretraga)</a><br />',
	'chemFunctions_CAS' => 'CAS broj',
	'chemFunctions_EINECS' => 'EINECS',
	'chemFunctions_CHEBI' => 'CHEBI',
	'chemFunctions_PubChem' => 'PubChem',
	'chemFunctions_SMILES' => 'SMILES',
	'chemFunctions_InChI' => 'InChI',
	'chemFunctions_RTECS' => 'RTECS',
	'chemFunctions_KEGG' => 'KEGG',
	'chemFunctions_ATCCode' => 'ATCCode',
	'chemFunctions_DrugBank' => 'DrugBank',
	'chemFunctions_ECNumber' => 'ECNumber',
	'chemFunctions_Formula' => 'Formula',
	'chemFunctions_Name' => 'IUPAC Name',
	'chemFunctions_ChemFormInputError' => 'HemFormula: greška!'
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$messages['stq'] = array(
	'chemicalsources'                  => 'Chemiske Wällen',
	'chemFunctions_DataList'           => 'Ätterfoulgjend fint me Links tou Sieden, do der eventuell Informatione uur chemiske Substanzen ounbjoode, ätter do Jie säike::<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Ferbiendenge $MIXCASNameFormula ap NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS an ju Oxford University (UK) (nit truchsäikboar)</a><br />',
	'chemFunctions_CAS'                => 'CAS Nummer',
	'chemFunctions_ATCCode'            => 'ATCCode',
	'chemFunctions_ECNumber'           => 'ECNumber',
	'chemFunctions_Formula'            => 'Formel',
	'chemFunctions_Name'               => 'IUPAC Noome',
	'chemFunctions_ChemFormInputError' => 'Chemform: Iengoawe-Failer!',
);

/** Sundanese (Basa Sunda)
 * @author Kandar
 */
$messages['su'] = array(
	'chemicalsources'                  => 'Rujukan kimia',
	'chemicalsource-desc'              => 'Nambahkeun tag <nowiki><chemform></nowiki>, pikeun rumus kimia',
	'chemFunctions_ListPage'           => 'Rujukan kimia',
	'chemFunctions_DataList'           => 'Di handap ieu dibéréndélkeun tumbu ka loka-loka nu sugan nyadiakeun émbaran ngeunaan jat kimia anu keur ditéang.<br/><br/>
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula at NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS at the Oxford University (UK) (teu bisa nyusud)</a><br />',
	'chemFunctions_CAS'                => 'Nomer CAS',
	'chemFunctions_ATCCode'            => 'ATCCode',
	'chemFunctions_ECNumber'           => 'ECNumber',
	'chemFunctions_Formula'            => 'Rumus',
	'chemFunctions_Name'               => 'Ngaran IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: salah asupan!',
);

/** Swedish (Svenska)
 * @author Lejonel
 * @author M.M.S.
 * @author Max sonnelid
 */
$messages['sv'] = array(
	'chemicalsources'                  => 'Kemiska källor',
	'chemicalsource-desc'              => 'Lägger till taggen <nowiki><chemform></nowiki>, för kemiska formler',
	'chemFunctions_ListPage'           => 'Kemiska källor',
	'chemFunctions_DataList'           => 'I listan härunder finns länkar till webbplatser som kan ha information om den kemiska substans du söker efter.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Förening $MIXCASNameFormula hos NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">Säkerhetsblad (MSDS) på Oxfords universitets webbplats</a><br />',
	'chemFunctions_CAS'                => 'CAS-nummer',
	'chemFunctions_ATCCode'            => 'ATC-kod',
	'chemFunctions_ECNumber'           => 'EC-nummer',
	'chemFunctions_Formula'            => 'Formel',
	'chemFunctions_Name'               => 'IUPAC-namn',
	'chemFunctions_ChemFormInputError' => 'Chemform: Fel i indata!',
);

/** Telugu (తెలుగు)
 * @author Veeven
 */
$messages['te'] = array(
	'chemicalsources'        => 'రసాయన మూలాలు',
	'chemFunctions_ListPage' => 'రసాయన మూలాలు',
	'chemFunctions_CAS'      => 'CAS సంఖ్య',
	'chemFunctions_Formula'  => 'సూత్రం',
);

/** Turkish (Türkçe)
 * @author Karduelis
 */
$messages['tr'] = array(
	'chemFunctions_Name' => 'IUPAC adı',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'chemicalsources'                  => 'Nguồn hóa học',
	'chemicalsource-desc'              => 'Thêm thẻ <nowiki><chemform></nowiki> để viết biểu thức hóa học',
	'chemFunctions_ListPage'           => 'Nguồn hóa học',
	'chemFunctions_DataList'           => 'Đây là danh sách những website có thể cung cấp thông tin về chất hóa học này:<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Chất $MIXCASNameFormula tại NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS tại Đại học Oxford (Anh)</a><br /> (không có bộ tìm kiếm)',
	'chemFunctions_CAS'                => 'Số CAS',
	'chemFunctions_Formula'            => 'Công thức',
	'chemFunctions_Name'               => 'Tên IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: lỗi nhập!',
);

