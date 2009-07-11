<?php

$messages = array();

$messages['en'] = array(
	'indexfunc-desc' => 'Parser function to create automatic redirects and disambiguation pages',
	
	'indexfunc-badtitle' => 'Invalid title: "$1"',
	'indexfunc-editwarn' => 'Warning: This title is an index title for [[$1]].
Be sure the page you are about to create does not already exist under a different title.
If you create this page, remove this title from the <nowiki>{{#index:}}</nowiki> on $1.',
	'indexfunc-index-exists' => 'The page "$1" already exists',
	'indexfunc-index-taken' => '"$1" is already used as an index by "$2"',

	'index' => 'Index',
	'index-legend' => 'Search the index',
	'index-search' => 'Search: ',
	'index-submit' => 'Submit',
	'index-disambig-start' => "'''$1''' may refer to several pages:",
	'index-exclude-categories' => '', # List of categories to exclude from the auto-disambig pages
	'index-missing-param' => 'This page cannot be used with no parameters',
);
