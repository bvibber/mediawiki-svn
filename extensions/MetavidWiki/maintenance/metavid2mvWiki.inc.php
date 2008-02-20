<?php
/*
 * metavid2mvWiki.inc.php Created on Jan 19, 2008
 *
 * All Metavid Wiki code is Released under the GPL2
 * for more info visit http:/metavid.ucsc.edu/code
 * 
 * @author Michael Dale
 * @email dale@ucsc.edu
 * @url http://metavid.ucsc.edu
 */
 
 /*
  * Templates: 
  */
 function upTemplates($force=false){
/***************************************************
 * Transcripts: 
 * updates transcript templates
 ***************************************************/
 	$wgTemplateTitle = Title :: makeTitle(NS_TEMPLATE, 'Ht_en');
	do_update_wiki_page($wgTemplateTitle, '<noinclude>
		This is the default Template for the display of transcript text. 
		</noinclude><includeonly>{{ #if:  {{{PersonName|}}} | {{ #ifexist: Image:{{{PersonName}}}.jpg | [[Image:{{{PersonName}}}.jpg|44px|left]]|[[Image:Missing person.jpg|44px|left]]}} |}}{{ #if:{{{PersonName|}}}|[[{{{PersonName}}}]]: |}}{{{BodyText}}}
		</includeonly>',null, $force);
/****************************************************
 * Archive.org file type semantics
 ****************************************************/  
	$archive_org_ftypes = array('64Kb_MPEG4','256Kb_MPEG4','MPEG1','MPEG2','flash_flv');
	foreach($archive_org_ftypes as $ftype){
		$pTitle= Title::makeTitle(SMW_NS_PROPERTY, 'Ao_file_'.$ftype );
		do_update_wiki_page($pTitle, '[[has type::URL]]',null, $force);
	}
/*****************************************************
 * Bill Templates
 ****************************************************/	
	$bill_template='<noinclude>Bill Person Template simplifies the structure of articles about Bills.
<pre>{{Bill|
GovTrackID=The GovTrack Bill ID (used to key-into GovTracks Bill info)|
ThomasID=The bill\'s Tomas id (used for Thomas linkback)|
MAPLightBillID=The Map light Bill ID (used for supporting and opposing interest)|
OpenCongressBillID=The open congress bill id (used for bill rss feeds)|
Title Description=The short title/description of the bill|
Date Introduced=The date the bill was introduced|
Session=The session of congress (110 for 2007-08, 109 for 2005-2006 etc)|
Bill Key=The short bill name ie: H.R. #|
Sponsor=Who the Bill was Sponsored By|
Cosponsor #=Cosponsor, Where # is 1-70 for listing all cosponsors|
Supporting Interest #=Interest, Where # is 1-20 for listing top supporting interests|
Opposing Interest #=Interest, Where # is 1-20 for listing top opposing interests|				
}}</pre>The template name (Bill) should be given as the \'\'first\'\' thing on a page. The Cosponsored list should come at the end.
</noinclude><includeonly>
==Bill [[Bill Key:={{{Bill Key}}}]] in the {{ #if: {{{Session|}}}| [[Congress Session:={{{Session}}}]] |}} of Congress==
<span style="float:right">{{navimg|xsize=50|ysize=50|image=Crystal_Clear_mimetype_video.png|link=Category:{{{Bill Key}}}}}</span>
{{ #if: {{{Title Description|}}}|{{{Title Description}}} |}}

{{ #if: {{{Bill Key|}}}| Media in [[:Category:{{{Bill Key}}}]] |}}
{{ #if: {{{Date Introduced|}}}|* Date Introduced: [[Date Bill Introduced:={{{Date Introduced}}}]] |}}
{{ #if: {{{Sponsor|}}}|* Sponsor: [[Bill Sponsor:={{{Sponsor}}}]] |}}';
$bill_template.='{{ #if: {{{Cosponsor 1|}}}|* Cosponsor: [[Bill Cosponsor:={{{Cosponsor 1}}}]] |}}';

//$bill_template.='{{ #for: {{{n}}} | {{{Cosponsor $n$}}}<br/> }}';
for($i=2;$i<70;$i++){	
	$bill_template.='{{ #if: {{{Cosponsor '.$i.'|}}}|, [[Bill Cosponsor:={{{Cosponsor '.$i.'}}}]] |}}';
}
//output mapLight info if present:
$bill_template.='{{ #if: {{{MapLightBillID|}}}|==Intrests who<span style="color:green">support</span> bill becoming law=='."\n".' |}}';
for($i=1;$i<20;$i++){
	$bill_template.='{{ #if: {{{Supporting Interest '.$i.'|}}}|* [[Supporting Interest:={{{Supporting Interest '.$i.'}}}]]'."\n".' |}}';
}
$bill_template.='{{ #if: {{{MapLightBillID|}}}|==Interests who<span style="color:red">oppose</span> bill becoming law=='."\n".' |}}';
for($i=1;$i<20;$i++){
	$bill_template.='{{ #if: {{{Opposing Interest '.$i.'|}}}|* [[Opposing Interest:={{{Supporting Interest '.$i.'}}}]]'."\n".'|}}';
}
//@@todo could do inline rss once we get a good cache model for http://www.mediawiki.org/wiki/Extension:RSS_Reader
// maybe just action=purge on as a cron job, with $parser->disableCache(); commented out 
$bill_template.='{{ #if: {{{OpenCongressBillID|}}}|==Bill RSS Feeds==
* In the News [http://www.opencongress.org/bill/{{{OpenCongressBillID|}}}/atom_news]
* Blog Coverage [http://www.opencongress.org/bill/{{{OpenCongressBillID|}}}/atom_blogs]
* Bill Actions [http://www.opencongress.org/bill/{{{OpenCongressBillID|}}}/atom]
|}}';

$bill_template.='
==Data Sources==  		
{{ #if: {{{ThomasID|}}}|* [[Metavid Sources#Thomas|Thomas]] Official Bill Information:[[Data_Source_URL:=http://thomas.loc.gov/cgi-bin/bdquery/z?{{{ThomasID}}}:]] [[Thomas Bill ID:={{{ThomasID}}}| ]] |}}
{{ #if: {{{GovTrackID|}}}|* [[Metavid Sources#GovTrack|GovTrack]] Bill Overview:[[Data_Source_URL:=http://www.govtrack.us/congress/bill.xpd?bill={{{GovTrackID}}}]] [[GovTrack Bill ID:={{{GovTrackID}}}| ]] |}} 
{{ #if: {{{MapLightBillID|}}}|* [[Metavid Sources#MapLight|MapLight]] Bill Overview:[[Data_Source_URL:=http://maplight.org/map/us/bill/{{{MapLightBillID}}}]] [[Map Light Bill ID:={{{MapLightBillID}}}| ]] |}}
[[Category:Bill]]
</includeonly>';	
	$wgTemplateTitle = Title :: makeTitle(NS_TEMPLATE, 'Bill');
	do_update_wiki_page($wgTemplateTitle,$bill_template ,null, $force);
	//update some semnatic property types:
	$wgPropTitle = Title::newFromText('Data_Source_URL', SMW_NS_PROPERTY);
	do_update_wiki_page($wgPropTitle, '[[has type::URL]]',null, $force);
	
	$wgPropTitle = Title::newFromText('Date_Bill_Introduced', SMW_NS_PROPERTY);
	do_update_wiki_page($wgPropTitle, '[[has type::Date]]',null, $force);
/***************************************
 * Interest Group templates:
 **************************************/
 $interest_template = '<noinclude>Interest Group Template simplifies the structure of articles about Interest Groups.
<pre>{{Interest Group|
MapLightInterestID=The MapLight Interest ID|
Funded Name #=funded name where 1 is 1-100 for top 100 contributions|
Funded Amount #=funded amount to name 1 (required pair to funded name #)|	
Supported Bill #=Bills the Interest group supported (long name) 1-100|
Opposed Bill #=Bills Interest group Opposed (long name) 1-100|
}}</pre>
</noinclude><includeonly>
{{ #if: {{{Funded Name 1|}}}|==Recipients Funded==
Showing contributions 2001-2008 Senate / 2005-2008 House [[Data_Source_URL:=http://maplight.org/map/us/interest/{{{MapLightInterestID}}}|source]]
|}}';
/*
 * output top 100 contributers
 */
 $interest_template.='{{ #if: {{{Funded Name 1|}}}|==Bill Supported Funded==
Showing contributions 2001-2008 Senate / 2005-2008 House [[Data_Source_URL:=http://maplight.org/map/us/interest/{{{MapLightInterestID}}}|source]]
|}}';
for($i=1;$i<100;$i++){
	 $interest_template.='{{ #if: {{{Funded Name '.$i.'|}}}|*[[Funded:={{{Funded Name '.$i.'}}};{{{Funded Amount 1}}}]]
|}}';
}
/*
 * output bills supported / opposed template vars:
 */
foreach(array('Supported','Opposed') as $pos){
	$interest_template.='{{ #if: {{{'.$pos.' Bill '.$i.'|}}}|=='.$pos.' Bills==
Pulled from maplight [[Data_Source_URL:=http://maplight.org/map/us/interest/{{{MapLightInterestID}}}/bills|source]]
|}}'; 
	for($i=1;$i<200;$i++){	 
		$interest_template.='{{ #if: {{{'.$pos.' Bill '.$i.'|}}}|*[['.$pos.' Bill:={{{'.$pos.' Bill '.$i.'}}}]]
|}}';
	}
}
$interest_template.='[[Category:Interest Group]]
</includeonly>';

$wgTemplateTitle = Title :: makeTitle(NS_TEMPLATE, 'Interest Group');
do_update_wiki_page($wgTemplateTitle,$interest_template ,null, $force);

$wgPropTitle = Title::newFromText('Funded', SMW_NS_PROPERTY);
do_update_wiki_page($wgPropTitle, '[[has type:=Page;Number]]',null, $force);

/***************************************
 *  Update people templates
 ***************************************/ 
	global $valid_attributes;
	$wgTemplateTitle = Title :: makeTitle(NS_TEMPLATE, 'Congress Person');
	$wgTemplateArticle = new Article($wgTemplateTitle);
	$template_body = '<noinclude>Congress Person template simplifies 
			the structure of articles about Congress People.
			<pre>{{Congress Person|' . "\n";
	foreach ($valid_attributes as $dbKey => $attr) {
		list ($name, $desc) = $attr;				 
		$template_body .= $name . '=' . $desc . "|\n";
	}
	
	$template_body .= '}}</pre>' .
	'The order of the fields is not relevant. The template name (Congress Person) should be given as the \'\'first\'\' thing on a page.
			</noinclude>' .
	'<includeonly>' . "\n";
	//include the image if present: 
	$template_body .= '{{ #if: { Image:{{PAGENAME}}.jpg}| [[Image:{{PAGENAME}}.jpg]] |}}' . "\n";
	foreach ($valid_attributes as $dbKey => $attr) {
		list ($name, $desc) = $attr;	
			$template_body .= "{{ #if: {{{" . $name . "}}}| [[$name:={{{" . $name . "}}}| ]] \n|}}";		
	}	
	$template_body .= '[[Category:Congress Person]] [[Category:Person]]
			</includeonly>';
	echo "updated 'Congress Person' template\n";
	do_update_wiki_page($wgTemplateTitle, $template_body,null, $force);
	
/************************************
 * page helpers
 ************************************/
 $wgTempNavImg = Title::makeTitle(NS_TEMPLATE, 'Navimg');
 do_update_wiki_page($wgTempNavImg, '<div style="position: relative; width: {{{xsize|{{{size|}}}}}}px; height: {{{ysize|{{{size|}}}}}}px; overflow: hidden;"><div style="position: absolute; top: 0; left: 0; font-size: 200pt; width: {{{xsize|{{{size|}}}}}}px; height: {{{ysize|{{{size|}}}}}}px;  overflow: hidden; line-height: {{{ysize|{{{size|}}}}}}px; z-index: 3;">[[:{{{link|}}}|{{{linktext|&nbsp;}}}]]</div><div style="position: absolute; top: 0; left: 0; z-index: 2;">[[Image:{{{image|}}}|{{{xsize|{{{size|}}}}}}px|image page]]</div></div><noinclude>
Simple Usage example: {{navimg|xsize=50|ysize=50|image=Wikimedia-logo.svg|link=MediaWiki}}
</noinclude>
');
}
function do_people_insert() {
	global $valid_attributes, $states_ary;
	$dbr = wfGetDB(DB_SLAVE);

	include_once('scrape_and_insert.inc.php');		
	$mvScrape = new MV_BaseScraper();
	
	//do people query:
	$res = $dbr->query("SELECT * FROM `metavid`.`people`");
	if ($dbr->numRows($res) == 0)
		die('could not find people: ' . "\n");
	$person_ary = array ();
	while ($person = $dbr->fetchObject($res)) {
		$person_ary[] = $person;
	}
	foreach ($person_ary as $person) {
		
		$person_title = Title :: newFromUrl($person->name_clean);
		//semantic data via template:
		$page_body = '{{Congress Person|' . "\n";
		foreach ($valid_attributes as $dbKey => $attr) {			
			list ($name, $desc) = $attr;							
			if ($dbKey == 'district'){
				//special case for district:
				if($person->district){
					if($person->district!=0){
						$page_body .= "{$name}=".text_number($person->district).' District'."|\n";
					}
				}
			}else if($dbKey=='maplight_id'){
				print 'do_maplight_id'."\n";
				//try to grab the maplight id
				$raw_results = $mvScrape->doRequest('http://maplight.org/map/us/legislator/search/'.$person->last.'+'.$person->first);
				preg_match_all('/map\/us\/legislator\/([^"]*)">(.*)<\/a>.*<td>([^<]*)<.*<td>([^<]*)<.*<td>([^<]*)<.*<td>([^<]*)</U',$raw_results, $matches);
				//print_r($matches);
				//die;
				//do point system for match
				$point=array();
				$title_lookup=array('Rep.'=>'House','Sen.'=>'Senate');	
				if(isset($matches['2'])){			
					foreach($matches['2'] as $k=>$name_html){
						if(!isset($point[$k]))$point[$k]=0;
						list($lname,$fname) = explode(',',trim(strip_tags($name_html)));
						if($person->first==$fname)$point[$k]+=2;
						if($person->last==$lname)$point[$k]+=2;
						if($person->state==$matches['3'][$k])$point[$k]++;
						if($person->district==$matches['4'][$k])$point[$k]++;
						if($person->party==$matches['5'][$k])$point[$k]++;				
						if($title_lookup[$person->title]==$matches['6'])$point[$k]++;						
					}									
					//sort the point score 
					asort($point);
					reset($point);
					$page_body .="{$name}=".key($point)."|\n";
				}			
			}else{			
				if (trim($person->$dbKey) != '') {		
					if ($dbKey == 'state')	$person->state = $states_ary[$person->state];				
					$page_body .= "{$name}={$person->$dbKey}|  \n";
				}
			}
		}
			
		//add in the full name attribute: 
		$page_body .= "Full Name=" . $person->title . ' ' . $person->first .
			' ' . $person->middle . ' ' . $person->last . "|  \n";			
		
			
			
		$page_body .= '}}';
		//add in basic info to be overwitten by tranclude (from
		$full_name = $person->title . ' ' . $person->first .
		' ' . $person->middle . ' ' . $person->last;
		if (trim($full_name) == '')
			$full_name = $person->name_clean;			
		 
		$page_body .= "\n" .'Basic Person page For <b>' . $full_name . "</b><br>\n".
				 			"Text Spoken By [[Special:MediaSearch/person/{$person->name_clean}|$full_name]] "; 
				;
		do_update_wiki_page($person_title, $page_body);		
		die;
	}
	foreach ($person_ary as $person) {
		//download/upload all the photos:
		$imgTitle = Title :: makeTitle(NS_IMAGE, $person->name_clean . '.jpg');
		//if(!$imgTitle->exists()){			
		global $wgTmpDirectory;
		$url = 'http://www.opensecrets.org/politicians/img/pix/' . $person->osid . '.jpg';
		//print $wgTmpDirectory . "\n";
		$local_file = tempnam($wgTmpDirectory, 'WEBUPLOAD');
		//copy file:

		# Check if already there existence
		$image = wfLocalFile($imgTitle);
		if ($image->exists()) {
			echo ($imgTitle->getDBkey() . " already in the wiki\n");
			continue;
		}

		for ($ct = 0; $ct < 10; $ct++) {
			if (!@ copy($url, $local_file)) {
				print ("failed to copy $url to local_file (tring again) \n");
			} else {
				print "copy success\n";
				$ct = 10;
			}
			if ($ct == 9)
				print 'complete failure' . "\n";
		}

		# Stash the file
		echo ("Saving " . $imgTitle->getDBkey() . "...");
		$image = wfLocalFile($imgTitle);

		$archive = $image->publish($local_file);
		if (WikiError :: isError($archive)) {
			echo ("failed.\n");
			continue;
		}
		echo ("importing...");
		$comment = 'Image file for [[' . $person->name_clean . ']]';
		$license = '';

		if ($image->recordUpload($archive, $comment, $license)) {
			# We're done!
			echo ("done.\n");
		} else {
			echo ("failed.\n");
		}
		//}
	}
}
//$i=0;
function do_stream_attr_check($old_stream) {
	global $i;
	$mvStream = & mvGetMVStream(array (
		'name' => $old_stream->name
	));
	//print "doding stream attr check: ";
	//print_r($old_stream);
	
	if ($mvStream->date_start_time != $old_stream->adj_start_time) {
		$mvStream->date_start_time = $old_stream->adj_start_time;
	}
	if ($mvStream->duration != ($old_stream->adj_end_time - $old_stream->adj_start_time)) {
		$mvStream->duration = ($old_stream->adj_end_time - $old_stream->adj_start_time);
	}	
	$mvStream->updateStreamDB();
	print "\nran stream db update: " .$mvStream->duration . ' ' . $mvStream->date_start_time."\n";
	//if($i==3)die;
	//$i++;
}
function do_stream_file_check(& $old_stream) {
	global $mvgIP;
	$mvStream = & mvGetMVStream(array (
		'name' => $old_stream->name
	));
	$file_list = $mvStream->getFileList();

	if ($old_stream->trascoded != 'none') {
		//print "transcode is: " . $old_stream->trascoded;
		if ($old_stream->trascoded == 'low')
			$set = array (
				'mv_ogg_low_quality'
			);
		if ($old_stream->trascoded == 'high')
			$set = array (
				'mv_ogg_high_quality'
			);
		if ($old_stream->trascoded == 'all')
			$set = array (
				'mv_ogg_high_quality',
				'mv_ogg_low_quality'
			);
		//print "set: " . print_r($set);
		//remove old file pointers: 
		$dbw = wfGetDB(DB_WRITE);
		$sql = "DELETE FROM `mv_stream_files` WHERE `stream_id`=".$mvStream->id . " AND " .
				"(`file_desc_msg`='mv_ogg_high_quality' OR `file_desc_msg`='mv_ogg_low_quality')";
		$dbw->query($sql);
		//update files:
		foreach ($set as $qf) {
			do_insert_stream_file($mvStream, $old_stream, $qf);
		}
	}
	//check for archive.org stuff too..
	/*if($old_stream->archive_org!=''){
		$found=false;
		foreach($file_list as $file){
			if($file->path_type =='ext_archive_org'){
				$found=true;
			}
		}
		if(!$found)do_insert_stream_file($mvStream, $old_stream, 'mv_archive_org_link');
	}*/	
}
function do_insert_stream_file($mvStream, $old_stream, $quality_msg) {
	global $mvVideoArchivePaths;
	$dbw = wfGetDB(DB_WRITE);
	if ($quality_msg == 'mv_ogg_low_quality') {		
		$path = $mvVideoArchivePaths[$old_stream->archive_server] . $mvStream->name. '.ogg';		
	} else if ($quality_msg == 'mv_ogg_high_quality') {
		$path = $mvVideoArchivePaths[$old_stream->archive_server] .$mvStream->name.'.HQ.ogg';
	}else{
		return '';
	}
	//get file duration from nfo file (if avaliable ): 
	$nfo_url = $path . '.nfo';
	$nfo_txt = file($nfo_url);	
	if($nfo_txt){
		if( isset($nfo_txt[0])){
			list($na, $len) = explode('n:', $nfo_txt[0]);					
			$len = trim($len);
			//trim leading zero
			if($len[0]=='0')$len=substr($len,1);
			//trim sub frame times:
			if(strpos($len, '.')!==false){
				$len = substr($len, 0, strpos($len, '.'));
			}		
			$dur=ntp2seconds($len);		
		}else{
			echo "empty nfo file: $nfo_url \n";
			$dur=0;
		}
	}else{
		echo "missing nfo file: $nfo_url \n";
		$dur=0;
	}
	
	$sql = "INSERT INTO `mv_stream_files` (`stream_id`, `file_desc_msg`, `path`, `duration`)" .
		" VALUES ('{$mvStream->id}', '{$quality_msg}', " ." '{$path}', {$dur} )";
	$dbw->query($sql);
}
//@@todo convert to MV_EditStream 
function do_add_stream(& $mvTitle, & $stream) {
	$MV_SpecialAddStream = new MV_SpecialCRUDStream('add');
	$MV_SpecialAddStream->stream_name = $mvTitle->getStreamName();
	$MV_SpecialAddStream->stream_type = 'metavid_file';
	$MV_SpecialAddStream->stream_desc = mv_semantic_stream_desc($mvTitle, $stream);
	//add the stream:
	$MV_SpecialAddStream->add_stream();
}
function do_stream_insert($mode, $stream_name = '') {
	global $mvgIP, $MVStreams, $options;
	$dbr = wfGetDB(DB_SLAVE);
	if ($mode == 'all'){
		$sql = "SELECT * FROM `metavid`.`streams` WHERE `sync_status`='in_sync'";
	}else if($mode=='files') {
		$sql = "SELECT * FROM `metavid`.`streams` WHERE `trascoded` != 'none'";
	}else{
		$sql = "SELECT * FROM `metavid`.`streams` WHERE `name` LIKE '{$stream_name}'";
	}	
	$res = $dbr->query($sql);
	if ($dbr->numRows($res) == 0)
		die('could not find stream: ' . $stream_name . "\n");
	//load all stream names: 
	while ($row = $dbr->fetchObject($res)) {
		$streams[] = $row;
	}
	print "working on " . count($streams) . ' streams'."\n";
	foreach ($streams as $stream) {
		//init the stream
		$MVStreams[$stream->name] = new MV_Stream($stream);
		//check if the stream has already been added to the wiki (if not add it)	
		$mvTitle = new MV_Title('MvStream:' . $stream->name);
		if (!$mvTitle->doesStreamExist()) {
			//print 'do stream desc'."\n";
			do_add_stream($mvTitle, $stream);
			echo "stream " . $mvTitle->getStreamName() . " added \n";
		} else {			
				$force = (isset($options['force']))?true:false;
				do_update_wiki_page($stream->name, mv_semantic_stream_desc($mvTitle, $stream), MV_NS_STREAM,$force);
			//$updated = ' updated' echo "stream " . $mvTitle->getStreamName() . " already present $updated\n";
		}
		//add duration and start_time attr		
		do_stream_attr_check($stream);

		//do insert/copy all media images 
		if(!isset($options['noimage'])){
			do_proccess_images($stream);
		}

		//check for files (make sure they match with metavid db values
		do_stream_file_check($stream);
		
		if(!isset($options['skiptext'])){
			//proccess all stream text: 
			do_proccess_text($stream);
		}
	}
}
function do_proccess_text($stream){
		$dbr = wfGetDB(DB_SLAVE);
		/* for now use the stream search table (in the future should put in our orphaned person data)
		 * should be able to do quick checks against the index. */
		$sql = "SELECT (`time`+" . CC_OFFSET . ") as time, `value` " .
				"FROM `metavid`.`stream_attr_time_text` 
						WHERE `stream_fk`=" . $stream->id . "
						AND `time` >= " . $stream->adj_start_time . "
						AND `time` <= " . $stream->adj_end_time . "
				ORDER BY `time` ASC ";

		//$sql = "SELECT * FROM `metavid`.`stream_search` WHERE `stream_fk`={$stream->id}";
		$page_res = $dbr->query($sql);
		if ($dbr->numRows($page_res) == 0)
			echo 'No pages for stream' . $stream->name . "\n";
		$pages = array ();
		while ($page = $dbr->fetchObject($page_res)) {
			$pages[] = $page;
		}
		print "Checking ".count($pages) . " text pages\n";
		$i=$j=0;
		foreach ($pages as $inx => $page) {
			//status updates:
			if($i==50){
				print "on $j of ". count($pages) . "\n";
				$i=0;
			}
			$i++;
			$j++;			
			$start_time = $page->time - $stream->adj_start_time;
			if (seconds2ntp($start_time) < 0)
				$start_time = '0:00:00';
			if (($inx +1) == count($pages)) {
				$end_time = $stream->adj_end_time - $stream->adj_start_time;
			} else {
				$end_time = $pages[$inx +1]->time - $stream->adj_start_time;
			}
			if (($end_time - $start_time) > 40)
				$end_time = $start_time +40;
			//skip if end_time <1
			if ($end_time < 0)
				continue;
			//now pull up the person for the given stream time:`metavid`.`people`.`name_clean` 
			$sql = "SELECT * , abs( `metavid`.`people_attr_stream_time`.`time` -{$page->time} ) AS `distance` " .
			"FROM `metavid`.`people_attr_stream_time` " .
			"LEFT JOIN `metavid`.`people` ON `metavid`.`people_attr_stream_time`.`people_fk` = `metavid`.`people`.`id` " .
			"WHERE `metavid`.`people_attr_stream_time`.`stream_fk` ={$stream->id} " .
				//have a negative threshold of 4 seconds
			"AND  (`metavid`.`people_attr_stream_time`.`time`-{$page->time})>-4 " .
				//have a total distance threshold of 30 seconds
			"AND abs( `metavid`.`people_attr_stream_time`.`time` -{$page->time} )< 90 " .
			"ORDER BY `distance` ASC " .
			"LIMIT 1 ";
			$person_res = $dbr->query($sql);

			$page_title = $stream->name . '/' . seconds2ntp($start_time) . '/' . seconds2ntp($end_time);
			//print $page_title . "\n";
			$page_body = '';
			if ($dbr->numRows($person_res) != 0) {
				$person = $dbr->fetchObject($person_res);
				$person_name = utf8_encode($person->name_clean);
				$page_body .= "\n[[Spoken By::{$person_name}]] ";
			}
			$page_body .= trim(str_replace("\n", ' ', strtolower($page->value)));

			//print $page_title . "\n";
			//die;
			//print $page_body . "\n\n";
			do_update_wiki_page('Ht_en:' . $page_title, $page_body, MV_NS_MVD);
		}
}
/* 
 * for each image add it to the image directory
 */
function do_proccess_images($stream) {
	global $mvLocalImgLoc, $MVStreams, $wgDBname;
	$dbr =& wfGetDB(DB_SLAVE);
	$dbw =& wfGetDB(DB_MASTER);

	//get all images for the current stream: 
	$sql = "SELECT * FROM `metavid`.`image_archive` 
				WHERE `stream_fk`= {$stream->id}";
	$image_res = $dbr->query($sql);
	$img_count = $dbr->numRows($image_res);
	print "Found " . $img_count . " images for stream " . $stream->name . "\n";
	//grab from metavid and copy to local directory structure: 
	$i=$j= 0;	
	while ($row = $dbr->fetchObject($image_res)) {
		$relative_time = $row->time - $stream->adj_start_time;
		//status updates: 
		if ($i == 10) {			
			print "On image $j of $img_count time: " . seconds2ntp($relative_time) . "\n";
			$i = 0;
		}
		$j++;
		$i++;
		//get streamImage obj:
		$mv_stream_id = $MVStreams[$stream->name]->getStreamId();
		$local_img_dir = MV_StreamImage :: getLocalImageDir($mv_stream_id);
		$metavid_img_url = 'http://metavid.ucsc.edu/image_media/' . $row->id . '.jpg';
		
		$local_img_file = $local_img_dir . '/' . $relative_time . '.jpg';
		//check if the image already exist in the new table
		$sql = "SELECT * FROM `$wgDBname`.`mv_stream_images` " .
				"WHERE `stream_id`={$mv_stream_id} " .
				"AND `time`=$relative_time";
		$img_check = $dbr->query($sql);
		$doInsert = true;
		if ($dbr->numRows($img_check) != 0) {
			//make sure its there: 
			if (is_file($local_img_file)) {
				//print "skiped stream_id:" . $mv_stream_id . " time: " . $relative_time . "\n";
				continue;
			} else {
				//grab but don't insert: 
				$doInsert = false;
			}
		}
		if ($doInsert) {
			//insert: 		
			$dbw->insert('mv_stream_images', array (
				'stream_id' => $MVStreams[$stream->name]->getStreamId(), 'time' => $relative_time));
			$img_id = $dbw->insertId();
			//$grab = exec('cd ' . $img_path . '; wget ' . $im_url);
		}

		if (is_file($local_img_file)) {
			echo "skipped $local_img_file \n";
			continue;
		}
		if (!copy($metavid_img_url, $local_img_file)) {
			echo "failed to copy $metavid_img_url to $local_img_file...\n";
		} else {
			//all good don't report anything'		
		}
	}
}


//given a stream name it pulls all metavid stream data and builds semantic wiki page
function mv_semantic_stream_desc(& $mvTitle, & $stream) {
	global $start_time, $end_time;
	/*$sql = "SELECT * FROM `metavid`.`streams` WHERE `name` LIKE '" . $mvTitle->getStreamName() . "'";
	$dbr = wfGetDB(DB_SLAVE);
	$res = $dbr->query($sql);
	//echo "\n" . $sql . "\n";
	$stream = $dbr->fetchObject($res);*/
	$stream_id = $stream->id;
	$out = '';
	$pout = mv_proccess_attr('stream_attr_varchar', $stream_id);
	$pout .= mv_proccess_attr('stream_attr_int', $stream_id);
	//add links/generic text at the start 
	$out .= '==Official Record==' . "\n";
	$date = date('Ymd', $start_time);
	$cspan_date = date('Y-m-d', $start_time);
	$ch_type = '';
	if (strpos($mvTitle->getStreamName(), 'house') !== false)
		$ch_type = 'h';
	if (strpos($mvTitle->getStreamName(), 'senate') !== false)
		$ch_type = 's';
	if ($ch_type != '') {
		$out .= '*[[GovTrack]] Congressional Record' .
		'[http://www.govtrack.us/congress/recordindex.xpd?date=' . $date .
		'&where=' . $ch_type .
		']' . "\n\n";
		$out .= '*[[THOMAS]] Congressional Record ' .
		'[http://thomas.loc.gov/cgi-bin/query/B?r110:@FIELD(FLD003+' . $ch_type . ')+@FIELD(DDATE+' . $date . ')' .
		']' . "\n\n";
		$out .= '*[[THOMAS]] Extension of Remarks ' .
		'[http://thomas.loc.gov/cgi-bin/query/B?r110:@FIELD(FLD003+' . $ch_type . ')+@FIELD(DDATE+' . $date . ')' .
		']' . "\n\n";
	}	
	if ($stream->archive_org != '') {
		//grab file list from archive.org:
		require_once('scrape_and_insert.inc.php');
		$aos = new MV_ArchiveOrgScrape();
		$file_list = $aos->getFileList($stream->name);
		if($file_list){
			$out .= '==More Media Sources=='."\n";	
			$out .= '*[[Archive.org]] hosted original copy ' .
			'[http://www.archive.org/details/mv_' . $stream->name . ']' . "\n";
			
			//all streams have congretional cronical: 
			$out .= '*[[CSPAN]]\'s Congressional Chronicle ' .
			'[http://www.c-spanarchives.org/congress/?q=node/69850&date=' . $cspan_date . '&hors=' . $ch_type . ']';		
			//also output 'direct' semantic links to alternate file qualities:
			$out.="\n===Full File Links===\n";	
			$dbw = wfGetDB(DB_WRITE);		
			foreach($file_list as $file){
				$name = str_replace(' ', '_',$file[2]);
				$url = $file[1];
				$size = $file[3];
				$out .= "*[[ao_file_{$name}:={$url}|$name]] {$size}\n";
				
				//add these files into the mv_files table:
				//@@todo future we should tie the mv_files table to the semantic properties? 
				//check if already present:
				$quality_msg = 'ao_file_'.$name;
				$path_type = 'url_file';
				$dbr = wfGetDB(DB_SLAVE);
				$res = $dbr->query("SELECT * FROM `mv_stream_files` 
						WHERE `stream_id`={$mvTitle->getStreamId()} 
						AND `file_desc_msg`='{$quality_msg}'");
				if($dbr->numRows($res) == 0){
					$sql = "INSERT INTO `mv_stream_files` (`stream_id`, `file_desc_msg`, `path_type`, `path`)" .
					" VALUES ('{$mvTitle->getStreamId()}', '{$quality_msg}', '{$path_type}','{$url}' )";
				}else{
					$row = $dbr->fetchObject($res);
					//update that msg key *just in case* 
					$sql = "UPDATE  `mv_stream_files` SET `path_type`='{$path_type}', `path`='$url' WHERE `id`={$row->id}";
				}					
				$dbw->query($sql);								
			}
			$dbw->commit();
			//more semantic properties 
			$out .= "\n\n";
			$out .= $pout;
			$out .= '[[stream_duration:=' . ($end_time - $start_time) . '| ]]' . "\n";
			if($stream->org_start_time){
				$out .= '[[original_date:='.$stream->org_start_time.'| ]]';
			}
		}			
	}		
	//add stream category (based on sync status)
	switch($stream->sync_status){
		case 'not_checked':
			$out.="\n\n".'[[Category:Stream Unchecked]]';
		break;
		case 'impossible':
			$out.="\n\n".'[[Category:Stream Out of Sync]]';
		break;
		case 'in_sync':
			$out.="\n\n".'[[Category:Stream Basic Sync]]';
			//other options [stream high quality sync ]; 
		break;
	} 	
	
	return $out;
}
function do_rm_congress_persons(){
	$dbr =& wfGetDB(DB_SLAVE);		
	$result = $dbr->query( " SELECT *
	FROM `categorylinks`
	WHERE `cl_to` LIKE 'Congress_Person' ");
	while($row = $dbr->fetchObject($result)){		
		$pTitle = Title::makeTitle(NS_MAIN, $row->cl_sortkey);
		$pArticle = new Article($pTitle);
		$pArticle->doDeleteArticle( 'removed reason' );
		print "removed title: " .$pTitle->getText() . "\n";
	}
}
function mv_proccess_attr($table, $stream_id) {
	global $start_time, $end_time;
	$dbr = wfGetDB(DB_SLAVE);
	$sql = "SELECT * FROM `metavid`.`$table` WHERE `stream_fk`=$stream_id";
	$res = $dbr->query($sql);
	$out = '';
	while ($var = $dbr->fetchObject($res)) {
		$type_title = getTypeTitle($var->type);
		if ($var->type == 'adj_start_time')
			$start_time = $var->value;
		if ($var->type == 'adj_end_time')
			$end_time = $var->value;
		if ($type_title != '') {
			$reltype = ($type_title[0] == 'rel') ? '::' : ':=';
			$out .= '[[' . $var->type . ':=' . $var->value . '| ]]' . "\n";
		}
	}
	return $out;
}

function getTypeTitle($type) {
	switch ($type) {
		case 'cspan_type' :
			return array (
				'rel',
				'Government Event'
			);
			break;
		case 'cspan_title' :
			return array (
				'atr',
				'C-SPAN Title'
			);
			break;
		case 'cspan_desc' :
			return array (
				'atr',
				'C-SPAN Description'
			);
			break;
		case 'adj_start_time' :
			return array (
				'atr',
				'Unix Start Time'
			);
			break;
		case 'adj_end_time' :
			return array (
				'atr',
				'Unix End Time'
			);
			break;
		default :
			return '';
			break;
	}
}
?>
