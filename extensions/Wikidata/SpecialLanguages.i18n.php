<?php
/**
 * Internationalisation file for Language Manager extension.
 *
 * @addtogroup Extensions
 */

$wdMessages = array();

/** English
 */
$wdMessages['en'] = array(
	'datasearch'                            => 'Wikidata: Data search',
	'langman_title'                         => 'Language manager',
	'languages'                             => 'Wikidata: Language manager',
	'ow_save'                               => 'Save',
	'ow_history'                            => 'History',
	'ow_datasets'                           => 'Data-set selection',
	'ow_noedit_title'                       => 'No permission to edit',
	'ow_noedit'                             => 'You are not permitted to edit pages in the dataset "$1". Please see [[{{MediaWiki:Ow editing policy url}}|our editing policy]].',
	'ow_editing_policy_url'                 => 'Project:Permission policy',
	'ow_uipref_datasets'                    => 'Default view',
	'ow_uiprefs'                            => 'Wikidata',
	'ow_none_selected'                      => '<None selected>',
	'ow_conceptmapping_help'                => '<p>possible actions: <ul>
<li>&action=insert&<data_context_prefix>=<defined_id>&...  insert a mapping</li>
<li>&action=get&concept=<concept_id>  read a mapping back</li>
<li>&action=list_sets  return a list of possible data context prefixes and what they refer to.</li>
<li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> for one defined meaning in a concept, return all others</li>
<li>&action=help   Show helpful help.</li>
</ul></p>',
	'ow_conceptmapping_uitext'              => '<p>Concept Mapping allows you to identify which defined meaning in one dataset is identical	to defined meanings in other datasets.</p>',
	'ow_conceptmapping_no_action_specified' => 'Apologies, I do not know how to "$1".',
	'ow_dm_OK'                              => 'OK',
	'ow_dm_not_present'                     => 'not entered',
	'ow_dm_not_found'                       => 'not found in database or malformed',
	'ow_mapping_successful'                 => 'Mapped all fields marked with [OK]<br />',
	'ow_mapping_unsuccessful'               => 'Need to have at least two defined meanings before I can link them.',
	'ow_will_insert'                        => 'Will insert the following:',
	'ow_contents_of_mapping'                => 'Contents of mapping',
	'ow_available_contexts'                 => 'Available contexts',
	'ow_add_concept_link'                   => 'Add link to other concepts',
	'ow_concept_panel'                      => 'Concept Panel',
	'ow_dm_badtitle'                        => 'This page does not point to any DefinedMeaning (concept). Please check the web address.',
	'ow_dm_missing'                         => 'This page seems to point to a non-existent DefinedMeaning (concept). Please check the web address.',
	'ow_AlternativeDefinition'              => 'Alternative definition',
	'ow_AlternativeDefinitions'             => 'Alternative definitions',
	'ow_Annotation'                         => 'Annotation',
	'ow_ApproximateMeanings'                => 'Approximate meanings',
	'ow_ClassAttributeAttribute'            => 'Attribute',
	'ow_ClassAttributes'                    => 'Class attributes',
	'ow_ClassAttributeLevel'                => 'Level',
	'ow_ClassAttributeType'                 => 'Type',
	'ow_ClassMembership'                    => 'Class membership',
	'ow_Collection'                         => 'Collection',
	'ow_CollectionMembership'               => 'Collection membership',
	'ow_Definition'                         => 'Definition',
	'ow_DefinedMeaningAttributes'           => 'Annotation',
	'ow_DefinedMeaning'                     => 'Defined meaning',
	'ow_DefinedMeaningReference'            => 'Defined meaning',
	'ow_ExactMeanings'                      => 'Exact meanings',
	'ow_Expression'                         => 'Expression',
	'ow_ExpressionMeanings'                 => 'Expression meanings',
	'ow_Expressions'                        => 'Expressions',
	'ow_IdenticalMeaning'                   => 'Identical meaning?',
	'ow_IncomingRelations'                  => 'Incoming relations',
	'ow_GotoSource'                         => 'Go to source',
	'ow_Language'                           => 'Language',
	'ow_LevelAnnotation'                    => 'Annotation',
	'ow_OptionAttribute'                    => 'Property',
	'ow_OptionAttributeOption'              => 'Option',
	'ow_OptionAttributeOptions'             => 'Options',
	'ow_OptionAttributeValues'              => 'Option values',
	'ow_OtherDefinedMeaning'                => 'Other defined meaning',
	'ow_PopupAnnotation'                    => 'Annotation',
	'ow_Relations'                          => 'Relations',
	'ow_RelationType'                       => 'Relation type',
	'ow_Spelling'                           => 'Spelling',
	'ow_Synonyms'                           => 'Synonyms',
	'ow_SynonymsAndTranslations'            => 'Synonyms and translations',
	'ow_Source'                             => 'Source',
	'ow_SourceIdentifier'                   => 'Source identifier',
	'ow_TextAttribute'                      => 'Property',
	'ow_Text'                               => 'Text',
	'ow_TextAttributeValues'                => 'Plain texts',
	'ow_TranslatedTextAttribute'            => 'Property',
	'ow_TranslatedText'                     => 'Translated text',
	'ow_TranslatedTextAttributeValue'       => 'Text',
	'ow_TranslatedTextAttributeValues'      => 'Translatable texts',
	'ow_LinkAttribute'                      => 'Property',
	'ow_LinkAttributeValues'                => 'Links',
	'ow_Property'                           => 'Property',
	'ow_Value'                              => 'Value',
	'ow_meaningsoftitle'                    => 'Meanings of "$1"',
	'ow_meaningsofsubtitle'                 => '<em>Wiki link:</em> [[$1]]',
	'ow_Permission_denied'                  => '<h2>PERMISSION DENIED</h2>',
	'ow_copy_no_action_specified'           => 'Please specify an action',
	'ow_copy_help'                          => 'Someday, we might help you.',
	'ow_please_proved_dmid'                 => 'It seems your input is missing a "?dmid=<ID>" (dmid=Defined Meaning ID)<br />Please contact a server administrator.',
	'ow_please_proved_dc1'                  => 'It seems your input is missing a "?dc1=<something>" (dc1=dataset context 1, dataset to copy FROM)<br />Please contact a server administrator.',
	'ow_please_proved_dc2'                  => 'It seems your input is missing a "?dc2=<something>" (dc2=dataset context 2, dataset to copy TO)<br />Please contact a server administrator.',
	'ow_copy_successful'                    => "<h2>Copy Successful</h2>Your data appears to have been copied successfully. Do not forget to doublecheck to make sure!",
	'ow_copy_unsuccessful'                  => '<h3>Copy unsuccessful</h3> No copy operation has taken place.',
	'ow_no_action_specified'                => "<h3>No action was specified</h3> Perhaps you came to this page directly? Normally you do not need to be here.",
	'ow_db_consistency_not_found'          => "<h2>Error</h2>There is an issue with database consistency, wikidata cannot find valid data connected to this defined meaning ID. It might be lost. Please contact the server operator or administrator.",
);

/** Aragonese (Aragonés)
 * @author Juanpabl
 */
$wdMessages['an'] = array(
	'datasearch' => 'Wikidata: Mirar datos',
);

$wdMessages['ar'] = array(
	'datasearch' => 'Wikidata: بحث البيانات',
	'langman_title' => 'مدير اللغة',
	'languages' => 'Wikidata: مدير اللغة',
);

/** Kotava (Kotava)
 * @author Wikimistusik
 */
$wdMessages['avk'] = array(
	'datasearch' => 'Wikidata : Origaneyara',
);

$wdMessages['bcl'] = array(
	'datasearch' => 'Wikidata: Data search',#identical but defined
	'languages' => 'Wikidata: Manager kan tataramon',
);

/** Bulgarian (Български)
 * @author DCLXVI
 */
