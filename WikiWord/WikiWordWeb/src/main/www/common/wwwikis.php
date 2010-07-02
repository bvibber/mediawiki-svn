<?php
require_once(dirname(__FILE__)."/wwutils.php");

if (!defined('NS_IMAGE'))
    define('NS_IMAGE', 6);

if (!defined('NS_TEMPLATE'))
    define('NS_TEMPLATE', 10);


class WWWikis extends WWUtils {
    var $wikidbs = array();

    function getWikiTableName($lang, $table) {
	global $wwWikitableNamePattern, $wwCommonsTablePrefix;

	if ( $lang == "commons" && $wwCommonsTablePrefix ) return "$wwCommonsTablePrefix$table";

	if ($wwWikitableNamePattern) {
	    return str_replace(array('{lang}', '{name}'), array($lang, $table), $wwWikitableNamePattern);
	}

	return $table;
    }

    function getWikiInfo($lang) {
	global $wwWikiInfoTable, $wwWikiDbName;
	global $wwWikiServerName, $wwWikiFastServerName;
	global $wwCommonsServerName, $wwCommonsFastServerName;

	$db = str_replace('{lang}', $lang, $wwWikiDbName);

	$dbname = "{$lang}wiki_p";
	$sql = "select * from $wwWikiInfoTable ";
	$sql .= " where dbname = " . $this->quote("$db");

	$rs = $this->query($sql);
	$info = mysql_fetch_assoc($rs);
	mysql_free_result($rs);

	if (!$info) $info = false;
	else {
		$info['server'] = str_replace('{num}', $info['server'], $wwWikiServerName);

		if ( $wwWikiFastServerName ) $info['fast-server'] = str_replace('{num}', $info['server'], $wwWikiServerName);
		else $info['fast-server'] = $info['server'];

		if ($lang == "commons" && $wwCommonsServerName) $info['server'] = $wwCommonsServerName;
		if ($lang == "commons" && $wwCommonsFastServerName) $info['fast-server'] = $wwCommonsFastServerName;
		else $info['fast-server'] = $info['server'];
	}

	return $info;
    }

    function getWikiConnection($lang, $fast = false) {
	if ( $fast ) $key = "$lang+fast"; 
	else $key = $lang;

	if (isset($this->wikidbs[$key])) return $this->wikidbs[$key];

	$info = $this->getWikiInfo($lang);

	if (!$info) {
		$db = false;
	} else {
	    if ($fast && isset($info['fast-server'])) $server = $info['fast-server'];
	    else $server = $info['server'];

	    $db = mysql_connect($server, $this->dbuser, $this->dbpassword);
	    if (!$db) throw new Exception("Connection Failure to Database: " . mysql_error());
	    if (!mysql_select_db($info['dbname'], $db)) throw new Exception ("Database not found: " . mysql_error());
	    if (!mysql_query("SET NAMES Latin1;", $db)) throw new Exception ("Database not found: " . mysql_error());
	}

	$this->wikidbs[$key] = $db;
	return $db;
    }

    function queryWiki($lang, $sql) {
	$db = $this->getWikiConnection($lang, false);
	if (!$db) throw new Exception ("Wiki not found: $lang");

	return $this->query($sql, $db);
    }

    function queryWikiFast($lang, $sql) {
	$db = $this->getWikiConnection($lang, true);
	if (!$db) throw new Exception ("Wiki not found: $lang");

	return $this->query($sql, $db);
    }

}
