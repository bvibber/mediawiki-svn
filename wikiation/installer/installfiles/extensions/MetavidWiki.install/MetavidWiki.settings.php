<?PHP

require_once( "$IP/extensions/ParserFunctions/ParserFunctions.php" );

//set up namespace:
$smwgNamespaceIndex=100;
//set metavid namespace to smw namespace + 6
$mvNamespaceIndex=$smwgNamespaceIndex + 6;
include_once("$IP/extensions/MetavidWiki/includes/MV_Settings.php");

//include SemanticMediaWiki settings (if used) 
include_once("$IP/extensions/SemanticMediaWiki/includes/SMW_Settings.php");
enableSemantics('localhost');

//include after enableSemantics to enable metavid Extension semantic wiki overrides
enableMetavid();

?>
