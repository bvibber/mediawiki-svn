<?php

$queueDir = "/var/spool/checkin-test-trigger";
$targetRepo = "http://svn.wikimedia.org/svnroot/mediawiki";
$testSuites = array(
	array(
		'suite' => 'ParserTests',
		'path' => '/trunk/phase3',
		'localpath' => "/home/parsertests/phase3",
		'command' => 'php maintenance/parserTests.php --upload',
	),
	/*
	// When they gain the ability... :)
	'UnitTests' => array(
		'name' => 'UnitTests',
		'path' => '/trunk/phase3',
		'localpath' => "$base/phase3",
		'command' => 'cd tests && make test',
	),
	*/
);

