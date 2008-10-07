<?php
/* hanndles acutal output of special stats page */
if ( !defined( 'MEDIAWIKI' ) ) die();

class SpecialPlayerStatsGrabber extends SpecialPage {
	var $action='';
        function SpecialPlayerStatsGrabber() {
                SpecialPage::SpecialPage("PlayerStatsGrabber");
                wfLoadExtensionMessages('PlayerStatsGrabber');
        }
        //used for page title
 		function getDescription(){
 			switch($this->req_param){
 				case 'Survey':
 					return wfMsg('ps_take_video_survey');
 				break; 				
 				case '':
 				default:
 					return wfMsg('playerstatsgrabber'); 				
 				break;
 			}
 		}
        function execute( $par ) {
                global $wgRequest, $wgOut;                       
                $this->req_param = $par;   
                 print $wgRequest->getText('param');                          
                //set the header: 
                $this->setHeaders();
               
                //do the page: 
                switch($this->req_param){                	
                	case 'Survey':
                		//check if 
                		$this->do_survey_forum();
                	break;
                	case '':default:
                		$this->do_stats_page();
                	break;
                }                                                             
        }
        function do_stats_page(){
			global $wgOut, $wgRequest;			
        	$wgOut->addHTML( " $this->req_param stats output will go here, with a link to a full report once its available");   
        }
        function do_survey_forum(){
        	global $wgOut, $psEmbedAry, $wgTitle;
        	$wgOut->addHTML ( wfMsg('ps_survey_description'));
        	
        	//select the embed ary element:
        	$tw=0; 
        	foreach($psEmbedAry as $embed){
        		$tw+=$embed['weight'];
        	}
        	$selected_val = rand(0, $tw);
        	foreach($psEmbedAry as $embed){
        		$tw+=$embed['weight'];
        		if($tw>=$selected_val){
        			break;
        		}
        	}
        	$embed_code='';
        	if(isset($embed['html_code'])){
        		$embed_code=$embed['html_code'];
        	}else if(isset($embed['wiki_code'])){
        		$embed_code=$embed['wiki_code'];
        	}
        	//$q = 'action='.$this->action;
			#if ( "no" == $redirect ) { $q .= "&redirect=no"; }
			$action = $wgTitle->escapeLocalURL( $q );
        	//work with "embed"
        	//output table with flash and or video embed:
        	$wgOut->addHTML( <<<EOT
<table>
<tr>
	<td>
	</td>
	<td>
	<form id="editform" name="editform" method="post" action="$action" enctype="multipart/form-data">
		form goes here		
	</form>
	</td>
</tr>
</table>

EOT
		);
        	$o='';
        		$o.='<td>'.$embed_code.'</td>';
        		$o.='<td>';
        			
        		$o.='</td>';
        	$o.='</tr></table>';
        }
}


?>