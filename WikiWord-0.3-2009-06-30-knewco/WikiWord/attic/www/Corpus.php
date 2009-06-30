<?
require_once("dermis/XhtmlDerm.php");

$conceptTypeNames = array(
	0 => "UNKNOWN",
	10 =>"PLACE",
	20 => "PERSON",
	30 => "TIME",
	40 => "LIFEFORM",
	50 => "OTHER",
	100 => "NONE",
);

$corpuses = array( //FIXME: from DB!
	'nds.wikipedia.org' => 'nds',
	'nl.wikipedia.org' => 'nl',
);

class ItemRenderer {
	var $corpus;
	var $printerFunction;
	
	function __construct($corpus, $printerFunction) {
		$this->corpus = $corpus;
		$this->printerFunction = $printerFunction;
		
		$this->blockTag = NULL;
		$this->itemSeparator = NULL;
	}
	
	function printRows($res, $aspectName = NULL) {
	    static $blockIdCounter = 0;
	
		print "<div class='list'>";
		if ($aspectName!==NULL) {
			print "<span class='aspect-name'>"; //FIXME: tag
			print htmlspecialchars($aspectName);
			print "</span>";
			print ": ";
		}

		if ($this->blockTag) {
			$blockIdCounter += 1;
			$id = "toggleblock$blockIdCounter";
		
			print "[<a href=\"javascript:void(0)\" onclick=\"toggle(this)\">+</a>] ";
			print "<".$this->blockTag." id='".$id."'>";
		}
		
		$first = true;
		while ($r = $res->fetch_object()) {
			if ($first) $first = false;
			else if ($this->itemSeparator) print $this->itemSeparator;
		
			$f = $this->printerFunction;
			$this->$f($r);
			
		}
		
		if ($this->blockTag) {
			print "</".$this->blockTag.">\n";
			print "<script type='text/javascript'>
					var e = document.getElementById('".$id."'); 
					e.style.display = 'none';
					if (!e.firstChild) e.parentNode.style.display = 'none';
			</script>\n";
		}
		print "</div>\n";
	}
	
}

class ConceptRenderer extends ItemRenderer {
	var $aspects;

	function __construct($corpus, $printerFunction, $aspects) {
		ItemRenderer::__construct($corpus, $printerFunction);
		
		$this->aspects = $aspects;
		$this->itemSeparator = $printerFunction == "printReference" ? ", " : NULL;
		$this->blockTag = $printerFunction == "printItem" ? "ul" : NULL;
	}
	
	function needsConceptRecord() {
		return $this->printerFunction != "printReference";
	} 
	
	function needsResourceRecord() {
		return $this->printerFunction != "printReference";
	} 
	
	function needsConceptDefinition() {
		return $this->printerFunction != "printReference";
	} 
	
	function printReference( $concept ) {
		$detailURL = $this->getConceptDetailURL($concept->id);
		print "<a href='".htmlspecialchars($detailURL)."' class='concept-name' title='".htmlspecialchars($concept->name)."'>#".htmlspecialchars($concept->id)."</a>";
		if (isset($concept->occ)) print "<span class=\"count\">&times;" . $concept->occ . "</span>";
	}

	function printItem( $concept, $itemTag = "li" ) {
		$this->printShort($concept, $itemTag);
	}
	
	function printShort( $concept, $itemTag = "div" ) {
		global $conceptTypeNames;
		$type = $conceptTypeNames[$concept->type];
		$cls = "concept-type-" . $type;
		
		print "\t<".$itemTag." class='concept $cls'>";
		$this->printReference($concept);
		print " <span class='concept-type'>(".$type.")</span> ";
		
		if ($concept->definition) {
			print "<span class='definition'>".htmlspecialchars($concept->definition)."</span>";
		}
		
		if ($concept->resource) {
			$u = $this->corpus->url . urlencode($concept->resource_name);
			$t = $concept->resource_name;
			print " <span class='concept-resource'>(from <a href=".htmlspecialchars($u).">".htmlspecialchars($t)."@".htmlspecialchars($this->corpus->domain)."</a>)</span>";
		}
		
		print "</".$itemTag.">\n";
	}
	
