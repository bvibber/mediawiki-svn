<?php

$IP = dirname(__FILE__);

require_once("$IP/config.php");
require_once("$IP/wwimages.php");

if ($wwAPI) require_once("$IP/wwclient.php");
else require_once("$IP/wwthesaurus.php");

function printConceptList($concepts, $lang) {
    ?>
    <ul class="terselist">
      <?php
	foreach ($concepts as $c) {
	    ?><li><?php
	    print getConceptDetailsLink($lang, $c);
	    ?></li><?php
	}
      ?>
    </ul>
    <?php
}

function printConceptImageList($concept, $class = "terselist") {
    global $utils, $wwThumbSize, $wwMaxPreviewImages;

    if (!$concept) return false;

    if (is_array($concept) && !isset($concept['id']) && isset($concept[0])) $images = $concept; #XXX: HACK
    else $images = $utils->getImagesAbout($concept, $wwMaxPreviewImages);

    ?>
    <ul class="<?php print $class; ?>">
      <?php
	foreach ($images as $img) {
	  ?><li><?php
	  print $utils->getThumbnailHTML($img, $wwThumbSize, $wwThumbSize);
	  ?></li><?php
	}
      ?>
    </ul>
    <?php
}

function getConceptDetailsURL($langs, $concept) {
    global $wwSelf;

    if ( is_array($langs) ) $langs = implode('|', $langs);

    return "$wwSelf?id=" . urlencode($concept['id']) . "&lang=" . urlencode($langs); 
}

function getConceptDetailsLink($langs, $concept) {
    global $utils;
    $name = $utils->pickLocal($concept['name'], $langs);
  
    $u = getConceptDetailsURL($langs, $concept);
    return '<a href="' . htmlspecialchars($u) . '">' . htmlspecialchars($name) . '</a>';
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
	$links[$page] = $u;
    }

    return $urls;
}

function getConceptPageLinks($lang, $concept) {
    $urls = getConceptPageURLs($lang, $concept);
    if (!$urls) return false;

    foreach ($urls as $page => $u) {
	$u = "http://$domain/wiki/" . urlencode($page); 
	$links[] = '<a href="' . htmlspecialchars($u) . '">' . htmlspecialchars( str_replace("_", " ", $page) ) . '</a>';
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

    ?>
    <tr class="row_item">
      <td class="cell_weight  <?php print "weight_$wclass"; ?>"><?php print htmlspecialchars($score); ?></td>
      <td colspan="3" class="cell_name  <?php print "weight_$wclass"; ?>">
	<h3>
	<?php print getConceptDetailsLink($langs, $concept); ?>
	<?php /* TODO: wiki links */ ?>
	</h3>
      </td>
    </tr>

    <?php if (isset($definition) && !empty($definition)) { 
	$definition = $utils->pickLocal($definition, $langs);
    ?>

    <tr class="row_def">
      <td></td>
      <td colspan="3"><?php print htmlspecialchars($definition); ?></td>
    </tr>
    <?php } ?>

    <tr class="row_details row_images">
      <td></td>
      <td class="cell_images" colspan="3">
      <?php 
	  $gallery = $utils->getImagesAbout($concept, $terse ? $wwMaxPreviewImages : $wwMaxGalleryImages );
	  $c = printConceptImageList( $gallery, $terse ? "terselist" : "gallery" ); 
      ?>
      </td>
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

$utils = new WWImages( $thesaurus );
if ( !$utils->db ) $utils->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);

if (@$_REQUEST['debug']) $utils->debug = true;

$limit = 20;
$norm = 1;

$result = NULL;

$languages = array( $lang, "en", "commons" ); #TODO: make the user define this list

if (!$error) {
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
	.error { color: red; font-weight: bold; }
	.weight_huge { font-size: 140%; font-weight:bold; }
	.weight_big { font-size: 120%; font-weight:bold; }
	.weight_normal { font-size: 110%; font-weight:bold; }
	.weight_some { font-size: 100%; font-weight:bold; }
	.weight_little { font-size: 90%; font-weight:bold; }
	.weight_unknown { font-size: 100%; font-weight:bold; }
	.row_def td { font-size: 80%; font-style:italic; }
	.row_details td { font-size: 80%; }
	.cell_weight { text-align: right; }
	.cell_label { text-align: right; }
	.header { text-align: left; }
	.inputform { text-align: center; margin:1ex auto; padding:1ex; width:80%; border:1px solid #666666; background-color:#DDDDDD; }
	.footer { font-size:80%; text-align: center; border-top: 1px solid #666666; }
	.note { font-size:80%; }
	.terselist, .terselist li { display: inline; margin:0; padding:0; }
	.terselist li { display: inline; }
	.terselist li:before { content:" - " }
	.terselist li:first-child:before { content:"" }

	.gallery li { display: inline; padding:0.5ex; margin:0.5ex; }
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
    </div>

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
</html>
<?php
$utils->close();
?>