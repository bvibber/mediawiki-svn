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
 function upTempalte_Ht_en($force = false) {
	$wgTemplateTitle = Title :: makeTitle(NS_TEMPLATE, 'Ht_en');
	if (!$wgTemplateTitle->exists() || $force) {
		do_update_wiki_page($wgTemplateTitle, '<noinclude>
		This is the default Template for the display of transcript text. 
		</noinclude><includeonly>{{ #if:  {{{PersonName|}}} | {{ #ifexist: Image:{{{PersonName}}}.jpg | [[Image:{{{PersonName}}}.jpg|44px|left]]|[[Image:Missing person.jpg|44px|left]]}} |}}{{ #if:{{{PersonName|}}}|[[{{{PersonName}}}]]: |}}{{{BodyText}}}
		</includeonly>');
	}
}
function upTemplate_bill($force=false){
	$wgTemplateTitle = Title :: makeTitle(NS_TEMPLATE, 'Bill');
	if (!$wgTemplateTitle->exists() || $force) {
		do_update_wiki_page($wgTemplateTitle, '<noinclude>Bill Person Template simplifies the structure of articles about Bills.
<pre>{{Bill|
GovTrackID=The GovTrack Bill ID (used to key-into GovTracks Bill info)|
ThomasID=The bill\'s Tomas id (used for Thomas linkback)|
Title Description=The short title/description of the bill|
Date Introduced=The date the bill was introduced|
Session=The session of congress (110 for 2007-08) |
Bill Key=The short bill name ie: H.R. #|
Sponsor=Who the Bill was Sponsored By|
Cosponsor #= Where # is 1-70 for listing all cosponsors|
}}</pre>The template name (Bill) should be given as the \'\'first\'\' thing on a page. The Cosponsored list should come at the end.
</noinclude><includeonly>
==Bill [[Bill Key:={{{Bill Key}}}]] in the {{ #if: {{{Session|}}}| [[Congress Session:={{{Session}}}]] |}} of Congress==
{{ #if: {{{Title Description|}}}|{{{Title Description}}} |}}

<span style="background:#eee">{{ #if: {{{Bill Key|}}}| Media tagged/categorized with [[:Category:{{{Bill Key}}}]] |}}</span>
{{ #if: {{{Date Introduced|}}}|* Date Introduced: [[Date Bill Introduced:={{{Date Introduced}}}]] |}}
{{ #if: {{{Sponsor|}}}|* Sponsor: [[Bill Sponsor:={{{Sponsor}}}]] |}}
{{ #if: {{{Cosponsor 1|}}}|* Cosponsor: [[Bill Cosponsor:={{{Cosponsor 1}}}]] |}}{{ #if: {{{Cosponsor 2|}}}|, [[Bill Cosponsor:={{{Cosponsor 2}}}]] |}}{{ #if: {{{Cosponsor 3|}}}|, [[Bill Cosponsor:={{{Cosponsor 3}}}]] |}}{{ #if: {{{Cosponsor 4|}}}|, [[Bill Cosponsor:={{{Cosponsor 4}}}]] |}}{{ #if: {{{Cosponsor 5|}}}|, [[Bill Cosponsor:={{{Cosponsor 5}}}]] |}}{{ #if: {{{Cosponsor 6|}}}|, [[Bill Cosponsor:={{{Cosponsor 6}}}]] |}}{{ #if: {{{Cosponsor 7|}}}|, [[Bill Cosponsor:={{{Cosponsor 7}}}]] |}}{{ #if: {{{Cosponsor 8|}}}|, [[Bill Cosponsor:={{{Cosponsor 8}}}]] |}}{{ #if: {{{Cosponsor 9|}}}|, [[Bill Cosponsor:={{{Cosponsor 9}}}]] |}}{{ #if: {{{Cosponsor 10|}}}|, [[Bill Cosponsor:={{{Cosponsor 10}}}]] |}}{{ #if: {{{Cosponsor 11|}}}|, [[Bill Cosponsor:={{{Cosponsor 11}}}]] |}}{{ #if: {{{Cosponsor 12|}}}|, [[Bill Cosponsor:={{{Cosponsor 12}}}]] |}}{{ #if: {{{Cosponsor 13|}}}|, [[Bill Cosponsor:={{{Cosponsor 13}}}]] |}}{{ #if: {{{Cosponsor 14|}}}|, [[Bill Cosponsor:={{{Cosponsor 14}}}]] |}}{{ #if: {{{Cosponsor 15|}}}|, [[Bill Cosponsor:={{{Cosponsor 15}}}]] |}}{{ #if: {{{Cosponsor 16|}}}|, [[Bill Cosponsor:={{{Cosponsor 16}}}]] |}}{{ #if: {{{Cosponsor 17|}}}|, [[Bill Cosponsor:={{{Cosponsor 17}}}]] |}}{{ #if: {{{Cosponsor 18|}}}|, [[Bill Cosponsor:={{{Cosponsor 18}}}]] |}}{{ #if: {{{Cosponsor 19|}}}|, [[Bill Cosponsor:={{{Cosponsor 19}}}]] |}}{{ #if: {{{Cosponsor 20|}}}|, [[Bill Cosponsor:={{{Cosponsor 20}}}]] |}}{{ #if: {{{Cosponsor 21|}}}|, [[Bill Cosponsor:={{{Cosponsor 21}}}]] |}}{{ #if: {{{Cosponsor 22|}}}|, [[Bill Cosponsor:={{{Cosponsor 22}}}]] |}}{{ #if: {{{Cosponsor 23|}}}|, [[Bill Cosponsor:={{{Cosponsor 23}}}]] |}}{{ #if: {{{Cosponsor 24|}}}|, [[Bill Cosponsor:={{{Cosponsor 24}}}]] |}}{{ #if: {{{Cosponsor 25|}}}|, [[Bill Cosponsor:={{{Cosponsor 25}}}]] |}}{{ #if: {{{Cosponsor 26|}}}|, [[Bill Cosponsor:={{{Cosponsor 26}}}]] |}}{{ #if: {{{Cosponsor 27|}}}|, [[Bill Cosponsor:={{{Cosponsor 27}}}]] |}}{{ #if: {{{Cosponsor 28|}}}|, [[Bill Cosponsor:={{{Cosponsor 28}}}]] |}}{{ #if: {{{Cosponsor 29|}}}|, [[Bill Cosponsor:={{{Cosponsor 29}}}]] |}}{{ #if: {{{Cosponsor 30|}}}|, [[Bill Cosponsor:={{{Cosponsor 30}}}]] |}}{{ #if: {{{Cosponsor 31|}}}|, [[Bill Cosponsor:={{{Cosponsor 31}}}]] |}}{{ #if: {{{Cosponsor 32|}}}|, [[Bill Cosponsor:={{{Cosponsor 32}}}]] |}}{{ #if: {{{Cosponsor 33|}}}|, [[Bill Cosponsor:={{{Cosponsor 33}}}]] |}}{{ #if: {{{Cosponsor 34|}}}|, [[Bill Cosponsor:={{{Cosponsor 34}}}]] |}}{{ #if: {{{Cosponsor 35|}}}|, [[Bill Cosponsor:={{{Cosponsor 35}}}]] |}}{{ #if: {{{Cosponsor 36|}}}|, [[Bill Cosponsor:={{{Cosponsor 36}}}]] |}}{{ #if: {{{Cosponsor 37|}}}|, [[Bill Cosponsor:={{{Cosponsor 37}}}]] |}}{{ #if: {{{Cosponsor 38|}}}|, [[Bill Cosponsor:={{{Cosponsor 38}}}]] |}}{{ #if: {{{Cosponsor 39|}}}|, [[Bill Cosponsor:={{{Cosponsor 39}}}]] |}}{{ #if: {{{Cosponsor 40|}}}|, [[Bill Cosponsor:={{{Cosponsor 40}}}]] |}}{{ #if: {{{Cosponsor 41|}}}|, [[Bill Cosponsor:={{{Cosponsor 41}}}]] |}}{{ #if: {{{Cosponsor 42|}}}|, [[Bill Cosponsor:={{{Cosponsor 42}}}]] |}}{{ #if: {{{Cosponsor 43|}}}|, [[Bill Cosponsor:={{{Cosponsor 43}}}]] |}}{{ #if: {{{Cosponsor 44|}}}|, [[Bill Cosponsor:={{{Cosponsor 44}}}]] |}}{{ #if: {{{Cosponsor 45|}}}|, [[Bill Cosponsor:={{{Cosponsor 45}}}]] |}}{{ #if: {{{Cosponsor 46|}}}|, [[Bill Cosponsor:={{{Cosponsor 46}}}]] |}}{{ #if: {{{Cosponsor 47|}}}|, [[Bill Cosponsor:={{{Cosponsor 47}}}]] |}}{{ #if: {{{Cosponsor 48|}}}|, [[Bill Cosponsor:={{{Cosponsor 48}}}]] |}}{{ #if: {{{Cosponsor 49|}}}|, [[Bill Cosponsor:={{{Cosponsor 49}}}]] |}}{{ #if: {{{Cosponsor 50|}}}|, [[Bill Cosponsor:={{{Cosponsor 50}}}]] |}}{{ #if: {{{Cosponsor 51|}}}|, [[Bill Cosponsor:={{{Cosponsor 51}}}]] |}}{{ #if: {{{Cosponsor 52|}}}|, [[Bill Cosponsor:={{{Cosponsor 52}}}]] |}}{{ #if: {{{Cosponsor 53|}}}|, [[Bill Cosponsor:={{{Cosponsor 53}}}]] |}}{{ #if: {{{Cosponsor 54|}}}|, [[Bill Cosponsor:={{{Cosponsor 54}}}]] |}}{{ #if: {{{Cosponsor 55|}}}|, [[Bill Cosponsor:={{{Cosponsor 55}}}]] |}}{{ #if: {{{Cosponsor 56|}}}|, [[Bill Cosponsor:={{{Cosponsor 56}}}]] |}}{{ #if: {{{Cosponsor 57|}}}|, [[Bill Cosponsor:={{{Cosponsor 57}}}]] |}}{{ #if: {{{Cosponsor 58|}}}|, [[Bill Cosponsor:={{{Cosponsor 58}}}]] |}}{{ #if: {{{Cosponsor 59|}}}|, [[Bill Cosponsor:={{{Cosponsor 59}}}]] |}}{{ #if: {{{Cosponsor 60|}}}|, [[Bill Cosponsor:={{{Cosponsor 60}}}]] |}}{{ #if: {{{Cosponsor 61|}}}|, [[Bill Cosponsor:={{{Cosponsor 61}}}]] |}}{{ #if: {{{Cosponsor 62|}}}|, [[Bill Cosponsor:={{{Cosponsor 62}}}]] |}}{{ #if: {{{Cosponsor 63|}}}|, [[Bill Cosponsor:={{{Cosponsor 63}}}]] |}}{{ #if: {{{Cosponsor 64|}}}|, [[Bill Cosponsor:={{{Cosponsor 64}}}]] |}}{{ #if: {{{Cosponsor 65|}}}|, [[Bill Cosponsor:={{{Cosponsor 65}}}]] |}}{{ #if: {{{Cosponsor 66|}}}|, [[Bill Cosponsor:={{{Cosponsor 66}}}]] |}}{{ #if: {{{Cosponsor 67|}}}|, [[Bill Cosponsor:={{{Cosponsor 67}}}]] |}}{{ #if: {{{Cosponsor 68|}}}|, [[Bill Cosponsor:={{{Cosponsor 68}}}]] |}}{{ #if: {{{Cosponsor 69|}}}|, [[Bill Cosponsor:={{{Cosponsor 69}}}]] |}}


==External Sources==
{{ #if: {{{ThomasID|}}}|* Thomas Official Information:[http://thomas.loc.gov/cgi-bin/bdquery/z?{{{ThomasID}}}:] [[Thomas Bill ID:={{{ThomasID}}}| ]] |}}
{{ #if: {{{GovTrackID|}}}|* GovTrack Bill Overview:[http://www.govtrack.us/congress/bill.xpd?bill={{{GovTrackID}}}] [[GovTrack Bill ID:={{{GovTrackID}}}| ]] |}} 
[[Category:Bill]]
</includeonly>
');
		//update some semnatic property types:
		$wgPropTitle = Title::newFromText('Data_Source_URL', SMW_NS_PROPERTY);
		do_update_wiki_page($wgPropTitle, '[[has type::URL]]');
		
		$wgPropTitle = Title::newFromText('Date_Bill_Introduced', SMW_NS_PROPERTY);
		do_update_wiki_page($wgPropTitle, '[[has type::Date]]');

	}
}
function upTemplate_person($force = false) {
	global $valid_attributes;
	$wgTemplateTitle = Title :: makeTitle(NS_TEMPLATE, 'Congress Person');
	if (!$wgTemplateTitle->exists() || $force) {
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
			//raw semantic data (@@todo make pretty template table thing)
			$template_body .= "{{ #if: {{{" . $name . "}}}| [[$name:={{{" . $name . "}}}| ]] |}} \n";
		}
		$template_body .= '[[Category:Congress Person]] [[Category:Person]]
				</includeonly>';
		echo "updated 'Congress Person' template\n";
		do_update_wiki_page($wgTemplateTitle, $template_body);
	}
}
function do_people_insert() {
	global $valid_attributes, $states_ary;
	$dbr = wfGetDB(DB_SLAVE);

	//check person
	upTemplate_person();
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
			if (trim($person-> $dbKey) != '') {
				if ($dbKey == 'state')
					$person->state = $states_ary[$person->state];
				$page_body .= "|{$name}={$person->$dbKey}|  \n";
			}
		}
		//add in the full name attribute: 
		$page_body .= "|Full Name=" . $person->title . ' ' . $person->first .
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
		$sql = "DELETE FROM `mv_stream_files` WHERE `stream_id`=".$mvStream->id;
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
				do_update_wiki_page($stream->name, mv_semantic_stream_desc($mvTitle, $stream), MV_NS_STREAM);
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
		$out .= '==More Media Sources=='."\n";	
		$out .= '*[[Archive.org]] hosted original copy ' .
		'[http://www.archive.org/details/mv_' . $stream->name . ']' . "\n";
	}
	//all streams have congretional cronical: 
	$out .= '*[[CSPAN]]\'s Congressional Chronicle ' .
	'[http://www.c-spanarchives.org/congress/?q=node/69850&date=' . $cspan_date . '&hors=' . $ch_type . ']';
	$out .= "\n\n";
	$out .= $pout;
	$out .= '[[stream_duration:=' . ($end_time - $start_time) . '| ]]' . "\n";
	if($stream->org_start_time){
		$out .= '[[original_date:='.$stream->org_start_time.'| ]]';
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
