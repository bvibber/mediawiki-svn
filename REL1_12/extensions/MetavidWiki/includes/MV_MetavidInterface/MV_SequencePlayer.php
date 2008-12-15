<?php
/*
 * MV_SequencePlayer.php Created on Nov 2, 2007
 * 
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 */
 if ( !defined( 'MEDIAWIKI' ) )  die( 1 );
 //make sure the parent class mv_component is included
 
 class MV_SequencePlayer extends MV_Component{
 	function render_menu(){
		return wfMsg('mv_sequence_player_title');
	}
	function getHTML(){
 		global $wgOut;
 		$article = & $this->mv_interface->article;
 		//@@todo look at mv_interface context to get what to display in tool box:
 		$wgOut->addHTML(''. 
 		'<div style="position:absolute;width:320px;height:270px;" id="mv_video_container"></div>' ."\n".
 				'<div style="display:none;" id="mv_inline_pl_txt">'.$article->getSequenceText().'</div>' );
	}
 }
?>