$wdMessages['bg'] = array(
	'datasearch' => 'Уикиданни: Търсене на данни',
	'ow_save'                         => 'Съхранение',
	'ow_history'                      => 'История',
	'ow_noedit_title'                 => 'Необходими са права за редактиране',
	'ow_dm_OK'                        => 'Добре',
	'ow_will_insert'                  => 'Ще бъде вмъкнато следното:',
	'ow_AlternativeDefinition'        => 'Алтернативно определение',
	'ow_AlternativeDefinitions'       => 'Алтернативни определения',
	'ow_Annotation'                   => 'Анотация',
	'ow_ClassAttributeAttribute'      => 'Атрибут',
	'ow_ClassAttributeLevel'          => 'Ниво',
	'ow_ClassAttributeType'           => 'Вид',
	'ow_Definition'                   => 'Определение',
	'ow_ExactMeanings'                => 'Точни значения',
	'ow_PopupAnnotation'              => 'Анотация',
	'ow_Synonyms'                     => 'Синоними',
	'ow_SynonymsAndTranslations'      => 'Синоними и преводи',
	'ow_Source'                       => 'Източник',
	'ow_Text'                         => 'Текст',
	'ow_TranslatedText'               => 'Преведен текст',
	'ow_TranslatedTextAttributeValue' => 'Текст',
	'ow_LinkAttributeValues'          => 'Препратки',
	'ow_Value'                        => 'Стойност',
	'ow_meaningsofsubtitle'           => '<em>Уики-препратка:</em> [[$1]]',
	'ow_copy_no_action_specified'     => 'Необходимо е да се посочи действие',
	'ow_copy_successful'              => '<h2>Копирането беше успешно</h2>Данните изглежда са копирани успешно. Уверите, че това наистина е така!',
	'ow_no_action_specified'          => '<h3>Не е посочено действие</h3> Вероятно сте попаднали тук директно? Обикновено не се налага да идвате тук.',
);

$wdMessages['bn'] = array(
	'datasearch' => 'Wikidata: তথ্য অনুসন্ধান',
	'langman_title' => 'ভাষা ব্যবস্থাপক',
	'languages' => 'Wikidata: ভাষা ব্যবস্থাপক',
);

/** Brezhoneg (Brezhoneg)
 */
$wdMessages['br'] = array(
	'datasearch' => 'Wikidata: Klask roadennoù',
	'langman_title'                         => 'Merer yezhoù',
	'languages'                             => 'Wikidata: Merer yezhoù',
	'ow_save'                               => 'Enrollañ',
	'ow_history'                            => 'Istor',
	'ow_datasets'                           => 'Dibab an diaz',
	'ow_noedit'                             => 'N\'oc\'h ket aotreet da zegas kemmoù war pajennoù an diaz "$1". Sellit ouzh [[{{MediaWiki:Ow editing policy url}}|ar reolennoù kemmañ]].',
	'ow_noedit_title'                       => "N'oc'h ket aotreet da zegas kemmoù",
	'ow_uipref_datasets'                    => 'Gwel dre ziouer',
	'ow_none_selected'                      => '<Netra diuzet>',
	'ow_conceptmapping_help'                => "<p>oberoù posupl : <ul> <li>&action=insert&<data_context_prefix>=<defined_id>&... ensoc'hañ ul liamm</li> <li>&action=get&concept=<concept_id> adkavout ul liamm</li> <li>&action=list_sets degas ur rollad rakgerioù eus kendestennoù roadennoù posupl, hag ar pezh a reont dave dezhañ.</li> <li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> evit ur ster termenet en ur gendestenn, degas an holl re all</li> <li>&action=help Diskouez ar skoazell.</li> </ul></p>",
	'ow_conceptmapping_uitext'              => "<p>Dre liammañ ar meizadoù e c'haller lakaat war wel sterioù termenet ur strobad roadennoù heñvel ouzh sterioù termenet strobadoù roadennoù all.</p>",
	'ow_conceptmapping_no_action_specified' => 'Fazi, dibosupl ober "$1"',
	'ow_dm_OK'                              => 'Mat eo',
	'ow_dm_not_present'                     => "n'emañ ket e-barzh",
	'ow_dm_not_found'                       => "n'eo ket bet kavet en diaz titouroù, pe stummet fall eo",
	'ow_mapping_successful'                 => 'Liammet eo bet an holl vaeziennoù merket gant [Mat eo]<br>',
	'ow_mapping_unsuccessful'               => 'Ret eo kaout da nebeutañ daou ster termenet a-raok gellout liammañ anezho.',
	'ow_will_insert'                        => 'a verko an destenn-mañ :',
	'ow_contents_of_mapping'                => 'Hollad al liammoù',
	'ow_available_contexts'                 => 'Kendestennoù hegerzh',
	'ow_add_concept_link'                   => 'Ouzhpennañ liammoù war-du meizadoù all',
	'ow_concept_panel'                      => 'Merañ ar Meizadoù',
	'ow_dm_badtitle'                        => "Ne gas ket ar bajenn-mañ da SterTermenet ebet (meizad). Gwiriit chomlec'h an URL mar plij.",
	'ow_dm_missing'                         => 'Evit doare ne gas ar bajenn-mañ da SterTermenet ebet (meizad). Gwiriit an URL mar plij.',
	'ow_AlternativeDefinition'              => 'Termenadur all',
	'ow_AlternativeDefinitions'             => 'Termenadurioù all',
	'ow_Annotation'                         => 'Notennadur',
	'ow_ApproximateMeanings'                => 'Sterioù damheñvel',
	'ow_ClassAttributeAttribute'            => 'Perzh',
	'ow_ClassAttributes'                    => 'Perzhioù ar rummad',
	'ow_ClassAttributeLevel'                => 'Live',
	'ow_ClassAttributeType'                 => 'Seurt',
	'ow_ClassMembership'                    => 'Rummadoù',
	'ow_Collection'                         => 'Dastumad',
	'ow_CollectionMembership'               => 'Dastumadoù',
	'ow_Definition'                         => 'Termenadur',
	'ow_DefinedMeaningAttributes'           => 'Notennadur',
	'ow_DefinedMeaning'                     => 'Ster termenet',
	'ow_DefinedMeaningReference'            => 'Ster termenet',
	'ow_ExactMeanings'                      => 'Talvoudegezh rik',
	'ow_Expression'                         => 'Termen',
	'ow_ExpressionMeanings'                 => 'Sterioù an termen',
	'ow_Expressions'                        => 'Termenoù',
	'ow_IdenticalMeaning'                   => 'Termen kevatal-rik ?',
	'ow_IncomingRelations'                  => 'Darempredoù o tont tre',
	'ow_GotoSource'                         => "Mont d'ar vammenn",
	'ow_Language'                           => 'Yezh',
	'ow_LevelAnnotation'                    => 'Notennadur',
	'ow_OptionAttribute'                    => 'Perzh',
	'ow_OptionAttributeOption'              => 'Dibarzh',
	'ow_OptionAttributeOptions'             => 'Dibaboù',
	'ow_OptionAttributeValues'              => 'Talvoudegezh an dibarzhioù',
	'ow_OtherDefinedMeaning'                => 'Ster termenet all',
	'ow_PopupAnnotation'                    => 'Notennadur',
	'ow_Relations'                          => 'Darempredoù',
	'ow_RelationType'                       => 'Seurt darempred',
	'ow_Spelling'                           => 'Reizhskrivañ',
	'ow_Synonyms'                           => 'Heñvelsterioù',
	'ow_SynonymsAndTranslations'            => 'Heñvelsterioù ha troidigezhioù',
	'ow_Source'                             => 'Mammenn',
	'ow_SourceIdentifier'                   => 'Daveer ar vammenn',
	'ow_TextAttribute'                      => 'Perzh',
	'ow_Text'                               => 'Testenn',
	'ow_TextAttributeValues'                => 'Skrid plaen',
	'ow_TranslatedTextAttribute'            => 'Perzh',
	'ow_TranslatedText'                     => 'Testenn troet',
	'ow_TranslatedTextAttributeValue'       => 'Testenn',
	'ow_TranslatedTextAttributeValues'      => 'Testennoù da dreiñ',
	'ow_LinkAttribute'                      => 'Perzh',
	'ow_LinkAttributeValues'                => 'Liammoù',
	'ow_Property'                           => 'Perzh',
	'ow_Value'                              => 'Talvoudenn',
	'ow_meaningsoftitle'                    => 'Sterioù "$1"',
	'ow_meaningsofsubtitle'                 => '<em>Liamm Wiki :</em> [[$1]]',
	'ow_Permission_denied'                  => "<h2>AOTRE NAC'HET</h2>",
	'ow_copy_no_action_specified'           => 'Spisait un ober mar plij',
	'ow_copy_help'                          => "Un deiz bennak e vimp gouest d'o skoazellañ...",
	'ow_please_proved_dmid'                 => 'Mankout a ra un ?dmid=<...> (dmid=SterTermenet ID)<br>Kit e darempred gant merour ar servijer.',
	'ow_please_proved_dc1'                  => 'Mankout a ra un ?dc1=<...> (dc1=kendestenn an diaz 1, diaz a eiler ADAL dezhañ)<br>Kit e darempred gant merour ar servijer.',
	'ow_please_proved_dc2'                  => 'Mankout a ra un ?dc2=<...> (dc1=kendestenn an diaz 2, diaz a eiler ADAL dezhañ)<br>Kit e darempred gant merour ar servijer.',
	'ow_copy_successful'                    => '<h2>Eilskrid aet da benn vat</h2>Evit doare eo bet eilet mat ho roadennoù (gwiriit memes tra).',
);

