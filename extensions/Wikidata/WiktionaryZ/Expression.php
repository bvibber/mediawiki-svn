<?php

require_once('Transaction.php');

class Expression {
	public $id;
	public $spelling;
	public $languageId;
	public $pageId;
	
	function __construct($id, $spelling, $languageId) {
		$this->id = $id;
		$this->spelling = $spelling;
		$this->languageId = $languageId;
	}
	
	function createNewInDatabase() {
		$this->pageId = $this->createPage();
		createInitialRevisionForPage($this->pageId, 'Created by adding expression');
	}
	
	function createPage() {
		return createPage(Namespace::getIndexForName("WiktionaryZ"), getPageTitle($this->spelling));
//		return createPage(16, getPageTitle($this->spelling));
	}
	
	function isBoundToDefinedMeaning($definedMeaningId) {
		return expressionIsBoundToDefinedMeaning($definedMeaningId, $this->id);
	}

	function bindToDefinedMeaning($definedMeaningId, $identicalMeaning) {
		createSynonymOrTranslation($definedMeaningId, $this->id, $identicalMeaning);	
	}
	
	function assureIsBoundToDefinedMeaning($definedMeaningId, $identicalMeaning) {
		if (!$this->isBoundToDefinedMeaning($definedMeaningId)) 
			$this->bindToDefinedMeaning($definedMeaningId, $identicalMeaning);		
	}
}

function getExpression($expressionId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT spelling, language_id " .
								" FROM uw_expression_ns " .
								" WHERE uw_expression_ns.expression_id=$expressionId".
								" AND " . getLatestTransactionRestriction('uw_expression_ns'));
	$expressionRecord = $dbr->fetchObject($queryResult);
	$expression = new Expression($expressionId, $expressionRecord->spelling, $expressionRecord->language_id);
	return $expression; 
}

function newObjectId($table) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO objects (`table`, `UUID`) VALUES (". $dbr->addQuotes($table) . ", UUID())");
	
	return $dbr->insertId();
}

function getExpressionId($spelling, $languageId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$sql = 'SELECT expression_id FROM uw_expression_ns ' .
			'WHERE spelling=binary '. $dbr->addQuotes($spelling) . ' AND language_id=' . $languageId .
			' AND '. getLatestTransactionRestriction('uw_expression_ns');
	$queryResult = $dbr->query($sql);
	$expression = $dbr->fetchObject($queryResult);
	return $expression->expression_id;
}	

function createExpressionId($spelling, $languageId) {
	$expressionId = newObjectId('uw_expression_ns');

	$dbr = &wfGetDB(DB_MASTER);
	$spelling = $dbr->addQuotes($spelling);

	$dbr->query("INSERT INTO uw_expression_ns(expression_id, spelling, language_id, add_transaction_id) values($expressionId, $spelling, $languageId, ". getUpdateTransactionId() .")");
	 
	return $expressionId;		
}

function getPageTitle($spelling) {
	return str_replace(' ', '_', $spelling);
}

function createPage($namespace, $title) {
	$dbr = &wfGetDB(DB_MASTER);
	$title = $dbr->addQuotes($title);
	$timestamp = $dbr->timestamp(); 
	
	$sql = "insert into page(page_namespace,page_title,page_is_new,page_touched) ".
	       "values($namespace, $title, 1, $timestamp)";
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function setPageLatestRevision($pageId, $latestRevision) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "update page set page_latest=$latestRevision where page_id=$pageId";	
	$dbr->query($sql);
}
function createInitialRevisionForPage($pageId, $comment) {
	global
		$wgUser;
		
	$dbr = &wfGetDB(DB_MASTER);
	$userId = $wgUser->getID();	
	$userName = $dbr->addQuotes($wgUser->getName());
	$comment = $dbr->addQuotes($comment);
	$timestamp = $dbr->timestamp();
	
	$sql = "insert into revision(rev_page,rev_comment,rev_user,rev_user_text,rev_timestamp) ".
	        "values($pageId, $comment, $userId, $userName, $timestamp)";
	$dbr->query($sql);

	$revisionId = $dbr->insertId();
	setPageLatestRevision($pageId, $revisionId); 
	
	return $revisionId;
}
	
