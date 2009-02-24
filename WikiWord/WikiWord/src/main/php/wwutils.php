<?php

class WWUtil {
    static function connect($server, $user, $password, $database) {
	$db = mysql_connect($server, $user, $password) or die("Connection Failure to Database: " . mysql_error());
	mysql_select_db($database, $db) or die ("Database not found: " . mysql_error());
	mysql_query("SET NAMES UTF8;", $db) or die ("Database not found: " . mysql_error());

	if (isset($this)) $this->db = $db;
	return $db;
    }

    static function query($sql, $db = NULL) {
	if ($db == NULL && isset($this)) $db = $this->$db;

	if ($debug) {
	    print htmlspecialchars($sql);
	}

	$result = mysql_query($sql, $db);

	if(!$result) {
		$error = mysql_error($db);
		$errno = mysql_errno($db);
		throw new Exception("$error (#$errno)");
	}

	return $result;
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
}
