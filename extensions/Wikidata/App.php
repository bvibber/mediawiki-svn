<?php

global 
	$wdSiteContext;
$wdSiteContext="ow";

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
		$wgMessageCache, $wgExtensionPreferences;
		
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("SET NAMES utf8");
	
	$wgMessageCache->addMessages(
		array(
			'ow_uilang'=>'Your user interface language: $1',
			'ow_uilang_set'=>'Set your preferences',
			'ow_save' => 'Save',
			'ow_history' => 'History',
			'ow_datasets' => 'Data-set selection',
			'ow_noedit' => 'You are not permitted to edit pages in the dataset "$1". Please see [[Project:Permission policy|our editing policy]].',
			'ow_noedit_title' => 'No permission to edit',
			'ow_uipref_datasets' => 'Default view',
			'ow_uiprefs' => 'Wikidata',
			'ow_none_selected' => '<None selected>',
			'ow_conceptmapping_help' => "<p>possible actions: <ul>
				<li>&action=insert&<data_context_prefix>=<defined_id>&...  insert a mapping</li>
				<li>&action=get&concept=<concept_id>  read a mapping back</li>
				<li>&action=list_sets  return a list of possible data context prefixes and what they refer to.</li>
				<li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> for one defined meaning in a concept, return all others</li>
				<li>&action=help   Show helpful help.</li>
				</ul></p>",
			'ow_conceptmapping_uitext' => "
					<p>Concept Mapping allows you to identify
					which defined meaning in one dataset is identical
					to defined meanings in other datasets.</p>\n",
			'ow_conceptmapping_no_action_specified'=>"Apologies, I don't know how to '$1'.",
			'ow_dm_OK'=>'OK',
			'ow_dm_not_present'=>'not entered',
			'ow_dm_not_found'=>'not found in database or malformed',
			'ow_mapping_successful'=>"Mapped all fields marked with [OK]<br>\n",
			'ow_mapping_unsuccessful'=>"Need to have at least two defined meanings before I can link them.\n",
			'ow_will_insert'=>"Will insert the following:",
			'ow_contents_of_mapping'=>'Contents of mapping',
			'ow_available_contexts'=>'Available contexts',
			'add_concept_link'=>'Add link to other concepts',
			'ow_concept_panel'=>'Concept Panel',
			'ow_dm_badtitle'=>'This page does not point to any DefinedMeaning (concept). Please check the web address.',
			'ow_dm_missing'=>'This page seems to point to a non-existent DefinedMeaning (concept). Please check the web address.',
		"ow_AlternativeDefinition" => "Alternative definition",
		"ow_AlternativeDefinitions" => "Alternative definitions",	
		"ow_Annotation" => "Annotation",
		"ow_ApproximateMeanings" => "Approximate meanings",	
		"ow_ClassAttributeAttribute" => "Attribute",
		"ow_ClassAttributes" => "Class attributes",
		"ow_ClassAttributeLevel" => "Level",
		"ow_ClassAttributeType" => "Type",
		"ow_ClassMembership" => "Class membership",
		"ow_Collection" => "Collection",
		"ow_CollectionMembership" => "Collection membership",
		"ow_Definition" => "Definition",
		"ow_DefinedMeaningAttributes" => "Annotation",
		"ow_DefinedMeaning" => "Defined meaning",
		"ow_DefinedMeaningReference" => "Defined meaning",
		"ow_ExactMeanings" => "Exact meanings",
		"ow_Expression" => "Expression",
                "ow_ExpressionMeanings" => "Expression meanings",
		"ow_Expressions" => "Expressions",
		"ow_IdenticalMeaning" => "Identical meaning?",
		"ow_IncomingRelations" => "Incoming relations",
		"ow_GotoSource" => "Go to source",
		"ow_Language" => "Language",
		"ow_LevelAnnotation" => "Annotation",
		"ow_OptionAttribute" => "Property",
		"ow_OptionAttributeOption" => "Option",
		"ow_OptionAttributeOptions" => "Options",
		"ow_OptionAttributeValues" => "Option properties",
		"ow_OtherDefinedMeaning" => "Other defined meaning",
		"ow_PopupAnnotation" => "Annotation",
		"ow_PossibleSynonym" => "Possible synonym",
		"ow_PossiblySynonymous" => "Possibly synonymous",
		"ow_Relations" => "Relations",
		"ow_RelationType" => "Relation type",
		"ow_Spelling" => "Spelling",
		"ow_Synonyms" => "Synonyms", 
		"ow_SynonymsAndTranslations" => "Synonyms and translations",
		"ow_Source" => "Source",
		"ow_SourceIdentifier" => "Source identifier",
		"ow_TextAttribute" => "Property",
		"ow_Text" => "Text",
		"ow_TextAttributeValues" => "String properties",
		"ow_TranslatedTextAttribute" => "Property",
		"ow_TranslatedText" => "Translated text",
		"ow_TranslatedTextAttributeValue" => "Text",
		"ow_TranslatedTextAttributeValues" => "Text properties",
		"ow_LinkAttribute" => "Property",
		"ow_LinkAttributeValues" => "Links"

		)
	);


	$datasets=wdGetDatasets();
	$datasetarray['']=wfMsgHtml('ow_none_selected');
	foreach($datasets as $datasetid=>$dataset) {
		$datasetarray[$datasetid]=$dataset->fetchName();
	}
	$wgExtensionPreferences[]=array(
	'name'=>'ow_uipref_datasets',
	'section'=>'ow_uiprefs',
	'type'=>PREF_OPTIONS_T,
	'size'=>10,
	'options'=>$datasetarray
	);
	
	global 
		$messageCacheOK;
	$messageCacheOK=True;

	return true;
}


