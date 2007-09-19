<?php

# (C) 2007  Alan Smithee  (licensed under the GPL v. 2, GPL v. 3 or any later version, though you're not likely to care)
# throwaway rapid prototype to copy defined meanings between tables.
# I didn't write this, nobody saw me, you can't prove a thing!
# Actually somewhat easier than fighting through multiple layers of
# code in the recordsets for now.
# probably will refactor this code into ulta-pretty helpers or
# other recordset improvements.
#
# Addendum: this might not actually be so throwaway as was hoped.
# Don't you love it when that happens?


# common abbreviations used in varnames and comments:
# dm = defined meaning. 
# dmid = defined meaning id: unique identifier for each dm.
# dc = dataset context. datasets are implemented by having
#			tables with different prefixes
# dc1 = dataset (context) 1 (we are copying FROM dc1 (so we READ) )
# dc2 = dataset (context) 2 (we are copying TO dc2 (so we WRITE) ) 
# 
# naming conventions:
# Normal: Java Style  
#	* ClassName->methodName($variableName); /* comment */
#	* CopyTools::getRow(...); # comment
# Wrappers around PHP functions or extensions to PHP function set: Same style as the wrapped function
#	* mysql_insert_assoc(...); # comment

header("Content-type: text/html; charset=UTF-8");

define('MEDIAWIKI', true );
require_once("../../../StartProfiler.php");
include_once("../../../includes/Defines.php");
include_once("../../../LocalSettings.php");
require_once("Setup.php");
require_once("../OmegaWiki/WikiDataAPI.php");
require_once("../OmegaWiki/Transaction.php");


global $wgDBserver, $wgDBuser, $wgDBpassword, $wgDBname;

$db1=$wgDBserver;  # hostname
$db2=$wgDBuser;  # user
$db3=$wgDBpassword;  # pass
$db4=$wgDBname;  # db-name

$connection=MySQL_connect($db1,$db2,$db3);
if (!$connection)die("Cannot connect to SQL server. Try again later.");
MySQL_select_db($db4)or die("Cannot open database");
mysql_query("SET NAMES 'utf8'");


/** copies items in the objects table.
 * As a "side-effect" 
 * also conveniently reports  to see if something was already_there
 * (we don't want to accidentally duplicate things umpteen times, so the
 * side-effect is almost as important)
 */
class ObjectCopier {
	
	protected $id;
	protected $dc1;
	protected $dc2;
	protected $object;
	protected $already_there=null;

	function __construct($id, $dc1, $dc2) {
		$this->id=$id;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}

	function getObject() {
		return $this->object;
	}

	function setObject($object) {
		$this->object=$object;
	}

	/** return true if the object was already present in the other dataset*/
	public function already_there(){
		return $this->already_there;
	}

	protected function read() {
		$dc1=$this->dc1;
		$id=$this->id;
		$this->object=CopyTools::getRow($dc1, "objects", "WHERE object_id=$id");
	}

	/* tries to retrieve the identical UUID from the destination
	 * (dc2) dataset, if it exists.
	 * @returns the associative array representing this object,
	 *  if successful. Else returns an empty array.
	 */
	protected function identical() {
		var_dump($this->object);
		$uuid=mysql_escape_string($this->object["UUID"]);
		$dc2=$this->dc2;
		return CopyTools::getRow($dc2, "objects", "WHERE `UUID`='$uuid'");
	}

	/** Write copy of object into the objects table,taking into account
	 * necessary changes.
	 * possible TODO: Currently induces the target table from the original
	 * destination table name.
	 * Perhaps would be wiser to get the target table as an (override) parameter.
	 */
	function write() {
		$dc2 = $this->dc2;
		$object = $this->object;
		unset($object["object_id"]);

		$tableName_exploded = explode("_", $object["table"]);
		$tableName_exploded[0] = $dc2;
		$tableName = implode("_", $tableName_exploded);
		$object["table"]=$tableName;

		CopyTools::dc_insert_assoc($dc2,"objects",$object);
		return mysql_insert_id();
	}

