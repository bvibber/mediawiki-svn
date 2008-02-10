t		<?php
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
	//swich on letter types:
	var $bill_types = array('H.J.RES.'=>'hj', 'H.R.'=>'h', 'H.RES.'=>'hr', 
							'S.'=>'s', 'S.CON.RES.'=>'sc', 'S.J.RES'=>'sj', 'S.RES.1'=>'sr');
							
	var $govTrack_bill_url ='http://www.govtrack.us/congress/bill.xpd?bill=';
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
		    //$mvd_res = MV_Index::getMVDInRange($stream->id, null, null, $mvd_type='ht_en',false,$smw_properties=array('Spoken_by'), '');		    
		    /*while ($row = $dbr->fetchObject($mvd_res)) {	   	   
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
		   	} */ 
		    
		    //get people from metavid table (and conform to mvd_res)
		    $sql = 'SELECT  (`people_time`.`time`-`streams`.`adj_start_time`) as `time`, 	
		    	   `person_lookup`.`name_clean`	 as `Spoken_by`   		
		    	   FROM  `metavid`.`people_attr_stream_time` as `people_time`
		    	   RIGHT JOIN `metavid`.`streams` as `streams` ON `streams`.`id`=`people_time`.`stream_fk`
		    	   LEFT JOIN `metavid`.`people` as `person_lookup` ON  `person_lookup`.`id` = `people_time`.`people_fk` 
		    	   WHERE `streams`.`name`=\''.$stream->name.'\' 		    	   		
		    	   ORDER BY `people_time`.`time` ';		    
			$people_res = $dbr->query($sql);			
			$cur_person = '';
			$curKey=0;
			while ($row = $dbr->fetchObject($people_res)) {
				if(!isset( $row->Spoken_by))continue;   			
				$cur_row_person = $row->Spoken_by;
				if($cur_person!=$cur_row_person){
					$db_person_ary[]=get_object_vars($row);					
					$curKey=count($db_person_ary)-1;
					$db_person_ary[$curKey]['Spoken_by']= str_replace(' ','_',$db_person_ary[$curKey]['Spoken_by']);
					$db_person_ary[$curKey]['start_time']=$row->time;
					$cur_person=$cur_row_person;
				}else{
					//update the end time: 
					$db_person_ary[$curKey]['end_time']=$row->time;
				}
			}			
		  	//list on screen times for everyone: 
		   	foreach($db_person_ary as $row){
		   		print $row['Spoken_by'] . ' on screen for '. ($row['end_time']-$row['start_time']) . "\n";
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
		   			$bill_categories=array();
		   			$rawpage = $this->doRequest($this->base_url . $pData['href']);
		   			//$rawpage = $this->doRequest('http://www.c-spanarchives.org/congress/?q=node/77531&id=8330447');
		   			
		   			preg_match('/<\/a><\/center><\/td><th><center>([^<]*)/', $rawpage, $title_matches);
		   			preg_match('/<table width="400">\n<tr><td>\n(.*)<\/tr><\/td>/',$rawpage, $page_matches);
		   			
		   			if(isset($title_matches[1]) && isset($page_matches[1])){
		   				$title = trim($title_matches[1]);
		   				$body = $page_matches[1];
		   				//print_r($page_matches);
		   			}else{
		   				die("error can't find title or body\n");
		   			}
		   			
		   			//do debate tag search:		   			
		   			preg_match('/<td colspan="2">Debate:\s<[^>]*>([^<]*)/', $rawpage, $debate_matches);		   			
		   			if(isset($debate_matches[1])){
		   				$bill_key = trim($debate_matches[1]);
		   				print "found debate: tag " .$bill_key."\n";
		   				$bill_categories[$bill_key]=$bill_key;	
		   				//build gov-track-congress-session friendly debate url:
						$this->get_and_proccess_govtrack_billid($bill_key,$stream->date_start_time);												
		   				  	   		
		   			}		   			
		   			$annotate_body ='';		   			
		   			$annotate_body.='Speach By: [[Speach by:='.str_replace('_',' ',$pData['Spoken_by']).']] ';
		   			//title fix hack for C-span error motion to procceed 
		   			//@@todo add in the rest of the motions:		   			
		   			if(strpos($title,'MOTION TO PROCEED')!==false){
		   				$title = str_replace('MOTION TO PROCEED','', $title);
		   				//$annotate_body.="[[Bill Motion:=MOTION TO PROCEED]]\n";
		   			}
		   			//fix title case
		   			$title = ucwords( strtolower($title));
		   			//don't cap a few words: '
		   			$title = str_replace(array('And','Or','Of'), array('and','or','of'), $title);
		   			
		   			//replace '' with ``
		   			$body = str_replace('\'\'', '``', $body);
		   			//replace bill names with [[Catgory:: bill name #]]		   		
		   			//$bill_pattern = '/(H\.R\.\s[0-9]+)/';
		   			$bill_pattern='/';
		   			$bill_pattern_ary=array();
		   			$or='';
		   			foreach($this->bill_types as $cspanT=>$govtrakT){
		   				$cspanT = str_replace('RES', '[\s]?RES', $cspanT);//sometimes spaces before res in text
		   				$cspanT = str_replace('CON', '[\s]?CON', $cspanT);//sometimes spaces before res in text
		   				$bill_pattern.=$or.'('.str_replace('.','\\.\s', $cspanT).'\s?[0-9]+)';
		   				$bill_pattern_ary[]='('.str_replace('.','\\.\s', $cspanT).'\s?[0-9]+)';
		   				$or='|';
		   			}
		   			$bill_pattern.='/i';//case insensative
		   			//$body='bla bla H.R. 3453 test S. 3494 some more text';
		   			//print "pattern:".$bill_pattern . "\n";
		   			preg_match_all($bill_pattern, $body, $bill_matches);
		   			//print_r($bill_matches);		   			
		   			if(isset($bill_matches[1])){
		   				foreach($bill_matches as$k=> $bill_type_ary){
		   					if($k!=0){
		   						if(isset($bill_type_ary[0])){		
		   							$bill_categories[$bill_type_ary[0]]=str_replace(' ','',$bill_type_ary[0]);
		   						}else if(isset($bill_type_ary[1])){		
		   							$bill_categories[$bill_type_ary[1]]=str_replace(' ','',$bill_type_ary[1]);
		   						}
		   					}
		   				}
		   			}		 	
		   					   			
		   			$body = preg_replace($bill_pattern_ary, '[[Mentions Bill:=$0]]', $body);	
		   			//print "BODY: $body";
		   			
		   			//source the doument:
		   			$body.="\n\n".'Source: [[Data Source Name:=C-SPAN Congressional Chronicle]] [[Data Source URL:='.$this->base_url . $pData['href'].']]'; 
		   				   					   			
		   			$body.="\n";
		   			//add the title to the top of the page: 
		   			$body="===$title===\n". $body;		   			
		   			$cspan_title_str = 'Thomas_en:'.$stream->name.'/'.
		   				seconds2ntp($pData['wiki_start_time']).'/'.
		   				seconds2ntp($pData['wiki_end_time']);		   				
		   			$cspanTitle=Title::makeTitle(MV_NS_MVD, ucfirst($cspan_title_str));
		   			//print "do edit ".$cspanTitle->getText()."\n";
		   			do_update_wiki_page($cspanTitle, $body);				   			
		   			//protect editing of the offical record (but allow moving for sync)	
		   			$cspanTitle->loadRestrictions();
					global $wgRestrictionTypes;
					foreach( $wgRestrictionTypes as $action ) {
						// Fixme: this form currently requires individual selections,
						// but the db allows multiples separated by commas.
						$mRestrictions[$action] = implode( '', $cspanTitle->getRestrictions( $action ) );
					}
					$article = new Article($cspanTitle);
					$mRestrictions['edit']['sysop']=true;
					$expiry = Block::infinity();
					$dbw = wfGetDb(DB_MASTER);
					$dbw->begin();
					$ok = $article->updateRestrictions( $mRestrictions,wfMsg('mv_source_material'), false, $expiry );					
					if($ok){
						print "updated permisions for ". $cspanTitle->getText()."\n";
		   				$dbw->commit();
					}else{
						print "failed to update restrictions\n";
					}
		   			
		   					   			
		   			//proccess each bill to the annotation body;
		   			$bcat=''; 
		   			$bill_lead_in ="\n\nBill ";
		   			foreach($bill_categories as $bill){
		   				if(trim($bill)!=''){
			   				$this->get_and_proccess_govtrack_billid($bill,$stream->date_start_time);		
			   				$annotate_body.=$bill_lead_in.'[[Bill:='.$this->cur_bill_short_title.']] ';
			   				$bill_lead_in=' , ';		   				   		
			   				$bcat.="[[Category:$bill]] ";
		   				}
		   			}
		   			if(trim($title)!=''){
		   				$annotate_body.="[[Category:$title]]\n";
		   			}
		   					   			
		   			$anno_title_str =  'Anno_en:'.$stream->name.'/'.
		   				seconds2ntp($pData['wiki_start_time']).'/'.
		   				seconds2ntp($pData['wiki_end_time']);
		   						   			
		   			$annoTitle =Title::makeTitle(MV_NS_MVD, ucfirst($anno_title_str));
		   			//print "do edit ".$annoTitle->getText()."\n";
		   			do_update_wiki_page($annoTitle, $annotate_body);
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
	/* converts c-span bill_id to gov_track bill id */
	function get_and_proccess_govtrack_billid($bill_key, $stream_date){
		global $MvBillTypes;
		//first get the year to detrim the house session: 
		$year =date('y', $stream_date);
		$session='';
		if($year=='01'||$year=='02'){$session='107';
		}else if($year=='03'||$year=='04'){$session='108';
		}else if($year=='06'||$year=='05'){$session='109';
		}else if($year=='07'||$year=='08'){$session='110';
		}else if($year=='09'||$year=='10'){$session='111';
		}else if($year=='11'||$year=='12'){$session='112';}
		$this->cur_session=$session;	
		foreach($this->bill_types as $cspanT=>$govtrakT){
			$bill_key = trim($bill_key);
			if(substr($bill_key, 0,strlen($cspanT))==$cspanT){
				$govTrackBillId= $govtrakT.$session.'-'.trim(substr($bill_key,strlen($cspanT)));
			}
		}
		if($govTrackBillId){
			$this->proccessGovTrackBill($govTrackBillId, $bill_key);	
			$this->govTrackBillId= $govTrackBillId;								
		}else{
			print 'error in getting govTrack bill id on: '. $bill_key . "\n";
			return null;
		}	
	}
	function proccessGovTrackBill($govTrackBillId, $bill_key){
		//get the bill title & its sponser / cosponsers: 
		$rawGovTrackPage = $this->doRequest($this->govTrack_bill_url . $govTrackBillId);
		//$rawGovTrackPage = $this->doRequest('http://www.govtrack.us/congress/bill.xpd?bill=h110-400');
									
		print "gov_track id: ". $govTrackBillId . " from: " . $this->govTrack_bill_url . $govTrackBillId. "\n";
		
		//get title: 
		
		preg_match('/property="dc:title" datatype="xsd:string" style="margin-bottom: 1em">([^<]*)<\/div><p style="margin-top: 1.75em; margin-bottom: 1.75em">([^<]*)/',$rawGovTrackPage, $title_match);
		if(isset($title_match[1])){
			$title_short  = str_replace('_',' ',$title_match[1]);
			$this->cur_bill_short_title=$title_short;
			$title_desc = $title_match[2];
		}else{
			die('could not get title for bill: ' . $govTrackBillId);
		}
		
		//print "raw govtrack:\n $rawGovTrackPage";
		//get the $thomas_match 
		preg_match('/thomas\.loc\.gov\/cgi-bin\/bdquery\/z\?(.*):/', $rawGovTrackPage, $thomas_match);								
		//get introduced: //strange .* does not seem to work :( 
		preg_match('/Introduced<\/nobr><\/td><td style="padding-left: 1em; font-size: 75%; color: #333333"><nobr>([^<]*)/m', $rawGovTrackPage, $date_intro_match);	
		//print_r($date_intro_match);						
		//get sponsor govtrack_id: 
		preg_match('/usbill:sponsor[^<]*<a href="person.xpd\?id=([^"]*)/', $rawGovTrackPage, $sponsor_match);			
		//lookup govtrack_id 
		//print_r($sponsor_match);
		if(isset($sponsor_match[1])){
			$sponsor_name = str_replace('_',' ',$this->get_wiki_name_from_govid($sponsor_match[1]));
		}
		//get cosponsor chunk:
		$scospon=strpos($rawGovTrackPage, 'Cosponsors [as of');
		$cochunk = substr($rawGovTrackPage, 
			$scospon,								 
			strpos($rawGovTrackPage, '<a href="/faq.xpd#cosponsors">')-$scospon);							
		preg_match_all('/person.xpd\?id=([^"]*)/',$cochunk,  $cosponsor_match);				
									
		$bp = "{{Bill|\n".
			'GovTrackID='.$govTrackBillId."|\n";
		if(isset($thomas_match[1]))$bp.='ThomasID='.$thomas_match[1]."|\n";
		if(isset($this->cur_session))$bp.='Session='.$this->cur_session."|\n";
		$bp.='Bill Key='.$bill_key."|\n";							
		if(isset($date_intro_match[1]))$bp.='Date Introduced='.$date_intro_match[1]."|\n";
		if($title_desc){
			$bp.='Title Description='.$title_desc."|\n";
		}													
		if($sponsor_name)$bp.='Sponsor='.$sponsor_name."|\n";
		
		if(isset($cosponsor_match[1])){
			foreach($cosponsor_match[1] as $k=>$govid){
				$cosponsor_name = $this->get_wiki_name_from_govid($govid);
				if($cosponsor_name){
					$bp.='Cosponsor '.($k+1).'='.$cosponsor_name."|\n";	
				}
			}	
		}							
		$bp.="}}\n";		
		//set up the base bill page:
		$wgBillTitle = Title::newFromText($title_short);
		do_update_wiki_page($wgBillTitle, $bp);
		//set up a redirect for the bill key, and a link for the category page: 
		$wgBillKeyTitle =Title::newFromText($bill_key);
		do_update_wiki_page($wgBillKeyTitle, '#REDIRECT [['.$title_short.']]');							
		//set up link on the category page:
		$wgCatPageTitle =Title::newFromText($bill_key, NS_CATEGORY);		
		do_update_wiki_page($wgCatPageTitle, 'Also see [[:'.$wgBillTitle->getText().']]');		
	}
	function get_wiki_name_from_govid($govID){
		if(!isset($this->govTrack_cache)){
			$sql = 'SELECT * FROM `smw_relations` WHERE `relation_title` = \'GovTrack_Person_ID\'';
			$dbr = wfGetDB( DB_SLAVE );	
			$res = $dbr->query($sql);
			while ($row = $dbr->fetchObject($res)) {
				$this->govTrack_cache[$row->object_title]=$row->subject_title;
			}
		}
		if(!isset($this->govTrack_cache[$govID])){
			$wgTitle = Title::newFromText('CongressVid:Missing_People');
			append_to_wiki_page($wgTitle, "Missing GovTrack person: [http://www.govtrack.us/congress/person.xpd?id=$govID $govID] \n\n");
			return false;	
		}
		return str_replace('_',' ',$this->govTrack_cache[$govID]);
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
