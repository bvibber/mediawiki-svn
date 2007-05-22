<?php

require_once('Transaction.php');
require_once('WikidataNamespaces.php');

require_once('Wikidata.php');
$wdDataSetContext=DefaultWikidataApplication::getDataSetContext();
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
		global
			$expressionNameSpaceId;
			
		return createPage($expressionNameSpaceId, getPageTitle($this->spelling));
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

	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT spelling, language_id " .
								" FROM {$dc}_expression_ns " .
								" WHERE {$dc}_expression_ns.expression_id=$expressionId".
								" AND " . getLatestTransactionRestriction("{$dc}_expression_ns"));
	$expressionRecord = $dbr->fetchObject($queryResult);
	$expression = new Expression($expressionId, $expressionRecord->spelling, $expressionRecord->language_id);
	return $expression; 
}

function newObjectId($table) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;

	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO {$dc}_objects (`table`, `UUID`) VALUES (". $dbr->addQuotes($table) . ", UUID())");
	
	return $dbr->insertId();
}

function getTableNameWithObjectId($objectId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;

	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT `table`" .
		" FROM {$dc}_objects" .
		" WHERE object_id=$objectId"
	);
	
	if ($objectRecord = $dbr->fetchObject($queryResult))
		return $objectRecord->table;
	else
		return "";
}

function getExpressionId($spelling, $languageId) {

	global $wdDataSetContext;
	$dc=$wdDataSetContext;

	$dbr = &wfGetDB(DB_SLAVE);
	$sql = "SELECT expression_id FROM {$dc}_expression_ns " .
			'WHERE spelling=binary '. $dbr->addQuotes($spelling) . ' AND language_id=' . $languageId .
			' AND '. getLatestTransactionRestriction("{$dc}_expression_ns");
	$queryResult = $dbr->query($sql);
	$expression = $dbr->fetchObject($queryResult);
	return $expression->expression_id;
}	

function createExpressionId($spelling, $languageId) {
	
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	
	$expressionId = newObjectId("{$dc}_expression_ns");

	$dbr = &wfGetDB(DB_MASTER);
	$spelling = $dbr->addQuotes($spelling);

	$dbr->query("INSERT INTO {$dc}_expression_ns(expression_id, spelling, language_id, add_transaction_id) values($expressionId, $spelling, $languageId, ". getUpdateTransactionId() .")");
	 
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
	$queryResult = $dbr->query("SELECT syntrans_sid FROM {$dc}_syntrans " .
								"WHERE defined_meaning_id=$definedMeaningId AND expression_id=$expressionId LIMIT 1");

	if ($synonym = $dbr->fetchObject($queryResult))
		return $synonym->syntrans_sid;
	else
		return 0;
}

function createSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning) {
	
	global $wdDataSetContext;
	$dc=$wdDataSetContext;

	$synonymId = getSynonymId($definedMeaningId, $expressionId);
	
	if ($synonymId == 0)
		$synonymId = newObjectId("{$dc}_syntrans");
	
	$dbr = &wfGetDB(DB_MASTER);
	$identicalMeaningInteger = (int) $identicalMeaning;	
	$sql = "insert into {$dc}_syntrans(syntrans_sid, defined_meaning_id, expression_id, identical_meaning, add_transaction_id) ".
	       "values($synonymId, $definedMeaningId, $expressionId, $identicalMeaningInteger, ". getUpdateTransactionId() .")";
	$queryResult = $dbr->query($sql);
}

function expressionIsBoundToDefinedMeaning($definedMeaningId, $expressionId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT expression_id FROM {$dc}_syntrans WHERE expression_id=$expressionId AND defined_meaning_id=$definedMeaningId AND ". getLatestTransactionRestriction("{$dc}_syntrans") ." LIMIT 1");
	
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
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT relation_id FROM {$dc}_meaning_relations " .
								"WHERE meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId LIMIT 1");

	if ($relation = $dbr->fetchObject($queryResult))
		return $relation->relation_id;
	else
		return 0;
}

function relationExists($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;

	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT meaning1_mid FROM {$dc}_meaning_relations " .
								"WHERE meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId " .
								"AND " . getLatestTransactionRestriction("{$dc}_meaning_relations"));
	
	return $dbr->numRows($queryResult) > 0;
}

function createRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;

	$relationId = getRelationId($definedMeaning1Id, $relationTypeId, $definedMeaning2Id);
	
	if ($relationId == 0)
		$relationId = newObjectId("{$dc}_meaning_relations");
		
	$dbr =& wfGetDB(DB_MASTER);
	$sql = "INSERT INTO {$dc}_meaning_relations(relation_id, meaning1_mid, meaning2_mid, relationtype_mid, add_transaction_id) " .
			" VALUES ($relationId, $definedMeaning1Id, $definedMeaning2Id, $relationTypeId, ". getUpdateTransactionId() .")";
	$dbr->query($sql);
}

function addRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	if (!relationExists($definedMeaning1Id, $relationTypeId, $definedMeaning2Id)) 
		createRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id);
}

function removeRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_meaning_relations SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId " .
				" AND remove_transaction_id IS NULL");
}

function removeRelationWithId($relationId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_meaning_relations SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE relation_id=$relationId " .
				" AND remove_transaction_id IS NULL");
}

function addClassAttribute($classMeaningId, $levelMeaningId, $attributeMeaningId, $attributeType) {
	if (!classAttributeExists($classMeaningId, $levelMeaningId, $attributeMeaningId, $attributeType)) 
		createClassAttribute($classMeaningId, $levelMeaningId, $attributeMeaningId, $attributeType);		
}

function classAttributeExists($classMeaningId, $levelMeaningId, $attributeMeaningId, $attributeType) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT object_id FROM {$dc}_class_attributes" .
								" WHERE class_mid=$classMeaningId AND level_mid=$levelMeaningId AND attribute_mid=$attributeMeaningId AND attribute_type = " . $dbr->addQuotes($attributeType) .
								' AND ' . getLatestTransactionRestriction("{$dc}_class_attributes"));
	
	return $dbr->numRows($queryResult) > 0;		
}

function createClassAttribute($classMeaningId, $levelMeaningId, $attributeMeaningId, $attributeType) {
	$objectId = getClassAttributeId($classMeaningId, $levelMeaningId, $attributeMeaningId, $attributeType);
	
	if ($objectId == 0)
		$objectId = newObjectId("{$dc}_class_attributes");
		
	$dbr =& wfGetDB(DB_MASTER);
	$sql = "INSERT INTO {$dc}_class_attributes(object_id, class_mid, level_mid, attribute_mid, attribute_type, add_transaction_id) " .
			" VALUES ($objectId, $classMeaningId, $levelMeaningId, $attributeMeaningId, " . $dbr->addQuotes($attributeType) . ', ' . getUpdateTransactionId() . ')';
	$dbr->query($sql);	
}

function getClassAttributeId($classMeaningId, $levelMeaningId, $attributeMeaningId, $attributeType) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT object_id FROM {$dc}_class_attributes " .
								"WHERE class_mid=$classMeaningId AND level_mid =$levelMeaningId AND attribute_mid=$attributeMeaningId AND attribute_type = " . $dbr->addQuotes($attributeType));

	if ($classAttribute = $dbr->fetchObject($queryResult))
		return $classAttribute->object_id;
	else
		return 0;
}

function removeClassAttributeWithId($classAttributeId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_class_attributes SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE object_id=$classAttributeId " .
				" AND remove_transaction_id IS NULL");
}			

function getClassMembershipId($classMemberId, $classId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT class_membership_id FROM {$dc}_class_membership " .
								"WHERE class_mid=$classId AND class_member_mid=$classMemberId LIMIT 1");

	if ($classMembership = $dbr->fetchObject($queryResult))
		return $classMembership->class_membership_id;
	else
		return 0;
}

function createClassMembership($classMemberId, $classId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$classMembershipId = getClassMembershipId($classMemberId, $classId);
	
	if ($classMembershipId == 0)
		$classMembershipId = newObjectId("{$dc}_class_membership");

	$dbr =& wfGetDB(DB_MASTER);
	$sql = "INSERT INTO {$dc}_class_membership(class_membership_id, class_mid, class_member_mid, add_transaction_id) " .
			"VALUES ($classMembershipId, $classId, $classMemberId, ". getUpdateTransactionId() .")";
	$dbr->query($sql);
}

