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

    function addImageUsage($image, $key, $usage = "page", $weight = 1) {
	$this->addImage($image, $key, $usage, $weight);
    }

    function addTags($image, $tags, $prefix = "") {
	global $wwTagScores;

	if (isset($this->images[$image])) {
	    foreach ($tags as $tag => $weight) {
		if (is_int($tag)) {
		    $tag = $prefix.$weight;

		    if (isset($wwTagScores[$tag])) $weight = $wwTagScores[$tag];
		    else $weight = 0;
		} else {
		    $tag = $prefix.$tag;
		}

		$this->images[$image]['score'] += $weight;
		$this->images[$image]['tags'][] = $tag;
	    }
	}
    }

    function addMeta($image, $meta) {
	if (isset($this->images[$image])) {
	    $this->images[$image]['meta'] = $meta;
	}
    }

}

class WWImages extends WWWikis {
    var $thesaurus;

    function __construct($thesaurus) {
	$this->thesaurus = $thesaurus;
	if ( !empty( $this->thesaurus->db ) ) $this->db = $thesaurus->db;
    }

    function queryImagesOnPage($lang, $ns, $title, $commonsOnly = false) {
	if ($lang == "commons") $commonsOnly = false;

	$imagelinks_table = $this->getWikiTableName($lang, "imagelinks");
	$page_table = $this->getWikiTableName($lang, "page");
	$image_table = $this->getWikiTableName($lang, "image");
	$commons_image_table = $this->getWikiTableName("commons", "image");

	$sql = "/* queryImagesOnPage(" . $this->quote($lang) . ", " . (int)$ns . ", " . $this->quote($title) . ", " . (int)$commonsOnly . ") */ ";

	$sql .= " SELECT I.il_to as name FROM $imagelinks_table as I ";
	$sql .= " JOIN $page_table as P on P.page_id = I.il_from ";
	if ($commonsOnly) $sql .= " LEFT JOIN $image_table as R on R.img_name = I.il_to ";
	if ($commonsOnly) $sql .= " JOIN $commons_image_table as C on C.img_name = I.il_to ";
	
	$sql .= " WHERE P.page_namespace = " . (int)$ns;
	$sql .= " AND P.page_title = " . $this->quote($title);
	if ($commonsOnly) $sql .= " AND R.img_name IS NULL";

	return $this->queryWikiFast($lang, $sql);
    }

    function getImagesOnPage($lang, $ns, $title, $commonsOnly = false) {
	$rs = $this->queryImagesOnPage($lang, $ns, $title, $commonsOnly);

	$list = WWUtils::slurpList($rs, "name");
	mysql_free_result($rs);

	return $list;
    }

    function queryImagesOnPageTemplates($lang, $ns, $title, $commonsOnly = false) {
	if ($lang == "commons") $commonsOnly = false;

	$imagelinks_table = $this->getWikiTableName($lang, "imagelinks");
	$page_table = $this->getWikiTableName($lang, "page");
	$image_table = $this->getWikiTableName($lang, "image");
	$commons_image_table = $this->getWikiTableName("commons", "image");
	$templatelinks_table = $this->getWikiTableName($lang, "templatelinks");

	$sql = "/* queryImagesOnPageTemplates(" . $this->quote($lang) . ", " . (int)$ns . ", " . $this->quote($title) . ", " . (int)$commonsOnly . ") */ ";

	$sql .= " SELECT I.il_to as name FROM $imagelinks_table as I ";
	$sql .= " JOIN $page_table as TP on TP.page_id = I.il_from ";
	$sql .= " JOIN $templatelinks_table as T on T.tl_namespace = TP.page_namespace AND T.tl_title = TP.page_title ";
	$sql .= " JOIN $page_table as P on P.page_id = T.tl_from ";
	if ($commonsOnly) $sql .= " LEFT JOIN $image_table as R on R.img_name = I.il_to ";
	if ($commonsOnly) $sql .= " JOIN $commons_image_table as C on C.img_name = I.il_to ";

	$sql .= " WHERE P.page_namespace = " . (int)$ns;
	$sql .= " AND P.page_title = " . $this->quote($title);
	if ($commonsOnly) $sql .= " AND R.img_name IS NULL";

	return $this->queryWikiFast($lang, $sql);
    }

    function getImagesOnPageTemplates($lang, $ns, $title, $commonsOnly = false) {
	$rs = $this->queryImagesOnPageTemplates($lang, $ns, $title, $commonsOnly);
	$list = WWUtils::slurpList($rs, "name");
	mysql_free_result($rs);
	return $list;
    }

