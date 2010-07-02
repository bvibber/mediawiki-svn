<?php
require_once(dirname(__FILE__)."/wwutils.php");

	/** Unknown type, SHOULD not occurr in final data. MAY be used for
	 * resources that are referenced but where not available for analysis,
	 * or have not yet been analyzed. 
	 **/
	define('WW_RC_TYPE_UNKNOWN', 0);
	
	/**
	 * A "real" page, describing a concept.
	 */
	define('WW_RC_TYPE_ARTICLE', 10);
	
	/**
	 * This page is a supplemental part of an article, typically a transcluded
	 * subpage or simmilar.   
	 */
	define('WW_RC_TYPE_SUPPLEMENT', 15);
	
	
	/**
	 * A page solely defining a redirect/alias for another page
	 */
	define('WW_RC_TYPE_REDIRECT', 20);

	/**
	 * A disambuguation page, listing different meanings for the page title, 
	 * each linking to a article page.
	 */
	define('WW_RC_TYPE_DISAMBIG', 30);
	
	/**
	 * A page that contains a list of concepts that share some common property or quality,
	 * usually each linking to a page describing that concept.
	 */
	define('WW_RC_TYPE_LIST', 40);
	
	/**
	 * A category page.
	 */
	define('WW_RC_TYPE_CATEGORY', 50);
	
	/**
	 * This page does not contain relevant information for WikiWord
	 */
	define('WW_RC_TYPE_OTHER', 99);
	
	/**
	 * A page that is broken in some way, or was marked as bad or disputed. Such pages
	 * SHOULD generally be treated as if theys didn't exist.
	 */
	define('WW_RC_TYPE_BAD', 100);
	
	/**
	 * A resource that is not a page by itself, but merely a section of a page. Sections
	 * SHOULD always be part of a page of type ARTICLE, and are expected to descibe
	 * a narrower concept than the "parent" page.
	 */
	define('WW_RC_TYPE_SECTION', 200);


class WWThesaurus extends WWUTils {

    function normalizeSearchString( $s, $norm = 1 ) {
	if ( $norm >= 1 ) $s = mb_strtolower($s, "utf-8");
	if ( $norm >= 1 ) $s = str_replace("-", "", $s);

	#TODO: 2: whitespace and punctuation
	#TODO: 3: translit

	return $s;
    }

    function queryConceptsForTerm($qlang, $term, $languages, $norm = 1, $rclang = null, $limit = 100) {
	global $wwTablePrefix, $wwThesaurusDataset, $wwLanguages;

	if ( !$languages ) $languages = array_keys( $wwLanguages );

	$term = $this->normalizeSearchString($term, $norm);

	$sql = "SELECT I.*, S.score ";
	if ( $rclang ) $sql .= ", R.resources ";

	$sql .= " FROM {$wwTablePrefix}_{$wwThesaurusDataset}_concept_info as I ";
	$sql .= " JOIN {$wwTablePrefix}_{$wwThesaurusDataset}_search_index as S ON I.concept = S.concept ";
	if ( $rclang ) $sql .= " JOIN {$wwTablePrefix}_{$wwThesaurusDataset}_resource_index as R ON R.concept = I.concept ";

	$sql .= " WHERE term = " . $this->quote($term) 
	      . " AND I.lang IN " . $this->quoteSet($languages) 
	      . " AND S.lang = " . $this->quote($qlang) 
	      . " AND S.norm <= " . (int)$norm;

	$sql .= " ORDER BY S.score DESC, S.concept "
	      . " LIMIT " . (int)$limit;

	#FIXME: query-lang vs. output-languages!

	return $this->query($sql);
    }

    function getConceptsForTerm($qlang, $term, $languages, $norm = 1, $rclang = null, $limit = 100) {
	$rs = $this->queryConceptsForTerm($qlang, $term, $languages, $norm, $rclang, $limit);
	$list = WWUtils::slurpRows($rs);
	mysql_free_result($rs);
	return $this->buildConcepts($list);
    }
 
