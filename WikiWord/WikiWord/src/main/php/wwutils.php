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
		throw new Exception("$error (#$errno)");
	}

	return $result;
    }

    function close() {
	if ($this->db) mysql_close($this->db);
	$this->db = NULL;
    }

    function queryConceptsForTerm($lang, $term) {
	global $wwTablePrefix;

	$term = trim($term);

	$sql = "SELECT M.*, definition FROM {$wwTablePrefix}_{$lang}_meaning as M"
	      . " JOIN {$wwTablePrefix}_{$lang}_definition as D ON M.concept = D.concept "
	      . " WHERE term_text = \"" . mysql_real_escape_string($term) . "\""
	      . " ORDER BY freq DESC "
	      . " LIMIT 100";

	return $this->query($sql);
    }

    function queryLocalConceptInfo($lang, $id) {
	global $wwTablePrefix;

	$term = trim($term);

	$sql = "SELECT C.*, F.*, definition FROM {$wwTablePrefix}_{$lang}_concept_info as F "
	      . " JOIN {$wwTablePrefix}_{$lang}_concept as C ON F.concept = C.id "
	      . " JOIN {$wwTablePrefix}_{$lang}_definition as D ON F.concept = D.concept "
	      . " WHERE F.concept = $id ";

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

    static function unpickle($s, $hasId=true, $hasName=true, $hasConf=true) {
	$ss = explode("\x1E", $s);
	$items = array();

	foreach ($ss as $i) {
	    $r = explode("\x1F", $i);
	    $offs = -1;

	    if ($hasId)   $r['id']   = $r[$offs += 1];
	    if ($hasName) $r['name'] = $r[$offs += 1];
	    if ($hasConf) $r['conf'] = $r[$offs += 1];

	    $items[] = $r;
	}

	return $items;
    }
}
