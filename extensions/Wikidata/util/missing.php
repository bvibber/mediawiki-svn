<?php
//header("Content-type: text/html; charset=UTF-8");

define('MEDIAWIKI', true );
include_once("../../../LocalSettings.php");
global $wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname;

$db1=$wgDBserver;  # hostname
$db2=$wgDBuser;  # user
$db3=$wgDBpassword;  # pass
$db4=$wgDBname;  # db-name

$connection=MySQL_connect($db1,$db2,$db3);
if (!$connection)die("Cannot connect to SQL server. Try again later.");
MySQL_select_db($db4)or die("Cannot open database");
mysql_query("SET NAMES 'utf8'");

echo "
<style type=\"text/css\"><!--
body {font-family:arial,sans-serif}
--></style>
";

function stopwatch(){
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}


$start=stopwatch();

$collection_id=$_REQUEST['collection'];
$language_id=$_REQUEST['language'];
$collection_esc=mysql_real_escape_string($collection_id);
$language_esc=mysql_real_escape_string( $language_id);

$result = mysql_query(
"SELECT spelling 
FROM uw_collection_ns, uw_defined_meaning, uw_expression_ns
WHERE collection_id=$collection_id
AND collection_mid=defined_meaning_id 
AND uw_defined_meaning.expression_id=uw_expression_ns.expression_id
")or die ("error ".mysql_error());

$row= mysql_fetch_array($result, MYSQL_NUM);
$collection= $row[0];

$result = mysql_query("SELECT language_name
FROM language_names 
where name_language_id = 85
and language_id=$language_id
")or die ("error ".mysql_error());

$row= mysql_fetch_array($result, MYSQL_NUM);
$language=$row[0];

echo"
<h1>$collection</h1>
<h2>$language</h2>
<small><i>For large collections, this query might take up to a minute. Please be patient</i></small>
<hr width=950 size=1 noshade><br>
<h3> Missing defined meanings </h3>

";

$result = mysql_query(" 
	SELECT en.id, en.spelling 
	FROM 
	(
		SELECT member_mid as id, spelling
		FROM uw_collection_contents, uw_syntrans, uw_expression_ns 
		WHERE collection_id = $collection_esc 
		AND uw_syntrans.defined_meaning_id= uw_collection_contents.member_mid 
		AND uw_expression_ns.expression_id = uw_syntrans.expression_id 
		AND language_id=85 
		AND uw_syntrans.remove_transaction_id IS NULL 
		ORDER BY spelling
	) as en 
	LEFT JOIN 
	(
		SELECT member_mid as id, spelling 
		FROM uw_collection_contents, uw_syntrans, uw_expression_ns WHERE
		collection_id = $collection_esc 
		AND uw_syntrans.defined_meaning_id= uw_collection_contents.member_mid 
		AND uw_expression_ns.expression_id = uw_syntrans.expression_id 
		AND language_id = $language_esc 
		AND uw_syntrans.remove_transaction_id IS NULL 
		ORDER BY spelling
	) as actual 
	ON en.id=actual.id 
	WHERE actual.id IS NULL
")or die ("error ".mysql_error());



while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	$id=$row[0];
	$spelling=$row[1];
	print "<a href=\"../../../index.php?title=DefinedMeaning:".$spelling."_($id)\">$spelling</a>;\n";
}
print "<br>\n";

print "<hr>\n
<h3>Already present</h3>\n";
$result = mysql_query(" 
	SELECT actual.id, actual.spelling 
	FROM 
	(
		SELECT member_mid as id, spelling
		FROM uw_collection_contents, uw_syntrans, uw_expression_ns 
		WHERE collection_id = $collection_esc 
		AND uw_syntrans.defined_meaning_id= uw_collection_contents.member_mid 
		AND uw_expression_ns.expression_id = uw_syntrans.expression_id 
		AND language_id=85 
		AND uw_syntrans.remove_transaction_id IS NULL 
		ORDER BY spelling
	) as en 
	LEFT JOIN 
	(
		SELECT member_mid as id, spelling 
		FROM uw_collection_contents, uw_syntrans, uw_expression_ns WHERE
		collection_id = $collection_esc 
		AND uw_syntrans.defined_meaning_id= uw_collection_contents.member_mid 
		AND uw_expression_ns.expression_id = uw_syntrans.expression_id 
		AND language_id = $language_esc 
		AND uw_syntrans.remove_transaction_id IS NULL 
		ORDER BY spelling
	) as actual 
	ON en.id=actual.id 
	WHERE actual.id IS NOT NULL
")or die ("error ".mysql_error());


while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
	$id=$row[0];
	$spelling=$row[1];
	print "<a href=\"../../../index.php?title=DefinedMeaning:".$spelling."_($id)\">$spelling</a>;\n";
}



echo"
<hr>
<div align=\"right\">
<small>Page time: ".substr((stopwatch()-$start),0,5)." seconds</small>
</div>
Notes:
<ul>
<li>Particular (typically common) words occur multiple times. This is because these words have multiple (defined) meanings.</li>
</ul>
<hr>
<p align=\"left\">
<h3> see also</h3>
<ul>
<li><a href=\"collection.php?collection=$collection_id\">Return to  Number of Expressions per language in this collection</a></li>
<li><a href=\"stats.php\">Overview, expressions per langauge</a></li>
<li><a href=\"../../..\">return to Omegawiki proper</li></a>
</p>
"
?>
