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
    	$searchCondition = "and expression1.spelling like " . $dbr->addQuotes("$search%");
	else
		$searchCondition = "";
	
	if ($query == 'relation-type') 
		$sql = "select member_mid as row_id, expression1.spelling as relation, expression2.spelling as collection " .
	            "from uw_collection_contents, uw_collection_ns, uw_syntrans syntrans1, uw_expression_ns expression1, uw_syntrans syntrans2, uw_expression_ns expression2 " .
	            "where uw_collection_contents.collection_id=uw_collection_ns.collection_id and uw_collection_ns.collection_type='RELT' " .
	            
	            "and syntrans1.defined_meaning_id=uw_collection_contents.member_mid " .
	            "and expression1.expression_id=syntrans1.expression_id and expression1.language_id=85 " .
	            
	            "and syntrans2.defined_meaning_id=uw_collection_ns.collection_mid " .
	            "and expression2.expression_id=syntrans2.expression_id and expression2.language_id=85 " .
	
				"and uw_collection_contents.is_latest_set=1 ";
	else if ($query == 'defined-meaning')
		$sql = "select syntrans1.defined_meaning_id as row_id, expression1.spelling as relation ".
				"from uw_expression_ns expression1, uw_syntrans syntrans1 ".
				
				//"syntrans1.defined_meaning_id = uw_defined_meaning.defined_meaning_id " .
	            "where expression1.expression_id=syntrans1.expression_id ";//and expression1.language_id=85 ";
	                          
	$sql .= $searchCondition . " ORDER BY expression1.spelling LIMIT 10";
	$queryResult = $dbr->query($sql);
	
	echo('<table id="'. $prefix .'table" cellspacing="0" cellpadding="0">');
	
	while ($row = $dbr->fetchRow($queryResult)) 
		echo('<tr id="'. $row['row_id'] .'" class="suggestion-row inactive" onclick="suggestRowClicked(this)" onmouseover="mouseOverRow(this)" onmouseout="mouseOutRow(this)"><td>' . $row['relation'] . '</td><td>'. $row['collection'] .'</td></tr>');
 
	echo('</table>');
?>
