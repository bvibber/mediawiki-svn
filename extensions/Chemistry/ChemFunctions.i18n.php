<?php

/**
 * MediaWiki Internationalisation file for ChemFunctions.php and SpecialChemicalsources.php.
 *
 * @addtogroup Extensions
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

/**
 * To write the i18n so that it functions with another listpage:
 *   1) copy this file to the i18n of your choice
 *   2) Choose your prefix (the best way, if this i18n file is called YourPrefix, then choose that as your Prefix
 *   3) Replace all occurences of ChemFunctions with the name of your Prefix.
 *   4) $wgYourPrefix_Parameters should contain the names of the parameters as they can be given to your specialpage
 *		In the ListPage you have created, the Magiccodes are then $ParameterName, so if the Parameter is called 'WhAtEvEr',
 *		then in the ListPage $WhAtEvEr will be replaced with the value of the parameter WhAtEvEr
 *   5) In the internationalisation messages, you need the following values:
 *		1) The name of your specialpage
 *		2) YourPrefix_ListPage -> the name of the page which will hold the links, this page should be in the project namespace
 *		3) YourPrefix_SearchExplanation -> a text which can appear as help above the input boxes, when no parameter is given to the page
 *		4) YourPrefix_DataList -> If the ListPage does not exist, this is a html string that is displayed as alternative
 *		5) For each of the parameters in $wgYourPrefix_Parameters you should have a YourPrefix_ParameterName,
 *			containing the string as you want it displayed.
 *			So, if you have a parameter 'WhAtEvEr', you should have a 'YourPrefix_WhAtEvEr' with value 'Whatever'
 */

/**
 * This is a list of all the possible parameters supplied to Special:ChemicalSources
 *  Note: The names must be the same (also same case) as supplied in wgChemFunctions_Messages after the 'chemFunctions_'
 *
 * Variables to be handled in parameters:
 *   CAS = The CAS-number of the chemical
 *   EINECS = The EINECS number of the chemical
 *   Name = The name of the chemical (not specific)
 *   Formula = The formula of the chemical (not by definition specific)
 *   PubChem = The PubChem number of the chemical
 *   SMILES = The SMILES notation of the chemical
 *   InChI = The InChI notation of the chemical
 *   ATCCode = The ATCCode for the chemical
 *   KEGG = The KEGG for the chemical
 *   RTECS = The RTECS code for the chemical
 *   DrugBank = The DrugBank code for the chemical
 *   ECNumber = The EC Number for the compound
 */

$wgChemFunctions_Prefix = "ChemFunctions";
$wgChemFunctions_Parameters = array ('CAS',
									 'Formula',
									 'Name',
									 'EINECS',
									 'CHEBI',
									 'PubChem',
									 'SMILES',
									 'InChI',
									 'ATCCode',
									 'DrugBank',
									 'KEGG',
									 'ECNumber',
									 'RTECS');

# Begin internationalisation

$wgChemFunctions_Messages = array();

