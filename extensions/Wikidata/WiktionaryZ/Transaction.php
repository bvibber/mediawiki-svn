<?php

require_once('Attribute.php');
require_once('Record.php');
require_once('RecordSet.php');

interface QueryTransactionInformation {
	public function getRestriction($tableName);
	public function versioningAttributes();
	public function versioningFields($tableName);
	public function versioningOrderBy();
	public function setVersioningAttributes($record, $row);
}

class QueryLatestTransactionInformation implements QueryTransactionInformation {
	public function getRestriction($tableName) {
		return getLatestTransactionRestriction($tableName);
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
	
	public function setVersioningAttributes($record, $row) {
	}
}

class QueryHistoryTransactionInformation implements QueryTransactionInformation {
	public function getRestriction($tableName) {
		return "1";
	}
	
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

class QueryAtTransactionInformation implements QueryTransactionInformation {
	protected $transactionId;
	
	public function __construct($transactionId) {
		$this->transactionId = $transactionId;
	}
	
	public function getRestriction($tableName) {
		return getAtTransactionRestriction($tableName, $this->transactionId);
	}
	
	public function versioningAttributes() {
		global
			$recordLifeSpanAttribute;
			
		return array($recordLifeSpanAttribute);
	}
	
	public function versioningFields($tableName) {
		return array($tableName . '.add_transaction_id', $tableName . '.remove_transaction_id', $tableName . '.remove_transaction_id IS NULL AS is_live');
	}
	
	public function versioningOrderBy() {
		return array();
	}
	
	public function setVersioningAttributes($record, $row) {
		global
			$recordLifeSpanAttribute;
			
		$record->setAttributeValue($recordLifeSpanAttribute, getRecordLifeSpanTuple($row['add_transaction_id'], $row['remove_transaction_id']));
	}
}

global
	$updateTransactionId;

function startNewTransaction($userID, $userIP, $comment) {
	global
		$updateTransactionId;
	
	$dbr =& wfGetDB(DB_MASTER);
	$timestamp = wfTimestampNow();
	
	$dbr->query('INSERT INTO transactions (user_id, user_ip, timestamp, comment) VALUES ('. $userID . ', ' . $dbr->addQuotes($userIP) . ', ' . $timestamp . ', ' . $dbr->addQuotes($comment) . ')');
	$updateTransactionId = $dbr->insertId();
}

function getUpdateTransactionId() {
	global
		$updateTransactionId;

	return $updateTransactionId;	
}

function getLatestTransactionId() {
	$dbr =& wfGetDB(DB_SLAVE);
	$queryResult = $dbr->query("SELECT max(transaction_id) AS transaction_id FROM transactions");

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

global
	$transactionIdAttribute, $userAttribute, $timestampAttribute, $transactionStructure, $addTransactionAttribute, $removeTransactionAttribute, 
	$recordLifeSpanAttribute, $recordLifeSpanStructure;
	
$transactionIdAttribute = new Attribute('transaction-id', 'Transaction ID', 'integer');
$userAttribute = new Attribute('user', 'User', 'user');
$timestampAttribute = new Attribute('timestamp', 'Time', 'timestamp');
$transactionStructure = new Structure($userAttribute, $timestampAttribute);

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

function getTransactionTuple($transactionId) {
	global
		$transactionStructure, $transactionIdAttribute, $userAttribute, $timestampAttribute, $wgUser, $wgContLang;
	
	$result = new ArrayRecord($transactionStructure);
	$result->setAttributeValue($transactionIdAttribute, $transactionId);
	
	if ($transactionId > 0) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT user_id, user_ip, timestamp, comment FROM transactions WHERE transaction_id=$transactionId");
		
		if ($transaction = $dbr->fetchObject($queryResult)) {
			$result->setAttributeValue($userAttribute, getUserLabel($transaction->user_id, $transaction->user_ip));	
			$result->setAttributeValue($timestampAttribute,  $wgContLang->timeanddate($transaction->timestamp));
		}
	}
	else {
		if ($transactionId != null)
			$result->setAttributeValue($userAttribute, "Unknown");
		else
			$result->setAttributeValue($userAttribute, "");	
				
		$result->setAttributeValue($timestampAttribute,  "");
	}

	return $result;
}

function getRecordLifeSpanTuple($addTransactionId, $removeTransactionId) {
	global
		$recordLifeSpanStructure, $addTransactionAttribute, $removeTransactionAttribute;
	
	$result = new ArrayRecord($recordLifeSpanStructure);
	$result->setAttributeValue($addTransactionAttribute, getTransactionTuple($addTransactionId));
	$result->setAttributeValue($removeTransactionAttribute, getTransactionTuple($removeTransactionId));
	
	return $result;
}

?>