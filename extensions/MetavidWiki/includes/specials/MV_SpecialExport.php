<?php
/*
 * MV_SpecialExport.php Created on Oct 23, 2007
 * 
 * All Metavid Wiki code is Released Under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 * 
 * exports Video feeds in a few different queries to machine readable formats
 * initially target: rss, miro  (format attribute)
 * atom etc would be good.  
 * 
 * 
 * Special:MvExport 
 */
if (!defined('MEDIAWIKI')) die();

global $IP, $smwgIP;
//export types:
function doExportStream($par = null){$MvSpecialExport = new MV_SpecialExport('stream',$par);}
function doExportCat($par = null){$MvSpecialExport = new MV_SpecialExport('category',$par);}
function doExportSeq($par = null){$MvSpecialExport = new MV_SpecialExport('sequence',$par);}
function doExportSearch($par = null){$MvSpecialExport = new MV_SpecialExport('search',$par);}

SpecialPage::addPage( new SpecialPage('MvVideoFeed','',true,'doExportCat',false) );
SpecialPage::addPage( new SpecialPage('MvExportStream','',true,'doExportStream',false) );
SpecialPage::addPage( new SpecialPage('MvExportSequence','',true,'doExportSeq',false) );
SpecialPage::addPage( new SpecialPage('MvExportSearch','',true,'doExportSearch',false) );

//extend supported feed types:
$wgFeedClasses['cmml']='CmmlFeed';
$wgFeedClasses['podcast']='PodcastFeed';

class MV_SpecialExport {
	var $feed = null;
	function __construct($export_type, $par){
		$this->export_type=$export_type;	
		$this->par = $par;	
		$this->execute();
	}
	//@@todo think about integration into api.php	
	function execute() {
		global $wgRequest, $wgOut, $wgUser, $mvStream_name, $mvgIP;
		$html='';
		//set universal variables: 
		$this->feed_format = $wgRequest->getVal('feed_format');	
		
		switch($this->export_type){
			case 'stream':
				$this->stream_name = $wgRequest->getVal('stream_name');	
				$this->req_time = $wgRequest->getVal('t');		
				if(!$this->req_time)$this->req_time = $wgRequest->getVal('time_range');
				
				switch($this->feed_format ){
					case 'cmml':
						$this->get_stream_cmml();
					break;
					case 'roe':
						$this->get_roe_desc();
					break;
				}				
			break;
			case 'category':
				$this->cat=$wgRequest->getVal('cat'); 	
				$this->get_category_feed();		
			break;
			case 'search':
				$this->get_search_feed();
			break;
			case 'sequence':			
				$this->seq_title = $this->par;
				$this->get_sequence_xspf();
			break;			
		}
		//@@todo cleaner exit? 
		exit();
	}    
	function get_sequence_xspf(){		
		//get the sequence article and export in xspf format: 		
		$seqTitle = Title::newFromText($this->seq_title, MV_NS_SEQUENCE);
		$seqArticle = new MV_SequencePage($seqTitle);	
		header('Content-Type: text/xml');
		$o='<?xml version="1.0" encoding="UTF-8"?>'."\n";
 		$o.='<playlist version="1" xmlns="http://xspf.org/ns/0/">'."\n";
 		$o.='	<title>'.$seqTitle->getText().'</title>'."\n";
 		$o.='	<info>'.$seqTitle->getFullURL().'</info>'."\n";
 		$o.='	<trackList>'."\n";
 		$seqArticle->parsePlaylist(); 	
 		foreach($seqArticle->clips as $clip){
	 		$o.='	<track>'."\n";
	 		$o.='		<title>'.htmlentities($clip['title']).'</title>'."\n";
	 		$o.='		<location>'.htmlentities($clip['src']).'</location>'."\n";
	 		$o.='		<info>'.htmlentities($clip['info']).'</info>'."\n";
	 		$o.='		<image>'.htmlentities($clip['image']).'</image>'."\n";
	 		$o.='		<annotation>'.htmlentities($clip['desc']).'</annotation>'."\n";	 		
	 		$o.='	</track>'."\n";
 		}
 		$o.='	</trackList>'."\n";
 		$o.='</playlist>';
 		print $o;
	}
	//start high level: 
	function get_roe_desc(){
		//returns a high level description with cmml links (or inline-populated upon request)
		 $streamTitle = new MV_Title($this->stream_name.'/'.$this->req_time);
		if(!$streamTitle->doesStreamExist()){
			//@@todo we should output the error in XML friendly manner
			die('stream does not exist');
		}
		//get the stream stream req 
		header('Content-Type: text/xml');
		//print the header:
		print '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
		?>
<ROE>
	<head>
		<link rel="alternate" type="text/html" href="<?=htmlentities($wgTitle->getFullURL() )?>" />
		<img id="stream_thumb" src="<?=htmlentities($streamTitle->getStreamImageURL())?>"/>
	</head>
		<?
		
		//get all avaliable files
		$file_list =$streamTitle->mvStream->getFileList(); 		
		?>
		<track id="v" provides="video">
			<switch distinction="quality">
			<? foreach($file_list as $sf){ ?>
				<video id="<?=htmlentities($sf->getNameKey())?>"
					  src="<?=htmlentities($sf->getFullURL())?>"
					  content-type=<?=htmlentities($sf->getContentType())?>
			<? } ?>
			</switch>
		</track>
		<?
		//get all avaliable stream text layers ( inline request CMML (if apropo ))		 
	}
	
