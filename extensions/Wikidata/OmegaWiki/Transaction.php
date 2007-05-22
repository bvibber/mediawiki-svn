<?php

require_once('Attribute.php');
require_once('Record.php');
require_once('RecordSet.php');
require_once("Wikidata.php");
$wdDataSetContext=DefaultWikidataApplication::getDataSetContext();

interface QueryTransactionInformation {
	public function getRestriction($table);
	public function getTables();
	public function versioningAttributes();
	public function versioningFields($tableName);
	public function versioningOrderBy();
	public function versioningGroupBy($table);
	public function setVersioningAttributes($record, $row);
}

class DefaultQueryTransactionInformation {
	public function getRestriction($table) {
		return "1";
	}
	
	public function getTables() {
		return array();
	}

	public function versioningAttributes() {
		return array();
	}
	
	public function versioningFields($tableName) {
		return array();
	}
	
	public function versioningOrderBy() {
		return array();
	}
	
	public function versioningGroupBy($table) {
		return array();
	}
	
	public function setVersioningAttributes($record, $row) {
	}
}

class QueryLatestTransactionInformation extends DefaultQueryTransactionInformation {
	public function getRestriction($table) {
		return getLatestTransactionRestriction($table->name);
	}
	
	public function setVersioningAttributes($record, $row) {
	}
}

class QueryHistoryTransactionInformation extends DefaultQueryTransactionInformation {
	public function versioningAttributes() {
		global
			$recordLifeSpanAttribute;
			
		return array($recordLifeSpanAttribute);
	}

	public function versioningFields($tableName) {
		return array($tableName . '.add_transaction_id', $tableName . '.remove_transaction_id', $tableName . '.remove_transaction_id IS NULL AS is_live');
	}

	public function versioningOrderBy() {
		return array('is_live DESC', 'add_transaction_id DESC');
	}
	
	public function setVersioningAttributes($record, $row) {
		global
			$recordLifeSpanAttribute;
			
		$record->setAttributeValue($recordLifeSpanAttribute, getRecordLifeSpanTuple($row['add_transaction_id'], $row['remove_transaction_id']));
	}
}

class QueryAtTransactionInformation extends DefaultQueryTransactionInformation {
	protected $transactionId;
	protected $addAttributes;
	
	public function __construct($transactionId, $addAttributes) {
		$this->transactionId = $transactionId;
		$this->addAttributes = $addAttributes;
	}
	
	public function getRestriction($table) {
		return getAtTransactionRestriction($table->name, $this->transactionId);
	}
	
	public function versioningAttributes() {
		global
			$recordLifeSpanAttribute;
		
		if ($this->addAttributes)	
			return array($recordLifeSpanAttribute);
		else
			return array();
	}
	
	public function versioningFields($tableName) {
		return array($tableName . '.add_transaction_id', $tableName . '.remove_transaction_id', $tableName . '.remove_transaction_id IS NULL AS is_live');
	}
	
	public function setVersioningAttributes($record, $row) {
		global
			$recordLifeSpanAttribute;
			
		if ($this->addAttributes)	
			$record->setAttributeValue($recordLifeSpanAttribute, getRecordLifeSpanTuple($row['add_transaction_id'], $row['remove_transaction_id']));
	}
}

class QueryUpdateTransactionInformation extends DefaultQueryTransactionInformation {
	protected $transactionId;
	
	public function __construct($transactionId) {
		$this->transactionId = $transactionId;
	}
	
	public function getRestriction($table) {
		return 
			" " . $table->name . ".add_transaction_id =". $this->transactionId . 
			" OR " . $table->name . ".removeTransactionId =" . $this->transactionId;
	}
	
//	public function versioningAttributes() {
//		global
//			$recordLifeSpanAttribute;
//			
//		return array();
//	}
	
//	public function versioningFields($tableName) {
//		return array($tableName . '.add_transaction_id', $tableName . '.remove_transaction_id', $tableName . '.remove_transaction_id IS NULL AS is_live');
//	}
	
//	public function setVersioningAttributes($record, $row) {
//		global
//			$recordLifeSpanAttribute;
//			
//		$record->setAttributeValue($recordLifeSpanAttribute, getRecordLifeSpanTuple($row['add_transaction_id'], $row['remove_transaction_id']));
//	}
}

