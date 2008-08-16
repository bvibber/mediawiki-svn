<?php
/*
 * Created on Aug 15, 2008
 *
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 */
 class MV_Navigator extends MV_Component{
 	function getHTML(){
 		//get all annotative layers
 		return wfMsg()
 	}
 	function render_full(){
 		$wgOut->addHTML('<div id="MV_Navigator">');
 			$this->getHTML(); 
 		$wgOut->addHTML('</div>');
 	}
 }
?>