function classMembershipExists($classMemberId, $classId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT class_member_mid FROM {$dc}_class_membership " .
								"WHERE class_mid=$classId AND class_member_mid=$classMemberId  " .
								"AND " . getLatestTransactionRestriction("{$dc}_class_membership"));
	
	return $dbr->numRows($queryResult) > 0;
}

function addClassMembership($classMemberId, $classId) {
	if (!classMembershipExists($classMemberId, $classId))
		createClassMembership($classMemberId, $classId);
}

function removeClassMembership($classMemberId, $classId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_class_membership SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE class_mid=$classId AND class_member_mid=$classMemberId " .
				" AND remove_transaction_id IS NULL");
}

function removeClassMembershipWithId($classMembershipId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;

	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_class_membership SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE class_membership_id=$classMembershipId" .
				" AND remove_transaction_id IS NULL");
}

function removeSynonymOrTranslation($definedMeaningId, $expressionId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_syntrans SET remove_transaction_id=". getUpdateTransactionId() . 
				" WHERE defined_meaning_id=$definedMeaningId AND expression_id=$expressionId AND remove_transaction_id IS NULL LIMIT 1");
}

function removeSynonymOrTranslationWithId($syntransId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_syntrans SET remove_transaction_id=". getUpdateTransactionId() . 
				" WHERE syntrans_sid=$syntransId AND remove_transaction_id IS NULL LIMIT 1");
}

function updateSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning) {
	removeSynonymOrTranslation($definedMeaningId, $expressionId);
	createSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning);
}

function updateSynonymOrTranslationWithId($syntransId, $identicalMeaning) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT defined_meaning_id, expression_id" .
								" FROM {$dc}_syntrans" .
								" WHERE syntrans_sid=$syntransId AND remove_transaction_id IS NULL");
				
	if ($syntrans = $dbr->fetchObject($queryResult)) 
		updateSynonymOrTranslation($syntrans->defined_meaning_id, $syntrans->expression_id, $identicalMeaning);
}

function updateDefinedMeaningDefinition($definedMeaningId, $languageId, $text) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId != 0)
		updateTranslatedText($definitionId, $languageId, $text);	
}

function updateOrAddDefinedMeaningDefinition($definedMeaningId, $languageId, $text) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId != 0)
		updateTranslatedText($definitionId, $languageId, $text);
	else
		addDefinedMeaningDefiningDefinition($definedMeaningId, $languageId, $text);	
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
	global $wdDataSetContext;
	$dc=$wdDataSetContext;

	$dbr = &wfGetDB(DB_MASTER);
	$sql = "insert into {$dc}_translated_content(translated_content_id,language_id,text_id,add_transaction_id) values($translatedContentId, $languageId, $textId, ". getUpdateTransactionId() .")";	
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function translatedTextExists($textId, $languageId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT translated_content_id" .
		" FROM {$dc}_translated_content" .
		" WHERE translated_content_id=$textId" .
		" AND language_id=$languageId" .
		" AND " . getLatestTransactionRestriction("{$dc}_translated_content")
	);

	return $dbr->numRows($queryResult) > 0;	
}

function addTranslatedText($translatedContentId, $languageId, $text) {
	$textId = createText($text);
	createTranslatedContent($translatedContentId, $languageId, $textId);
}

function addTranslatedTextIfNotPresent($translatedContentId, $languageId, $text) {
	if (!translatedTextExists($translatedContentId, $languageId)) 	
		addTranslatedText($translatedContentId, $languageId, $text);
}

function getDefinedMeaningDefinitionId($definedMeaningId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT meaning_text_tcid FROM {$dc}_defined_meaning WHERE defined_meaning_id=$definedMeaningId " .
								" AND " . getLatestTransactionRestriction("{$dc}_defined_meaning"));

	return $dbr->fetchObject($queryResult)->meaning_text_tcid;
}

function updateDefinedMeaningDefinitionId($definedMeaningId, $definitionId) {
	$dbr = &wfGetDB(DB_MASTER);
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr->query("UPDATE {$dc}_defined_meaning SET meaning_text_tcid=$definitionId WHERE defined_meaning_id=$definedMeaningId" .
				" AND " . getLatestTransactionRestriction("{$dc}_defined_meaning"));
}

function newTranslatedContentId() {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	return newObjectId("{$dc}_translated_content");
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
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO {$dc}_alt_meaningtexts (meaning_mid, meaning_text_tcid, source_id, add_transaction_id) " .
			    "VALUES ($definedMeaningId, $translatedContentId, $sourceMeaningId, " . getUpdateTransactionId() . ")");
}

