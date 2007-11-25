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
 *  Note: The names must be the same (also same case) as supplied in wgChemFunctions_Messages after the 'ChemFunctions_'
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
	'ChemFunctions_ListPage' => 'Chemical sources',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Below is a list of links to sites that may provide information about the chemical substance you are looking for.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula at NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS at the Oxford University (UK) (not searchable)</a><br />',
	'ChemFunctions_CAS' => 'CAS number',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATCCode',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'ECNumber',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'IUPAC Name',

	'ChemFunctions_ChemFormInputError' => 'Chemform: Input error!'
);

$wgChemFunctions_Messages['ar'] = array(
	'chemicalsources' => 'مصادر كيميائية',
	'ChemFunctions_ListPage' => 'مصادر كيميائية',
	'ChemFunctions_DataList' => 'بالأسفل قائمة بوصلات إلى مواقع قد تحتوي على معلومات عن المادة الكيميائية التي تبحث عنها.<br /><br /> * <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula at NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS في جامعة أكسفورد (UK) (غير قابل للبحث)</a><br />',
	'ChemFunctions_CAS' => 'رقم CAS',
	'ChemFunctions_EINECS' => 'Einecs',#identical but defined
	'ChemFunctions_CHEBI' => 'CHEBI',#identical but defined
	'ChemFunctions_PubChem' => 'PubChem',#identical but defined
	'ChemFunctions_SMILES' => 'SMILES',#identical but defined
	'ChemFunctions_InChI' => 'InChI',#identical but defined
	'ChemFunctions_RTECS' => 'RTECS',#identical but defined
	'ChemFunctions_KEGG' => 'KEGG',#identical but defined
	'ChemFunctions_ATCCode' => 'ATCCode',#identical but defined
	'ChemFunctions_DrugBank' => 'بنك المخدرات',
	'ChemFunctions_ECNumber' => 'رقم EC',
	'ChemFunctions_Formula' => 'الصيغة',
	'ChemFunctions_Name' => 'اسم IUPAC',
	'ChemFunctions_ChemFormInputError' => 'Chemform: خطأ إدخال!',
);

$wgChemFunctions_Messages['de'] = array(
	'chemicalsources' => 'Chemische Quellen',
	'ChemFunctions_ListPage' => 'Chemische Quellen',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Nachfolgend finden Sie Links zu Seiten, die eventuell Informationen über chemische Substanzen anbieten, nach denen Sie suchen:<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Verbindung $MIXCASNameFormula auf NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS an der Oxford University (UK) (nicht durchsuchbar)</a><br />',
	'ChemFunctions_CAS' => 'CAS Nummer',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATCCode',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'ECNumber',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'IUPAC Name',

	'ChemFunctions_ChemFormInputError' => 'Chemform: Eingabe Fehler!'
);

$wgChemFunctions_Messages['el'] = array(
	'chemicalsources' => 'Χημικές πηγές',
	'ChemFunctions_ListPage' => 'Χημικές πηγές',
);