function findExpression($spelling, $languageId) {
	if ($expressionId = getExpressionId($spelling, $languageId)) 
		return new Expression($expressionId, $spelling, $languageId);
	else
		return null;	
}

function createExpression($spelling, $languageId) {
	$expression = new Expression(createExpressionId($spelling, $languageId), $spelling, $languageId);
	$expression->createNewInDatabase();
	return $expression;
}

function findOrCreateExpression($spelling, $languageId) {
	if ($expression = findExpression($spelling, $languageId))
		return $expression;
	else
		return createExpression($spelling, $languageId);
}

function getSynonymId($definedMeaningId, $expressionId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT syntrans_sid FROM uw_syntrans " .
								"WHERE defined_meaning_id=$definedMeaningId AND expression_id=$expressionId LIMIT 1");

	if ($synonym = $dbr->fetchObject($queryResult))
		return $synonym->syntrans_sid;
	else
		return 0;
}

function createSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning) {
	$synonymId = getSynonymId($definedMeaningId, $expressionId);
	
	if ($synonymId == 0)
		$synonymId = newObjectId('uw_syntrans');
	
	$dbr = &wfGetDB(DB_MASTER);
	$identicalMeaningInteger = (int) $identicalMeaning;	
	$sql = "insert into uw_syntrans(syntrans_sid, defined_meaning_id, expression_id, identical_meaning, add_transaction_id) ".
	       "values($synonymId, $definedMeaningId, $expressionId, $identicalMeaningInteger, ". getUpdateTransactionId() .")";
	$queryResult = $dbr->query($sql);
}

function expressionIsBoundToDefinedMeaning($definedMeaningId, $expressionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT expression_id FROM uw_syntrans WHERE expression_id=$expressionId AND defined_meaning_id=$definedMeaningId AND ". getLatestTransactionRestriction('uw_syntrans') ." LIMIT 1");
	
	return $dbr->numRows($queryResult) > 0;
}

function addSynonymOrTranslation($spelling, $languageId, $definedMeaningId, $identicalMeaning) {
	$expression = findOrCreateExpression($spelling, $languageId);
	$expression->assureIsBoundToDefinedMeaning($definedMeaningId, $identicalMeaning);
}
	
function getMaximum($field, $table) {
	$dbr = &wfGetDB(DB_SLAVE);
	$sql = "select max($field) as maximum from $table";
	$queryResult = $dbr->query($sql);
	
	if ($maximum = $dbr->fetchObject($queryResult))
		return $maximum->maximum;
	else
		return 0;
}

function getRelationId($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT relation_id FROM uw_meaning_relations " .
								"WHERE meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId LIMIT 1");

	if ($relation = $dbr->fetchObject($queryResult))
		return $relation->relation_id;
	else
		return 0;
}

function relationExists($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT meaning1_mid FROM uw_meaning_relations " .
								"WHERE meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId " .
								"AND " . getLatestTransactionRestriction('uw_meaning_relations'));
	
	return $dbr->numRows($queryResult) > 0;
}

function createRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$relationId = getRelationId($definedMeaning1Id, $relationTypeId, $definedMeaning2Id);
	
	if ($relationId == 0)
		$relationId = newObjectId('uw_meaning_relations');
		
	$dbr =& wfGetDB(DB_MASTER);
	$sql = "INSERT INTO uw_meaning_relations(relation_id, meaning1_mid, meaning2_mid, relationtype_mid, add_transaction_id) " .
			" VALUES ($relationId, $definedMeaning1Id, $definedMeaning2Id, $relationTypeId, ". getUpdateTransactionId() .")";
	$dbr->query($sql);
}

function addRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	if (!relationExists($definedMeaning1Id, $relationTypeId, $definedMeaning2Id)) 
		createRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id);
}

function removeRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_meaning_relations SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId " .
				" AND remove_transaction_id IS NULL");
}

function removeRelationWithId($relationId) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_meaning_relations SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE relation_id=$relationId " .
				" AND remove_transaction_id IS NULL");
}

function addClassAttribute($classMeaningId, $attibuteMeaningId) {
	if (!classAttributeExists($classMeaningId, $attibuteMeaningId)) 
		createClassAttribute($classMeaningId, $attibuteMeaningId);		
}

function classAttributeExists($classMeaningId, $attibuteMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT object_id FROM uw_class_attributes " .
								"WHERE class_mid=$classMeaningId AND attribute_mid=$attibuteMeaningId " .
								"AND " . getLatestTransactionRestriction('uw_class_attributes'));
	
	return $dbr->numRows($queryResult) > 0;		
}

function createClassAttribute($classMeaningId, $attibuteMeaningId) {
	$objectId = getClassAttributeId($classMeaningId, $attibuteMeaningId);
	
	if ($objectId == 0)
		$objectId = newObjectId('uw_class_attributes');
		
	$dbr =& wfGetDB(DB_MASTER);
	$sql = "INSERT INTO uw_class_attributes(object_id, class_mid, attribute_mid, add_transaction_id) " .
			" VALUES ($objectId, $classMeaningId, $attibuteMeaningId, ". getUpdateTransactionId() .")";
	$dbr->query($sql);	
}

function getClassAttributeId($classMeaningId, $attibuteMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT object_id FROM uw_class_attributes " .
								"WHERE class_mid=$classMeaningId AND attribute_mid=$attibuteMeaningId");

	if ($classAttribute = $dbr->fetchObject($queryResult))
		return $classAttribute->object_id;
	else
		return 0;
}

function removeClassAttributeWithId($classAttributeId) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_class_attributes SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE object_id=$classAttributeId " .
				" AND remove_transaction_id IS NULL");
}			

function getClassMembershipId($classMemberId, $classId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT class_membership_id FROM uw_class_membership " .
								"WHERE class_mid=$classId AND class_member_mid=$classMemberId LIMIT 1");

	if ($classMembership = $dbr->fetchObject($queryResult))
		return $classMembership->class_membership_id;
	else
		return 0;
}

function createClassMembership($classMemberId, $classId) {
	$classMembershipId = getClassMembershipId($classMemberId, $classId);
	
	if ($classMembershipId == 0)
		$classMembershipId = newObjectId('uw_class_membership');

	$dbr =& wfGetDB(DB_MASTER);
	$sql = "INSERT INTO uw_class_membership(class_membership_id, class_mid, class_member_mid, add_transaction_id) " .
			"VALUES ($classMembershipId, $classId, $classMemberId, ". getUpdateTransactionId() .")";
	$dbr->query($sql);
}

function classMembershipExists($classMemberId, $classId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT class_member_mid FROM uw_class_membership " .
								"WHERE class_mid=$classId AND class_member_mid=$classMemberId  " .
								"AND " . getLatestTransactionRestriction('uw_class_membership'));
	
	return $dbr->numRows($queryResult) > 0;
}

function addClassMembership($classMemberId, $classId) {
	if (!classMembershipExists($classMemberId, $classId))
		createClassMembership($classMemberId, $classId);
}

function removeClassMembership($classMemberId, $classId) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_class_membership SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE class_mid=$classId AND class_member_mid=$classMemberId " .
				" AND remove_transaction_id IS NULL");
}

function removeClassMembershipWithId($classMembershipId) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_class_membership SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE class_membership_id=$classMembershipId" .
				" AND remove_transaction_id IS NULL");
}

function removeSynonymOrTranslation($definedMeaningId, $expressionId) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_syntrans SET remove_transaction_id=". getUpdateTransactionId() . 
				" WHERE defined_meaning_id=$definedMeaningId AND expression_id=$expressionId AND remove_transaction_id IS NULL LIMIT 1");
}