	/*get stream CMML (prefix all tags if nessesary) */
	function get_stream_cmml($prefix=''){		
		$dbr =& wfGetDB(DB_SLAVE);		
		//get the stream title	
		$streamTitle = new MV_Title($this->stream_name.'/'.$this->req_time);		
		$wgTitle = Title::newFromText($this->stream_name.'/'.$this->req_time, MV_NS_STREAM);
		//do mvd_index query:
		$mvd_res = MV_Index::getMVDInRange($streamTitle->getStreamId(),
				$streamTitle->getStartTimeSeconds(), 
				$streamTitle->getEndTimeSeconds());
		//get the stream stream req 
		header('Content-Type: text/xml');
		//print the header:
		print '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';		
		$tracks=array();
		if(count($dbr->numRows($mvd_res))!=0){ 
			global $wgOut;
				//use catapiller for logo
			$MV_Overlay = new MV_Overlay();	
			$wgOut->clearHTML();	
			while($mvd = $dbr->fetchObject($mvd_res)){	
				$MV_Overlay->get_article_html($mvd);	
				if(!isset($tracks[$mvd->mvd_type]))$tracks[$mvd->mvd_type]='';			
				$tracks[$mvd->mvd_type].='						
				<clip id="mvd_'.$mvd->id.'" start="ntp:'.seconds2ntp($mvd->start_time).'" end="ntp:'.seconds2ntp($mvd->end_time).'">
				<img src="'.htmlentities($streamTitle->getStreamImageURL(null, seconds2ntp($mvd->start_time))).'"/>
				<body><![CDATA[
						'.$wgOut->getHTML().'
					]]></body> 
				</clip>';			 					
				//clear wgOutput
				$wgOut->clearHTML();
			}
		}		
 	    //based on: http://trac.annodex.net/wiki/CmmlChanges
?>
<!DOCTYPE roe SYSTEM "http://svn.annodex.net/standards/roe/roe_1_0.xsd">
<ROE>
	<body>
	<stream id="<?=$this->stream_name?>" basetime="0">
			<import id="videosrc" lang="en" title="<?=$streamTitle->getStreamNameText()?>" 
				 contenttype="video/ogg" 
			src="<?=$streamTitle->getWebStreamURL()?>" start="0" end="60">
				<param id="vheight" name="video.height" value="320"/>
				<param id="vwidth"  name="video.width"  value="240"/>
			</import>		
	</stream>	
<?
	foreach($tracks as $role=>$body_string){
?>
<cmml lang="en" id="simple" role="<?=$role?>">		
	<head>
		<title><?=wfMsg($role)?></title>					
	</head>
	<?=$body_string?>
	</cmml>
<?
	}
	?>
	</omdl>
	<?
	exit;
	}
	// @@todo integrate cache query (similar to SpecialRecentChanges::rcOutputFeed ))
	function get_category_feed(){
		global $wgSitename, $wgRequest, $wgOut, $wgCategoryPagingLimit;		
		//get the category article: 
		$title = Title::makeTitle( NS_CATEGORY,  $this->cat);		
		$article = new Article($title);
		
		$this->limit = $wgCategoryPagingLimit;	
		
		$this->feed = new mvRSSFeed(
			$wgSitename . ' - ' . wfMsgForContent( 'video_feed_cat' ) .$this->cat, //title 
			$article->getContent(), //description
			$title->getFullUrl() //link 
		);
		$this->feed->outHeader();
		
		$this->from = $wgRequest->getVal( 'from' );
		$this->until = $wgRequest->getVal( 'until' );
		
		//do a query (get all video items in this category) 
		if( $this->from != '' ) {
			$pageCondition = 'cl_sortkey >= ' . $dbr->addQuotes( $this->from );
			$this->flip = false;
		} elseif( $this->until != '' ) {
			$pageCondition = 'cl_sortkey < ' . $dbr->addQuotes( $this->until );
			$this->flip = true;
		} else {
			$pageCondition = '1 = 1';
			$this->flip = false;
		}
		$dbr = wfGetDB( DB_SLAVE );				
		$res = $dbr->select(
			array( 'page', 'categorylinks' ),
			array( 'page_title', 'page_namespace', 'page_len', 'page_is_redirect', 'cl_sortkey' ),
			$pageCondition . " AND (
				  `page_namespace`  =  ".MV_NS_MVD." OR 
				  `page_namespace`  =  ".MV_NS_STREAM." OR 
				  `page_namespace`  = ".MV_NS_SEQUENCE." )
				   AND `cl_from`=  `page_id`
			       AND `cl_to` = '{$title->getDBkey()}'
			       AND `page_is_redirect`=0",
			#+ $pageCondition,
			__METHOD__,
			array( 'ORDER BY' => $this->flip ? 'cl_sortkey DESC' : 'cl_sortkey',
			       'USE INDEX' => 'cl_sortkey', 
			       'LIMIT'    => $this->limit + 1 ) );
		
