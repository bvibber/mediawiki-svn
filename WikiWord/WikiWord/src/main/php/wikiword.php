<?php

$IP = dirname(__FILE__);

require_once("$IP/config.php");
require_once("$IP/wwutils.php");

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

function printLocalConcept($lang, $row, $pos = 0) {
    global $wwSelf;

    extract($row);

    if (!isset($weight) && isset($freq)) $weight = $freq;

    $wu = "http://$lang.wikipedia.org/wiki/" . urlencode($concept_name); 
    $cu = "$wwSelf?id=" . urlencode($concept) . "&lang=" . urlencode($lang); 

    if (!$weight) $wclass = "x";
    else if ($weight>1000) $wclass = "huge";
    else if ($weight>100) $wclass = "big";
    else if ($weight>10) $wclass = "normal";
    else if ($weight>2) $wclass = "some";
    else $wclass = "little";

    ?>
    <tr class="row_item">
      <td class="col_weight  <?php print "weight_$wclass"; ?>"><?php print htmlspecialchars($weight); ?></td>
      <td class="col_name  <?php print "weight_$wclass"; ?>"><a href="<?php print htmlspecialchars($wu); ?>"><?php print htmlspecialchars($concept_name); ?></a></td>
      <td class="col_concept  <?php print "weight_$wclass"; ?>">(#<a href="<?php print htmlspecialchars($cu); ?>"><?php print htmlspecialchars($concept); ?></a>)</td>
    </tr>
    <tr class="row_def">
      <td></td>
      <td colspan="2"><?php print htmlspecialchars($definition); ?></td>
    </tr>
    <?php
    if ($weight && $weight<2 && $pos>=3) return false;
    else return true;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr"> 
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>WikiWord Navigator</title>

    <style type="text/css">
	.error { color: red; font-weight: bold; }
	.weight_huge { font-size: 140%; font-weight:bold; }
	.weight_big { font-size: 120%; font-weight:bold; }
	.weight_normal { font-size: 110%; font-weight:bold; }
	.weight_some { font-size: 100%; font-weight:normal; }
	.weight_little { font-size: 90%; font-weight:normal; }
	.row_def td { font-size: 80%; }
	.col_weight td { text-align: right; }
    </style>
</head>
<body>
    <h1>WikiWord Navigator</h1>
    <p>Experimental proof of concept <a href="http://brightbyte.de/page/WikiWord">WikiWord</a> navigator.</p>

    <form name="search" action="<?php print $wwSelf; ?>">
      <p>
      <label for="term">Term: </label><input type="text" name="term" id="term" size="24" value="<?php print htmlspecialchars($term); ?>"/>
      <label for="term">Language: </label>
	<?php WWUtils::printSelector("lang", $wwLanguages, $lang) ?>
      <input type="submit" value="go"/>
      </p>
      <p>Note: this is a thesaurus lookup, not a full text search. Only exact matches are considered, matching is case-sensitive.</p>
    </form>
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
</body>
</html>
<?php
$utils->close();
?>