/** Deutsch (Deutsch)
 */
$wdMessages['de'] = array(
	'langman_title'                         => 'Sprachmanager',
	'ow_save'                               => 'Speichern',
	'ow_history'                            => 'Versionen/Autoren',
	'ow_datasets'                           => 'Auswahl des Datasets',
	'ow_noedit'                             => 'Du hast nicht die Erlaubnis Seiten im Dataset "$1" zu editieren. Siehe [[{{MediaWiki:Ow editing policy url}}|unsere Richtlinien]].',
	'ow_noedit_title'                       => 'Keine Editiererlaubnis',
	'ow_uipref_datasets'                    => 'Standardansicht',
	'ow_uiprefs'                            => 'Wikidata',
	'ow_none_selected'                      => '<nichts ausgewählt>',
	'ow_conceptmapping_help'                => '<p>Mögliche Aktionen: <ul> <li>&action=insert&<data_context_prefix>=<defined_id>&... Eine Verknüpfung hinzufügen</li> <li>&action=get&concept=<concept_id> Eine Verknüpfung abrufen</li> <li>&action=list_sets Zeige eine Liste von möglichen Datenkontextpräfixen und auf was sie sich beziehen</li> <li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> für eine DefinedMeaning in einem Kontext, zeige alle anderen</li> <li>&action=help Hilfe anzeigen.</li> </ul></p>',
	'ow_conceptmapping_uitext'              => '<p>Mit Concept Mapping kann festgelegt werden, welche DefinedMeaning in einem Dataset mit anderen DefinedMeanings aus anderen Datasets identisch ist.</p>',
	'ow_conceptmapping_no_action_specified' => 'Entschuldigung, ich kann nicht "$1".',
	'ow_dm_OK'                              => 'OK',
	'ow_dm_not_present'                     => 'nicht eingegeben',
	'ow_dm_not_found'                       => 'nicht in der Datenbank gefunden oder fehlerhaft',
	'ow_mapping_successful'                 => 'Alle mit [OK] markierten Felder wurden zugeordnet<br>',
	'ow_mapping_unsuccessful'               => 'Es werden mindestens zwei DefinedMeanings zum Verknüpfen benötigt.',
	'ow_will_insert'                        => 'Folgendes wird eingesetzt:',
	'ow_contents_of_mapping'                => 'Inhalte der Verknüpfung',
	'ow_available_contexts'                 => 'Verfügbare Kontexte',
	'ow_add_concept_link'                   => 'Link zu anderen Konzepten hinzufügen',
	'ow_concept_panel'                      => 'Konzeptschaltfläche',
	'ow_dm_badtitle'                        => 'Diese Seite weist nicht zu einer DefinedMeaning (Konzept). Bitte überprüfe die Webadresse.',
	'ow_dm_missing'                         => 'Diese Seite weist zu einer nicht existierenden DefinedMeaning (Konzept). Bitte überprüfe die Webadresse.',
	'ow_AlternativeDefinition'              => 'Alternative Definition',
	'ow_AlternativeDefinitions'             => 'Alternative Definitionen',
	'ow_Annotation'                         => 'Annotation',
	'ow_ApproximateMeanings'                => 'Ungefähre Bedeutungen',
	'ow_ClassAttributeAttribute'            => 'Attribut',
	'ow_ClassAttributes'                    => 'Klassenattribute',
	'ow_ClassAttributeLevel'                => 'Level',
	'ow_ClassAttributeType'                 => 'Typ',
	'ow_ClassMembership'                    => 'Klassenzugehörigkeit',
	'ow_Collection'                         => 'Sammlung',
	'ow_CollectionMembership'               => 'Sammlungszugehörigkeit',
	'ow_Definition'                         => 'Definition',
	'ow_DefinedMeaningAttributes'           => 'Annotation',
	'ow_DefinedMeaning'                     => 'DefinedMeaning',
	'ow_DefinedMeaningReference'            => 'DefinedMeaning',
	'ow_ExactMeanings'                      => 'Exakte Bedeutungen',
	'ow_Expression'                         => 'Ausdruck',
	'ow_ExpressionMeanings'                 => 'Ausdrucksbedeutungen',
	'ow_Expressions'                        => 'Ausdrücke',
	'ow_IdenticalMeaning'                   => 'Identische Bedeutung?',
	'ow_IncomingRelations'                  => 'Eingehende Relationen',
	'ow_GotoSource'                         => 'Gehe zur Quelle',
	'ow_Language'                           => 'Sprache',
	'ow_LevelAnnotation'                    => 'Annotation',
	'ow_OptionAttribute'                    => 'Eigenschaft',
	'ow_OptionAttributeOption'              => 'Option',
	'ow_OptionAttributeOptions'             => 'Optionen',
	'ow_OptionAttributeValues'              => 'Optionswerte',
	'ow_OtherDefinedMeaning'                => 'Andere DefinedMeaning',
	'ow_PopupAnnotation'                    => 'Annotation',
	'ow_Relations'                          => 'Relationen',
	'ow_RelationType'                       => 'Relationstyp',
	'ow_Spelling'                           => 'Schreibweise',
	'ow_Synonyms'                           => 'Synonyme',
	'ow_SynonymsAndTranslations'            => 'Synonyme und Übersetzungen',
	'ow_Source'                             => 'Quelle',
	'ow_SourceIdentifier'                   => 'Quellenbezeichner',
	'ow_TextAttribute'                      => 'Eigenschaft',
	'ow_Text'                               => 'Text',
	'ow_TextAttributeValues'                => 'Plaintext',
	'ow_TranslatedTextAttribute'            => 'Eigenschaft',
	'ow_TranslatedText'                     => 'Übersetzter Text',
	'ow_TranslatedTextAttributeValue'       => 'Text',
	'ow_TranslatedTextAttributeValues'      => 'Übersetzbarer Text',
	'ow_LinkAttribute'                      => 'Eigenschaft',
	'ow_LinkAttributeValues'                => 'Links',
	'ow_Property'                           => 'Eigenschaft',
	'ow_Value'                              => 'Wert',
	'ow_meaningsoftitle'                    => 'Bedeutungen von "$1"',
	'ow_meaningsofsubtitle'                 => '<em>Wikilink:</em> [[$1]]',
	'ow_Permission_denied'                  => '<h2>ERLAUBNIS VERWEIGERT</h2>',
	'ow_copy_no_action_specified'           => 'Bitte lege eine Aktion fest.',
	'ow_copy_help'                          => 'Eines Tages können wir dir helfen.',
	'ow_please_proved_dmid'                 => 'Oje, deiner Eingabe fehlt ?dmid=<something> (dmid=Defined Meaning ID)<br>Ups, bitte kontaktiere den Serveradminstrator.',
	'ow_please_proved_dc1'                  => 'Oje, deiner Eingabe fehlt ?dc1=<something> (dc1=dataset context 1, dataset to copy FROM)<br>Ups, bitte kontaktiere den Serveradminstrator.',
	'ow_please_proved_dc2'                  => 'Oje, deiner Eingabe fehlt ?dc2=<something> (dc2=dataset context 2, dataset to copy TO) <br>Ups, bitte kontaktiere den Serveradminstrator.',
	'ow_copy_successful'                    => '<h2>Kopieren erfolgreich</h2>Deine Daten scheinen erfolgreich kopiert worden zu sein. Bitte vergiss nicht nochmals zu prüfen um sicherzugehen!',
);

/** Ελληνικά (Ελληνικά) */
$wdMessages['el'] = array(
	'datasearch' => 'Βικιδεδομένα: Αναζήτηση δεδομένων',
	'langman_title' => 'Διαχειριστής γλώσσας',
	'languages'     => 'Wikidata: Διαχειριστής γλώσσας',
);

/** Español (Español)
 * @author Ascánder
 */
