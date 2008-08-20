<?php
/*
 * Created on Aug 15, 2008
 *
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 */
 class MV_Navigator extends MV_Component{
 	function getHTML(){
 		global $wgUser;
 		$o='';
 		$sk = $wgUser->getSkin();
 		$dbr =& wfGetDB(DB_SLAVE);
 		//get all annotative layers
 		$stream_id = $this->mv_interface->article->mvTitle->getStreamId();
 		$stream_name = $this->mv_interface->article->mvTitle->getStreamName();
 		$stream_time_req = $this->mv_interface->article->mvTitle->getTimeRequest();
 		$start_sec = $this->mv_interface->article->mvTitle->getStartTimeSeconds(); 
 		$duration_sec = $this->mv_interface->article->mvTitle->getDuration();
		$end_sec   = $this->mv_interface->article->mvTitle->getEndTimeSeconds(); 	
		//print "start $start_sec end:$end_sec \n ";		
		foreach(array('prev','next') as $pntype){
			if($o!='')$o.=' ';
			if($pntype=='prev'){
				if($start_sec==0)
					continue;
				$qstart = 0;
				$qend = $start_sec;								
				$orderby = 'end_time ASC';
			}else if($pntype=='next'){			
				$qstart = $end_sec+1;				
				$qend = $duration_sec;
				$orderby = 'start_time ASC';
			}
			//print "Qstart looking for $pntype:$qstart:  ".seconds2ntp($qstart) ." Qend:$qend : " . seconds2ntp($qend) . " \n";
	 		$mvd_rows = MV_Index::getMVDInRange(
	 								$stream_id, 
	 								$qstart, 
	 								$qend,
	 								$mvd_type='anno_en',
	 								$getText=false,
	 								$smw_properties=array('speech_by','bill','category'), 
	 								$options=array('LIMIT'=>1, 'ORDER BY'=>$orderby)	 							
	 						);	 
	 		//print "SHOULD GET $pntype for $stream_time_req";				
	 		reset($mvd_rows);
	 		if(count($mvd_rows)!=0){
	 			$row = current($mvd_rows);
	 			//$prev_end = $row->end_time;	 		
	 			$stime_req = seconds2ntp($row->start_time) . '/' . seconds2ntp($row->end_time);
	 			$streamTitle = Title::newFromText($stream_name .'/'. $stime_req, MV_NS_STREAM);
	 			$tool_tip ='';
	 			//print_r($row);		 			 	
	 			if(trim($row->speech_by)!=''){	 			
	 				$o.=wfMsg('mv_'.$pntype.'_speech', $sk->makeKnownLinkObj($streamTitle, $row->speech_by));
	 				$tool_tip.=	 'Speech By: '. $row->speech_by;	
	 			}else if(trim($row->bill)!=''){
	 				$o.=wfMsg('mv_'.$pntype.'_bill', $sk->makeKnownLinkObj($streamTitle, $row->bill));			 			
	 			}else if(count($row->category)!=0){
	 				$first_cat =  current($row->category);
	 				$o.=wfMsg('mv_'.$pntype.'_cat',  $sk->makeKnownLinkObj($streamTitle, $first_cat));
	 			}
	 		}	 		
		}
		
 		return $o;
 	}
 	function render_full(){
 		global $wgOut;
 		$wgOut->addHTML('<div id="MV_Navigator">');
 			$wgOut->addHTML($this->getHTML()); 
 		$wgOut->addHTML('</div>');
 	}
 }
?>