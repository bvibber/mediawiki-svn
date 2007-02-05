<?php

$wgDefaultGoPrefix='Expression:';
# FIXME - get NS ids from DB (need to put this in a loader hook)
$wgNamespaceProtection[ 16 ] = array( 'editwikidata' );
$wgNamespaceProtection[ 24 ] = array( 'editwikidata' );
$wgGroupPermissions['wikidata']['editwikidata']=true;
$wgHooks['BeforePageDisplay'][]='addWikidataHeader';
$wgCustomHandlerPath = array('*'=>"{$IP}/extensions/Wikidata/OmegaWiki/");
require_once("{$IP}/extensions/Wikidata/SpecialLanguages.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialSuggest.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialDatasearch.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialTransaction.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialNeedsTranslation.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialImportLangNames.php");

function addWikidataHeader() {
  global $wgOut,$wgScriptPath;
  $wgOut->addScript("<script type='text/javascript' src='{$wgScriptPath}/extensions/Wikidata/OmegaWiki/suggest.js'></script>");
  $wgOut->addLink(array('rel'=>'stylesheet','type'=>'text/css','media'=>'screen, projection','href'=>"{$wgScriptPath}/extensions/Wikidata/OmegaWiki/suggest.css"));
  $wgOut->addLink(array('rel'=>'stylesheet','type'=>'text/css','media'=>'screen, projection','href'=>"{$wgScriptPath}/extensions/Wikidata/OmegaWiki/tables.css"));                                                                                                                                                                    
}

# all DMs will be put in this class by default.
$wgDefaultClassMids = array(402295);

?>