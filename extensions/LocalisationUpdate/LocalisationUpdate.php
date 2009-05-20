<?php
/*
KNOWN ISSUES:
- Only works with SVN revision 50605 or later of the
  Mediawiki core
*/

//Info about me!
$wgExtensionCredits['other'][] = array(
	'path'           => __FILE__,
	'name'           => 'LocalisationUpdate',
	'author'         => 'Tom Maaswinkel',
	'version'        => '0.1',
	'description'    => 'Extension to keep the localized messages as up to date as possible',
	'descriptionmsg' => 'localisationupdate-desc',
);

$wgExtensionMessagesFiles['LocalisationUpdate'] = dirname( __FILE__ ) . '/LocalisationUpdate.i18n.php';

//Use the right hook
$wgHooks['MessageNotInMwNs'][] = "FindUpdatedMessage";

//DB Search funtion
function FindUpdatedMessage(&$message,$lckey,$langcode,$isFullKey) {
	//Define a cache
	static $cache = array();
	$db = wfGetDB ( DB_SLAVE );

	// If the key also contains the language code remove the language code from the key
	if($isFullKey) {
		$lckey = preg_replace("/\/".$langcode."/","",$lckey);
	}

	//If message is in the cache, don't get an update!
	if(array_key_exists($lckey."/".$langcode,$cache)) {
		$message = $cache[$lckey."/".$langcode];
		return true;
	}

	//Get the message from the database
	$query = "select value from localisation where identifier = '".$db->strencode($lckey)."' and language = '".$db->strencode($langcode)."'";
	$result = $db->query($query); // Check if the database has any updated message
	if($db->numRows($result) == 0) { // If no results found, exit here
		return true;
	}

	$row = $db->fetchObject($result); // Get the result
	$message = $row->value; // And change the message variable
	$cache[$lckey."/".$langcode] = $message; //Update the cache
	return true;
}

//Called from the cronjob to fetch new messages from SVN
function updateMessages($verbose = false) {
	//Need this later
	global $wgExtensionMessagesFiles;

	//Prevent the script from timing out
	set_time_limit(0);
	ini_set("max_execution_time",0);

	//Update all MW core messages
	$result = updateMediawikiMessages($verbose);

	//Update all Extension messages
	foreach($wgExtensionMessagesFiles as $extension => $locFile) {
		$result += updateExtensionMessages($locFile,$extension,$verbose);
	}

	//And output the result!
	myLog("Updated {$result} messages in total");
	myLog("Done");
	return true;
}

//Update Extension Messages
function updateExtensionMessages($file,$extension,$verbose) {
	global $IP;

	//Define the wikimedia SVN path
	$wmSvnPath = 'svn.wikimedia.org/svnroot/mediawiki/';

	//Find the right SVN folder
	$svnFolder = SpecialVersion::getSvnRevision(dirname($file),false,false,true);

	//Create a full path
	if(!empty($IP)) {
		$localfile = $IP."/".$file;
	}

	//Get the full SVN directory path
	$svndir = "http://".$wmSvnPath.$svnFolder;

	//Compare the 2 files
	$result = compareExtensionFiles($extension,$svndir."/".basename($file),$file,$verbose,false,true);
	return $result;
}

