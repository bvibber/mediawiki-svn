<?php
/**
 * Adds the 'templateinfo' action to the MediaWiki API.
 *
 * @author Yaron Koren
 */

/**
 * Protect against register_globals vulnerabilities.
 * This line must be present before any global variable is referenced.
 */
if (!defined('MEDIAWIKI')) die();

/**
 * @addtogroup API
 */
class APIQueryTemplateInfo extends ApiQueryBase {

	public function __construct( $query, $moduleName ) {
		parent :: __construct( $query, $moduleName, 'ti' );
	}

	private function validateXML( $xml ) {
		$xmlDTD =<<<END
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE template [
<!ELEMENT template (description?,params?,data*)>
<!ELEMENT params (param|group)*>
<!ELEMENT param (label?,description?,options?,type?,data*)>
<!ATTLIST param id ID #REQUIRED>
<!ELEMENT group (label?,description?,param*,data*)>
<!ELEMENT label (#PCDATA|msg)*>
<!ELEMENT description (#PCDATA|msg)*>
<!ELEMENT options (option*)>
<!ELEMENT option (#PCDATA|msg)*>
<!ELEMENT type (field*)>
<!ATTLIST type name CDATA #REQUIRED>
<!ELEMENT field EMPTY>
<!ATTLIST field name CDATA #REQUIRED>
<!ATTLIST field value CDATA #REQUIRED>
<!ELEMENT msg (#PCDATA)>
<!ATTLIST msg lang CDATA #REQUIRED>
<!ELEMENT data (field*)>
<!ATTLIST data app CDATA #REQUIRED>
]>

END;
		// we are using the SimpleXML library to do the XML validation
		// for now - this may change later
		// hide parsing warnings
		libxml_use_internal_errors(true);
		$xml_success = simplexml_load_string($xmlDTD . $xml);
		return $xml_success;
	}

	public function execute() {
		$params = $this->extractRequestParams();
		$titles = $this->getPageSet()->getGoodTitles();
		$this->addTables( 'page_props' );
		$this->addFields( array( 'pp_page', 'pp_value' ) );
		$this->addWhere( array(
			'pp_page' => array_keys( $titles ),
			'pp_propname' => 'templateinfo'
		) );
		if ( !is_null( $params['continue'] ) )
		{
			$fromid = intval( $params['continue'] );
			$this->addWhere( "pp_page >= $fromid" );
		}
		$this->addOption( 'ORDER BY', 'pp_page' );

		$res = $this->select(__METHOD__);
		while ( $row = $this->getDB()->fetchObject( $res ) ) {
			$vals = array( );
			if ($this->validateXML( $row->pp_value )) {
				ApiResult::setContent( $vals, $row->pp_value );
			} else {
				ApiResult::setContent( $vals, "Error! Invalid XML" );
			}
			$fit = $this->addPageSubItems( $row->pp_page, $vals );
			if( !$fit ) {
				$this->setContinueEnumParameter( 'continue', $row->pp_page );
				break;
			}
		}
	}
	
	public function getAllowedParams() {
		return array (
			'continue' => null,
		);
	}

	public function getParamDescription() {
		return array (
			'continue' => 'When more results are available, use this to continue',
		);
	}

	public function getDescription() {
		return 'Template information, defined by the Template Info extension (http://www.mediawiki.org/Extension:Template_Info)';
	}

	protected function getExamples() {
		return array (
			'api.php?action=query&prop=templateinfo&titles=Template:Foo|Template:Bar',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}

}
