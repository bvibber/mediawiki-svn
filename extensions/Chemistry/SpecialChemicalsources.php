<?php

/**
 * Special List-page adapted for Chemical sources.
 *
 * The i18n file is required for operation!
 *
 * i18n is retrieved from ChemFunctions.i18n.php
 * wfSpecialChemicalSources (adds the specialpage
 * Class SpecialChemicalsources is an extension of SpecialPage
 * Parameter checking is performed in the function "TransposeAndCheckParams"
 *
 *
 * @package MediaWiki
 * @subpackage SpecialPage
 */

if (!defined('MEDIAWIKI')) die();

# Credentials.
$wgExtensionFunctions[] = 'wfSpecialChemicalsources';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Special:Chemicalsources',
	'description' => 'Special Page for Chemical sources',
	'author' => 'Dirk Beetstra',
	'url' => 'http://meta.wikimedia.org/wiki/Chemistry'
);

# Includes
global $IP;
require_once ("$IP/includes/SpecialPage.php");
require_once( 'ChemFunctions.i18n.php' );

# Add the page
function wfSpecialChemicalsources () {
	SpecialPage::addPage( new SpecialChemicalsources );
}

class SpecialChemicalsources extends SpecialPage {
	var $Parameters, $Prefix;

	function SpecialChemicalsources() {
		global $wgChemFunctions_Parameters, $wgChemFunctions_Prefix;
		$this->Parameters = $wgChemFunctions_Parameters;
		$this->Prefix = $wgChemFunctions_Prefix;

		global $wgMessageCache, $wgChemFunctions_Messages;
		foreach( $wgChemFunctions_Messages as $key => $value ) {
			$wgMessageCache->addMessages( $wgChemFunctions_Messages[$key], $key );
		}

		SpecialPage::SpecialPage( 'Chemicalsources' );
		$this->includable( false );
	}

	function execute ($par) {
		global $wgOut, $wgRequest, $wgContLang, $wgScript, $wgServer, $wgTitle;

		$wgOut->setPagetitle( wfMsg("chemicalsources") );

		$Params = $wgRequest->getValues();

		$ParamsCheck = "";
		foreach ($this->Parameters as $key) {
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

	#Create the actual page
	function OutputChemicalSources($transParams) {
		global $wgOut;

		# First, see if we have a custom list setup
		$bstitle = Title::makeTitleSafe( NS_PROJECT, wfMsg( $this->Prefix . '_ListPage' ) );
		if( $bstitle ) {
			$revision = Revision::newFromTitle( $bstitle );
			if( $revision ) {
				$bstext = $revision->getText();
				if( $bstext ) {
					$bstext = strtr($bstext, $transParams);
					$wgOut->addWikiText( $bstext );
				}
			} else {
				$bstext = wfMsg( $Prefix . '_DataList' );
				$bstext = strtr($bstext, $transParams);
				$wgOut->addHTML( $bstext );
			}
		}
	}

	# Check the parameters supplied, make the mixed parameters, and put them into the transpose matrix.
	function TransposeAndCheckParams($Params) {
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

		# Create some new from old ones

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

		# Put the parameters into the transpose array:

		$transParams = array("\$MIXCASNameFormula" => $TEMPCASNAMEFORMULA,
							 "\$MIXCASName" => $TEMPCASNAME,
							 "\$MIXCASFormula" => $TEMPCASFORMULA,
							 "\$MIXNameFormula" => $TEMPNAMEFORMULA);
		foreach ($this->Parameters as $key) {
			if ( isset( $Params[$key] ) ) {
				$transParams["\$" . $key] =  $Params[$key] ;
			} else {
				$transParams["\$" . $key] =  "" ;
			}
		}
		return $transParams;
	}

	#If no parameters supplied, get them!
	function getParams() {
		global $wgTitle, $wgOut; $wfMsg;
		$action = $wgTitle->escapeLocalUrl();
		$go = htmlspecialchars( wfMsg( "go" ) );

		$wgOut->addWikitext ( wfMsg($this->Prefix . '_SearchExplanation'));
		$wgOut->addHTML("<table><tr><td>");
		foreach ($this->Parameters as $key) {
		   $this->GetParam_Row($this->Prefix . "_" . $key, $key, $action, $go);
		}
		$wgOut->addHTML("</table>");
	}

	#Creates a table row
	function GetParam_Row($p, $q, $action, $go) {
		global $wgOut;
		$wgOut->addHTML ( wfMsg( $p ) . ": ");
		$wgOut->addHTML("</td><td>
			<form action=\"$action\" method='post'>
				<input name=\"$q\" id=\"$q\" />
				<input type='submit' value=\"$go\" />
			</form>
		</td></tr>");
		$wgOut->addHTML("<tr><td>");
	}
}

#End of php.
?>
