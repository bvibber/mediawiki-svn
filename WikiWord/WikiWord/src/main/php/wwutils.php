<?php

class WWUtils {
    var $debug = false;
    var $db = NULL;

    var $dbuser;
    var $dbpassword;

    function connect($server, $user, $password, $database) {
	$db = mysql_connect($server, $user, $password) or die("Connection Failure to Database: " . htmlspecialchars(mysql_error())."\n");
	mysql_select_db($database, $db) or die ("Database not found: " . htmlspecialchars(mysql_error())."\n");
	mysql_query("SET NAMES UTF8;", $db) or die ("Database not found: " . htmlspecialchars(mysql_error())."\n");

	$this->dbuser = $user;
	$this->dbpassword = $password;
	$this->db = $db;

	return $db;
    }

    function query($sql, $db = NULL) {
	if ($db == NULL && isset($this)) $db = $this->db;

	if ($this->debug) {
	    print "\n<br/>" .  htmlspecialchars($sql) . "<br/>\n";
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

    function quote($s) {
	return '"' . mysql_real_escape_string($s) . '"';
    }

    function close() {
	if ($this->db) mysql_close($this->db);
	$this->db = NULL;

	foreach ($this->wikidbs as $name => $db) {
	    if ($db) mysql_close($db);
	}

	$this->wikidbs = array();
    }

    static function slurpList($rs, $field) {
	if (is_string($rs)) $rs = $this->query($rs);

	$list = array();
	while ($row = mysql_fetch_assoc($rs)) {
	    $list[] = $row[$field];
	}

	return $list;
    }

    static function slurpAssoc($rs, $keyField, $valueField) {
	if (is_string($rs)) $rs = $this->query($rs);

	$list = array();
	while ($row = mysql_fetch_assoc($rs)) {
	    $key = $row[$keyField];
	    $value = $row[$valueField];
	    $list[$key] = $value;
	}

	return $list;
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
}