$wdMessages['es'] = array(
	'ow_save'                          => 'Guardar',
	'ow_history'                       => 'Historial',
	'ow_datasets'                      => 'Selección de la base',
	'ow_noedit'                        => 'No tienes permiso de modificar las páginas de la base "$1". Mira [[{{MediaWiki:Ow editing policy url}}|nuestras reglas de modificación]].',
	'ow_noedit_title'                  => 'No se permite modificar',
	'ow_uipref_datasets'               => 'Vista por defecto',
	'ow_uiprefs'                       => 'Wikidata',
	'ow_none_selected'                 => '<No hay nada seleccionado>',
	'ow_conceptmapping_uitext'         => '<p>Ligar los conceptos permite identificar los sentidos definidos en un juego de datos que son idénticos a sentidos definidos en otros juegos de datos.</p>',
	'ow_dm_OK'                         => 'OK',
	'ow_dm_not_present'                => 'Ausente',
	'ow_dm_not_found'                  => 'No encontrado en la base de datos o con errores de representación',
	'ow_mapping_successful'            => 'Todos los campos marcados con [OK] fueron enlazados',
	'ow_mapping_unsuccessful'          => 'Deben haber dos sentidos definidos para poder ligarlos.',
	'ow_will_insert'                   => 'Insertará el texto siguiente:',
	'ow_available_contexts'            => 'Conceptos disponibles',
	'ow_add_concept_link'              => 'Enlazar otros conceptos',
	'ow_concept_panel'                 => 'Tablero de conceptos',
	'ow_dm_badtitle'                   => 'Esta pagina no enlaza ningún SentidoDefinido (concepto). Verifica el RUL.',
	'ow_dm_missing'                    => 'Esta página se dirige hacia un SentidoDefinido (concepto) inexistente. Verifica el URL.',
	'ow_AlternativeDefinition'         => 'Definición alterna',
	'ow_AlternativeDefinitions'        => 'Definiciones alternas',
	'ow_Annotation'                    => 'Anotación',
	'ow_ApproximateMeanings'           => 'Sentidos aproximados',
	'ow_ClassAttributeAttribute'       => 'Atributo',
	'ow_ClassAttributes'               => 'Atributos de clase',
	'ow_ClassAttributeLevel'           => 'Nivel',
	'ow_ClassAttributeType'            => 'Tipo',
	'ow_ClassMembership'               => 'Clases',
	'ow_Collection'                    => 'Colección',
	'ow_CollectionMembership'          => 'Colecciones',
	'ow_Definition'                    => 'Definición',
	'ow_DefinedMeaningAttributes'      => 'Notas',
	'ow_DefinedMeaning'                => 'Sentido definido',
	'ow_DefinedMeaningReference'       => 'Sentido definido',
	'ow_ExactMeanings'                 => 'Sentidos exactos',
	'ow_Expression'                    => 'Expresión',
	'ow_ExpressionMeanings'            => 'Significados de expresión',
	'ow_Expressions'                   => 'Expresiones',
	'ow_IdenticalMeaning'              => '¿Idéntico sentido?',
	'ow_IncomingRelations'             => 'Relaciones entrantes',
	'ow_GotoSource'                    => 'Ir a la fuente',
	'ow_Language'                      => 'Lengua',
	'ow_LevelAnnotation'               => 'Nota',
	'ow_OptionAttribute'               => 'Propiedad',
	'ow_OptionAttributeOption'         => 'Opción',
	'ow_OptionAttributeOptions'        => 'Opciones',
	'ow_OptionAttributeValues'         => 'Valores',
	'ow_OtherDefinedMeaning'           => 'Otro sentido definido',
	'ow_PopupAnnotation'               => 'Nota',
	'ow_Relations'                     => 'Relaciones',
	'ow_RelationType'                  => 'Tipo de relación',
	'ow_Spelling'                      => 'Ortografía',
	'ow_Synonyms'                      => 'Sinónimos',
	'ow_SynonymsAndTranslations'       => 'Sinónimos y traducciones',
	'ow_SourceIdentifier'              => 'Fuente',
	'ow_TextAttribute'                 => 'Propiedad',
	'ow_Text'                          => 'Texto',
	'ow_TextAttributeValues'           => 'Textos libres',
	'ow_TranslatedTextAttribute'       => 'Propiedad',
	'ow_TranslatedText'                => 'Texto traducido',
	'ow_TranslatedTextAttributeValue'  => 'Texto',
	'ow_TranslatedTextAttributeValues' => 'Textos traducibles',
	'ow_LinkAttribute'                 => 'Propiedad',
	'ow_LinkAttributeValues'           => 'Enlaces',
	'ow_Property'                      => 'Propiedad',
	'ow_Value'                         => 'Valor',
	'ow_meaningsoftitle'               => 'Significado de "$1"',
	'ow_meaningsofsubtitle'            => '<em>Wiki enlace:</em> [[$1]]',
	'ow_Permission_denied'             => '<h2>PERMISO NEGADO</h2>',
	'ow_please_proved_dmid'            => 'Falta un ?dmid=<...> (dmid=Id de SentidoDefinido)
Favor contactar al administrador.',
	'ow_please_proved_dc1'             => 'Falta un ?dc1=<...> (dc1=contexto de la base DESDE la cual se copia)<br>Contacta a un administrador.',
	'ow_please_proved_dc2'             => 'Falta un ?dc2=<...> (dc1=Contexto de la segunda base, hacia la cual se copia)
Favor contactar al administrador.',
	'ow_copy_successful'               => '<h2>Copia exitosa</h2>Sus datos han sido copiados exitosamente (Favor verificar de todas formas).',
);

/** Suomi (Suomi)
 */
$wdMessages['fi'] = array(
	'ow_AlternativeDefinition'   => 'Vaihtoehtoinen määritelmä',
	'ow_AlternativeDefinitions'  => 'Vaihtoehtoiset määritelmät',
	'ow_Annotation'              => 'Annotaatiot',
	'ow_ClassAttributeAttribute' => 'Ominaisuus',
	'ow_ClassAttributes'         => 'Luokkaominaisuudet',
	'ow_ClassAttributeLevel'     => 'Taso',
	'ow_ClassAttributeType'      => 'Tyyppi',
	'ow_Definition'              => 'Määritelmä',
	'ow_ExactMeanings'           => 'Tarkat merkityset',
	'ow_Expression'              => 'Ilmaisu',
	'ow_Expressions'             => 'Ilmaisut',
	'ow_IdenticalMeaning'        => 'Identtinen merkitys',
	'ow_Language'                => 'Kieli',
	'ow_LevelAnnotation'         => 'Annotaatio',
	'ow_Spelling'                => 'Kirjoitusasu',
	'ow_SynonymsAndTranslations' => 'Synonyymit ja käännökset',
	'ow_Text'                    => 'Teksti',
);

/** French (Français)
 * @author Grondin
 */