class QueryAuthoritativeContributorTransactionInformation extends DefaultQueryTransactionInformation {
	protected $availableAuthorities;
	protected $authoritiesToShow;
	protected $showCommunityContribution;
	
	public function __construct($availableAuthorities, $authoritiesToShow, $showCommunityContribution) {
		$this->availableAuthorities = $availableAuthorities;
		$this->authoritiesToShow = $authoritiesToShow;
		$this->showCommunityContribution = $showCommunityContribution;
	}
	
	protected function getKeyFieldRestrictions($table, $prefix) {
		$result = array();
		
		foreach ($table->keyFields as $keyField)
			$result[] = $table->name . "." . $keyField . "=" . $prefix . $table->name . "." . $keyField; 
		
		return implode(" AND ", $result);
	}
	
	public function getRestriction($table) {
		global $wdDataSetContext;
		$dc=$wdDataSetContext;
		$result =  
			$table->name . ".add_transaction_id={$dc}_transactions.transaction_id";

		$showAnyAuthorities = count($this->authoritiesToShow) > 0;

		if ($this->showCommunityContribution || $showAnyAuthorities) {
			$availableAuthorityIds = array_keys($this->availableAuthorities);
			$availableAuthoritiesSet = "(" . implode(", ", $availableAuthorityIds) . ")";
			
			$result =  
				$table->name . ".add_transaction_id={$dc}_transactions.transaction_id" .
				" AND (";
				
			if ($this->showCommunityContribution)
				$result .=	
					"(" .
						" {$dc}_transactions.user_id NOT IN " . $availableAuthoritiesSet . 
						" AND " .$table->name . ".add_transaction_id=(" .
							" SELECT max(add_transaction_id) " .
							" FROM " . $table->name . " AS latest_" . $table->name . ", {$dc}_transactions as latest_transactions" .
							" WHERE " . $this->getKeyFieldRestrictions($table, 'latest_') .
							" AND latest_transactions.transaction_id=latest_" . $table->name . ".add_transaction_id" .
							" AND latest_transactions.user_id NOT IN (" . implode(", ", $availableAuthorityIds) . ")" .
							")" .
						" AND NOT EXISTS (" .
							" SELECT * " .
							" FROM " . $table->name . " AS latest_" . $table->name . ", {$dc}_transactions as latest_transactions" .
							" WHERE " . $this->getKeyFieldRestrictions($table, 'latest_') .
							" AND latest_transactions.transaction_id=latest_" . $table->name . ".remove_transaction_id" .
							" AND latest_transactions.user_id NOT IN " . $availableAuthoritiesSet .
							" AND latest_" . $table->name . ".remove_transaction_id > " . $table->name . ".add_transaction_id" .
						")" . 
					" )";
			else 
				$result .= " 0 "; 
			
			if ($showAnyAuthorities)
				$result .=
					" OR (" .
						" {$dc}_transactions.user_id IN (" . implode(", ", $this->authoritiesToShow) . ") " .
						" AND " .$table->name . ".add_transaction_id=(" .
							" SELECT max(add_transaction_id) " .
							" FROM " . $table->name . " AS latest_" . $table->name . ", {$dc}_transactions as latest_transactions" .
							" WHERE " . $this->getKeyFieldRestrictions($table, 'latest_') .
							" AND latest_transactions.transaction_id=latest_" . $table->name . ".add_transaction_id" .
							" AND latest_transactions.user_id={$dc}_transactions.user_id" .
							")" .
						" AND NOT EXISTS (" .
							" SELECT * " .
							" FROM " . $table->name . " AS latest_" . $table->name . ", {$dc}_transactions as latest_transactions" .
							" WHERE " . $this->getKeyFieldRestrictions($table, 'latest_') .
							" AND latest_transactions.transaction_id=latest_" . $table->name . ".remove_transaction_id" .
							" AND latest_transactions.user_id={$dc}_transactions.user_id" .
							" AND latest_" . $table->name . ".remove_transaction_id > " . $table->name . ".add_transaction_id" .
						")" . 
					" )";
			
			$result .= " )";
		}
		else 
			$result .= " AND 0";
			
		return $result;
	}
	