	function dup() {
		$this->read();
		$object2=$this->identical();
		if (CopyTools::sane_key_exists("object_id",$object2)) {
			$this->already_there=true;
			$newid=$object2["object_id"];
		} else {
			$this->already_there=false;
			$newid=$this->write();
		}
		return $newid;
	}
}


/** obtain an expression definition from the database
 * @param $expression_id	the id of the expression
 * @param $dc1			dataset to READ expression FROM
 */
function expression($expression_id, $dc1) {
	return CopyTools::getRow($dc1, "expression", "WHERE expression_id=$expression_id");
}


function getOldSyntrans($dc1, $dmid, $expid) {
	return CopyTools::getRow($dc1, "syntrans", "where defined_meaning_id=$dmid and expression_id=$expid");
}

function writeSyntrans($syntrans, $newdmid, $newexpid, $dc2) {
	$syntrans["defined_meaning_id"]=$newdmid;
	$syntrans["expression_id"]=$newexpid;
	CopyTools::dc_insert_assoc($dc2,"syntrans",$syntrans);
}	

function dupSyntrans($dc1, $dc2, $olddmid, $oldexpid, $newdmid, $newexpid) {
	$syntrans=getOldSyntrans($dc1, $olddmid, $oldexpid);
	$copier=new ObjectCopier($syntrans["syntrans_sid"], $dc1, $dc2);
	$newid=$copier->dup();
	if ($copier->already_there()) {
		return;
	}
	$syntrans["syntrans_sid"]=$newid;
	writeSyntrans($syntrans, $newdmid, $newexpid, $dc2);
}

function get_syntranses($dmid, $dc1) {
	return CopyTools::getRows($dc1, "syntrans", "where defined_meaning_id=$dmid");
}


/* some coy&paste happening here, might want to tidy even before we
* toss this throwaway code*/
function write_expression($expression, $src_dmid, $dst_dmid, $dc1, $dc2) {

	$copier=new ObjectCopier($expression["expression_id"], $dc1, $dc2);
	$target_expid1=$copier->dup();
	$save_expression=$expression;
	$save_expression["expression_id"]=$target_expid1;
	if  (!($copier->already_there())) {
		CopyTools::dc_insert_assoc($dc,"expression",$save_expression);
	}
	dupsyntrans(
		$dc1,
		$dc2,
		$src_dmid,
		$expression["expression_id"],
		$dst_dmid,
		$save_expression["expression_id"]
	);

}

function write_syntranses($syntranses, $src_dmid, $dst_dmid, $dc1, $dc2) {
	var_dump($syntranses);
	print "<br>\nExpressions:"; 
	foreach ($syntranses as $syntrans) {
		$expression=expression($syntrans["expression_id"],$dc1);
		print $expression["spelling"].";";
		write_expression($expression, $src_dmid, $dst_dmid, $dc1, $dc2);
		# ^- which incidentally also dups the syntrans
	}
}

function dup_syntranses($src_dmid, $dst_dmid, $dc1, $dc2) {
	$syntranses=get_syntranses($src_dmid, $dc1);
	write_syntranses($syntranses, $src_dmid, $dst_dmid, $dc1, $dc2);
}

function read_translated_content($dc1,$tcid) {
	return CopyTools::getRows($dc1,"translated_content","where translated_content_id=$tcid");
}

function write_translated_content($dc1, $dc2, $tcid, $content) { 
	$content["translated_content_id"]=$tcid;
	$content["text_id"]=dup_text($dc1, $dc2, $content["text_id"]);
	var_dump($content);
	CopyTools::dc_insert_assoc($dc2, "translated_content", $content);
}