function removeSynonymOrTranslationWithId($syntransId) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_syntrans SET remove_transaction_id=". getUpdateTransactionId() . 
				" WHERE syntrans_sid=$syntransId AND remove_transaction_id IS NULL LIMIT 1");
}

function updateSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning) {
	removeSynonymOrTranslation($definedMeaningId, $expressionId);
	createSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning);
}

function updateSynonymOrTranslationWithId($syntransId, $identicalMeaning) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT defined_meaning_id, expression_id" .
								" FROM uw_syntrans" .
								" WHERE syntrans_sid=$syntransId AND remove_transaction_id IS NULL");
				
	if ($syntrans = $dbr->fetchObject($queryResult)) 
		updateSynonymOrTranslation($syntrans->defined_meaning_id, $syntrans->expression_id, $identicalMeaning);
}

function updateDefinedMeaningDefinition($definedMeaningId, $languageId, $text) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId != 0)
		updateTranslatedText($definitionId, $languageId, $text);	
}

function updateTranslatedText($setId, $languageId, $text) {
	removeTranslatedText($setId, $languageId);
	addTranslatedText($setId, $languageId, $text);
}
 
function createText($text) {
	$dbr = &wfGetDB(DB_MASTER);
	$text = $dbr->addQuotes($text);
	$sql = "insert into text(old_text) values($text)";	
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function createTranslatedContent($translatedContentId, $languageId, $textId) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "insert into translated_content(translated_content_id,language_id,text_id,add_transaction_id) values($translatedContentId, $languageId, $textId, ". getUpdateTransactionId() .")";	
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function translatedTextExists($definitionId, $languageId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT translated_content_id FROM translated_content WHERE translated_content_id=$definitionId AND language_id=$languageId AND " . getLatestTransactionRestriction('translated_content'));

	return $dbr->numRows($queryResult) > 0;	
}

function addTranslatedText($definitionId, $languageId, $definition) {
	$textId = createText($definition);
	createTranslatedContent($definitionId, $languageId, $textId);
}

function addTranslatedTextIfNotPresent($definitionId, $languageId, $definition) {
	if (!translatedTextExists($definitionId, $languageId)) 	
		addTranslatedText($definitionId, $languageId, $definition);
}

function getDefinedMeaningDefinitionId($definedMeaningId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=$definedMeaningId " .
								" AND " . getLatestTransactionRestriction('uw_defined_meaning'));

	return $dbr->fetchObject($queryResult)->meaning_text_tcid;
}

function updateDefinedMeaningDefinitionId($definedMeaningId, $definitionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_defined_meaning SET meaning_text_tcid=$definitionId WHERE defined_meaning_id=$definedMeaningId" .
				" AND " . getLatestTransactionRestriction('uw_defined_meaning'));
}

function newTranslatedContentId() {
	return newObjectId('translated_content');
}

function addDefinedMeaningDefiningDefinition($definedMeaningId, $languageId, $text) {
	$definitionId = newTranslatedContentId();		
	addTranslatedText($definitionId, $languageId, $text);
	updateDefinedMeaningDefinitionId($definedMeaningId, $definitionId);
}

function addDefinedMeaningDefinition($definedMeaningId, $languageId, $text) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId == 0)
		addDefinedMeaningDefiningDefinition($definedMeaningId, $languageId, $text);
	else 
		addTranslatedTextIfNotPresent($definitionId, $languageId, $text);
}

function createDefinedMeaningAlternativeDefinition($definedMeaningId, $translatedContentId, $sourceMeaningId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_alt_meaningtexts (meaning_mid, meaning_text_tcid, source_id, add_transaction_id) " .
			    "VALUES ($definedMeaningId, $translatedContentId, $sourceMeaningId, " . getUpdateTransactionId() . ")");
}

function addDefinedMeaningAlternativeDefinition($definedMeaningId, $languageId, $text, $sourceMeaningId) {
	$translatedContentId = newTranslatedContentId();
	
	createDefinedMeaningAlternativeDefinition($definedMeaningId, $translatedContentId, $sourceMeaningId);
	addTranslatedText($translatedContentId, $languageId, $text);
}