		//echo 'last query: ' . $dbr->lastQuery();
		$count = 0;
		$this->nextPage = null;
		while( $x = $dbr->fetchObject ( $res ) ) {
			if( ++$count > $this->limit ) {
				// We've reached the one extra which shows that there are
				// additional pages to be had. Stop here...
				$this->nextPage = $x->cl_sortkey;
				break;
			}				
			$title = Title::makeTitle( $x->page_namespace, $x->page_title );			
			$this->feed->outPutItem($title);
		}				
		$this->feed->outFooter();
		//$this->rows =  
	}
	function get_search_feed(){	
		global $wgSitename, $wgOut;	
		//set up search obj: 
		$sms = new MV_SpecialMediaSearch();
		//setup filters:
		$sms->setUpFilters();
		//do the search:
		$sms->doSearch();
		//get the search page title:
		$msTitle = Title::MakeTitle(NS_SPECIAL, 'MediaSearch');
		
		$this->feed = new mvRSSFeed(
			$wgSitename . ' - ' .wfMsg('mediasearch'). ' : '. $sms->getFilterDesc(), //title 
			$sms->getFilterDesc(), //description
			$msTitle->getFullUrl().'?'.$sms->get_httpd_filters_query() //link 
		);
		$this->feed->outHeader();	
		//for each search result: 		
		foreach ($sms->results as $stream_id => & $stream_set) {			
			$matches = 0;
			$stream_out = $mvTitle = '';			
			foreach ($stream_set as & $srange) {
				$cat_html = $mvd_out = '';
				$range_match=0;						
				foreach ($srange['rows'] as $inx=> & $mvd) {								
					$matches++;			
					$wTitle = Title::MakeTitle(MV_NS_MVD, $mvd->wiki_title);
					$this->feed->outPutItem($wTitle);
				}
			}
		}
		$this->feed->outFooter();		
	}      
}
class mvRSSFeed extends ChannelFeed{
	function outHeader() {
		$this->outXmlHeader();
		?>
<rss version="2.0"
	xmlns:creativeCommons="http://backend.userland.com/creativeCommonsRssModule"
	xmlns:media="http://search.yahoo.com/mrss/"
	xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
	xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
	xmlns:blip="http://blip.tv/dtd/blip/1.0"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
    xmlns:amp="http://www.adobe.com/amp/1.0"
	xmlns:dcterms="http://purl.org/dc/terms"
	xmlns:gm="http://www.google.com/schemas/gm/1.1">
	<channel>
	<title><?=$this->getTitle()?></title>
	<link><?=$this->getUrl()?></link>
	<description><?=$this->getDescription()?></description>	
	<?
	}
	function outPutItem($wikiTitle, $desc_text=''){
		global $wgOut;		
		$mvTitle = new MV_Title($wikiTitle);
		//@@todo this should be done cleaner/cached 
		//@@todo we need absolute links
		
		$thumb_ref = $mvTitle->getStreamImageURL('320x240');
		if($desc_text==''){
			$article = new Article($wikiTitle);
			$wgOut->clearHTML(); 			
			$wgOut->addWikiText($article->getContent() );
			$desc_html = $wgOut->getHTML();		
			$wgOut->clearHTML();					
		}else{
			$desc_html = &$desc_text;
		}
		$desc_xml ='<![CDATA[				
			<center class="mv_rss_view_only">
			<a href="'.$wikiTitle->getFullUrl().'"><img src="'.$thumb_ref.'" border="0" /></a>
			</center>
			<br />'.
			$desc_html. 
			']]>';
				
		$stream_url = $mvTitle->getWebStreamURL();			
		$talkpage = $wikiTitle->getTalkPage();			
					
		$type_desc = ($mvTitle->getMvdTypeKey())?wfMsg($mvTitle->getMvdTypeKey()):'';			
		$time_desc = ($mvTitle->getTimeDesc())?$mvTitle->getTimeDesc():'';					
		?>	
		<item>
		<link><?=mvRSSFeed::xmlEncode($wikiTitle->getFullUrl())?></link>
		<title><?=mvRSSFeed::xmlEncode(
			$mvTitle->getStreamNameText() . ' ' .  $time_desc)?></title>
		<description><?=$desc_xml?></description>
		<enclosure type="video/ogg" url="<?=mvRSSFeed::xmlEncode($stream_url)?>"/>
		<comments><?=mvRSSFeed::xmlEncode($talkpage->getFullUrl())?></comments>
		<media:thumbnail url="<?=mvRSSFeed::xmlEncode($thumb_ref)?>"/>
		<? /*todo add in alternate streams HQ, lowQ archive.org etc: 
		<media:group>
    		<media:content blip:role="Source" expression="full" fileSize="2702848" height="240" isDefault="true" type="video/msvideo" url="http://blip.tv/file/get/Conceptdude-EroticDanceOfANiceBabe266.avi" width="360"></media:content>
    		<media:content blip:role="web" expression="full" fileSize="3080396" height="240" isDefault="false" type="video/x-flv" url="http://blip.tv/file/get/Conceptdude-EroticDanceOfANiceBabe266.flv" width="360"></media:content>
  		</media:group>
  		*/ ?> 
		</item>
		<?
	}
}
?>
