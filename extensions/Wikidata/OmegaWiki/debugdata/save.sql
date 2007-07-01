Start request
POST /wikidata/index.php?title=DefinedMeaning:UnitTest_%28663665%29&action=edit&dataset=uw
Host: thex:8080
User-Agent: Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.1) Gecko/20070123 BonEcho/2.0.0.1
Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5
Accept-Language: en-us,en;q=0.5
Accept-Encoding: gzip,deflate
Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7
Keep-Alive: 300
Connection: keep-alive
Referer: http://thex:8080/wikidata/index.php?title=DefinedMeaning:UnitTest_%28663665%29&action=edit&dataset=uw
Cookie: omegawikiUserName=Admin; omegawikiUserID=1; omegawiki_session=e902bafbfd2ba98187cbc3f0d5bd5f8d
Content-Type: application/x-www-form-urlencoded
Content-Length: 4999

Main cache: FakeMemCachedClient
Message cache: MediaWikiBagOStuff
Parser cache: MediaWikiBagOStuff
Unstubbing $wgMessageCache on call of $wgMessageCache->addMessages from initializeWikidata
Unstubbing $wgLoadBalancer on call of $wgLoadBalancer->getConnection from wfGetDB
SQL: BEGIN
SQL: SELECT /* Database::select */  set_prefix  FROM `wikidata_sets`  
SQL: select * from wikidata_sets where set_prefix='uw'
Unstubbing $wgUser on call of $wgUser->getOption from DataSet::fetchName
Cache miss for user 1
SQL: SELECT /* User::loadFromDatabase */  *  FROM `user`  WHERE user_id = '1'   LIMIT 1 
SQL: SELECT /* User::loadFromDatabase */  ug_group  FROM `user_groups`  WHERE ug_user = '1' 
Logged in from session
SQL: select language_id,wikimedia_key from language
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and language_id='85' and uw_expression_ns.remove_transaction_id is NULL
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and language_id='85' and uw_expression_ns.remove_transaction_id is NULL
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.remove_transaction_id is NULL LIMIT 1
Imported data set: OmegaWiki community
SQL: select * from wikidata_sets where set_prefix='tt'
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and language_id='85' and uw_expression_ns.remove_transaction_id is NULL
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and language_id='85' and uw_expression_ns.remove_transaction_id is NULL
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.remove_transaction_id is NULL LIMIT 1
Imported data set: test set
Unstubbing $wgLang on call of $wgLang->getCode from MessageCache::get
Unstubbing $wgContLang on call of $wgContLang->hasVariants from StubUserLang::_newObject
SQL: SELECT /* MediaWikiBagOStuff::_doquery */ value,exptime FROM `objectcache` WHERE keyname='omegawiki:messages-hash'
SQL: SELECT /* MediaWikiBagOStuff::_doquery */ value,exptime FROM `objectcache` WHERE keyname='omegawiki:messages'
MessageCache::load(): got from global cache
Unstubbing $wgParser on call of $wgParser->firstCallInit from MessageCache::transform
Language::loadLocalisation(): got localisation for en from source
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and language_id='85' and uw_expression_ns.remove_transaction_id is NULL
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and language_id='85' and uw_expression_ns.remove_transaction_id is NULL
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.remove_transaction_id is NULL LIMIT 1
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and language_id='85' and uw_expression_ns.remove_transaction_id is NULL
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and language_id='85' and uw_expression_ns.remove_transaction_id is NULL
SQL: select spelling from uw_syntrans,uw_expression_ns where uw_syntrans.defined_meaning_id='0' and uw_expression_ns.expression_id=uw_syntrans.expression_id and uw_expression_ns.remove_transaction_id is NULL LIMIT 1
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
Fully initialised
SQL: SELECT `namespace`.ns_id, ns_search_default, ns_subpages, ns_parent, ns_target, ns_system, ns_hidden, ns_count, ns_class, ns_name, ns_default, ns_canonical FROM `namespace` LEFT JOIN `namespace_names` ON (`namespace_names`.ns_id=`namespace`.ns_id) ORDER BY `namespace`.ns_id ASC
SQL: SELECT /* User::edits */  user_editcount  FROM `user`  WHERE user_id = '1'   LIMIT 1 
EditPage::edit: enter
Unstubbing $wgOut on call of $wgOut->setArticleFlag from EditPage::edit
EditPage::importFormData: Form data appears to be incomplete
POST DATA: array (
  'transaction' => '142037',
  'update-defined-meaning-663665-definition-translated-text-125-text' => 'eenheids testen is nie moelik nie',
  'update-defined-meaning-663665-definition-translated-text-89-text' => 'Eenheids testen zijn om eenheiden te testen',
  'update-defined-meaning-663665-definition-translated-text-85-text' => 'Unit testing is useful for testing units',
  'add-defined-meaning-663665-definition-translated-text-language' => '120',
  'add-defined-meaning-663665-definition-translated-text-text' => 'one wonders what gets saved',
  'add-defined-meaning-663665-definition-object-attributes-text-attribute-values-text-attribute' => '0',
  'attributesLevel' => 'DefinedMeaning',
  'attributesObjectId' => '663665',
  'add-defined-meaning-663665-definition-object-attributes-text-attribute-values-text' => '',
  'add-defined-meaning-663665-definition-object-attributes-translated-text-attribute-values-translated-text-attribute' => '0',
  'add-defined-meaning-663665-definition-object-attributes-translated-text-attribute-values-translated-text-value-language' => '0',
  'add-defined-meaning-663665-definition-object-attributes-translated-text-attribute-values-translated-text-value-text' => '',
  'add-defined-meaning-663665-definition-object-attributes-url-attribute-values-url-attribute' => '0',
  'add-defined-meaning-663665-definition-object-attributes-url-attribute-values-url' => '',
  'add-defined-meaning-663665-definition-object-attributes-option-attribute-values-option-attribute' => '0',
  'onUpdate' => 'updateSelectOptions(\'add-defined-meaning-663665-defined-meaning-attributes-option-attribute-values-option-attribute-option\',',
  'add-defined-meaning-663665-alternative-definitions-alternative-definition-language' => '0',
  'add-defined-meaning-663665-alternative-definitions-alternative-definition-text' => '',
  'add-defined-meaning-663665-alternative-definitions-source-id' => '0',
  'update-defined-meaning-663665-synonyms-translations-663666-indentical-meaning' => 'on',
  'add-defined-meaning-663665-synonyms-translations-663666-object-attributes-text-attribute-values-text-attribute' => '0',
  'add-defined-meaning-663665-synonyms-translations-663666-object-attributes-text-attribute-values-text' => '',
  'add-defined-meaning-663665-synonyms-translations-663666-object-attributes-translated-text-attribute-values-translated-text-attribute' => '0',
  'add-defined-meaning-663665-synonyms-translations-663666-object-attributes-translated-text-attribute-values-translated-text-value-language' => '0',
  'add-defined-meaning-663665-synonyms-translations-663666-object-attributes-translated-text-attribute-values-translated-text-value-text' => '',
  'add-defined-meaning-663665-synonyms-translations-663666-object-attributes-url-attribute-values-url-attribute' => '0',
  'add-defined-meaning-663665-synonyms-translations-663666-object-attributes-url-attribute-values-url' => '',
  'add-defined-meaning-663665-synonyms-translations-663666-object-attributes-option-attribute-values-option-attribute' => '0',
  'add-defined-meaning-663665-synonyms-translations-expression-language' => '0',
  'add-defined-meaning-663665-synonyms-translations-expression-spelling' => '',
  'add-defined-meaning-663665-synonyms-translations-indentical-meaning' => 'on',
  'add-defined-meaning-663665-relations-relation-type' => '0',
  'add-defined-meaning-663665-relations-other-defined-meaning' => '0',
  'add-defined-meaning-663665-class-membership-class' => '0',
  'add-defined-meaning-663665-collection-membership-collection-meaning' => '0',
  'add-defined-meaning-663665-collection-membership-source-identifier' => '',
  'add-defined-meaning-663665-defined-meaning-attributes-text-attribute-values-text-attribute' => '0',
  'add-defined-meaning-663665-defined-meaning-attributes-text-attribute-values-text' => '',
  'add-defined-meaning-663665-defined-meaning-attributes-translated-text-attribute-values-translated-text-attribute' => '0',
  'add-defined-meaning-663665-defined-meaning-attributes-translated-text-attribute-values-translated-text-value-language' => '0',
  'add-defined-meaning-663665-defined-meaning-attributes-translated-text-attribute-values-translated-text-value-text' => '',
  'add-defined-meaning-663665-defined-meaning-attributes-url-attribute-values-url-attribute' => '0',
  'add-defined-meaning-663665-defined-meaning-attributes-url-attribute-values-url' => '',
  'add-defined-meaning-663665-defined-meaning-attributes-option-attribute-values-option-attribute' => '0',
  'summary' => 'what gets saved',
  'save' => 'Save',
)
SQL: SELECT /* Title::getCascadeProtectionSources */  pr_expiry  FROM `templatelinks`,`page_restrictions`  WHERE tl_namespace = '24' AND tl_title = 'UnitTest_(663665)' AND (tl_from=pr_page) AND pr_cascade = '1' 
SQL: SELECT /* LinkCache::addLinkObj */  page_id  FROM `page`  WHERE page_namespace = '24' AND page_title = 'UnitTest_(663665)'   LIMIT 1 
SQL: SELECT /* Title::loadRestrictions */  *  FROM `page_restrictions`  WHERE pr_page = '651038' 
SQL: SELECT /* Title::loadRestrictionsFromRow */  page_restrictions  FROM `page`  WHERE page_id = '651038'   LIMIT 1 
EditPage::edit: Checking blocks
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
IP: 77.248.97.238
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: INSERT INTO uw_transactions (user_id, user_ip, timestamp, comment) VALUES (1, '77.248.97.238', 20070701160850, 'what gets saved')
definedMeaningId:663665, filterLanguageId:0, possiblySynonymousRelationTypeId:0, queryTransactionInformation:QueryTransactionInformation (...)
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=663665  AND  uw_defined_meaning.remove_transaction_id IS NULL 
SQL: SELECT language_id, text_id, uw_translated_content.add_transaction_id, uw_translated_content.remove_transaction_id, uw_translated_content.remove_transaction_id IS NULL AS is_live FROM uw_translated_content WHERE translated_content_id=663667 AND  uw_translated_content.add_transaction_id <= 142037 AND (uw_translated_content.remove_transaction_id > 142037 OR uw_translated_content.remove_transaction_id IS NULL) 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT text_id, text_text FROM uw_text WHERE text_id IN (204570, 204572, 204573)
SQL: SELECT value_id, object_id, attribute_mid, text, uw_text_attribute_values.add_transaction_id, uw_text_attribute_values.remove_transaction_id, uw_text_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_text_attribute_values WHERE object_id IN (663667) AND  uw_text_attribute_values.add_transaction_id <= 142037 AND (uw_text_attribute_values.remove_transaction_id > 142037 OR uw_text_attribute_values.remove_transaction_id IS NULL) 
SQL: SELECT value_id, object_id, attribute_mid, value_tcid, uw_translated_content_attribute_values.add_transaction_id, uw_translated_content_attribute_values.remove_transaction_id, uw_translated_content_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_translated_content_attribute_values WHERE object_id IN (663667) AND  uw_translated_content_attribute_values.add_transaction_id <= 142037 AND (uw_translated_content_attribute_values.remove_transaction_id > 142037 OR uw_translated_content
SQL: SELECT value_id, object_id, attribute_mid, url, uw_url_attribute_values.add_transaction_id, uw_url_attribute_values.remove_transaction_id, uw_url_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_url_attribute_values WHERE object_id IN (663667) AND  uw_url_attribute_values.add_transaction_id <= 142037 AND (uw_url_attribute_values.remove_transaction_id > 142037 OR uw_url_attribute_values.remove_transaction_id IS NULL) 
SQL: SELECT value_id, object_id, option_id, uw_option_attribute_values.add_transaction_id, uw_option_attribute_values.remove_transaction_id, uw_option_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_option_attribute_values WHERE object_id IN (663667) AND  uw_option_attribute_values.add_transaction_id <= 142037 AND (uw_option_attribute_values.remove_transaction_id > 142037 OR uw_option_attribute_values.remove_transaction_id IS NULL) 
SQL: SELECT object_id, level_mid, attribute_mid, attribute_type, uw_class_attributes.add_transaction_id, uw_class_attributes.remove_transaction_id, uw_class_attributes.remove_transaction_id IS NULL AS is_live FROM uw_class_attributes WHERE class_mid=663665 AND  uw_class_attributes.add_transaction_id <= 142037 AND (uw_class_attributes.remove_transaction_id > 142037 OR uw_class_attributes.remove_transaction_id IS NULL) 
SQL: SELECT meaning_text_tcid, source_id, uw_alt_meaningtexts.add_transaction_id, uw_alt_meaningtexts.remove_transaction_id, uw_alt_meaningtexts.remove_transaction_id IS NULL AS is_live FROM uw_alt_meaningtexts WHERE meaning_mid=663665 AND  uw_alt_meaningtexts.add_transaction_id <= 142037 AND (uw_alt_meaningtexts.remove_transaction_id > 142037 OR uw_alt_meaningtexts.remove_transaction_id IS NULL) 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT syntrans_sid, expression_id, identical_meaning, uw_syntrans.add_transaction_id, uw_syntrans.remove_transaction_id, uw_syntrans.remove_transaction_id IS NULL AS is_live FROM uw_syntrans WHERE defined_meaning_id=663665 AND  uw_syntrans.add_transaction_id <= 142037 AND (uw_syntrans.remove_transaction_id > 142037 OR uw_syntrans.remove_transaction_id IS NULL) 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT expression_id, language_id, spelling FROM uw_expression_ns WHERE expression_id IN (663664) AND  uw_expression_ns.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, attribute_mid, text, uw_text_attribute_values.add_transaction_id, uw_text_attribute_values.remove_transaction_id, uw_text_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_text_attribute_values WHERE object_id IN (663666) AND  uw_text_attribute_values.add_transaction_id <= 142037 AND (uw_text_attribute_values.remove_transaction_id > 142037 OR uw_text_attribute_values.remove_transaction_id IS NULL) 
SQL: SELECT value_id, object_id, attribute_mid, value_tcid, uw_translated_content_attribute_values.add_transaction_id, uw_translated_content_attribute_values.remove_transaction_id, uw_translated_content_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_translated_content_attribute_values WHERE object_id IN (663666) AND  uw_translated_content_attribute_values.add_transaction_id <= 142037 AND (uw_translated_content_attribute_values.remove_transaction_id > 142037 OR uw_translated_content
SQL: SELECT value_id, object_id, attribute_mid, url, uw_url_attribute_values.add_transaction_id, uw_url_attribute_values.remove_transaction_id, uw_url_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_url_attribute_values WHERE object_id IN (663666) AND  uw_url_attribute_values.add_transaction_id <= 142037 AND (uw_url_attribute_values.remove_transaction_id > 142037 OR uw_url_attribute_values.remove_transaction_id IS NULL) 
SQL: SELECT value_id, object_id, option_id, uw_option_attribute_values.add_transaction_id, uw_option_attribute_values.remove_transaction_id, uw_option_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_option_attribute_values WHERE object_id IN (663666) AND  uw_option_attribute_values.add_transaction_id <= 142037 AND (uw_option_attribute_values.remove_transaction_id > 142037 OR uw_option_attribute_values.remove_transaction_id IS NULL) 
SQL: SELECT relation_id, relationtype_mid, meaning2_mid, uw_meaning_relations.add_transaction_id, uw_meaning_relations.remove_transaction_id, uw_meaning_relations.remove_transaction_id IS NULL AS is_live FROM uw_meaning_relations WHERE meaning1_mid=663665 AND  uw_meaning_relations.add_transaction_id <= 142037 AND (uw_meaning_relations.remove_transaction_id > 142037 OR uw_meaning_relations.remove_transaction_id IS NULL)  ORDER BY add_transaction_id
SQL: SELECT relation_id, relationtype_mid, meaning1_mid, uw_meaning_relations.add_transaction_id, uw_meaning_relations.remove_transaction_id, uw_meaning_relations.remove_transaction_id IS NULL AS is_live FROM uw_meaning_relations WHERE meaning2_mid=663665 AND  uw_meaning_relations.add_transaction_id <= 142037 AND (uw_meaning_relations.remove_transaction_id > 142037 OR uw_meaning_relations.remove_transaction_id IS NULL)  ORDER BY relationtype_mid
SQL: SELECT class_membership_id, class_mid, uw_class_membership.add_transaction_id, uw_class_membership.remove_transaction_id, uw_class_membership.remove_transaction_id IS NULL AS is_live FROM uw_class_membership WHERE class_member_mid=663665 AND  uw_class_membership.add_transaction_id <= 142037 AND (uw_class_membership.remove_transaction_id > 142037 OR uw_class_membership.remove_transaction_id IS NULL) 
SQL: SELECT collection_id, internal_member_id, uw_collection_contents.add_transaction_id, uw_collection_contents.remove_transaction_id, uw_collection_contents.remove_transaction_id IS NULL AS is_live FROM uw_collection_contents WHERE member_mid=663665 AND  uw_collection_contents.add_transaction_id <= 142037 AND (uw_collection_contents.remove_transaction_id > 142037 OR uw_collection_contents.remove_transaction_id IS NULL) 
SQL: SELECT value_id, object_id, attribute_mid, text, uw_text_attribute_values.add_transaction_id, uw_text_attribute_values.remove_transaction_id, uw_text_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_text_attribute_values WHERE object_id IN (663665) AND  uw_text_attribute_values.add_transaction_id <= 142037 AND (uw_text_attribute_values.remove_transaction_id > 142037 OR uw_text_attribute_values.remove_transaction_id IS NULL) 
SQL: SELECT value_id, object_id, attribute_mid, value_tcid, uw_translated_content_attribute_values.add_transaction_id, uw_translated_content_attribute_values.remove_transaction_id, uw_translated_content_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_translated_content_attribute_values WHERE object_id IN (663665) AND  uw_translated_content_attribute_values.add_transaction_id <= 142037 AND (uw_translated_content_attribute_values.remove_transaction_id > 142037 OR uw_translated_content
SQL: SELECT value_id, object_id, attribute_mid, url, uw_url_attribute_values.add_transaction_id, uw_url_attribute_values.remove_transaction_id, uw_url_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_url_attribute_values WHERE object_id IN (663665) AND  uw_url_attribute_values.add_transaction_id <= 142037 AND (uw_url_attribute_values.remove_transaction_id > 142037 OR uw_url_attribute_values.remove_transaction_id IS NULL) 
SQL: SELECT value_id, object_id, option_id, uw_option_attribute_values.add_transaction_id, uw_option_attribute_values.remove_transaction_id, uw_option_attribute_values.remove_transaction_id IS NULL AS is_live FROM uw_option_attribute_values WHERE object_id IN (663665) AND  uw_option_attribute_values.add_transaction_id <= 142037 AND (uw_option_attribute_values.remove_transaction_id > 142037 OR uw_option_attribute_values.remove_transaction_id IS NULL) 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=663665  AND  uw_defined_meaning.remove_transaction_id IS NULL 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT translated_content_id FROM uw_translated_content WHERE translated_content_id=663667 AND language_id=120 AND  uw_translated_content.remove_transaction_id IS NULL 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: insert into uw_text(text_text) values('one wonders what gets saved')
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: insert into uw_translated_content(translated_content_id,language_id,text_id,add_transaction_id) values(663667, 120, 204574, 142038)
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=663665  AND  uw_defined_meaning.remove_transaction_id IS NULL 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=663665  AND  uw_defined_meaning.remove_transaction_id IS NULL 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=663665  AND  uw_defined_meaning.remove_transaction_id IS NULL 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=663665  AND  uw_defined_meaning.remove_transaction_id IS NULL 
SQL: UPDATE /* Title::touchArray */  `page` SET page_touched = '20070701160851' WHERE page_namespace = '24' AND page_title = 'UnitTest_(663665)'
SQL: INSERT /* RecentChange::save */  INTO `recentchanges` (rc_timestamp,rc_cur_time,rc_namespace,rc_title,rc_type,rc_minor,rc_cur_id,rc_user,rc_user_text,rc_comment,rc_this_oldid,rc_last_oldid,rc_bot,rc_moved_to_ns,rc_moved_to_title,rc_ip,rc_patrolled,rc_new,rc_old_len,rc_new_len,rc_id) VALUES ('20070701160851','20070701160851','24','UnitTest_(663665)','0','0','651038','1','Admin','what gets saved','0','0','0','0','','77.248.97.238','0','0','0','0',NULL)
SQL: SELECT /* UserMailer::notifyOnPageChange */  wl_user  FROM `watchlist`  WHERE wl_title = 'UnitTest_(663665)' AND wl_namespace = '24' AND (wl_user <> 1) AND (wl_notificationtimestamp IS NULL) 
SQL: UPDATE /* UserMailer::NotifyOnChange */  `watchlist` SET wl_notificationtimestamp = '20070701160851' WHERE wl_title = 'UnitTest_(663665)' AND wl_namespace = '24'
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT max(transaction_id) AS transaction_id FROM uw_transactions
definedMeaningId:663665, filterLanguageId:0, possiblySynonymousRelationTypeId:0, queryTransactionInformation:QueryTransactionInformation (...)
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=663665  AND  uw_defined_meaning.remove_transaction_id IS NULL 
SQL: SELECT language_id, text_id FROM uw_translated_content WHERE translated_content_id=663667 AND  uw_translated_content.remove_transaction_id IS NULL 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT text_id, text_text FROM uw_text WHERE text_id IN (204570, 204572, 204574, 204573)
SQL: SELECT value_id, object_id, attribute_mid, text FROM uw_text_attribute_values WHERE object_id IN (663667) AND  uw_text_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, attribute_mid, value_tcid FROM uw_translated_content_attribute_values WHERE object_id IN (663667) AND  uw_translated_content_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, attribute_mid, url FROM uw_url_attribute_values WHERE object_id IN (663667) AND  uw_url_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, option_id FROM uw_option_attribute_values WHERE object_id IN (663667) AND  uw_option_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT object_id, level_mid, attribute_mid, attribute_type FROM uw_class_attributes WHERE class_mid=663665 AND  uw_class_attributes.remove_transaction_id IS NULL 
SQL: SELECT meaning_text_tcid, source_id FROM uw_alt_meaningtexts WHERE meaning_mid=663665 AND  uw_alt_meaningtexts.remove_transaction_id IS NULL 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT syntrans_sid, expression_id, identical_meaning FROM uw_syntrans WHERE defined_meaning_id=663665 AND  uw_syntrans.remove_transaction_id IS NULL 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT expression_id, language_id, spelling FROM uw_expression_ns WHERE expression_id IN (663664) AND  uw_expression_ns.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, attribute_mid, text FROM uw_text_attribute_values WHERE object_id IN (663666) AND  uw_text_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, attribute_mid, value_tcid FROM uw_translated_content_attribute_values WHERE object_id IN (663666) AND  uw_translated_content_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, attribute_mid, url FROM uw_url_attribute_values WHERE object_id IN (663666) AND  uw_url_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, option_id FROM uw_option_attribute_values WHERE object_id IN (663666) AND  uw_option_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT relation_id, relationtype_mid, meaning2_mid FROM uw_meaning_relations WHERE meaning1_mid=663665 AND  uw_meaning_relations.remove_transaction_id IS NULL  ORDER BY add_transaction_id
SQL: SELECT relation_id, relationtype_mid, meaning1_mid FROM uw_meaning_relations WHERE meaning2_mid=663665 AND  uw_meaning_relations.remove_transaction_id IS NULL  ORDER BY relationtype_mid
SQL: SELECT class_membership_id, class_mid FROM uw_class_membership WHERE class_member_mid=663665 AND  uw_class_membership.remove_transaction_id IS NULL 
SQL: SELECT collection_id, internal_member_id FROM uw_collection_contents WHERE member_mid=663665 AND  uw_collection_contents.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, attribute_mid, text FROM uw_text_attribute_values WHERE object_id IN (663665) AND  uw_text_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, attribute_mid, value_tcid FROM uw_translated_content_attribute_values WHERE object_id IN (663665) AND  uw_translated_content_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, attribute_mid, url FROM uw_url_attribute_values WHERE object_id IN (663665) AND  uw_url_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT value_id, object_id, option_id FROM uw_option_attribute_values WHERE object_id IN (663665) AND  uw_option_attribute_values.remove_transaction_id IS NULL 
SQL: SELECT language.language_id AS row_id,language_names.language_name FROM language JOIN language_names ON language.language_id = language_names.language_id WHERE language_names.name_language_id = 85
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT uw_collection_ns.collection_id FROM (uw_collection_contents INNER JOIN uw_collection_ns ON uw_collection_ns.collection_id = uw_collection_contents.collection_id) WHERE uw_collection_contents.member_mid = 663665 AND uw_collection_ns.collection_type = 'CLAS' AND  uw_collection_contents.remove_transaction_id IS NULL  AND  uw_collection_ns.remove_transaction_id IS NULL 
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT /* Job::pop */  *  FROM `job`   ORDER BY job_id  LIMIT 1 
SQL: COMMIT
SQL: BEGIN
SQL: SELECT /* LinkBatch::doQuery */ page_id, page_namespace, page_title FROM `page` WHERE (page_namespace=2 AND page_title IN ('Admin')) OR (page_namespace=3 AND page_title IN ('Admin')) OR (page_namespace=25 AND page_title IN ('UnitTest_(663665)'))
SQL: SELECT /* Article::pageData */  page_id,page_namespace,page_title,page_restrictions,page_counter,page_is_redirect,page_is_new,page_random,page_touched,page_latest,page_len  FROM `page`  WHERE page_id = '651038'   LIMIT 1 
SQL: SELECT /* User::checkNewtalk */  user_id  FROM `user_newtalk`  WHERE user_id = '1'   LIMIT 1 
SQL: SELECT /* MediaWikiBagOStuff::_doquery */ value,exptime FROM `objectcache` WHERE keyname='omegawiki:sitenotice'
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT 1 FROM `uw_transactions` LIMIT 1
SQL: SELECT /* WatchedItem::isWatched */  1  FROM `watchlist`  WHERE wl_user = '1' AND wl_namespace = '24' AND wl_title = 'UnitTest_(663665)' 
OutputPage::sendCacheControl: private caching;  **
SQL: COMMIT
Request ended normally