function dup_translated_content($dc1, $dc2, $tcid) {
	$translated_content=read_translated_content($dc1, $tcid);
	$copier=new ObjectCopier($tcid, $dc1, $dc2);
	$new_tcid=$copier->dup();
	# note the issue where translated content is added later:
	# since all translated content for a single dm 
	# shares one UUID, we can't check for that eventuality.
	if ($copier->already_there()) {
		return;
	}
	foreach ($translated_content as $item) {
		write_translated_content($dc1, $dc2, $new_tcid, $item);
	}
	return $new_tcid;
}

function read_text($dc1,$text_id) {
	return CopyTools::getRow($dc1,"text","where text_id=$text_id");
}

function write_text($dc2,$text) {
	unset($text["text_id"]);
	# inconsistent, insert_assoc should accept dc, table
	$target_table=mysql_real_escape_string("${dc2}_text");
	CopyTools::dc_insert_assoc($dc2, "text", $text);
	return mysql_insert_id();
}

function dup_text($dc1, $dc2, $text_id) {
	$text=read_text($dc1, $text_id);
	$id=write_text($dc2, $text);
	return $id;
}

class RelationsCopier {

	protected $old_dmid;
	protected $new_dmid;
	protected $dc1;
	protected $dc2;

	function __construct($dc1, $dc2, $old_dmid, $new_dmid) {
		$this->old_dmid=$old_dmid;
		$this->new_dmid=$new_dmid;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}

	function read() {
		$dc1=$this->dc1;
		$dmid=$this->old_dmid;
		return CopyTools::getRows($dc1,"meaning_relations","where meaning1_mid=$dmid");
	}

	function write_single($relation) {
		var_dump($relation);
		$dc1=$this->dc1;
		$dc2=$this->dc2;
		$new_dmid=$this->new_dmid;

		$copier=new ObjectCopier($relation["relation_id"], $dc1, $dc2);
		$relation["relation_id"]=$copier->dup();
		if ($copier->already_there()) {
			return;
		}
		$relation["meaning1_mid"]=$new_dmid;
		$dmcopier=new DefinedMeaningCopier($relation["meaning2_mid"],$dc1, $dc2);
		$relation["meaning2_mid"]=$dmcopier->dup_stub();
		# Typically checks same values each time. Accelerated by query_cache:
		$rtcopier=new DefinedMeaningCopier($relation["relationtype_mid"],$dc1, $dc2);
		$relation["relationtype_mid"]=$rtcopier->dup_stub();
		var_dump($relation);
		$copier=new ObjectCopier($relation["relation_id"], $dc1, $dc2);
		$relation["relation_id"]=$copier->dup();
		if ($copier->already_there()) {
			return;
		}
		CopyTools::dc_insert_assoc($dc2,"meaning_relations",$relation);

	}

	function dup() {
		$rows=$this->read();
		echo "copying relations";
		foreach ($rows as $row) {
			$this->write_single($row);
		}
	}			
}

class CollectionCopier {
	protected $dmid;
	protected $save_dmid;
	protected $dc1;
	protected $dc2;
	protected $already_there=false;

	public function already_there() {
		return $this->already_there;
	}
	
	public function __construct ($dc1, $dc2, $dmid, $save_dmid) {
		$this->dmid=$dmid;
		$this->save_dmid=$save_dmid;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}

	public function read($dc=Null){
		if (is_null($dc)) {
			$dc=$this->dc1;
		}
		$dmid=$this->dmid;
		return CopyTools::getRows($dc, "collection_contents", "WHERE member_mid=$dmid");
	}


	public function read_definition($collection_id) {
		$dc1=$this->dc1;
		return CopyTools::getRow($dc1,"collection","WHERE collection_id=$collection_id");
	}

