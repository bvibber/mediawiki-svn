<?php

class Expression {
	public $id;
	public $spelling;
	public $languageId;
	public $pageId;
	public $revisionId;
	
	function __construct($id, $spelling, $languageId) {
		$this->id = $id;
		$this->spelling = $spelling;
		$this->languageId = $languageId;
	}
	
	function getPageTitle() {
		return str_replace(' ', '_', $this->spelling);
	}
	
	function updateFromDatabase() {
		$this->revisionId = getRevisionForExpressionId($this->id);
	}
	
	function createNewInDatabase() {
		$this->pageId = $this->createPage();
		$this->revisionId = createInitialRevisionForPage($this->pageId, 'Created by adding expression');
		
		linkExpressionToRevision($this->id, $this->revisionId);
	}
	
	function createPage() {
		return createPage(16, $this->getPageTitle(), $this->languageId);
	}
	
	function isBoundToDefinedMeaning($definedMeaningId) {
		return getSetIdForDefinedMeaningAndExpression($definedMeaningId, $this->id);
	}

	function bindToDefinedMeaning($definedMeaningId, $endemicMeaning) {
		$setId = determineSetIdForDefinedMeaning($definedMeaningId);
		createSynonymOrTranslation($setId, $definedMeaningId, $this->id, $this->revisionId, $endemicMeaning);	
	}
	
	function assureIsBoundToDefinedMeaning($definedMeaningId, $endemicMeaning) {
		if (!$this->isBoundToDefinedMeaning($definedMeaningId)) 
			$this->bindToDefinedMeaning($definedMeaningId, $endemicMeaning);		
	}
}

function getExpressionId($spelling, $languageId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$sql = 'select expression_id from uw_expression_ns where spelling=binary '. $dbr->addQuotes($spelling) . ' and language_id=' . $languageId . ' and is_latest=1';
	$queryResult = $dbr->query($sql);
	$expression = $dbr->fetchObject($queryResult);
	return $expression->expression_id;
}	

function setFirstVersion($expressionId, $firstVersionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "update uw_expression_ns set first_ver=$firstVersionId where expression_id=$expressionId";
	$dbr->query($sql);
}
	
function createExpressionId($spelling, $languageId) {
	$dbr = &wfGetDB(DB_MASTER);
	$spelling = $dbr->addQuotes($spelling);
	$sql = "insert into uw_expression_ns(spelling,language_id,is_latest) values($spelling, $languageId, 1)";
	$dbr->query($sql);
	$expressionId = $dbr->insertId();
	
	setFirstVersion($expressionId, $expressionId);
	 
	return $expressionId;		
}

function getRevisionForExpressionId($expressionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$sql = "select rev_id from revision where rev_data_id=$expressionId";
	$queryResult = $dbr->query($sql);
	
	if ($revision = $dbr->fetchObject($queryResult))
		return $revision->rev_id;
	else
		return null;
}