function removeTranslatedText($translatedContentId, $languageId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE translated_content SET remove_transaction_id=". getUpdateTransactionId() . 
				" WHERE translated_content_id=$translatedContentId AND language_id=$languageId AND remove_transaction_id IS NULL");
}

function removeTranslatedTexts($translatedContentId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE translated_content SET remove_transaction_id=". getUpdateTransactionId() . 
				" WHERE translated_content_id=$translatedContentId AND remove_transaction_id IS NULL");
}

function removeDefinedMeaningAlternativeDefinition($definedMeaningId, $definitionId) {
	removeTranslatedTexts($definitionId);

	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_alt_meaningtexts SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE meaning_text_tcid=$definitionId AND meaning_mid=$definedMeaningId" .
				" AND remove_transaction_id IS NULL");
}

function removeDefinedMeaningDefinition($definedMeaningId, $languageId) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId != 0)
		removeTranslatedText($definitionId, $languageId);
}

function definedMeaningInCollection($definedMeaningId, $collectionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_id FROM uw_collection_contents WHERE collection_id=$collectionId AND member_mid=$definedMeaningId " .
								"AND ". getLatestTransactionRestriction('uw_collection_contents'));
	
	return $dbr->numRows($queryResult) > 0;
}

function addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_collection_contents(collection_id, member_mid, internal_member_id, add_transaction_id) " .
					"VALUES ($collectionId, $definedMeaningId, ". $dbr->addQuotes($internalId) .", ". getUpdateTransactionId() .")");
}

function addDefinedMeaningToCollectionIfNotPresent($definedMeaningId, $collectionId, $internalId) {
	if (!definedMeaningInCollection($definedMeaningId, $collectionId))
		addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId);
}

function getDefinedMeaningFromCollection($collectionId, $internalMemberId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT member_mid FROM uw_collection_contents WHERE collection_id=$collectionId AND internal_member_id=". $dbr->addQuotes($internalMemberId) .
								" AND " .getLatestTransactionRestriction('uw_collection_contents'));
	
	if ($definedMeaningObject = $dbr->fetchObject($queryResult)) 
		return $definedMeaningObject->member_mid;
	else
		return null;
}

function removeDefinedMeaningFromCollection($definedMeaningId, $collectionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_collection_contents SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE collection_id=$collectionId AND member_mid=$definedMeaningId AND remove_transaction_id IS NULL");	
}

function updateDefinedMeaningInCollection($definedMeaningId, $collectionId, $internalId) {
	removeDefinedMeaningFromCollection($definedMeaningId, $collectionId);
	addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId);	
}

function bootstrapCollection($collection, $languageId, $collectionType){
	$expression = findOrCreateExpression($collection, $languageId);
	$definedMeaningId = addDefinedMeaning($expression->id);
	$expression->assureIsBoundToDefinedMeaning($definedMeaningId, true);
	addDefinedMeaningDefinition($definedMeaningId, $languageId, $collection);
	return addCollection($definedMeaningId, $collectionType);
}

function getCollectionMeaningId($collectionId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_mid FROM uw_collection_ns " .
								" WHERE collection_id=$collectionId AND " . getLatestTransactionRestriction('uw_collection_ns'));
	
	return $dbr->fetchObject($queryResult)->collection_mid;	
}

function getCollectionId($collectionMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_id FROM uw_collection_ns " .
								" WHERE collection_mid=$collectionMeaningId AND " . getLatestTransactionRestriction('uw_collection_ns'));

	return $dbr->fetchObject($queryResult)->collection_id;	
}

function addCollection($definedMeaningId, $collectionType) {
	$collectionId = newObjectId('uw_collection_ns');
	
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_collection_ns(collection_id, collection_mid, collection_type, add_transaction_id)" .
				" VALUES($collectionId, $definedMeaningId, '$collectionType', ". getUpdateTransactionId() .")");
	
	return $collectionId;	
}