	function printBox( $concept ) {
		//print_r($concept);
	
		global $conceptTypeNames;
		$type = $conceptTypeNames[$concept->type];
		$cls = "concept-type-" . $type;
		
		print "\t<div class='box concept-box $cls'>";
		$this->printShort($concept, "div");
		if ($this->aspects) {
			foreach ($this->aspects as $aspectQuery) {
				$aspectQuery->printResultFor($concept);
			}
		}
		print "</div>\n";
	}
	
	function getConceptDetailURL($id) {
		return getQueryURL(array("domain" => $this->corpus->domain, "query" => "concept-details", "concept" => $id));
	}
}

class TermRenderer extends ItemRenderer {
	var $aspects;

	function __construct($corpus, $printerFunction, $aspects) {
		ItemRenderer::__construct($corpus, $printerFunction);
		
		$this->aspects = $aspects;
		$this->itemSeparator = $printerFunction == "printReference" ? ", " : NULL;
	}
	
	function printReference( $term) {
		$detailURL = $this->getTermDetailURL($term->term_text);
		print "<a href='".htmlspecialchars($detailURL)."' class='term-text' title='".htmlspecialchars($term->term_text)."'>".htmlspecialchars($term->term_text)."</a>";
		if (isset($term->occ)) print "<span class=\"count\">&times;" . $term->occ . "</span>";
	}

	function printShort( $term, $itemTag = "div") {
		global $conceptTypeNames;
		
		print "\t<".$itemTag." class='term'>";
		$this->printReference($concept);
		print "</".$itemTag.">\n";
	}
	
	function printBox( $term ) {
		global $conceptTypeNames;
		
		print "\t<div class='box term-box'>";
		$this->printShort($concept, "div");
		if ($this->aspects) {
			foreach ($this->aspects as $aspectQuery) {
				$aspectQuery->printResultFor($term);
			}
		}
		print "</div>\n";
	}
	
	function getTermDetailURL($term) {
		return getQueryURL(array("domain" => $this->corpus->domain, "query" => "meanings", "term" => $term));
	}
}

class CorpusDB {
	var $db;
	var $prefix;
	var $url;
	var $domain;
	
	function __construct($db, $prefix, $domain) {
		$this->prefix = $prefix;
		$this->domain = $domain;
		$this->url = "http://$domain/wiki/";

		extract($db);
		
		if (!isset($port)) $port = 3306;
		$this->db = mysqli_connect ( $host, $username, $passwd, $dbname, $port);
		
		if (!$this->db || mysqli_connect_errno()>0) {
			throw new Exception("failed to connect to $host:$port/$dbname: ".mysqli_connect_error());
		}
		
		mysqli_query($this->db, "SET NAMES UTF8;");
	}
	
	function query($sql) {
		$res = mysqli_query($this->db, $sql);
	
		if (!$res || mysqli_errno($this->db)>0) {
			$err = mysqli_error($this->db);
			print "<p class=\"error\">SQL Error: ".htmlspecialchars($err)."<br/>Offending SQL: <tt>".htmlspecialchars($sql)."</tt></p>";
			throw new Exception("query failed: ".$err);
		}
		
		return $res;
	}
	
	function close() {
		if ($this->db) $this->db->close();
	}
	
	function quote($s) {
		return "'".mysqli_real_escape_string($this->db, $s)."'";
	}
}

class Select {
	function __construct( $from ) {
		$this->fields = array();
		$this->from = $from;
		$this->join = array();
		$this->where = array();
		$this->group = array();
		$this->having = array();
		$this->order = array();
	}
	
	function getSQL($limit) {
		$sql = "SELECT ";
		
		if ($this->fields) $sql .= implode(", ", $this->fields);
		else $sql .= " * ";
		
		$sql.= " FROM ".$this->from." ";
		
		if ($this->join)   $sql .= implode(" ", $this->join);
		
		if ($this->where)  $sql .= " WHERE "    . "(" . implode(") AND (", $this->where) . ")";
		if ($this->group)  $sql .= " GROUP BY " . implode(", ", $this->group);
		if ($this->having) $sql .= " HAVING "   . "(" . implode(") AND (", $this->having) . ")";
		if ($this->order)  $sql .= " ORDER BY " . implode(", ", $this->order);
		
		if ($limit) $sql.= " LIMIT " . $limit;
		return $sql;
	}
}