    function queryImagesInCategory($lang, $title) {
	$categorylinks_table = $this->getWikiTableName($lang, "categorylinks");
	$page_table = $this->getWikiTableName($lang, "page");

	$sql = "/* queryImagesInCategory(" . $this->quote($lang) . ", " . $this->quote($title) . ") */ ";

	$sql .= " SELECT P.page_title as name FROM $page_table as P ";
	$sql .= " JOIN $categorylinks_table as C on C.cl_from = P.page_id ";

	$sql .= " WHERE C.cl_to = " . $this->quote($title);
	$sql .= " AND P.page_namespace = " . NS_IMAGE;

	return $this->queryWikiFast($lang, $sql);
    }

    function getImagesInCategory($lang, $title) {
	$rs = $this->queryImagesInCategory($lang, $title);
	$list = WWUtils::slurpList($rs, "name");
	mysql_free_result($rs);
	return $list;
    }

    function queryTagsForImages($lang, $images, $tagTable) {
	if (!$images) return false;

	$sql = "/* queryTagsForImages(" . $this->quote($lang) . ", " . $this->quoteSet($images) . ") */ ";

	$sql .= " SELECT image, group_concat( concat(type, ':', tag) separator '|') as tags ";
 	$sql .= " FROM $tagTable as T ";
	$sql .= " WHERE T.image IN " . $this->quoteSet($images);
 	$sql .= " GROUP BY image ";

	return $this->queryWiki($lang, $sql);
    }

    function getTagsForImages($lang, $images, $tagTable) {
	if (!$images) return array();

	$rs = $this->queryTagsForImages($lang, $images, $tagTable);
	$list = WWUtils::slurpAssoc($rs, "image", "tags");
	mysql_free_result($rs);
	return $list;
    }

    function queryMetaForImages($lang, $images) {
	if (!$images) return false;

	$image_table = $this->getWikiTableName($lang, "image");

	$sql = "/* queryMetaForImages(" . $this->quote($lang) . ", " . $this->quoteSet($images) . ") */ ";

	$sql .= " SELECT img_name, img_size, img_width, img_height, img_media_type, img_major_mime, img_minor_mime, img_timestamp, img_sha1 ";
 	$sql .= " FROM $image_table as T ";
	$sql .= " WHERE T.img_name IN " . $this->quoteSet($images);

	return $this->queryWikiFast($lang, $sql);
    }

    function getMetaForImages($lang, $images) {
	if (!$images) return array();

	$rs = $this->queryMetaForImages($lang, $images);
	$list = WWUtils::slurpRows($rs, "img_name");
	mysql_free_result($rs);
	return $list;
    }

    function queryTemplatesOnImagePage($lang, $image) {
	$page_table = $this->getWikiTableName($lang, "page");
	$templatelinks_table = $this->getWikiTableName($lang, "templatelinks");

	$sql = "/* queryTemplatesOnImagePage(" . $this->quote($lang) . ", " . $this->quote($image) . ") */ ";

	$sql .= " SELECT tl_title as template FROM $templatelinks_table as T ";
	$sql .= " JOIN $page_table as P on P.page_id = T.tl_from AND T.tl_namespace = " . NS_TEMPLATE . " ";

	$sql .= " WHERE P.page_title = " . $this->quote($image);
	$sql .= " AND P.page_namespace = " . NS_IMAGE;

	return $this->queryWikiFast($lang, $sql);
    }

    function getTemplatesOnImagePage($lang, $image) {
	$rs = $this->queryTemplatesOnImagePage($lang, $image);
	$list = WWUtils::slurpList($rs, "template");
	mysql_free_result($rs);
	return $list;
    }

    function queryCategoriesOfImagePage($lang, $image) {
	$page_table = $this->getWikiTableName($lang, "page");
	$categorylinks_table = $this->getWikiTableName($lang, "categorylinks");

	$sql = "/* queryCategoriesOfImagePage(" . $this->quote($lang) . ", " . $this->quote($image) . ") */ ";

	$sql .= " SELECT cl_to as category FROM $categorylinks_table as C ";
	$sql .= " JOIN $page_table as P on P.page_id = C.cl_from ";

	$sql .= " WHERE P.page_title = " . $this->quote($image);
	$sql .= " AND P.page_namespace = " . NS_IMAGE;

	return $this->queryWikiFast($lang, $sql);
    }