   function getPagesForConcept( $id, $lang = null ) {
	$p = $this->getConceptInfo( $id, $lang );
	return $p['pages'];
    }

    /*
    function queryConceptsForPage($lang, $page, $limit = 100) {
	global $wwTablePrefix, $wwThesaurusDataset;

	$page = trim($page);

	$sql = "SELECT O.global_concept as id, O.* FROM {$wwTablePrefix}_{$lang}_resource as R "
	      . " JOIN {$wwTablePrefix}_{$lang}_about as A ON A.resource = R.id "
	      . " JOIN {$wwTablePrefix}_{$wwThesaurusDataset}_origin as O ON O.lang = \"" . mysql_real_escape_string($lang) . "\" AND A.concept = O.local_concept "
	      . " WHERE R.name = \"" . mysql_real_escape_string($page) . "\""
	      . " LIMIT " . (int)$limit;

	return $this->query($sql);
    }

    function getConceptsForPage($lang, $page, $limit = 100) {
	$rs = $this->queryConceptsForPage($lang, $page);
	$list = WWUtils::slurpRows($rs);
	mysql_free_result($rs);
	return $list;
    }

    function queryLocalConcepts($id) {
	global $wwTablePrefix, $wwThesaurusDataset;
	$sql = "SELECT O.lang, O.local_concept_name from {$wwTablePrefix}_{$wwThesaurusDataset}_origin as O ";
	$sql .= " WHERE O.global_concept = " . (int)$id;

	return $this->query($sql);
    }

    function getLocalConcepts($id) { //NOTE: deprecated alias for backward compat
	return getPagesForConcept($id);
    } */

    /*
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
    }*/

    /*function getConceptInfo( $id, $lang = null ) {
	$result = $this->getConcept($id, $lang);

	$result['broader'] = $this->getBroaderForConcept($id);
	$result['narrower'] = $this->getNarrowerForConcept($id);
	$result['related'] = $this->getRelatedForConcept($id);

	if ( $lang ) {
	    $d = $this->getDefinitionForConcept($id);
	    $result['related'] = $d[$lang];
	}

	return $result;
    }*/

    /*
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
    */

    function spliceResources( $rc, &$into ) {
	if (!$rc) return;

	if (is_string($rc)) {
		$rr = explode("|", $rc);

		$rc = array();
		foreach ($rr as $r) {
		    list($t, $lang, $n) = explode(":", $p, 3);
		    $rc[$lang][$n] = (int)$t;
		}
	}

	if (!$into) $into = $rc;
	else $into = array_merge( $into, $rc );
    }

    function splitPages( $s ) {
	$pp = explode("|", $s);

	$pages = array();
	foreach ($pp as $p) {
	    list($t, $n) = explode(":", $p, 2);
	    $pages[$n] = (int)$t;
	}

	return $pages;
    }

    function splitConcepts( $s ) {
	$ss = explode("|", $s);

	$concepts = array();
	foreach ($ss as $p) {
	    list($id, $n) = explode(":", $p, 2);
	    $id = (int)$id;
	    $concepts[$id] = $n;
	}

	return $concepts;
    }

    /////////////////////////////////////////////////////////
    function getConceptInfo( $id, $lang = null, $fields = null, $rclang = null ) {
	global $wwTablePrefix, $wwThesaurusDataset;

	#TODO: concept cache!

	if ( $fields && is_array($fields)) $fields = implode(", ", $fields);
	if ( !$fields ) $fields = "*";

	#TODO: scores, concept-type, ...

	$sql = "SELECT $fields ";
	if ( $rclang ) $sql .= ", R.resources ";
	  
	$sql .= " FROM {$wwTablePrefix}_{$wwThesaurusDataset}_concept_info as I ";
	if ( $rclang ) $sql .= " JOIN {$wwTablePrefix}_{$wwThesaurusDataset}_resource_index as R ON R.concept = I.concept ";

	$sql .= " WHERE concept = ".(int)$id;

	if ($lang) {
	    if ( is_array($lang) ) $sql .= " AND lang IN " . $this->quoteSet($lang);
	    else $sql .= " AND lang = " . $this->quote($lang);
	}

	$r = $this->getRows($sql);
	if (!$r) return false;

	return $this->buildConcept($r);
    }