$wgChemFunctions_Messages['fr'] = array(
	'chemicalsources' => 'Sources pour la chimie',
	'ChemFunctions_ListPage' => 'Sources pour la chimie',
	'ChemFunctions_DataList' => 'Suit une liste de liens vers des sites qui peuvent apporter des informations à propos des substances chimiques que vous recherchez.

* [http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI Composé $MIXCASNameFormula], NIST
* [http://ptcl.chem.ox.ac.uk/MSDS/ MSDS], Université d\'Oxford',
	'ChemFunctions_CAS' => 'Numéro CAS',
	'ChemFunctions_EINECS' => 'Numéro EINECS',
	'ChemFunctions_CHEBI' => 'ChEBI',
	'ChemFunctions_PubChem' => 'PubChem',#identical but defined
	'ChemFunctions_InChI' => 'InChl',
	'ChemFunctions_RTECS' => 'RTECS',#identical but defined
	'ChemFunctions_KEGG' => 'KEGG',#identical but defined
	'ChemFunctions_ATCCode' => 'Code ATC',
	'ChemFunctions_DrugBank' => 'DrugBank',#identical but defined
	'ChemFunctions_Formula' => 'Formule',
	'ChemFunctions_Name' => 'Nom UICPA',
	'ChemFunctions_ChemFormInputError' => 'Chemform, intrant erroné!',
);

$wgChemFunctions_Messages['frp'] = array(
	'chemicalsources' => 'Sôrses por la ch·imie',
	'ChemFunctions_ListPage' => 'Sôrses por la ch·imie',
	'ChemFunctions_DataList' => 'Siut una lista de lims vers des setos que pôvont aduire des enformacions a propôs de les substances ch·imiques que vos rechèrchiéd.

* [http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI <span title="« NIST » : pâge en anglès" style="text-decoration:none">Composâ $MIXCASNameFormula</span>], NIST
* [http://ptcl.chem.ox.ac.uk/MSDS/ <span title="« MSDS » : pâge en anglès" style="text-decoration:none">MSDS</span>], Univèrsitât d’Oxford',
	'ChemFunctions_CAS' => 'Numerô CAS',
	'ChemFunctions_EINECS' => 'Numerô EINECS',
	'ChemFunctions_CHEBI' => 'ChEBI',
	'ChemFunctions_ATCCode' => 'Code ATC',
	'ChemFunctions_ECNumber' => 'Numerô CE',
	'ChemFunctions_Name' => 'Nom IUPAC',
	'ChemFunctions_ChemFormInputError' => 'Chemform, entrent fôtif !',
);

$wgChemFunctions_Messages['gl'] = array(
	'chemicalsources' => 'Fontes químicas',
	'ChemFunctions_ListPage' => 'Fontes químicas',
	'ChemFunctions_DataList' => 'Embaixo hai unha listaxe das ligazóns aos sitios que poden proporcionar información acerca da sustancia química que procura.<br /><br /> * <a 
href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Composto $MIXCASNameFormula en NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Universidade de Oxford (UK) (non consultábel)</a><br />',
	'ChemFunctions_CAS' => 'número CAS',
);

$wgChemFunctions_Messages['hsb'] = array(
	'chemicalsources' => 'Chemiske žórła',
	'ChemFunctions_ListPage' => 'Chemiske žórła',
	'ChemFunctions_DataList' => 'Deleka je lisćina websydłow, kotrež poskića dalše informacije wo substancy za kotrejž pytaš:<br /><br /> * <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula at NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS uniwersity Oxford (UK) (njepřepytajomne)</a><br />',
	'ChemFunctions_CAS' => 'Ličba CAS',
	'ChemFunctions_EINECS' => 'EINECS',
	'ChemFunctions_Formula' => 'Formla',
	'ChemFunctions_Name' => 'Mjeno IUPAC',
	'ChemFunctions_ChemFormInputError' => 'Chemform: Zapodatny zmylk',
);

$wgChemFunctions_Messages['id'] = array(
	'chemicalsources' => 'Rujukan kimia',
	'ChemFunctions_ListPage' => 'Rujukan kimia',
	'ChemFunctions_DataList' => 'Berikut adalah daftar pranala ke situs yang mungkin menyediakan informasi mengenai zat kimia yang Anda cari.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Senyawa $MIXCASNameFormula di NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS di Oxford University (UK) (tanpa fasilitas pencarian)</a><br />',
	'ChemFunctions_CAS' => 'Nomor CAS',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATCCode',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'ECNumber',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'Nama IUPAC',
	'ChemFunctions_ChemFormInputError' => 'Chemform: Kesalahan pada masukan!',
);

$wgChemFunctions_Messages['it'] = array(
	'chemicalsources' => 'Informazioni sui composti chimici',
	'ChemFunctions_ListPage' => 'Informazioni sui composti chimici',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Di seguito viene presentato un elenco di collegamenti a siti presso i quali si possono referire informazioni sui composti chimici cercati.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Scheda del composto $MIXCASNameFormula presso il NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">Scheda di sicurezza MSDS presso la Oxford University (UK) (ricerca non attiva)</a><br />',
	'ChemFunctions_CAS' => 'Numero CAS',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATCCode',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'ECNumber',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'Nome IUPAC',

	'ChemFunctions_ChemFormInputError' => 'Chemform: Input non corretto.'

);

$wgChemFunctions_Messages['nl'] = array(
	'chemicalsources' => 'Chemicaliën bronnen',
	'ChemFunctions_ListPage' => 'Chemicaliën bronnen',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Hieronder staat een lijst van pagina\'s die meer informatie over de chemische verbinding kunnen verschaffen.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Verbinding $MIXCASNameFormula op de pagina van het NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS op de pagina van de Oxford University (UK) (geen zoekresultaten)</a><br />',
	'ChemFunctions_CAS' => 'CAS-nummer',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATC-code',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'EC-nummer',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'IUPAC-naam',

	'ChemFunctions_ChemFormInputError' => 'Chemform: Invoerfout!'
);

$wgChemFunctions_Messages['no'] = array(
	'chemicalsources' => 'Kjemiske kilder',
	'ChemFunctions_ListPage' => 'Kjemiske kilder',
	'ChemFunctions_DataList' => 'Nedenunder er en liste over lenker til sider som kan gi nyttig informasjon om den kjemiske substansen du leter etter.<br /><br />
* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compound $MIXCASNameFormula ved NIST</a><br />
* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS ved Oxford University (UK) (ikke søkbar)</a><br />',
	'ChemFunctions_CAS' => 'CAS-nummer',
	'ChemFunctions_EINECS' => 'Einecs',#identical but defined
	'ChemFunctions_CHEBI' => 'CHEBI',#identical but defined
	'ChemFunctions_PubChem' => 'PubChem',#identical but defined
	'ChemFunctions_SMILES' => 'SMILES',#identical but defined
	'ChemFunctions_InChI' => 'InChI',#identical but defined
	'ChemFunctions_RTECS' => 'RTECS',#identical but defined
	'ChemFunctions_KEGG' => 'KEGG',#identical but defined
	'ChemFunctions_ATCCode' => 'ATCCode',#identical but defined
	'ChemFunctions_DrugBank' => 'DrugBank',#identical but defined
	'ChemFunctions_ECNumber' => 'ECNumber',#identical but defined
	'ChemFunctions_Formula' => 'Formula',#identical but defined
	'ChemFunctions_Name' => 'IUPAC-navn',
	'ChemFunctions_ChemFormInputError' => 'Chemform: Input-feil!',
);

$wgChemFunctions_Messages['oc'] = array(
	'chemicalsources' => 'Fonts per la quimia',
	'ChemFunctions_ListPage' => 'Fonts per la quimia',
	'ChemFunctions_CAS' => 'Numèro CAS',
	'ChemFunctions_EINECS' => 'Numèro EINECS',
	'ChemFunctions_ATCCode' => 'Còde ATC',
	'ChemFunctions_Name' => 'Nom UICPA',
	'ChemFunctions_ChemFormInputError' => 'Chemform, dintrant erronèu!',
);

$wgChemFunctions_Messages['pms'] = array(
	'chemicalsources' => 'Sorgiss Chìmiche',
	'ChemFunctions_ListPage' => 'Sorgiss Chìmiche',
	'ChemFunctions_DataList' => 'Ambelessì sota a-i é na lista ëd sit ch\'a peulo smon-e d\'anformassion rësgoard a le sostanse chìmica dont as parla.<br /><br /> * <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Compòst $MIXCASNameFormula da \'nt ël NIST</a><br /> * <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS a l\'Università d\'Oxford (UK) (as peul nen sërchesse d\'ambelessì)</a><br />',
	'ChemFunctions_CAS' => 'Nùmer dël CAS',
	'ChemFunctions_Formula' => 'Fòrmula',
	'ChemFunctions_Name' => 'Nòm IUPAC',
	'ChemFunctions_ChemFormInputError' => 'Chemform: eror ant ij dat!',
);

$wgChemFunctions_Messages['ru'] = array(
	'chemicalsources' => 'Источники по химии',
	'ChemFunctions_ListPage' => 'Источники по химии',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Ниже представлен список ссылок на сайты, которые могут содержать информацию об интересующем вас веществе.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Соединение $MIXCASNameFormula на сайте NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS на сайте Оксфордского университета (Великобритания) (поиск отсутствует)</a><br />',
	'ChemFunctions_CAS' => 'CAS-число',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATCCode',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'ECNumber',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'IUPAC-название',

	'ChemFunctions_ChemFormInputError' => 'Chemform: ошибка ввода!'
);

$wgChemFunctions_Messages['sk'] = array(
	'chemicalsources' => 'Chemické zdroje',
	'ChemFunctions_ListPage' => 'Chemické zdroje',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Nižšie je zoznam odkazov na stránky, ktoré môžu poskytnúť informácie o chemikálii, ktorú používate.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Zlúčenina $MIXCASNameFormula na NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Oxford University (UK) (nedá sa vyhľadávať)</a><br />',
	'ChemFunctions_CAS' => 'CAS číslo',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATCCode',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'ECNumber',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'IUPAC názov',

	'ChemFunctions_ChemFormInputError' => 'Chemform: Input error!'
);

$wgChemFunctions_Messages['sr-ec'] = array(
	'chemicalsources' => 'Хемијски извори',
	'ChemFunctions_ListPage' => 'Хемијски извори',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Овде се налази списак веза ка сајтовима који прожају информације о хемојском једињењу коју тражите.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Једињење $MIXCASNameFormula на NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS на Оксфордском универзитету (УК) (немогућа претрага)</a><br />',
	'ChemFunctions_CAS' => 'CAS број',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATCCode',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'ECNumber',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'IUPAC Name',

	'ChemFunctions_ChemFormInputError' => 'ХемФормула: грешка!'
);

$wgChemFunctions_Messages['sr-el'] = array(
	'chemicalsources' => 'Hemijski izvori',
	'ChemFunctions_ListPage' => 'Hemijski izvori',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Ovde se nalazi spisak veza ka sajtovima koji prožaju informacije o hemojskom jedinjenju koju tražite.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Jedinjenje $MIXCASNameFormula na NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS na Oksfordskom univerzitetu (UK) (nemoguća pretraga)</a><br />',
	'ChemFunctions_CAS' => 'CAS broj',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATCCode',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'ECNumber',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'IUPAC Name',

	'ChemFunctions_ChemFormInputError' => 'HemFormula: greška!'
);

$wgChemFunctions_Messages['sr'] = $wgChemFunctions_Messages['sr-ec'];

$wgChemFunctions_Messages['vi'] = array(
	'chemicalsources' => 'Nguồn hóa học',
	'ChemFunctions_ListPage' => 'Nguồn hóa học',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Đây là danh sách những website có thể cung cấp thông tin về chất hóa học này:<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Chất $MIXCASNameFormula tại NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS tại Đại học Oxford (Anh)</a> (không có bộ tìm kiếm)<br />',
	'ChemFunctions_CAS' => 'Số CAS',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATCCode',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'ECNumber',
	'ChemFunctions_Formula' => 'Công thức',
	'ChemFunctions_Name' => 'Tên IUPAC',

	'ChemFunctions_ChemFormInputError' => 'Chemform: lỗi nhập!'
);
