<?php
/*
 * scrape_and_insert.php Created on Oct 1, 2007
 * 
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 */ 

 $cur_path = $IP = dirname( __FILE__ );
 //include commandLine.inc from the mediaWiki maintenance dir: 
require_once( '../../../maintenance/commandLine.inc' );
require_once('maintenance_util.inc.php');

 if ( count( $args ) == 0 || isset( $options['help'] ) ) {
 	print <<<EOT
 	
Scrapes External WebSites and updates relavent local semantic content.
 
Usage php scrape_and_insert.php insert_type [site] [options]
site:
	'cspan_chronicle' will take all it can from  http://www.c-spanarchives.org/
options: 		
	'-s --stream_name steam_name|all' the strean name or keyword "all" to proc all streams
	'--limit X' to only process X number of streams (when stream_name set to all)
	'--offset Y' to start on Y of streams (when stream_name set to all)

EOT;
exit();
}
/*
 * procc the request
 */ 
 function proc_args(){
 	global $args; 	
	switch($args[0]){
		case 'cspan_chronicle':
			$MV_CspanScraper = new MV_CspanScraper();
			$MV_CspanScraper->doScrapeInsert();
		break;
	}
 }

/*
 * set up the user:
 */
$userName = 'mvBot';
$wgUser = User::newFromName( $userName );
if ( !$wgUser ) {
	print "Invalid username\n";
	exit( 1 );
}
if ( $wgUser->isAnon() ) {
	$wgUser->addToDatabase();
}

