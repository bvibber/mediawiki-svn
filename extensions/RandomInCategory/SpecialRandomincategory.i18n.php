<?php

function efInitRandomincategoryMessages() {
	global $wgMessageCache;
	$m = array(
		'en' => array(
'randomincategory'                => 'Random page in category',
'randomincategory-toolbox'        => 'Random page',
'randomincategory-nocategory'     => 'Category $1 doesn\'t exist or is empty',
'randomincategory-label'          => 'Category:',
'randomincategory-submit'         => 'Go',
		),
	);
	
	foreach( $m as $lang => $messages ) {
		$wgMessageCache->addMessages( $messages, $lang );
	}
}