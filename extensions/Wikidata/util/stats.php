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

echo "
<style type=\"text/css\"><!--
body {font-family:arial,sans-serif}
--></style>
";

function stopwatch(){
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

/*
$result = mysql_query("SELECT 
uw_defined_meaning.defined_meaning_id , uw_expression_ns.spelling
FROM uw_defined_meaning, uw_expression_ns
where uw_defined_meaning.defined_meaning_id=1446
and uw_defined_meaning.expression_id=uw_expression_ns.expression_id
limit 0,40")or die ("error ".mysql_error());

*/

$start=stopwatch();

$result = mysql_query("SELECT *
FROM language_names 
where name_language_id = 85
")or die ("error ".mysql_error());

while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
//echo $row[0]." - ".$row[1]." - ".$row[2]."<br>";
$lang[$row[0]]=$row[2];
}


////////////////////////////////////////////////////////
$result = mysql_query("SELECT 
language_id, count(*)
FROM uw_expression_ns
group by language_id
order by count(*) desc
 ")or die ("error ".mysql_error());

echo "<center>

<h1>Number of expressions per language</h1>
<hr width=950 size=1 noshade><br>
<table cellpadding=0 width=950><tr><td width=200><b>Language</b></td><td><b>Entries</b></td><td></td></tr>";
$width=600;
$limit=500;
$max=0;
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
if($max<$row[1])$max=$row[1];
$wi=ceil((($row[1]/$max)*$width));
$per=ceil((($row[1]/$max)*100));
if($row[1]>$limit)echo "<tr><td >".$lang[$row[0]]."</td><td>".$row[1]."</td><td><img src=sc1.png width=\"$wi\" height=20> $per %</td></tr>";
else $tx.=$lang[$row[0]].", ";
//$ar[$row[0]].=$row[1]."	".$row[2]."\n";
//filewrite("out/".$row[0].".txt",$row[1]."	".$row[2]);
}
echo "
<tr><td colspan=4>
<div align=justify>

<h3>Languages with less than $limit entries:</h3>
$tx
</div>
</td>
</table><center>";

/*
for($i=0;$i<250;$i++){
if(strlen($ar[$i])>20)filewrite("out/".$lang[$i].".txt",$ar[$i]);

}
*/
////////////////////////////////////////////////////////


//echo "<pre>".$ar[85]."</pre>";

echo "
<br>
<hr size=1 noshade width=950>
<table width=950><tr><td>
<small>Page time: ".substr((stopwatch()-$start),0,5)." seconds</small>
<td align=right>

<small>Script contributed by <a href=http://www.dicts.info/>Zdenek Broz</a>
</small>
</td>
</tr></table>
<br>";

function filewrite($file,$txt){
$fw=fopen($file,"w+");
fwrite($fw,$txt."\n");
fclose($fw);
}




?>
