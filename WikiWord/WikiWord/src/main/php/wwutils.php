<?php

class WWUtils {
    var $debug = false;
    var $db = NULL;

    function connect($server, $user, $password, $database) {
	$db = mysql_connect($server, $user, $password) or die("Connection Failure to Database: " . mysql_error());
	mysql_select_db($database, $db) or die ("Database not found: " . mysql_error());
	mysql_query("SET NAMES UTF8;", $db) or die ("Database not found: " . mysql_error());

	$this->db = $db;

	return $db;
    }

    function query($sql, $db = NULL) {
	if ($db == NULL && isset($this)) $db = $this->db;

	if ($this->debug) {
	    print htmlspecialchars($sql);
	}

	if (!$db) {
	    throw new Exception("not connected!");
	}

	$result = mysql_query($sql, $db);

	if(!$result) {
		$error = mysql_error($db);
		$errno = mysql_errno($db);
		throw new Exception("$error (#$errno);\nlast query: $sql");
	}

	return $result;
    }

    function close() {
	if ($this->db) mysql_close($this->db);
	$this->db = NULL;
    }

    function queryConceptsForTerm($lang, $term) {
	global $wwTablePrefix, $wwThesaurusDataset;

	$term = trim($term);

	$sql = "SELECT M.*, O.*, definition FROM {$wwTablePrefix}_{$lang}_meaning as M"
	      . " JOIN {$wwTablePrefix}_{$lang}_definition as D ON M.concept = D.concept "
	      . " JOIN {$wwTablePrefix}_{$wwThesaurusDataset}_origin as O ON O.lang = \"" . mysql_real_escape_string($lang) . "\" AND M.concept = O.local_concept "
	      . " WHERE term_text = \"" . mysql_real_escape_string($term) . "\""
	      . " ORDER BY freq DESC "
	      . " LIMIT 100";

	return $this->query($sql);
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

    static function authFailed($realm) {
	    header("Status: 401 Unauthorized", true, 401);
	    header('WWW-Authenticate: Basic realm="'.$realm.'"');
	    die();
    }

    static function doBasicHttpAuth($passwords, $realm) {
	  if (!isset($_SERVER['PHP_AUTH_USER'])) {
	      authFailed();
	  }

	  $usr = $_SERVER['PHP_AUTH_USER'];
	  if (!isset($passwords[$usr])) {
	      authFailed();
	  }

	  $pw = $_SERVER['PHP_AUTH_PW'];
	  if ($pw != $passwords[$usr]) {
	      authFailed();
	  }

	  return $usr;
    }

    static function printSelector($name, $choices, $current = NULL) {
	print "\n\t\t<select name=\"".htmlspecialchars($name)."\" id=\"".htmlspecialchars($name)."\">\n";

	foreach ($choices as $choice => $name) {
	    $sel = $choice == $current ? " selected=\"selected\"" : "";
	    print "\t\t\t<option value=\"".htmlspecialchars($choice)."\"$sel>".htmlspecialchars($name)."</option>\n";
	}

	print "</select>";
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
