<?php
header("Content-type: text/html; charset=UTF-8");

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


function stopwatch(){
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function expression($expression_id, $dc1) {
	$expression=mysql_real_escape_string("${dc1}_expression_ns");
	$query=
	"SELECT  *
	FROM $expression
	WHERE expression_id=$expression_id";
	echo $query;

	$result = mysql_query($query)or die ("error ".mysql_error());

	$dmdata= mysql_fetch_assoc($result);
	return $dmdata;
}

/**@deprecated , use dupobject*/
function new_dm_id($dc2) {
	$defined_meaning=mysql_real_escape_string("${dc2}_defined_meaning");
	$query="SELECT max(defined_meaning_id) as maxdm from $defined_meaning";
	echo "$query\n";
	$result = mysql_query($query)or die ("error ".mysql_error());
	echo "bla\n";
	$data= mysql_fetch_assoc($result);
	var_dump($data);
	return $data["maxdm"]+1;
}

/**@deprecated , use dupobject*/
function new_exp_id($dc2) {
	$expression_ns=mysql_real_escape_string("${dc2}_expression_ns");
	$query="SELECT max(expression_id) as maxexp from $expression_ns";
	$result = mysql_query($query)or die ("error ".mysql_error());
	$data= mysql_fetch_assoc($result);
	var_dump($data);
	return $data["maxexp"]+1;
}

function readobject($id, $dc1) {
	$objects=mysql_real_escape_string("${dc1}_objects");
	$query="SELECT * from $objects where object_id=$id";
	$result = mysql_query($query)or die ("error ".mysql_error());
	$data= mysql_fetch_assoc($result);
	var_dump($data);
	return $data;
}

function writeobject($object,$dc2,$table) {
	$objects=mysql_real_escape_string("${dc2}_objects");
	unset($object["object_id"]);
	$object["table"]=$table;
	mysql_insert_assoc($objects,$object);
	return mysql_insert_id();
}

function dupobject($id, $table, $dc1, $dc2) {
	$object=readobject($id, $dc1);
	$newid=writeobject($object,$dc2, $table);
	return $newid;
}


/**
 * inverse of mysql_fetch_assoc
/* see: http://www.php.net/mysql_fetch_assoc (Comment by R. Bradly, 14-Sep-2006)
 */
   function mysql_insert_assoc ($my_table, $my_array) {

	// Find all the keys (column names) from the array $my_array

	// We compose the query
	$sql = "insert into `$my_table` set";
	// implode the column names, inserting "\", \"" between each (but not after the last one)
	// we add the enclosing quotes at the same time
	$sql_comma=$sql;
	foreach($my_array as $key=>$value) {
		$sql=$sql_comma;
		if (is_null($value)) {
			$value="DEFAULT";
		} else {
			$value="\"$value\"";
		}
		$sql.=" `$key`=$value";
		$sql_comma=$sql.",";
	}
	// Same with the values
	echo $sql."; <br>\n";
	$result = mysql_query($sql);

	if ($result)
	{
		echo "The row was added sucessfully";
		return true;
	}
	else
	{
		echo ("The row was not added<br>The error was" . mysql_error());
		return false;
	}
   }

function getOldSyntrans($dc1, $dmid, $expid) {
	$syntrans_table=mysql_real_escape_string("${dc1}_syntrans");
	$query="SELECT * from $syntrans_table where defined_meaning_id=$dmid and expression_id=$expid";
	$result = mysql_query($query)or die ("error ".mysql_error());
	$data= mysql_fetch_assoc($result);
	var_dump($data);
	return $data;
}

function writeSyntrans($syntrans, $newdmid, $newexpid, $dc2) {
	$syntrans["defined_meaning_id"]=$newdmid;
	$syntrans["expression_id"]=$newexpid;
	$syntrans_table=mysql_real_escape_string("${dc2}_syntrans");
	mysql_insert_assoc($syntrans_table,$syntrans);
}	

function dupSyntrans($dc1, $dc2, $olddmid, $oldexpid, $newdmid, $newexpid) {
	$syntrans=getOldSyntrans($dc1, $olddmid, $oldexpid);
	$table=mysql_real_escape_string("${dc2}_syntrans");
	$newid=dupObject($syntrans["syntrans_sid"], $table, $dc1, $dc2);
	$syntrans["syntrans_sid"]=$newid;
	writeSyntrans($syntrans, $newdmid, $newexpid, $dc2);
}


$start=stopwatch();

$dmid=$_REQUEST['dmid'];
$dc1=$_REQUEST['dc1'];
$dc2=$_REQUEST['dc2'];
$dmid_esc=mysql_real_escape_string($dmid);

echo $dmid_esc;
$defined_meaning=mysql_real_escape_string("${dc1}_defined_meaning");

$query=
"SELECT  *
FROM $defined_meaning
WHERE defined_meaning_id=$dmid_esc";
echo $query;

$result = mysql_query($query)or die ("error ".mysql_error());
$defined_meaning=mysql_fetch_assoc($result);
var_dump($defined_meaning);

$defining_expression=expression($defined_meaning["expression_id"], $dc1);
var_dump($defining_expression);

$dm_target_table=mysql_real_escape_string("${dc2}_defined_meaning");
$target_dmid=dupobject($defined_meaning["defined_meaning_id"], $dm_target_table, $dc1, $dc2);
var_dump($target_dmid);
$save_meaning=$defined_meaning;
$save_meaning["defined_meaning_id"]=$target_dmid;

$target_table=mysql_real_escape_string("${dc2}_expression_ns");
$target_expid1=dupobject($defining_expression["expression_id"], $target_table, $dc1, $dc2);
var_dump($target_expid1);
$save_expression=$defining_expression;
$save_expression["expression_id"]=$target_expid1;
mysql_insert_assoc($target_table,$save_expression);

$save_meaning["expression_id"]=$target_expid1;
mysql_insert_assoc($dm_target_table, $save_meaning);

dupsyntrans(
	$dc1,
	$dc2,
	$defined_meaning["defined_meaning_id"],
	$defining_expression["expression_id"],
	$save_meaning["defined_meaning_id"],
	$save_expression["expression_id"]
);

$title_name=$defining_expression["spelling"];
$title_number=$target_dmid;
$title=str_replace(" ","_",$title_name)."_(".$title_number.")";
$pagedata=array("page_namespace"=>24, "page_title"=>$title);
mysql_insert_assoc("page",$pagedata);



echo"
<hr>
<div align=\"right\">
<small>Page time: ".substr((stopwatch()-$start),0,5)." seconds</small>
</div>
";

?>
