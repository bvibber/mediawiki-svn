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



class MV_CspanScraper extends MV_BaseScraper{	
	var $base_url = 'http://www.c-spanarchives.org/congress/';
	var $base_query = '?q=node/69850';
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
			$cspan_url = $this->base_url .$this->base_query .  '&date='.$date_req.'&hors='.$hors;
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
		  	//list on screen times for everyone: 
		   	foreach($g_row_matches as $row){
		   		//print $row['Spoken_by'] . ' on screen for '. ($row['end_time']-$row['start_time']) . "\n";
		   		$db_person_ary[]=$row;
		   	}
		   	
			//print_r($db_person_ary);
			//die;
	
		   	//count($cspan_person_ary)	
		   	$cur_db_inx=0;	   	
		   	$cur_person=null;	
		   	$fistValid=true;	   		 
		   	for($i=0;$i<count($cspan_person_ary);$i++){
		   		//print "looking at: ". $cspan_person_ary[$i]['Spoken_by'] . "\n";
		   		//print "\tCSPAN: ". $cspan_person_ary[$i]['Spoken_by'] . ' on screen for '. $cspan_person_ary[$i]['length'].' or:'.ntp2seconds($cspan_person_ary[$i]['length']). "\n";
		   		//set up cur, the next and prev pointers: 
		   		$cur_person = $cspan_person_ary[$i]['Spoken_by'];
		   		
	   			//make sure next is not the same as current: 
	   			//note: we don't group above since the same person can give two subsequent different speeches 
	   			$next_person=$cur_person;
	   			$k_person_inx=1;
	   			$person_insert_set = array();
	   			while($next_person==$cur_person){
	   				if(isset($cspan_person_ary[$i+$k_person_inx])){
		   				$potential_next_person = (mv_is_valid_person($cspan_person_ary[$i+$k_person_inx]['Spoken_by']))?
		   					$cspan_person_ary[$i+$k_person_inx]['Spoken_by']:null;
						if($potential_next_person==null && $k_person_inx==1){
							$next_person=null;
							break;
						}else if($potential_next_person!=null){
							$next_person=$potential_next_person;
						}				   				
		   				$k_person_inx++;		   				
	   				}else{
	   					$next_person=null;
	   				}
	   			}		   			
	   			//should be no need to make sure prev is not the same as current (as we do greedy look ahead below) 
	   			//$prev_person = $cur_person;
	   			//$k=1;
	   			//while($prev_person==$cur_person){
	   				if(isset($cspan_person_ary[$i-1])){		   		
			   			$prev_person = (mv_is_valid_person($cspan_person_ary[$i-1]['Spoken_by']))?
			   				$cspan_person_ary[$i-1]['Spoken_by']:null;	
			   		}else{
	   					$prev_person=null;
	   				}		
	   			//}		   			   			 		   	

		   		if(mv_is_valid_person($cspan_person_ary[$i]['Spoken_by'])){		   					   		
		   			//print "\tis valid person looking for db sync\n";
		   			//print "\t prev: $prev_person cur: $cur_person next: $next_person\n";
		   			if($prev_person==null && $next_person==null){
		   				print "error both prev and next are null skiping person\n";		   			
		   				continue;
		   			}
		   			
		   			//check how long they where on screen (also check subquent)
		   			$cspan_on_screen_time=ntp2seconds($cspan_person_ary[$i]['length']);
		   					   		
		   			//print "NOW STARTING AT: $cur_db_inx of " . count($db_person_ary) . "\n";
		   			for($j=$cur_db_inx;$j<count($db_person_ary);$j++){				   				
						//print "searchig db on: " . $db_person_ary[$j]['Spoken_by'] . "!=" . $cspan_person_ary[$i]['Spoken_by'] . " \n";
	   					$prevMatch=$curMatch=$nextMatch=false;
	   					if($cur_db_inx==0 || $prev_person==null){
	   						//no need to check prev in db_inx
	   						$prevMatch=true;
	   					//	print "(no back check)";
	   					}else{
		   					if($db_person_ary[$j-1]['Spoken_by'] ==$prev_person){
							//	print "found prev match: $prev_person\n;";	
								$prevMatch=true;								
		   					}
	   					}
	   					if($db_person_ary[$j]['Spoken_by']==$cspan_person_ary[$i]['Spoken_by']){
						//	print "found cur match:". $cspan_person_ary[$i]['Spoken_by']."\n";
							$curMatch=true;
	   					}	   	
	   					if($next_person==null){
	   						//no need to check next in db_inx
	   						$nextMatch=true;
	   					//	print "(no next check)";
	   					}else{				
							if($db_person_ary[$j+1]['Spoken_by']==$next_person){
							//	print "found next match:".$next_person."\n";
								$nextMatch=true;
							}								
	   					}
	   					//if we have a match set do insert proc:
	   					if($prevMatch && $curMatch && $nextMatch){
	   						//print "FOUND Match on $j\n";
	   						//print "\t prev: $prev_person cur: $cur_person next: $next_person\n";
	   						$cur_db_inx=$j;
	   						//add all additional info we can from c-span: 
	   						//also push forward for all of current (we should always hit the first series of the same person first )
	   						$k=0;	   						
	   						//build insert set:
	   						$cur_start_time = $db_person_ary[$j]['start_time'];
	   						while($cur_person==$cspan_person_ary[$i+$k]['Spoken_by']){	   							   							  						
	   							//use the last cspan_person for start case	   							
	   							$cspan_person_ary[$i+$k]['wiki_start_time']=$cur_start_time;
		   						if(ntp2seconds($cspan_person_ary[$i+$k]['length']) > 
	   									$db_person_ary[$j]['end_time']-$cur_start_time){
		   								$cspan_person_ary[$i+$k]['wiki_end_time'] =$db_person_ary[$j]['end_time'];
		   								//already used up our db_person_ary continue: 
		   								print "a cspan insert sync " . 
											' '. $cspan_person_ary[$i+$k]['wiki_start_time']. " to " .
											$cspan_person_ary[$i+$k]['wiki_end_time']. " of " . 
											$db_person_ary[$j]['end_time'] . " for: " .
											$cspan_person_ary[$i]['Spoken_by'] . "\n";	
		   								break;
	   							}else{
	   									$cspan_person_ary[$i+$k]['wiki_end_time'] =$cur_start_time+
	   												ntp2seconds($cspan_person_ary[$i+$k]['length']);
	   									//print "add " . ntp2seconds($cspan_person_ary[$i+$k]['length']) . "\n";
	   									$cur_start_time+=ntp2seconds($cspan_person_ary[$i+$k]['length']);
	   							} 	 
	   							print "p cspan insert sync " . 
										' '. $cspan_person_ary[$i+$k]['wiki_start_time']. " to " .
										$cspan_person_ary[$i+$k]['wiki_end_time']. " of " . 
										$db_person_ary[$j]['end_time'] . " for: " .
											$cspan_person_ary[$i]['Spoken_by'] . "\n";	  			
	   							//print_r($db_person_ary[$j]);
	   							//print_r($cspan_person_ary[$i+$k]);									
	   							$k++;
	   							if(!isset($cspan_person_ary[$i+$k]))break;
	   						}	   	
	   						$k--;
	   						//extend the last property if within 100 seconds
	   						if(abs($cspan_person_ary[$i+$k]['wiki_end_time']-$db_person_ary[$j]['end_time'])<100){
	   							$cspan_person_ary[$i+$k]['wiki_end_time']=$db_person_ary[$j]['end_time'];	   			
	   							print "updated cspan insert for: ". $cspan_person_ary[$i]['Spoken_by'] . 
										' '. $cspan_person_ary[$i+$k]['wiki_start_time']. " to " .
										$cspan_person_ary[$i+$k]['wiki_end_time']. " of " . 
										$db_person_ary[$j]['end_time'] . "\n";
	   						}	 
	   						$k++;
//	   						/die;
	   						//move the index to the current: 
	   						$i=$i+$k;
	   						continue;
	   					}				   						   				
		   			}		    
		   		}else{
		   			//print $cspan_person_ary[$i]['Spoken_by'] . " is not valid person\n";
		   		}		   		
		   	}	
		   	print "Get Additonal C-SPAN Data For \"synced\" Data:\n";
		   	foreach($cspan_person_ary as $pData){
		   		if(isset($pData['wiki_start_time'])){
		   			//print_r($pData);
		   			print $this->base_url . $pData['href'] ."\n";
		   			$rawpage = $this->doRequest($this->base_url . $pData['href']) . "\n";
		   			preg_match('/<\/a><\/center><\/td><th><center>([^<]*)/', $rawpage, $title_matches);
		   			preg_match('/<table width="400">\n<tr><td>\n(.*)<\/tr><\/td>/',$rawpage, $page_matches);
		   			if(isset($title_matches[1]) && isset($page_matches[1])){
		   				$title = $title_matches[1];
		   				$body = $page_matches[1];
		   				//print_r($page_matches);
		   			}else{
		   				die("error can't find title or body\n");
		   			}
		   			//replace '' with ``
		   			$body = str_replace('\'\'', '``', $body);
		   			//replace H.R # with [[Catgory:: H.R #]]
		   			$bill_pattern = '/(H\.R\.\s[0-9]+)/';
		   			preg_match_all($bill_pattern, $body, $bill_matches);
		   			$bill_categories=array();
		   			if(isset($bill_matches[1])){
		   				foreach($bill_matches[1] as $bill_name){		   					
		   					$bill_categories[$bill_name]=$bill_name;
		   				}
		   			}		 
		   			$body = preg_replace($bill_pattern, '[[Mentions Bill:=$1]]', $body);
		   			$body.="\n\n";
		   			//title fix hack for C-span error motion to procceed 
		   			//@@todo add in the rest of the motions:		   			
		   			if(strpos($title,'MOTION TO PROCEED')!==false){
		   				$title = str_replace('MOTION TO PROCEED','', $title);
		   				$body.="[[Bill Motion:=MOTION TO PROCEED]]\n";
		   			}
		   			$body="===$title===\n". $body;
		   			$body.="[[Category:$title]]\n";
		   			
		   			foreach($bill_categories as $bill){
		   				$body.="[[Category:$bill]] ";
		   			}
		   			$body.='[[Speach by:='.$pData['Spoken_by'].'| ]] ';
		   			$title_str = 'thomas_en:'.$stream->name.'/'.
		   				seconds2ntp($pData['wiki_start_time']).'/'.
		   				seconds2ntp($pData['wiki_end_time']);
		   						   				
		   			$cspanTitle=Title::makeTitle(MV_NS_MVD, ucfirst($title_str));
		   			 
		   			//add title category
		   			//print "do insert ".$cspanTitle->getText()." \n";
		   			do_update_wiki_page($cspanTitle, $body);		   				
		   			//[Page: S14580] replaced with:  [[Category:BillName]]
		   			//would be good to link into the official record for "pages"
		   			
		   			//[[speach by:=name]]
		   			//[[category:=title]]
		   			
		   			//for documentation: 
		   			//semantic qualities would be Aruging For:billX or Arguging Agaist billY
		   			
		   			//these pages are non-editable 
		   			//maybe put the category info into annotations layer? (since it applies to both?)
		   			
		   			
		   			//do new page mvd:or_
		   			//die;
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
