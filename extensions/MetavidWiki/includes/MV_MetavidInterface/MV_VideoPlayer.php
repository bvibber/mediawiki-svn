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
		//give the stream the request information:
		$mvTitle= & $this->mv_interface->article->mvTitle;
		//check if media is availible: 	
		$mvTitle->dispVideoPlayerTime=true;			
		return $mvTitle->getEmbedVideoHtml('embed_vid');
	}
	function render_menu(){
		return 'embed video';
	}
 }
?>
