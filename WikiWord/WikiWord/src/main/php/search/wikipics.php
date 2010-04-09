<?php

$IP = dirname( dirname(__FILE__) );

require_once("$IP/config.php");
require_once("$IP/common/wwimages.php");

if ($wwAPI) require_once("$IP/common/wwclient.php");
else require_once("$IP/common/wwthesaurus.php");

function printConceptList($langs, $concepts, $class) {
    if (!$concepts) return;
    ?>
    <ul class="<?php print $class; ?>">
      <?php
	foreach ($concepts as $c) {
	    ?><li><?php
	    print getConceptDetailsLink($langs, $c);
	    ?></li><?php
	}
      ?>
    </ul>
    <?php
}

function getImagesAbout($concept, $max) {
    global $utils, $profiling;

    $t = microtime(true);
    $pics = $utils->getImagesAbout($concept, $max);
    $profiling['pics'] += (microtime(true) - $t);

    return $pics;
}

function printConceptImageList($concept, $class = "tersetable", $columns = 5) {
    global $utils, $wwThumbSize, $wwMaxPreviewImages;

    if (!$concept) return false;

    if (is_array($concept) && !isset($concept['id'])) $images = $concept; #XXX: HACK
    else $images = getImagesAbout($concept, $wwMaxPreviewImages);

    if (!$images) return;

    $imgList = array_values($images);

    ?>
    <table class="imageTable <?php print $class; ?>">
    <tbody>
      <?php
	$i = 0;
	$c = count($images);
	while ($i < $c) {
	  $i = printConceptImageRow($imgList, $i, $columns);
	}
      ?>
      </tr>
    </tbody>
    </table>
    <?php
}

function printConceptImageRow($images, $from, $columns = 5) {
	global $wwThumbSize, $utils;

	$cw = $wwThumbSize + 20;
	$cwcss = $cw . "px";

	$c = count($images);

	$to = $from + $columns;
	if ( $to > $c ) $to = $c;

	print "\t<tr class=\"imageRow\">\n";
	
	for ($i = $from; $i<$to; $i += 1) {
	  $img = $images[$i];
	  print "\t\t<td class=\"imageCell\" width=\"$cw\" align=\"left\" valign=\"bottom\" nowrap=\"nowrap\" style=\"width: $cwcss\">";
	  print $utils->getThumbnailHTML($img, $wwThumbSize, $wwThumbSize);
	  print "</td>\n";
	}
	
	print "\n\t</tr>\n";

	print "\t<tr class=\"imageMetaRow\">\n";
	
	for ($i = $from; $i<$to; $i += 1) {
	  $img = $images[$i];
	  $title = str_replace("_", " ", $img['name']);

	  print "\t\t<td class=\"imageMetaCell\" width=\"$cw\" align=\"left\" valign=\"top\" style=\"width: $cwcss\">";
	  print "<div class=\"imageTitle\">" . htmlspecialchars( $title ) . "</div>";
	  print "</td>\n";
	}
	
	print "\n\t</tr>\n";

	return $to;
}

function getConceptDetailsURL($langs, $concept) {
    global $wwSelf;

    if ( is_array($langs) ) $langs = implode('|', $langs);

    return "$wwSelf?id=" . urlencode($concept['id']) . "&lang=" . urlencode($langs); 
}

function getConceptDetailsLink($langs, $concept) {
    global $utils;
    $name = $utils->pickLocal($concept['name'], $langs);
    $name = str_replace("_", " ", $name);
    $score = @$concept['score'];
  
    $u = getConceptDetailsURL($langs, $concept);
    return '<a href="' . htmlspecialchars($u) . '" title="' . htmlspecialchars($name) . ' (score: ' . (int)$score . ')'. '">' . htmlspecialchars($name) . '</a>';
}

function pickPage( $pages ) {
    if (!$pages) return false;

    foreach ( $pages as $page => $type ) {
	if ($type == 10) return $page;
    }

    return $pages[0];
}