//Update the Mediawiki Core Messages
function updateMediawikiMessages($verbose) {
	global $IP;

	//Define the wikimedia SVN path
	$wmSvnPath = 'svn.wikimedia.org/svnroot/mediawiki/';

	//Create an array which will later contain all the files that we want to try to update
	$files = array();

	//The directory which contains the files
	$dirname = "languages/messages";

	//Get the full path to the directory
	if(!empty($IP)) {
		$dirname = $IP."/".$dirname;
	}

	//Open the directory
	$dir = opendir( $dirname );
	while( false !== ( $file = readdir( $dir ) ) ) {
		$m = array();

		//And save all the filenames of files containing messages
		if( preg_match( '/Messages([A-Z][a-z_]+)\.php$/', $file, $m ) ) {
			if($m[1] != 'En') { //Except for the english one
				$files[] = $file;
			}
		}
	}
	closedir( $dir );

	//Get the SVN folder used for the checkout
	$svnFolder = SpecialVersion::getSvnRevision($dirname,false,false,true);

	//Do not update if not from SVN
	if(empty($svnFolder)) {
		myLog("Can't update localisation as the files are not retrieved from SVN");
		return 0;
	}

	//Get the full SVN Path
	$svndir = "http://".$wmSvnPath.$svnFolder;

	//Find the changed English strings (as these messages won't be updated in ANY language)
	$changedEnglishStrings = compareFiles($dirname."/MessagesEn.php",$svndir."/MessagesEn.php",$verbose);

	//Count the changes
	$changedCount = 0;

	//For each language
	foreach($files as $file) {
		$svnfile = $svndir."/".$file;
		$localfile = $dirname."/".$file;

		//Compare the files
		$result = compareFiles($svnfile,$localfile,$verbose,$changedEnglishStrings,false,true);

		//And update the change counter
		$changedCount += count($result);
	}

	//Log some nice info
	myLog("{$changedCount} Mediawiki messages are updated");
	return $changedCount;
}

//Remove all unneeded content
function cleanupFile($contents) {
	//We don't need any PHP tags
	$contents = preg_replace("/<\\?php/","",$contents);
	$contents = preg_replace("/\?>/","",$contents);
	$results = array();
	//And we only want the messages array
	preg_match("/\\\$messages(.*\s)*?\);/",$contents,$results);

	//If there is any!
	if(!empty($results[0])) {
		$contents = $results[0];
	} else {
		$contents = "";
	}

	//Windows vs Unix always stinks when comparing files
	$contents = preg_replace("/\\\r/","",$contents);

	//return the cleaned up file
	return $contents;
}

function compareFiles($basefile,$comparefile,$verbose,$forbiddenKeys = array(), $alwaysGetResult = true, $saveResults = false) {
	//We need to write to the DB later
	$db = wfGetDB ( DB_MASTER );

	$compare_messages = array();
	$base_messages = array();

	//Get the languagecode
	$m = array();
	preg_match( '/Messages([A-Z][a-z_]+)\.php$/', $basefile, $m );
	$langcode = strtolower($m[1]);

	//use cURL to get the SVN contents
	if(preg_match("/^http/",$basefile)) {
		$basefilecontents = Http::get($basefile);
		if(empty($basefilecontents)) {
			myLog("Can't get the contents of ".$basefile." (curl)");
			return array();
		}
	} else {//otherwise try file_get_contents
		if(!$basefilecontents = file_get_contents($basefile)) {
			myLog("Can't get the contents of ".$basefile);
			return array();
		}
	}

	$basehash = "";
	$comparehash = "";

	//Only get the part we need
	$basefilecontents = cleanupFile($basefilecontents);

	//Change the variable name
	$basefilecontents = preg_replace("/\\\$messages/","\$base_messages",$basefilecontents);

	$basehash = md5($basefilecontents);
	//If this is the remote file check if the file has changed since our last update
	if(preg_match("/^http/",$basefile) && !$alwaysGetResult) {
		$result = $db->query("select * from localisation_file_hash where file = '".$db->strencode($basefile)."' and hash = '{$basehash}'");
		if($db->numRows($result) == 0) { //If it did, save the new file hash
			$db->query("delete from localisation_file_hash where file = '".$db->strencode($basefile)."'");
			$db->query("insert into localisation_file_hash (file,hash) values ('".$db->strencode($basefile)."','{$basehash}')");
		} else {
			myLog("Skipping {$langcode} since the remote file hasn't changed since our last update");
			myLog("");
			return array();
		}
	}

	//Get the array with messages
	eval($basefilecontents);

	//use cURL to get the contents
	if(preg_match("/^http/",$comparefile)) {
		$comparefilecontents = Http::get($comparefile);
		if(empty($comparefilecontents)) {
			myLog("Can't get the contents of ".$comparefile." (curl)");
			return array();
		}
	} else { //otherwise use file_get_contents
		if(!$comparefilecontents = file_get_contents($comparefile)) {
			myLog("Can't get the contents of ".$comparefile);
			return array();
		}
	}

	//only get the stuff we need
	$comparefilecontents = cleanupFile($comparefilecontents);

	//rename the array
	$comparefilecontents = preg_replace("/\\\$messages/","\$compare_messages",$comparefilecontents);

	$comparehash = md5($comparefilecontents);
	//If this is the remote file check if the file has changed since our last update
	if(preg_match("/^http/",$comparefile) && !$alwaysGetResult) {
		$result = $db->query("select * from localisation_file_hash where file = '".$db->strencode($comparefile)."' and hash = '{$comparehash}'");
		if($db->numRows($result) == 0) {//If it did, save the new file hash
			$db->query("delete from localisation_file_hash where file = '".$db->strencode($comparefile)."'");
			$db->query("insert into localisation_file_hash (file,hash) values ('".$db->strencode($comparefile)."','{$comparehash}')");
		} else {
			myLog("Skipping {$langcode} since the remote file hasn't changed since our last update");
			myLog("");
			return array();
		}
	}
	//Get the array
	eval($comparefilecontents);


	//if the localfile and the remote file are the same, skip them!
	if($basehash == $comparehash && !$alwaysGetResult) {
		myLog("Skipping {$langcode} since the remote file is the same as the local file");
		myLog("");
		return array();
	}

	//Add the messages we got with our previous update(s) to the local array (as we already got these as well)
	$result = $db->query("select identifier,value from localisation where language = '".$db->strencode($langcode)."'");
	while($resObj = $db->fetchObject($result)) {
		$compare_messages[$resObj->identifier] = $resObj->value;
	}

	//Compare the remote and local message arrays
	$changedStrings = array_diff_assoc($base_messages,$compare_messages);

	//If we want to save the differences
	if($saveResults) {
		myLog("--Checking languagecode {$langcode}--");
		//The save them
		$updates = saveChanges($changedStrings,$forbiddenKeys,$base_messages,$langcode);
		myLog("{$updates} messages updated for {$langcode}.");

		myLog("");
	}

	return $changedStrings;
}


