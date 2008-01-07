<?php
/*
 * Created on Jun 28, 2007
 *
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * 
 * The metavid interface class
 * provides the metavid interface for Metavid: requests
 * provides the base metadata
 * 
 */
 if ( !defined( 'MEDIAWIKI' ) )  die( 1 );

 
 class MV_MetavidInterface{
 	var $components = array();
 	var $context=null;
 	var $page_title='';
 	var $page_header='';
 	//default layout: 
 	var $cpLayout = array(
 		'MV_VideoPlayer'=>'position:absolute;width:322px;top:30px;height:270px;left:10px;margin:4px;',
 		'MV_Overlay'=>'position:absolute;left:365px;right:10px;top:30px;bottom:10px;margin:4px;',
 		'MV_Tools'=>'position:absolute;width:322px;top:312px;left:10px;bottom:10px;margin:4px;',
 		'MV_StreamMeta'=>'position:absolute;width:322px;top:307px;left:10px;bottom:10px;margin:4px;',
 		
 		'MV_SequencePlayer'=>'position:absolute;width:322px;top:25px;bottom:195px;left:10px;margin:4px;',
 		'MV_SequenceTools'=>'position:absolute;left:365px;right:10px;top:25px;bottom:195px;margin:4px;',
 		'MV_SequenceTimeline'=>'position:absolute;left:10px;right:10px;height:174px;bottom:0px;margin:4px;',
 	);
 	function __construct($contextType, & $contextArticle=null ){
 		global $mv_default_view;
 		$this->context = $contextType;
 		if($contextArticle)
 			$this->article = & $contextArticle; 
		//set up base layout for each context:		
 		switch($contextType){
 			case 'special':
 				$this->setupSpecialView();
 			break;
 			case 'edit_sequence':
 				$this->setupEditSequenceView();
 			break;
 			case 'sequence': 				
 				$this->setupSequenceView();
 			break;
 			case 'stream': 				
 				$this->setupStreamView();
 			break; 		
 		}  	
 	}
 	/*function setupSequenceView(){
 		global $mvgIP;
 		//set up the base sequence: 
 		foreach(array('MV_VideoPlayer', ''
 		/*foreach(array('MV_VideoPlayer') as $cp_name){
			require_once($mvgIP . '/includes/MV_MetavidInterface/'.$cp_name.'.php');
			$this->components[$cp_name] = new $cp_name( 
				array('mv_interface'=>&$this)
			);				
		}
 	}*/
 	function setupEditSequenceView(){
 		global $mvgIP, $wgTitle;
 		foreach(array('MV_SequencePlayer', 'MV_SequenceTools', 'MV_SequenceTimeline') as $cp_name){			
			$this->components[$cp_name] = new $cp_name( 
				array('mv_interface'=>&$this)
			);				
		}
		//set up additonal pieces
		$this->page_title=wfMsg('mv_edit_sequence', $wgTitle->getText() );		
 	}
 	/*function setupSpecialView(){
 		global $mvgIP;
 		foreach(array('MV_VideoPlayer', 'MV_Overlay','MV_Tools') as $cp_name){
			require_once($mvgIP . '/includes/MV_MetavidInterface/'.$cp_name.'.php');
			$this->components[$cp_name] = new $cp_name( 
				array('mv_interface'=>&$this)
			);				
		}
		$this->page_title ='<span style="position:relative;top:-12px;font-weight:bold">'.
 			$this->article->mvTitle->getStreamNameText(). ' <span id="mv_stream_time">'.
 			$this->article->mvTitle->getTimeDesc() . '</span>'.
		'</span>';
		//set conditions
		$this->components['MV_Overlay']->setReq('Recentchanges');
 	}*/
 	function setupStreamView(){
 		global $mvgIP, $mvDefaultStreamViewLength, $wgOut; 		
 		//add in full title var: 
 		$wgOut->addScript("<script type= \"text/javascript\">".'/*<![CDATA[*/'." 		
 		var mvTitle = '{$this->article->mvTitle->getWikiTitle()}'; \n".
 		'/*]]>*/</script>'."\n");
 	
 		//set defaults if null
 		$this->article->mvTitle->setStartEndIfEmpty(
 			seconds2ntp(0), 
 			seconds2ntp($mvDefaultStreamViewLength));
 		
		//set up the interface objects:
		foreach(array('MV_VideoPlayer', 'MV_Overlay','MV_Tools') as $cp_name){
			$this->components[$cp_name] = new $cp_name( 
				array('mv_interface'=>&$this)
			);				
		}		
		//also add prev next pagging	 		
		$this->page_header ='<span style="position:relative;top:-12px;font-weight:bold">'.
 			$this->article->mvTitle->getStreamNameText().
 			$this->components['MV_Tools']->stream_paging_links('prev') . 
			' <span id="mv_stream_time">'.$this->article->mvTitle->getTimeDesc() . '</span>'.
			$this->components['MV_Tools']->stream_paging_links('next') . 			
		'</span>';	

		
		$this->page_title = $this->article->mvTitle->getStreamNameText().' '.$this->article->mvTitle->getTimeDesc();
 	}
 	/*renders style position based on default layout */
 	function getStylePos($cp_name){
 		if(isset($this->cpLayout[$cp_name]))return $this->cpLayout[$cp_name];
 		return '';
 	}
 	/*
 	 * renders the full page  to the wgOut object
 	 */
 	function render_full(){
 		global $wgOut; 	
 		//add some variables for the interface: 	 		
 		
 		//output title and header: 		
 		$wgOut->setHTMLTitle($this->page_title);
 		if($this->page_header=='')$this->page_header = '<span style="position:relative;top:-12px;font-weight:bold">' . 
 			$this->page_title . '</span>';
 		$wgOut->addHTML($this->page_header);
 		
 		
 		//output the time range
 		//output basic steam info in the 
 		//$out.='<div id="mv_base_container" style="position:absolute;border:solid;border-color:red;width:100%;height:100%">';
 		foreach($this->components as $cpKey => &$component){ 			
 			$component->render_full();
 		}
 		//for now output spacers 
		//@@todo output a dynamic spacer javascript layout		
		$out='';  	
 		for($i=0;$i<28;$i++)$out.="<br>";
 		$wgOut->addHTML($out); 		 		
 	} 	
 }
?>
