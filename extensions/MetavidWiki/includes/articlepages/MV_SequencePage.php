<?php
/*
 * MV_SequencePage.php Created on Oct 17, 2007
 * 
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 * 
 * redirects the user to the sequence interface. 
 */
 //sequence just adds some sequence hooks: 
 
define('SEQUENCE_TAG', 'sequence');
 
//valid playlist in-line-attributes

 
 class MV_SequencePage extends Article{
 	function __construct($title){ 
 		mvfAddHTMLHeader('sequence');
 		parent::__construct($title);
 		return $this;
 	}
	 function doSeqReplace(&$input, &$argv, &$parser){	
		global $mv_video_attr, $wgOut, $markerList;
		
		$mv_pl_inline_attr = array('wClip', 'mvClip', 'title','linkback','desc','desc','image');
		//check for external embed: 	
		$lparse = clone $parser;
			
		$inline_out='';
		//do media lookup for wClip=Name  where appropriate 
		//valid properties:	
		$lines = explode("\n", $input);	
		$parseBucket=$cur_attr='';	
		foreach($lines as $line){	
			$e = strpos($line, '=');
			if($e!==false){
				$cur_attr = substr($line, 1,$e-1);
			}
			if(in_array($cur_attr, $mv_pl_inline_attr)){
				//close the parse bucket (found a valid inline attr)	
				if($parseBucket!=''&& $cur_attr!='desc'){		
					$output = $lparse->parse( $parseBucket, $parser->mTitle, $parser->mOptions, true, false );
					$inline_out.= '|desc=' . $output->getText() . "\n";
					$parseBucket='';
				}
			}		
			//for expanding reference to Media hosted on the wiki: 
			// || $cur_attr=='image'
			if($cur_attr=='wClip'){
				$title_str = substr($line, $e+1); 
				
				/*if( stripos($title_str, NS_IMAGE)===false && stripos($title_str, NS_MEDIA)===false){
					$mTitle = Title::newFromText($title_str, NS_IMAGE);					
				}else{					
					$mTitle = Title::newFromText($title_str);
				}
				if($mTitle->exists()){
					if($mTitle->getNamespace()==NS_IMAGE ||	$mTitle->getNamespace()==NS_MEDIA){									
						$img  = new Image( $mTitle );
						if($img->exists()){		
							if($cur_attr=='wClip'){			
								$line = "\n".'|srcClip='. $img->getUrl();
							}else if($cur_attr=='image'){
								$line = '|image='. $img->getUrl();
							}
						}
					}
				}*/
			}
			//wiki parse the desc: 
			if($cur_attr=='desc'){
				//make sure its not -->			
				if($parseBucket==''){	
					if($e!==false){			
						$parseBucket.=substr($line, $e+1)."\n";
					}
				}else{	
					$parseBucket.=$line."\n";					
				}			
			}			
			//if not collecting for the parse bucket go directly to the inline output
			if(	$parseBucket==''){	
				//if line not being wiki-parsed send it to htmlspecialchars
				$inline_out.=$line . "\n";		
			}
		}	
		//if we have lefter over parseBucket add it in:
		if($parseBucket!=''){
			$output= $lparse->parse( $parseBucket, $parser->mTitle, $parser->mOptions, true, false );
			$inline_out.= '|desc=' .$output->getText() . "\n";
		}
		//print 'pl:' .$inline_out . "\n*****end******\n";
		$vidtag = '<div style="float:left;padding:5px;"><playlist';
		foreach($argv as $attr=>$val){
			//make sure its a valid attribute: 
			if(in_array($attr, $mv_video_attr)){
				$vidtag.=' ' . $attr .'="'.htmlspecialchars(trim($val)).'"';
			}
		}
		$vidtag.='>';
		$vidtag.='<!--'.$inline_out.'--></playlist></div><br>';
		
		$marker = "xx-marker".count($markerList)."-xx";
	    $markerList[] = $vidtag;
	    return $marker;
	}
 	function getPageContent(){
 		$base_text = parent::getContent();
 		//strip the sequence
 		$seqClose = strpos($base_text, '</'.SEQUENCE_TAG.'>');
 		if($seqClose!==false){
 			return trim(substr($base_text, $seqClose+strlen('</'.SEQUENCE_TAG.'>')));
 		}
 	}
 	function getSequenceText(){
 		//check if the current article exists: 
 		if($this->mTitle->exists()){
	 		$base_text = parent::getContent();
	 		$seqClose = strpos($base_text, '</'.SEQUENCE_TAG.'>');
	 		if($seqClose!==false){
	 			//strip the sequence tag: 
	 			return "\n".trim(substr($base_text, strlen('<'.SEQUENCE_TAG.'>'), $seqClose-strlen('</'.SEQUENCE_TAG.'>') ))."\n";
	 		}else{
	 			return $base_text;
	 		}
 		}else{
 			//return a "new empty sequence ..only set the title:"
 			return '|title=' . $this->mTitle->getText()."\n";
 		}
 	} 
 }
?>
