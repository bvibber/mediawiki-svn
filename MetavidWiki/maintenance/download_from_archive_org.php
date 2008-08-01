<?php
$cur_path = $IP = dirname(__FILE__);
//include commandLine.inc from the mediaWiki maintance dir: 
require_once ('../../../maintenance/commandLine.inc');

$mvDownloadDir = '/tmp/';

if (count($args) == 0 || isset ($options['help'])) {
	print "
	Downloads files from archive.org to configured directory:{$mvDownloadDir}

	options keyword: 
		all 		  //to get all
		[stream_name] //to grab a specifc stream
	";
}
proccess_streams($args[0]);

function proccess_streams($stream_name='all'){
	if($stream_name=='all'){
		$sql = "SELECT * FROM `mv_streams` LIMIT 0, 5000";
	}else{
		$sql = "SELECT * FROM `mv_streams` WHERE `name` ={$stream_name}";
	}
	$dbr = wfGetDB(DB_READ);
	$result = $dbr->query($sql);
	while($stream = $dbr->fetchObject($result) ){
		print_r($stream);
	}
	
}


?>