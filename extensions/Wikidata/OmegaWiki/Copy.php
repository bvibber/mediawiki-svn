<?php

# (C) 2007  Alan Smithee  (licensed under the GPL v. 2, GPL v. 3 or any later version, though you're not likely to care)
# Copy library to copy defined meanings between tables.
# Based on the util/copy.php throwaway. 
#
# Not the greatest code ever written, but will have to live with it for now
#
# common abbreviations used in varnames and comments:
# dm = defined meaning. 
# dmid = defined meaning id: unique identifier for each dm.
# dc = dataset context. datasets are implemented by having
#			tables with different prefixes
# dc1 = dataset (context) 1 (we are copying FROM dc1 (so we READ) )
# dc2 = dataset (context) 2 (we are copying TO dc2 (so we WRITE) ) 
# 
# naming conventions (may deviate slightly from current conventions document):
# Normal: Java Style  
#	* ClassName->methodName($variableName); /* comment */
#	* CopyTools::getRow(...); # comment
# Wrappers around PHP functions or extensions to PHP function set: Same style as the wrapped function
#	* mysql_insert_assoc(...); # comment
# Variables that coincide with database columns: Same style as column
#	* $object_id
#	* $defined_meaning_id
#	$ $attribute_mid
#
# TODO:
# * Change to library
# * some read/write/dup functions are still main namespace, should get their own
# * classes (!!)

# How to use:
#
# Step 1:
# Set up one of those fancy wikidata transactions on dc2
# Don't have one? CopyTools::newCopyTransaction($dc1, $dc2) is your friend
#
# Step 2:  
# copy
# $copier=new DefinedMeaningCopier($dmid, $dc1, $dc2);
# $copier->dup()
# 
# et voila!
#
# Optional 1:
# dup()ing  something will return the new id.
# so ie you can get the dmid (defined meaning id) in dc2 (the
# destination dataset) with:
# $newDmid=$copier->dup();
# 
# note that if something is already copied, no new
# copy is made, but dup() will still return the appropriate
# new id.
# 
# This behaviour is only true for singular items (like defined meanings).
# O classes that duplicate entire sets of items at once, the dup method
# currently returns nothing. Logically it should really return an array,
# but I haven't really found anything that needs that yet, so haven't
# made an effort.
#
# Optional 2:
# If you attempt to dup something that was already there, nothing will
# happen, the item will not be dupped and the already_there flag will be set.
#  Querying $something->already_there() will return true in that case. 
#
# TWarning on testing:
# so far I've only tested this thoroughly where DefinedMeaningCopier is the entry point.
# In all other cases: YMMV, HTH, HAND. 
# (we so need unit testing ^^;;)

