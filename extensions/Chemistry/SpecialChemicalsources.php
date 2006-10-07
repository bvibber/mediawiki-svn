<?php

/**
 * This Special page accepts one or more out of a set of parameters:
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
 *
 *   From these the parameters $CASNameFormula, $CASName, $CASFormula and $NameFormula are generated.
 *
 *  These parameters are built into the page [[Wikipedia:Chemical Sources]] by replacement
 *  of the $ codes (empty codes giving empty strings).
 *
 *  Note: ChemFunctions.i18n.php has to be stored in the same folder like SpecialChemicalsources.php 
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */

if (!defined('MEDIAWIKI')) die();

$wgExtensionFunctions[] = 'wfSpecialChemicalsources';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Special:Chemicalsources.php',
	'description' => 'Special Page for Chemical sources',
	'author' => 'Dirk Beetstra',
	'url' => 'http://meta.wikimedia.org/wiki/Chemistry'
);

function wfSpecialChemicalsources () {
	global $IP, $wgMessageCache, $wgServer, $wgChemFunctions_Messages, $wgChemFunctions_ChemicalIdentifiers;

	require_once( 'ChemFunctions.i18n.php' );
	require_once ("$IP/includes/SpecialPage.php");

	global $wgMessageCache, $wgChemFunctions_Messages;
	foreach( $wgChemFunctions_Messages as $key => $value ) {
		$wgMessageCache->addMessages( $wgChemFunctions_Messages[$key], $key );
	}

	class SpecialChemicalsources extends SpecialPage {

		/**
		 * Constructor
		 */
		function SpecialChemicalsources() {
			SpecialPage::SpecialPage( 'Chemicalsources' );
			$this->includable( false );
		}

		function execute ($par) {
			global $wgOut, $wgRequest, $wgContLang, $wgScript, $wgServer, $wgTitle, $wgChemFunctionChemIdent;

			$wgOut->setPagetitle( wfMsg('ChemFunctions_Chemicalsources') );
			$Params = $wgRequest->getValues();
			$ParamsCheck = "";
			global $wgChemFunctions_ChemicalIdentifiers;
			foreach ($wgChemFunctions_ChemicalIdentifiers as $key) {
			  	if ( isset( $Params [$key] ) )
					$ParamsCheck .= $Params [$key];
			}
			if ($ParamsCheck) {
				$transParams = $this->TransposeAndCheckParams($Params);
				$this->OutputChemicalSources($transParams);
			} else {
				$Params = $this->getParams();
			}
		}

		function OutputChemicalSources($transParams) {
#Create the actual page
			global $wgOut;
			# First, see if we have a custom list setup in
			# [[Wikipedia:Chemical sources]] or equivalent.
			$bstitle = Title::makeTitleSafe( NS_PROJECT, wfMsg( 'ChemFunctions_ChemicalsourcesPage' ) );
			if( $bstitle ) {
				$revision = Revision::newFromTitle( $bstitle );
				if( $revision ) {
					$bstext = $revision->getText();
					if( $bstext ) {
						$bstext = strtr($bstext, $transParams);
						$wgOut->addWikiText( $bstext );
					}
				} else {
					$bstext = wfMsg( 'ChemFunctions_ChemicalDataList' );
					$bstext = strtr($bstext, $transParams);
					$wgOut->addHTML( $bstext );
				}
			}
		}

		function TransposeAndCheckParams($Params) {
# Check the parameters supplied
# Make the mixed parameters
# and put them into the transpose matrix.
			if ( isset( $Params['CAS'] ) )
				$Params['CAS'] = preg_replace( '/[^0-9\-]/', "", $Params['CAS'] );
			else $Params['CAS'] = '';
			if ( isset( $Params['EINECS'] ) )
			 	$Params['EINECS'] = preg_replace( '/[^0-9\-]/', "", $Params['EINECS'] );
			else $Params['EINECS'] = '';
			if ( isset( $Params['CHEBI'] ) )
				$Params['CHEBI'] = preg_replace( '/[^0-9\-]/', "", $Params['CHEBI'] );
			else $Params['CHEBI'] = '';
			if ( isset( $Params['PubChem'] ) )
				$Params['PubChem'] = preg_replace( '/[^0-9\-]/', "", $Params['PubChem'] );
			/*
			if ( isset( $Params['SMILES'] ) )
				$Params['SMILES'] = $Params['SMILES'];
			else $Params['SMILES'] = '';
			if ( isset( $Params['InChI'] ) )
				$Params['InChI'] = $Params['InChI'];
			else $Params['InChI'] = '';
			*/
			if ( isset( $Params['ATCCode'] ) )
				$Params['ATCCode'] = preg_replace( '/[^0-9\-]/', "", $Params['ATCCode'] );
			else $Params['ATCCode'] = '';
			if ( isset( $Params['KEGG'] ) )
				$Params['KEGG'] = preg_replace( '/[^C0-9\-]/', "", $Params['KEGG'] );
			else $Params['KEGG'] = '';
			if ( isset( $Params['RTECS'] ) )
				$Params['RTECS'] = preg_replace( '/[^0-9\-]/', "", $Params['RTECS'] );
			else $Params['RTECS'] = '';
			if ( isset( $Params['ECNumber'] ) )
				$Params['ECNumber'] = preg_replace( '/[^0-9\-]/', "", $Params['ECNumber'] );
			else $Params['ECNumber'] = '';
			if ( isset( $Params['Drugbank'] ) )
				$Params['Drugbank'] = preg_replace( '/[^0-9\-]/', "", $Params['Drugbank'] );
			else $Params['Drugbank'] = '';
			if ( isset( $Params['Formula']  ) )
				$Params['Formula'] = preg_replace( "<,*?>", "", $Params['Formula'] );
			else $Params['Formula'] = '';
			if ( isset( $Params['Name'] ) )
				$Params['Name'] = str_replace( " ", "%20", $Params['Name'] );
			else $Params['Name'] = '';


			$TEMPCASNAMEFORMULA = $Params["CAS"];
			if(empty ($TEMPCASNAMEFORMULA)){
				$TEMPCASNAMEFORMULA = $Params["Formula"];
			}
			if(empty ($TEMPCASNAMEFORMULA)){
				$TEMPCASNAMEFORMULA = $Params["Name"];
			}

			$TEMPNAMEFORMULA = $Params["Name"];
			if(empty ($TEMPNAMEFORMULA)){
				$TEMPNAMEFORMULA = $Params["Formula"];
			}

			$TEMPCASFORMULA = $Params["CAS"];
			if(empty ($TEMPCASFORMULA)){
				$TEMPCASFORMULA = $Params["Formula"];
			}

			$TEMPCASNAME = $Params["CAS"];
			if(empty ($TEMPCASNAME)){
				$TEMPCASNAME = $Params["Name"];
			}

			$transParams = array("\$MIXCASNameFormula" => $TEMPCASNAMEFORMULA,
								 "\$MIXCASName" => $TEMPCASNAME,
								 "\$MIXCASFormula" => $TEMPCASFORMULA,
								 "\$MIXNameFormula" => $TEMPNAMEFORMULA);
			global $wgChemFunctions_ChemicalIdentifiers;
			foreach ($wgChemFunctions_ChemicalIdentifiers as $key) {
				if ( isset( $Params[$key] ) ) 
					$transParams["\$" . $key] =  $Params[$key] ;
			}
			return $transParams;
		}


		function getParams() {
#If no parameters supplied, get them!
			global $wgTitle, $wgOut; $wfMsg;
			$action = $wgTitle->escapeLocalUrl();
			$go = htmlspecialchars( wfMsg( "go" ) );

			global $wgChemFunctions_ChemicalIdentifiers;
			$wgOut->addHTML("<table><tr><td>");
			foreach ($wgChemFunctions_ChemicalIdentifiers as $key) {
			   $this->GetParam_Row("ChemFunctions_" . $key, $key, $action, $go);
			}
			$wgOut->addHTML("</table>");
		}

		function GetParam_Row($p, $q, $action, $go) {
#Creates a table row
			global $wgOut;
			$wgOut->addWikitext("[[" . htmlspecialchars( wfMsg( $p ) ) . "]]: ");
			$wgOut->addHTML("</td><td>
				<form action=\"$action\" method='post'>
					<input name=\"$q\" id=\"$q\" />
					<input type='submit' value=\"$go\" />
				</form>
			</td></tr>");
			$wgOut->addHTML("<tr><td>");
		}

	}

	SpecialPage::addPage( new SpecialChemicalsources );

}

#End of php.
?>