class CorpusQuery {
	var $corpus;
	var $renderer;
	var $limit;	
	var $name;
	
	function __construct($corpus, $renderer, $name) {
		$this->corpus = $corpus;
		$this->renderer = $renderer;
		$this->name = $name;
		$this->limit = "100"; //TODO: paging
	}
	
	function getTitle($params) {
	    return $this->name;
	}
	
	function paramsFromRequest() {
		$params = array();
		if (isset($_REQUEST['offset'])) $params['offset'] = $_REQUEST['offset'];
		return $params;
	}
	
	function getSQL($params) {
		//abstract
	}
	
	function getLimit($params) {
		$limit = $this->limit;
		if (isset($params['offset'])) $limit =  $params['offset'] . ", " . $limit;
		return $limit;
	}

	function printResultFor($params) {
		$sql = $this->getSQL($params);
#		print_r("*** $sql ***");
		$res = $this->corpus->query($sql);
		$this->renderer->printRows($res, $this->name);
		$res->free();
	}	
	
	function addConceptJoins($select, $idField) {
		if ($this->renderer->needsConceptRecord()) {
			$select->join[] = " JOIN {$this->corpus->prefix}concept as C ON C.id = ".$idField;
			$select->fields[] = "C.*";
		
			if ($this->renderer->needsConceptDefinition()) {
				$select->join[] = " LEFT JOIN {$this->corpus->prefix}definition as D ON D.concept = C.id";
				$select->fields[] = "D.definition as definition";
			}
			
			if ($this->renderer->needsResourceRecord()) {
				//$select->join[] = " JOIN {$this->corpus->prefix}resource as RC ON C.resource = RC.id";
				//$select->fields[] = "RC.name as resource_name";
				$select->fields[] = "IF (C.resource IS NULL, NULL, name) as resource_name";
			}
		}
	}
}

class AllConceptsQuery extends CorpusQuery {
	function __construct($corpus, $renderer, $name) {
		CorpusQuery::__construct($corpus, $renderer, $name);
	}
	
	function getTitle($params) {
	    return "All Concepts";
	}
	
	function paramsFromRequest() {
	    $params = CorpusQuery::paramsFromRequest();
		return $params;
	}
	
	function getSQL($params) {
		
		//TODO: nicely get table name
		return "SELECT *, IF (resource IS NULL, NULL, name) as resource_name
				FROM {$this->corpus->prefix}concept as C 
				LEFT JOIN {$this->corpus->prefix}definition as D 
					ON D.concept = C.id
				LIMIT " . $this->getLimit($params); 
	}	
}

class TermConceptsQuery extends CorpusQuery {
	function __construct($corpus, $renderer, $name, $cutoff) {
		CorpusQuery::__construct($corpus, $renderer, $name);
		$this->cutoff = $cutoff;
	}
	
	function getTitle($params) {
		$term = @$params["term"];
	    return "Concepts for term ".$term;
	}
	
	function paramsFromRequest() {
	    $params = CorpusQuery::paramsFromRequest();
		$params["term"] = @$_REQUEST["term"];
		
		if ($params["term"] == NULL || $params["term"] == "") return false;
		else return $params;
	}
	
	function getSQL($params) {
		if (is_object($params)) $params = get_object_vars($params);
		$term = @$params["term"];
		$cutoff = isset($params["cutoff"]) ? (int)$params["cutoff"] : $this->cutoff;
		
		$select = new Select("{$this->corpus->prefix}use as R");
		$select->fields[] = "R.concept as id";
		$select->fields[] = "R.concept_name as name";
		$select->fields[] = "COUNT(*) as occ";
		
		$this->addConceptJoins($select, "R.concept");
		
		$select->where[] = "term_text = ".$this->corpus->quote($term);
		$select->group[] = "R.concept";
		$select->order[] = "occ desc";
		if ($cutoff) $select->having[] = "occ >= ".(int)$cutoff;
		
		$limit = $this->getLimit($params);
		return $select->getSQL( $limit ); 
	}	
}

