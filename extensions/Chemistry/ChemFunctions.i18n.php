<?php

/**
 * MediaWiki Internationalisation file for ChemFunctions.php and SpecialChemicalsources.php.
 *
 * @package MediaWiki
 * @subpackage Extensions
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

/**
 *  en
 */

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

/**
 * de
 */

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

/**
 * it
 */

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

/**
 * nl
 */

$wgChemFunctions_Messages['nl'] = array(
	'chemicalsources' => 'Chemicaliën bronnen',
	'ChemFunctions_ListPage' => 'Chemicaliën bronnen',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Hieronder staat een lijst van pagina\'s, die meer informatie over de chemische verbinding kunnen verschaffen.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&amp;Units=SI">Verbinding $MIXCASNameFormula op de pagina van het NIST</a><br />
	* <a href="http://ptcl.chem.ox.ac.uk/MSDS/">MSDS op de pagina van de Oxford University (UK) (geen zoekresultaten)</a><br />',
	'ChemFunctions_CAS' => 'CAS nummer',
	'ChemFunctions_EINECS' => 'Einecs',
	'ChemFunctions_CHEBI' => 'CHEBI',
	'ChemFunctions_PubChem' => 'PubChem',
	'ChemFunctions_SMILES' => 'SMILES',
	'ChemFunctions_InChI' => 'InChI',
	'ChemFunctions_RTECS' => 'RTECS',
	'ChemFunctions_KEGG' => 'KEGG',
	'ChemFunctions_ATCCode' => 'ATC Code',
	'ChemFunctions_DrugBank' => 'DrugBank',
	'ChemFunctions_ECNumber' => 'EC Nummer',
	'ChemFunctions_Formula' => 'Formula',
	'ChemFunctions_Name' => 'IUPAC Naam',

	'ChemFunctions_ChemFormInputError' => 'Chemform: Input fout!'
);

/**
 * sk
 */

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

/**
 *  vi
 */

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

?>