    function buildConcepts($rows) {
	$concepts = array();
	$buff = array();
	$id = null;
	foreach($rows as $row) {
	    if ( $id !== null && $id != $row['concept'] ) {
		if ($buff) {
			$concepts[$id] = $this->buildConcept($buff);
			$buff = array();
		}

		$id = null;
		$score = null;
	    }

	    if ($id === null) {
		$id = (int)$row['concept'];
	    }

	    $buff[] = $row;
	}

	if ($buff) {
		$concepts[$id] = $this->buildConcept($buff);
		$buff = array();
	}

	usort($concepts, array('WWThesaurus', 'byScore'));
	return $concepts;
    }

    function byScore( $a, $b ) {
	if ( isset($a['score']) && isset($b['score']) ) return $b['score'] - $a['score'];
	if ( isset($a['conf']) && isset($b['conf']) ) return $b['conf'] - $a['conf'];
	if ( isset($a['freq']) && isset($b['freq']) ) return $b['freq'] - $a['freq'];
	if ( isset($a['id']) && isset($b['id']) ) return $a['id'] - $b['id'];
	return 0;
    }

    function buildConcept($rows) {
	$concept = array();
	$concept["languages"] = array();

	$broader = array();
	$narrower = array();
	$similar = array();
	$related = array();

	foreach ($rows as $row) {
	    if (!isset($concept["id"]) && isset($row["concept"])) $concept["id"] = (int)$row["concept"];
	    if (!isset($concept["score"]) && isset($row["score"])) $concept["score"] = (int)$row["score"];

	    $lang = $row["lang"];
	    $concept["languages"][] = $lang;

	    #TODO: scores, concept-type, ...

	    if (@$row["name"] !== null) $concept["name"][$lang] = $row["name"];
	    if (@$row["definition"] !== null) $concept["definition"][$lang] = $row["definition"];

	    if (@$row["resources"])  $this->spliceResources($row["resources"], &$concept["pages"]);
	    else if (@$row["pages"]) $concept["pages"][$lang] = $this->splitPages($row["pages"]);

	    if (@$row["broader"] !== null)  $broader[$lang] =  $this->splitConcepts($row["broader"]);
	    if (@$row["narrower"] !== null) $narrower[$lang] = $this->splitConcepts($row["narrower"]);
	    if (@$row["similar"] !== null)  $similar[$lang] =  $this->splitConcepts($row["similar"]);
	    if (@$row["related"] !== null)  $related[$lang] =  $this->splitConcepts($row["related"]);
	}

	$concept["broader"] =  $this->mogrifyLocalInfo($broader);
	$concept["narrower"] = $this->mogrifyLocalInfo($narrower);
	$concept["similar"] =  $this->mogrifyLocalInfo($similar);
	$concept["related"] =  $this->mogrifyLocalInfo($related);

	return $concept;
    }

    function mogrifyLocalInfo( $byLanguage ) {
	$byId = array();
	
	foreach ( $byLanguage as $lang => $items ) {
	    foreach ( $items as $id => $name ) {
		if (!isset($byId[$id])) {
		    $byId[$id] = array(
			'id'  => $id,
			'name' => array()
		    );
		}

		$byId[$id]['name'][$lang] = $name;
	    }
	}

	return $byId;
    }