$wdMessages['fr'] = array(
	'datasearch' => 'Wikidata: Recherche de données',
	'langman_title'                         => 'Gestion des langues',
	'languages'                             => 'Wikidata: Gestion des langues',
	'ow_save'                               => 'Sauvegarder',
	'ow_history'                            => 'Historique',
	'ow_datasets'                           => 'Selection des données définies',
	'ow_noedit'                             => "Vous n'êtes pas autorisé d'éditer les pages dans les données préétablies « $1 ». Veuillez voir [[{{MediaWiki:Ow editing policy url}}|nos règles d'édition]].",
	'ow_noedit_title'                       => "Aucune permission d'éditer.",
	'ow_uipref_datasets'                    => 'Vue par défaut',
	'ow_uiprefs'                            => 'Données wiki',
	'ow_none_selected'                      => '<Aucune sélection>',
	'ow_conceptmapping_help'                => "<p>actions possibles : <ul>
<li>&action=insert&<data_context_prefix>=<defined_id>&...  insérer une carte</li>
<li>&action=get&concept=<concept_id>  revoir une carte</li>
<li>&action=list_sets  retourne une liste des préfixes de contextes possibles et sur quoi ils se réfèrent.</li>
<li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> pour un défini dans le sens d'un concept, retourne tous les autres.</li>
<li>&action=help  Voir l’aide complète.</li>
</ul></p>",
	'ow_conceptmapping_uitext'              => "<p>Le carte des concepts vous permets d'identifier
que le sens défini dans une donnée soit identique
aux sens définis dans les autres données.</p>",
	'ow_conceptmapping_no_action_specified' => 'Excuses, je ne comprend pas « $1 ».',
	'ow_dm_OK'                              => 'Valider',
	'ow_dm_not_present'                     => 'non inscrit',
	'ow_dm_not_found'                       => 'non trouvé dans la base de donnée ou mal rédigé',
	'ow_mapping_successful'                 => 'Planifie tous les champs marqués avec [Valider]<br>',
	'ow_mapping_unsuccessful'               => 'Nécessite au moins deux sens définis avanc que je puisse les relier.',
	'ow_will_insert'                        => 'Insersera les suivants :',
	'ow_contents_of_mapping'                => 'Contenu de la planification',
	'ow_available_contexts'                 => 'Contextes disponibles',
	'ow_add_concept_link'                   => 'Ajoute un lien aux autres concepts',
	'ow_concept_panel'                      => 'Éventail de concepts',
	'ow_dm_badtitle'                        => "Cette page ne pointe sur aucun concept ou sens défini. Veuillez vérifier l'adresse internet.",
	'ow_dm_missing'                         => "Cette pas semble pointer vers un concept ou sens inexistant. Veuillez vérifier l'adresse internet.",
	'ow_AlternativeDefinition'              => 'Définition alternative',
	'ow_AlternativeDefinitions'             => 'Définitions alternatives',
	'ow_Annotation'                         => 'Annotation',
	'ow_ApproximateMeanings'                => 'Sens approximatifs',
	'ow_ClassAttributeAttribute'            => 'Attribut',
	'ow_ClassAttributes'                    => 'Attributs de classe',
	'ow_ClassAttributeLevel'                => 'Niveau',
	'ow_ClassMembership'                    => 'Classes',
	'ow_CollectionMembership'               => 'Collections',
	'ow_Definition'                         => 'Définition',
	'ow_DefinedMeaning'                     => 'Sens défini',
	'ow_DefinedMeaningReference'            => 'Sens défini',
	'ow_ExactMeanings'                      => 'Sens exacts',
	'ow_ExpressionMeanings'                 => 'Sens des expressions',
	'ow_IdenticalMeaning'                   => 'Sens identique ?',
	'ow_IncomingRelations'                  => 'Relations entrantes',
	'ow_GotoSource'                         => 'Voir la source',
	'ow_Language'                           => 'Langue',
	'ow_OptionAttribute'                    => 'Propriété',
	'ow_OptionAttributeValues'              => 'Valeurs des options',
	'ow_OtherDefinedMeaning'                => 'Autre sens défini',
	'ow_RelationType'                       => 'Type de relation',
	'ow_Spelling'                           => 'Orthographe',
	'ow_Synonyms'                           => 'Synonymes',
	'ow_SynonymsAndTranslations'            => 'Synonymes et traductions',
	'ow_SourceIdentifier'                   => 'Identificateur de source',
	'ow_TextAttribute'                      => 'Propriété',
	'ow_Text'                               => 'Texte',
	'ow_TextAttributeValues'                => 'Texte libre',
	'ow_TranslatedTextAttribute'            => 'Propriété',
	'ow_TranslatedText'                     => 'Texte traduit',
	'ow_TranslatedTextAttributeValue'       => 'Texte',
	'ow_TranslatedTextAttributeValues'      => 'Textes traduisibles',
	'ow_LinkAttribute'                      => 'Propriété',
	'ow_LinkAttributeValues'                => 'Liens',
	'ow_Property'                           => 'Propriété',
	'ow_Value'                              => 'Valeur',
	'ow_meaningsoftitle'                    => 'Sens de "$1"',
	'ow_meaningsofsubtitle'                 => '<em>lien wiki :</em> [[$1]]',
	'ow_Permission_denied'                  => '<h2>PERMISSION REFUSÉE</h2>',
	'ow_copy_no_action_specified'           => 'Merci de spécifier une action',
	'ow_copy_help'                          => 'Aide à venir...',
	'ow_please_proved_dmid'                 => 'Il manque un ?dmid=<...> (dmid=SensDéfini ID)<br>Contactez l’administrateur.',
	'ow_please_proved_dc1'                  => 'Il manque un ?dc1=<...> (dc1=contexte de la base 1, base DEPUIS laquelle on copie)<br>Contactez l’administrateur.',
	'ow_please_proved_dc2'                  => 'Il manque un ?dc2=<...> (dc1=contexte de la base 2, base VERS laquelle on copie)<br>Contactez l’administrateur.',
	'ow_copy_successful'                    => '<h2>Succès de la copie</h2>Vos données ont été copiées avec succès (vérifiez quand même).',
	'ow_copy_unsuccessful'                  => "<h3>Copie infructueuse</h3> Aucune opération de copie n'a pris place.",
	'ow_no_action_specified'                => "<h3>Aucune action n'a été spécifiée</h3> Peut-être, seriez vous venu sur cette page directement ? Vous n'avez pas besoin, en principe, d'être là.",
	'ow_db_consistency_not_found'           => "<h2>Erreur</h2> Un problème a été trouvé dans la base de donnée. Wikidata ne peut trouver des données valides liées au numéro de sens défini. Il pourrait être perdu. Veuillez contacter l'opérateur ou l'administrateur du serveur.",
);

$wdMessages['gl'] = array(
	'datasearch' => 'Wikidata: Procura de datos',
	'langman_title' => 'Xestor de linguas',
	'languages' => 'Wikidata: Xestor de linguas',
);

$wdMessages['he'] = array(
	'langman_title' => 'מנהל שפות',
);

$wdMessages['hsb'] = array(
	'datasearch' => 'Wikidata: Pytanje datow',
	'langman_title' => 'Zrjadowak rěčow',
	'languages' => 'Wikidata: Zrjadowak rěčow',
);

$wdMessages['id'] = array(
	'datasearch' => 'Wikidata: Pencarian data',
	'langman_title' => 'Pengelola bahasa',
	'languages'=>'Wikidata: Pengelola bahasa',
);

/** ქართული (ქართული)
 * @author Sopho
 */
$wdMessages['ka'] = array(
	'ow_history'                  => 'ისტორია',
	'ow_AlternativeDefinition'    => 'ალტერნატიული განსაზღვრება',
	'ow_AlternativeDefinitions'   => 'ალტერნატიული განსაზღვრებები',
	'ow_Annotation'               => 'შენიშვნა',
	'ow_ApproximateMeanings'      => 'მიახლოებითი მნიშვნელობები',
	'ow_Collection'               => 'კოლექცია',
	'ow_Definition'               => 'განსაზღვრება',
	'ow_DefinedMeaningAttributes' => 'შენიშვნა',
	'ow_ExactMeanings'            => 'ზუსტი მნიშვნელობები',
	'ow_Expression'               => 'გამოთქმა',
	'ow_IdenticalMeaning'         => 'იდენტური მნიშვნელობა?',
	'ow_Language'                 => 'ენა',
	'ow_LevelAnnotation'          => 'შენიშვნა',
	'ow_PopupAnnotation'          => 'შენიშვნა',
	'ow_Spelling'                 => 'ორთოგრაფია',
	'ow_SynonymsAndTranslations'  => 'სინონიმები და თარგმანი',
	'ow_Source'                   => 'წყარო',
	'ow_Text'                     => 'ტექსტი',
);

$wdMessages['kk-kz'] = array(
	'langman_title' => 'Тілдерді меңгеру',
);

$wdMessages['kk-tr'] = array(
	'langman_title' => 'Tilderdi meñgerw',
);

$wdMessages['kk-cn'] = array(
	'langman_title' => 'تٴىلدەردٴى مەڭگەرۋ',
);

$wdMessages['kk'] = $wdMessages['kk-kz'];

/** Luxembourgish (Lëtzebuergesch)
 * @author Robby
 */
$wdMessages['lb'] = array(
	'datasearch' => 'Wikidata: Date sichen',
	'langman_title' => 'Sproochmanager',
	'languages'     => 'Wikidata: Sproochmanager',
);

/** Lithuanian (Lietuvių)
 * @author Matasg
 */
