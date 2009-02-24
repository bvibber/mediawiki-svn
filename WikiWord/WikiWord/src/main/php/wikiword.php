<?php

$IP = dirname(__FILE__);

require_once("$IP/config.php");
require_once("$IP/wwutils.php");

$concept = @$_REQUEST['id'];
$term = @$_REQUEST['term'];
$lang = @$_REQUEST['lang'];

$error = NULL;

$utils = new WWUtils();
$utils->connect($wwDBServer, $wwDBUser, $wwDBPassword, $wwDBDatabase);

if ($id) {
    $result = $utils->queryConceptInfo($lang, $id);
} else {
    $result = $utils->queryConceptsForTerm($lang, $id);
}

function print_result_row($row) {
    extract($row);

    $bu = "http://www.bridgemanart.com/Search.aspx?key=artist:" . urlencode($bridgeman_name);
    $wu = "http://en.wikipedia.org/wiki/" . urlencode($wikiword_name); //FIXME: which wiki?!

    print "\t\t<li>";
    print "<big><b><a href=\"".htmlspecialchars($bu)."\">".htmlspecialchars($bridgeman_name)."</a></b></big> ";
    print "<small>(#".htmlspecialchars($bridgeman_id).")</small> ";
    print "<br/><small><b><a href=\"".htmlspecialchars($wu)."\">".htmlspecialchars($wikiword_name)."</a></b>";
    if ($definition) print ": " . htmlspecialchars($definition);
    print "</small>";
    print "</li>\n";
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr"> 
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Experimental Bridgeman Search</title>
</head>
<body>
    <h1>Experimental Bridgeman Search</h1>
    <p>This is a very rough interface to a first preview of an effort to tag and index Bidgeman's artist list by using Data from Wikipedia.
    This is proof of concept and work in progress. It's not intended for public use.</p>

    <form name="search">
      <p>
      <lable for="q">Artist: </label><input type="text" name="q" id="q" length="24" value="<?php print htmlspecialchars($query); ?>"/>
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
	  print_result_row($row);
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