function addDefinedMeaningAlternativeDefinition($definedMeaningId, $languageId, $text, $sourceMeaningId) {
	$translatedContentId = newTranslatedContentId();
	
	createDefinedMeaningAlternativeDefinition($definedMeaningId, $translatedContentId, $sourceMeaningId);
	addTranslatedText($translatedContentId, $languageId, $text);
}

function removeTranslatedText($translatedContentId, $languageId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_translated_content SET remove_transaction_id=". getUpdateTransactionId() . 
				" WHERE translated_content_id=$translatedContentId AND language_id=$languageId AND remove_transaction_id IS NULL");
}

function removeTranslatedTexts($translatedContentId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_translated_content SET remove_transaction_id=". getUpdateTransactionId() . 
				" WHERE translated_content_id=$translatedContentId AND remove_transaction_id IS NULL");
}

function removeDefinedMeaningAlternativeDefinition($definedMeaningId, $definitionId) {
	// Dilemma: 
	// Should we also remove the translated texts when removing an
	// alternative definition? There are pros and cons. For
	// now it is easier to not remove them so they can be rolled
	// back easier.      
//	removeTranslatedTexts($definitionId);

	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_alt_meaningtexts SET remove_transaction_id=" . getUpdateTransactionId() .
				" WHERE meaning_text_tcid=$definitionId AND meaning_mid=$definedMeaningId" .
				" AND remove_transaction_id IS NULL");
}

function removeDefinedMeaningDefinition($definedMeaningId, $languageId) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId != 0)
		removeTranslatedText($definitionId, $languageId);
}

function definedMeaningInCollection($definedMeaningId, $collectionId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_id FROM {$dc}_collection_contents WHERE collection_id=$collectionId AND member_mid=$definedMeaningId " .
								"AND ". getLatestTransactionRestriction("{$dc}_collection_contents"));
	
	return $dbr->numRows($queryResult) > 0;
}

function addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO {$dc}_collection_contents(collection_id, member_mid, internal_member_id, add_transaction_id) " .
					"VALUES ($collectionId, $definedMeaningId, ". $dbr->addQuotes($internalId) .", ". getUpdateTransactionId() .")");
}

function addDefinedMeaningToCollectionIfNotPresent($definedMeaningId, $collectionId, $internalId) {
	if (!definedMeaningInCollection($definedMeaningId, $collectionId))
		addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId);
}

function getDefinedMeaningFromCollection($collectionId, $internalMemberId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT member_mid FROM {$dc}_collection_contents WHERE collection_id=$collectionId AND internal_member_id=". $dbr->addQuotes($internalMemberId) .
								" AND " .getLatestTransactionRestriction("{$dc}_collection_contentsr"));
	
	if ($definedMeaningObject = $dbr->fetchObject($queryResult)) 
		return $definedMeaningObject->member_mid;
	else
		return null;
}

function removeDefinedMeaningFromCollection($definedMeaningId, $collectionId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_collection_contents SET remove_transaction_id=" . getUpdateTransactionId() .
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
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_mid FROM {$dc}_collection_ns " .
								" WHERE collection_id=$collectionId AND " . getLatestTransactionRestriction("{$dc}_collection_ns"));
	
	return $dbr->fetchObject($queryResult)->collection_mid;	
}

function getCollectionId($collectionMeaningId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT collection_id FROM {$dc}_collection_ns " .
								" WHERE collection_mid=$collectionMeaningId AND " . getLatestTransactionRestriction("{$dc}_collection_ns"));

	return $dbr->fetchObject($queryResult)->collection_id;	
}

function addCollection($definedMeaningId, $collectionType) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$collectionId = newObjectId("{$dc}_collection_ns");
	
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO {$dc}_collection_ns(collection_id, collection_mid, collection_type, add_transaction_id)" .
				" VALUES($collectionId, $definedMeaningId, '$collectionType', ". getUpdateTransactionId() .")");
	
	return $collectionId;	
}

