<?php
/**
 * Internationalisation file for Language Manager extension.
 *
 * @addtogroup Extensions
*/


$wgLanguageManagerMessages = array();

$wgLanguageManagerMessages['en'] = array(
	'langman_title' => 'Language manager',
	'languages'=>'Wikidata: Language manager',
	"ow_save" => "Save",
	"ow_history" => "History",
	"ow_datasets" => "Data-set selection",
	"ow_noedit" => "You are not permitted to edit pages in the dataset \"$1\". Please see [[Project:Permission policy|our editing policy]].",
	"ow_noedit_title" => "No permission to edit",
	"ow_uipref_datasets" => "Default view",
	"ow_uiprefs" => "Wikidata",
	"ow_none_selected" => "<None selected>",
	"ow_conceptmapping_help" => "<p>possible actions: <ul>
		<li>&action=insert&<data_context_prefix>=<defined_id>&...  insert a mapping</li>
		<li>&action=get&concept=<concept_id>  read a mapping back</li>
		<li>&action=list_sets  return a list of possible data context prefixes and what they refer to.</li>
		<li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> for one defined meaning in a concept, return all others</li>
		<li>&action=help   Show helpful help.</li>
		</ul></p>",
	"ow_conceptmapping_uitext" => "
			<p>Concept Mapping allows you to identify
			which defined meaning in one dataset is identical
			to defined meanings in other datasets.</p>\n",
	"ow_conceptmapping_no_action_specified"=>"Apologies, I do not know how to \"$1\".",
	"ow_dm_OK"=>"OK",
	"ow_dm_not_present"=>"not entered",
	"ow_dm_not_found"=>"not found in database or malformed",
	"ow_mapping_successful"=>"Mapped all fields marked with [OK]<br>\n",
	"ow_mapping_unsuccessful"=>"Need to have at least two defined meanings before I can link them.\n",
	"ow_will_insert"=>"Will insert the following:",
	"ow_contents_of_mapping"=>"Contents of mapping",
	"ow_available_contexts"=>"Available contexts",
	"ow_add_concept_link"=>"Add link to other concepts",
	"ow_concept_panel"=>"Concept Panel",
	"ow_dm_badtitle"=>"This page does not point to any DefinedMeaning (concept). Please check the web address.",
	"ow_dm_missing"=>"This page seems to point to a non-existent DefinedMeaning (concept). Please check the web address.",
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
	"ow_OptionAttributeValues" => "Option values",
	"ow_OtherDefinedMeaning" => "Other defined meaning",
	"ow_PopupAnnotation" => "Annotation",
	"ow_Relations" => "Relations",
	"ow_RelationType" => "Relation type",
	"ow_Spelling" => "Spelling",
	"ow_Synonyms" => "Synonyms", 
	"ow_SynonymsAndTranslations" => "Synonyms and translations",
	"ow_Source" => "Source",
	"ow_SourceIdentifier" => "Source identifier",
	"ow_TextAttribute" => "Property",
	"ow_Text" => "Text",
	"ow_TextAttributeValues" => "Plain texts",
	"ow_TranslatedTextAttribute" => "Property",
	"ow_TranslatedText" => "Translated text",
	"ow_TranslatedTextAttributeValue" => "Text",
	"ow_TranslatedTextAttributeValues" => "Translatable texts",
	"ow_LinkAttribute" => "Property",
	"ow_LinkAttributeValues" => "Links",
	"ow_Property" => "Property",
	"ow_Value" => "Value",
	"ow_meaningsoftitle"=>"Meanings of \"$1\"",
	"ow_meaningsofsubtitle"=>"<em>Wiki link:</em> [[$1]]",
	"ow_Permission_denied"=>"<h2>PERMISSION DENIED</h2>",
	"ow_copy_no_action_specified"=>"Please specify an action",
	"ow_copy_help"=>"Someday, we might help you.",
	"ow_please_proved_dmid"=>"Oh dear, it seems your input is missing a ?dmid=<something>   (dmid=Defined Meaning ID)<br>Whoops, please contact a server administrator.", 
	"ow_please_proved_dc1"=>"Oh dear, it seems your input is missing a ?dc1=<something>   (dc1=dataset context 1, dataset to copy FROM)<br>Whoops, please contact a server administrator.", 
	"ow_please_proved_dc2"=>"Oh dear, it seems your input is missing a ?dc2=<something>   (dc2=dataset context 2, dataset to copy TO)<br>Whoops, please contact a server administrator.", 
	"ow_copy_successful"=>"<h2>Copy Successful</h2>Your data appears to have been copied successfully. Don't forget to doublecheck to make sure!",
	"ow_copy_unsuccessful"=>"<h3>Copy unsuccessful</h3> No copy operation has taken place.",
	"ow_no_action_specified"=>"<h3>No action was specified</h3> Perhaps you came to this page directly? Normally you don't need to be here.",
	"ow_db_consistency__not_found"=>"<h2>Error</h2>There is an issue with database consistency, wikidata can't find valid data connected to this defined meaning ID, it might be lost. Please contact the server operator or administrator."
);