#require_once("../../../StartProfiler.php");
#include_once("../../../includes/Defines.php");
#include_once("../../../LocalSettings.php");
#require_once("Setup.php");
require_once("WikiDataAPI.php");
require_once("Transaction.php");

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
	protected $autovivify=false; # tradeoff: create object references if not found,
					# but catch less errors
	protected $tableName;

	function setTableName($tableName) {
		$this->tableName=$tableName;
	}

	/** 
	 * if can't find object in src (dc1) dataset,
	 * if set:  create said object now.
	 * if unset: throw exception.
	 * default: unset, because typically not finding
	 * the object we're looking for means something is
	 * very wrong. Some tables (like uw_collection_conte
	 */
	function setAutovivify($bool) {
		$this->autovivify=$bool;
	}

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
		if (is_null($dc1))
			throw new Exception("ObjectCopier: provided source dataset(dc1) is null");
		if (is_null($id))
			throw new Exception("ObjectCopier: provided identifier is null");

		$this->object=CopyTools::getRow($dc1, "objects", "WHERE object_id=$id");
	}

	/* tries to retrieve the identical UUID from the destination
	 * (dc2) dataset, if it exists.
	 * @returns the associative array representing this object,
	 *  if successful. Else returns an empty array.
	 */
	protected function identical() {
		$uuid=mysql_escape_string($this->object["UUID"]);
		if (is_null($uuid))
			throw new Exception("ObjectCopier: UUID is null");
		$dc2=$this->dc2;
		return CopyTools::getRow($dc2, "objects", "WHERE `UUID`='$uuid'");
	}

	/** Write copy of object into the objects table,taking into account
	 * necessary changes.
	 * possible TODO: Currently induces the target table from the original
	 * destination table name.
	 * Perhaps would be wiser to get the target table as an (override) parameter.
	 */
	function write($dc=Null) {

		if (is_null($dc)) {
			$dc = $this->dc2;
		}
		
		$object = $this->object;
		unset($object["object_id"]);

		$tableName_exploded = explode("_", $object["table"]);
		$tableName_exploded[0] = $dc;
		$tableName = implode("_", $tableName_exploded);
		$object["table"]=$tableName;

		CopyTools::dc_insert_assoc($dc,"objects",$object);
		return mysql_insert_id();
	}

	/** create a new objects table entry . 
	 * See also database schema documentation (insofar available) or
	 * do sql statement DESC <dc>_objects for table format (where <dc> is a valid
	 * dataset prefix) */
	function create_key($uuid=null) {
		echo "crumb B1<br>\n";
		if (is_null($this->tableName)) {
			throw new Exception("ObjectCopier: Object autovivification requires a table name to assist in creating an object. No table name was provided.");
		}
		$this->object["object_id"]=null; # just to be on some kind of safe side.
		$this->object["table"]="unset_".$this->tableName; # slightly hackish, this
		$this->object["original_id"]=0;	# no idea what this is for.

		if (is_null($uuid)) {
			$uuid_query=CopyTools::doQuery("SELECT UUID()");
			$uuid=$uuid_query["UUID()"];
		}
		$this->object["UUID"]=$uuid;
		$this->id=$this->write($this->dc1);
		return $this->id;
		
	}
	
	/** 
	 * create a valid object key in the objects table, and return it
	 * @param $dc	the dataset (prefix) to create the object in
	 * @param $table	which table is the object originally from? (minus dataset prefix)
	 * @param $uuid  (optional) : override the auto-generated uuid with this one.
	 */
	public static function makeObjectId($dc, $table, $uuid=null) {
		# Sorta Exploiting internals, because -hey- we're internal
		# don't try this at home kids. 
		# probably this would be tidier if the non-static method called
		# the static one, or something. We only really need
		# create_key(), so only filling in the data that that method needs.
		$objectCopier=new ObjectCopier(null, $dc, null);
		$objectCopier->setTableName($table);
		return $objectCopier->create_key($uuid);
	}

	function dup() {
		if (is_null($this->id)) {
			var_dump($this->autovivify);
			if ($this->autovivify) {
				$this->create_key();
			} else {
				throw new Exception("ObjectCopier: provided id is null");
			}
		}
	
		$this->read();
		if (!CopyTools::sane_key_exists("object_id", $this->object)) {
			if ($this->autovivify) {
				$this->create_key();
			} else {
				echo "crumb d2B\n";
				$id=$this->id;
				$table=$this->object["table"];
				throw new Exception("ObjectCopier: Could not find object information for object with id '$id' stored in `$table` in the objects table with prefix '$dc1'");
			}
		}

		$object2=$this->identical();
		if (CopyTools::sane_key_exists("object_id",$object2)) {
			echo "ALREADY THERE? o1, o2<br>\n";
			var_dump($this->object);
			var_dump($object2);
			$this->already_there=true;
			$newid=$object2["object_id"];
		} else {
			$this->already_there=false;
			$newid=$this->write();
		}
		AttributeCopier::copy($this->dc1, $this->dc2, $this->object["object_id"], $newid);
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
		CopyTools::dc_insert_assoc($dc2,"expression",$save_expression);
	}
	dupSyntrans(
		$dc1,
		$dc2,
		$src_dmid,
		$expression["expression_id"],
		$dst_dmid,
		$save_expression["expression_id"]
	);
	return $target_expid1;

}