	public function getTables() {
		global $wdDataSetContext;
		$dc=$wdDataSetContext;
		return array("{$dc}_transactions");
	}
	
	public function versioningAttributes() {
		global
			$authorityAttribute;
			
		return array($authorityAttribute);
	}

	public function versioningFields($tableName) {
		return array("{$dc}_transactions.user_id", $tableName . '.add_transaction_id');
	}

	public function setVersioningAttributes($record, $row) {
		global
			$authorityAttribute;
			
		$userID = $row['user_id'];
		
		if (array_key_exists($userID, $this->availableAuthorities))
			$userName = $this->availableAuthorities[$userID]; //getUserName($userID);
		else
			$userName = "Community";	
			
		$record->setAttributeValue($authorityAttribute, $userName);
	}
}

global
	$updateTransactionId;

function startNewTransaction($userID, $userIP, $comment) {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	global
		$updateTransactionId;
	
	$dbr =& wfGetDB(DB_MASTER);
	$timestamp = wfTimestampNow();
	
	$dbr->query("INSERT INTO {$dc}_transactions (user_id, user_ip, timestamp, comment) VALUES (". $userID . ', ' . $dbr->addQuotes($userIP) . ', ' . $timestamp . ', ' . $dbr->addQuotes($comment) . ')');
	$updateTransactionId = $dbr->insertId();
}

function getUpdateTransactionId() {
	global
		$updateTransactionId;

	return $updateTransactionId;	
}

function getLatestTransactionId() {
	global $wdDataSetContext;
	$dc=$wdDataSetContext;
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT max(transaction_id) AS transaction_id FROM {$dc}_transactions");

	if ($transaction = $dbr->fetchObject($queryResult)) 
		return $transaction->transaction_id;
	else
		return 0;
}

function getLatestTransactionRestriction($table) {
	return ' '. $table . '.remove_transaction_id IS NULL ';
}

function getAtTransactionRestriction($table, $transactionId) {
	return ' '. $table . '.add_transaction_id <= '. $transactionId . ' AND ('.		
				$table . '.remove_transaction_id > '. $transactionId . ' OR ' . $table . '.remove_transaction_id IS NULL) ';
}

function getViewTransactionRestriction($table) {
	global
		$wgRequest;
	
	$action = $wgRequest->getText('action');
	
	if ($action == 'edit')
		return getLatestTransactionRestriction($table);
	else if ($action == 'history')
		return '1';
	else 
		return getLatestTransactionRestriction($table);		
}

function getOperationSelectColumn($table, $transactionId) {
	return " IF($table.add_transaction_id=$transactionId, 'Added', 'Removed') AS operation "; 
}

function getInTransactionRestriction($table, $transactionId) {
	return " ($table.add_transaction_id=$transactionId OR $table.remove_transaction_id=$transactionId) ";
}

global
	$transactionIdAttribute, $userAttribute, $userIPAttribute, $timestampAttribute,
	$transactionStructure, $summaryAttribute, 
	$addTransactionAttribute, $removeTransactionAttribute, 
	$recordLifeSpanAttribute, $recordLifeSpanStructure,
	$authorityAttribute, $wgAuthorityAttributeName;
	
$transactionIdAttribute = new Attribute('transaction-id', 'Transaction ID', 'integer');
$userAttribute = new Attribute('user', 'User', 'user');
$authorityAttribute = new Attribute('authority', $wgAuthorityAttributeName, 'authority');
$userIPAttribute = new Attribute('user-ip', 'User IP', 'IP');
$timestampAttribute = new Attribute('timestamp', 'Time', 'timestamp');
$summaryAttribute = new Attribute('summary', 'Summary', 'text');
$transactionStructure = new Structure($transactionIdAttribute, $userAttribute, $userIPAttribute, $timestampAttribute, $summaryAttribute);

