<?php

/**
 * A MediaWiki extension that adds a Specialpage for Chemical sources.
 *
 * The i18n file is required for operation!
 * Installation: copy this file and ChemFunctions.i18n.php into the extensions directory
 *   and add 'require_once( "$IP/extensions/SpecialChemicalsources.php" );' to localsettings.php (using the correct path)
 *
 * i18n is retrieved from ChemFunctions.i18n.php
 * wfSpecialChemicalSources (adds the specialpage)
 * Class SpecialChemicalsources is an extension of SpecialPage
 * Parameter checking is performed in the function "TransposeAndCheckParams"
 *
 * @addtogroup SpecialPage
 * @addtogroup Extensions
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

/**
 * Adapting this listpage to your own listpage
 *
 * 1) Write your own i18n file (see instructions there)
 * 2) copy this file to the name of your specialpage, and in that file:
 * 3) make the line "require_once( 'ChemFunctions.i18n.php' );" call your i18n file
 * 4) Replace all the occurences of the word 'chemicalsources' with the name of your specialpage
 * 5) Replace every occurence of the word ChemFunctions with your chosen prefix from your i18n.
 * 6) rewrite the function TransposeAndCheckParams
 *	  You will get a list $Params which contains: $Params['paramname']='value'
 *	  You have to return a list which contains: $transParams['thestringtoreplaceinyourpage'] = 'withwhatitshouldbereplaced'
 */

if (!defined('MEDIAWIKI')) die();

# Credentials.
$wgExtensionFunctions[] = 'wfSpecialChemicalsources';
$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Special:Chemicalsources',
	'description' => 'Special Page for Chemical sources',
	'author' => 'Dirk Beetstra',
	'url' => 'http://meta.wikimedia.org/wiki/Chemistry/SpecialChemicalsources.php'
);

#Includes
global $IP;
require_once ("$IP/includes/SpecialPage.php");
require_once ( dirname(__FILE__) . '/ChemFunctions.i18n.php' );

# Add the page
function wfSpecialChemicalsources () {
	SpecialPage::addPage( new SpecialChemicalsources );
}

class SpecialChemicalsources extends SpecialPage {
	private $Parameters, $Prefix;

	function SpecialChemicalsources() {
		global $wgChemFunctions_Parameters, $wgChemFunctions_Prefix;
		$this->Parameters = $wgChemFunctions_Parameters;
		$this->Prefix = $wgChemFunctions_Prefix;

		global $wgMessageCache, $wgChemFunctions_Messages;
		foreach( array_keys($wgChemFunctions_Messages) as $key) {
			$wgMessageCache->addMessages( $wgChemFunctions_Messages[$key], $key );
		}

		SpecialPage::SpecialPage( 'Chemicalsources' );
		$this->includable( false );
	}

	function execute () {
		global $wgOut, $wgRequest;

		$wgOut->setPagetitle( wfMsg("chemicalsources") );

		$Params = $wgRequest->getValues();

		$ParamsCheck = "";
		foreach ($this->Parameters as $key) {
			  if ( isset( $Params [$key] ) )
				$ParamsCheck .= $Params [$key];
		}

		if ($ParamsCheck) {
			$transParams = $this->TransposeAndCheckParams($Params);
			$this->OutputListPage($transParams);
		} else {
			$Params = $this->getParams();
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
		else $Params['PubChem'] = '';
		if ( isset( $Params['SMILES'] ) )
			$Params['SMILES'] = preg_replace( '/\ /', "", $Params['SMILES'] );
		else $Params['SMILES'] = '';
		if ( isset( $Params['InChI'] ) )
			$Params['InChI'] = preg_replace( '/\ /', "", $Params['InChI'] );
		else $Params['InChI'] = '';
		if ( isset( $Params['ATCCode'] ) )
			$Params['ATCCode'] = preg_replace( '/[^0-9\-]/', "", $Params['ATCCode'] );
		else $Params['ATCCode'] = '';
		if ( isset( $Params['KEGG'] ) )
			$Params['KEGG'] = preg_replace( '/[^C0-9\-]/', "", $Params['KEGG'] );
		else $Params['KEGG'] = '';
		if ( isset( $Params['RTECS'] ) )
			$Params['RTECS'] = preg_replace( '/\ /', "", $Params['RTECS'] );
		else $Params['RTECS'] = '';
		if ( isset( $Params['ECNumber'] ) )
			$Params['ECNumber'] = preg_replace( '/[^0-9\-]/', "", $Params['ECNumber'] );
		else $Params['ECNumber'] = '';
		if ( isset( $Params['Drugbank'] ) )
			$Params['Drugbank'] = preg_replace( '/[^0-9\-]/', "", $Params['Drugbank'] );
		else $Params['Drugbank'] = '';
		if ( isset( $Params['Formula']  ) )
			$Params['Formula'] = preg_replace( '/\ /', "" , $Params['Formula'] );
		else $Params['Formula'] = '';
		if ( isset( $Params['Name'] ) )
			$Params['Name'] = preg_replace( '/\ /', "%20", $Params['Name'] );
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

	#Create the actual page
	function OutputListPage($transParams) {
		global $wgOut;

		# check all the parameters before we put them in the page

		foreach ($transParams as $key => $value) {
			 $transParams[$key] = wfUrlEncode( htmlentities( preg_replace( "/\<.*?\>/","", $value) ) );
		}

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
				$bstext = wfMsg( $this->Prefix . '_DataList' );
				$bstext = strtr($bstext, $transParams);
				$wgOut->addHTML( $bstext );
			}
		}
	}

	#If no parameters supplied, get them!
	function getParams() {
		global $wgTitle, $wgOut;
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