class ConceptTermsQuery extends CorpusQuery {
	function __construct($corpus, $renderer, $name, $cutoff) {
		CorpusQuery::__construct($corpus, $renderer, $name);
		$this->cutoff = $cutoff;
	}
	
	function getTitle($params) {
		$id = @$params["concept"];
	    return "Terms for Concept #".$id;
	}
	
	function paramsFromRequest() {
	    $params = CorpusQuery::paramsFromRequest();
		$params["concept"] = @$_REQUEST["concept"];
		if ($params["concept"] == NULL || $params["concept"] == "") return false;
		else return $params;
	}
	
	function getSQL($params) {
		if (is_object($params)) $params = get_object_vars($params);
		$id = @$params["concept"];
		if (!$id) $id = @$params["id"];
		$cutoff = isset($params["cutoff"]) ? (int)$params["cutoff"] : $this->cutoff;
		
		$select = new Select("{$this->corpus->prefix}use as R");
		$select->fields[] = "R.term as id";
		$select->fields[] = "R.term_text as term_text";
		$select->fields[] = "COUNT(*) as occ";
		
		$select->where[] = "R.concept = ".(int)$id;
		$select->group[] = "term";
		$select->order[] = "occ desc";
		if ($cutoff) $select->having[] = "occ >= ".(int)$cutoff;
		
		$limit = $this->getLimit($params);
		return $select->getSQL($limit); 
	}	
}

class BroaderConceptsQuery extends CorpusQuery {
	function __construct($corpus, $renderer, $name) {
		CorpusQuery::__construct($corpus, $renderer, $name);
	}
	
	function getTitle($params) {
		$id = @$params["concept"];
	    return "Concepts broader than #".$id;
	}
	
	function paramsFromRequest() {
	    $params = CorpusQuery::paramsFromRequest();
		$params["concept"] = @$_REQUEST["concept"];
		if ($params["concept"] == NULL || $params["concept"] == "") return false;
		else return $params;
	}
	
	function getSQL($params) {
		if (is_object($params)) $params = get_object_vars($params);
		$id = @$params["concept"];
		if (!$id) $id = @$params["id"];
		
		$select = new Select("{$this->corpus->prefix}broader as R");
		$select->fields[] = "broad as id";
		$select->fields[] = "broad_name as name";
		$select->order[] = "R.broad";
		
		$this->addConceptJoins($select, "R.broad");
		
		$select->where[] = "narrow = ".((int)$id);
		$select->group[] = "broad";
		
		$limit = $this->getLimit($params);
		return $select->getSQL($limit); 
	}	
}

class LinkedConceptsQuery extends CorpusQuery {
	function __construct($corpus, $renderer, $name) {
		CorpusQuery::__construct($corpus, $renderer, $name);
	}
	
	function getTitle($params) {
		$id = @$params["concept"];
	    return "Concepts referenced by #".$id;
	}
	
	function paramsFromRequest() {
	    $params = CorpusQuery::paramsFromRequest();
		$params["concept"] = @$_REQUEST["concept"];
		if ($params["concept"] == NULL || $params["concept"] == "") return false;
		else return $params;
	}
	
	function getSQL($params) {
		if (is_object($params)) $params = get_object_vars($params);
		$id = @$params["concept"];
		if (!$id) $id = @$params["id"];
		
		$select = new Select("{$this->corpus->prefix}reference as R");
		$select->fields[] = "target as id";
		$select->fields[] = "target_name as name";
		$select->order[] = "R.target";
		
		$this->addConceptJoins($select, "R.target");
		
		$select->where[] = "source = ".((int)$id);
		$select->group[] = "target";
		
		$limit = $this->getLimit($params);
		return $select->getSQL($limit); 
	}	
}

class NarrowConceptsQuery extends BroaderConceptsQuery {
	function __construct($corpus, $renderer, $name) {
		CorpusQuery::__construct($corpus, $renderer, $name);
	}
	
	function getTitle($params) {
		$id = @$params["concept"];
	    return "Concepts more narrow than #".$id;
	}
	
