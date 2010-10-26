<?php
/*
*
*	If you want your extension to be used on wikis that have a multi-lingual readership, we will need to add internationalization support to the extension. 
*
*	1.For any text string displayed to the user, define a message. MediaWiki supports parameterized messages and that feature should be used when a message is dependent on information generated at runtime. Assign each message a lowercase message id.
*	2.In the setup and implementation code, replace each literal use of the message with a call to wfMsg( $msgID, $param1, $param2, ... ). Example : wfMsg( 'addition', '1', '2', '3' )
*	3.Store the message definition in the internalization file (WikiBhasha.i18n.php) . This is normally done by setting up an array that maps language and message id to each string. Each message id should be lowercase and they may not contain spaces
*
*
*/
$messages = array();

/**
 * English
 */
$messages['en'] = array(
	'wikibhasha' => 'WikiBhasha',
	'wikibhasha-desc' => "Extension's description",
	'wikiBhashaLink' => 'WikiBhasha(Beta)'
);

/**
 * German (Deutsch)
 */
$messages['de'] = array(
	'wikibhasha' => 'WikiBhasha',
	'wikibhasha-desc' => 'Beschreibung der Erweiterung',
	'wikiBhashaLink' => 'WikiBhasha(Beta)'
);
