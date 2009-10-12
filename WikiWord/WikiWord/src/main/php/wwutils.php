<?php

if (!defined('NS_IMAGE'))
    define('NS_IMAGE', 6);

class ImageCollection {

    function __construct() {
	$this->images = array();
    }

    static function compareRecords($a, $b) {
	return $b['score'] - $a['score']; //NOTE: descending
    }

    function size() {
	return count($this->images);
    }

    function listImages($max) {
	uasort($this->images, "Imagecollection::compareRecords");

	if ($max) return array_slice($this->images, 0, $max);
	else return $this->images;
    }

    function addImage($image, $key, $usage = "page", $weight = 1) {
	if (!isset($this->images[$image])) {
	    $rec = array(
		"name" => $image,
		"score" => 0
	    );
	} else {
	  $rec = $this->images[$image];
	}

	if (!isset($rec[$usage])) $rec[$usage] = array();
	$rec[$usage][] = $key;
	$rec["score"] += $weight;

	$this->images[$image] = $rec;
	return $rec['score'];
    }

    function addImages($images, $key, $usage = "page", $weight = 1) {
	foreach ($images as $image) {
	    $this->addImage($image, $key, $usage, $weight);
	}
    }

}

class WWUtils {
    var $debug = false;
    var $db = NULL;
    var $wikidbs = array();

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

    function getWikiTableName($lang, $table) {
	global $wwWikitableNamePattern;

	if ($wwWikitableNamePattern) {
	    return str_replace(array('{lang}', '{name}'), array($lang, $table), $wwWikitableNamePattern);
	}

	return $table;
    }

    function quote($s) {
	return '"' . mysql_real_escape_string($s) . '"';
    }