    /*
    function getConcept( $id, $lang = null, $limit = 100 ) {
	return $this->getConceptInfo($id, $lang);
    }

    function getRelatedForConcept( $id, $lang = null, $limit = 100 ) {
	$concept = $this->getConceptInfo($id, $lang, "lang, related");
	if (!$concept) return false;
	else if ($lang) return $concept["related"][$lang];
	else return $concept["related"];
    }

    function getBroaderForConcept( $id, $lang = null, $limit = 100 ) {
	$concept = $this->getConceptInfo($id, $lang, "lang, broader");
	if (!$concept) return false;
	else if ($lang) return $concept["broader"][$lang];
	else return $concept["broader"];
    }

    function getNarrowerForConcept( $id, $lang = null, $limit = 100 ) {
	$concept = $this->getConceptInfo($id, $lang, "lang, narrower");
	if (!$concept) return false;
	else if ($lang) return $concept["narrower"][$lang];
	else return $concept["narrower"];
    }

    function getPagesForConcept( $id, $lang = null, $limit = 100 ) {
	if (!$lang) return false;

	$concept = $this->getConceptInfo($id, $lang, "lang, pages");
	if (!$concept) return false;
	else if ($lang) return array_keys( $concept["pages"][$lang] );
    }

    function getNamesForConcept( $id, $lang = null ) {
	$concept = $this->getConceptInfo($id, $lang, "lang, name");
	if (!$concept) return false;

	$result = array();
	foreach ($concept["languages"] as $ll) {
		if (@$concept["name@$ll"]) 
			$result[$ll] = $concept["name@$ll"];
	}

	return $result;
    }

    function getDefinitionForConcept( $id, $lang = null, $limit = 100 ) {
	$concept = $this->getConceptInfo($id, $lang, "lang, definition");
	if (!$concept) return false;

	$result = array();
	foreach ($concept["languages"] as $ll) {
		if (@$concept["definition@$ll"]) 
			$result[$ll] = $concept["definition@$ll"];
	}

	return $result;
    }*/

    function getTermsForConcept( $id, $lang = null, $limit = 100 ) {
	global $wwTablePrefix, $wwThesaurusDataset, $wwLanguages;

	if ( !$lang ) $lang = array_keys( $wwLanguages );
	if ( !is_array($lang) ) $lang = preg_split('![\\s,;|/:]\\s*!', $lang);
	$result = array();

	foreach ($lang as $ll) {
	    $sql = "SELECT M.term_text FROM {$wwTablePrefix}_{$ll}_meaning as M"
		  . " JOIN {$wwTablePrefix}_{$wwThesaurusDataset}_origin as O ON O.lang = \"" . mysql_real_escape_string($ll) . "\" AND M.concept = O.local_concept "
		  . " WHERE O.global_concept = " . (int)$id
		  . " ORDER BY freq DESC "
		  . " LIMIT " . (int)$limit;

	    $terms = $this->getList($sql, "term_text");
	    if ( $terms === false || $terms === null ) return false;
	    if ( !$terms ) continue;

	    $result[$ll] = $terms;
	}

	return $result;
    }

    /*
    function getLinksForConcept( $id, $lang = null, $limit = 100 ) {
	global $wwTablePrefix, $wwThesaurusDataset;

	$sql = "SELECT C.* FROM {$wwTablePrefix}_{$wwThesaurusDataset}_concept as C "
	      . " JOIN  {$wwTablePrefix}_{$wwThesaurusDataset}_link as L ON L.target = C.id "
	      . " WHERE L.anchor = ".(int)$id
	      . " LIMIT " . (int)$limit;

	return $this->getRows($sql);
    }

    function getReferencesForConcept( $id, $lang = null, $limit = 100 ) {
	global $wwTablePrefix, $wwThesaurusDataset;

	$sql = "SELECT C.* FROM {$wwTablePrefix}_{$wwThesaurusDataset}_concept as C "
	      . " JOIN  {$wwTablePrefix}_{$wwThesaurusDataset}_link as L ON L.anchor = C.id "
	      . " WHERE L.target = ".(int)$id
	      . " LIMIT " . (int)$limit;

	return $this->getRows($sql);
    }

    function getScoresForConcept( $id, $lang = null ) {
	global $wwTablePrefix, $wwThesaurusDataset;

	$sql = "SELECT S.* FROM {$wwTablePrefix}_{$wwThesaurusDataset}_concept_stats as S "
	      . " WHERE S.concept = ".(int)$id
	    ;

	$r = $this->getRows($sql);
	if ( !$r ) return false;

	return $r;
    }*/

}
