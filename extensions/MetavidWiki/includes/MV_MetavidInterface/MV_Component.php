<?php
if ( !defined( 'MEDIAWIKI' ) )  die( 1 );
/*
 * Created on Jun 28, 2007
 *
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * the base component class
 */
 class MV_Component{
 	var $name = 'MV_Component';
 	var $mv_interface=null;
 	//default values: 
 	var $status='ok';
 	var $innerHTML ='';
 	var $js_eval=false;  	 	
 	var $req = '';
 	
 	function __construct($init=array()){
 		foreach($init as $k=>$v){
 			$this->$k=$v;
 		}
 	} 	
 	function getReqStreamName(){ 
 		if(isset($this->mv_interface->article))
 			return $this->mv_interface->article->mvTitle->getStreamName();
 		return null;
 	} 	
 	function setReq($req, $q=''){ 		
 		$this->req=$req; 	
 		if($q!=''){
 			$this->q=$q;
 		}
 	}
 	/* to be overwitten by class */
	function getHTML(){
		global $wgOut;
		$wgOut->addHTML( get_class($this) . ' component html');
	}
	function render_menu(){
		return get_class($this) . ' component menu';
	}
 	function render_full(){
 		global $wgOut;
 		//"<div >" .
 		$wgOut->addHTML("<fieldset id=\"".get_class($this)."\" >\n" .
 					"<legend id=\"mv_leg_".get_class($this)."\">".$this->render_menu()."</legend>\n"); 				
 		//do the implemented html 
 		$this->getHTML(); 
 		$wgOut->addHTML("</fieldset>\n");
	}
 }
?>
