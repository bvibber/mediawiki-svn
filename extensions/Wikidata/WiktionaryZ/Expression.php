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

function determineSetIdForDefinedMeaning($definedMeaningId) {
	$result = getLatestSetIdForDefinedMeaning($definedMeaningId);
	
	if ($result == 0)
		$result = getMaximum('set_id', 'uw_syntrans') + 1;
		
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

?>