function saveChanges($changedStrings,$forbiddenKeys,$base_messages,$langcode) {
	global $verbose;

	//Gonna write to the DB again
	$db = wfGetDB ( DB_MASTER );

	//Count the updates
	$updates = 0;
	foreach($changedStrings as $key => $value) {
		//If this message wasn't changed in english
		if(!array_key_exists($key , $forbiddenKeys )){
			//See if we can update the database
			$db->query("update localisation set value = '".$db->strencode($base_messages[$key])."' where language = '".$db->strencode($langcode)."' and identifier like '".$db->strencode($key)."'");
			if($db->affectedRows() == 0) { //Otherwise do a new insert
				$db->query("insert into localisation (value, language, identifier) values ('".$db->strencode($base_messages[$key])."', '".$db->strencode($langcode)."','".$db->strencode($key)."')");
				if($db->affectedRows() == 0) {
					throw new MWException( "An error has occured while inserting a new message to the database to the database!" );
				}
			}

			//Output extra logmessages when needed
			if($verbose) {
				myLog("Updated message {$key} from {$compare_messages[$key]} to {$base_messages[$key]}");
			}

			//Update the counter
			$updates++;
		}
	}
	return $updates;
}

function cleanupExtensionFile($contents) {
	//We don't want PHP tags
	$contents = preg_replace("/<\?php/","",$contents);
	$contents = preg_replace("/\?>/","",$contents);
	$results = array();
	//And we only want message arrays
	preg_match_all("/\\\$messages(.*\s)*?\);/",$contents,$results);
	//But we want them all in one string
	$contents = implode("\n\n",$results[0]);

	//And we hate the windows vs linux linebreaks
	$contents = preg_replace("/\\\r/","",$contents);
	return $contents;
}

