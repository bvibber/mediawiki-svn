<?php
require_once ( '../../../maintenance/commandLine.inc' );

// include util functions:
require_once( 'maintenance_util.inc.php' );

if ( count( $args ) == 0 || isset ( $options['help'] ) ) {
	print<<<EOT
One time fixes to wiki content

Usage php metavid2mvWiki.php [options] action
options:
	--dry 			// will just print out operations that it will run
	--offset [val]	//start on a given offset (in case things don't finish

actions: 
	strip_speech_by  //strips extra speech by text
	update_stream_desc //updates stream desc

EOT;
	exit ();
}
$mvDryRun = ( isset( $options['dry'] ) ) ? true:false;

switch ( $args[0] ) {
	case 'strip_speech_by' :
		strip_speech_by();
	break;
	case 'update_stream_time_dur':
		update_stream_time_dur();
	break;
	case 'update_stream_desc':
		update_stream_desc();
	break;
}
function update_stream_desc(){
	/*==Official Record==
*[[GovTrack]] Congressional Record[http://www.govtrack.us/congress/recordindex.xpd?date=20080609&where=h]

*[[THOMAS]] Congressional Record [http://thomas.loc.gov/cgi-bin/query/B?r110:@FIELD(FLD003+h)+@FIELD(DDATE+20080609)]

*[[THOMAS]] Extension of Remarks [http://thomas.loc.gov/cgi-bin/query/B?r110:@FIELD(FLD003+h)+@FIELD(DDATE+20080609)]

==More Media Sources==
*[[CSPAN]]'s Congressional Chronicle [http://www.c-spanarchives.org/congress/?q=node/69850&date=2008-06-09&hors=h]
*[[Archive.org]] hosted original copy [http://www.archive.org/details/mv_house_proceeding_06-09-08_01]

===Full File Links===
*[[ao_file_MPEG2:=http://www.archive.org/download/mv_house_proceeding_06-09-08_01/house_proceeding_06-09-08_01.mpeg2|MPEG2]] (2.6 GB)
*[[ao_file_flash_flv:=http://www.archive.org/download/mv_house_proceeding_06-09-08_01/house_proceeding_06-09-08_01.flv|flash_flv]] 	 
	 */
	$dbr = wfGetDB( DB_SLAVE );
	//get all streams 
	$streams_res = $dbr->select('mv_streams','*');
	while($stream = $dbr->fetchObject( $streams_res )){
		//get stream text
		$streamTitle = Title::newFromText($stream->name, MV_NS_STREAM);
		$streamArticle = new Article($streamTitle);
		$cur_text = trim( $streamArticle->getContent() );
		$cur_text=preg_replace('/\*\[\[GovTrack\]\] Congressional Record\[([^\[]*)\]/',
						'*[$1 GovTrack Congressional Record]', $cur_text);
		
		$cur_text=preg_replace('/\*\[\[THOMAS\]\] Congressional Record \[([^\[]*)\]/',
						'*[$1 THOMAS Congressional Record]', $cur_text);
		
		$cur_text=preg_replace('/\*\[\[THOMAS\]\] Extension of Remarks \[([^\[]*)\]/', '*[$1 THOMAS Extension of Remarks]', $cur_text);
		
		$cur_text=preg_replace('/\*\[\[Archive.org\]\] hosted original copy \[([^\[]*)\]/','*[$1 Archive.org hosted original copy]', $cur_text);
		
		$cur_text=preg_replace('/\*\[\[CSPAN\]\]\'s Congressional Chronicle \[([^\[]*)\]/','*[$1 CSPAN Congressional Chronicle]', $cur_text);
		//do force update
		do_update_wiki_page( $streamTitle, $cur_text, MV_NS_STREAM, $force = true );
	}
	
	
	//update links
}

/*function update_stream_time_dur(){
	$streams_res = $dbr->select('mv_streams','*');
	while($stream = $dbr->fetchObject( $streams_res )){
		//check if we have the duration in the file: 
		$stream_files_res =  $dbr->select('mv_stream_files','*',array('stream_id'=>$stream->id));		
		while($stream_file = $dbr->fetchObject( $streams_res )){
			if($stream_file->duration!=0){
				$dur = $stream_file->duration;
				break;
			}else{
				$file_loc = $stream_file->path;
			}
		}
		
	}
}*/
function strip_speech_by() {
	global $mvDryRun;
	$dbr = wfGetDB( DB_SLAVE );
	$streams_res = $dbr->select( 'mv_mvd_index', '*',
					$conds = array( 'mvd_type' => 'Anno_en' ),
					$fname = 'strip_speech_by',
					$options = array( 'LIMIT' => 10000 ) );
	$inx = 0;
	while ( $mvd_row = $dbr->fetchObject( $streams_res ) ) {
		$mvdTitle = Title::newFromText( $mvd_row->wiki_title, MV_NS_MVD );
		$mvdArticle = new Article( $mvdTitle );
		$cur_text = trim( $mvdArticle->getContent() );
		// print "old text: "
		$st = 'Speech By:';
		if ( substr( $cur_text, 0, strlen( $st ) ) == $st ) {
			print "$inx :up: " . $mvd_row->wiki_title . "\n";
			$new_text = trim( substr( $cur_text, strlen( $st ) ) );
			// print "new text: $new_text\n";
			if ( !$mvDryRun )
				do_update_wiki_page( $mvdTitle, $new_text, MV_NS_MVD, $force = true );
		}
		$inx++;
	}
}
?>