function addDefinedMeaning($definingExpressionId) {
	global
		$definedMeaningNameSpaceId;
	
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	
	$definedMeaningId = newObjectId("{$dc}_defined_meaning"); 
	
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO {$dc}_defined_meaning(defined_meaning_id, expression_id, add_transaction_id) values($definedMeaningId, $definingExpressionId, ". getUpdateTransactionId() .")");

	$expression = getExpression($definingExpressionId);
	$pageId = createPage($definedMeaningNameSpaceId, getPageTitle("$expression->spelling ($definedMeaningId)"));
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
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$textValueAttributeId = newObjectId("{$dc}_text_attribute_values");
	createTextAttributeValue($textValueAttributeId, $objectId, $textAttributeId, $text);
}

function createTextAttributeValue($textValueAttributeId, $objectId, $textAttributeId, $text) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query(
		"INSERT INTO {$dc}_text_attribute_values (value_id, object_id, attribute_mid, text, add_transaction_id) " .
		"VALUES ($textValueAttributeId, $objectId, $textAttributeId, " . $dbr->addQuotes($text) . ", ". getUpdateTransactionId() .")"
	);	
}

function removeTextAttributeValue($textValueAttributeId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE {$dc}_text_attribute_values SET remove_transaction_id=". getUpdateTransactionId() .
				" WHERE value_id=$textValueAttributeId" .
				" AND remove_transaction_id IS NULL");	
}

function updateTextAttributeValue($text, $textValueAttributeId) {
	$textValueAttribute = getTextValueAttribute($textValueAttributeId);
	removeTextAttributeValue($textValueAttributeId);
	createTextAttributeValue($textValueAttributeId, $textValueAttribute->object_id, $textValueAttribute->attribute_mid, $text);
}

function getTextValueAttribute($textValueAttributeId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT object_id, attribute_mid, text" .
		" FROM {$dc}_text_attribute_values" .
		" WHERE value_id=$textValueAttributeId " .
		" AND " . getLatestTransactionRestriction("{$dc}_text_attribute_values")
	);

	return $dbr->fetchObject($queryResult);
}

function addURLAttributeValue($objectId, $urlAttributeId, $url) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$urlValueAttributeId = newObjectId("{$dc}_url_attribute_values");
	createURLAttributeValue($urlValueAttributeId, $objectId, $urlAttributeId, $url);
}

function createURLAttributeValue($urlValueAttributeId, $objectId, $urlAttributeId, $url) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query(
		"INSERT INTO {$dc}_url_attribute_values (value_id, object_id, attribute_mid, url, label, add_transaction_id) " .
		"VALUES ($urlValueAttributeId, $objectId, $urlAttributeId, " . $dbr->addQuotes($url) . ", " . $dbr->addQuotes($url) . ", ". getUpdateTransactionId() .")"
	);	
}

function removeURLAttributeValue($urlValueAttributeId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query(
		"UPDATE {$dc}_url_attribute_values SET remove_transaction_id=". getUpdateTransactionId() .
		" WHERE value_id=$urlValueAttributeId" .
		" AND remove_transaction_id IS NULL"
	);	
}

function updateURLAttributeValue($url, $urlValueAttributeId) {
	$urlValueAttribute = getURLValueAttribute($urlValueAttributeId);
	removeURLAttributeValue($urlValueAttributeId);
	createURLAttributeValue($urlValueAttributeId, $urlValueAttribute->object_id, $urlValueAttribute->attribute_mid, $url);
}

function getURLValueAttribute($urlValueAttributeId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query(
		"SELECT object_id, attribute_mid, url" .
		" FROM {$dc}_url_attribute_values WHERE value_id=$urlValueAttributeId " .
		" AND " . getLatestTransactionRestriction("{$dc}_url_attribute_values")
	);

	return $dbr->fetchObject($queryResult);
}

function createTranslatedTextAttributeValue($valueId, $objectId, $attributeId, $translatedContentId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO {$dc}_translated_content_attribute_values (value_id, object_id, attribute_mid, value_tcid, add_transaction_id) " .
			    "VALUES ($valueId, $objectId, $attributeId, $translatedContentId, ". getUpdateTransactionId() .")");
}

function addTranslatedTextAttributeValue($objectId, $attributeId, $languageId, $text) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$translatedTextValueAttributeId = newObjectId("{$dc}_translated_content_attribute_values");
	$translatedContentId = newTranslatedContentId();
	
	createTranslatedTextAttributeValue($translatedTextValueAttributeId, $objectId, $attributeId, $translatedContentId);
	addTranslatedText($translatedContentId, $languageId, $text);
}

