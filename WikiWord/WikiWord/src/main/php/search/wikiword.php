<?php

$IP = dirname( dirname(__FILE__) );

require_once("$IP/config.php");
require_once("$IP/common/wwimages.php");

if ($wwAPI) require_once("$IP/common/wwclient.php");
else require_once("$IP/common/wwthesaurus.php");

function printLocalConceptList($lang, $concepts) {
    global $utils;
    if (is_string($concepts)) $concepts = $utils->unpickle($concepts, $lang, true, false, true);

    ?>
    <ul class="terselist">
      <?php
	foreach ($concepts as $c) {
	    ?><li><?php
	    printLocalConceptLink($lang, $c);
	    ?></li><?php
	}
      ?>
    </ul>
    <?php
}

function printConceptImageList($concept, $class = "terselist") {
    global $utils, $wwThumbSize, $wwMaxPreviewImages;

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

function printLocalConceptLink($lang, $row) {
    global $wwSelf, $images;

    $row = normalizeConceptRow($lang, $row);

    extract($row);

    if (!isset($weight) && isset($freq)) $weight = $freq;
    if (!isset($weight) && isset($conf)) $weight = $conf;
    if (!isset($concept_name) && isset($name)) $concept_name = $name;
    if (!isset($concept_name)) $concept_name = NULL;
    if (!isset($concept) && isset($id)) $concept = $id;
    if (!isset($concept)) $concept = NULL;

    if ($lang == 'commons') $domain = 'commons.wikimedia.org';
    else $domain = "$lang.wikipedia.org";

    $wu = $concept_name ? "http://$domain/wiki/" . urlencode($concept_name) : NULL; 
    $cu = "$wwSelf?id=" . urlencode($concept) . "&lang=" . urlencode($lang); 

    if ($images) $cu .= "&images=1";

    ?>
    <li>
	<?php if ($concept_name) { ?>
	  <a href="<?php print htmlspecialchars($cu); ?>"><?php print htmlspecialchars($concept_name); ?></a>
	<?php } ?>
	<?php if ($concept) { ?>
	  (<a href="<?php print htmlspecialchars($wu); ?>" title="<?php print htmlspecialchars($concept_name); ?>">wiki page</a>)
	<?php } ?>
    </li>
    <?php
}

function printTermList($lang, $terms) {
    global $utils;
    if (is_string($terms)) $terms = $utils->unpickle($terms, $lang);

    ?>
    <ul class="terselist">
      <?php
	foreach ($terms as $t) {
	    printTermLink($lang, $terms);
	}
      ?>
    </ul>
    <?php
}

function printTermLink($lang, $row) {
    global $wwSelf;

    extract($row);

    if (!isset($weight) && isset($freq)) $weight = $freq;
    if (!isset($weight) && isset($conf)) $weight = $conf;
    if (!isset($term_text) && isset($term)) $term_text = $term;
    if (!isset($term_text) && isset($text)) $term_text = $text;
    if (!isset($term_text) && isset($name)) $term_text = $name;
    if (!isset($term_text) && isset($value)) $term_text = $value;

    $tu = "$wwSelf?term=" . urlencode($term_text) . "&lang=" . urlencode($lang); 

    ?>
    <li>
	<a href="<?php print htmlspecialchars($tu); ?>"><?php print htmlspecialchars($term_text); ?></a>
    </li>
    <?php
}

function normalizeConceptRow($lang, $row) {
    global $wwSelf;

    #FIXME: handle complex concept records!

    if (!$row) return $row;

    if (!isset($row['lang'])) $row['lang'] = $lang;
    if (!isset($row['weight']) && isset($row['freq'])) $row['weight'] = $row['freq'];
    if (!isset($row['weight']) && isset($row['conf'])) $row['weight'] = $row['conf'];
    if (!empty($row['local_concept_name'])) $row['concept_name'] = $row['local_concept_name'];
    if (!isset($row['concept_name']) && isset($row['name'])) $row['concept_name'] = $row['name'];
    if (!isset($row['concept_name']) && isset($row['global_concept_name'])) $row['concept_name'] = $row['global_concept_name'];
    if (!isset($row['reference_id']) && isset($row['global_id'])) $row['reference_id'] = $row['global_id'];
    if (!isset($row['reference_id']) && isset($row['global_concept'])) $row['reference_id'] = $row['global_concept'];
    if (!isset($row['reference_id']) && isset($row['concept'])) $row['reference_id'] = $row['concept'];
    if (!isset($row['reference_id']) && isset($row['id'])) $row['reference_id'] = $row['id'];
    if (!empty($row['definition']) && is_array($row['definition'])) $row['definition'] = $row['definition'][$lang];

    #print "<pre>";
    #print_r($row);
    #print "</pre>";

    $row['wu'] = "http://$lang.wikipedia.org/wiki/" . urlencode($row['concept_name']); 
    #$row['cu'] = "$wwSelf?id=" . urlencode($row['concept']) . "&lang=" . urlencode($lang); 
    $row['cu'] = "$wwSelf?id=" . urlencode($row['reference_id']) . "&lang=" . urlencode($lang); 
    $row['gu'] = "$wwSelf?id=" . urlencode($row['reference_id']) . "&images=g"; 

    if (!isset($row['weight']) || !$row['weight']) { 
      $row['wclass'] = "unknown";
      $row['weight'] = NULL;
    }
    else if ($row['weight']>1000) $row['wclass'] = "huge";
    else if ($row['weight']>100) $row['wclass'] = "big";
    else if ($row['weight']>10) $row['wclass'] = "normal";
    else if ($row['weight']>2) $row['wclass'] = "some";
    else $row['wclass'] = "little";

    return $row;
}

function printLocalConcept($a_lang, $a_row, $b_lang, $b_row, $pos = 0, $terse = true) {
    global $wwSelf, $images, $utils;
    global $wwMaxPreviewImages, $wwMaxGalleryImages;

    $a_row = normalizeConceptRow($a_lang, $a_row);
    $b_row = normalizeConceptRow($b_lang, $b_row);

    if ($a_lang && $a_row) extract($a_row, EXTR_PREFIX_ALL, "a");
    if ($b_lang && $b_row) extract($b_row, EXTR_PREFIX_ALL, "b");

    ?>
    <tr class="row_item">
      <td class="cell_weight  <?php print "weight_$a_wclass"; ?>"><?php print htmlspecialchars($a_weight); ?></td>
      <td colspan="3" class="cell_name  <?php print "weight_$a_wclass"; ?>">
	<h3>
	<a href="<?php print htmlspecialchars($a_cu); ?>"><?php print htmlspecialchars($a_concept_name); ?></a>
	<span class="conceptref">(<a href="<?php print htmlspecialchars($a_wu); ?>" title="<?php print htmlspecialchars($a_concept_name); ?>">wiki page</a>)</span>
	</h3>
      </td>
      <?php if ($b_row) { ?>
      <td colspan="3" class="cell_name  <?php print "weight_$b_wclass"; ?>">
	<h3>
	<a href="<?php print htmlspecialchars($b_cu); ?>"><?php print htmlspecialchars($b_concept_name); ?></a>
	<span class="conceptref">(<a href="<?php print htmlspecialchars($b_wu); ?>" title="<?php print htmlspecialchars($b_concept_name); ?>">wiki page</a>)</span>
	</h3>
      </td>
      <?php } ?>
    </tr>

    <?php if (isset($a_definition) && !empty($a_definition)) { ?>
    <tr class="row_def">
      <td></td>
      <td class="cell_label">Definition:</td>
      <td colspan="2"><?php print htmlspecialchars($a_definition); ?></td>
      <?php if ($b_row) { ?>
      <td class="cell_label">Definition:</td>
      <td colspan="2"><?php print htmlspecialchars($b_definition); ?></td>
      <?php } ?>
    </tr>
    <?php } ?>

    <?php if (isset($a_terms) && !empty($a_terms)) { ?>
    <tr class="row_details row_terms">
      <td></td>
      <td class="cell_label">Terms:</td>
      <td class="cell_terms" colspan="2"><?php printTermList($a_lang, $a_terms); ?></td>
      <?php if ($b_row) { ?>
      <td class="cell_label">Terms:</td>
      <td class="cell_terms" colspan="2"><?php printTermList($b_lang, $b_terms); ?></td>
      <?php } ?>
    </tr>
    <?php } ?>

    <?php if ($images) { ?>
    <tr class="row_details row_images">
      <td></td>
      <td class="cell_label">Images:</td>
      <td class="cell_broader" colspan="<?php $b_row ? 5 : 2 ?>">
      <?php 
	  $gallery = $utils->getImagesAbout($a_reference_id, $terse ? $wwMaxPreviewImages : $wwMaxGalleryImages );
	  $c = printConceptImageList( $gallery, $terse ? "terselist" : "gallery" ); 
      ?>
      </td>
    </tr>
    <?php } ?>

    <?php if (isset($a_similar) && !empty($a_similar)) { ?>
    <tr class="row_details row_similar">
      <td></td>
      <td class="cell_label">Similar:</td>
      <td class="cell_similar" colspan="2"><?php printLocalConceptList($a_lang, $a_similar); ?></td>
      <?php if ($b_row) { ?>
      <td class="cell_label">Similar:</td>
      <td class="cell_similar" colspan="2"><?php printLocalConceptList($b_lang, $b_similar); ?></td>
      <?php } ?>
    </tr>
    <?php } ?>

    <?php if (isset($a_related) && !empty($a_related)) { ?>
    <tr class="row_details row_related">
      <td></td>
      <td class="cell_label">Related:</td>
      <td class="cell_related" colspan="2"><?php printLocalConceptList($a_lang, $a_related); ?></td>
      <?php if ($b_row) { ?>
      <td class="cell_label">Related:</td>
      <td class="cell_related" colspan="2"><?php printLocalConceptList($b_lang, $b_related); ?></td>
      <?php } ?>
    </tr>
    <?php } ?>

    <?php if (isset($a_narrower) && !empty($a_narrower)) { ?>
    <tr class="row_details row_narrower">
      <td></td>
      <td class="cell_label">Narrower:</td>
      <td class="cell_narrower" colspan="2"><?php printLocalConceptList($a_lang, $a_narrower); ?></td>
      <?php if ($b_row) { ?>
      <td class="cell_label">Narrower:</td>
      <td class="cell_narrower" colspan="2"><?php printLocalConceptList($b_lang, $b_narrower); ?></td>
      <?php } ?>
    </tr>
    <?php } ?>

    <?php if (isset($a_broader) && !empty($a_broader)) { ?>
    <tr class="row_details row_broader">
      <td></td>
      <td class="cell_label">Broader:</td>
      <td class="cell_broader" colspan="2"><?php printLocalConceptList($a_lang, $a_broader); ?></td>
      <?php if ($b_row) { ?>
      <td class="cell_label">Broader:</td>
      <td class="cell_broader" colspan="2"><?php printLocalConceptList($b_lang, $b_broader); ?></td>
      <?php } ?>
    </tr>
    <?php } ?>

    <?php
    if (isset($a_weight) && $a_weight && $a_weight<2 && $pos>=3) return false;
    else return true;
}

$concept = @$_REQUEST['id'];
$term = @$_REQUEST['term'];
$lang = @$_REQUEST['lang'];
$tolang = @$_REQUEST['tolang'];
$images = (@$_REQUEST['images'] || $wwImageSearch === true ) && !($wwImageSearch === false);

if (!isset($_REQUEST['translate'])) $tolang = NULL;
if ($lang == $tolang) $tolang = NULL;

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

$result = NULL;

if (!$error) {
  try {
      if ($lang && $concept) {
	  $result = $thesaurus->getConceptInfo($concept, $lang);
	  if ( $result ) $result = array( $result ); //hack
      } else if ($lang && $term) {
	  $result = $thesaurus->getConceptsForTerm($lang, $term, $limit);
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
	  <?php if ($wwImageSearch === null) { ?>
	  <td>
	    <label for="images">Images: </label>
	    <input type="checkbox" name="images" value="Images" <?php print $images ? " checked=\"checked\"" : ""?>/>
	  </td>
	  <?php } ?>
	</tr>
	<?php if ($wwAllowTranslate) { ?>
	<tr>
	  <td>
	    &nbsp;
	  </td>
	  <td>
	    <label for="term" style="display:none">Translate: </label>
	    <?php WWUtils::printSelector("tolang", $wwLanguages, @$_REQUEST['tolang']) ?>
	  </td>
	  <td>
	    <input type="submit" name="translate" value="translate"/>
	  </td>
	</tr>
	<?php } ?>
      </table>
      <p class="note">Note: this is a thesaurus lookup, not a full text search. Only exact matches are considered, matching is case-sensitive.</p>
    </form>
    </div>
<?php
if ($error) {
  print "<p class=\"error\">".htmlspecialchars($error)."</p>";
}
?>    

<?php
if ($result && $concept) {
    foreach ( $result as $row ) {
	if (@$row['id']) $id = $row['id'];
	else if (@$row['concept']) $id = $row['concept'];
	else $id = "concept";

?>    
    <div id="<?php print htmlspecialchars("concept-$id")?>">

    <table  border="0" class="results">
    <?php 
	  if ($lang) {
	      $continue= printLocalConcept($lang, $row, NULL, NULL, 0, false);
	  }
	  //else $continue= printGlobalConcept($lang, $row, $count);

	  if (!$continue) break;
    ?>
    </table>
    </div>

<?php
      } #concept loop
} else if ($result && $term) {
    if ($tolang) $title = "$lang: $term -> $tolang";
    else if ($term) $title = "$lang: $term";
?>    
    <h2><?php print htmlspecialchars($title); ?></h2>
    <table  border="0" class="results">
    <?php 
      $count = 0;
      foreach ( $result as $row ) {
	  $count += 1;

	  if ($lang) {
	      $show_single = true;

	      if ($tolang && isset($row['global_concept'])) {
		  $toresult = $utils->queryConceptInfo($row['global_concept'], $tolang);
		  while ($torow = mysql_fetch_assoc($toresult)) {
		      $continue= printLocalConcept($lang, $row, $tolang, $torow, $count, true);
		      $show_single = false;
		  }
		  mysql_free_result($toresult);
	      } 

	      if ($show_single) {
		  $continue= printLocalConcept($lang, $row, NULL, NULL, $count, true);
	      }
	  }
	  //else $continue= printGlobalConcept($lang, $row, $count);

	  if (!$continue) break;
      }
    ?>
    </table>

    <p>Found <?php print $count; ?> items.</p>

<?php
}
?>

<p class="footer">
The WikiWord Navigator is part of the <a href="http://wikimedia.de">Wikimedia</a> project <a href="http://brightbyte.de/page/WikiWord">WikiWord</a>
<p>
</body>
</html>
<?php
$utils->close();
?>