$messages['lt'] = array(
	'datasearch' => 'Wikidata: Duomenų paieška',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$wdMessages['nl'] = array(
	'datasearch' => 'Wikidata: Gegevens zoeken',
	'langman_title'                         => 'Taalmanager',
	'languages'                             => 'Wikidata: Taalmanager',
	'ow_save'                               => 'Opslaan',
	'ow_history'                            => 'Geschiedenis',
	'ow_datasets'                           => 'Data-set selectie',
	'ow_noedit'                             => 'U heeft geen rechten om pagina\'s te bewerken in de dataset "$1". Zie [[{{MediaWiki:Ow editing policy url}}|ons bewerkingsbeleid]].',
	'ow_noedit_title'                       => 'Geen toestemming om te wijzigen',
	'ow_uipref_datasets'                    => 'Standaard overzicht',
	'ow_uiprefs'                            => 'Wikidata',
	'ow_none_selected'                      => '<Geen selectie>',
	'ow_conceptmapping_help'                => '<p>mogelijke handelingen:<ul>
<li>&action=insert&<data_context_prefix>=<defined_id>&...  een mapping toevoegen</li>
<li>&action=get&concept=<concept_id>  een mapping teruglezen</li>
<li>&action=list_sets  geeft een lijst met mogelijke gegevenscontextvoorvoegsels terug en waar ze aan refereren.</li>
<li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> voor een defined meaning in een cencept, toont all overigen</li>
<li>&action=help  Zinvolle helptekst tonen.</li>
</ul></p>',
	'ow_conceptmapping_uitext'              => 'ConceptMapping maakt het mogelijk om gedefinieerde betekenissen in meerdere data-sets als identiek te markeren.',
	'ow_conceptmapping_no_action_specified' => 'Excuses, maar ik weet niet hoe ik kan "$1".',
	'ow_dm_OK'                              => 'Ok',
	'ow_dm_not_present'                     => 'niet ingevoegd',
	'ow_dm_not_found'                       => 'niet aangetroffen in de database of verminkt',
	'ow_mapping_successful'                 => 'Wat met [OK] gemarkeerd is, is gemapt.<br>',
	'ow_mapping_unsuccessful'               => 'Minstens twee gedefinieerde betekenissen zijn nodig voordat er gelinkt kan worden.',
	'ow_will_insert'                        => 'Zal het volgende toevoegen:',
	'ow_contents_of_mapping'                => 'Inhoud van de mapping',
	'ow_add_concept_link'                   => 'Voeg link toe aan concepten',
	'ow_concept_panel'                      => 'Concept paneel',
	'ow_dm_badtitle'                        => 'Deze pagina wijst niet naar enige DefinedMeaning (concept). Controleer svp het web adres.',
	'ow_AlternativeDefinition'              => 'Alternatieve definitie',
	'ow_AlternativeDefinitions'             => 'Alternatieve definities',
	'ow_Annotation'                         => 'Annotatie',
	'ow_ApproximateMeanings'                => 'Niet exacte betekenissen',
	'ow_ClassAttributeAttribute'            => 'Attribuut',
	'ow_ClassAttributes'                    => 'Klasse attributen',
	'ow_ClassAttributeLevel'                => 'Niveau',
	'ow_ClassMembership'                    => 'Klasse lidmaatschap',
	'ow_Collection'                         => 'Collectie',
	'ow_CollectionMembership'               => 'Collectie lidmaatschap',
	'ow_Definition'                         => 'Definitie',
	'ow_DefinedMeaningAttributes'           => 'Annotatie',
	'ow_ExactMeanings'                      => 'Exacte betekenissen',
	'ow_Expression'                         => 'Expressie',
	'ow_Expressions'                        => 'Expressies',
	'ow_IdenticalMeaning'                   => 'Identieke betekenis?',
	'ow_IncomingRelations'                  => 'Binnenkomende relaties',
	'ow_Language'                           => 'Taal',
	'ow_LevelAnnotation'                    => 'Annotatie',
	'ow_OptionAttribute'                    => 'Eigenschap',
	'ow_OptionAttributeOption'              => 'Optie',
	'ow_OptionAttributeOptions'             => 'Opties',
	'ow_OptionAttributeValues'              => 'Optie waarden',
	'ow_OtherDefinedMeaning'                => 'Andere gedefinieerde betekenis',
	'ow_PopupAnnotation'                    => 'Annotatie',
	'ow_Relations'                          => 'Relaties',
	'ow_RelationType'                       => 'Relatietype',
	'ow_Synonyms'                           => 'Synoniemen',
	'ow_SynonymsAndTranslations'            => 'Synoniemen en vertalingen',
	'ow_Source'                             => 'Bron',
	'ow_SourceIdentifier'                   => 'Bron identificatie',
	'ow_TextAttribute'                      => 'Eigenschap',
	'ow_Text'                               => 'Tekst',
	'ow_TextAttributeValues'                => 'Platte tekst',
	'ow_TranslatedTextAttribute'            => 'Eigenschap',
	'ow_TranslatedText'                     => 'Vertaalde tekst',
	'ow_TranslatedTextAttributeValue'       => 'Tekst',
	'ow_TranslatedTextAttributeValues'      => 'Vertaalbare tekst',
	'ow_LinkAttribute'                      => 'Eigenschap',
	'ow_Property'                           => 'Eigenschap',
	'ow_Value'                              => 'Waarde',
	'ow_meaningsoftitle'                    => 'Betekenissen van "$1"',
	'ow_Permission_denied'                  => '<h2>TOESTEMMING GEWEIGERD</h2>',
	'ow_copy_successful'                    => '<h2>Kopiëren succesvol</h2>Het lijkt er op dat het kopiëren van de data goed ging. Vergeet niet om dit te controleren!',
);

/** Norwegian (‪Norsk (bokmål)‬)
 * @author Jon Harald Søby
 */
$wdMessages['no'] = array(
	'datasearch' => 'Wikidata: Datasøk',
	'langman_title' => 'Språkbehandler',
	'languages'     => 'Wikidata: Språkbehandler',
);

$wdMessages['oc'] = array(
	'datasearch' => 'Wikidata: Recèrca de donadas',
	'langman_title' => 'Gestion de las lengas',
	'languages' => 'Wikidata: Gestion de las lengas',
);

/** Piemontèis (Piemontèis)
 */
$wdMessages['pms'] = array(
	'datasearch' => 'Wikidata: Arsërca antra ij dat',
	'langman_title'                   => 'Gestor dle lenghe',
	'languages'                       => 'Wikidata: Gestor dle lenghe',
	'ow_save'                         => 'Salvé',
	'ow_datasets'                     => 'Base dat',
	'ow_AlternativeDefinition'        => 'Definission alternativa',
	'ow_AlternativeDefinitions'       => 'Definission alternative',
	'ow_Annotation'                   => 'Nòta',
	'ow_ApproximateMeanings'          => 'Sust a truch e branca',
	'ow_ClassMembership'              => 'Part ëd la class',
	'ow_Collection'                   => 'Colession',
	'ow_CollectionMembership'         => 'Part ëd la colession',
	'ow_Definition'                   => 'Sust',
	'ow_DefinedMeaningAttributes'     => 'Nòta',
	'ow_DefinedMeaning'               => 'Sust definì',
	'ow_DefinedMeaningReference'      => 'Sust definì',
	'ow_ExactMeanings'                => 'Àutri sust daspërlor',
	'ow_Expression'                   => 'Espression',
	'ow_Expressions'                  => 'Espression',
	'ow_IdenticalMeaning'             => 'Istess sust?',
	'ow_IncomingRelations'            => "Relassion ch'a rivo",
	'ow_Language'                     => 'Lenga',
	'ow_LevelAnnotation'              => 'Nòta',
	'ow_OtherDefinedMeaning'          => 'Àutri sust',
	'ow_PopupAnnotation'              => 'Nòta',
	'ow_Relations'                    => 'Relassion',
	'ow_RelationType'                 => 'Sòrt ëd relassion',
	'ow_Spelling'                     => 'Forma',
	'ow_Synonyms'                     => 'Sinònim',
	'ow_SynonymsAndTranslations'      => 'Sinònim e viragi',
	'ow_Source'                       => 'Sorgiss',
	'ow_SourceIdentifier'             => 'Identificativ dla sorgiss',
	'ow_Text'                         => 'Test',
	'ow_TranslatedTextAttributeValue' => 'Test',
	'ow_Property'                     => 'Proprietà',
	'ow_Value'                        => 'Valor',
);

/** Português (Português)
 */
$wdMessages['pt'] = array(
	'datasearch' => 'Wikidata: Pesquisa de dados',
	'langman_title'                         => 'Gestor de línguas',
	'languages'                             => 'Gestor de línguas',
	'ow_save'                               => 'Salvar',
	'ow_history'                            => 'História',
	'ow_datasets'                           => 'Selecção do conjunto de dados',
	'ow_noedit'                             => 'Não está autorizado a editar páginas no conjunto de dados "$1". Por favor, veja [[{{MediaWiki:Ow editing policy url}}|a nossa política de edição]].',
	'ow_noedit_title'                       => 'Não tem permissões para editar',
	'ow_uipref_datasets'                    => 'Vista padrão',
	'ow_uiprefs'                            => 'Wikidata',
	'ow_none_selected'                      => '<Nenhum seleccionado>',
	'ow_conceptmapping_help'                => '<p>acções possíveis: 
<ul>
 <li>&action=insert&<data_context_prefix>=<defined_id>&... inserir um mapeamento</li> 
 <li>&action=get&concept=<concept_id> ler um mapeamento de volta</li>
 <li>&action=list_sets retornar uma lista de prefixos de contexto de dados possíveis e a que se referem.</li>
 <li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> para um significado definido num conceito, retornar todos os outros</li>
 <li>&action=help Mostrar ajuda preciosa.</li> 
</ul>
</p>',
	'ow_conceptmapping_uitext'              => '<p>O Mapeamento de Conceitos permite-lhe identificar que significado definido num conjunto de dados é idêntico a outros significados definidos noutro conjunto de dados.</p>',
	'ow_conceptmapping_no_action_specified' => 'Desculpe, não sei como "$1".',
	'ow_dm_OK'                              => 'OK',
	'ow_dm_not_present'                     => 'não introduzido',
	'ow_dm_not_found'                       => 'não encontrado na base de dados ou mal formado',
	'ow_mapping_successful'                 => 'Mapeados todos os campos marcados com [OK]<br>',
	'ow_mapping_unsuccessful'               => 'É necessário ter dois significados definidos antes de poder ligá-los.',
	'ow_will_insert'                        => 'Será inserido o seguinte:',
	'ow_contents_of_mapping'                => 'Conteúdo de mapeamento',
	'ow_available_contexts'                 => 'Contextos disponíveis',
	'ow_add_concept_link'                   => 'Adicionar ligação para outros conceitos',
	'ow_concept_panel'                      => 'Painel de Conceitos',
	'ow_dm_badtitle'                        => 'Esta página não aponta para nenhum Significado Definido (conceito). Por favor, verifique o endereço web.',
	'ow_dm_missing'                         => 'Esta página parece apontar para um Significado Definido (conceito) inexistente. Por favor, verifique o endereço web.',
	'ow_AlternativeDefinition'              => 'Definição alternativa',
	'ow_AlternativeDefinitions'             => 'Definições alternativas',
	'ow_Annotation'                         => 'Anotação',
	'ow_ApproximateMeanings'                => 'Significados aproximados',
	'ow_ClassAttributeAttribute'            => 'Atributo',
	'ow_ClassAttributes'                    => 'Atributos de classe',
	'ow_ClassAttributeLevel'                => 'Nível',
	'ow_ClassAttributeType'                 => 'Tipo',
	'ow_ClassMembership'                    => 'Associação a classes',
	'ow_Collection'                         => 'Colecção',
	'ow_CollectionMembership'               => 'Associação a colecções',
	'ow_Definition'                         => 'Definição',
	'ow_DefinedMeaningAttributes'           => 'Anotação',
	'ow_DefinedMeaning'                     => 'Significado definido',
	'ow_DefinedMeaningReference'            => 'Significado definido',
	'ow_ExactMeanings'                      => 'Significados exactos',
	'ow_Expression'                         => 'Expressão',
	'ow_ExpressionMeanings'                 => 'Significados da expressão',
	'ow_Expressions'                        => 'Expressões',
	'ow_IdenticalMeaning'                   => 'Significado idêntico?',
	'ow_IncomingRelations'                  => 'Relações afluentes',
	'ow_GotoSource'                         => 'Ir para fonte',
	'ow_Language'                           => 'Língua',
	'ow_LevelAnnotation'                    => 'Anotação',
	'ow_OptionAttribute'                    => 'Propriedade',
	'ow_OptionAttributeOption'              => 'Opção',
	'ow_OptionAttributeOptions'             => 'Opções',
	'ow_OptionAttributeValues'              => 'Valores da opção',
	'ow_OtherDefinedMeaning'                => 'Outro significado definido',
	'ow_PopupAnnotation'                    => 'Anotação',
	'ow_Relations'                          => 'Relações',
	'ow_RelationType'                       => 'Tipo de relação',
	'ow_Spelling'                           => 'Ortografia',
	'ow_Synonyms'                           => 'Sinónimos',
	'ow_SynonymsAndTranslations'            => 'Sinónimos e traduções',
	'ow_Source'                             => 'Fonte',
	'ow_SourceIdentifier'                   => 'Identificador da fonte',
	'ow_TextAttribute'                      => 'Propriedade',
	'ow_Text'                               => 'Texto',
	'ow_TextAttributeValues'                => 'Textos plenos',
	'ow_TranslatedTextAttribute'            => 'Propriedade',
	'ow_TranslatedText'                     => 'Texto traduzido',
	'ow_TranslatedTextAttributeValue'       => 'Texto',
	'ow_TranslatedTextAttributeValues'      => 'Textos traduzíveis',
	'ow_LinkAttribute'                      => 'Propriedade',
	'ow_LinkAttributeValues'                => 'Ligações',
	'ow_Property'                           => 'Propriedade',
	'ow_Value'                              => 'Valor',
	'ow_meaningsoftitle'                    => 'Significados de "$1"',
	'ow_meaningsofsubtitle'                 => '<em>Ligação Wiki:</em> [[$1]]',
	'ow_Permission_denied'                  => '<h2>PERMISSÃO NEGADA</h2>',
	'ow_copy_no_action_specified'           => 'Por favor, especifique uma acção',
	'ow_copy_help'                          => 'Um dia pode ser que possamos ajudá-lo.',
	'ow_please_proved_dmid'                 => 'Epá, parece que está a faltar um ?dmid=<qualquercoisa> (dmid=ID do Significado Definido) aos dados introduzidos<br>
Oops, por favor, contacte um administrador do servidor.',
	'ow_please_proved_dc1'                  => 'Epá, parece que está a faltar um ?dc1=<qualquercoisa> (dc1=contexto de conjunto de dados 1, conjunto de dados DO qual copiar) aos dados introduzidos<br>
Oops, por favor, contacte um administrador do servidor.',
	'ow_please_proved_dc2'                  => 'Epá, parece que está a faltar um ?dc2=<qualquercoisa> (dc2=contexto de conjunto de dados 2, conjunto de dados PARA o qual copiar) aos dados introduzidos<br>
Oops, por favor, contacte um administrador do servidor.',
	'ow_copy_successful'                    => '<h2>Cópia com Sucesso</h2>
Os seus dados aparentam ter sido copiados com sucesso. Não se esqueça de verificar para ter a certeza!',
);

$wdMessages['ro'] = array(
	'datasearch' => 'Wikidata: Căutare de date',
);

$wdMessages['sk'] = array(
	'datasearch' => 'Wikidata: Hľadanie údajov',
	'langman_title' => 'Správca jazykov',
	'languages' => 'Wikidata: Správca jazykov',
);

/** ћирилица (ћирилица)
 * @author Millosh
 */
$wdMessages['sr-ec'] = array(
	'ow_save'                               => 'Сачувај',
	'ow_history'                            => 'Историја',
	'ow_datasets'                           => 'Одабир скупа података',
	'ow_noedit'                             => 'Није ти дозвољено да мењаш стране у скупу података "$1". Види [[{{MediaWiki:Ow editing policy url}}|нашу уређивачку политику]].',
	'ow_noedit_title'                       => 'Без дозволе за уређивање',
	'ow_uipref_datasets'                    => 'Подразумевани поглед',
	'ow_uiprefs'                            => 'Викидата',
	'ow_none_selected'                      => '<Ништа није означено>',
	'ow_conceptmapping_help'                => '<p>могуће акције: <ul> <li>&action=insert&<data_context_prefix>=<defined_id>&... унеси мапирање</li> <li>&action=get&concept=<concept_id> поново прочитај мапирање</li> <li>&action=list_sets врати листу могућих контекстуалних префикса и оног на шта упућују.</li> <li>&action=get_associated&dm=<defined_meaning_id>&dc=<dataset_context_prefix> за једно дефинисано значење у концепту врати сва остала</li> <li>&action=help Прикажи помоћ.</li> </ul></p>',
	'ow_conceptmapping_uitext'              => '<p>Мапирање концепата ти омогућава да установиш које је дефинисано значење у једном скупу података истоветно с дефинисаним значењима у другим скуповима података.</p>',
	'ow_conceptmapping_no_action_specified' => 'Извињавам се, не знам како да урадим "$1".',
	'ow_dm_OK'                              => 'Уреду',
	'ow_dm_not_present'                     => 'није унесено',
	'ow_dm_not_found'                       => 'није пронађено у бази података или је лоше обликовано',
	'ow_mapping_successful'                 => 'Сва поља означена са [Уреду]<br />',
	'ow_mapping_unsuccessful'               => 'Потребна су бар два дефинисана значења пре него што их могу повезати.',
	'ow_will_insert'                        => 'Унеће се следеће:',
	'ow_contents_of_mapping'                => 'Садржај мапирања',
	'ow_available_contexts'                 => 'Могући контексти',
	'ow_add_concept_link'                   => 'Додај линк у друге концепте',
	'ow_concept_panel'                      => 'Табла концепата',
	'ow_dm_badtitle'                        => 'Ова страна не показује на ДефинисаноЗначење (концепт). Провери веб адресу.',
	'ow_dm_missing'                         => 'Ова страна показује на непостојеће ДефинисаноЗначење (концепт). Провери веб адресу.',
	'ow_AlternativeDefinition'              => 'Алтернативна дефиниција',
	'ow_AlternativeDefinitions'             => 'Алтернативне дефиниције',
	'ow_Annotation'                         => 'Коментар',
	'ow_ApproximateMeanings'                => 'Приближна значења',
	'ow_ClassAttributeAttribute'            => 'Особина',
	'ow_ClassAttributes'                    => 'Класа особина',
	'ow_ClassAttributeLevel'                => 'Ниво',
	'ow_ClassAttributeType'                 => 'Тип',
	'ow_ClassMembership'                    => 'Класа чланство',
	'ow_Collection'                         => 'Збирка',
	'ow_CollectionMembership'               => 'Збирка чланство',
	'ow_Definition'                         => 'Дефиниција',
	'ow_DefinedMeaningAttributes'           => 'Коментар',
	'ow_DefinedMeaning'                     => 'Дефинисано значење',
	'ow_DefinedMeaningReference'            => 'Дефинисано значење',
	'ow_ExactMeanings'                      => 'Тачна значења',
	'ow_Expression'                         => 'Израз',
	'ow_ExpressionMeanings'                 => 'Значења израза',
	'ow_Expressions'                        => 'Значења',
	'ow_IdenticalMeaning'                   => 'Истоветно значење',
	'ow_IncomingRelations'                  => 'Долазеће релације',
	'ow_GotoSource'                         => 'Иди на извор',
	'ow_Language'                           => 'Језик',
	'ow_LevelAnnotation'                    => 'Коментар',
	'ow_OptionAttribute'                    => 'Особина',
	'ow_OptionAttributeOption'              => 'Опција',
	'ow_OptionAttributeOptions'             => 'Опције',
	'ow_OptionAttributeValues'              => 'Вредности опције',
	'ow_OtherDefinedMeaning'                => '(Неко) друго дефинисано значење',
	'ow_PopupAnnotation'                    => 'Коментар',
	'ow_Relations'                          => 'Релације',
	'ow_RelationType'                       => 'Тип релације',
	'ow_Spelling'                           => 'Правопис',
	'ow_Synonyms'                           => 'Синоними',
	'ow_SynonymsAndTranslations'            => 'Синоними и преводи',
	'ow_Source'                             => 'Извор',
	'ow_SourceIdentifier'                   => 'Означавалац извора',
	'ow_TextAttribute'                      => 'Особина',
	'ow_Text'                               => 'Текст',
	'ow_TextAttributeValues'                => 'Равни текстови',
	'ow_TranslatedTextAttribute'            => 'Особина',
	'ow_TranslatedText'                     => 'Преведен текст',
	'ow_TranslatedTextAttributeValue'       => 'Текст',
	'ow_TranslatedTextAttributeValues'      => 'Текстови за превођење',
	'ow_LinkAttribute'                      => 'Особина',
	'ow_LinkAttributeValues'                => 'Линкови',
	'ow_Property'                           => 'Особина',
	'ow_Value'                              => 'Вредност',
	'ow_meaningsoftitle'                    => 'Значења "$1"',
	'ow_meaningsofsubtitle'                 => '<em>Вики линк:</em> [[$1]]',
	'ow_Permission_denied'                  => '<h2>ПРИСТУП НИЈЕ ДОЗВОЉЕН</h2>',
	'ow_copy_no_action_specified'           => 'Одреди акцију',
	'ow_copy_help'                          => 'Можда ћемо моћи да ти помогнемо једног дана.',
	'ow_please_proved_dmid'                 => 'Ух, изгледа да у твом уносу недостаје ?dmid=<something> (dmid=Defined Meaning ID)<br>Хмм... Контактирај администратора сервера.',
	'ow_please_proved_dc1'                  => 'Ух, изгледа да у твом уносу недостаје ?dc1=<something> (dc1=dataset context 1, dataset to copy FROM)<br>Хмм... Контактирај администратора сервера.',
	'ow_please_proved_dc2'                  => 'Ух, изгледа да у твом уносу недостаје ?dc2=<something> (dc2=dataset context 2, dataset to copy TO)<br>Хмм... Контактирај администратора сервера.',
	'ow_copy_successful'                    => '<h2>Умножавање успешно</h2>Чини се да су твоји подаци успешно умножени. Не заборави два пута да провериш!',
);

/** Seeltersk (Seeltersk)
 * @author Pyt
 */
$wdMessages['stq'] = array(
	'datasearch' => 'Wikidata: Doatensäike',
	'langman_title' => 'Sproakmanager',
	'languages'     => 'Wikidata: Sproakmanager',
);

$wdMessages['sv'] = array(
	'datasearch' => 'Wikidata: Datasökning',
	'langman_title' => 'Språkhanterare',
	'languages' => 'Wikidata: Språkhanterare',
);

/** Turkish (Türkçe)
 * @author Karduelis
 */
$wdMessages['tr'] = array(
	'langman_title' => 'Lisan idarecisi',
	'languages'     => 'Wikidata: Lisan idarecisi',
);

/** Volapük (Volapük)
 */
$wdMessages['vo'] = array(
	'ow_history'                      => 'Jenotem',
	'ow_add_concept_link'             => 'Läükön yümi tikädes votik',
	'ow_concept_panel'                => 'Tikädafremül',
	'ow_Annotation'                   => 'Penet',
	'ow_Collection'                   => 'Konlet',
	'ow_Definition'                   => 'Miedet',
	'ow_DefinedMeaningAttributes'     => 'Penet',
	'ow_Expression'                   => 'Notod',
	'ow_Expressions'                  => 'Notods',
	'ow_Language'                     => 'Pük',
	'ow_LevelAnnotation'              => 'Penet',
	'ow_PopupAnnotation'              => 'Penet',
	'ow_Spelling'                     => 'Tonatam',
	'ow_Source'                       => 'Fonät',
	'ow_Text'                         => 'Vödem',
	'ow_TranslatedTextAttributeValue' => 'Vödem',
	'ow_LinkAttributeValues'          => 'Liuds',
);

$wdMessages['yue'] = array(
	'datasearch' => 'Wikidata: 搵資料',
);

$wdMessages['zh-hans'] = array(
	'datasearch' => 'Wikidata: 数据搜寻',
	'langman_title' => '语言管理员',
	'languages'=>'Wikidata: 语言管理员',
);

$wdMessages['zh-hant'] = array(
	'datasearch' => 'Wikidata: 資料搜尋',
	'langman_title' => '語言管理員',
	'languages'=>'Wikidata: 語言管理員',
);

$wdMessages['yue'] = $wdMessages['zh-hant'];
$wdMessages['zh'] = $wdMessages['zh-hans'];
$wdMessages['zh-cn'] = $wdMessages['zh-hans'];
$wdMessages['zh-hk'] = $wdMessages['zh-hant'];
$wdMessages['zh-sg'] = $wdMessages['zh-hans'];
$wdMessages['zh-tw'] = $wdMessages['zh-hant'];
$wdMessages['zh-yue'] = $wdMessages['yue'];
