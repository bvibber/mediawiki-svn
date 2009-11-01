<?php
require_once(dirname(__FILE__)."/wwutils.php");

class WWThesaurus extends WWUTils {

    function queryConceptsForTerm($lang, $term) {
	global $wwTablePrefix, $wwThesaurusDataset;

	$term = trim($term);

	$sql = "SELECT M.*, O.*, definition FROM {$wwTablePrefix}_{$lang}_meaning as M"
	      . " LEFT JOIN {$wwTablePrefix}_{$lang}_definition as D ON M.concept = D.concept "
	      . " JOIN {$wwTablePrefix}_{$wwThesaurusDataset}_origin as O ON O.lang = \"" . mysql_real_escape_string($lang) . "\" AND M.concept = O.local_concept "
	      . " WHERE term_text = \"" . mysql_real_escape_string($term) . "\""
	      . " ORDER BY freq DESC "
	      . " LIMIT 100";

	return $this->query($sql);
    }

    function queryLocalConcepts($id) {
	global $wwTablePrefix, $wwThesaurusDataset;
	$sql = "SELECT O.lang, O.local_concept_name from {$wwTablePrefix}_{$wwThesaurusDataset}_origin as O ";
	$sql .= " WHERE O.global_concept = " . (int)$id;

	return $this->query($sql);
    }

    function getLocalConcepts($id) {
	$rs = $this->queryLocalConcepts($id);
	$list = WWUtils::slurpAssoc($rs, "lang", "local_concept_name");
	mysql_free_result($rs);
	return $list;
    }

    function queryLocalConceptInfo($lang, $id) {
	global $wwTablePrefix, $wwThesaurusDataset;

	$sql = "SELECT O.*, C.*, F.*, definition FROM {$wwTablePrefix}_{$lang}_concept_info as F "
	      . " JOIN {$wwTablePrefix}_{$lang}_concept as C ON F.concept = C.id "
	      . " JOIN {$wwTablePrefix}_{$wwThesaurusDataset}_origin as O ON O.lang = \"" . mysql_real_escape_string($lang) . "\" AND F.concept = O.local_concept "
	      . " LEFT JOIN {$wwTablePrefix}_{$lang}_definition as D ON F.concept = D.concept "
	      . " WHERE O.local_concept = $id ";

	return $this->query($sql);
    }

    function queryConceptInfo($id, $lang) {
	global $wwTablePrefix, $wwThesaurusDataset;

	$sql = "SELECT O.*, C.*, F.*, definition FROM  {$wwTablePrefix}_{$wwThesaurusDataset}_origin as O "
	      . " LEFT JOIN {$wwTablePrefix}_{$wwThesaurusDataset}_concept_info as F ON O.global_concept = F.concept "
	      . " LEFT JOIN {$wwTablePrefix}_{$lang}_concept as C ON O.local_concept = C.id "
	      . " LEFT JOIN {$wwTablePrefix}_{$lang}_definition as D ON O.local_concept = D.concept "
	      . " WHERE O.global_concept = $id AND O.lang = \"" . mysql_real_escape_string($lang) . "\" ";

	return $this->query($sql);
    }

    function unpickle($s, $lang, $hasId=true, $hasName=true, $hasConf=true) {
	$ss = explode("\x1E", $s);
	$items = array();

	$fetchNames = false;

	foreach ($ss as $i) {
	    $r = explode("\x1F", $i);
	    $offs = -1;

	    if ($hasId)   $r['id']   = @$r[$offs += 1];
	    if ($hasName) $r['name'] = @$r[$offs += 1];
	    if ($hasConf) $r['conf'] = @$r[$offs += 1];

	    if ($hasId && !isset($r['name'])) 
	      $fetchNames = true;

	    if ($hasId) $items[ $r['id'] ] = $r;
	    else $items[] = $r;
	}

	if ($fetchNames) {
	    $names = $this->fetchNames(array_keys($items), $lang);

	    $keys = array_keys($items);
	    foreach ($keys as $k) {
		$id = $items[$k]['id'];
		$items[$k]['name'] = $names[$id];
	    }
	}

	return $items;
    }

    function fetchNames($ids, $lang) {
	global $wwTablePrefix, $wwThesaurusDataset;

	$names = array();
	if (!$ids) return $names;

	$set = NULL;
	foreach ($ids as $id) {
	   if ($set===NULL) $set = "";
	   else $set .= ", ";
	   $set .= $id;
	}

	$sql = "select global_concept as id, local_concept_name as name from {$wwTablePrefix}_{$wwThesaurusDataset}_origin ";
	$sql .= "where global_concept in ($set) and lang = \"" . mysql_real_escape_string($lang) . "\" ";

	$res = $this->query($sql);

	while ($row = mysql_fetch_assoc($res)) {
	    $id = $row['id'];
	    $names[$id] = $row['name'];
	}
	
	mysql_free_result($res);

	return $names;
    }

}