function compareExtensionFiles($extension,$basefile,$comparefile,$verbose, $alwaysGetResult = true, $saveResults = false) {
	//Let's mess with the database again
	$db = wfGetDB ( DB_MASTER );
	$compare_messages = array();
	$base_messages = array();

	if(preg_match("/^http/",$basefile)) {
		$basefilecontents = Http::get($basefile);
		if(empty($basefilecontents)) {
			myLog("Can't get the contents of ".$basefile." (curl)");
			return 0;
		}
	} else { //or otherwise file _get_contents
		if(!$basefilecontents = file_get_contents($basefile)) {
			myLog("Can't get the contents of ".$basefile);
			return 0;
		}
	}

	$basehash = "";
	$comparehash = "";

	//Cleanup the file where needed
	$basefilecontents = cleanupExtensionFile($basefilecontents);

	//Rename the arrays
	$basefilecontents = preg_replace("/\\\$messages/","\$base_messages",$basefilecontents);

	$basehash = md5($basefilecontents);
	//If this is the remote file
	if(preg_match("/^http/",$basefile) && !$alwaysGetResult) {
		//Check if the hash has changed
		$result = $db->query("select * from localisation_file_hash where file = '".$db->strencode($basefile)."' and hash = '{$basehash}'");
		if($db->numRows($result) == 0) {
			$db->query("delete from localisation_file_hash where file = '".$db->strencode($basefile)."'");
			$db->query("insert into localisation_file_hash (file,hash) values ('".$db->strencode($basefile)."','{$basehash}')");
		} else {
			myLog("Skipping {$extension} since the remote file hasn't changed since our last update");
			myLog("");
			return 0;
		}
	}

	//And get the real contents
	eval($basefilecontents);

	//Use cURL when available
	if(preg_match("/^http/",$comparefile)) {
		$comparefilecontents = Http::get($comparefile);
		if(empty($comparefilecontents)) {
			myLog("Can't get the contents of ".$comparefile." (curl)");
			return 0;
		}
	} else { //Otherwise use file_get_contents
		if(!$comparefilecontents = file_get_contents($comparefile)) {
			myLog("Can't get the contents of ".$comparefile);
			return 0;
		}
	}

	//Only get what we need
	$comparefilecontents = cleanupExtensionFile($comparefilecontents);

	//Rename the array
	$comparefilecontents = preg_replace("/\\\$messages/","\$compare_messages",$comparefilecontents);
	$comparehash = md5($comparefilecontents);
	if(preg_match("/^http/",$comparefile) && !$alwaysGetResult) {
		//Check if the remote file has changed
		$result = $db->query("select * from localisation_file_hash where file = '".$db->strencode($comparefile)."' and hash = '{$comparehash}'");
		if($db->numRows($result) == 0) {//If so, save the new hash
			$db->query("delete from localisation_file_hash where file = '".$db->strencode($comparefile)."'");
			$db->query("insert into localisation_file_hash (file,hash) values ('".$db->strencode($comparefile)."','{$comparehash}')");
		} else {
			myLog("Skipping {$extension} since the remote file hasn't changed since our last update");
			myLog("");
			return 0;
		}
	}
	//Get the real array
	eval($comparefilecontents);

	//If both files are the same, they can be skipped
	if($basehash == $comparehash && !$alwaysGetResult) {
		myLog("Skipping {$extension} since the remote file is the same as the local file");
		myLog("");
		return 0;
	}

	//Update counter
	$updates = 0;

	if(empty($base_messages['en'])) {
		$base_messages['en'] = array();
	}

	if(empty($compare_messages['en'])) {
		$compare_messages['en'] = array();
	}

	//Find the changed english strings
	$forbiddenKeys = array_diff_assoc($base_messages['en'],$compare_messages['en']);

	//Do an update for each language
	foreach($base_messages as $language => $messages) {
		if($language == "en") { //Skip english
			continue;
		}

		//Add the already known messages to the array so we will only find new changes
		$result = $db->query("select identifier,value from localisation where language = '".$db->strencode($language)."'");
		while($resObj = $db->fetchObject($result)) {
			$compare_messages[$language][$resObj->identifier] = $resObj->value;
		}

		if(empty($compare_messages[$language])) {
			$compare_messages[$language] = array();
		}

		//Get the array of changed strings
		$changedStrings = array_diff_assoc($messages,$compare_messages[$language]);

		//If we want to save the changes
		if($saveResults) {
			myLog("--Checking languagecode {$language} for extension {$extension}--");
			//Do really save the changes
			$updates += saveChanges($changedStrings,$forbiddenKeys,$messages,$language);
		}
	}

	//And log some stuff
	myLog("Updated ".$updates." messages for the '{$extension}' extension");
	myLog("");

	return $updates;
}