$wgChemFunctions_Messages['en'] = array(
	'chemicalsources' => 'Chemical sources',
	'chemFunctions_ListPage' => 'Chemical sources',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Below is a list of links to sites that may provide information about the chemical substance you are looking for.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula at NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS at the Oxford University (UK) (not searchable)</a><br />',
	'chemFunctions_CAS' => 'CAS number',
	'chemFunctions_EINECS' => 'Einecs',
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
$wgChemFunctions_Messages['ar'] = array(
	'chemicalsources'                  => 'مصادر كيميائية',
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
$wgChemFunctions_Messages['bg'] = array(
	'chemFunctions_Formula' => 'Формула',
);

$wgChemFunctions_Messages['de'] = array(
	'chemicalsources' => 'Chemische Quellen',
	'chemFunctions_ListPage' => 'Chemische Quellen',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Nachfolgend finden Sie Links zu Seiten, die eventuell Informationen über chemische Substanzen anbieten, nach denen Sie suchen:<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Verbindung $MIXCASNameFormula auf NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS an der Oxford University (UK) (nicht durchsuchbar)</a><br />',
	'chemFunctions_CAS' => 'CAS Nummer',
	'chemFunctions_EINECS' => 'Einecs',
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

$wgChemFunctions_Messages['el'] = array(
	'chemicalsources' => 'Χημικές πηγές',
	'chemFunctions_ListPage' => 'Χημικές πηγές',
);

$wgChemFunctions_Messages['fr'] = array(
	'chemicalsources' => 'Sources pour la chimie',
	'chemFunctions_ListPage' => 'Sources pour la chimie',
	'chemFunctions_DataList' => 'Suit une liste de liens vers des sites qui peuvent apporter des informations à propos des substances chimiques que vous recherchez.

* [http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI Composé $MIXCASNameFormula], NIST
* [http://ptcl.chem.ox.ac.uk/MSDS/ MSDS], Université d\'Oxford',
	'chemFunctions_CAS' => 'Numéro CAS',
	'chemFunctions_EINECS' => 'Numéro EINECS',
	'chemFunctions_CHEBI' => 'ChEBI',
	'chemFunctions_SMILES' => 'SMILE',
	'chemFunctions_InChI' => 'InChl',
	'chemFunctions_ATCCode' => 'Code ATC',
	'chemFunctions_Formula' => 'Formule',
	'chemFunctions_Name' => 'Nom UICPA',
	'chemFunctions_ChemFormInputError' => 'Chemform, intrant erroné!',
);

$wgChemFunctions_Messages['frp'] = array(
	'chemicalsources' => 'Sôrses por la ch·imie',
	'chemFunctions_ListPage' => 'Sôrses por la ch·imie',
	'chemFunctions_DataList' => 'Siut una lista de lims vers des setos que pôvont aduire des enformacions a propôs de les substances ch·imiques que vos rechèrchiéd.

* [http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI <span title="« NIST » : pâge en anglès" style="text-decoration:none">Composâ $MIXCASNameFormula</span>], NIST
* [http://ptcl.chem.ox.ac.uk/MSDS/ <span title="« MSDS » : pâge en anglès" style="text-decoration:none">MSDS</span>], Univèrsitât d’Oxford',
	'chemFunctions_CAS' => 'Numerô CAS',
	'chemFunctions_EINECS' => 'Numerô EINECS',
	'chemFunctions_CHEBI' => 'ChEBI',
	'chemFunctions_ATCCode' => 'Code ATC',
	'chemFunctions_ECNumber' => 'Numerô CE',
	'chemFunctions_Name' => 'Nom IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform, entrent fôtif !',
);

$wgChemFunctions_Messages['gl'] = array(
	'chemicalsources' => 'Fontes químicas',
	'chemFunctions_ListPage' => 'Fontes químicas',
	'chemFunctions_DataList' => 'Embaixo hai unha listaxe das ligazóns aos sitios que poden proporcionar información acerca da sustancia química que procura.<br /><br /> * <a 
href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Composto $MIXCASNameFormula en NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Universidade de Oxford (UK) (non consultábel)</a><br />',
	'chemFunctions_CAS' => 'número CAS',
);

$wgChemFunctions_Messages['hsb'] = array(
	'chemicalsources' => 'Chemiske žórła',
	'chemFunctions_ListPage' => 'Chemiske žórła',
	'chemFunctions_DataList' => 'Deleka je lisćina websydłow, kotrež poskića dalše informacije wo substancy za kotrejž pytaš:<br /><br /> * <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula at NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS uniwersity Oxford (UK) (njepřepytajomne)</a><br />',
	'chemFunctions_CAS' => 'Ličba CAS',
	'chemFunctions_EINECS' => 'EINECS',
	'chemFunctions_Formula' => 'Formla',
	'chemFunctions_Name' => 'Mjeno IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: Zapodatny zmylk',
);

$wgChemFunctions_Messages['id'] = array(
	'chemicalsources' => 'Rujukan kimia',
	'chemFunctions_ListPage' => 'Rujukan kimia',
	'chemFunctions_DataList' => 'Berikut adalah daftar pranala ke situs yang mungkin menyediakan informasi mengenai zat kimia yang Anda cari.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Senyawa $MIXCASNameFormula di NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS di Oxford University (UK) (tanpa fasilitas pencarian)</a><br />',
	'chemFunctions_CAS' => 'Nomor CAS',
	'chemFunctions_EINECS' => 'Einecs',
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

$wgChemFunctions_Messages['it'] = array(
	'chemicalsources' => 'Informazioni sui composti chimici',
	'chemFunctions_ListPage' => 'Informazioni sui composti chimici',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Di seguito viene presentato un elenco di collegamenti a siti presso i quali si possono referire informazioni sui composti chimici cercati.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Scheda del composto $MIXCASNameFormula presso il NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">Scheda di sicurezza MSDS presso la Oxford University (UK) (ricerca non attiva)</a><br />',
	'chemFunctions_CAS' => 'Numero CAS',
	'chemFunctions_EINECS' => 'Einecs',
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

$wgChemFunctions_Messages['nl'] = array(
	'chemicalsources' => 'Scheikundige bronnen',
	'chemFunctions_ListPage' => 'Scheikundige bronnen',
	'chemFunctions_DataList' => 'Hieronder staat een lijst van pagina\'s die meer informatie over de scheikundige verbinding kunnen verschaffen.

* [http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI Verbinding $MIXCASNameFormula op de pagina van het NIST]
* [http://ptcl.chem.ox.ac.uk/MSDS/ MSDS op de pagina van de Oxford University (UK) (geen zoekresultaten)]',
	'chemFunctions_CAS' => 'CAS-nummer',
	'chemFunctions_ATCCode' => 'ATC-code',
	'chemFunctions_ECNumber' => 'EC-nummer',
	'chemFunctions_Name' => 'IUPAC-naam',
	'chemFunctions_ChemFormInputError' => 'Chemform: Invoerfout!',
);

$wgChemFunctions_Messages['no'] = array(
	'chemicalsources' => 'Kjemiske kilder',
	'chemFunctions_ListPage' => 'Kjemiske kilder',
	'chemFunctions_DataList' => 'Nedenunder er en liste over lenker til sider som kan gi nyttig informasjon om den kjemiske substansen du leter etter.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula ved NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS ved Oxford University (UK) (ikke søkbar)</a><br />',
	'chemFunctions_CAS' => 'CAS-nummer',
	'chemFunctions_Name' => 'IUPAC-navn',
	'chemFunctions_ChemFormInputError' => 'Chemform: Input-feil!',
);

$wgChemFunctions_Messages['oc'] = array(
	'chemicalsources' => 'Fonts per la quimia',
	'chemFunctions_ListPage' => 'Fonts per la quimia',
	'chemFunctions_CAS' => 'Numèro CAS',
	'chemFunctions_EINECS' => 'Numèro EINECS',
	'chemFunctions_ATCCode' => 'Còde ATC',
	'chemFunctions_Name' => 'Nom UICPA',
	'chemFunctions_ChemFormInputError' => 'Chemform, dintrant erronèu!',
);

$wgChemFunctions_Messages['pms'] = array(
	'chemicalsources' => 'Sorgiss Chìmiche',
	'chemFunctions_ListPage' => 'Sorgiss Chìmiche',
	'chemFunctions_DataList' => 'Ambelessì sota a-i é na lista ëd sit ch\'a peulo smon-e d\'anformassion rësgoard a le sostanse chìmica dont as parla.<br /><br /> * <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compòst $MIXCASNameFormula da \'nt ël NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS a l\'Università d\'Oxford (UK) (as peul nen sërchesse d\'ambelessì)</a><br />',
	'chemFunctions_CAS' => 'Nùmer dël CAS',
	'chemFunctions_Formula' => 'Fòrmula',
	'chemFunctions_Name' => 'Nòm IUPAC',
	'chemFunctions_ChemFormInputError' => 'Chemform: eror ant ij dat!',
);

$wgChemFunctions_Messages['pt'] = array(
	'chemicalsources' => 'Fontes de química',
	'chemFunctions_ListPage' => 'Fontes de química',
	'chemFunctions_CAS' => 'Número CAS',
	'chemFunctions_Formula' => 'Fórmula',
	'chemFunctions_ChemFormInputError' => 'Chemform: Erro nos dados introduzidos!',
);

$wgChemFunctions_Messages['ru'] = array(
	'chemicalsources' => 'Источники по химии',
	'chemFunctions_ListPage' => 'Источники по химии',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Ниже представлен список ссылок на сайты, которые могут содержать информацию об интересующем вас веществе.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Соединение $MIXCASNameFormula на сайте NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS на сайте Оксфордского университета (Великобритания) (поиск отсутствует)</a><br />',
	'chemFunctions_CAS' => 'CAS-число',
	'chemFunctions_EINECS' => 'Einecs',
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
	'chemFunctions_Name' => 'IUPAC-название',

	'chemFunctions_ChemFormInputError' => 'Chemform: ошибка ввода!'
);

$wgChemFunctions_Messages['sk'] = array(
	'chemicalsources' => 'Chemické zdroje',
	'chemFunctions_ListPage' => 'Chemické zdroje',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Nižšie je zoznam odkazov na stránky, ktoré môžu poskytnúť informácie o chemikálii, ktorú používate.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Zlúčenina $MIXCASNameFormula na NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Oxford University (UK) (nedá sa vyhľadávať)</a><br />',
	'chemFunctions_CAS' => 'CAS číslo',
	'chemFunctions_EINECS' => 'Einecs',
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
	'chemFunctions_Name' => 'IUPAC názov',

	'chemFunctions_ChemFormInputError' => 'Chemform: Input error!'
);

$wgChemFunctions_Messages['sr-ec'] = array(
	'chemicalsources' => 'Хемијски извори',
	'chemFunctions_ListPage' => 'Хемијски извори',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Овде се налази списак веза ка сајтовима који прожају информације о хемојском једињењу коју тражите.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Једињење $MIXCASNameFormula на NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS на Оксфордском универзитету (УК) (немогућа претрага)</a><br />',
	'chemFunctions_CAS' => 'CAS број',
	'chemFunctions_EINECS' => 'Einecs',
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

$wgChemFunctions_Messages['sr-el'] = array(
	'chemicalsources' => 'Hemijski izvori',
	'chemFunctions_ListPage' => 'Hemijski izvori',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Ovde se nalazi spisak veza ka sajtovima koji prožaju informacije o hemojskom jedinjenju koju tražite.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Jedinjenje $MIXCASNameFormula na NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Oksfordskom univerzitetu (UK) (nemoguća pretraga)</a><br />',
	'chemFunctions_CAS' => 'CAS broj',
	'chemFunctions_EINECS' => 'Einecs',
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

$wgChemFunctions_Messages['sr'] = $wgChemFunctions_Messages['sr-ec'];

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$wgChemFunctions_Messages['stq'] = array(
	'chemicalsources' => 'Chemiske Wällen',

);

/** Croatian (Hrvatski)
 * @author Dnik
 */
$wgChemFunctions_Messages['hr'] = array(
	'chemicalsources'        => 'Kemijski izvori',
	'chemFunctions_ListPage' => 'Kemijski izvori',
	'chemFunctions_Formula'  => 'Formula',
);

$wgChemFunctions_Messages['vi'] = array(
	'chemicalsources' => 'Nguồn hóa học',
	'chemFunctions_ListPage' => 'Nguồn hóa học',
	'chemFunctions_SearchExplanation' => '',
	'chemFunctions_DataList' => 'Đây là danh sách những website có thể cung cấp thông tin về chất hóa học này:<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Chất $MIXCASNameFormula tại NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS tại Đại học Oxford (Anh)</a> (không có bộ tìm kiếm)<br />',
	'chemFunctions_CAS' => 'Số CAS',
	'chemFunctions_EINECS' => 'Einecs',
	'chemFunctions_CHEBI' => 'CHEBI',
	'chemFunctions_PubChem' => 'PubChem',
	'chemFunctions_SMILES' => 'SMILES',
	'chemFunctions_InChI' => 'InChI',
	'chemFunctions_RTECS' => 'RTECS',
	'chemFunctions_KEGG' => 'KEGG',
	'chemFunctions_ATCCode' => 'ATCCode',
	'chemFunctions_DrugBank' => 'DrugBank',
	'chemFunctions_ECNumber' => 'ECNumber',
	'chemFunctions_Formula' => 'Công thức',
	'chemFunctions_Name' => 'Tên IUPAC',

	'chemFunctions_ChemFormInputError' => 'Chemform: lỗi nhập!'
);