function createPage($namespace, $title, $languageId) {
	$dbr = &wfGetDB(DB_MASTER);
	$title = $dbr->addQuotes($title);
	$timestamp = $dbr->timestamp(); 
	
	$sql = "insert into page(page_namespace,page_title,page_is_new,page_title_language_id,page_touched) ".
	       "values($namespace, $title, 1, $languageId, $timestamp)";
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
	
function linkExpressionToRevision($expressionId, $revisionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "update revision set rev_data_id=$expressionId where rev_id=$revisionId";
	$dbr->query($sql);	
}

function findExpression($spelling, $languageId) {
	if ($expressionId = getExpressionId($spelling, $languageId)) {
		$expression = new Expression($expressionId, $spelling, $languageId);
		$expression->updateFromDatabase();  
		return $expression;
	}
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

function getSetIdForDefinedMeaningAndExpression($definedMeaningId, $expressionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$sql = "select set_id from uw_syntrans where defined_meaning_id=$definedMeaningId and expression_id=$expressionId";
	$queryResult = $dbr->query($sql);
	
	if ($set = $dbr->fetchObject($queryResult))
		return $set->set_id;
	else
		return 0;	
}

function getLatestSetIdForDefinedMeaning($definedMeaningId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$sql = "select set_id from uw_syntrans where defined_meaning_id=$definedMeaningId and is_latest_set=1";
	$queryResult = $dbr->query($sql);
	
	if ($set = $dbr->fetchObject($queryResult))
		return $set->set_id;
	else
		return 0;	
}

function newSetIdForDefinedMeaning() {
	return getMaximum('set_id', 'uw_syntrans') + 1;
}

function determineSetIdForDefinedMeaning($definedMeaningId) {
	$result = getLatestSetIdForDefinedMeaning($definedMeaningId);
	
	if ($result == 0)
		$result = newSetIdForDefinedMeaning();
		
	return $result;
}

function createSynonymOrTranslation($setId, $definedMeaningId, $expressionId, $revisionId, $endemicMeaning) {
	$dbr = &wfGetDB(DB_MASTER);
	$endemicMeaningInteger = (int) $endemicMeaning;	
	$sql = "insert into uw_syntrans(set_id,defined_meaning_id,expression_id,first_set,revision_id,endemic_meaning,is_latest_set) ".
	       "values($setId, $definedMeaningId, $expressionId, $setId, $revisionId, $endemicMeaningInteger, 1)";
	$queryResult = $dbr->query($sql);
}

function addSynonymOrTranslation($spelling, $languageId, $definedMeaningId, $endemicMeaning) {
	$expression = findOrCreateExpression($spelling, $languageId);
	$expression->assureIsBoundToDefinedMeaning($definedMeaningId, $endemicMeaning);
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

function getLatestRevisionForDefinedMeaning($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$sql = "SELECT revision_id from uw_defined_meaning where defined_meaning_id=$definedMeaningId and is_latest_ver=1 limit 1";
	$queryResult = $dbr->query($sql);
	
	return $dbr->fetchObject($queryResult)->revision_id;
}
	
function relationExists($setId, $definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT * FROM uw_meaning_relations WHERE set_id=$setId AND meaning1_mid=$definedMeaning1Id AND meaning2_mid=$definedMeaning2Id AND relationtype_mid=$relationTypeId AND is_latest_set=1");
	
	return $dbr->numRows($queryResult) > 0;
}

function getSetIdForDefinedMeaningRelations($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$sql = "SELECT set_id from uw_meaning_relations where meaning1_mid=$definedMeaningId and is_latest_set=1 limit 1";
	$queryResult = $dbr->query($sql);
			
	$setId = $dbr->fetchObject($queryResult)->set_id;
	
	if (!$setId) {
		$sql = "SELECT max(set_id) as max_id from uw_meaning_relations";
		$queryResult = $dbr->query($sql);
		$setId = $dbr->fetchObject($queryResult)->max_id + 1;
	}
	
	return $setId;		
}

function addRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$setId = getSetIdForDefinedMeaningRelations($definedMeaning1Id);
	$revisionId = getLatestRevisionForDefinedMeaning($definedMeaning1Id);
	
	if (!relationExists($setId, $definedMeaning1Id, $relationTypeId, $definedMeaning2Id)) {
		$dbr =& wfGetDB(DB_MASTER);
		$sql = "insert into uw_meaning_relations(set_id, meaning1_mid, meaning2_mid, relationtype_mid, is_latest_set, first_set, revision_id) " .
				"values($setId, $definedMeaning1Id, $definedMeaning2Id, $relationTypeId, 1, $setId, $revisionId)";
		$dbr->query($sql);
	}
}

function removeRelation($definedMeaning1Id, $relationTypeId, $definedMeaning2Id) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("delete from uw_meaning_relations where meaning1_mid=$definedMeaning1Id and meaning2_mid=$definedMeaning2Id and ".
				"relationtype_mid=$relationTypeId AND is_latest_set=1 LIMIT 1");
}

function removeSynonymOrTranslation($definedMeaningId, $expressionId) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("delete from uw_syntrans where defined_meaning_id=$definedMeaningId and expression_id=$expressionId and ".
				"is_latest_set=1 LIMIT 1");
}

function updateSynonymOrTranslation($definedMeaningId, $expressionId, $identicalMeaning) {
	$dbr =& wfGetDB(DB_MASTER);
	$identicalMeaningInteger = (int) $identicalMeaning;
	$dbr->query("UPDATE uw_syntrans SET endemic_meaning=$identicalMeaningInteger WHERE defined_meaning_id=$definedMeaningId and expression_id=$expressionId and ".
				"is_latest_set=1 LIMIT 1");	
}

function updateDefinedMeaningDefinition($definedMeaningId, $languageId, $text) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_defined_meaning dm, translated_content tc, text t SET old_text=". $dbr->addQuotes($text) ." WHERE dm.defined_meaning_id=$definedMeaningId AND dm.is_latest_ver=1 ".
									"AND tc.set_id=dm.meaning_text_tcid AND tc.language_id=$languageId AND tc.text_id=t.old_id AND tc.is_latest_set=1");	
}

function updateTranslatedText($textId, $languageId, $text) {
	$dbr =& wfGetDB(DB_MASTER);
	$dbr->query("UPDATE translated_content tc, text t SET old_text=". $dbr->addQuotes($text) ." WHERE ".
									"tc.set_id=$textId AND tc.language_id=$languageId AND tc.text_id=t.old_id AND tc.is_latest_set=1");	
}
 