	/** write collection definition (and associated dm) to dc2
	 * if it doesn't already exist.
	 * If it already exists, will only look up the id.
	 * returns the  id for dc2 either way.
	 */
	public function write_definition($definition){
		$dc1=$this->dc1;
		$dc2=$this->dc2;

		print "<br>\nCopying collection</br>";
		var_dump($definition);
		print $definition["collection_id"];
		$objcopier=new ObjectCopier($definition["collection_id"], $dc1, $dc2);
		$definition["collection_id"]=$objcopier->dup();
		if (!$objcopier->already_there()) {
			$dmid= $definition["collection_mid"];
			$dmcopier=new DefinedMeaningCopier($dmid,$dc1,$dc2);
			$definition["collection_mid"]=$dmcopier->dup_stub();

			CopyTools::dc_insert_assoc($dc2, "collection", $definition);

		}
		return $definition["collection_id"];

	}
	
	/** look up the collection definition in %_collection, 
	 * and copy if doesn't already exist in dc2 
	 */
	public function dup_definition($collection_id) {
		$definition=$this->read_definition($collection_id);
		return $this->write_definition($definition);
	}


	# we create a mapping and THEN do collections, now we need to prevent ourselves dupping 
	# existing mappings
	public function existing_mapping($member_id) {
		$dc2=$this->dc2;
		$query="SELECT ${dc2}_collection_contents.* FROM ${dc2}_collection_contents, ${dc2}_collection
			WHERE ${dc2}_collection_contents.collection_id = ${dc2}_collection.collection_id
			AND collection_type=\"MAPP\" 
			AND internal_member_id=\"${member_id}\"";
		$mapping_here=CopyTools::doQuery($query);

		if ($mapping_here==false)
			return false;
		else
			return true; # if anything is actually returned, we know the score.
	}


	/** write a single collection_contents row,
	 * (if the collection doesn't exist yet), also dup the definition
	 */
	public function write_single($row){
		$dc2=$this->dc2;
		$save_dmid=$this->save_dmid;
		$row["collection_id"]=$this->dup_definition($row["collection_id"]);
		
		if ( $this->existing_mapping($row["internal_member_id"]) )
			return;

		$row["member_mid"]=$save_dmid;
		CopyTools::dc_insert_assoc($dc2, "collection_contents", $row);
	}

	public function write($rows){
		foreach ($rows as $row) {
			$this->write_single($row);
		}
	}

	/** writes a duplicate. does *NOT* return ids on return, as there
	 * are multiple ids 
	 */
	public function dup() {
		# Is there something already there? If so, do not dup.
		$checkrows=$this->read($this->dc2);
		foreach ($checkrows as $row) {
			if ($row["member_mid"]==$this->save_dmid){
				$this->already_there=true;
				return;
			}
		}

		#seems ok, let's dup.
		$rows=$this->read($this->dc1);
		$this->write($rows);
	}
}
	

class DefinedMeaningCopier {

	protected $defined_meaning;
	protected $save_meaning;
	protected $dmid;
	protected $dc1;
	protected $dc2;
	protected $already_there=false;
	
	public function __construct ($dmid, $dc1, $dc2) {
		$this->dmid=$dmid;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}
	
	protected function read() {
		$dmid=$this->dmid;
		print "<".$dmid."-".$this->dc1.">";
		$this->defined_meaning=CopyTools::getRow($this->dc1,"defined_meaning","where defined_meaning_id=$dmid");
		return $this->defined_meaning; # for convenience
	}


	public function getDM() {
		$dm=$this->defined_meaning;
		if (is_null($dm)) {
			$dm=$this->read();
		}
		return $this->defined_meaning;
	}

	public function already_there() {
		return $this->already_there;
	}

	public	function dup() {
		$this->dup_stub();
		$this->dup_rest();
		return $this->save_meaning["defined_meaning_id"];
	}

