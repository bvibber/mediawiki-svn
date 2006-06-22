<?php
/*
 * Created on May 10, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 	define( 'MEDIAWIKI', true );
 	
 	require_once('../../../LocalSettings.php');
 	require_once("Setup.php");
	
	$search = ltrim($_GET['search']);
	$prefix = $_GET['prefix'];
	$query = $_GET['query'];
	
	$dbr =& wfGetDB( DB_SLAVE );
	
	if ($search != '')
    	$searchCondition = "AND expression1.spelling LIKE " . $dbr->addQuotes("$search%");
	else
		$searchCondition = "";
	
	switch ($query) {
		case 'relation-type':
			$sql = getSQLForCollectionOfType('RELT');
			break;
		case 'attribute':
			$sql = getSQLForCollectionOfType('ATTR');
			break;
		case 'defined-meaning':
			$sql = "SELECT syntrans1.defined_meaning_id AS row_id, expression1.spelling AS relation ".
					"FROM uw_expression_ns expression1, uw_syntrans syntrans1 ".
	            	"WHERE expression1.expression_id=syntrans1.expression_id ";
	        break;	
	    case 'collection':
	    	$sql = "SELECT collection.collection_id AS row_id, expression1.spelling AS relation ".
	    			"FROM uw_expression_ns expression1, uw_collection_ns collection, uw_syntrans syntrans ".
	    			"WHERE expression1.expression_id=syntrans.expression_id AND syntrans.defined_meaning_id=collection.collection_mid ".
	    			"AND collection.is_latest=1 AND syntrans.is_latest_set=1 AND expression1.is_latest=1 ";
	    	break;
	}
	                          
	$sql .= $searchCondition . " ORDER BY expression1.spelling LIMIT 10";
	$queryResult = $dbr->query($sql);
	
	echo('<table id="'. $prefix .'table" cellspacing="0" cellpadding="0">');
	
	while ($row = $dbr->fetchRow($queryResult)) 
		echo('<tr id="'. $row['row_id'] .'" class="suggestion-row inactive" onclick="suggestRowClicked(this)" onmouseover="mouseOverRow(this)" onmouseout="mouseOutRow(this)"><td>' . $row['relation'] . '</td><td>'. $row['collection'] .'</td></tr>');
 
	echo('</table>');
	
	function getSQLForCollectionOfType($collectionType) {
		return "SELECT member_mid AS row_id, expression1.spelling AS relation, expression2.spelling AS collection " .
	            "FROM uw_collection_contents, uw_collection_ns, uw_syntrans syntrans1, uw_expression_ns expression1, uw_syntrans syntrans2, uw_expression_ns expression2 " .
	            "WHERE uw_collection_contents.collection_id=uw_collection_ns.collection_id and uw_collection_ns.collection_type='$collectionType' " .
	            
	            "AND syntrans1.defined_meaning_id=uw_collection_contents.member_mid " .
	            "AND expression1.expression_id=syntrans1.expression_id and expression1.language_id=85 " .
	            
	            "AND syntrans2.defined_meaning_id=uw_collection_ns.collection_mid " .
	            "AND expression2.expression_id=syntrans2.expression_id and expression2.language_id=85 " .
	
				"AND uw_collection_contents.is_latest_set=1 ";
	}
?>
