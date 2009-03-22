<?php

$IP = dirname(__FILE__);

require_once("$IP/config.php");
require_once("$IP/wwutils.php");

function printLocalConceptList($lang, $concepts) {
    global $utils;
    if (is_string($concepts)) $concepts = $utils->unpickle($concepts, $lang);

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
    if (!isset($concept_name)) $concept_name = NULL;
    if (!isset($concept) && isset($id)) $concept = $id;
    if (!isset($concept)) $concept = NULL;

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

    if (!$row) return $row;

    $row['lang'] = $lang;

    if (!isset($row['weight']) && isset($row['freq'])) $row['weight'] = $row['freq'];
    if (!isset($row['weight']) && isset($row['conf'])) $row['weight'] = $row['conf'];
    if (!isset($row['concept_name']) && isset($row['name'])) $row['concept_name'] = $row['name'];
    if (!isset($row['concept']) && isset($row['id'])) $row['concept'] = $row['id'];
    if (!isset($row['concept']) && isset($row['global_id'])) $row['concept'] = $row['global_id'];
    if (!isset($row['concept']) && isset($row['global_concept'])) $row['concept'] = $row['global_concept'];

    $row['wu'] = "http://$lang.wikipedia.org/wiki/" . urlencode($row['concept_name']); 
    $row['cu'] = "$wwSelf?id=" . urlencode($row['concept']) . "&lang=" . urlencode($lang); 

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

function printLocalConcept($a_lang, $a_row, $b_lang, $b_row, $pos = 0) {
    global $wwSelf;

    $a_row = normalizeConceptRow($a_lang, $a_row);
    $b_row = normalizeConceptRow($b_lang, $b_row);

    if ($a_lang && $a_row) extract($a_row, EXTR_PREFIX_ALL, "a");
    if ($b_lang && $b_row) extract($b_row, EXTR_PREFIX_ALL, "b");

    ?>
    <tr class="row_item">
      <td class="cell_weight  <?php print "weight_$a_wclass"; ?>"><?php print htmlspecialchars($a_weight); ?></td>
      <td colspan="3" class="cell_name  <?php print "weight_$a_wclass"; ?>">
	<a href="<?php print htmlspecialchars($a_wu); ?>"><?php print htmlspecialchars($a_concept_name); ?></a>
	<span class="conceptref">(#<a href="<?php print htmlspecialchars($a_cu); ?>"><?php print htmlspecialchars($a_concept); ?></a>)</span>
      </td>
      <?php if ($b_row) { ?>
      <td colspan="3" class="cell_name  <?php print "weight_$b_wclass"; ?>">
	<a href="<?php print htmlspecialchars($b_wu); ?>"><?php print htmlspecialchars($b_concept_name); ?></a>
	<span class="conceptref">(#<a href="<?php print htmlspecialchars($b_cu); ?>"><?php print htmlspecialchars($b_concept); ?></a>)</span>
      </td>
      <?php } ?>
    </tr>

    <tr class="row_def">
      <td></td>
      <td class="cell_label">Definition:</td>
      <td colspan="2"><?php print htmlspecialchars($a_definition); ?></td>
      <?php if ($b_row) { ?>
      <td colspan="2"><?php print htmlspecialchars($b_definition); ?></td>
      <?php } ?>
    </tr>

    <?php if (isset($a_terms) && !empty($a_terms)) { ?>
    <tr class="row_details row_terms">
      <td></td>
      <td class="cell_label">Terms:</td>
      <td class="cell_terms" colspan="2"><?php printTermList($a_lang, $a_terms); ?></td>
      <?php if ($b_row) { ?>
      <td class="cell_terms" colspan="2"><?php printTermList($b_lang, $b_terms); ?></td>
      <?php } ?>
    </tr>
    <?php } ?>

    <?php if (isset($a_similar) && !empty($a_similar)) { ?>
    <tr class="row_details row_similar">
      <td></td>
      <td class="cell_label">Similar:</td>
      <td class="cell_similar" colspan="2"><?php printLocalConceptList($a_lang, $a_similar); ?></td>
      <?php if ($b_row) { ?>
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

if (!isset($_REQUEST['translate'])) $tolang = NULL;
if ($lang == $tolang) $tolang = NULL;

if (!isset($wwSelf)) $wwSelf = @$_SERVER["PHP_SELF"];

$error = NULL;

if ($lang && !isset($wwLanguages[$lang])) {
    $lang = NULL;
    $error = "bad language code: $lang";
}

$utils = new WWUtils();
$utils->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);

if (@$_REQUEST['debug']) $utils->debug = true;

$limit = 20;

$result = NULL;

if (!$error) {
  try {
      if ($concept) {
	  if ($lang) {
	    $result = $utils->queryLocalConceptInfo($lang, $concept);
	  } /*else {
	    $result = $utils->queryGlobalConceptInfo($concept);
	  }*/
      } else if ($lang && $term) {
	  $result = $utils->queryConceptsForTerm($lang, $term, $limit);
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
if ($result) {
?>    
    <table  border="0" class="results">
    <?php 
      $count = 0;
      while ($row = mysql_fetch_assoc($result)) {
	  $count += 1;

	  if ($lang) {
	      $show_single = true;

	      if ($tolang && isset($row['global_concept'])) {
		  $toresult = $utils->queryLocalConceptInfo($tolang, $row['global_concept']);
		  while ($torow = mysql_fetch_assoc($toresult)) {
		      $continue= printLocalConcept($lang, $row, $tolang, $torow, $count);
		      $show_single = false;
		  }
		  mysql_free_result($toresult);
	      } 

	      if ($show_single) {
		  $continue= printLocalConcept($lang, $row, NULL, NULL, $count);
	      }
	  }
	  //else $continue= printGlobalConcept($lang, $row, $count);

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