	private function dup_stub (){
		$dmid=$this->dmid;
		$dc1=$this->dc1;
		$dc2=$this->dc2;

		echo "<br><h3>copying dm $dmid</h3><br>\n";
		$this->read();

		# bit of exp here too (defnitely need to tidy)
		$defining_expression=expression($this->defined_meaning["expression_id"], $dc1);
		$dm_target_table=mysql_real_escape_string("${dc2}_defined_meaning");
		$copier=new ObjectCopier($this->defined_meaning["defined_meaning_id"], $dc1, $dc2);
		$target_dmid=$copier->dup();
		var_dump($target_dmid);
		$this->save_meaning=$this->defined_meaning;
		$this->save_meaning["defined_meaning_id"]=$target_dmid;

		$this->already_there=$copier->already_there();
		if (!($copier->already_there())) {
			# exp
			$target_table=mysql_real_escape_string("${dc2}_expression");
			$exp_copier=new ObjectCopier($defining_expression["expression_id"], $dc1, $dc2);
			$target_expid1=$exp_copier->dup();
			var_dump($target_expid1);
			$save_expression=$defining_expression;
			$save_expression["expression_id"]=$target_expid1;
			CopyTools::dc_insert_assoc($dc2, "expression", $save_expression);
			# and insert that info into the dm
			$this->save_meaning["expression_id"]=$target_expid1;
		}
		$this->save_meaning["meaning_text_tcid"]=dup_translated_content($dc1, $dc2, $this->defined_meaning["meaning_text_tcid"]);

		if (!($copier->already_there())) {
			CopyTools::dc_insert_assoc($dc2, "defined_meaning", $this->save_meaning);

			$title_name=$defining_expression["spelling"];
			$title_number=$target_dmid;
			$title=str_replace(" ","_",$title_name)."_(".$title_number.")";
			CopyTools::createPage($title);
		
			$concepts=array(
				$dc1 => $this->defined_meaning["defined_meaning_id"],
				$dc2 => $this->save_meaning["defined_meaning_id"]);
			createConceptMapping($concepts);
		}

		return $this->save_meaning["defined_meaning_id"];
	}		
			
	function dup_rest() {
		$dmid=$this->dmid;
		$dc1=$this->dc1;
		$dc2=$this->dc2;
		dup_syntranses(
			$this->defined_meaning["defined_meaning_id"],
			$this->save_meaning["defined_meaning_id"],
			$dc1,
			$dc2
		);
		
		$relationsCopier=new RelationsCopier(
			$dc1, 
			$dc2, 
			$this->defined_meaning["defined_meaning_id"],
			$this->save_meaning["defined_meaning_id"]);
		$relationsCopier->dup();
		
		# can't merge collections, since they're not entirely covered by
		# the objects table. So we don't copy them more than once.
		if (!$this->already_there()) {
			$collectionCopier=new CollectionCopier(
				$dc1, 
				$dc2, 
				$this->defined_meaning["defined_meaning_id"],
				$this->save_meaning["defined_meaning_id"]);
			$collectionCopier->dup();
		}

	}
}
	
class CopyTools {
	/** create a relevant entry in the `page` table. */
	public static function createPage($title) {
		# page is not a Wikidata table, so it needs to be treated differently (yet again :-/)
		$escTitle=mysql_real_escape_string($title);
		$existing_page_data=CopyTools::doQuery("SELECT * FROM page WHERE page_namespace=24 AND page_title=\"$escTitle\"");
		print "<br>PAGE COUNT: ".count($existing_page_data)."<br>\n";
		if ($existing_page_data==false) {
			$pagedata=array("page_namespace"=>24, "page_title"=>$title);
			CopyTools::mysql_insert_assoc("page",$pagedata);
		}
	}

	/** Times our execution time, nifty! */
	public static function stopwatch(){
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	}

	/** start a new copy transaction
	 */
	public static function newCopyTransaction($dc1, $dc2) {
		startNewTransaction(0, "127.0.0.1", "copying from $dc1 to $dc2", $dc2);
	}

	/** retrieve a single row from the database as an associative array
	 * @param $dc		the dataset prefix we need
	 * @param $table	the name of the table (minus dataset prefix)
	 * @peram $where		the actual WHERE clause we need to uniquely find our row
	 * @returns an associative array, representing our row. \
	 *	keys=column headers, values = row contents
	 */
	public static function getRow($dc, $table, $where) {
		$target_table=mysql_real_escape_string("${dc}_${table}");
		$query="SELECT * FROM $target_table ".$where;
		print $query."<br>\n";
		return CopyTools::doQuery($query);
	}


