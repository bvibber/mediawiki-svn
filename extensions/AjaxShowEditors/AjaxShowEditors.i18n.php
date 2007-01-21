<?php

global $wgAjaxShowEditorsMessages;
$wgAjaxShowEditorsMessages = array();

$wgAjaxShowEditorsMessages['en'] = array(
	'ajax-se-title' => 'Currently editing:',
	'ajax-se-pending' => 'pending refresh ... (click this box or start editing)',
	'ajax-se-idling' => '($1s ago)',
);
$wgAjaxShowEditorsMessages['fi'] = array(
	'ajax-se-title'   => 'Samanaikaiset muokkaajat:',
	'ajax-se-pending' => 'odotetaan päivitystä… (napsauta tästä tai aloita muokkaaminen)',
	'ajax-se-idling'  => '($1 s sitten)',
);
?>