$wgLanguageManagerMessages['ar'] = array(
	'langman_title' => 'مدير اللغة',
	'languages' => 'Wikidata: مدير اللغة',
);

$wgLanguageManagerMessages['bcl'] = array(
	'languages' => 'Wikidata: Manager kan tataramon',
);

$wgLanguageManagerMessages['bn'] = array(
	'langman_title' => 'ভাষা ব্যবস্থাপক',
	'languages' => 'Wikidata: ভাষা ব্যবস্থাপক',
);

$wgLanguageManagerMessages['br'] = array(
	'langman_title' => 'Merer yezhoù',
	'languages' => 'Wikidata: Merer yezhoù',
);

$wgLanguageManagerMessages['de'] = array(
	'langman_title' => 'Sprachmanager',
);

$wgLanguageManagerMessages['el'] = array(
	'langman_title' => 'Διαχειριστής γλώσσας',
	'languages' => 'Wikidata: Διαχειριστής γλώσσας',
);

$wgLanguageManagerMessages['fr'] = array(
	'langman_title' => 'Gestion des langues',
	'languages' => 'Wikidata: Gestion des langues',
);

$wgLanguageManagerMessages['gl'] = array(
	'langman_title' => 'Xestor de linguas',
	'languages' => 'Wikidata: Xestor de linguas',
);

$wgLanguageManagerMessages['he'] = array(
	'langman_title' => 'מנהל שפות',
);

$wgLanguageManagerMessages['hsb'] = array(
	'langman_title' => 'Zrjadowak rěčow',
	'languages' => 'Wikidata: Zrjadowak rěčow',
);

$wgLanguageManagerMessages['id'] = array(
	'langman_title' => 'Pengelola bahasa',
	'languages'=>'Wikidata: Pengelola bahasa',
);

$wgLanguageManagerMessages['kk-kz'] = array(
	'langman_title' => 'Тілдерді меңгеру',
);

$wgLanguageManagerMessages['kk-tr'] = array(
	'langman_title' => 'Tilderdi meñgerw',
);

$wgLanguageManagerMessages['kk-cn'] = array(
	'langman_title' => 'تٴىلدەردٴى مەڭگەرۋ',
);

$wgLanguageManagerMessages['kk'] = $wgLanguageManagerMessages['kk-kz'];

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$wgLanguageManagerMessages['lb'] = array(
	'langman_title' => 'Sproochmanager',
	'languages'     => 'Wikidata: Sproochmanager',
);

$wgLanguageManagerMessages['nl'] = array(
	'langman_title' => 'Taalmanager',
	'languages'=>'Wikidata: Taalmanager',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$wgLanguageManagerMessages['no'] = array(
	'langman_title' => 'Språkbehandler',
	'languages'     => 'Wikidata: Språkbehandler',

);

$wgLanguageManagerMessages['oc'] = array(
	'langman_title' => 'Gestion de las lengas',
	'languages' => 'Wikidata: Gestion de las lengas',
);

$wgLanguageManagerMessages['pms'] = array(
	'langman_title' => 'Gestor dle lenghe',
	'languages' => 'Wikidata: Gestor dle lenghe',
);

$wgLanguageManagerMessages['pt'] = array(
	'langman_title' => 'Gestor de línguas',
	'languages' => 'Wikidata: Gestor de línguas',
);

$wgLanguageManagerMessages['sk'] = array(
	'langman_title' => 'Správca jazykov',
	'languages' => 'Wikidata: Správca jazykov',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$wgLanguageManagerMessages['stq'] = array(
	'langman_title' => 'Sproakmanager',
	'languages'     => 'Wikidata: Sproakmanager',
);

$wgLanguageManagerMessages['sv'] = array(
	'langman_title' => 'Språkhanterare',
	'languages' => 'Wikidata: Språkhanterare',
);

/** Turkish (Türkçe)
 * @author Karduelis
 */
$wgLanguageManagerMessages['tr'] = array(
	'langman_title' => 'Lisan idarecisi',
	'languages'     => 'Wikidata: Lisan idarecisi',
);

$wgLanguageManagerMessages['zh-hans'] = array(
	'langman_title' => '语言管理员',
	'languages'=>'Wikidata: 语言管理员',
);

$wgLanguageManagerMessages['zh-hant'] = array(
	'langman_title' => '語言管理員',
	'languages'=>'Wikidata: 語言管理員',
);

$wgLanguageManagerMessages['yue'] = $wgLanguageManagerMessages['zh-hant'];
$wgLanguageManagerMessages['zh'] = $wgLanguageManagerMessages['zh-hans'];
$wgLanguageManagerMessages['zh-cn'] = $wgLanguageManagerMessages['zh-hans'];
$wgLanguageManagerMessages['zh-hk'] = $wgLanguageManagerMessages['zh-hant'];
$wgLanguageManagerMessages['zh-sg'] = $wgLanguageManagerMessages['zh-hans'];
$wgLanguageManagerMessages['zh-tw'] = $wgLanguageManagerMessages['zh-hant'];
$wgLanguageManagerMessages['zh-yue'] = $wgLanguageManagerMessages['yue'];

