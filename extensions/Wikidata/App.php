<?php

$wgDefaultGoPrefix='Expression:';
$wgHooks['BeforePageDisplay'][]='addWikidataHeader';
$wgHooks['GetEditLinkTrail'][]='addWikidataEditLinkTrail';
$wgHooks['GetHistoryLinkTrail'][]='addHistoryLinkTrail';
$wgExtensionFunctions[]='initializeWikidata';

$wgCustomHandlerPath = array('*'=>"{$IP}/extensions/Wikidata/OmegaWiki/");
$wgDefaultClassMids = array(402295);

# The term dataset prefix identifies the Wikidata instance that will
# be used as a resource for obtaining language-independent strings
# in various places of the code. If the term db prefix is empty,
# these code segments will fall back to (usually English) strings.
# If you are setting up a new Wikidata instance, you may want to
# set this to ''.
$wdTermDBDataSet='uw';

# This is the dataset that should be shown to all users by default.
# It _must_ exist for the Wikidata application to be executed 
# successfully.
$wdDefaultViewDataSet='uw';
$wdShowCopyPanel=true;

$wdGroupDefaultView=array();
# Here you can set group defaults.
$wdGroupDefaultView['wikidata-omega']='uw';
#$wdGroupDefaultView['wikidata-test']='tt';

# These are the user groups
$wgGroupPermissions['wikidata-omega']['editwikidata-uw']=true;
$wgGroupPermissions['wikidata-omega']['editwikidata-moo']=true;
$wgGroupPermissions['wikidata-omega']['editwikidata-tt']=false;
$wgGroupPermissions['wikidata-test']['editwikidata-tt']=true;
$wgGroupPermissions['wikidata-copy']['wikidata-copy']=true;

# The permission needed to do ...
$wgCommunityEditPermission="editwikidata-tt"; # only used for copy for now
$wgCommunity_dc="tt";

# The site prefix allows us to have multiple sets of customized
# messages (for different, typically site-specific UIs)
# in a single database.
$wdSiteContext="ow";

require_once("{$IP}/extensions/Wikidata/AddPrefs.php");
require_once("{$IP}/extensions/Wikidata/SpecialLanguages.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialSuggest.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialSelect.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialDatasearch.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialTransaction.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialNeedsTranslation.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialImportLangNames.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialAddCollection.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialConceptMapping.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialCopy.php");
require_once("{$IP}/extensions/Wikidata/OmegaWiki/SpecialExportTSV.php");

function addWikidataHeader() {
	global $wgOut,$wgScriptPath;
	$dc=wdGetDataSetContext();
	$wgOut->addScript("<script type='text/javascript' src='{$wgScriptPath}/extensions/Wikidata/OmegaWiki/suggest.js'></script>");
	$wgOut->addLink(array('rel'=>'stylesheet','type'=>'text/css','media'=>'screen, projection','href'=>"{$wgScriptPath}/extensions/Wikidata/OmegaWiki/suggest.css"));
	$wgOut->addLink(array('rel'=>'stylesheet','type'=>'text/css','media'=>'screen, projection','href'=>"{$wgScriptPath}/extensions/Wikidata/OmegaWiki/tables.css"));                                                                                                                                                                    
	return true;
}

function addWikidataEditLinkTrail(&$trail) {
	global $wgTitle;
	$ns=Namespace::get($wgTitle->getNamespace());
	if($ns->getHandlerClass()=='OmegaWiki' || $ns->getHandlerClass()=='DefinedMeaning') {
		$dc=wdGetDatasetContext();
		$trail="&dataset=$dc";
	}
	return true;
}

function addHistoryLinkTrail(&$trail) {
	global $wgTitle;
	$ns=Namespace::get($wgTitle->getNamespace());  
	if($ns->getHandlerClass()=='OmegaWiki' || $ns->getHandlerClass()=='DefinedMeaning') {  
    	$dc=wdGetDatasetContext();
    	$trail="&dataset=$dc";
  	}
	return true;
}