    function getCategoriesOfImagePage($lang, $image) {
	$rs = $this->queryCategoriesOfImagePage($lang, $image);
	$list = WWUtils::slurpList($rs, "category");
	mysql_free_result($rs);
	return $list;
    }

    function getTemplateScores($templates, $values = NULL) {
	global $wwTemplateScores;
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

    function queryImagesOnPagesGlobally( $concepts ) {
	global $wwLanguages;

	if (!$concepts) return false;

	$globalimagelinks_table = $this->getWikiTableName("commons", "globalimagelinks");

	$wikis = array();
	$pages = array();
	$pagesText = "";

	foreach ($concepts as $lang => $rc) {
	    if (!isset($wwLanguages[$lang])) continue;

	    $wiki = $lang . "wiki";
	    if (!in_array($wiki, $wikis)) $wikis[] = $wiki;

	    foreach ($rc as $r => $t) {
		if ( $t != 10) continue; //use only articles
		$p = "gil_wiki = " . $this->quote($wiki) . " AND gil_page_namespace_id = 0 AND gil_page_title = " . $this->quote($r);
		$pages[] = $p;

		if ($pagesText) $pagesText .= ", ";
		$pagesText .= $wiki . ':' . str_replace('*', '_', $r);
	    }
	}

	if (!$pages || !$wikis) return false;

	$sql = " /* queryImagesOnPagesGlobally($pagesText) */ ";
	$sql .= " SELECT distinct gil_to as image FROM $globalimagelinks_table ";
	$sql .= " WHERE gil_wiki in " . $this->quoteSet( $wikis );
	$sql .= " AND gil_page_namespace_id = 0 ";
	$sql .= " AND ( ( " . implode(" ) OR ( ", $pages) . " ) ) ";

	return $this->queryWiki("commons", $sql);
    }

    function getImagesOnPagesGlobally( $concepts ) {
	if (!$concepts) return array();

	$rs = $this->queryImagesOnPagesGlobally($concepts);
	if (!$rs) return false;

	$list = WWUtils::slurpList($rs, "image");
	mysql_free_result($rs);
	return $list;
    }

    function queryGlobalUsageCounts( $images, $wikis = null ) {
	global $wwUsageTable;

	if (!$images) return false;

	if ( $wwUsageTable ) {
		$sql = " /* queryGlobalUsageCounts(" . str_replace('*', '_', implode(', ', $images) ) . ") */ ";
		$sql .= " SELECT image, wiki, linkcount FROM $wwUsageTable ";
		$sql .= " WHERE image IN " . $this->quoteSet( $images );

		if ( $wikis ) {
		    if ( is_array( $wikis ) ) $sql .= " AND wiki IN " . $this->quoteSet( $wikis );
		    else if ( $wikis ) $sql .= " AND wiki RLIKE " . $this->quote( '^' . $wikis . '$' );

		    #TODO: could also limit to to x or min size n using toolserver.wiki !
		}

		return $this->queryWiki("commons", $sql);
	} else {
		$globalimagelinks_table = $this->getWikiTableName("commons", "globalimagelinks");

		$sql = " /* queryGlobalUsageCounts(" . str_replace('*', '_', implode(', ', $images) ) . ") */ ";
		$sql .= " SELECT gil_to as image, gil_wiki as wiki, count(*) as linkcount FROM $globalimagelinks_table ";
		$sql .= " WHERE gil_page_namespace_id = 0 ";
		$sql .= " AND gil_to IN " . $this->quoteSet( $images );

		if ( $wikis ) {
		    if ( is_array( $wikis ) ) $sql .= " AND gil_wiki IN " . $this->quoteSet( $wikis );
		    else if ( $wikis ) $sql .= " AND gil_wiki RLIKE " . $this->quote( '^' . $wikis . '$' );

		    #TODO: could also limit to to x or min size n using toolserver.wiki !
		}

		$sql .= " GROUP BY gil_to, gil_wiki ";
	
		return $this->queryWikiFast("commons", $sql);
	}
    }

    function getGlobalUsageCounts( $images, $wikis = null ) {
	if (!$images) return array();

	$rs = $this->queryGlobalUsageCounts($images, $wikis);
	if (!$rs) return false;

	if (is_string($rs)) $rs = $this->query($rs);

	$imageUsage = array();
	while ($row = mysql_fetch_assoc($rs)) {
	    $image = $row["image"];
	    $wiki = $row["wiki"];
	    $linkcount = $row["linkcount"];

	    $imageUsage[$image][$wiki] = $linkcount;
	}

	$images = array_keys( $imageUsage );
	foreach ($images as $image) {
	    $imageUsage[$image]['*max*'] = array_reduce( $imageUsage[$image], 'max', 0 );
	}

	return $imageUsage;
    }

    function getImagesAbout($concept, $max = 0) {
	global $wwLanguages, $wwFrequentImageThreshold; //FIXME: put config into member vars!

	$pages = null;
	if ( is_array($concept) ) {
		if (isset($concept['pages']) && $concept['pages']!==null) $pages = $concept['pages'];
		else if (isset($concept['id']) && $concept['id']!==null) $concept = $concept['id'];
		else if (isset($concept['concept'])) $concept = $concept['concept'];
	} 

	if ($pages === null) {
		$pages = $this->thesaurus->getPagesForConcept($concept);
		if (!$pages) return false;
	}

	$images = new ImageCollection();

	$globalImageList = $this->getImagesOnPagesGlobally($pages); //use wikis for $wwLanguages only

	//TODO: sanity limit on number of images. $max * 5 ?
	//$globalImageUsage = $this->getGlobalUsageCounts($globalImageList, ".*wiki"); //use all wikipedias
	$globalImageUsage = $this->getGlobalUsageCounts($globalImageList, null); //use all wikis
	//FIXME:  getGlobalUsageCounts is SLOW!!!

	foreach ($globalImageUsage as $image => $usage) {
	    $m = @$usage['*max*'];
	    if ( $m >= $wwFrequentImageThreshold ) continue;

	    foreach ($usage as $wiki => $c) { //FIXME: only count usage on *RELEVANT* pages.
		$images->addImageUsage($image, $wiki.":*", "article", 1);
	    }
	}

	if ($max && $images->size()>$max) { //short-cirquit, if we already reached the max
	    $this->addImageMeta($images);
	    $this->addImageTags($images);
	    return $images->listImages($max);
	}

	if (isset($pages['commons'])) {
	    $cpages = $pages['commons'];

	    foreach ($cpages as $cpage => $t) {
		if ( $t == 50 && preg_match('/^Category:(.*)$/', $cpage, $m) ) {
		    if ( @$m[1] ) $cpage = $m[1]; //hack
		    $img = $this->getImagesInCategory("commons", $cpage); 

		    if ($img) $images->addImages($img, "commons:category:" . $cpage, "category", 0.5);
		}
	    }
	}

	$this->addImageMeta($images);
	$this->addImageTags($images);
	return $images->listImages($max);
    }

    function addImageTags($images) {
	global $wwTagsTable;


	if ( $wwTagsTable ) {
		$img = array();
		foreach ($images->images as $image) {
			$img[] = $image['name'];
		}

		$tagMap = $this->getTagsForImages('commons', $img, $wwTagsTable);
		foreach ($tagMap as $image => $tags) {
			if ($tags) {
				if (is_string($tags)) $tags = preg_split('/\s*[|;]\s*/', $tags);
				$images->addTags($image, $tags, "");
			}
		}
	} else {
		foreach ($images->images as $image) {
			$image = $image['name'];

			$tmps = $this->getTemplatesOnImagePage('commons', $image);
			if ($tmps) $images->addTags($image, $tmps, "Template:");
			
			$cats = $this->getCategoriesOfImagePage('commons', $image);
			if ($cats) $images->addTags($image, $cats, "Category:");
		}
	}
    }

    function addImageMeta($images) {
	$img = array();
	foreach ($images->images as $image) {
		$img[] = $image['name'];
	}

	$infoMap = $this->getMetaForImages('commons', $img);
	foreach ($infoMap as $image => $meta) {
		$images->addMeta($image, $meta, "");
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

	$alt = str_replace('_', ' ', $name);

	$tags = "";
	if (isset($image['tags'])) {
	    foreach ($image['tags'] as $tag) {
		$tags .= "tag-" . str_replace(":", "-", $tag) . " ";
	    }
	}

	$isImage = true;

	if (isset($image['meta']['img_media_type'])) {
	    if ($image['meta']['img_media_type'] != "BITMAP" && $image['meta']['img_media_type'] != "DRAWING") $isImage = false;
	} else {
	    if (!preg_match('/\.(png|jpe?g|gif|bmp|tiff?|svg|djvu?)$/i', $name)) $isImage = false;
	}

	if ($isImage) $html= "<img src=\"" . htmlspecialchars($thumb) . "\" alt=\"" . htmlspecialchars($alt) . "\" border=\"0\"/>";
	else $html= htmlspecialchars($alt); //TODO: use icon!

	$html= "<a href=\"" . htmlspecialchars($page) . "\" title=\"" . htmlspecialchars($title) . " (score " . htmlspecialchars($image['score']) . ")\" class=\"thumb-link $tags\">$html</a>";

	if (is_array($image)) {
	    $html .= "<!-- " . str_replace("--", "~~", var_export( $image, true ) ) . " -->";
	}

	return $html;
    }
}

class WWFakeImages extends WWImages {

    function __construct($thesaurus) {
	WWImages::__construct($thesaurus);
	
	$this->somePics = array(
		"Carrots_of_many_colors.jpg",
		"Infantryman_in_1942_with_M1_Garand,_Fort_Knox,_KY.jpg",
		"Wilhelmshaven_Meerjungfrau.jpg",
		"Sunrise_over_Veterans_Park_2420.jpg",
		"Halde_Rheinpreußen,_Grubenlampe,_III_retouched.jpg",
	);

	$this->morePics = array(
		"Yellow_Tiger_Moth_Góraszka.JPG",
		"Mauritania_boy1.jpg",
		"Mavericks_Surf_Contest_2010b.jpg",
		"Sellers_translating_along_truss_during_EVA-3_on_STS-121.jpg",
		"Fruitbowlwithmelons.jpg",
	);

	$this->fewPics = array(
		$this->somePics[0],
		$this->morePics[0],
	);

	$this->allPics = array_merge($this->somePics, $this->morePics);
    }

    function queryImagesOnPage($lang, $ns, $title, $commonsOnly = false) {
	throw new Exception( __METHOD__ . " not implemented" );
    }

    function getImagesOnPage($lang, $ns, $title, $commonsOnly = false) {
	return $this->somePics;
    }

    function queryImagesOnPageTemplates($lang, $ns, $title, $commonsOnly = false) {
	throw new Exception( __METHOD__ . " not implemented" );
    }

    function getImagesOnPageTemplates($lang, $ns, $title, $commonsOnly = false) {
	return $this->fewPics;
    }

    function queryImagesInCategory($lang, $title) {
	throw new Exception( __METHOD__ . " not implemented" );
    }

    function getImagesInCategory($lang, $title) {
	return $this->allPics;
    }

    function queryTagsForImages($lang, $images, $tagTable) {
	throw new Exception( __METHOD__ . " not implemented" );
    }

    function getTagsForImages($lang, $images, $tagTable) {
	$r = array();
	foreach ($images as $img) {
		$r[$img] = array("assessment:Featured_picture", "license:Cc-by-sa");
	}
	
	return $r;
    }

    function getMetaForImages($lang, $images) {
	$r = array();
	foreach ($images as $img) {
		$r[$img] = array(
			'img_name' => $img,
			'img_size' => 123456,
			'img_width' => 300,
			'img_height' => 200,
			'img_media_type' => 'BITMAP',
			'img_major_mime' => 'image',
			'img_minor_mime' => 'jpeg',
			'img_timestamp' => '20090808132422',
		);
	}
	
	return $r;
    }


    function queryTemplatesOnImagePage($lang, $image) {
	throw new Exception( __METHOD__ . " not implemented" );
    }

    function getTemplatesOnImagePage($lang, $image) {
	return array("Cc-by-sa", "Featured_picture");
    }

    function queryCategoriesOfImagePage($lang, $image) {
	throw new Exception( __METHOD__ . " not implemented" );
    }

    function getCategoriesOfImagePage($lang, $image) {
	return array("Splorg", "Snork");
    }

    function queryImagesOnPagesGlobally( $concepts ) {
	throw new Exception( __METHOD__ . " not implemented" );
    }

    function getImagesOnPagesGlobally( $concepts ) {
	return $this->allPics;
    }

    function queryGlobalUsageCounts( $images, $wikis = null ) {
	throw new Exception( __METHOD__ . " not implemented" );
    }

    function getGlobalUsageCounts( $images, $wikis = null ) {
	if (!$images) return array();

	foreach ($images as $current) {
	    $imageUsage[$current] = array(
		"xx" => 2,
		"yy" => 3,
		"zz" => 6,
		"*max*" => 6 
	    );
	}

	return $imageUsage;
    }
}