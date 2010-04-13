<?php
/**
 * Allows people to define a grammar in a wiki then use that grammar to input information to the same wiki
 * @file
 * @ingroup Extensions
 * @author Nathanael Thompson <than4213@gmail.com>
 * @copyright Copyright © 2010 Nathanael Thompson
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
if ( !defined( "MEDIAWIKI" ) ) {
	die( "This is not a valid entry point.\n" );
}

$wgExtensionCredits["other"][] = array(
	"path" => __FILE__,
	"name" => "ParseEngine",
	"author" => "Nathanael Thompson",
	"url" => "http://www.mediawiki.org/wiki/Extension:ParseEngine",
	"version" => "1.0",
	"descriptionmsg" => "parseengine-desc",
);

$dir = dirname( __FILE__ );
$wgAutoloadClasses["ParseEngine"] = "$dir/ParseEngine.body.php";

$wgTheParseEngine = new ParseEngine();
$wgHooks["BeforePreSaveTransform"][] = array($wgTheParseEngine, "parse", $wgParseEngineGrammar);
$wgHooks["ParserBeforeStrip"][] = "wfParseEngineCallFromParse";

define ( "NS_GRAMMAR" , 91628);
define ( "NS_GRAMMAR_TALK" , 91629);
$wgExtraNamespaces[NS_GRAMMAR] = "Grammar";
$wgExtraNamespaces[NS_GRAMMAR_TALK] = "Grammar_talk";

function wfParseEngineCallFromParse($unUsed, $text) {
	global $wgTheParseEngine, $wgParseEngineGrammar;
	return $wgTheParseEngine->parse($wgParseEngineGrammar, $text);
}
