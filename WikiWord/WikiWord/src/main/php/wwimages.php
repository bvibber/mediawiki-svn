<?php
require_once(dirname(__FILE__)."/wwwikis.php");

class ImageCollection {

    function __construct() {
	$this->images = array();
    }

    static function compareRecords($a, $b) {
	$d = (float)$b['score'] - (float)$a['score']; //NOTE: descending

	if ( $d > 0 ) return 1; 
	else if ( $d < 0 ) return -1; 
	else return 0;
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
		"score" => 0,
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

    function addTags($image, $tags, $prefix = "") {
	global $wwTagScores;

	if (isset($this->images[$image])) {
	    foreach ($tags as $tag => $weight) {
		if (is_int($tag)) {
		    $tag = $prefix.$weight;

		    if (isset($wwTagScores[$tag])) $weight = $wwTagScores[$tag];
		    else continue;
		} else {
		    $tag = $prefix.$tag;
		}

		$this->images[$image]['score'] += $weight;
		$this->images[$image]['tags'][] = $tag;
	    }
	}
    }

}

class WWImages extends WWUtils {
    var $thesaurus;

    function __construct($thesaurus) {
	$this->thesaurus = $thesaurus;
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

    function queryCategoriesOfImagePage($lang, $image) {
	global $wwTablePrefix, $wwThesaurusDataset, $wwCommonsTablePrefix;
	$page_table = $this->getWikiTableName($lang, "page");
	$categorylinks_table = $this->getWikiTableName($lang, "categorylinks");

	$sql = "/* queryCategoriesOfImagePage(" . $this->quote($lang) . ", " . $this->quote($image) . ") */ ";

	$sql .= " SELECT cl_to as category FROM $categorylinks_table as C ";
	$sql .= " JOIN $page_table as P on P.page_id = C.cl_from ";

	$sql .= " WHERE P.page_title = " . $this->quote($image);
	$sql .= " AND P.page_namespace = " . NS_IMAGE;

	return $this->queryWiki($lang, $sql);
    }

    function getCategoriesOfImagePage($lang, $image) {
	$rs = $this->queryCategoriesOfImagePage($lang, $image);
	$list = WWUtils::slurpList($rs, "category");
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
	global $wwFakeCommonsConcepts, $wwFakeCommonsPlural, $wwLanguages;

	$concepts = $this->thesaurus->getLocalConcepts($id);

	if ($wwFakeCommonsConcepts && isset($concepts['en'])) {
	    $concepts['commons'] = @$concepts['en'];
	}

	$images = new ImageCollection();
	
	foreach ($concepts as $lang => $title) {
	    if ($lang == "commons") continue;
	    if (!isset($wwLanguages[$lang])) continue;

	    $img = $this->getRelevantImagesOnPage($lang, 0, $title, true); //FIXME: resource mapping
	    $images->addImages($img, $lang . ":" . $title, "article", 1);
	}

	if ($max && $images->size()>$max) {
	    $this->addImageTags($images);
	    return $images->listImages($max);
	}

	if (isset($concepts['commons'])) {
	    $title = $concepts['commons'];

	    $img = $this->getRelevantImagesOnPage("commons", 0, $title, false); //FIXME: resource mapping
	    $images->addImages($img, "commons:" . $title, "gallery", 0.8);

	    if ($max && $images->size()>$max) {
		$this->addImageTags($images);
		return $images->listImages($max);
	    }

	    $img = $this->getImagesInCategory("commons", $title); //FIXME: resource mapping
	    if ($img) $images->addImages($img, "commons:category:" . $title, "category", 0.5);
	    else if ($wwFakeCommonsConcepts && $wwFakeCommonsPlural && !preg_match('/s$/', $title)) {
		$cname = $title."s";

		$img = $this->getImagesInCategory("commons", $cname); //FIXME: resource mapping
		$images->addImages($img, "commons:category:" . $cname, "category(pl)", 0.5);
	    }
	}

	$this->addImageTags($images);
	return $images->listImages($max);
    }

    function addImageTags($images) {
	foreach ($images->images as $image) {
		$image = $image['name'];

		$tags = $this->getTemplatesOnImagePage('commons', $image);
		$images->addTags($image, $tags, "Template:");

		$cats = $this->getCategoriesOfImagePage('commons', $image);
		$images->addTags($image, $cats, "Category:");
	}
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

	$tags = "";
	if (isset($image['tags'])) {
	    foreach ($image['tags'] as $tag) {
		$tags .= "tag-" . str_replace(":", "-", $tag) . " ";
	    }
	}

	$html= "<img src=\"" . htmlspecialchars($thumb) . "\" alt=\"" . htmlspecialchars($title) . "\" border=\"0\"/>";
	$html= "<a href=\"" . htmlspecialchars($page) . "\" title=\"" . htmlspecialchars($title) . " (score " . htmlspecialchars($image['score']) . ")\" class=\"thumb-link $tags\">$html</a>";

	if (is_array($image)) {
	    $html .= "<!-- " . str_replace("--", "~~", var_export( $image, true ) ) . " -->";
	}

	return $html;
    }
}
