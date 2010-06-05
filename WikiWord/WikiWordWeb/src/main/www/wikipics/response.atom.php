<?php 
if (!defined("WIKIPICS")) die("not a valid entry point");

function printConceptEntry( $langs, $concept ) {
   global $utils, $wwDbDate;
   
    extract( $concept );
   
   $name = $utils->pickLocal($concept['name'], $langs);
   $name = str_replace("_", " ", $name);
   
   if (empty($definition)) $definition = false;
   else if (is_array($definition)) {
   		$definition = $utils->pickLocal($definition, $langs);
   		$defLang = $utils->pickLanguage($definition, $langs);
   	}
   
  $detailsURL = getConceptDetailsURL($langs, $concept);
  $thesaurusURL = false;
  ?>
  <entry>
     <id>sha1:<?php print sha1("$id/$wwDbDate/brief"); ?></id>
     <title><?php print htmlspecialchars($name); ?></title>
     <updated><?php print htmlspecialchars($wwDbDate); ?></updated>
     <link rel="alternate" type="application/atom+xml" href="<?php print htmlspecialchars($detailsURL); ?>"/>
     <?php if($detailsURL) { ?> <link rel="related" type="application/rdf+xml" href="<?php print htmlspecialchars($thesaurusURL); ?>"/> <?php } ?>
     <?php if($thesaurusURL) { ?> <link rel="related" type="text/html" lang="<?php print htmlspecialchars($pageLang); ?>" href="<?php print htmlspecialchars($pageURL); ?>"/> <?php } ?>
     <summary type="text" lang="<?php print htmlspecialchars($defLang); ?>">
     	<?php print htmlspecialchars($definition); ?>
     </summary>
     <content type="xhtml" xmlns="http://www.w3.org/1999/xhtml">
     		<table class="results">
     		<?php printConcept($concept, $langs, true); /*HTML!*/ ?>
     		</table>
     </content>
	<?php	
	   //use http://a9.com/-/spec/opensearch/1.1/subset and http://a9.com/-/spec/opensearch/1.1/superset
	   //use both atom:link and opensearch:link!
		listRelatedConcepts($concept);
		listImageEnclosures($concept);
	?>
   </entry>
  <?php
}

function listConcepts($langs, $concepts) {
	foreach ($concepts as $row) {
		printConceptEntry($langs, $row);
	}
}

if( @$_GET( 'ctype' ) == 'application/xml' ) {
	// Makes testing tweaks about a billion times easier
	$ctype = 'application/xml';
} else {
	$ctype = 'application/atom+xml';
}

if (!isset($wwDbDate)) $wwDbDate = date("Y-m-d H:i:s T");

if (!isset($scriptPath)) $scriptPath = "./";
if (!isset($skinPath)) $skinPath = "$scriptPath/../skin/";

$uri = "$scriptPath/search.php?lang=".urlencode($lang)."&format=atom";

if ($mode == 'term') {
	$search = "$lang:$term";
	$uri .= "&term=".urlencode(term);
	$title = $term;
} else if ($mode == 'concept') {
    $term = "\${$conceptId}";
	$search = "$lang:$term";
	$uri .= "&id=".urlencode($conceptId);
	
	$keys = array_keys($result);
	$k = $keys[0];
	$concept = $result[$k];
	$title = $utils->pickLocal($concept['name'], $langs);
}

header("Content-Type: $ctype; charset=$ctype");
?><?xml version="1.0" encoding="UTF-8"?>
 <feed xmlns="http://www.w3.org/2005/Atom" 
       xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/">
       
		<link rel="search"
		         href="http://example.com/opensearchdescription.xml" 
		         type="application/opensearchdescription+xml" 
		         title="WikiPics Search" />
		         
		   <title><?php print htmlspecialchars("$title ($lang) WikiPics"); ?></title> 
		   <link rel="self" href="<?php print htmlspecialchars($uri); ?>"/>
		   <updated><?php print htmlspecialchars($wwDbDate); ?></updated>
		   <author> 
		     <name><?php print htmlspecialchars($wwDbAuthor); ?></name>
		   </author> 
		   <icon><?php print htmlspecialchars("$skinPath/favicon.ico"); ?></icon> 
		   <logo><?php print htmlspecialchars("$skinPath/wikipics.png"); ?></logo> 
		   <generator>WikiPics 0.1&alpha; (experimental)</generator> 
		   <id>sha1:<?php print sha1("$search/$date"); ?></id>
		   <opensearch:totalResults><?php print count($result); ?></opensearch:totalResults>
		   <opensearch:startIndex>1</opensearch:startIndex>
		   <opensearch:itemsPerPage>100</opensearch:itemsPerPage>
		   <opensearch:Query role="request" language="<?php print htmlspecialchars($lang); ?>" searchTerms="<?php print htmlspecialchars($lang); ?>" startPage="1" />
		         
		         
		<?php         
		if ($error) {
			reportError($error);
		} else if ($mode=='concept') {
			listConceptImages($concept);
			listRelatedConcepts($concept);
		} else if ($mode=='term') {
			listConcepts($languages, $result);
		}
		?>
</xml>
