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
 *
 *
 * Maintenance Utility Functions: 
 */
 
 /*
 * set up the bot user:
 */
$botUserName = 'MvBot';
$wgUser = User::newFromName( $botUserName );
if ( !$wgUser ) {
	print "Invalid username\n";
	exit( 1 );
}
if ( $wgUser->isAnon() ) {
	$wgUser->addToDatabase();
}
 //returns true if person found in category person: 
 $mv_valid_people_cache = array();
function mv_is_valid_person($person_key){
	global $mv_valid_people_cache;
	if(isset($mv_valid_people_cache[$person_key])){
		return $mv_valid_people_cache[$person_key];
	}
	$dbr = wfGetDB(DB_SLAVE);
	$result = $dbr->select( 'categorylinks', 'cl_sortkey', 
			array('cl_to'=>'Person', 
			'cl_sortkey'=>str_replace('_',' ',$person_key)),
			__METHOD__,
			array('LIMIT'=>'1'));
	if($dbr->numRows($result)!= 0){
		$mv_valid_people_cache[$person_key]=true;
	}else{
		$mv_valid_people_cache[$person_key]=false;
	}	
	return $mv_valid_people_cache[$person_key];
}
function do_update_wiki_page($wgTitle, $wikiText, $ns = null, $forceUpdate=false) {
	global $botUserName;
	if (!is_object($wgTitle)) {	
		//get the title and make sure the first letter is uper case 
		$wgTitle = Title::makeTitle($ns, ucfirst($wgTitle));
	}
	//print "INSERT BODY: ".$wikiText;
	//make sure the text is utf8 encoded: 
	$wikiText = utf8_encode($wikiText);
	
	$wgArticle = new Article($wgTitle);
	if(!mvDoMvPage($wgTitle, $wgArticle, false)){
		print "bad title: ".$wgTitle->getDBkey()." no edit";
		if($wgTitle->exists()){
			print "remove article";			
			$wgArticle->doDeleteArticle( 'bad title' );		
		}
		//some how mvdIndex and mvd pages got out of sync do a seperate check for the mvd: 
		if(MV_Index::getMVDbyTitle($wgArticle->mTitle->getDBkey())!=null){
			print ', rm mvd';
			MV_Index::remove_by_wiki_title($wgArticle->mTitle->getDBkey());			
		}
		print "\n";
		return ;		
	}		
	if ($wgTitle->exists()) {			
		//if last edit!=mvBot skip (don't overwite peoples improvments') 
		$rev = & Revision::newFromTitle($wgTitle);
		if( $botUserName!= $rev->getRawUserText()){
			print ' skiped page edited by user:'.$rev->getRawUserText()." != $botUserName \n";
			if(!$forceUpdate)return ;
		}
		//proc article:		
		$cur_text = $wgArticle->getContent();
		//if its a redirect skip
		if(substr($cur_text, 0, strlen('#REDIRECT') )=='#REDIRECT'){
			print ' skiped page moved by user:'.$rev->getRawUserText()."\n";
			if(!$forceUpdate)return ;
		}
		//check if text is identical: 		
		if (trim($cur_text) == trim($wikiText)) {
			print "text is identical (no update)\n";					
			if(!$forceUpdate)return ;
		}
	}
	//got here do the edit: 	
	$sum_txt = 'metavid bot insert';	
	$wgArticle->doEdit($wikiText, $sum_txt);
	print "did edit on " . $wgTitle->getDBkey() . "\n";
	//die;
}
?>