function initializeWikidata() {
	global 
		$wgMessageCache, $wgExtensionPreferences, $wdSiteContext, $wgPropertyToColumnFilters;
		
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("SET NAMES utf8");
	
	$msgarray = array(
		"save" => "Save",
		"history" => "History",
		"datasets" => "Data-set selection",
		"noedit" => "You are not permitted to edit pages in the dataset \"$1\". Please see [[Project:Permission policy|our editing policy]].",
		"noedit_title" => "No permission to edit",
		"uipref_datasets" => "Default view",
		"uiprefs" => "Wikidata",
		"none_selected" => "<None selected>",
		"conceptmapping_help" => "<p>possible actions: <ul>
			<li>&action=insert&<data_context_prefix>=<defined_id>&...  insert a mapping</li>
			<li>&action=get&concept=<concept_id>  read a mapping back</li>
			<li>&action=list_sets  return a list of possible data context prefixes and what they refer to.</li>
			<li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> for one defined meaning in a concept, return all others</li>
			<li>&action=help   Show helpful help.</li>
			</ul></p>",
		"conceptmapping_uitext" => "
				<p>Concept Mapping allows you to identify
				which defined meaning in one dataset is identical
				to defined meanings in other datasets.</p>\n",
		"conceptmapping_no_action_specified"=>"Apologies, I do not know how to \"$1\".",
		"dm_OK"=>"OK",
		"dm_not_present"=>"not entered",
		"dm_not_found"=>"not found in database or malformed",
		"mapping_successful"=>"Mapped all fields marked with [OK]<br>\n",
		"mapping_unsuccessful"=>"Need to have at least two defined meanings before I can link them.\n",
		"will_insert"=>"Will insert the following:",
		"contents_of_mapping"=>"Contents of mapping",
		"available_contexts"=>"Available contexts",
		"add_concept_link"=>"Add link to other concepts",
		"concept_panel"=>"Concept Panel",
		"dm_badtitle"=>"This page does not point to any DefinedMeaning (concept). Please check the web address.",
		"dm_missing"=>"This page seems to point to a non-existent DefinedMeaning (concept). Please check the web address.",
		"AlternativeDefinition" => "Alternative definition",
		"AlternativeDefinitions" => "Alternative definitions",	
		"Annotation" => "Annotation",
		"ApproximateMeanings" => "Approximate meanings",	
		"ClassAttributeAttribute" => "Attribute",
		"ClassAttributes" => "Class attributes",
		"ClassAttributeLevel" => "Level",
		"ClassAttributeType" => "Type",
		"ClassMembership" => "Class membership",
		"Collection" => "Collection",
		"CollectionMembership" => "Collection membership",
		"Definition" => "Definition",
		"DefinedMeaningAttributes" => "Annotation",
		"DefinedMeaning" => "Defined meaning",
		"DefinedMeaningReference" => "Defined meaning",
		"ExactMeanings" => "Exact meanings",
		"Expression" => "Expression",
                "ExpressionMeanings" => "Expression meanings",
		"Expressions" => "Expressions",
		"IdenticalMeaning" => "Identical meaning?",
		"IncomingRelations" => "Incoming relations",
		"GotoSource" => "Go to source",
		"Language" => "Language",
		"LevelAnnotation" => "Annotation",
		"OptionAttribute" => "Property",
		"OptionAttributeOption" => "Option",
		"OptionAttributeOptions" => "Options",
		"OptionAttributeValues" => "Option values",
		"OtherDefinedMeaning" => "Other defined meaning",
		"PopupAnnotation" => "Annotation",
		"Relations" => "Relations",
		"RelationType" => "Relation type",
		"Spelling" => "Spelling",
		"Synonyms" => "Synonyms", 
		"SynonymsAndTranslations" => "Synonyms and translations",
		"Source" => "Source",
		"SourceIdentifier" => "Source identifier",
		"TextAttribute" => "Property",
		"Text" => "Text",
		"TextAttributeValues" => "Plain texts",
		"TranslatedTextAttribute" => "Property",
		"TranslatedText" => "Translated text",
		"TranslatedTextAttributeValue" => "Text",
		"TranslatedTextAttributeValues" => "Translatable texts",
		"LinkAttribute" => "Property",
		"LinkAttributeValues" => "Links",
		"Property" => "Property",
		"Value" => "Value",
		"meaningsoftitle"=>"Meanings of \"$1\"",
		"meaningsofsubtitle"=>"<em>Wiki link:</em> [[$1]]",
		"Permission_denied"=>"<h2>PERMISSION DENIED</h2>",
		"copy_no_action_specified"=>"Please specify an action",
		"copy_help"=>"Someday, we might help you.",
		"please_proved_dmid"=>"Oh dear, it seems your input is missing a ?dmid=<something>   (dmid=Defined Meaning ID)<br>Whoops, please contact a server administrator.", 
		"please_proved_dc1"=>"Oh dear, it seems your input is missing a ?dc1=<something>   (dc1=dataset context 1, dataset to copy FROM)<br>Whoops, please contact a server administrator.", 
		"please_proved_dc2"=>"Oh dear, it seems your input is missing a ?dc2=<something>   (dc2=dataset context 2, dataset to copy TO)<br>Whoops, please contact a server administrator.", 
		"copy_successful"=>"<h2>Copy Successful</h2>Your data appears to have been copied successfully. Don't forget to doublecheck to make sure!",
		"db_consistency__not_found"=>"<h2>Error</h2>There is an issue with database consistency, wikidata can't find valid data connected to this defined meaning ID, it might be lost. Please contact the server operator or administrator."
	);

	$prefixedmsgarray=array();
	
	foreach($msgarray as $key=>$value) 
		$prefixedmsgarray[$wdSiteContext."_".$key]=$value;

	$wgMessageCache->addMessages($prefixedmsgarray);

	$datasets=wdGetDatasets();
	$datasetarray['']=wfMsgHtml('ow_none_selected');
	foreach($datasets as $datasetid=>$dataset) {
		$datasetarray[$datasetid]=$dataset->fetchName();
	}
	$wgExtensionPreferences[] = array(
		'name' => 'ow_uipref_datasets',
		'section' => 'ow_uiprefs',
		'type' => PREF_OPTIONS_T,
		'size' => 10,
		'options' => $datasetarray
	);
        $wgPropertyToColumnFilters = array(
                    new PropertyToColumnFilter("partOfSpeech", "Part of speech", array(358760))
	);
                            	
	global 
		$messageCacheOK;
		
	$messageCacheOK = true;

	return true;
}


