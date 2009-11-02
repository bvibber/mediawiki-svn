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
class TemplateInfoAPI extends ApiBase {

	public function __construct($query, $moduleName) {
		parent :: __construct($query, $moduleName);
	}

	public function execute() {
		global $wgContLang, $wgParser, $egTemplateInfoXML;

		$params = $this->extractRequestParams();
		$template_name = $params['template'];

		if (strlen($template_name) == 0) {
			$this->dieUsage("A template name must be specified", 'param_substr');
		}

		$template_title = Title::makeTitleSafe(NS_TEMPLATE, $template_name);
		if (! $template_title->exists()) {
			$this->dieUsage("A template does not exist by this name", 'param_substr');
		}
		$parser_options = new ParserOptions();
		$article = new Article($template_title);
		$text = $wgParser->parse($article->getContent(), $template_title, $parser_options);
		if (empty($egTemplateInfoXML)) {
			$this->dieUsage("This template does not contain an XML definition.", 'param_substr');
		}

		$data = array($egTemplateInfoXML);

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
		// hide parsing warnings
		libxml_use_internal_errors(true);
		$xml_success = simplexml_load_string($xmlDTD . $egTemplateInfoXML);
		if (! $xml_success) {
			$this->dieUsage("Template contains invalid XML", 'badxml');
		}

		// Set top-level elements
		$result = $this->getResult();
		$result->setIndexedTagName($data, 'p');
		$result->addValue(null, $this->getModuleName(), $data);
	}

	protected function getAllowedParams() {
		return array (
			'template' => null,
		);
	}

	protected function getParamDescription() {
		return array (
			'template' => 'The name of the template to retrieve information for',
		);
	}

	protected function getDescription() {
		return 'Template information, defined by the Templat Info extension (http://www.mediawiki.org/Extension:Template_Info)';
	}

	protected function getExamples() {
		return array (
			'api.php?action=templateinfo&template=My_template',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}

}