function getConceptPageURLs($lang, $concept) {
    if (!isset($concept['pages'][$lang]) || !$concept['pages'][$lang]) return false;

    if ($lang == 'commons') $domain = 'commons.wikimedia.org';
    else $domain = "$lang.wikipedia.org";

    $urls = array();
    foreach ($concept['pages'][$lang] as $page => $type) {
	$u = "http://$domain/wiki/" . urlencode($page); 
	$urls[$page] = $u;
    }

    return $urls;
}

function getConceptPageLinks($lang, $concept) {
    $urls = getConceptPageURLs($lang, $concept);
    if (!$urls) return false;

    foreach ($urls as $page => $u) {
	$links[] = '<a href="' . htmlspecialchars($u) . '" title="' . htmlspecialchars( str_replace("_", " ", $page) ) . '">' . htmlspecialchars( $lang . ":" . str_replace("_", " ", $page) ) . '</a>';
    }

    return $links;
}

function getAllConceptPageLinks($concept) {
    $links = array();

    foreach ( $concept['languages'] as $lang ) {
	$ll = getConceptPageLinks($lang, $concept);
	if ($ll) $links[$lang] = $ll;
    }

    return $links;
}

function printList($items, $escape = true, $class = "list") {
    ?>
    <ul class="<?php print htmlspecialchars($class); ?>">
      <?php
	foreach ($items as $item) {
	    if ( $escape ) $item = htmlspecialchars($item);
	    print "\t\t<li>" . $item . "</li>\n";
	}
      ?>
    </ul>
    <?php
}

function printConceptPageList( $langs, $concept, $class ) {
    $linksByLanguage = getAllConceptPageLinks($concept);
    ?>
    <ul class="<?php print htmlspecialchars($class); ?>">
      <?php
	foreach ( $linksByLanguage as $lang => $links ) {
	    foreach ($links as $link ) {
		print "\t\t<li>" . $link . "</li>\n";
	    }
	}
      ?>
    </ul>
    <?php
}

function array_key_diff($base, $other) {
    $keys = array_keys($other);
    foreach ($keys as $k) {
	unset($base[$k]);
    }

    return $base;
}

function printRelatedConceptList( $langs, $concept, $class = "" ) {
    $related = array();
    if ( @$concept['similar'] ) $related += $concept['similar'];
    if ( @$concept['related'] ) $related += $concept['related'];
    if ( @$concept['narrower'] ) $related += $concept['narrower'];

    if (isset($concept['broader'])) $related = array_key_diff($related, $concept['broader']);

    printConceptList($langs, $related, $class);
}

function printConceptCategoryList( $langs, $concept, $class = "" ) {
    if (isset($concept['broader'])) printConceptList($langs, $concept['broader'], $class);
}

function printDefList($items, $scapeKeys = true, $escapeValues = true, $class = "list") {
    ?>
    <dl class="<?php print htmlspecialchars($class); ?>">
      <?php
	foreach ($items as $key => $item) {
	    if ( $escapeKeys ) $key = htmlspecialchars($key);
	    print "\t\t<dt>" . $key . "</dt>\n";

	    if ( $escapeValues ) $item = htmlspecialchars($item);
	    print "\t\t\t<dd>" . $item . "</dd>\n";
	}
      ?>
    </sl>
    <?php
}

function getWeightClass($weight) {
    if (!isset($weight) || !$weight) { 
      return "unknown";
      $weight = NULL;
    }
    else if ($weight>1000) return "huge";
    else if ($weight>100) return "big";
    else if ($weight>10) return "normal";
    else if ($weight>2) return "some";
    else return "little";
}