	function getSQL($params) {
		if (is_object($params)) $params = get_object_vars($params);
		$id = @$params["concept"];
		if (!$id) $id = @$params["id"];
		
		$select = new Select("{$this->corpus->prefix}broader as R");
		$select->fields[] = "narrow as id";
		$select->fields[] = "narrow_name as name";
		$select->order[] = "R.narrow";
		
		$this->addConceptJoins($select, "R.narrow");
		
		$select->where[] = "broad = ".((int)$id);
		$select->group[] = "narrow";
		
		$limit = $this->getLimit($params);
		return $select->getSQL($limit); 
	}	
}

class LinkingConceptsQuery extends LinkedConceptsQuery {
	function __construct($corpus, $renderer, $name) {
		CorpusQuery::__construct($corpus, $renderer, $name);
	}
	
	function getTitle($params) {
		$id = @$params["concept"];
	    return "Concepts referencing #".$id;
	}
	
	function getSQL($params) {
		if (is_object($params)) $params = get_object_vars($params);
		$id = @$params["concept"];
		if (!$id) $id = @$params["id"];
		
		$select = new Select("{$this->corpus->prefix}reference as R");
		$select->fields[] = "source as id";
		$select->fields[] = "source_name as name";
		$select->order[] = "R.source";
		
		$this->addConceptJoins($select, "R.source");
		
		$select->where[] = "target = ".((int)$id);
		$select->group[] = "source";
		
		$limit = $this->getLimit($params);
		return $select->getSQL($limit); 
	}	
}

class ConceptDetailsQuery extends CorpusQuery {
	function __construct($corpus, $renderer, $name) {
		CorpusQuery::__construct($corpus, $renderer, $name);
	}
	
	function getTitle($params) {
		$id = @$params["concept"];
	    return "Concepts Details for #".$id;
	}
	
	function paramsFromRequest() {
	    $params = CorpusQuery::paramsFromRequest();
		$params["concept"] = @$_REQUEST["concept"];
		if ($params["concept"] == NULL || $params["concept"] == "") return false;
		else return $params;
	}
	
	function getSQL($params) {
		if (is_object($params)) $params = get_object_vars($params);
		$id = @$params["concept"];
		if (!$id) $id = @$params["id"];
		
		$select = new Select("{$this->corpus->prefix}concept as C");
		$select->join[] = "LEFT JOIN {$this->corpus->prefix}definition as D ON D.concept = C.id";

		$select->fields[] = "*";
		$select->fields[] = "IF (C.resource IS NULL, NULL, name) as resource_name";
		
		$select->where[] = "id = ".((int)$id);
		
		$limit = $this->getLimit($params);
		return $select->getSQL($limit); 
	}	
}

function getQueryURL($params) {
	$u = $_SERVER["PHP_SELF"];
	
	if ($params) {
		$first = true;
		foreach ($params as $p => $v) {
			if ($v===false || $v===NULL) continue;
			
			if ($first) {
				$first = false;
				$u .= "?";
			}
			else $u .= "&";
			
			$u.= urlencode($p);
			
			if ($v===true) continue;
			
			$u.= "=";
			$u.= urlencode($v);
		}
	}
	
	return $u;
}

