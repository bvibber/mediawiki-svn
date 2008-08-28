<?php
require_once ('../../../maintenance/commandLine.inc');

//include util functions:
require_once('maintenance_util.inc.php');

if (count($args) == 0 || isset ($options['help'])) {
	print<<<EOT
One time fixes to wiki content

Usage php metavid2mvWiki.php [options] action
options:
	--dry 			// will just print out operations that it will run
	--offset [val]	//start on a given offset (in case things don't finish

actions: 
	strip_speech_by  //strips extra speech by text

EOT;
	exit ();
}
$mvDryRun= (isset($options['dry']))?true:false;

switch ($args[0]) {
	case 'strip_speech_by' :
		strip_speech_by();
	break;
}

function strip_speech_by(){
	global $mvDryRun;
	$dbr = wfGetDB(DB_SLAVE);
	$streams_res = $dbr->select('mv_mvd_index','*', 
					$conds=array('mvd_type'=>'Anno_en'), 
					$fname = 'strip_speech_by', 
					$options = array('LIMIT'=>10000));
	$inx = 0;
	while($mvd_row = $dbr->fetchObject( $streams_res )){
		$mvdTitle = Title::newFromText($mvd_row->wiki_title, MV_NS_MVD);
		$mvdArticle = new Article($mvdTitle);
		$cur_text = trim($mvdArticle->getContent());
		//print "old text: "
		$st = 'Speech By:';
		if(substr($cur_text, 0, strlen($st) )==$st){
			print "$inx :up: " . $mvd_row->wiki_title. "\n";
			$new_text = trim(substr($cur_text, strlen($st)));
			//print "new text: $new_text\n";
			if(!$mvDryRun)
				do_update_wiki_page($mvdTitle, $new_text, MV_NS_MVD, $force=true);
		}
		$inx++;
	}	
}
?>