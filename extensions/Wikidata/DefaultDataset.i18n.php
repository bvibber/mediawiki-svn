<?php
/**
 * Internationalisation file for Language Manager extension.
 *
 * @addtogroup Extensions
*/

$wgDefaultDatasetMessages = array();

/** English
 */
$wgDefaultDatasetMessages['en'] = array(
	'save'                               => 'Save',
	'history'                            => 'History',
	'datasets'                           => 'Data-set selection',
	'noedit'                             => 'You are not permitted to edit pages in the dataset "$1". Please see [[Project:Permission policy|our editing policy]].',
	'noedit_title'                       => 'No permission to edit',
	'uipref_datasets'                    => 'Default view',
	'uiprefs'                            => 'Wikidata',
	'none_selected'                      => '<None selected>',
	'conceptmapping_help'                => '<p>possible actions: <ul>
<li>&action=insert&<data_context_prefix>=<defined_id>&...  insert a mapping</li>
<li>&action=get&concept=<concept_id>  read a mapping back</li>
<li>&action=list_sets  return a list of possible data context prefixes and what they refer to.</li>
<li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> for one defined meaning in a concept, return all others</li>
<li>&action=help   Show helpful help.</li>
</ul></p>',
	'conceptmapping_uitext'              => '<p>Concept Mapping allows you to identify which defined meaning in one dataset is identical to defined meanings in other datasets.</p>',
	'conceptmapping_no_action_specified' => 'Apologies, I do not know how to "$1".',
	'dm_OK'                              => 'OK',
	'dm_not_present'                     => 'not entered',
	'dm_not_found'                       => 'not found in database or malformed',
	'mapping_successful'                 => 'Mapped all fields marked with [OK]<br />',
	'mapping_unsuccessful'               => 'Need to have at least two defined meanings before I can link them.',
	'will_insert'                        => 'Will insert the following:',
	'contents_of_mapping'                => 'Contents of mapping',
	'available_contexts'                 => 'Available contexts',
	'add_concept_link'                   => 'Add link to other concepts',
	'concept_panel'                      => 'Concept Panel',
	'dm_badtitle'                        => 'This page does not point to any DefinedMeaning (concept). Please check the web address.',
	'dm_missing'                         => 'This page seems to point to a non-existent DefinedMeaning (concept). Please check the web address.',
	'AlternativeDefinition'              => 'Alternative definition',
	'AlternativeDefinitions'             => 'Alternative definitions',
	'Annotation'                         => 'Annotation',
	'ApproximateMeanings'                => 'Approximate meanings',
	'ClassAttributeAttribute'            => 'Attribute',
	'ClassAttributes'                    => 'Class attributes',
	'ClassAttributeLevel'                => 'Level',
	'ClassAttributeType'                 => 'Type',
	'ClassMembership'                    => 'Class membership',
	'Collection'                         => 'Collection',
	'CollectionMembership'               => 'Collection membership',
	'Definition'                         => 'Definition',
	'DefinedMeaningAttributes'           => 'Annotation',
	'DefinedMeaning'                     => 'Defined meaning',
	'DefinedMeaningReference'            => 'Defined meaning',
	'ExactMeanings'                      => 'Exact meanings',
	'Expression'                         => 'Expression',
	'ExpressionMeanings'                 => 'Expression meanings',
	'Expressions'                        => 'Expressions',
	'IdenticalMeaning'                   => 'Identical meaning?',
	'IncomingRelations'                  => 'Incoming relations',
	'GotoSource'                         => 'Go to source',
	'Language'                           => 'Language',
	'LevelAnnotation'                    => 'Annotation',
	'OptionAttribute'                    => 'Property',
	'OptionAttributeOption'              => 'Option',
	'OptionAttributeOptions'             => 'Options',
	'OptionAttributeValues'              => 'Option values',
	'OtherDefinedMeaning'                => 'Other defined meaning',
	'PopupAnnotation'                    => 'Annotation',
	'Relations'                          => 'Relations',
	'RelationType'                       => 'Relation type',
	'Spelling'                           => 'Spelling',
	'Synonyms'                           => 'Synonyms',
	'SynonymsAndTranslations'            => 'Synonyms and translations',
	'Source'                             => 'Source',
	'SourceIdentifier'                   => 'Source identifier',
	'TextAttribute'                      => 'Property',
	'Text'                               => 'Text',
	'TextAttributeValues'                => 'Plain texts',
	'TranslatedTextAttribute'            => 'Property',
	'TranslatedText'                     => 'Translated text',
	'TranslatedTextAttributeValue'       => 'Text',
	'TranslatedTextAttributeValues'      => 'Translatable texts',
	'LinkAttribute'                      => 'Property',
	'LinkAttributeValues'                => 'Links',
	'Property'                           => 'Property',
	'Value'                              => 'Value',
	'meaningsoftitle'                    => 'Meanings of "$1"',
	'meaningsofsubtitle'                 => '<em>Wiki link:</em> [[$1]]',
	'Permission_denied'                  => '<h2>PERMISSION DENIED</h2>',
	'copy_no_action_specified'           => 'Please specify an action',
	'copy_help'                          => 'Someday, we might help you.',
	'please_proved_dmid'                 => 'Oh dear, it seems your input is missing a ?dmid=<something>   (dmid=Defined Meaning ID)<br />Whoops, please contact a server administrator.',
	'please_proved_dc1'                  => 'Oh dear, it seems your input is missing a ?dc1=<something>   (dc1=dataset context 1, dataset to copy FROM)<br />Whoops, please contact a server administrator.',
	'please_proved_dc2'                  => 'Oh dear, it seems your input is missing a ?dc2=<something>   (dc2=dataset context 2, dataset to copy TO)<br />Whoops, please contact a server administrator.',
	'copy_successful'                    => "<h2>Copy Successful</h2>Your data appears to have been copied successfully. Don't forget to doublecheck to make sure!",
	'copy_unsuccessful'                  => '<h3>Copy unsuccessful</h3> No copy operation has taken place.',
	'no_action_specified'                => "<h3>No action was specified</h3> Perhaps you came to this page directly? Normally you don't need to be here.",
	'db_consistency__not_found'          => "<h2>Error</h2>There is an issue with database consistency, wikidata can't find valid data connected to this defined meaning ID, it might be lost. Please contact the server operator or administrator.",
);

/** For future compatibility : merges defaultDatasetMessags into wgLanguageManagerMessages with site dataset context substitutions, in place 
 * @param $wgDefaultDatasetMessages  ref to the messages from DefaultDataset.i18n.php
 * @param $wgLanguageManagerMessages ref tp Normal i18n messages
 * @param $wdSiteContext 	current site context.
 */
function defaultDataset2wgLanguageManager(&$wgDefaultDatasetMessages, &$wgLanguageManagerMessages,$wdSiteContext) {
	
	foreach ($wgDefaultDatasetMessages as $language => $msgarray) {
		$prefixedmsgarray=array();
		
		foreach($msgarray as $key=>$value) 
			$prefixedmsgarray[$wdSiteContext."_".$key]=$value;
		
		$wgLanguageManagerMessages[$language]=$prefixedmsgarray;
	}
}