function getTranslatedTextAttribute($valueId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT value_id, object_id, attribute_mid, value_tcid FROM {$dc}_translated_content_attribute_values WHERE value_id=$valueId " .
								" AND " . getLatestTransactionRestriction("{$dc}_translated_content_attribute_values"));

	return $dbr->fetchObject($queryResult);
}

function removeTranslatedTextAttributeValue($valueId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$translatedTextAttribute = getTranslatedTextAttribute($valueId);
	
	// Dilemma: 
	// Should we also remove the translated texts when removing a
	// translated content attribute? There are pros and cons. For
	// now it is easier to not remove them so they can be rolled
	// back easier.      
//	removeTranslatedTexts($translatedTextAttribute->value_tcid);  

	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query(
		"UPDATE {$dc}_translated_content_attribute_values" .
		" SET remove_transaction_id=". getUpdateTransactionId() .
		" WHERE value_id=$valueId" .
		" AND remove_transaction_id IS NULL"
	);
}

function optionAttributeValueExists($objectId, $optionId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDb(DB_SLAVE);
	$queryResult = $dbr->query("SELECT value_id FROM {$dc}_option_attribute_values" .
								' WHERE object_id = ' . $objectId .
								' AND option_id = ' . $optionId .
								' AND ' . getLatestTransactionRestriction("{$dc}_option_attribute_values"));
	return $dbr->numRows($queryResult) > 0;
}

function addOptionAttributeValue($objectId, $optionId) {
	if (!optionAttributeValueExists($objectId, $optionId))
		createOptionAttributeValue($objectId, $optionId);
}

function createOptionAttributeValue($objectId, $optionId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$valueId = newObjectId("{$dc}_option_attribute_values");

	$dbr =& wfGetDb(DB_MASTER);
	$sql = "INSERT INTO {$dc}_option_attribute_values(value_id,object_id,option_id,add_transaction_id)" .
			' VALUES(' . $valueId .
			',' . $objectId .
			',' . $optionId .
			',' . getUpdateTransactionId() . ')';
	$dbr->query($sql);
}

function removeOptionAttributeValue($valueId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_MASTER);
	$sql = "UPDATE {$dc}_option_attribute_values" .
			' SET remove_transaction_id = ' . getUpdateTransactionId() .
			' WHERE value_id = ' . $valueId .
			' AND ' . getLatestTransactionRestriction("{$dc}_option_attribute_values");
	$dbr->query($sql);
}

function optionAttributeOptionExists($attributeId, $optionMeaningId, $languageId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT option_id FROM {$dc}_option_attribute_options" .
								' WHERE attribute_id = ' . $attributeId .
								' AND option_mid = ' . $optionMeaningId .
								' AND language_id = ' . $languageId .
								' AND ' . getLatestTransactionRestriction("{$dc}_option_attribute_options"));
	return $dbr->numRows($queryResult) > 0;		
}

function addOptionAttributeOption($attributeId, $optionMeaningId, $languageId) {
	if (!optionAttributeOptionExists($attributeId, $optionMeaningId, $languageId))
		createOptionAttributeOption($attributeId, $optionMeaningId, $languageId);
}

function createOptionAttributeOption($attributeId, $optionMeaningId, $languageId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$optionId = newObjectId("{$dc}_option_attribute_options");

	$dbr =& wfGetDB(DB_MASTER);
	$sql = "INSERT INTO {$dc}_option_attribute_options(option_id,attribute_id,option_mid,language_id,add_transaction_id)" .
			' VALUES(' . $optionId .
			',' . $attributeId .
			',' . $optionMeaningId .
			',' . $languageId .
			',' . getUpdateTransactionId() . ')';
	$dbr->query($sql);
}

function removeOptionAttributeOption($optionId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_MASTER);
	$sql = "UPDATE {$dc}_option_attribute_options" .
			' SET remove_transaction_id = ' . getUpdateTransactionId() .
			' WHERE option_id = ' . $optionId .
			' AND ' . getLatestTransactionRestriction("{$dc}_option_attribute_options");
	$dbr->query($sql);
}