function printConcept($concept, $langs, $terse = true) {
    global $utils, $wwMaxPreviewImages, $wwMaxGalleryImages;

    extract( $concept );
    $wclass = getWeightClass($score);
    $lclass = $terse ? "terselist" : "list";
    $gallery = getImagesAbout($concept, $terse ? $wwMaxPreviewImages : $wwMaxGalleryImages );

    if (is_array($definition)) $definition = $utils->pickLocal($definition, $langs);

    ?>
    <tr class="row_top">
      <td colspan="3">&nbsp;</td>
    </tr>

    <tr class="row_head">
      <td colspan="1" class="cell_name">
	<h3 class="<?php print "weight_$wclass"; ?>">
	<?php print getConceptDetailsLink($langs, $concept); ?>
	</h3>
      </td>
      <td colspan="2" class="cell_pages">
	<?php printConceptPageList($langs, $concept, $lclass) ?>
      </td>
    </tr>

    <tr class="row_def">
      <td colspan="3"><?php print htmlspecialchars($definition); ?></td>
    </tr>

    <tr class="row_related">
      <td class="cell_related" colspan="3">
      Related: 
      <?php 
	  printRelatedConceptList( $langs, $concept, $lclass ); 
      ?>
      </td>
    </tr>

    <tr class="row_images">
      <td class="cell_images" colspan="3">
      <?php 
	  printConceptImageList( $gallery, $terse ? "tersegallery" : "gallery" ); 
      ?>
      <p>more...<!-- TODO --></p>
      </td>
    </tr>

    <tr class="row_category">
      <td class="cell_related" colspan="3">
      Broader:
      <?php 
	  printConceptCategoryList( $langs, $concept, $lclass ); 
      ?>
      </td>
    </tr>

    <tr class="row_bottom">
      <td colspan="3">&nbsp;</td>
    </tr>

    <tr class="row_blank">
      <td colspan="3">&nbsp;</td>
    </tr>

    <?php
    if (isset($score) && $score && $score<2 && $pos>=3) return false;
    else return true;
}

$conceptId = @$_REQUEST['id'];
$term = @$_REQUEST['term'];
$lang = @$_REQUEST['lang'];

if (!isset($wwSelf)) $wwSelf = @$_SERVER["PHP_SELF"];

$error = NULL;

if ($lang && !isset($wwLanguages[$lang])) {
    $lang = NULL;
    $error = "bad language code: $lang";
}

if ($wwAPI) $thesaurus = new WWClient($wwAPI);
else {
  $thesaurus = new WWThesaurus();
  $thesaurus->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);
}

if (@$wwFakeImages) $utils = new WWFakeImages( $thesaurus );
else $utils = new WWImages( $thesaurus );


if ( !$utils->db ) $utils->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);

if (@$_REQUEST['debug']) $utils->debug = true;

$limit = 20;
$norm = 1;

$result = NULL;

$languages = array( $lang, "en", "commons" ); #TODO: make the user define this list

$profiling['thesaurus'] = 0;
$profiling['pics'] = 0;