function corpusCSS() {
	return "
		body { font-family: sans-serif; }
		.error { color:red; }
		.concept-name { font-weight: bold; }
		.concept-type { font-size: 70%; }
		.count { font-size: 70%; }
		.concept-type-NONE > .concept { color: #AAA; text-decoration: line-through; }
		.concept-type-BAD > .concept { color: #FAA; text-decoration: line-through; }
		.concept-type-UNKNOWN > .concept { color: #AAA; }
		.definition { font-style:italic; font-family: serif; }
		.concept-resource { font-size: 70%; }
		.box { padding: 0.5ex; margin:0.5ex; border:1px solid black; }
		.aspect-name { font-family: bold; }
		.box .list { font-size:80%; }
		.list ul { padding: 0; margin: 0; margin-left: 2ex; }
		.list { margin-top: 0.8ex; margin-bottom: 0.8ex; }
	";
}

function corpusJS() {
	return "
	function nextElement(e) {
		e = e.nextSibling;
		while (e && e.nodeType!=1) e = e.nextSibling;
		return e;
	}
	
	function toggle(link) {
		block = nextElement(link);
		if (!block) alert('block not found!');
		if (block.style.display == 'none') {
			block.style.display = 'block';
			link.innerHTML = '&ndash;';
		}
		else {
			block.style.display = 'none';
			link.innerHTML = '+';
		}
	}
	";
}

function corpusContent($derm) {
	$derm->execute("queryForm");
	if ($derm->has("result")) {
		$derm->execute("result");
	}
}

function corpusQueryForm($derm) {
    global $corpuses;

	print "\n<form name='query' class='query'>\n";
	print $derm->markup("selectBox", array(
		"id" => "domain_field",
		"name" => "domain",
		"options" => array_keys($corpuses),
		//"selected" => $_REQUEST["domain"],
	));
	print $derm->element("input", array(
		"type" => "text",
		"id" => "term_field",
		"name" => "term",
		"value" => @$_REQUEST["term"],
	));
	print $derm->element("input", array(
		"type" => "hidden",
		"id" => "query_field",
		"name" => "query",
		"value" => "meanings",
	));
	print $derm->element("input", array(
		"type" => "submit",
		"id" => "go_button",
		"name" => "go",
		"value" => "Get Meanings",
	));
	print "\n</form>\n";
}

function corpusFromRequest() {
	global $corpuses; //FIXME: from DB!

	$domain = @$_REQUEST["domain"];
	if (!$domain) return NULL;
	
	$prefix = @$corpuses[$domain];
	if (!$prefix) return NULL;
	
	$prefix .= "_";
	
	$db = array( //FIXME: config file!
		"host" => "localhost",
		"username" => "wikisense",
		"passwd" => "34hkjl654",
		"dbname" => "WikiSense",
	);

	$corpus = new CorpusDB($db, $prefix, $domain);
	return $corpus;
}

function queryFromRequest($corpus) {
	$q = @$_REQUEST["query"];
	if (!$q) return NULL;

	//TODO: limit/paging
	
	if ($q == "meanings" || $q == "concept-details" || $q == "all-concepts") {
		$conceptListRenderer = new ConceptRenderer($corpus, "printItem", NULL); 
		$termListRenderer = new TermRenderer($corpus, "printReference", NULL); 
		
		$aspects = array(
			new ConceptTermsQuery($corpus, $termListRenderer, "Terms", 3), //FIXME: smart cutoff in renderer?! JS toggle?
			new BroaderConceptsQuery($corpus, $conceptListRenderer, "Broader"),
			new NarrowConceptsQuery($corpus, $conceptListRenderer, "Narrower"),
			new LinkedConceptsQuery($corpus, $conceptListRenderer, "Links"),
			new LinkingConceptsQuery($corpus, $conceptListRenderer, "References"),
		);
		
		$conceptBoxesRenderer = new ConceptRenderer($corpus, "printBox", $aspects); 
		
		if ($q == "all-concepts") return new AllConceptsQuery($corpus, $conceptBoxesRenderer, null);
		else if ($q == "meanings") return new TermConceptsQuery($corpus, $conceptBoxesRenderer, null, 0);
		else if ($q == "concept-details") return new ConceptDetailsQuery($corpus, $conceptBoxesRenderer, null);
	}
	
	return NULL;
}

function printResult($derm) {
	$query = $derm->value("query");

	$params = $query->paramsFromRequest();
	if ($params === false) return;

	print "<h2>".$derm->escape($query->getTitle($params))."</h2>";
	$query->printResultFor($params);
}

$title = "WikiWord Corpus";
$corpus = corpusFromRequest();
$query = $corpus ? queryFromRequest($corpus) : NULL;

$derm = new XhtmlDerm();

if ($corpus) {
	$derm->setValue("corpus", $corpus);
	$title.= " ({$corpus->domain})";
}

if ($query) {
	$derm->setValue("query", $query);
	$derm->setGenerator("result", "printResult");
}

$derm->setValue('windowtitle', $title);
$derm->setValue('pagetitle', $title);
$derm->setValue('footer', "WikiWord by Daniel Kinzler, 2005-2007");

$derm->addFunction('headstyles', 'corpusCSS');
$derm->addFunction('headscripts', 'corpusJS');
$derm->setGenerator('queryForm', 'corpusQueryForm');
$derm->setGenerator('bodycontent', 'corpusContent');
$derm->execute('page');

if ($corpus) {
	$corpus->close();
}

?>