function getDefinedMeaningDefinitionForLanguage($definedMeaningId, $languageId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT old_text FROM {$dc}_defined_meaning as dm, {$dc}_translated_content as tc, text as t ".
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
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT old_text FROM {$dc}_defined_meaning as dm, {$dc}_translated_content as tc, text as t ".
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
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = & wfGetDB(DB_SLAVE);	
	$query = "SELECT {$dc}_collection_ns.collection_id " .
			 "FROM ({$dc}_collection_contents INNER JOIN {$dc}_collection_ns ON {$dc}_collection_ns.collection_id = {$dc}_collection_contents.collection_id) " .
			 "WHERE {$dc}_collection_contents.member_mid = $objectId AND {$dc}_collection_ns.collection_type = 'CLAS' " .
			 	"AND " . getLatestTransactionRestriction("{$dc}_collection_contents") . " ".
			 	"AND " .getLatestTransactionRestriction("{$dc}_collection_ns");
	$queryResult = $dbr->query($query);

	return $dbr->numRows($queryResult) > 0;	 
}

function findCollection($name) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = & wfGetDB(DB_SLAVE);
	$query = "SELECT collection_id, collection_mid, collection_type FROM {$dc}_collection_ns" .
			" WHERE ".getLatestTransactionRestriction("{$dc}_collection_ns") .
			" AND collection_mid = (SELECT defined_meaning_id FROM {$dc}_syntrans WHERE expression_id = " . 
             "(SELECT expression_id FROM {$dc}_expression_ns WHERE spelling LIKE " . $dbr->addQuotes($name) . " limit 1))";
	$queryResult = $dbr->query($query);
	
	if ($collectionObject = $dbr->fetchObject($queryResult)) 
		return $collectionObject->collection_id;
	else
		return null;             
}

function getCollectionContents($collectionId) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr = & wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT member_mid,internal_member_id from {$dc}_collection_contents " .
			                   "WHERE collection_id=$collectionId AND ". getLatestTransactionRestriction("{$dc}_collection_contents"));
	$collectionContents = array();			                   
	
	while ($collectionEntry = $dbr->fetchObject($queryResult)) 
		$collectionContents[$collectionEntry->internal_member_id] = $collectionEntry->member_mid;
	
	return $collectionContents;
} 

function getCollectionMemberId($collectionId, $sourceIdentifier) {
    global $wdDataSetContext;
    $dc=$wdDataSetContext;
    $dbr = & wfGetDB(DB_SLAVE);
    $queryResult = $dbr->query(
		"SELECT member_mid" .
		" FROM {$dc}_collection_contents " .
        " WHERE collection_id=$collectionId" .
        " AND internal_member_id=". $dbr->addQuotes($sourceIdentifier) .
        " AND " . getLatestTransactionRestriction("{$dc}_collection_contents")
    );

    if ($collectionEntry = $dbr->fetchObject($queryResult)) 
        return $collectionEntry->member_mid;
    else
        return 0;
} 

function getAnyDefinedMeaningWithSourceIdentifier($sourceIdentifier) {
    global $wdDataSetContext;
    $dc=$wdDataSetContext;
    $dbr = & wfGetDB(DB_SLAVE);
    $queryResult = $dbr->query(
		"SELECT member_mid" .
		" FROM {$dc}_collection_contents " .
        " WHERE internal_member_id=". $dbr->addQuotes($sourceIdentifier) .
        " AND " . getLatestTransactionRestriction("{$dc}_collection_contents") .
        " LIMIT 1"
    );

    if ($collectionEntry = $dbr->fetchObject($queryResult)) 
        return $collectionEntry->member_mid;
    else
        return 0;
}

function getExpressionMeaningIds($spelling) {
    global $wdDataSetContext;
    $dc=$wdDataSetContext;
    $dbr = & wfGetDB(DB_SLAVE);
    $queryResult = $dbr->query(
		"SELECT defined_meaning_id" .
		" FROM {$dc}_expression_ns, {$dc}_syntrans " .
        " WHERE spelling=". $dbr->addQuotes($spelling) .
        " AND {$dc}_expression_ns.expression_id={$dc}_syntrans.expression_id" .
        " AND " . getLatestTransactionRestriction("{$dc}_syntrans") .
        " AND " . getLatestTransactionRestriction("{$dc}_expression_ns")
    );

	$result = array();
	
	while ($synonymRecord = $dbr->fetchObject($queryResult)) 
        $result[] = $synonymRecord->defined_meaning_id;

	return $result;
}

?>