    function getWikiInfo($lang) {
	global $wwWikiInfoTable, $wwWikiDbName, $wwWikiServerName;

	$db = str_replace('{lang}', $lang, $wwWikiDbName);

	$dbname = "{$lang}wiki_p";
	$sql = "select * from $wwWikiInfoTable ";
	$sql .= " where dbname = " . $this->quote("$db");

	$rs = $this->query($sql);
	$info = mysql_fetch_assoc($rs);
	mysql_free_result($rs);

	if (!$info) $info = false;
	else $info['server'] = str_replace('{num}', $info['server'], $wwWikiServerName);

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

    function queryImagesOnPage($lang, $ns, $title, $commonsOnly = false) {
	global $wwTablePrefix, $wwThesaurusDataset, $wwCommonsTablePrefix;

	if ($lang == "commons") $commonsOnly = false;

	$imagelinks_table = $this->getWikiTableName($lang, "imagelinks");
	$page_table = $this->getWikiTableName($lang, "page");
	$image_table = $this->getWikiTableName($lang, "image");

	$sql = "/* queryImagesOnPage(" . $this->quote($lang) . ", " . (int)$ns . ", " . $this->quote($title) . ", " . (int)$commonsOnly . ") */ ";

	$sql .= " SELECT I.il_to as name FROM $imagelinks_table as I ";
	$sql .= " JOIN $page_table as P on P.page_id = I.il_from ";
	if ($commonsOnly) $sql .= " LEFT JOIN $image_table as R on R.img_name = I.il_to ";
	if ($commonsOnly) $sql .= " JOIN {$wwCommonsTablePrefix}image as C on C.img_name = I.il_to ";
	
	$sql .= " WHERE P.page_namespace = " . (int)$ns;
	$sql .= " AND P.page_title = " . $this->quote($title);
	if ($commonsOnly) $sql .= " AND R.img_name IS NULL";

	return $this->queryWiki($lang, $sql);
    }

    function getImagesOnPage($lang, $ns, $title, $commonsOnly = false) {
	$rs = $this->queryImagesOnPage($lang, $ns, $title, $commonsOnly);

	$list = WWUtils::slurpList($rs, "name");
	mysql_free_result($rs);

	return $list;
    }

    function queryImagesOnPageTemplates($lang, $ns, $title, $commonsOnly = false) {
	global $wwTablePrefix, $wwThesaurusDataset, $wwCommonsTablePrefix;

	if ($lang == "commons") $commonsOnly = false;

	$imagelinks_table = $this->getWikiTableName($lang, "imagelinks");
	$page_table = $this->getWikiTableName($lang, "page");
	$image_table = $this->getWikiTableName($lang, "image");
	$templatelinks_table = $this->getWikiTableName($lang, "templatelinks");

	$sql = "/* queryImagesOnPageTemplates(" . $this->quote($lang) . ", " . (int)$ns . ", " . $this->quote($title) . ", " . (int)$commonsOnly . ") */ ";

	$sql .= " SELECT I.il_to as name FROM $imagelinks_table as I ";
	$sql .= " JOIN $page_table as TP on TP.page_id = I.il_from ";
	$sql .= " JOIN $templatelinks_table as T on T.tl_namespace = TP.page_namespace AND T.tl_title = TP.page_title ";
	$sql .= " JOIN $page_table as P on P.page_id = T.tl_from ";
	if ($commonsOnly) $sql .= " LEFT JOIN $image_table as R on R.img_name = I.il_to ";
	if ($commonsOnly) $sql .= " JOIN {$wwCommonsTablePrefix}image as C on C.img_name = I.il_to ";

	$sql .= " WHERE P.page_namespace = " . (int)$ns;
	$sql .= " AND P.page_title = " . $this->quote($title);
	if ($commonsOnly) $sql .= " AND R.img_name IS NULL";

	return $this->queryWiki($lang, $sql);
    }

    function getImagesOnPageTemplates($lang, $ns, $title, $commonsOnly = false) {
	$rs = $this->queryImagesOnPageTemplates($lang, $ns, $title, $commonsOnly);
	$list = WWUtils::slurpList($rs, "name");
	mysql_free_result($rs);
	return $list;
    }

    function queryImagesInCategory($lang, $title) {
	global $wwTablePrefix, $wwThesaurusDataset, $wwCommonsTablePrefix;
	$categorylinks_table = $this->getWikiTableName($lang, "categorylinks");
	$page_table = $this->getWikiTableName($lang, "page");

	$sql = "/* queryImagesInCategory(" . $this->quote($lang) . ", " . $this->quote($title) . ") */ ";

	$sql .= " SELECT P.page_title as name FROM $page_table as P ";
	$sql .= " JOIN $categorylinks_table as C on C.cl_from = P.page_id ";

	$sql .= " WHERE C.cl_to = " . $this->quote($title);
	$sql .= " AND P.page_namespace = " . NS_IMAGE;

	return $this->queryWiki($lang, $sql);
    }

    function getImagesInCategory($lang, $title) {
	$rs = $this->queryImagesInCategory($lang, $title);
	$list = WWUtils::slurpList($rs, "name");
	mysql_free_result($rs);
	return $list;
    }

    function queryTemplatesOnImagePage($lang, $image) {
	global $wwTablePrefix, $wwThesaurusDataset, $wwCommonsTablePrefix;
	$page_table = $this->getWikiTableName($lang, "page");
	$templatelinks_table = $this->getWikiTableName($lang, "templatelinks");

	$sql = "/* queryTemplatesOnImagePage(" . $this->quote($lang) . ", " . $this->quote($image) . ") */ ";

	$sql .= " SELECT tl_title as template FROM $templatelinks_table as T ";
	$sql .= " JOIN $page_table as P on P.page_id = T.tl_from AND T.tl_namespace = " . NS_TEMPLATE . " ";

	$sql .= " WHERE P.page_title = " . $this->quote($image);
	$sql .= " AND P.page_namespace = " . NS_IMAGE;

	return $this->queryWiki($lang, $sql);
    }

    function getTemplatesOnImagePage($lang, $image) {
	$rs = $this->queryTemplatesOnImagePage($lang, $image);
	$list = WWUtils::slurpList($rs, "template");
	mysql_free_result($rs);
	return $list;
    }

    function getTemplateScores($templates, $values = NULL) {
	global $wwWikiServerName;
	if ($values === NULL) $values = $wwTemplateScores;

	if (!$values) return 0;

	$score = 0;
	foreach ($templates as $t) {
	    $v = @$values[$t];
	    if ($v) $score += $v;
	}

	return $score;
    }

    function getRelevantImagesOnPage($lang, $ns, $title, $commonsOnly = false) {
	$img = $this->getImagesOnPage($lang, 0, $title, true);
	$timg = $this->getImagesOnPageTemplates($lang, 0, $title, true);
	$img = array_diff($img, $timg);
	return $img;
    }

    function getImagesAbout($id, $max = 0) {
	global $wwFakeCommonsConcepts, $wwFakeCommonsPlural;

	$concepts = $this->getLocalConcepts($id);

	if ($wwFakeCommonsConcepts && isset($concepts['en'])) {
	    $concepts['commons'] = @$concepts['en'];
	}

	$images = new ImageCollection();
	
	foreach ($concepts as $lang => $title) {
	    if ($lang == "commons") continue;

	    $img = $this->getRelevantImagesOnPage($lang, 0, $title, true); //FIXME: resource mapping
	    $images->addImages($img, $lang . ":" . $title, "article", 1);
	}

	if ($max && $images->size()>$max) 
	    return $images->listImages($max);

	if (isset($concepts['commons'])) {
	    $title = $concepts['commons'];

	    $img = $this->getRelevantImagesOnPage("commons", 0, $title, false); //FIXME: resource mapping
	    $images->addImages($img, "commons:" . $title, "gallery", 0.8);

	    if ($max && $images->size()>$max) 
		return $images->listImages($max);

	    $img = $this->getImagesInCategory("commons", $title); //FIXME: resource mapping
	    $images->addImages($img, "commons:category:" . $title, "category", 0.5);

	    if ($wwFakeCommonsConcepts && $wwFakeCommonsPlural) {
		$img = $this->getImagesInCategory("commons", $title."s"); //FIXME: resource mapping
		$images->addImages($img, "commons:category:" . $title."s", "category(pl)", 0.5);
	    }
	}

	return $images->listImages($max);
    }

    function getThumbnailURL($image, $width = 120, $height = NULL) {
	global $wwThumbnailURL;

	if (is_array($image)) $image = $image['name'];

	if (!$height) $height = $width;

	$u = $wwThumbnailURL;
	$u = str_replace("{name}", urlencode($image), $u);
	$u = str_replace("{width}", !$width ? "" : urlencode($width), $u);
	$u = str_replace("{height}", !$height ? "" : urlencode($height), $u);

	return $u;
    }

    function getImagePageURL($image) {
	global $wwImagePageURL;

	if (is_array($image)) $image = $image['name'];

	$u = $wwImagePageURL;
	$u = str_replace("{name}", urlencode($image), $u);

	return $u;
    }

    function getThumbnailHTML($image, $w = 120, $h = NULL) {
	$thumb = $this->getThumbnailURL($image, $w, $h);
	$page = $this->getImagePageURL($image);

	if (is_array($image)) {
	    $title = @$image['title'];
	    $name = @$image['name'];
	} else {
	    $name = $image;
	}

	if (!@$title) $title = $name;

	$html= "<img src=\"" . htmlspecialchars($thumb) . "\" alt=\"" . htmlspecialchars($title) . "\" border=\"0\"/>";
	$html= "<a href=\"" . htmlspecialchars($page) . "\" title=\"" . htmlspecialchars($title) . "\">$html</a>";

	if (is_array($image)) {
	    $html .= "<!-- " . htmlspecialchars( str_replace("--", "~~", var_export( $image, true ) ) ) . " -->";
	}

	return $html;
    }
}
