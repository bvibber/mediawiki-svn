<?php
/*
 * MV_Index.php Created on May 16, 2007
 *
 * All Metavid Wiki code is Released under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 * 
 */
 /*
  * maintains the index table state on various updates 
  * (need for indexed time ranged queries) 
  * (currently stored in a mysql table) 
  * in the future we should shift this to a search engine of some sort 
  * (ie lucene or sphinx) or just do everything with semantic relations
  * if we can get the speed to an acceptable level...
  * 
  * keep an additional copy of the text table is not ideal but...
  * we keep a copy of every version of the page so its not so bad ;P
  */
if ( !defined( 'MEDIAWIKI' ) )  die( 1 );

 class MV_Index {
 	var $mvTitle =null;
 	function __construct(& $mvTitle=null){
 		if($mvTitle!=null)
 			$this->mvTitle=$mvTitle; 		
 	}
 	/*
 	 * grabs near options count 
 	 * (similar to getNearStreams but returns a count instead of a result set)
 	 * 
 	 * @params:
 	 * $range_offset 
 	 * 
 	 * options: 
 	 * limit_by_current_type *default ...only include meta chunks of the current mvTitle type
 	 * limit_by_type = $type 	...include stream of given $type
 	 * all_in_rage		*default...include all meta that have any portion in range
 	 * contained_in_range		...include only meta that are contained in the given range
 	 * start_or_end_in_range 	...include only meta that start or end in the given range
 	 */
 	function getNearCount($range_offset='', $options=array()){ 		
 		global $mvDefaultClipLength, $mvIndexTableName;
 		if($range_offset=='')$range_offset=$mvDefaultClipLength;
 		
 		$dbr =& wfGetDB(DB_SLAVE);
 		//set up the count sql query:
 		$sql = "SELECT COUNT(1) as `count` FROM {$dbr->tableName($mvIndexTableName)} " . 
 			   "WHERE `stream_id`={$this->mvTitle->getStreamId()} ";
 		if(isset($options['limit_by_type'])){
 			$sql.="AND `mvd_type`='{$options[limit_by_type]}' "; 				
 		}else{
 			$sql.="AND `mvd_type`='{$this->mvTitle->getMvdTypeKey()}' "; 			
 		}
 		$st = $this->mvTitle->getStartTimeSeconds() - $range_offset;
 		if($st<0)$st=0;
 		//if end time not present use startTime 
 		if($this->mvTitle->getStartTimeSeconds()){
 			$et = $this->mvTitle->getStartTimeSeconds() + $range_offset;
 		}else{
 			$et = $this->mvTitle->getStartTimeSeconds() + $range_offset;
 		}
 		//make sure we don't exceed the stream duration
 		if($et > $this->mvTitle->getDuration())$et=$this->mvTitle->getDuration();
 		//default all in range:
 		$sql.=' AND ( ';
	 		// double or set for null or non-null end time range queries: 
	 		$sql.=" (`end_time` IS NULL 
						AND `start_time` > {$st} 
						AND `start_time` < {$et} 
	 			 	) 
	 			 	 OR  
	 			  	(`end_time` IS NOT NULL  
	 			   	AND `end_time` > {$st} 
	 			 	AND `start_time` < {$et} 
					) 
				)"; 		  			  
 		$result = $dbr->query($sql);
 		$row = $dbr->fetchObject( $result );
 		//print_r($row);
 		return $row->count; 		 		
 	}
 	function countMVDInRange($stream_id, $start_time=null, $end_time=null, $mvd_type='all'){
 		global $mvIndexTableName, $mvDefaultClipLength; 		
 		$dbr =& wfGetDB(DB_SLAVE);	 
 		$sql = "SELECT COUNT(1) as `count` " .
 				"FROM {$dbr->tableName($mvIndexTableName)} " . 
 				"WHERE `stream_id`={$stream_id} ";
		if($mvd_type!='all'){
			$sql.="AND `mvd_type`='{$mvd_type}' ";
		}
		//get any data in rage: 
		if($end_time)$sql.=" AND `start_time` <= " . $end_time;
		if($start_time)$sql.=" AND `end_time` >= " . $start_time; 	
		$result =& $dbr->query( $sql, 'MV_Index:countMVDInRange'); 
		$row = $dbr->fetchObject($result);	
		return $row->count;
 	}
 	/*
 	 * getMVDInRange returns the mvd titles that are in the given range
 	 */
 	function getMVDInRange($stream_id, $start_time=null, $end_time=null, $mvd_type='all',$getText=false){
 		global $mvIndexTableName, $mvDefaultClipLength; 		
 		$dbr =& wfGetDB(DB_SLAVE);	 		
 		
 		$sql = "SELECT `mv_page_id` as `id`, `mvd_type`, `wiki_title`, `stream_id`, `start_time`, `end_time` " .
 				"FROM {$dbr->tableName($mvIndexTableName)} " . 
 				"WHERE `stream_id`={$stream_id} ";
		if($mvd_type!='all'){
			//check if mvd_type is array:
			$sql.="AND `mvd_type`='{$mvd_type}' ";
		}
		//get any data that covers this rage: 
		if($end_time)$sql.=" AND `start_time` <= " . $end_time;
		if($start_time)$sql.=" AND `end_time` >= " . $start_time; 
		//add in ordering 
		$sql.=' ORDER BY `start_time` ASC ';
		//add in limit of 200 for now
		$sql.=' LIMIT 0, 200';
		//echo $sql;
 		$result =& $dbr->query( $sql, 'MV_Index:time_index_query'); 	 	
 		return $result;
 	}
 	function get_mvd_type_sql($mvd_type){
 		if(is_array($mvd_type)){
 			
 		} 		
 	}
 	function remove_by_stream_id($stream_id){
 		global $mvIndexTableName;
 		$dbw =& wfGetDB(DB_WRITE); 
 		$dbw->delete($mvIndexTableName, array('stream_id'=>$stream_id));
 	}
 	/* 
 	 * removes a single entry by wiki_title name
 	 */
 	function remove_by_wiki_title($wiki_title){
 		global $mvIndexTableName;
 		$dbw =& wfGetDB(DB_WRITE); 
 		$dbw->delete($mvIndexTableName, array('wiki_title'=>$wiki_title));
 		return true;
 	}
 	function doFiltersQuery(&$filters){
 		global $mvIndexTableName, $mvDefaultClipLength, $wgRequest, $mvDo_SQL_CALC_FOUND_ROWS, $mvSpokenByInSearchResult; 		
 		$dbr =& wfGetDB(DB_SLAVE);
 		//organize the queries (group full-text searches and category/attributes)
 		//if the attribute is not a numerical just add it to the fulltext query 
 		$ftq=$toplq=$snq=''; //top query and full text query ='' 		
 		if($filters=='')return array(); 		
 		
 		$selOpt = ($mvDo_SQL_CALC_FOUND_ROWS)?'SQL_CALC_FOUND_ROWS':''; 
 		
 		list( $this->limit, $this->offset ) = $wgRequest->getLimitOffset( 20, 'searchlimit' );
 		//print_r($filters);
 		//print_r($_GET);
 		foreach($filters as $f){
 			//proocc and or for fulltext:
 			if(!isset($f['a']))$f['a']='and';
 			switch($f['a']){
 				case 'and':$aon='+';break;
 				case 'or':$aon='';break;
 				case 'not':$aon='-';break;
 			}
 			//add to the fulltext query: 
 			switch($f['t']){
 				case 'spoken_by': 					
 					$ftq.=' '.$aon.'"spoken by::'.mysql_escape_string($f['v']).'"';
 				break; 			
 				case 'match':
 					$ftq.=' '.$aon.'"'.mysql_escape_string($f['v']).'"';
 				break;
 				//top level queries  (sets up time ranges )
 				case 'category': 				
 					$toplq.=' '.$aon.'"category:'.mysql_escape_string($f['v']).'" ';
 					//$ftq.=' '.$aon.'category:'.mysql_escape_string($f['v']);
 				break;
 				case 'stream_name':
 					if($snq!=''){
						switch($f['a']){
			 				case 'and':$snq='AND';break;
			 				case 'or':$snq='OR';break;
			 				case 'not':$snq='NOT';break;
			 			}			
 					}	
 					//get stream name:
 					//print "f: " . $f['v'];
 					$stream =& mvGetMVStream($f['v']);
 					$snq.=" `stream_id` = {$stream->getStreamId()} ";
 				break;
 				case 'smw_property':
	 				//more complicated query work needed ;)
 				break;
 			} 		
 		}
 		$searchindexTable = $dbr->tableName( 'searchindex' );
 		$ret_ary = array();
 		//only run the top range query if we have no secondary query
 		if($toplq!='' && $ftq==''){
 			$andstr='';
 			//apend AND if $snq not null: 
 			if($snq!='')$andstr.='AND'; 			
 			//@@todo we should only look in annotative layer for top level queries? ...
 			//@@todo paging for top level queries? ... 100 stream limit is probably ok  
 			//@@ no spoken by attribute for 'anno_en' mvd_type 	
 			$sql = "SELECT `mv_page_id` as `id`, `stream_id`,`start_time`,`end_time`, `wiki_title`, $searchindexTable.`si_text` as `text`
	 			FROM `$mvIndexTableName` 
	 			JOIN $searchindexTable ON `$mvIndexTableName`.`mv_page_id` = $searchindexTable.`si_page`
	 			WHERE $snq $andstr `mvd_type`='Anno_en' 
		 			AND MATCH ($searchindexTable.`si_text`) 
		 			AGAINST('$toplq' IN BOOLEAN MODE)
	 			LIMIT 0, 100";
	 		echo "topQ: $sql \n\n";
 			$top_result = $dbr->query($sql); 			
 			if($dbr->numRows($top_result)==0)return array();
 			//set up ranges sql query
 			$sql="SELECT $selOpt `mv_page_id` as `id`, `stream_id`,`start_time`,`end_time`, `wiki_title`, $searchindexTable.`si_text` as `text` ";
 				if($mvSpokenByInSearchResult)$sql.=",`smw_relations`.`object_title` as `spoken_by` ";
 				$sql.="FROM `$mvIndexTableName` " .
 				"JOIN $searchindexTable ON `$mvIndexTableName`.`mv_page_id` = $searchindexTable.`si_page` ";
 				if($mvSpokenByInSearchResult){
	 				$sql.="LEFT JOIN `smw_relations` ON (`mv_mvd_index`.`mv_page_id`=`smw_relations`.`subject_id` " .
	 					"AND `smw_relations`.`relation_title`='Spoken_By') ";
	 			}
 				$sql.="WHERE  ";
 			$or=''; 	
 			$sql.='( ';			 				  				 
 			while($row = $dbr->fetchObject( $top_result )){ 	
 				//also set initial sranges:
 				if(!isset($ret_ary[$row->stream_id]))$ret_ary[$row->stream_id]=array();
 				//insert into return ary: 				
 				$insertRow = ($ftq=='')?true:false;
 				//add that its a top level query to the row: 
 				$row->toplq=true;
 				MV_Index::insert_merge_range($ret_ary[$row->stream_id], $ret_ary, $row, $insertRow);	
 				 							
 				$sql.=$or. ' (`stream_id`='.$row->stream_id.' AND ' .
 						'`start_time`>='.$row->start_time.' AND '.
						'`end_time`<='.$row->end_time.' ) ';						
 				$or=' OR ';
 			} 			
 			$sql.=') '; 
 			//if($ftq!='')
 			//	$sql.=" AND MATCH (text) 
	 		//		AGAINST('$ftq' IN BOOLEAN MODE) ";	 		
		 	$sql.="LIMIT {$this->offset}, {$this->limit} ";
 		}else{ 		
 			//add the top query to the base query: 
 			$ftq.=$toplq;
	 		$sql = "SELECT $selOpt `mv_page_id` as `id`,`stream_id`,`start_time`,`end_time`, `wiki_title`, $searchindexTable.`si_text` AS `text` ";
	 		if($mvSpokenByInSearchResult)$sql.=",`smw_relations`.`object_title` as `spoken_by` ";
	 		$sql.="FROM `$mvIndexTableName` 
	 			JOIN $searchindexTable ON `$mvIndexTableName`.`mv_page_id` = $searchindexTable.`si_page` ";
	 			
 			//include spoken by relation in results (LEFT JOIN should not be *that* costly )
 			if($mvSpokenByInSearchResult){
 				$sql.="LEFT JOIN `smw_relations` ON (`mv_mvd_index`.`mv_page_id`=`smw_relations`.`subject_id` " .
 					"AND `smw_relations`.`relation_title`='Spoken_By') ";
 			}
	 		$sql.="WHERE $snq ";
	 		if($ftq!=''){
	 			$sql.="	MATCH ( $searchindexTable.`si_text` ) 
	 				AGAINST('$ftq' IN BOOLEAN MODE) ";
	 		}
	 		$sql.="LIMIT {$this->offset}, {$this->limit} ";
 		}
 		echo "SQL:".$sql;  			
 		$result = $dbr->query($sql);
 		
 		$this->numResults=$dbr->numRows($result);
 		if($dbr->numRows($result)==0) return array();
 		
 		if($mvDo_SQL_CALC_FOUND_ROWS){
 			$resFound = $dbr->query('SELECT FOUND_ROWS() as `count`;');
 			$found = $dbr->fetchObject( $resFound );
 			$this->numResultsFound = $found->count;
 		}else{
 			$this->numResultsFound =null;
 		}
 		//@@TODO hide empty categories (if limit > rows found )
 		
 		//group by time range in a given stream
 		
 		//while($row = $dbr->fetchObject( $result )){
 		//	$ret_ary[]=$row;
 		//}
 		//return $ret_ary;
 		//group by stream_name & time range: 
 		while($row = $dbr->fetchObject( $result )){
 			if(!isset($ret_ary[$row->stream_id])){
 				$ret_ary[$row->stream_id]=array();
 			} 		
 			if(count($ret_ary[$row->stream_id])==0){
 				$new_srange = array('s'=>$row->start_time, 
									'e'=> $row->end_time,
									'rows'=>array($row));
 				$ret_ary[$row->stream_id][]=$new_srange;
 			}else{
 				MV_Index::insert_merge_range($ret_ary[$row->stream_id], $ret_ary, $row);	 			
 			}
 		} 		 		
 		//throw out empty top level ranges
 		foreach($ret_ary as &$stream_set){
 			foreach($stream_set as $k=> &$srange){
 				if(count($srange['rows'])==0){
 					//print "throw out: ". $srange['s'] . $srange['e'];
 					unset($stream_set[$k]); 					
 				}
 			}
 		} 			 	
 		return $ret_ary;
 	}
 	function numResultsFound(){
 		if(isset($this->numResultsFound)){
 			return $this->numResultsFound;
 		}else{
 			return null;
 		}
 	}
 	function numResults(){
 		if(isset($this->numResults))
 			return $this->numResults;
 		return 0;
 	}
 	/*inserts search result into proper range and stream */ 
 	function insert_merge_range(& $sranges, &$ret_ary, $row, $doRowInsert=true){
 		foreach($sranges as & $srange){	 		
 				if($srange)	
 				//check if current encasolates $srow encapsulate and insert
 				if($row->start_time <= $srange['s']  && $row->end_time >= $srange['e']){
 					$srange['s']= $row->start_time;
 					$srange['e']= $row->end_time;
 					if($doRowInsert)
 						$srange['rows'][]=$row;		 					
 					//grab rows from any other stream segment that fits in new srange:  
 					foreach($ret_ary as &$sbrange){
 						if($row->start_time <= $sbrange['s']  && $row->end_time >= $sbrange['e']){
 							foreach($sbrange['rows'] as $sbrow){
 								$srange['rows'][]=$sbrow;
 							}
 							unset($sbrange);
 						}
 					}
 					return ;
 				}//else if current fits into srange insert
 				else if($row->start_time >= $srange['s']  &&  $row->end_time <= $srange['e']){
 					if($doRowInsert)
 						$srange['rows'][]=$row;
 					return ;
 				}
 				//make sure the current row does not already exist: 
 				foreach($srange['rows'] as $sbrow){
 					if($sbrow->wiki_title == $row->wiki_title){
 						return ;
 					}
 				}
		}	 			
		//just add as new srange
		$new_srange = array('s'=>$row->start_time, 
							'e'=> $row->end_time);
		if($doRowInsert){
			$new_srange['rows']=array($row);
		}else{
			$new_srange['rows']=array();
		}
		$ret_ary[$row->stream_id][]=$new_srange; 		 		
 	}
 	function getMVDbyId($id, $fields='*'){ 	
 		global $mvIndexTableName;	
 		$dbr =& wfGetDB(DB_SLAVE);
 		$result = $dbr->select( $mvIndexTableName, $fields,
 			array('mv_page_id'=>$id) );	
 		if($dbr->numRows($result)==0){
 			return array();
 		}else{			 			
 			return $dbr->fetchObject( $result );
 		} 		
 	}
 	function getMVDbyTitle($title_key, $fields='*'){ 	
 		global $mvIndexTableName;	
 		$dbr =& wfGetDB(DB_SLAVE);
 		$result = $dbr->select( $mvIndexTableName, $fields,
 			array('wiki_title'=>$title_key) );	
 		if($dbr->numRows($result)==0){
 			return null;
 		}else{			 			
 			return $dbr->fetchObject( $result );
 		} 		
 	}
 	function update_index_title($old_title, $new_title){
 		global $mvIndexTableName;

 		//make sure the new title is valid:  		
 		$mvTitle = new MV_Title( $new_title ); 		
 		if( $mvTitle->validRequestTitle() ){ 		
 			//build the update row
 			$update_ary = array(
				'wiki_title'=>$mvTitle->getWikiTitle(),
				'mvd_type'=>$mvTitle->getTypeMarker(),
				'stream_id'=>$mvTitle->getStreamId(), 
				'start_time'=>$mvTitle->getStartTimeSeconds(),
				'end_time'=>$mvTitle->getEndTimeSeconds() );
 			//get old row
 			$mvd_row = MV_Index::getMVDbyTitle( $old_title ); 			
 			$dbw =& wfGetDB(DB_WRITE); 	
 			$dbw->update($mvIndexTableName, $update_ary, 
 				array('mv_page_id'=>$mvd_row->mv_page_id));
 		}else{
 			//print "NOT VALID MOVE";
 			//@@todo better error handling (tyring to move a MVD data into bad request form)
 			throw new MWException("Invalid Page name for MVD namespace \n");
 		}			
 	}
 	/*
 	 * update_index_page updates the `mv_mvd_index` table (on MVD namespace saves) 
 	 */
 	function update_index_page(&$article, &$text){
 		global $mvgIP, $mvIndexTableName;
 		//check static or $this usage context
	 	//use mv title to split up the values: 		
		$mvTitle = new MV_Title($article->mTitle->getDBkey());
		//print "Wiki title: " . $mvTitle->getWikiTitle();	
 		//fist check if an mvd entry for this stream already exists:  		
		$mvd_row = MV_Index::getMVDbyTitle( $mvTitle->getWikiTitle() );
		//set up the insert values:
		$insAry = array(
			'wiki_title'=>$mvTitle->getWikiTitle(),
			'mvd_type'=>$mvTitle->getTypeMarker(),
			'stream_id'=>$mvTitle->getStreamId(), 
			'start_time'=>$mvTitle->getStartTimeSeconds(),
			'end_time'=>$mvTitle->getEndTimeSeconds(),			
		);
		
		$dbw =& wfGetDB(DB_WRITE); 					
 		if(count($mvd_row)==0){
 			return $dbw->insert( $mvIndexTableName , $insAry); 			
 		}else{
 			$dbw->update($mvIndexTableName, $insAry, 
 				array('mv_page_id'=>$mvd_row->mv_page_id));
 		}
 	}
 }
?>
