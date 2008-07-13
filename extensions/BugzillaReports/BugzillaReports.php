<?php
/*
See README for installation and usage
*/
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}
require_once("$IP/extensions/BugzillaReports/BMWExtension.php");
require_once("$IP/extensions/BugzillaReports/BugzillaQuery.php");

$wgExtensionCredits['parserhook'][] = array(
	'name'           => 'BugzillaReports',
	'version'        => '0.8-SNAPSHOT',
	'url'            => 'http://www.mediawiki.org/wiki/Extension:Bugzilla_Reports',
	'author'         => '[http://blog.bemoko.com Ian Homer]',
	'description'    => 'Provide bugzilla reports'
	'descriptionmsg' => 'bReport-desc'
);

$wgExtensionFunctions[] = 'efBugzillaReportsSetup';
$wgExtensionMessagesFiles['BugzillaReports'] = dirname(__FILE__) . '/BugzillaReports.i18n.php';

$wgHooks['LanguageGetMagic'][]       = 'efBugzillaReportsMagic';

$bzScriptPath = $wgScriptPath . '/extensions/BugzillaReports';
$bzHeadIncluded=false;  // flag to record whether the head has been included already so that we only include it once

/**
 * Register the function hook
 */
function efBugzillaReportsSetup() {
	global $wgParser;
	$wgParser->setFunctionHook( 'bugzilla', 'efBugzillaReportsRender' );
}

/**
 * Register the magic word
 */
function efBugzillaReportsMagic( &$magicWords, $langCode ) {
	$magicWords['bugzilla'] = array( 0, 'bugzilla' );
	return true;
}

/**
 * Call to render the bugzilla report
 */
function efBugzillaReportsRender( &$parser) {
	$bugzillaReport = new BugzillaReport( $parser );
	$args = func_get_args();
	array_shift( $args );
	return $bugzillaReport->render($args);
}

/**
 * The bugzilla report objects
 */
class BugzillaReport extends BMWExtension {

	# The handle on the query object
	var $query;

	# Default max rows for a report
	var $maxrowsFromConfig;
	var $maxrowsFromConfigDefault=100;

	# Default max rows which are used for aggregation of a bar chart report
	var $maxrowsForBarChartFromConfig;
	var $maxrowsForBarChartFromConfigDefault=500;

	public $dbuser,$bzserver,$interwiki;
	public $database,$host,$password;

	function BugzillaReport( &$parser ) {
		$this->parser =& $parser;

	}

	public function render($args) {
		global $wgBugzillaReports;
		global $bzScriptPath;
		global $wgDBserver,$wgDBname,$wgDBuser,$wgDBpassword;
		global $bzHeadIncluded;

		# Initialise query
		$this->query=new BugzillaQuery($this);
		$this->extractOptions($args);

		if (!$bzHeadIncluded) {
			$bzHeadIncluded=true;
			$this->parser->mOutput->addHeadItem('<link rel="stylesheet" type="text/css" media="screen, projection" href="' . $bzScriptPath . '/skins/bz_main.css" />');
			$this->parser->mOutput->addHeadItem('<script type="text/javascript" src="' . $bzScriptPath . '/scripts/jquery-1.2.6.min.js" ></script>');
			$script=<<< EOH
<script type= "text/javascript">
$(document).ready(function(){
	$("div.bz_comment").hide();
	$("tr.bz_bug").hover(
		function () {
			$(this).find("td div.bz_comment").show();
		},
		function () {
			$(this).find("td div.bz_comment").hide();
		}
	)
});
</script>
EOH;
			$this->parser->mOutput->addHeadItem($script);
		}

	$this->dbuser=$this->getProperty("user",$wgDBuser);
	$this->bzserver=$this->getProperty("bzserver","bzserver-property-not-set");
	$this->interwiki=$this->getProperty("interwiki",null);
	$this->database=$wgBugzillaReports['database'];
	$this->host=$wgBugzillaReports['host'];
	$this->password=$wgBugzillaReports['password'];
	$this->maxrowsFromConfig=
		$this->getProperty("maxrows",$this->maxrowsFromConfigDefault);
	$this->maxrowsForBarChartFromConfig=
		$this->getProperty("maxrowsbar",
			$this->maxrowsForBarChartFromConfigDefault);

	$this->debug && $this->debug("Rendering BugzillaReport");
	return $this->query->render().$this->getWarnings();
	}

	#
	# Set value - implementation of the abstract function from BMWExtension
	#
	protected function set($name,$value) {
		# debug variable is store on this object
		if ($name=="debug") {
			$this->$name=$value;
		} else {
			$this->query->set($name,$value);
		}
	}

	protected function getParameterRegex($name) {
		if ($name=="debug") {
			return "/^1$/";
		} else {
			return $this->query->getParameterRegex($name);
		}
	}

	function getProperty($name,$default) {
		global $wgBugzillaReports;
		$value;
		if (array_key_exists($name,$wgBugzillaReports)) {
			$value=$wgBugzillaReports[$name];
		} else {
			$value=$default;
		}
		$this->debug &&
			$this->debug("Env property $name=$value");
		return $value;
	}

	public function getErrorMessage($key) {
		$args = func_get_args();
		array_shift( $args );
		wfLoadExtensionMessages( 'BugzillaReports' );
		return '<strong class="error">BugzillaReports : '.
			wfMsgForContent($key,$args) . '</strong>';
	}
}
