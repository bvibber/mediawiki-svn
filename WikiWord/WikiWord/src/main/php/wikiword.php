<?php

$IP = dirname(__FILE__);

require_once("$IP/config.php");
require_once("$IP/wwutils.php");

function printLocalConceptList($lang, $concepts) {
    if (is_string($concepts)) $concepts = WWUtils::unpickle($concepts);

    ?>
    <ul class="terselist">
      <?php
	foreach ($concepts as $c) {
	    printLocalConceptLink($lang, $c);
	}
      ?>
    </ul>
    <?php
}

function printLocalConceptLink($lang, $row) {
    global $wwSelf;

    extract($row);

    if (!isset($weight) && isset($freq)) $weight = $freq;
    if (!isset($weight) && isset($conf)) $weight = $conf;
    if (!isset($concept_name) && isset($name)) $concept_name = $name;
    if (!isset($concept_name) && isset($name)) $concept_name = NULL;
    if (!isset($concept) && isset($id)) $concept = $id;
    if (!isset($concept) && isset($id)) $concept = NULL;

    $wu = $concept_name ? "http://$lang.wikipedia.org/wiki/" . urlencode($concept_name) : NULL; 
    $cu = "$wwSelf?id=" . urlencode($concept) . "&lang=" . urlencode($lang); 

    ?>
    <li>
	<?php if ($concept_name) { ?>
	  <a href="<?php print htmlspecialchars($wu); ?>"><?php print htmlspecialchars($concept_name); ?></a>
	<?php } ?>
	<?php if ($concept) { ?>
	  (#<a href="<?php print htmlspecialchars($cu); ?>"><?php print htmlspecialchars($concept); ?></a>)
	<?php } ?>
    </li>
    <?php
}

function printTermList($lang, $terms) {
    if (is_string($terms)) $terms = WWUtils::unpickle($terms);

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

function printLocalConcept($lang, $row, $pos = 0) {
    global $wwSelf;

    extract($row);

    if (!isset($weight) && isset($freq)) $weight = $freq;
    if (!isset($weight) && isset($conf)) $weight = $conf;
    if (!isset($concept_name) && isset($name)) $concept_name = $name;
    if (!isset($concept) && isset($id)) $concept = $id;

    $wu = "http://$lang.wikipedia.org/wiki/" . urlencode($concept_name); 
    $cu = "$wwSelf?id=" . urlencode($concept) . "&lang=" . urlencode($lang); 

    if (!isset($weight) || !$weight) { 
      $wclass = "x";
      $weight = NULL;
    }
    else if ($weight>1000) $wclass = "huge";
    else if ($weight>100) $wclass = "big";
    else if ($weight>10) $wclass = "normal";
    else if ($weight>2) $wclass = "some";
    else $wclass = "little";

    ?>
    <tr class="row_item">
      <td class="cell_weight  <?php print "weight_$wclass"; ?>"><?php print htmlspecialchars($weight); ?></td>
      <td colspan="3" class="cell_name  <?php print "weight_$wclass"; ?>">
	<a href="<?php print htmlspecialchars($wu); ?>"><?php print htmlspecialchars($concept_name); ?></a>
	<span class="conceptref">(#<a href="<?php print htmlspecialchars($cu); ?>"><?php print htmlspecialchars($concept); ?></a>)</span>
      </td>
    </tr>

    <tr class="row_def">
      <td></td>
      <td class="cell_label">Definition:</td>
      <td colspan="2"><?php print htmlspecialchars($definition); ?></td>
    </tr>

    <?php if (isset($terms) && !empty($terms)) { ?>
    <tr class="row_details row_terms">
      <td></td>
      <td class="cell_label">Terms:</td>
      <td class="cell_terms" colspan="2"><?php printTermList($lang, $terms); ?></td>
    </tr>
    <?php } ?>

    <?php if (isset($similar) && !empty($similar)) { ?>
    <tr class="row_details row_similar">
      <td></td>
      <td class="cell_label">Similar:</td>
      <td class="cell_similar" colspan="2"><?php printLocalConceptList($lang, $similar); ?></td>
    </tr>
    <?php } ?>

    <?php if (isset($related) && !empty($related)) { ?>
    <tr class="row_details row_related">
      <td></td>
      <td class="cell_label">Related:</td>
      <td class="cell_related" colspan="2"><?php printLocalConceptList($lang, $related); ?></td>
    </tr>
    <?php } ?>

    <?php if (isset($narrower) && !empty($narrower)) { ?>
    <tr class="row_details row_narrower">
      <td></td>
      <td class="cell_label">Narrower:</td>
      <td class="cell_narrower" colspan="2"><?php printLocalConceptList($lang, $narrower); ?></td>
    </tr>
    <?php } ?>

    <?php if (isset($broader) && !empty($broader)) { ?>
    <tr class="row_details row_broader">
      <td></td>
      <td class="cell_label">Broader:</td>
      <td class="cell_broader" colspan="2"><?php printLocalConceptList($lang, $broader); ?></td>
    </tr>
    <?php } ?>
    <?php
    if (isset($weight) && $weight && $weight<2 && $pos>=3) return false;
    else return true;
}

$concept = @$_REQUEST['id'];
$term = @$_REQUEST['term'];
$lang = @$_REQUEST['lang'];

if (!isset($wwSelf)) $wwSelf = @$_SERVER["PHP_SELF"];

$error = NULL;

if ($lang && !isset($wwLanguages[$lang])) {
    $lang = NULL;
    $error = "bad language code: $lang";
}

$utils = new WWUtils();
$utils->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);

if (@$_REQUEST['debug']) $utils->debug = true;

$result = NULL;

if (!$error) {
  try {
      if ($concept) {
	  if ($lang) {
	    $result = $utils->queryLocalConceptInfo($lang, $concept);
	  } else {
	    $result = $utils->queryGlobalConceptInfo($concept);
	  }
      } else if ($lang) {
	  $result = $utils->queryConceptsForTerm($lang, $term);
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
	.weight_some { font-size: 100%; font-weight:normal; }
	.weight_little { font-size: 90%; font-weight:normal; }
	.row_def td { font-size: 80%; font-style:italic; }
	.row_details td { font-size: 80%; }
	.cell_weight { text-align: right; }
	.cell_label { text-align: right; }
	.header { text-align: left; }
	.inputform { text-align: center; margin:1ex auto; padding:1ex; width:80%; border:1px solid #666666; background-color:#DDDDDD; }
	.footer { font-size:80%; text-align: center; border-top: 1px solid #666666; }
	.note { font-size:80%; }
	.terselist, .terselist li { display: inline; }
	.terselist li { display: inline; }
	.terselist li:before { content:" - " }
	.terselist li:first-child:before { content:"" }
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
      <p>
      <label for="term">Term: </label><input type="text" name="term" id="term" size="24" value="<?php print htmlspecialchars($term); ?>"/>
      <label for="term">Language: </label>
	<?php WWUtils::printSelector("lang", $wwLanguages, $lang) ?>
      <input type="submit" value="go"/>
      </p>
      <p class="note">Note: this is a thesaurus lookup, not a full text search. Only exact matches are considered, matching is case-sensitive.</p>
    </form>
    </div>
<?php
if ($error) {
  print "<p class=\"error\">".htmlspecialchars($error)."</p>";
}
?>    

<?php
if ($result) {
?>    
    <table  border="1" class="results">
    <?php 
      $count = 0;
      while ($row = mysql_fetch_assoc($result)) {
	  $count += 1;
	  if ($lang) $continue= printLocalConcept($lang, $row, $count);
	  else $continue= printGlobalConcept($lang, $row, $count);

	  if (!$continue) break;
      }

      mysql_free_result($result);
    ?>
    </table>

    <p>Found <?php print $count; ?> items.</p>

<?php
}
?>

<p class="footer">
The WikiWord Navigator is part of the WikiWord project <a href="http://brightbyte.de/page/WikiWord">WikiWord</a>
<p>
</body>
</html>
<?php
$utils->close();
?>