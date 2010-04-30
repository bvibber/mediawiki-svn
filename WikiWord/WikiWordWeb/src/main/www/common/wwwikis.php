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
	global $wwWikiInfoTable, $wwWikiDbName, $wwWikiServerName, $wwCommonsServerName;

	$db = str_replace('{lang}', $lang, $wwWikiDbName);

	$dbname = "{$lang}wiki_p";
	$sql = "select * from $wwWikiInfoTable ";
	$sql .= " where dbname = " . $this->quote("$db");

	$rs = $this->query($sql);
	$info = mysql_fetch_assoc($rs);
	mysql_free_result($rs);

	if (!$info) $info = false;
	else $info['server'] = str_replace('{num}', $info['server'], $wwWikiServerName);

	if ($lang == "commons" && $wwCommonsServerName) $info['server'] = $wwCommonsServerName;

	return $info;
    }

    function getWikiConnection($lang) {
	if (isset($this->wikidbs[$lang])) return $this->wikidbs[$lang];

	$info = $this->getWikiInfo($lang);

	if (!$info) {
		$db = false;
	} else {
	    $db = mysql_connect($info['server'], $this->dbuser, $this->dbpassword);
	    if (!$db) throw new Exception("Connection Failure to Database: " . mysql_error());
	    if (!mysql_select_db($info['dbname'], $db)) throw new Exception ("Database not found: " . mysql_error());
	    if (!mysql_query("SET NAMES Latin1;", $db)) throw new Exception ("Database not found: " . mysql_error());
	}

	$this->wikidbs[$lang] = $db;
	return $db;
    }

    function queryWiki($lang, $sql) {
	$db = $this->getWikiConnection($lang);
	if (!$db) throw new Exception ("Wiki not found: $lang");

	return $this->query($sql, $db);
    }

}