$addTransactionAttribute = new Attribute('add-transaction', 'Added', new RecordType($transactionStructure));
$removeTransactionAttribute = new Attribute('remove-transaction', 'Removed', new RecordType($transactionStructure));

$recordLifeSpanStructure = new Structure($addTransactionAttribute, $removeTransactionAttribute);
$recordLifeSpanAttribute = new Attribute('record-life-span', 'Record life span', new RecordType($recordLifeSpanStructure));

function getUserName($userId) {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT user_name FROM user WHERE user_id=$userId");
	
	if ($user = $dbr->fetchObject($queryResult))
		return $user->user_name;
	else
		return "";
}

function getUserLabel($userId, $userIP) {
	if ($userId > 0)
		return getUserName($userId);
	else if ($userIP != "")
		return $userIP;
	else
		return "Unknown"; 
}

function expandUserIDsInRecordSet($recordSet, $userIDAttribute, $userIPAttribute) {
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) { 
		$record = $recordSet->getRecord($i);
		$record->setAttributeValue(
			$userIDAttribute, 
			getUserLabel(
				$record->getAttributeValue($userIDAttribute),
				$record->getAttributeValue($userIPAttribute)
			)
		);
	}
}								

function expandTransactionIdsInRecordSet($recordSet, $transactionIdAttribute, $transactionAttribute) {
	for ($i = 0; $i < $recordSet->getRecordCount(); $i++) { 
		$record = $recordSet->getRecord($i);
		$record->setAttributeValue(
			$transactionAttribute, 
			getTransactionRecord($record->getAttributeValue($transactionIdAttribute))
		);
	}
}	

function getTransactionRecord($transactionId) {
	global
		$transactionStructure, $transactionIdAttribute, $userAttribute, $timestampAttribute, $summaryAttribute;
	
	global $wdDataSetContext;
	$dc=$wdDataSetContext;

	$result = new ArrayRecord($transactionStructure);
	$result->setAttributeValue($transactionIdAttribute, $transactionId);
	
	if ($transactionId > 0) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT user_id, user_ip, timestamp, comment FROM {$dc}_transactions WHERE transaction_id=$transactionId");
		
		if ($transaction = $dbr->fetchObject($queryResult)) {
			$result->setAttributeValue($userAttribute, getUserLabel($transaction->user_id, $transaction->user_ip));	
			$result->setAttributeValue($timestampAttribute, $transaction->timestamp);
			$result->setAttributeValue($summaryAttribute, $transaction->comment);
		}
	}
	else {
		if ($transactionId != null)
			$result->setAttributeValue($userAttribute, "Unknown");
		else
			$result->setAttributeValue($userAttribute, "");	
				
		$result->setAttributeValue($timestampAttribute, "");
		$result->setAttributeValue($summaryAttribute, "");
	}

	return $result;
}

function getRecordLifeSpanTuple($addTransactionId, $removeTransactionId) {
	global
		$recordLifeSpanStructure, $addTransactionAttribute, $removeTransactionAttribute;
	
	$result = new ArrayRecord($recordLifeSpanStructure);
	$result->setAttributeValue($addTransactionAttribute, getTransactionRecord($addTransactionId));
	$result->setAttributeValue($removeTransactionAttribute, getTransactionRecord($removeTransactionId));
	
	return $result;
}

function getTransactionLabel($transactionId) {
	global
		$timestampAttribute, $userAttribute, $summaryAttribute;
	
	if ($transactionId > 0) {
		$record = getTransactionRecord($transactionId);
		
		$label = 
			timestampAsText($record->getAttributeValue($timestampAttribute)) . ', ' .
			$record->getAttributeValue($userAttribute);
			
		$summary = $record->getAttributeValue($summaryAttribute);
		
		if ($summary != "")
			$label .= ', ' . $summary;
			
		return $label;
	}
	else 
		return "";
}

?>
