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
 function upTempalte_Ht_en($force = false) {
	$wgTemplateTitle = Title :: makeTitle(NS_TEMPLATE, 'Ht_en');
	if (!$wgTemplateTitle->exists() || $force) {
		do_update_wiki_page($wgTemplateTitle, '<noinclude>
		This is the default Template for the display of transcript text. 
		</noinclude><includeonly>{{ #if:  {{{PersonName|}}} | {{ #ifexist: Image:{{{PersonName}}}.jpg | [[Image:{{{PersonName}}}.jpg|44px|left]]|[[Image:Missing person.jpg|44px|left]]}} |}}{{ #if:{{{PersonName|}}}|[[{{{PersonName}}}]]: |}}{{{BodyText}}}
		</includeonly>');
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
