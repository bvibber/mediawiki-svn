<?php
/**
 * Allows people to define a grammar in a wiki format then use that grammar to input information to the wiki
 * @file
 * @ingroup Extensions
 * @author Nathanael Thompson <than4213@gmail.com>
 * @copyright Copyright © 2009 Nathanael Thompson
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
$wgHooks["BeforePreSaveTransform"][] = array(new ParseEngine(), "parse", $wgParseEngineGrammar);