function addDefinedMeaning($definingExpressionId) {
	$definedMeaningId = newObjectId('uw_defined_meaning'); 
	
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_defined_meaning(defined_meaning_id, expression_id, add_transaction_id) values($definedMeaningId, $definingExpressionId, ". getUpdateTransactionId() .")");

	$expression = getExpression($definingExpressionId);
	$pageId = createPage(Namespace::getIndexForName("DefinedMeaning"), getPageTitle("$expression->spelling ($definedMeaningId)"));
//	$pageId = createPage(22, getPageTitle("$expression->spelling ($definedMeaningId)"));
	createInitialRevisionForPage($pageId, 'Created by adding defined meaning');
	
	return $definedMeaningId;
}

function createNewDefinedMeaning($definingExpressionId, $languageId, $text) {
	$definedMeaningId = addDefinedMeaning($definingExpressionId);
	createSynonymOrTranslation($definedMeaningId, $definingExpressionId, true);
	addDefinedMeaningDefiningDefinition($definedMeaningId, $languageId, $text);
	
	return $definedMeaningId;
}

function addTextAttributeValue($objectId, $textAttributeId, $text) {
	$textValueAttributeId = newObjectId('uw_text_attribute_values');
	createTextAttributeValue($objectId, $textAttributeId, $text, $textValueAttributeId);
}

function createTextAttributeValue($objectId, $textAttributeId, $text, $textValueAttributeId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_text_attribute_values (value_id, object_id, attribute_mid, text, add_transaction_id) " .
			    "VALUES ($textValueAttributeId, $objectId, $textAttributeId, '$text', ". getUpdateTransactionId() .")");	
}

function removeTextAttributeValue($textValueAttributeId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_text_attribute_values SET remove_transaction_id=". getUpdateTransactionId() .
				" WHERE value_id=$textValueAttributeId" .
				" AND remove_transaction_id IS NULL");	
}

function updateTextAttributeValue($text, $textValueAttributeId) {
	$textValueAttribute = getTextValueAttribute($textValueAttributeId);
	removeTextAttributeValue($textValueAttributeId);
	createTextAttributeValue($textValueAttribute->object_id, $textValueAttribute->attribute_mid, $text, $textValueAttributeId);
}

function getTextValueAttribute($textValueAttributeId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT object_id, attribute_mid, text FROM uw_text_attribute_values WHERE value_id=$textValueAttributeId " .
								" AND " . getLatestTransactionRestriction('uw_text_attribute_values'));

	return $dbr->fetchObject($queryResult);
}

function createTranslatedTextAttributeValue($valueId, $objectId, $attributeId, $translatedContentId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_translated_content_attribute_values (value_id, object_id, attribute_mid, value_tcid, add_transaction_id) " .
			    "VALUES ($valueId, $objectId, $attributeId, $translatedContentId, ". getUpdateTransactionId() .")");
}

function addTranslatedTextAttributeValue($objectId, $attributeId, $languageId, $text) {
	$translatedTextValueAttributeId = newObjectId('uw_translated_content_attribute_values');
	$translatedContentId = newTranslatedContentId();
	
	createTranslatedTextAttributeValue($translatedTextValueAttributeId, $objectId, $attributeId, $translatedContentId);
	addTranslatedText($translatedContentId, $languageId, $text);
}

function removeTranslatedTextAttributeValue($textId) {
	removeTranslatedTexts($textId);

	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_translated_content_attribute_values SET remove_transaction_id=". getUpdateTransactionId() .
				" WHERE value_tcid=$textId" .
				" AND remove_transaction_id IS NULL");
}

