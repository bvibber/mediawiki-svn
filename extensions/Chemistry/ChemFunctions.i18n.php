
<?php
/**
 * Internationalisation file for ChemFunctions and Special:Chemicalsources extensions.
 *
 * @package MediaWiki
 * @subpackage Extensions
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

$wgChemFunctions_Parameters = array ('CAS', 'Formula', 'Name', 'EINECS', 'CHEBI', 'PubChem', 'SMILES', 'InChI', 'ATCCode', 'DrugBank', 'KEGG', 'ECNumber', 'RTECS');
$wgChemFunctions_Prefix = "ChemFunctions";

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
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Compound $MIXCASNameFormula at NIST</a><br />
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
	'ChemFunctions_Name' => 'IUPAC Name'
);

$wgChemFunctions_Messages['de'] = array(
	'chemicalsources' => 'Chemische Quellen',
	'ChemFunctions_ListPage' => 'Chemische Quellen',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Nachfolgend finden Sie Links zu Seiten, die eventuell Informationen über chemische Substanzen anbieten, nach denen Sie suchen:<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Verbindung $MIXCASNameFormula auf NIST</a><br />
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
	'ChemFunctions_Name' => 'IUPAC Name'
);

$wgChemFunctions_Messages['nl'] = array(
	'chemicalsources' => 'Chemicaliën bronnen',
	'ChemFunctions_ListPage' => 'Chemicaliën bronnen',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Hieronder staat een lijst van pagina\'s, die meer informatie over de chemische verbinding kunnen verschaffen.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Verbinding $MIXCASNameFormula op de pagina van het NIST</a><br />
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
	'ChemFunctions_Name' => 'IUPAC Naam'
);

$wgChemFunctions_Messages['sk'] = array(
	'chemicalsources' => 'Chemické zdroje',
	'ChemFunctions_ListPage' => 'Chemické zdroje',
	'ChemFunctions_SearchExplanation' => '',
	'ChemFunctions_DataList' => 'Nižšie je zoznam odkazov na stránky, ktoré môžu poskytnúť informácie o chemikálii, ktorú používate.<br /><br />
	* <a href="http://webbook.nist.gov/cgi/cbook.cgi?ID=$MIXCASNameFormula&Units=SI">Zlúčenina $MIXCASNameFormula na NIST</a><br />
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
	'ChemFunctions_Name' => 'IUPAC názov'
);

?>