	/** retrieve multiple rows from the database, as an array of associative arrays.
	 * @param $dc		the dataset prefix we need
	 * @param $table	the name of the table (minus dataset prefix)
	 * @peram $where		the actual WHERE clause we need to uniquely find our row
	 * @returns an array of associative arrays, representing our rows.  \
	 *	each associative array is structured with:		\
	 *	keys=column headers, values = row contents
	 */
	public static function getRows($dc, $table, $where) {
		$target_table=mysql_real_escape_string("${dc}_${table}");
		$query="SELECT * FROM $target_table ".$where;
		return CopyTools::doMultirowQuery($query);
	}


	/** Performs an arbitrary SQL query and returns an associative array
	 * Assumes that only 1 row can be returned!
	 * @param $query	a valid SQL query
	 * @returns an associative array, representing our row. \
	 *	keys=column headers, values = row contents
	 *
	 */
	public static function doQuery($query) {
		echo $query;
		$result = mysql_query($query)or die ("error ".mysql_error());
		$data= mysql_fetch_assoc($result);
		return $data;
	}
	/** Perform an arbitrary SQL query
	 * 
	 * @param $query	a valid SQL query
	 * @returns an array of associative arrays, representing our rows.  \
	 *	each associative array is structured with:		\
	 *	keys=column headers, values = row contents
	 */

	public static function doMultirowQuery($query) {
		$result = mysql_query($query)or die ("error ".mysql_error());
		$items=array();
		while ($nextexp=mysql_fetch_assoc($result)) {
			$items[]=$nextexp;
		}
		return $items;
	}

	/** identical to the php function array_key_exists(), but eats dirtier input
	 * returns false (rather than an error) on somewhat invalid input
	 */
	public static function sane_key_exists($key, $array) {
		if (is_null($key) or $key==false){
			return false;
		}
		if (is_null($array) or $array==false) {
			return false;
		}
		var_dump($array);
		return array_key_exists($key, $array);
	}

	/**
	 * inverse of mysql_fetch_assoc
	 * takes an associative array as parameter, and inserts data
	 * into table as a single row (keys=column names, values = data to be inserted)
	/* see: http://www.php.net/mysql_fetch_assoc (Comment by R. Bradly, 14-Sep-2006)
	 */
	public static function mysql_insert_assoc ($my_table, $my_array) {

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

	/**convenience wrapper around mysql_insert_assoc
	 * like mysql_insert_assoc, but allows you to specify dc prefix+table name separately
	 * Also transparently handles the internal transaction (WHICH MUST ALREADY BE OPEN!)
	 */
	public static function dc_insert_assoc($dc, $table_name, $array) {
		$target_table=mysql_real_escape_string("${dc}_${table_name}");
		if (CopyTools::sane_key_exists("add_transaction_id", $array)) {
			$array["add_transaction_id"]=getUpdateTransactionId();
		}
		return CopyTools::mysql_insert_assoc($target_table, $array);
	}


}


$start=CopyTools::stopwatch();

$dmid_dirty=$_REQUEST['dmid'];
$dc1_dirty=$_REQUEST['dc1'];
$dc2_dirty=$_REQUEST['dc2'];

$dmid=mysql_real_escape_string($dmid_dirty);
$dc1=mysql_real_escape_string($dc1_dirty);
$dc2=mysql_real_escape_string($dc2_dirty);

CopyTools::newCopyTransaction($dc1, $dc2);
$dmc=new DefinedMeaningCopier($dmid, $dc1, $dc2); #sorry, not a [[delorean]]
$dmc->dup(); 

echo"
<hr>
<div align=\"right\">
<small>Page time: ".substr((CopyTools::stopwatch()-$start),0,5)." seconds</small>
</div>
";

?>
