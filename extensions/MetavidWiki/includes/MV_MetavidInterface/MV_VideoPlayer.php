<?php
/*
 * Created on Jun 28, 2007
 *
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 */
 /*
  * stores all the html for the video player and its associated ajax functions
  */
  if ( !defined( 'MEDIAWIKI' ) )  die( 1 );
 class MV_VideoPlayer extends MV_Component{
 	var $name = 'MV_VideoPlayer'; 
 	 	
 	
 	function getHTML(){
 		global $wgOut; 	
 		if($this->getReqStreamName()!=null){
 			$wgOut->addHTML($this->embed_html());
 		}else{
 			$wgOut->addHTML('no stream selected');
 		}
	}
	function embed_html(){		
		global $mvDefaultVideoPlaybackRes;
		$out='';			
		$cur_stream =  & mvGetMVStream( $this->getReqStreamName() );
		//give the stream the request information:
		$cur_stream->mvTitle= & $this->mv_interface->article->mvTitle;
		//check if media is availible: 
		$stream_web_url = $cur_stream->getWebStreamURL();		
		if($stream_web_url){		 
			$out='<span id="mv_videoPlayerTime">'.$cur_stream->mvTitle->getStartTime().' to '.
				$cur_stream->mvTitle->getEndTime() . 
				'</span>';
			$out.="<video id=\"embed_vid\" thumbnail=\"".$cur_stream->getStreamImageURL() ."\"". 
			 	" controls=\"true\" embed_link=\"true\" src=\"".$stream_web_url."\"></video>";
		}else{
			$out.=wfMsg('mv_error_stream_missing');
		}
		return $out;
	}
	function render_menu(){
		return 'embed video';
	}
 }
?>