if (!$error) {
  $t = microtime(true);
  try {
      if ($lang && $conceptId) {
	  $result = $thesaurus->getConceptInfo($conceptId, $lang);
	  if ( $result ) $result = array( $result ); //hack
      } else if ($lang && $term) {
	  $result = $thesaurus->getConceptsForTerm($lang, $term, $languages, $norm, $limit);
      } 
  } catch (Exception $e) {
      $error = $e->getMessage();
  }
  $profiling['thesaurus'] += (microtime(true) - $t);
}


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr"> 
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>WikiWord Navigator</title>

    <style type="text/css">
	body { font-family: verdana, helvetica, arial, sans-serif; }
	td { text-align: left; vertical-align: top; }
	th { text-align: left; vertical-align: top; font-weight:bold; }

	a:link, a:visited, a:active {
	  color:#2200CC;
	}

	.error { color: red; font-weight: bold; }
	.weight_huge { font-size: 140%; font-weight:bold; }
	.weight_big { font-size: 120%; font-weight:bold; }
	.weight_normal { font-size: 110%; font-weight:bold; }
	.weight_some { font-size: 100%; font-weight:bold; }
	.weight_little { font-size: 90%; font-weight:bold; }
	.weight_unknown { font-size: 100%; font-weight:bold; }
	.row_def td { font-size: small; font-style:italic; }
	.row_details td { font-size: small; }
	.row_related td { font-size: small; }
	.row_category td { font-size: small; font-weight: bold; }
	.cell_weight { text-align: right; }
	.cell_label { text-align: right; }
	.header { text-align: left; }
	.inputform { text-align: center; margin:1ex auto; padding:1ex; width:80%; border:1px solid #666666; background-color:#DDDDDD; }
	.footer { font-size:80%; text-align: center; border-top: 1px solid #666666; }
	.note { font-size:80%; }

	.tersegallery, .tersegallery li, .terselist, .terselist li { display: inline; margin:0; padding:0; }
	.terselist li:before { content:", " }
	.terselist li:first-child:before { content:"" }

	.gallery li { display: inline; padding:0.5ex; margin:0.5ex; }
	.results { margin: 1em; }

	.imageCell { 
	    vertical-align: bottom; 
	    padding-top: 1em;
	}
	.imageCell img { border: 1px solid; }
	.imageCell, .imageMetaCell { 
	    font-size:small; 
	    border-spacing: 1em 0;
	    margin: 1em;
	}
    </style>
</head>
<body>
    <div class="header">
      <h1>WikiWord Navigator</h1>
      <p>Experimental semantic navigator and thesaurus interface for Wikipedia.</p>
      <p>The WikiWord Navigator was created as part of the WikiWord project run by <a href="http://wikimedia.de">Wikimedia Deutschland e.V.</a>.
      It is based on a <a href="http://brightbyte.de/page/WikiWord">diploma thesis</a> by Daniel Kinzler, and runs on the <a href="http://toolserver.org/">Wikimedia Toolserver</a>. WikiWord is an ongoing research project. Please contact <a href="http://brightbyte.de/page/Special:Contact">Daniel Kinzler</a> for more information.</p>
    </div>

    <div class="inputform" >
    <form name="search" action="<?php print $wwSelf; ?>">
      <table border="0" class="inputgrid">
	<tr>
	  <td>
	    <label for="term">Term: </label><input type="text" name="term" id="term" size="24" value="<?php print htmlspecialchars($term); ?>"/>
	  </td>
	  <td>
	    <label for="term" style="display:none">Language: </label>
	    <?php WWUtils::printSelector("lang", $wwLanguages, $lang) ?>
	  </td>
	  <td>
	    <input type="submit" name="go" value="go"/>
	  </td>
	</tr>
      </table>
      <p class="note">Note: this is a thesaurus lookup, not a full text search. Only exact matches are considered.</p>
      <?php
      if ($utils->debug) {
	      print '<input type="hidden" name="debug" value="true"/>';
	      print "<p>debug mode enabled!</p>";
	      flush();                           
      }
      ?>
    </form>
    </div>
<?php
if ($error) {
  print "<p class=\"error\">".htmlspecialchars($error)."</p>";
}
?>    

<?php
if ($result) {
    if ( $conceptId ) $terse = false;
    else $terse = true;

    $count = 0;
    foreach ( $result as $row ) {
	$count = $count + 1;
	$row['pos'] = $count;

?>    
    <table  border="0" class="results">
    <?php 
	  $continue= printConcept($row, $languages, $terse);

	  if (!$continue) break;
    ?>
    </table>

<?php
    } #concept loop

    if ($term) { ?>
	<p>Found <?php print $count; ?> items.</p>
    <?php }
} ?>

<p class="footer">
The WikiWord Navigator is part of the <a href="http://wikimedia.de">Wikimedia</a> project <a href="http://brightbyte.de/page/WikiWord">WikiWord</a>
<p>
</body>
<?php
foreach ( $profiling as $key => $value ) {
  print "<!-- $key: $value sec -->\n";
}
?>
</html>
<?php
$utils->close();
?>