function write_syntranses($syntranses, $src_dmid, $dst_dmid, $dc1, $dc2) {
	foreach ($syntranses as $syntrans) {
		$expression=expression($syntrans["expression_id"],$dc1);
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
		return $new_tcid;
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

class RelationsCopier extends Copier {

	protected $old_dmid;
	protected $new_dmid;
	protected $dc1;
	protected $dc2;
	protected $tableName="meaning_relations";

	function __construct($dc1, $dc2, $old_dmid, $new_dmid) {
		$this->old_dmid=$old_dmid;
		$this->new_dmid=$new_dmid;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}

	function read() {
		$dc1=$this->dc1;
		$dmid=$this->old_dmid;
		return CopyTools::getRows($dc1,$this->tableName,"where meaning1_mid=$dmid");
	}

	function write_single($relation) {
		$new_dmid=$this->new_dmid;

		if ($this->doObject($relation, "relation_id")) 
			return $relation["relation_id"];

		$relation["meaning1_mid"]=$new_dmid;
		$this->doDM($relation,"meaning2_mid");
		$this->doDM($relation,"relationtype_mid");

		$this->doInsert($relation);
		return $relation["relation_id"];
	}

	function dup() {
		$rows=$this->read();
		foreach ($rows as $row) {
			$this->write_single($row);
		}
	}			
}

/** copies collections 
 * TODO:
 * possibly *_definition should actually be in a different class.
 */
class CollectionCopier extends Copier {
	protected $dmid;
	protected $save_dmid;
	protected $dc1;
	protected $dc2;
	protected $already_there=false;
	protected $autovivifyObjects=true;
	protected $tableName="collection_contents";

	public function already_there() {
		return $this->already_there;
	}
	
	public function __construct ($dc1, $dc2, $dmid, $save_dmid) {
		echo "crumb A1<br>\n";
		$this->dmid=$dmid;
		$this->save_dmid=$save_dmid;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}

	public function read($dc=Null){
		echo "crumb A2<br>\n";
		if (is_null($dc)) {
			$dc=$this->dc1;
		}
		$dmid=$this->dmid;
		return CopyTools::getRows($dc, "collection_contents", "WHERE member_mid=$dmid");
	}


	public function read_definition($collection_id) {
		echo "crumb A3<br>\n";
		$dc1=$this->dc1;
		return CopyTools::getRow($dc1,"collection","WHERE collection_id=$collection_id");
	}

	/** write collection definition (and associated dm) to dc2
	 * if it doesn't already exist.
	 * If it already exists, will only look up the id.
	 * returns the  id for dc2 either way.
	 */
	public function write_definition($definition){
		echo "crumb A4--<br>\n";
		$dc1=$this->dc1;
		$dc2=$this->dc2;
		
		echo "crumb A4A-a<br>\n";
		$objcopier=new ObjectCopier($definition["collection_id"], $dc1, $dc2);
		$definition["collection_id"]=$objcopier->dup();
		var_dump($objcopier->already_there()); #crumb !

		echo "crumb A4A-b<br>\n";
		if (!$objcopier->already_there()) {
			echo "crumb A4B<br>\n";
			$dmid= $definition["collection_mid"];
			$dmcopier=new DefinedMeaningCopier($dmid,$dc1,$dc2);
			$definition["collection_mid"]=$dmcopier->dup_stub();

			echo "crumb A4C<br>\n";
			CopyTools::dc_insert_assoc($dc2, "collection", $definition);
			echo "crumb A4D<br>\n";

		}
		echo "crumb A4E<br>\n";
		return $definition["collection_id"];

	}
	
	/** look up the collection definition in %_collection, 
	 * and copy if doesn't already exist in dc2 
	 */
	public function dup_definition($collection_id) {
		echo "crumb A5<br>\n";
		$definition=$this->read_definition($collection_id);
		
		echo "crumb A5A<br>\n";
		return $this->write_definition($definition);
	}


	# we create a mapping and THEN do collections, now we need to prevent ourselves dupping 
	# existing mappings
	# @deprecated
	public function existing_mapping($member_id) {
		echo "crumb A6 ALERT DEPRECATED <br>\n";
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
		
		if ($this->doObject($row, "object_id")) {
			$this->already_there=true;
			return $row["object_id"];
		}

		$row["member_mid"]=$save_dmid;
		CopyTools::dc_insert_assoc($dc2, "collection_contents", $row);
	}

	public function write($rows){
		echo "crumb A8 <br>\n";
		foreach ($rows as $row) {
			echo "<a8>";
			$this->write_single($row);
		}
	}

	/** writes a duplicate. does *NOT* return ids on return, as there
	 * are multiple ids 
	 */
	public function dup() {
		echo "crumb A9 <br>\n";
		$rows=$this->read($this->dc1);
		echo "crumb A10 <br>\n";
		$this->write($rows);
		echo "crumb A11 <br>\n";
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
		if (is_null($dmid))
			throw new Exception ("DefinedMeaningCopier: read(): cannot read a dmid that is null");
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

	public function dup_stub (){
		$dmid=$this->dmid;
		$dc1=$this->dc1;
		$dc2=$this->dc2;

		$this->read();
		echo "<br><h2>".$this->dmid."</h2></br>";

		# bit of exp here too (defnitely need to tidy)
		$defining_expression=expression($this->defined_meaning["expression_id"], $dc1);
		$dm_target_table=mysql_real_escape_string("${dc2}_defined_meaning");
		$copier=new ObjectCopier($this->defined_meaning["defined_meaning_id"], $dc1, $dc2);
		$target_dmid=$copier->dup();
		$this->save_meaning=$this->defined_meaning;
		$this->save_meaning["defined_meaning_id"]=$target_dmid;

		$this->already_there=$copier->already_there();
		if (!($copier->already_there())) {
			$this->save_meaning["expression_id"]=write_expression($defining_expression, $dmid, $target_dmid, $dc1, $dc2);
		}
		$this->save_meaning["meaning_text_tcid"]=dup_translated_content($dc1, $dc2, $this->defined_meaning["meaning_text_tcid"]);
		if (!($copier->already_there())) {
			echo "crumb 1<br>\n";
			CopyTools::dc_insert_assoc($dc2, "defined_meaning", $this->save_meaning);

			$title_name=$defining_expression["spelling"];
			$title_number=$target_dmid;
			$title=str_replace(" ","_",$title_name)."_(".$title_number.")";
			CopyTools::createPage($title);
		
		}
		$concepts=array(
			$dc1 => $this->defined_meaning["defined_meaning_id"],
			$dc2 => $this->save_meaning["defined_meaning_id"]);
		$uuid_data=createConceptMapping($concepts);
		DefinedMeaningCopier::finishConceptMapping($dc1, $uuid_data[$dc1]);
		DefinedMeaningCopier::finishConceptMapping($dc2, $uuid_data[$dc2]);

		echo "crumb 3<br>\n";
		return $this->save_meaning["defined_meaning_id"];
	}		
	
	public static function finishConceptMapping($dc, $uuid) {
		$object_id=ObjectCopier::makeObjectID($dc, "collection_contents", $uuid);
		CopyTools::doQuery("	UPDATE ${dc}_collection_contents 
					SET object_id=$object_id
					WHERE internal_member_id=\"$uuid\"
					");
	}

	function dup_rest() {
		$dmid=$this->dmid;
		$dc1=$this->dc1;
		$dc2=$this->dc2;
		echo "crumb 4<br>\n";

		dup_syntranses(
			$this->defined_meaning["defined_meaning_id"],
			$this->save_meaning["defined_meaning_id"],
			$dc1,
			$dc2
		);
		

		echo "crumb 5 relation<br>\n";
		$relationsCopier=new RelationsCopier(
			$dc1, 
			$dc2, 
			$this->defined_meaning["defined_meaning_id"],
			$this->save_meaning["defined_meaning_id"]);
		$relationsCopier->dup();
		
		echo "crumb 6 collection <br>\n";
		$collectionCopier=new CollectionCopier(
			$dc1, 
			$dc2, 
			$this->defined_meaning["defined_meaning_id"],
			$this->save_meaning["defined_meaning_id"]);
		$collectionCopier->dup();

		echo "crumb 7 membership<br>\n";
		$classMembershipCopier=new ClassMembershipCopier(
			$dc1, 
			$dc2, 
			$this->defined_meaning["defined_meaning_id"],
			$this->save_meaning["defined_meaning_id"]);
		$classMembershipCopier->dup();
	}
}
	
/** provide a namespace for copying tools (so we don't clutter up the main namespace with
 * all our utility and tool functions) All functions here are public+static.
 */
class CopyTools {
	/** create a relevant entry in the `page` table. */
	public static function createPage($title) {
		# page is not a Wikidata table, so it needs to be treated differently (yet again :-/)
		$escTitle=mysql_real_escape_string($title);
		$existing_page_data=CopyTools::doQuery("SELECT * FROM page WHERE page_namespace=24 AND page_title=\"$escTitle\"");
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
	 * Gets a virtual user id from the wikidata_sets table, if available
	 * (else uses user 0)
	 * There's still some issues with transactions  especially wrt with user assignment
	 * where we intersect with the (old) "WikiDataAPI".
	 */
	public static function newCopyTransaction($dc1, $dc2) {

		$datasets=CopyTools::getRow_noDC("wikidata_sets", "WHERE set_prefix=\"$dc2\"");
		if (  $datasets == false  ) {
			throw new Exception("Dataset info for $dc2 not found.");
		}
		
		if (  array_key_exists("virtual_user_id", $datasets)  ) {
			$virtual_user_id=$datasets["virtual_user_id"];
		} else {
			$virtual_user_id=0;
		}
		
		startNewTransaction(
			$virtual_user_id, 
			"0.0.0.0", 
			"copying from $dc1 to $dc2", 
			$dc2	);
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
		return CopyTools::doQuery($query);
	}

	public static function getRow_noDC($table, $where) {
		$target_table=mysql_real_escape_string("${table}");
		$query="SELECT * FROM $target_table ".$where;
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
		$result = mysql_query($query);

		if (!$result) 
			throw new Exception("Mysql query failed: $query");

		if ($result===true) 
			return null;

		if (mysql_num_rows($result)==0) 
			return null;


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
		$result = mysql_query($query);
		if (!$result) 
			throw new Exception("Mysql query failed: $query");
		
		if ($result===true)
			return array();

		if (mysql_num_rows($result)==0) 
			return array();

		$items=array();
		while ($nextexp=mysql_fetch_assoc($result)) {
			$items[]=$nextexp;
		}
		return $items;
	}

	/** identical to the php function array_key_exists(), but eats dirtier input
	 * returns false (rather than an error) on somewhat invalid input. 
	 * (Namely, if either $key or $array is either null or false)
	 */
	public static function sane_key_exists($key, $array) {
		if (is_null($key) or $key==false){
			return false;
		}
		if (is_null($array) or $array==false) {
			return false;
		}
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
		
		global 
			$wdExtraDebugging;
		if ($wdExtraDebugging) {
			var_dump ($my_array);
			echo "<pre>$sql</pre>";
		}

		// Same with the values
		$result = mysql_query($sql);

		if ($result)
		{
			#echo "The row was added sucessfully";
			return true;
		}
		else
		{
			# how did we do errors again?
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

/**Copying uw_class_membership*/
class ClassMembershipCopier extends Copier{

	protected $old_class_member_mid;
	protected $new_class_member_mid;
	protected $dc1;
	protected $dc2;
	protected $tableName="class_membership";

	/** coming from the defined meaning(dm) we don't know the membership id,
	 * but we do have the dmid (defined meaning id) for the class member, so let's use that
	 */
	public function __construct($dc1, $dc2, $old_class_member_mid, $new_class_member_mid) {
		$this->old_class_member_mid=$old_class_member_mid;
		$this->new_class_member_mid=$new_class_member_mid;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}

	public function dup() {
		$memberships=$this->read();
		$this->write($memberships);
		
		return;
	}
		
	/** read all class memberships associated with the dmid */
	public function read() {
		$dc1=$this->dc1;
		$class_member_mid=$this->old_class_member_mid;
		return CopyTools::getRows($dc1, "class_membership", "WHERE class_member_mid=$class_member_mid");
	}

	public function write($memberships) {
		foreach ($memberships as $membership) {
			$this->write_single($membership);
		}
	}

	public function write_single($membership) { 
		$dc1=$this->dc1;
		$dc2=$this->dc2;
		$new_class_member_mid=$this->new_class_member_mid;
	
		$copier = new ObjectCopier($membership["class_membership_id"], $dc1, $dc2);
		$newid=$copier->dup();
		if ($copier->already_there()) {
			return $newid;
		}
		$membership["class_membership_id"]=$newid;
		$membership["class_member_mid"]=$new_class_member_mid;
		$oldmid=$membership["class_mid"];
		$this->doDM($membership,"class_mid", true);
		$newmid=$membership["class_mid"];
		$classAttributesCopier=new ClassAttributesCopier($oldmid, $newmid, $dc1, $dc2); 
		$classAttributesCopier->dup();
		echo "What are we working with <br>\n";
		var_dump($membership);
		CopyTools::dc_insert_assoc($dc2, "class_membership", $membership);
		return $newid;
	}

}

/** copying stuff in the %_class_attributes table actually
 * TODO: Actually I'm keying on class_mid atm, while I could be using the object_id-s
 * instead, the same way as the other AttributesCopiers.
 * I didn't realise this upfront. Changing this would be a nice improvement.
 */
class ClassAttributesCopier extends Copier {
	
	protected $src_class_mid;
	protected $dst_class_mid;
	protected $dc1;
	protected $dc2;
	protected $tableName="class_attributes";

	/** you saw that right, class_mid, not class_id, there's no such thing :-/
	 */
	public function __construct($src_class_mid, $dst_class_mid, $dc1, $dc2) {
		$this->src_class_mid=$src_class_mid;
		$this->dst_class_mid=$dst_class_mid;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}

	/** unchracteristically, returns the new class_mid, rather than object_id
	 * because in this case, the class_mid is the key characteristic
	 */
	public function dup() {
		if (is_null($this->src_class_mid))
			throw new Exception ("ClassAttributesCopier: Can't copy class; is null!");
		$attributes=$this->read();
		$this->write($attributes);
		return $this->dst_class_mid; # XXX currently broken:  actually it'll return the src_class_mid...
	}
	
	public function read() {
		$dc1=$this->dc1;
		$class_mid=$this->src_class_mid;
		return CopyTools::getRows($dc1, "class_attributes", "WHERE class_mid=$class_mid");
	}

	public function write($attributes) {
		foreach ($attributes as $attribute) {
			$this->write_single($attribute);
		}
	}

	
	public function write_single($attribute) {
		$dc1=$this->dc1;
		$dc2=$this->dc2;
		$class_mid=$this->src_class_mid;


		if ($this->doObject($attribute,"object_id")) 
			return $attribute["object_id"];

		$attribute["class_mid"]=$this->dst_class_mid;
		$this->doDM($attribute,"level_mid");
		$this->doDM($attribute,"attribute_mid");

		CopyTools::dc_insert_assoc($dc2, "class_attributes", $attribute);

		return $attribute["object_id"];
	}

}

/** copying stuff in the %_class_attributes table 
 * This version keys on attribute_id (object_id)
 */
class ClassAttributesCopier2 extends Copier {
	
	protected $object_id;
	protected $dc1;
	protected $dc2;
	protected $tableName="class_attributes";

	/** you saw that right, class_mid, not class_id, there's no such thing :-/
	 */
	public function __construct($object_id, $dc1, $dc2) {
		$this->object_id=$object_id;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}

	/** unchracteristically, returns the new class_mid, rather than object_id
	 * because in this case, the class_mid is the key characteristic
	 */
	public function dup() {
		if (is_null($this->object_id))
			throw new Exception ("ClassAttributesCopier2: Can't copy class by object_id: is null!");
		$attributes=$this->read();
		return $this->write($attributes);
	}
	
	# refactor candidate?
	public function read() {
		$dc1=$this->dc1;
		$object_id=$this->object_id;
		return CopyTools::getRows($dc1, $this->tableName, "WHERE object_id=$object_id");
	}

	#refactor_candidate
	public function write($attributes) {
		$latest=null;
		foreach ($attributes as $attribute) {
			$latest=$this->write_single($attribute);
		}
		return $latest;
	}

	
	public function write_single($attribute) {
		$dc1=$this->dc1;
		$dc2=$this->dc2;
		
		# TODO: Check: Is *this* actually safe?
		if ($this->doObject($attribute,"object_id")) 
			return $attribute["object_id"];

		$this->doDM($attribute, "class_mid"); #safe to do here, though not in the first ver.
		$this->doDM($attribute, "level_mid");
		$this->doDM($attribute, "attribute_mid");

		CopyTools::dc_insert_assoc($dc2, "class_attributes", $attribute);

		return $attribute["object_id"];
	}

}




/** abstract superclass for copiers
 *  will gradually be implemented anywhere I create, refactor, or 
 */
abstract class Copier {

	protected $dc1; // Source dataset
	protected $dc2; // Destination dataset
	protected $tableName; 	//Name of the table this class operates on.
				// if multiple tables, name of whatever principle table.
	protected $autovivifyObjects=false; 	// false: throw an error if we find 
						// 	  null references to the objects table
						// true: instead, create a valid
						// 	entry in the objects table and
						//	do the correct referencing
						// see also: ObjectCopier::$autovivify
	protected $already_there=null;	// true:	item was already present in 
					//		destination (dc2) dataset. No copy made.
					// false:	item was not present. Copy made.
					// null:	don't know (yet) / error/ other
					// see also: ObjectCopier::$already_there

	/** does the actual copying
	 * @return the unique id for the item we just copied in the destination (dc2) dataset,
	 *         or null, if no such id exists in this case (for instance, if we copied multiple
	 *         items, there is no single unique id)
	 */
	public abstract function dup();

	/** reads row or rows from table in source dataset (dc1) 
	 * @return row or array of rows for table in mysql_read_assoc() format */
	protected abstract function read();

	/** writes row or array of rows in mysql_read_assoc() format
	 * @return the unique id for the item we just copied in the destination (dc2) dataset,
	 *         or null, if no such id exists in this case (for instance, if we copied multiple
	 *         items, there is no single unique id)
	 */
	//public abstract function write();

	/** @returns true if the copied item was already present in the other dataset, false if it wasn't (and we just copied it over) , or null if don't know/error/other.
	 */
	public function already_there(){
		return $this->already_there;
	}

	/**
	 * A combination function to handle all the steps needed to check
	 * and copy a Defined Meaning (DM)
	 * So we have a row in the source (dc1) table, which has a column
	 * referring to a defined meaning
	 * Before the row can be stored in the destination dataset, 
	 * we should
	 * - Ensure that at least a stub of this defined meaning exists
	 * - make sure that the row refers to the dmid in the *destination* dataset (dc2),
	 *   instead of the source dataset (dc1).
	 * - returns True if the defined meaning was already_there().
	 * @param &$row : row to operate on, passed by reference
	 * @param $dmid_colum: a column in said row, containing the dmid to operate on
	 * @param $full=false (optional) : if true, does a dup instead of a dup_stub
	 * @return true if the updated dmid already existed in the destination (dc2) dataset before now
	 *	   false if it did not, and we just created it
	 */
	protected function doDM(&$row, $dmid_column, $full=false) {
		echo "IN COPIER<br>\n";
		var_dump($row);
		$dmCopier=new DefinedMeaningCopier($row[$dmid_column], $this->dc1, $this->dc2);
		if ($full) {
			$row[$dmid_column]=$dmCopier->dup();
		} else {
			$row[$dmid_column]=$dmCopier->dup_stub();
		}
		return $dmCopier->already_there();
	}

	/** 
	 * performs all the tasks to do with the column associated with
	 * the objects table in one go.
	 * 
	 * Assuming the row originally contains an object_id in the source dataset (dc1)
	 * updates the row(passed by reference) with the relevant object_id in the destination
	 *   dataset (dc2)
	 * 
	 * @param &$row : row to operate on, passed by reference
	 * @param $object_column: a column in said row, containing the object reference to operate on
	 * @returns 	true if examination of the objects table reveals that this particular row should already
	 *			exist in the destination dataset
	 *		false if this particular row does not yet exist in the table in the destination dataset. 
	 *			The objects table, and object reference
	 *			in your array have already been set correctly. Continue by filling in the rest
	 *			of the row data. (Do so before COMMIT).
	 *
	 * behaviour is modified by object properties $this->tableName and $this->autovivifyObjects.
	 */
	protected function doObject(&$row, $object_column) {
		$copier=new ObjectCopier($row[$object_column], $this->dc1, $this->dc2);
		$copier->setTableName($this->tableName);
		$copier->setAutovivify($this->autovivifyObjects);
		$row[$object_column]=$copier->dup();
		$this->already_there=$copier->already_there();
		return $this->already_there;
	}

	protected function doInsert($row) {
		CopyTools::dc_insert_assoc($this->dc2, $this->tableName, $row);
	}

}


abstract class AttributeCopier extends Copier {

	protected $src_object_id=null;
	protected $dst_object_id=null;

	public function __construct($dc1, $dc2, $src_object_id, $dst_object_id){
		$this->dc1=$dc1;
		$this->dc2=$dc2;
		$this->src_object_id=$src_object_id;
		$this->dst_object_id=$dst_object_id;
	}


	public static function copy($dc1, $dc2, $src_object_id, $dst_object_id) {
		echo "<h3> crumb: would copy attribs </h3>";
		if (is_null($src_object_id)) 
			throw new Exception("AttributeCopier: cannot copy: source object_id=null");

		if (is_null($dst_object_id)) 
			throw new Exception("AttributeCopier: cannot copy: destination object_id=null");
		$optionAttributeCopier=new OptionAttributeCopier($dc1, $dc2, $src_object_id, $dst_object_id);
		$optionAttributeCopier->dup();

		#$textAttributeCopier=new textAttributeCopier($this->dc1, $this->dc2, $src_object_id, $dst_object_id);
		#$textAttributeCopier->dup();

		#$translatedContentCopier=new translatedContentCopier($this->dc1, $this->dc2, $src_object_id, $dst_object_id);
		#$translatedContentCopier->dup();

		#$urlAttributeCopier=new URLAttributeCpier($this->dc1, $this->dc2, $src_object_id, $dst_object_id);
		#$urlAttributeCopier->dup();
	}
		
	protected function write($values) {
		$latest=null;
		foreach ($values as $value) {
			$latest=write_single($value);
		}
		return $latest;
	}

	protected abstract function write_single($attribute);

	protected function read() {
		$src_object_id=$this->src_object_id;
		if (is_null($src_object_id)) 
			throw new Exception("*AttributeCopier: cannot read: source object_id is null");

		$tableName=$this->tableName;
		if (is_null($tableName)) 
			throw new Exception("*AttributeCopier: cannot read: table name is null");

		return CopyTools::getRows($this->dc1, $tableName, "WHERE object_id=$src_object_id");
	}

	/** slightly different dup interface yet again. 
	 *  (I'm still experimenting. TODO: Settle on one for all.)
	 *  always returns destination object_id of last/arbitrary 
	 *  item dupped. (which means we can use this particular dup functuon 
	 *  for single *or* multi copy)
	 */
	public function dup() {
		$attributes=$this->read();
		return	$this->write($attributes);
	}

}


class OptionAttributeCopier extends AttributeCopier{
	protected $tableName="option_attribute_values"; 	//Name of the table this class operates on.
	
	public function __construct($dc1, $dc2, $src_object_id, $dst_object_id){
		parent::__construct($dc1, $dc2, $src_object_id, $dst_object_id);
	}

	/**
	 * *all attribute_value tables:
	 * **value_id: unique id in objects table
	 * **object_id: object we are referring to
	 * * Unique to option_attribute_values
	 * ** option_id: reference to the option_attribute_options table
	 */
	public function write_single($attribute) {

		if ($this->doObject($attribute, "value_id"))
			return $attribute["value_id"];

		$attribute["object_id"]=$this->new_object_id;

		$oaocopier=new OptionAttributeOptionsCopier($attribute["option_id"], $dc1, $dc2);
		$attribute["option_id"]=$oaocopier->dup();

		$this->doInsert($attribute);
		return $attribute["value_id"];
	}
}

/** Yes, there is actually a table called option_attribute_options.
 * These are the actual *options* that go with a particular option attribute
 * extends copier, not AttributeCopier, because oa_options are not themselves attributes.
 *
 * naming: $oao(s) is/are ObjectAtributeOption(s).
 */
class OptionAttributeOptionsCopier extends Copier {
	protected $option_id;
	protected $tableName="option_attribute_options"; //Name of the table this class operates on.

	public function __construct($option_id, $dc1, $dc2) {
		if (is_null($option_id)) 
			throw new Exception("OptionAttributeOptionsCopier: trying to construct with null option_id. No can do compadre.");

		$this->option_id=$option_id;
		$this->dc1=$dc1;
		$this->dc2=$dc2;
	}	

	public function dup() {
		$oaos=read();
		return write($oaos);
	}
	
	public function read(){
		return CopyTools::getRows($dc1, "class_membership", "WHERE class_member_mid=$class_member_mid");
	}

	/**
	 * TODO This is a refactor-candidate.
	 */
	public function write($oaos) {
		$latest=null;
		foreach ($oaos as $oao) {
			$latest=$this->write_single($oao);
		}
		return $latest;
	}

	/**
	* option_id: unique/ objects reference
	* attribute_id: reference to class_attributes (we think!)
	* option_mid: dm for this object. 
	* language_id: reference to mediawiki languages table
	*/
	public function write_single($oao) {

		if ($this->doObject($oao, "option_id"))
			return $oao["option_id"];

		$cacopier=new ClassAttributesCopier($oao["attribute_id"], $dc1, $dc2);
		$oao["attribute_id"]=$cacopier->dup();

		$this->doDM($oao, "option_mid");
		#language_id is mediawiki, not wikidata, so that's ok.

		return $oao["option_id"];
	}


}


?>
