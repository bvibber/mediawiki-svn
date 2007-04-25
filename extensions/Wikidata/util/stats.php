<?php

header("Content-type: text/html; charset=iso-8859-1");

$db1="localhost";  # hostname
$db2="root";  # user
$db3="nicheGod";  # pass
$db4="omegawiki";  # db-name

@$connection=MySQL_connect($db1,$db2,$db3);
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

echo "<h1>Number of expressions per language</h1>";

echo "<center><table cellpadding=0><tr><td><b>Language</b></td><td><b>Entries</b></td><td></td></tr>";
$width=600;
$max=0;
while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
if($max<$row[1])$max=$row[1];
$wi=ceil((($row[1]/$max)*$width));
$per=ceil((($row[1]/$max)*100));
echo "<tr><td >".$lang[$row[0]]."</td><td>".$row[1]."</td><td><img src=sc1.png width=\"$wi\" height=20> $per %</td></tr>";
//$ar[$row[0]].=$row[1]."	".$row[2]."\n";
//filewrite("out/".$row[0].".txt",$row[1]."	".$row[2]);
}
echo "</table><center>";

/*
for($i=0;$i<250;$i++){
if(strlen($ar[$i])>20)filewrite("out/".$lang[$i].".txt",$ar[$i]);

}
*/
////////////////////////////////////////////////////////


//echo "<pre>".$ar[85]."</pre>";

echo substr((stopwatch()-$start),0,4)." seconds";


?>
