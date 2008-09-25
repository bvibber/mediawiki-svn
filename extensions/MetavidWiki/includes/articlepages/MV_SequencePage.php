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
// sequence just adds some sequence hooks:
if ( !defined( 'MEDIAWIKI' ) )  die( 1 );

define( 'SEQUENCE_TAG', 'sequence_xml' );

/*
 * defines approved smil tags & attributes
 * would be nice to use a normal xml dtd  (document type defenitions) but since we use a
 * custom subset of smil (atm) we just have to maunally define it. and its kind of hevey weight  
 */
$mvSmilTags = Array(
	'smil'=>
);

class MV_SequencePage extends Article {
	var $outMode = 'page';
	var $clips = array();
	function __construct( $title ) {
		global $wgRequest;
		/*mvfAddHTMLHeader('sequence');
		 if($title!=null){
		 parent::__construct($title);
		 }
		 return $this;*/
		return parent::__construct( $title );
	}
	/*
	 * returns the xml output of the sequence with all wiki-text templates/magic words swaped out
	 * also resolves all image and media locations with absolute paths.
	 */
	function getParsedSequence(){
		global $wgParser,$wgOut, $wgUser, $wgEnableParserCache;
		$parserOutput=false;
		//temporarlly stop cache: 
		$wgEnableParserCache=false;
		
		if($wgEnableParserCache){
			$mvParserCache = & MV_ParserCache::singleton();
			$mvParserCache->addToKey( 'seq-xml' ); //differentiate the articles xml from article
			$parserOutput = $mvParserCache->get( $this, $wgUser );
		}
		if($parserOutput!=false)
			return $parserOutput->getText();
		//get seqXML: 
		$seq_text_xml = $this->getSequenceText();
		$seqXML=array();
		//parse
	    $xml_parser = xml_parser_create( 'UTF-8' ); // UTF-8 or ISO-8859-1
		   xml_parser_set_option( $xml_parser, XML_OPTION_CASE_FOLDING, 0 );
		   xml_parser_set_option( $xml_parser, XML_OPTION_SKIP_WHITE, 1 );
		   xml_parse_into_struct( $xml_parser, $seq_text_xml, $seqXML );
	    xml_parser_free($xml_parser);
		//print_r($seqXML);
		//die;
		
		
		$parserOutput = $wgParser->parse($seq_text, $this->mTitle, ParserOptions::newFromUser( $wgUser ));
			
		//save to cache if parser cache enabled:
		if($wgEnableParserCache)
			$mvParserCache->save( $parserOutput, $this, $wgUser );


		return $parserOutput->getText();
	}
	/*function doSeqReplace(&$input, &$argv, &$parser){
	 return
	 }*/
	/*function parsePlaylist(){
	 global $wgParser,$wgOut;
	 //valid playlist in-line-attributes:
		$mvInlineAttr = array('wClip', 'mvClip', 'title','linkback','desc','desc','image');
			
		//build a associative array of "clips"
		$seq_text = $this->getSequenceText();

		$seq_lines = explode("\n",$seq_text);
		$parseBucket=$cur_attr='';
		$clip_inx=-1;
		foreach($seq_lines as $line){
		//actions start with |
		$e = strpos($line, '=');
		if($e!==false){
		$cur_attr = substr($line, 1,$e-1);
		}
		if(in_array($cur_attr, $mvInlineAttr)){
		if($cur_attr=='mvClip'){
		$clip_inx++;
		}
		//close the parse bucket (found a valid inline attr)
		if($parseBucket!=''&& $cur_attr!='desc'){
		$output = $wgParser->parse( $parseBucket, $parser->mTitle, $parser->mOptions, true, false );
		$parseBucket='';
		}
		}
		$start_pos = ($e!==false)?$e+1:0;
		if($clip_inx!=-1){
		if(!isset($this->clips[$clip_inx]))$this->clips[$clip_inx]=array();
		if(!isset($this->clips[$clip_inx][$cur_attr]))$this->clips[$clip_inx][$cur_attr]='';
		$this->clips[$clip_inx][$cur_attr].= substr($line, $start_pos);
		}
		}
		//poluate data (this could go here or somewhere else)
		foreach($this->clips as $inx=>&$clip){
		if(trim($clip['mvClip'])==''){
		unset($this->clips[$inx]);
		continue;
		}
		if($clip['mvClip']){
		$sn = str_replace('?t=','/', $clip['mvClip']);
		$streamTitle = new MV_Title($sn);
		$wgStreamTitle = Title::newFromText($sn, MV_NS_STREAM);
		if($streamTitle->doesStreamExist()){
		//mvClip is a substitue for src so assume its there:
		$clip['src']=$streamTitle->getWebStreamURL();
		//title
		if(!isset($clip['title']))$clip['title']='';
		if($clip['title']=='')
		$clip['title']=$streamTitle->getTitleDesc();
			
		if(!isset($clip['info']))$clip['info']='';
		if($clip['info']=='')
		$clip['info']=$wgStreamTitle->getFullURL();
		}
		//check if we should look up the image:
		if(!isset($clip['image']))$clip['image']=='';
		if($clip['image']=='')
		$clip['image'] =$streamTitle->getFullStreamImageURL();
		//check if desc was present:
		if(!isset($clip['desc']))$clip['desc']='';
		//for now just lookup all ... @@todo future expose diffrent language tracks
		if($clip['desc']==''){
		$dbr =& wfGetDB(DB_SLAVE);
		$mvd_rows = MV_Index::getMVDInRange($streamTitle->getStreamId(),
		$streamTitle->getStartTimeSeconds(),
		$streamTitle->getEndTimeSeconds());

		if(count($mvd_rows)!=0){
		$MV_Overlay = new MV_Overlay();
		$wgOut->clearHTML();
		foreach($mvd_rows as $mvd){
		//output a link /line break
		$MV_Overlay->outputMVD($mvd);
		$wgOut->addHTML('<br>');
		}
		$clip['desc']=$wgOut->getHTML();
		$wgOut->clearHTML();
		}
		}
		}

		}
		//print_r($this->clips);
		}*/
	function doSeqReplace( &$input, &$argv, &$parser ) {
		global $wgTitle, $wgUser, $wgRequest, $markerList;
		$sk = $wgUser->getSkin();
		$title = Title::MakeTitle( NS_SPECIAL, 'MvExportSequence/' . $wgTitle->getDBKey() );
		$title_url = $title->getFullURL();
		$oldid = $wgRequest->getVal( 'oldid' );
		if ( isset( $oldid ) ) {
			// @@ugly hack .. but really this whole sequencer needs a serious rewrite)
			$ss = ( strpos( $title_url, '?' ) === false ) ? '?':'&';
			$title_url .= $ss . 'oldid=' . $oldid;
		}
			
		$vidtag = '<div id="file" class="fullImageLink"><playlist';
		$vidtag .= ' width="400" height="300" src="' . htmlspecialchars( $title_url ) . '">';
		$vidtag .= '</playlist></div><hr>';

		$marker = "xx-marker" . count( $markerList ) . "-xx";
		$markerList[] = $vidtag;
		return $marker;
	}
	function getPageContent() {
		global $wgRequest;
		$base_text = parent::getContent();
		// strip the sequence
		$seqClose = strpos( $base_text, '</' . SEQUENCE_TAG . '>' );
		if ( $seqClose !== false ) {
			return trim( substr( $base_text, $seqClose + strlen( '</' . SEQUENCE_TAG . '>' ) ) );
		}
	}
	//@@support static call if aritle is provided: 
	function getSequenceText($article=null) {		
		// check if the current article exists:
		if ( $this->mTitle->exists() ) {
			$base_text = parent::getContent();
			$seqClose = strpos( $base_text, '</' . SEQUENCE_TAG . '>' );
			if ( $seqClose !== false ) {
				// strip the sequence tag:
				$seqText = "\n" . trim( substr( $base_text, strlen( '<' . SEQUENCE_TAG . '>' ), $seqClose - strlen( '</' . SEQUENCE_TAG . '>' ) ) ) . "\n";
				return $seqText;
			}
		}
		// return a "new empty sequence ..only set the title:"
		return '';
			
	}
}
?>