class MV_CspanScraper extends MV_BaseScraper{	
	var $base_url = 'http://www.c-spanarchives.org/congress/?q=node/69850';
	function procArguments(){
		global $options, $args;		
		if( !isset($options['stream_name']) && !isset($options['s'])){				
			die("error missing stream name\n");
		}else{			
			$stream_inx = (isset($options['stream_name']))?$options['stream_name']:$options['s'];
			if($args[$stream_inx]=='all'){
				//put all in sync into stream list
				print "do all streams\n";
			}else{
				$stream_name = $args[$stream_inx];
				$this->streams[$stream_name]= new MV_Stream(array('name'=>$stream_name));		
				if(!$this->streams[$stream_name]->doesStreamExist()){
					die('error: stream '.$stream_name . ' does not exist');
				}
				print "Proccessing Stream: $stream_name \n";
			}
		}				
	}
	function doScrapeInsert(){
		foreach($this->streams as & $stream){
			if(!isset($stream->date_start_time))$stream->date_start_time=0;
			if($stream->date_start_time==0){
				die('error stream '. $stream->name . ' missing time info'."\n");
			}
			$hors =  (strpos($stream->name, 'house')!==false)?'h':'s';
			$date_req = date('Y-m-d', $stream->date_start_time);
			$cspan_url = $this->base_url . '&date='.$date_req.'&hors='.$hors;
			echo $cspan_url . "\n";			
			$rawpage = $this->doRequest($cspan_url);		
			//get the title and href if present:
			$patern = '/overlib\(\'(.*)\((Length: ([^\)]*)).*CAPTION,\'<font size=2>(.*)<((.*href="([^"]*))|.*)>/'; 
			preg_match_all($patern, $rawpage, $matches);
			$cspan_person_ary = array();
			//format matches: 
			foreach($matches[0] as $k=>$v){
				$href='';
				$href_match=array();
				preg_match('/href="(.*)"/',$matches[5][$k], $href_match);
				if(count($href_match)!=0)$href=$href_match[1];
				
				$porg = str_replace('<br>',' ',$matches[4][$k]);
				$porg = preg_replace('/[D|R]+\-\[.*\]/', '', $porg);
				$pparts = explode(',',$porg);
				$pname = trim($pparts[1]) . '_' . trim($pparts[0]);			
				$cspan_person_ary[]= array(
					'start_time'=>strip_tags($matches[1][$k]),
					'length'=>$matches[3][$k],
					'person_title'=>str_replace('<br>',' ',$matches[4][$k]),
					'Spoken_by'=>$pname,
					'href'=>$href
				);
			}									
		    //group people in page matches
		    //$g_cspan_matches=array();
		    //$prev_person=null;		    
		    //foreach($person_time_ary as $ptag){
		    //	$g_cspan_matches[strtolower($ptag['Spoken_by'])][]=$ptag;		    			    			    
		    //}
		   
		    //retrive db rows to find match: 
		   	$dbr =& wfGetDB(DB_SLAVE);
		    $mvd_res = MV_Index::getMVDInRange($stream->id, null, null, $mvd_type='ht_en',false,$smw_properties=array('Spoken_by'), '');
		    $db_person_ary=$g_row_matches=array();
		    //group peole in db matches:
		    $cur_person = '';
		    $curKey=0;
		   	while ($row = $dbr->fetchObject($mvd_res)) {			   		   		   				   		
		   		if(!isset($row->Spoken_by))continue;  	   				   
   				if($cur_person!=$row->Spoken_by){
   					$g_row_matches[]=get_object_vars($row);
   					$curKey=count($g_row_matches)-1;
   					$cur_person=$row->Spoken_by;
   				}else{
   					$g_row_matches[$curKey]['end_wiki_title']=$row->wiki_title;
   					$g_row_matches[$curKey]['end_time']+=($row->end_time-$row->start_time);
   				}	   	
   				//print_r($g_row_matches);
   				//if($curKey>2){
   				//	die;
   				//}   				
		   	}  
		   	//don't add people if they show up for less than 1 min 		
		   	foreach($g_row_matches as $row){
		   		print $row['Spoken_by'] . ' on screen for '. ($row['end_time']-$row['start_time']) . "\n";
		   		$db_person_ary[]=$row;
		   	}
		   	
			//print_r($db_person_ary);
			//die;
	
		   	//count($cspan_person_ary)	
		   	$cur_db_inx=0;	   		
		   	$fistValid=true;	   		 
		   	for($i=0;$i<10;$i++){
		   		print "looking at: ". $cspan_person_ary[$i]['Spoken_by'] . "\n";
		   		print "\tCSPAN: ". $cspan_person_ary[$i]['Spoken_by'] . ' on screen for '. $cspan_person_ary[$i]['length'].' or:'.ntp2seconds($cspan_person_ary[$i]['length']). "\n";
		   		//set up the next and prev pointers: 
		   		if(isset($cspan_person_ary[$i+1])){
		   			$next_person = (mv_is_valid_person($cspan_person_ary[$i+1]['Spoken_by']))?
		   				$cspan_person_ary[$i+1]['Spoken_by']:null;
		   		}else{
		   			$next_person=null;
		   		}
		   		if(isset($cspan_person_ary[$i-1])){
			   		$prev_person = (mv_is_valid_person($cspan_person_ary[$i-1]['Spoken_by']))?
			   			$cspan_person_ary[$i-1]['Spoken_by']:null;		
		   		}else{
		   			$prev_person=null;
		   		}		   			 		   	
		   		
		   		
		   		if(mv_is_valid_person($cspan_person_ary[$i]['Spoken_by'])){		   					   		
		   			print "\tis valid person looking for db sync\n";
		   			if($prev_person==null && $next_person==null){
		   				print "error both prev and next are null skiping person\n";		   			
		   				continue;
		   			}
		   			
		   			//check how long they where on screen (also check subquent)
		   			$cspan_on_screen_time=ntp2seconds($cspan_person_ary[$i]['length']);
		   			print "$cur_db_inx " . count($db_person_ary);
		   			for($j=$cur_db_inx;$j<count($db_person_ary);$j++){		
						print "searchig db on: " . $db_person_ary[$j]['Spoken_by'] . "!=" . $cspan_person_ary[$i]['Spoken_by'] . " \n";
	   					$prevMatch=$curMatch=$nextMatch=false;
	   					if($cur_db_inx==0 || $prev_person==null){
	   						//no need to check prev in db_inx
	   						$prevMatch=true;
	   						print "(no back check)";
	   					}else{
		   					if($db_person_ary[$j-1]['Spoken_by'] ==$prev_person){
								print "found prev match: $prev_person\n;";	
								$prevMatch=true;								
		   					}
	   					}
	   					if($db_person_ary[$j]['Spoken_by']==$cspan_person_ary[$i]['Spoken_by']){
							print "found cur match:". $cspan_person_ary[$i]['Spoken_by']."\n";
							$curMatch=true;
	   					}	   	
	   					if($next_person==null){
	   						//no need to check next in db_inx
	   						$nextMatch=true;
	   						print "(no next check)";
	   					}else{				
							if($db_person_ary[$j+1]['Spoken_by']==$next_person){
								print "found next match:".$next_person."\n";
								$nextMatch=true;
							}								
	   					}
	   					if($prevMatch && $curMatch && $nextMatch){
	   						print "\nFOUND TRIPILE on $j\n";
	   						$cur_db_inx=$j;
	   					}				   						   				
		   			}
		   			
		   			
		   			//while($cspan_person_ary[$i+$j]['Spoken_by']==$cspan_person_ary[$i]['Spoken_by']){
		   			//	$cspan_on_screen_time+=ntp2seconds($cspan_person_ary[$i]['length']);
		   			//	$j++;
		   			//}
		   			//if on screen more than 120 our proccess really should have tagged them:
		   			//if($cspan_on_screen_time>120){
		   				//
		   			//}
		   			
		   			/*
		   				
		   			}*/	
		   			//set the first valid flag back; 
		   		}else{
		   			print $cspan_person_ary[$i]['Spoken_by'] . " is not valid person\n";
		   		}
		   	}		   
		   	
		   	//$inx_cspan_person_ary = array_keys($g_row_matches);
		   	//$inx_row_person_ary = array_keys($g_person_time_ary);
		   	//for($i=0;$i<5;$i++){
		   		
		   	//}		   	
            //find match person1->person2
            
            
            //average switch time to get offset of stream
            //use offset to insert all $person_time_array data 
		}
	}
}
class MV_BaseScraper{
	function __construct(){
		$this->procArguments();
	}
	/*
	 * simple url cach using the mv_url_cache table
	 * 
	 * @@todo handle post vars
	 */
	function doRequest($url, $post_vars=array()){
		global $mvUrlCacheTable;
		$dbr = wfGetDB( DB_SLAVE );	
		$dbw = wfGetDB( DB_MASTER );
		//check the cache 
		//$sql = "SELECT * FROM `metavid`.`cache_time_url_text` WHERE `url` LIKE '$url'";	
		//select( $table, $vars, $conds='', $fname = 'Database::select', $options = array() )	
		$res = $dbr->select($mvUrlCacheTable, '*', array('url'=>$url), 'MV_BaseScraper::doRequest');
		//@@todo check date for experation
		if($res->numRows()==0){
			echo "do web request: " . $url . "\n";
			//get the content: 
			$page = file_get_contents($url);
			if($page===false){
				echo("error retriving $url retrying...\n");
				sleep(5);				
				return $this->doRequest($url);
			}
			if($page!=''){
				//insert back into the db: 
				//function insert( $table, $a, $fname = 'Database::insert', $options = array() )
				$dbw->insert($mvUrlCacheTable, array('url'=>$url, 'result'=>$page, 'req_time'=>time()));			
				return $page;
			}
		}else{			
			$row = $dbr->fetchObject( $res );
			return $row->result;			
		}
	}
}
//do procc args (now that classes are defined)
 proc_args();
?>
