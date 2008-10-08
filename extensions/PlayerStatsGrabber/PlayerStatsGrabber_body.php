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
                		if($wgRequest->getVal('action')=='submit'){
                			$this->do_submit();
                		}else{
                			$this->do_stats_page();
                		}
                	break;
                }                                                             
        }
        function do_stats_page(){
			global $wgOut, $wgRequest;			
        	$wgOut->addHTML( " $this->req_param stats output will go here, with a link to a full report once its available");   
        } 
        function do_survey_forum(){
        	global $wgOut, $psEmbedAry, $wgTitle, $wgUser,$wgEnableParserCache, $wgParser, $wgScript;
        	$wgOut->addHTML ( wfMsg('ps_survey_description'));
        	
        	//select the embed ary element:
        	$tw=0; 
        	foreach($psEmbedAry as $embed){
        		$tw+=$embed['weight'];
        	}
        	$selected_val = rand(1, $tw);
        	$tw=0;    
        	foreach($psEmbedAry as $embed){
        		$tw+=$embed['weight'];
        		if($tw>=$selected_val){
        			break;
        		}
        	}
        	$embed_code='';
        	if(isset($embed['html_code'])){
        		//run the stats (if not internal oggPlay)
        		$this->runJS_Stats();        	
        		$embed_code=$embed['html_code'];
        	}else if(isset($embed['wiki_code'])){      
        		$popts = new ParserOptions;
        		$parserOutput = $wgParser->parse( $embed['wiki_code'], $wgTitle, $popts);
        		$embed_code = $parserOutput->getText();        		
        	}
        	//print "EMBED C: $embed_code \n";
        	//$q = 'action='.$this->action;
			#if ( "no" == $redirect ) { $q .= "&redirect=no"; }
			//$action = $wgScript.'?title='.$wgTitle->getDBKey() . '/Thanks';
			$action = $wgTitle->getLocalURL('action=submit');
			$jsUserHash = sha1( $wgUser->getName() . $wgProxyKey);
			$enUserHash = Xml::encodeJsVar( $jsUserHash );
        	//work with "embed"
        	
			
        	//output table with flash and or video embed:
        	$wgOut->addHTML( <<<EOT
<p>
<table>
<tr>
	<td valign="top">
		$embed_code
	</td>	
	<td valign="top">
		<form id="ps_editform" name="ps_editform" method="post" action="{$action}" enctype="multipart/form-data">	
			<input type="hidden" name="uh" value={$enUserHash}>
EOT
	);
	//output questions:
	$wgOut->addHTML( wfMsg('ps_could_play') );
	//yes no with expand 
	$wgOut->addHTML( '<br><input type="radio" name="ps_could_play"  value="yes"
				onclick="document.getElementById(\'ps_could_not_play\').style.display = \'none\';">'.
				wfMsg('ps_play_yes') .'<br> 
				<input type="radio" name="ps_could_play"  value="no" 
				onclick="document.getElementById(\'ps_could_not_play\').style.display = \'inline\';">'.
				wfMsg('ps_play_no').'<p>
	<div id="ps_could_not_play" style="display:none;">
		'.wfMsg('ps_problem_checkoff').'
		 <table border="0" cellspacing="0" cellpadding="0">
           	<tr>
           	  <td><input type="checkbox" name="ps_no_video" value="1"></td>
              <td>'.wfMsg('ps_no_video').' </td>             
            </tr>
            <tr>
            <tr>
              <td><input type="checkbox" name="ps_jumpy_playback" value="1"></td>
              <td>'.wfMsg('ps_jumpy_playback').' </td>              
            </tr>
            <tr> 
              <td><input type="checkbox" name="ps_bad_sync" value="1"></td>
              <td>'.wfMsg('ps_bad_sync').' </td>             
            </tr>           
			<tr>
			  <td><input type="checkbox" name="ps_no_sound" value="1"></td>
              <td>'.wfMsg('ps_no_sound').'</td>              
            </tr>
          </table>		
          '.wfMsg('ps_problems_desc').'<br><textarea name="ps_problems_desc" rows="2" cols="40" MAXLENGTH="300"></textarea><br>        
	</div>
	'.wfMsg('ps_would_install') . '<br>'.
	'<input type="radio" name="ps_would_install"  value="yes">'.wfMsg('ps_yes_install').'<br>'.
	'<input type="radio" name="ps_would_install"  value="no">'.wfMsg('ps_no_install').'<br>'
); 	 
	//if ie output switch check: 
	if (preg_match('|MSIE ([0-9].[0-9]{1,2})|',$_SERVER['HTTP_USER_AGENT'],$matched)) {
		$wgOut->addHTML( '<br>'.
		wfMsg('ps_would_switch') .'<br>'.
		 '<input type="radio" name="ps_would_switch"  value="yes">'.wfMsg('ps_yes_switch').'<br>'.
		 '<input type="radio" name="ps_would_switch"  value="no">'.wfMsg('ps_no_install').'<br>'
		);		
	}	
	$wgOut->addHTML( '<br>'.	wfMsg('ps_your_email').'<br>'. 
		'<input type="text" name="ps_your_email"  size="30" maxlength="200"><p>'.
		wfMsg('ps_privacy'). '<br>'.
		'<input type="submit" value="'.wfMsg('ps_submit_survey').'">'		
	);
	
	$wgOut->addHTML('
		</form>
	</td>
</tr>
</table>');        	
        }
       function do_submit(){
        	global $wgRequest;
        	print_r($wgRequest);
        	//print "NO VIDEO: "
        	$insAry = array(        	            
            'user_hash'			=> $wgRequest->getVal('uh'),
        	
            'ps_could_play'		=> ( $wgRequest->getVal('ps_could_play')=='' )?0:1,
        	'ps_jumpy_playback' => ( $wgRequest->getVal('ps_jumpy_playback')=='' )?0:1,
            'ps_no_video' 		=> ( $wgRequest->getVal('ps_no_video')=='' )?0:1,
            'ps_bad_sync' 		=> ( $wgRequest->getVal('ps_bad_sync')=='' )?0:1,
            'ps_no_sound' 		=> ( $wgRequest->getVal('ps_no_sound')=='' )?0:1,        	
        	        	
            'ps_would_install' 	=> ( $wgRequest->getVal('ps_would_install')=='no' )?0:1,        	
        	
        	'ps_your_email'		=> htmlspecialchars( $wgRequest->getVal('ps_your_email' ) ),
        	'ps_problems_desc'	=> htmlspecialchars( $wgRequest->getVal('ps_problems_desc' ) )
        	);
        	if( $wgRequest->getVal('ps_would_switch')!='')
        		$insAry['ps_would_switch'] = ( $wgRequest->getVal('ps_would_switch')=='yes')?1:0;
        		
        	print_r($insAry);
        }
        /* to run the stats in cases where we are not using oggHanndler to play*/
        function runJS_Stats(){   
        	global $wgOut, $wgScriptPath;
        	$scriptPath = OggHandler::getMyScriptPath();
        	//include the javascript and do the stats       
        	$wgOut->addHTML( <<<EOT
        	<script type="text/javascript" src="$scriptPath/OggPlayer.js"></script>
        	<script type="text/javascript" src="$wgScriptPath/extensions/PlayerStatsGrabber/playerStats.js"></script>
        	<script type="text/javascript">
        		wgOggPlayer.doStats();
        	</script>
EOT
);
        }
}

?>