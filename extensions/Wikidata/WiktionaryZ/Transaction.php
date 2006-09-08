<?php

require_once('Attribute.php');

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

function getLatestTransactionRestriction($table) {
	return ' '. $table . '.remove_transaction_id IS NULL ';
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
	$userAttribute, $timestampAttribute, $transactionStructure, $addTransactionAttribute, $removeTransactionAttribute, 
	$recordLifeSpanAttribute, $recordLifeSpanStructure;
	
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

function getTransactionTuple($transactionId) {
	global
		$transactionStructure, $userAttribute, $timestampAttribute, $wgUser, $wgContLang;
	
	$result = new ArrayRecord($transactionStructure);
//	$result->setAttributeValue($userAttribute, "");
//	$result->setAttributeValue($timestampAttribute, "");
	
	if ($transactionId > 0) {
		$dbr =& wfGetDB(DB_SLAVE);
		$queryResult = $dbr->query("SELECT user_id, user_ip, timestamp, comment FROM transactions WHERE transaction_id=$transactionId");
		
		if ($transaction = $dbr->fetchObject($queryResult)) {
			if ($transaction->user_id > 0)
				$userText = getUserName($transaction->user_id);
			else if ($transaction->user_ip != "")
				$userText = $transaction->user_ip;
			else
				$userText = "Unknown"; 
				
			$result->setAttributeValue($userAttribute, $userText);	
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