function getDefinedMeaningDefinitionForLanguage($definedMeaningId, $languageId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT old_text FROM uw_defined_meaning as dm, translated_content as tc, text as t ".
								"WHERE dm.defined_meaning_id=$definedMeaningId " .
								" AND " . getLatestTransactionRestriction('dm') .
								" AND " . getLatestTransactionRestriction('tc') .
								" AND  dm.meaning_text_tcid=tc.translated_content_id AND tc.language_id=$languageId " .
								" AND  t.old_id=tc.text_id");	
	
	if ($definition = $dbr->fetchObject($queryResult)) 
		return $definition->old_text;
	else	
		return "";
}

function getDefinedMeaningDefinitionForAnyLanguage($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT old_text FROM uw_defined_meaning as dm, translated_content as tc, text as t ".
								"WHERE dm.defined_meaning_id=$definedMeaningId " .
								" AND " . getLatestTransactionRestriction('dm') .
								" AND " . getLatestTransactionRestriction('tc') .
								" AND dm.meaning_text_tcid=tc.translated_content_id " .
								" AND t.old_id=tc.text_id LIMIT 1");	
	
	if ($definition = $dbr->fetchObject($queryResult)) 
		return $definition->old_text;
	else	
		return "";
}

function getDefinedMeaningDefinition($definedMeaningId) {
	global
		$wgUser;
		
	$userLanguageId = getLanguageIdForCode($wgUser->getOption('language'));
	
	if ($userLanguageId > 0)
		$result = getDefinedMeaningDefinitionForLanguage($definedMeaningId, $userLanguageId);
	else
		$result = "";
	
	if ($result == "") {
		$result = getDefinedMeaningDefinitionForLanguage($definedMeaningId, 85);
		
		if ($result == "")
			$result = getDefinedMeaningDefinitionForAnyLanguage($definedMeaningId);
	}
	
	return $result;
}

function isClass($objectId) {
	$dbr = & wfGetDB(DB_SLAVE);	
	$query = "SELECT uw_collection_ns.collection_id " .
			 "FROM (uw_collection_contents INNER JOIN uw_collection_ns ON uw_collection_ns.collection_id = uw_collection_contents.collection_id) " .
			 "WHERE uw_collection_contents.member_mid = $objectId AND uw_collection_ns.collection_type = 'CLAS' " .
			 	"AND " . getLatestTransactionRestriction('uw_collection_contents') . " ".
			 	"AND " .getLatestTransactionRestriction('uw_collection_ns');
	$queryResult = $dbr->query($query);

	return $dbr->numRows($queryResult) > 0;	 
}

function findCollection($name) {
	$dbr = & wfGetDB(DB_SLAVE);
	$query = "SELECT collection_id, collection_mid, collection_type FROM uw_collection_ns" .
			" WHERE ".getLatestTransactionRestriction('uw_collection_ns') .
			" AND collection_mid = (SELECT defined_meaning_id FROM uw_syntrans WHERE expression_id = " . 
             "(SELECT expression_id FROM uw_expression_ns WHERE spelling LIKE " . $dbr->addQuotes($name) . " limit 1))";
	$queryResult = $dbr->query($query);
	
	if ($collectionObject = $dbr->fetchObject($queryResult)) 
		return $collectionObject->collection_id;
	else
		return null;             
}

function getCollectionContents($collectionId) {
	$dbr = & wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT member_mid,internal_member_id from uw_collection_contents " .
			                   "WHERE collection_id=$collectionId AND ". getLatestTransactionRestriction('uw_collection_contents'));
	$collectionContents = array();			                   
	
	while ($collectionEntry = $dbr->fetchObject($queryResult)) 
		$collectionContents[$collectionEntry->internal_member_id] = $collectionEntry->member_mid;
	
	return $collectionContents;
} 

function getCollectionMemberId($collectionId, $sourceIdentifier) {
    $dbr = & wfGetDB(DB_SLAVE);
    $queryResult = $dbr->query("SELECT member_mid from uw_collection_contents " .
                               "WHERE collection_id=$collectionId AND internal_member_id=". $dbr->addQuotes($sourceIdentifier));

    if ($collectionEntry = $dbr->fetchObject($queryResult)) 
        return $collectionEntry->member_mid;
    else
        return null;
} 

?>