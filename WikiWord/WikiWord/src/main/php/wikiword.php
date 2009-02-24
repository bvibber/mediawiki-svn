<?php

$IP = dirname(__FILE__);

require_once("$IP/config.php");
require_once("$IP/wwutils.php");

$concept = @$_REQUEST['id'];
$term = @$_REQUEST['term'];
$lang = @$_REQUEST['lang'];

$error = NULL;

if ($lang && !isset($wwLanguages[$lang])) {
    $lang = NULL;
    $error = "bad language code: $lang";
}

$utils = new WWUtils();
$utils->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);

if (!$error) {
  try {
      if ($id) {
	  if ($lang) {
	    $result = $utils->queryLocalConceptInfo($lang, $id);
	  } else {
	    $result = $utils->queryGlobalConceptInfo($id);
	  }
      } else if ($lang) {
	  $result = $utils->queryConceptsForTerm($lang, $id);
      }
  } catch (Exception $e) {
      $error = $e->getMessage();
  }
}

function printLocalConcept($lang, $row) {
    extract($row);

    if (!isset($weight) && isset($freq)) $weight = $freq;

    $wu = "http://$lang.wikipedia.org/wiki/" . urlencode($concept_name); 
    $cu = "$wwSelf?id=" . urlencode($concept) . "&lang=" . urlencode($lang); 

    print "\t\t<li>";
    if (isset($weight) && !empty($weight)) print "<b>" . htmlspecialchars($weight) . "</b> ";
    print "<big><b><a href=\"".htmlspecialchars($wu)."\">".htmlspecialchars($concept_name)."</a></b></big> ";
    print " (<a href=\"".htmlspecialchars($cu)."\">#".htmlspecialchars($concept)."</a>) ";
    if (isset($definition) && !empty($definition)) print "<br/><small>" . htmlspecialchars($definition) . "</small>";
    print "</li>\n";
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr"> 
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>WikiWord Navigator</title>
</head>
<body>
    <h1>WikiWord Navigator</h1>
    <p>Experimental proof of concept <a href="http://brightbyte.de/page/WikiWord">WikiWord</a> navigator.</p>

    <form name="search">
      <p>
      <lable for="term">Term: </label><input type="text" name="term" id="term" length="24" value="<?php print htmlspecialchars($term); ?>"/>
      <lable for="term">Language: </label>
	<?php WWUtils::printSelector("lang", $wwLanguages, $lang) ?>
      <input type="submit" value="go"/>
      </p>
    </form>
<?php
if ($error) {
  print "<p class=\"error\">".htmlspecialchars($error)."</p>";
}
?>    

<?php
if ($result) {
?>    
    <ul>
    <?php 
      $count = 0;
      while ($row = mysql_fetch_assoc($result)) {
	  if ($lang) printLocalConcept($lang, $row);
	  else printGlobalConcept($lang, $row);
	  $count += 1;
      }

      mysql_free_result($result);
    ?>
    </ul>

    <p>Found <?php print $count; ?> items.</p>

<?php
}
?>
</body>

<?php
mysql_close($db);
?>