function createText($text) {
	$dbr = &wfGetDB(DB_MASTER);
	$text = $dbr->addQuotes($text);
	$sql = "insert into text(old_text) values($text)";	
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function createTranslatedContent($setId, $languageId, $textId, $revisionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "insert into translated_content(set_id,language_id,text_id,first_set,revision_id,is_latest_set) values($setId, $languageId, $textId, $setId, $revisionId, 1)";	
	$dbr->query($sql);
	
	return $dbr->insertId();
}

function translatedTextExists($definitionId, $languageId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT * FROM translated_content WHERE set_id=$definitionId AND language_id=$languageId AND is_latest_set=1");

	return $dbr->numRows($queryResult) > 0;	
}

function addTranslatedText($definitionId, $languageId, $definition, $revisionId) {
	$textId = createText($definition);
	createTranslatedContent($definitionId, $languageId, $textId, $revisionId);
}

function addTranslatedTextIfNotPresent($definitionId, $languageId, $definition, $revisionId) {
	if (!translatedTextExists($definitionId, $languageId, $revisionId)) 	
		addTranslatedText($definitionId, $languageId, $definition, $revisionId);
}

function getDefinedMeaningDefinitionId($definedMeaningId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT meaning_text_tcid FROM uw_defined_meaning WHERE defined_meaning_id=$definedMeaningId AND is_latest_ver=1");

	return $dbr->fetchObject($queryResult)->meaning_text_tcid;
}

function updateDefinedMeaningDefinitionId($definedMeaningId, $definitionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_defined_meaning SET meaning_text_tcid=$definitionId WHERE defined_meaning_id=$definedMeaningId AND is_latest_ver=1");
}

function newTranslatedContentId() {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT max(set_id) as max_id FROM translated_content");
	
	return $dbr->fetchObject($queryResult)->max_id + 1;
}

function addDefinedMeaningDefiningDefinition($definedMeaningId, $revisionId, $languageId, $text) {
	$definitionId = newTranslatedContentId();		
	addTranslatedText($definitionId, $languageId, $text, $revisionId);
	updateDefinedMeaningDefinitionId($definedMeaningId, $definitionId);
}

function addDefinedMeaningDefinition($definedMeaningId, $revisionId, $languageId, $text) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId == 0)
		addDefinedMeaningDefiningDefinition($definedMeaningId, $revisionId, $languageId, $text);
	else 
		addTranslatedTextIfNotPresent($definitionId, $languageId, $text, $revisionId);
}

function createDefinedMeaningAlternativeDefinition($definedMeaningId, $translatedContentId, $revisionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT max(set_id) as max_id FROM uw_alt_meaningtexts");
	$setId = $dbr->fetchObject($queryResult)->max_id + 1;
	
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_alt_meaningtexts (set_id, meaning_mid, meaning_text_tcid, is_latest_set, first_set, revision_id) " .
			    "VALUES ($setId, $definedMeaningId, $translatedContentId, 1, $setId, $revisionId)");
}

function addDefinedMeaningAlternativeDefinition($definedMeaningId, $revisionId, $languageId, $text) {
	$translatedContentId = newTranslatedContentId();
	
	createDefinedMeaningAlternativeDefinition($definedMeaningId, $translatedContentId, $revisionId);
	addTranslatedText($translatedContentId, $languageId, $text, $revisionId);
}

function removeTranslatedText($textId, $languageId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE tc, t FROM translated_content AS tc, text AS t WHERE tc.set_id=$textId AND tc.language_id=$languageId AND tc.is_latest_set=1 AND tc.text_id=t.old_id");
}

function removeDefinedMeaningAlternativeDefinition($definedMeaningId, $definitionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE am, tc, t FROM uw_alt_meaningtexts AS am, translated_content AS tc, text AS t WHERE am.meaning_mid=$definedMeaningId AND am.meaning_text_tcid=$definitionId AND am.is_latest_set=1 AND tc.set_id=$definitionId AND tc.is_latest_set=1 AND tc.text_id=t.old_id");
}

function removeDefinedMeaningDefinition($definedMeaningId, $languageId) {
	$definitionId = getDefinedMeaningDefinitionId($definedMeaningId);
	
	if ($definitionId != 0)
		removeTranslatedText($definitionId, $languageId);
}

function definedMeaningInCollection($definedMeaningId, $collectionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT * FROM uw_collection_contents WHERE collection_id=$collectionId AND member_mid=$definedMeaningId AND is_latest_set=1");
	
	return $dbr->numRows($queryResult) > 0;
}

function getCollectionSetId($collectionId) {
	$dbr = &wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT set_id FROM uw_collection_contents WHERE collection_id=$collectionId AND is_latest_set=1 LIMIT 1");
	$result = $dbr->fetchObject($queryResult)->set_id;
	
	if ($result == 0) {
		$queryResult = $dbr->query("SELECT max(set_id) as max_set_id FROM uw_collection_contents");
		$result = $dbr->fetchObject($queryResult)->max_set_id + 1;
	}
	
	return $result;
}

function addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId, $revisionId) {
	$setId = getCollectionSetId($collectionId);		
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_collection_contents(set_id, collection_id, member_mid, is_latest_set, first_set, revision_id, internal_member_id) " .
					"VALUES ($setId, $collectionId, $definedMeaningId, 1, $setId, $revisionId, ". $dbr->addQuotes($internalId) .")");
}

function addDefinedMeaningToCollectionIfNotPresent($definedMeaningId, $collectionId, $internalId, $revisionId) {
	if (!definedMeaningInCollection($definedMeaningId, $collectionId))
		addDefinedMeaningToCollection($definedMeaningId, $collectionId, $internalId, $revisionId);
}

function removeDefinedMeaningFromCollection($definedMeaningId, $collectionId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE FROM uw_collection_contents WHERE collection_id=$collectionId AND member_mid=$definedMeaningId AND is_latest_set=1");	
}

function updateDefinedMeaningInCollection($definedMeaningId, $collectionId, $internalId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("UPDATE uw_collection_contents SET internal_member_id=".$dbr->addQuotes($internalId) . 
				" WHERE collection_id=$collectionId AND member_mid=$definedMeaningId AND is_latest_set=1");	
}


function bootstrapCollection($collection, $languageId, $collectionType){
	$expression = findOrCreateExpression($collection, $languageId);
	$definedMeaningId = addDefinedMeaning($expression->id, $expression->revisionId);
	$expression->assureIsBoundToDefinedMeaning($definedMeaningId, true);
	addDefinedMeaningDefinition($definedMeaningId, $expression->revisionId, $languageId, $collection);
	return addCollection($definedMeaningId, $collectionType);
}

function addCollection($definedMeaningId, $collectionType) {
	$dbr = &wfGetDB(DB_MASTER);
	$sql = "INSERT INTO uw_collection_ns(collection_mid,is_latest,collection_type) values($definedMeaningId,1,'$collectionType')";
	$queryResult = $dbr->query($sql);
	
	$collectionId = getMaximum("collection_id", "uw_collection_ns");
	
	$sql = "UPDATE uw_collection_ns set first_ver=$collectionId where collection_id=$collectionId";
	$queryResult = $dbr->query($sql);
	
	return $collectionId;	
}

function addDefinedMeaning($expressionId, $revisionId){
	$dbr = &wfGetDB(DB_MASTER);

	$sql = "insert into uw_defined_meaning(expression_id,revision_id,is_latest_ver) values($expressionId,$revisionId, 1)";
	$queryResult = $dbr->query($sql);
	$meaningId = $dbr->insertId($queryResult);

	$sql = "update uw_defined_meaning set first_ver=$meaningId where defined_meaning_id=$meaningId and is_latest_ver=1";
	$queryResult = $dbr->query($sql);
	
	return $meaningId;
}

function createNewDefinedMeaning($expressionId, $revisionId, $languageId, $text) {
	$definedMeaningId = addDefinedMeaning($expressionId, $revisionId);
	$setId = newSetIdForDefinedMeaning();
	createSynonymOrTranslation($setId, $definedMeaningId, $expressionId, $revisionId, true);
	addDefinedMeaningDefiningDefinition($definedMeaningId, $revisionId, $languageId, $text);
	
	return $definedMeaningId;
}

function createDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $translatedContentId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("INSERT INTO uw_dm_text_attribute_values (defined_meaning_id, attribute_mid, value_tcid) " .
			    "VALUES ($definedMeaningId, $attributeId, $translatedContentId)");
}

function addDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $languageId, $text, $revisionId) {
	$translatedContentId = newTranslatedContentId();
	
	createDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $translatedContentId);
	addTranslatedText($translatedContentId, $languageId, $text, $revisionId);
}

function removeDefinedMeaningTextAttributeValue($definedMeaningId, $attributeId, $textId) {
	$dbr = &wfGetDB(DB_MASTER);
	$dbr->query("DELETE av, tc, t FROM uw_dm_text_attribute_values AS av, translated_content AS tc, text AS t " .
				"WHERE av.defined_meaning_id=$definedMeaningId AND av.attribute_mid=$attributeId AND av.value_tcid=tc.set_id AND " .
				"tc.is_latest_set=1 AND tc.text_id=t.old_id");	
}

function getDefinedMeaningDefinition($definedMeaningId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT old_text FROM uw_defined_meaning as dm, translated_content as tc, text as t ".
								"WHERE dm.defined_meaning_id=$definedMeaningId AND " .
								"      dm.meaning_text_tcid=tc.set_id AND tc.language_id=85 AND tc.is_latest_set=1 AND " .
								"      t.old_id= tc.text_id");	
	
	if ($definition = $dbr->fetchObject($queryResult)) 
		return $definition->old_text;
	else	
		return "";
}

?>
