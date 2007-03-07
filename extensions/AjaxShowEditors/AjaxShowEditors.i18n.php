<?php

global $wgAjaxShowEditorsMessages;
$wgAjaxShowEditorsMessages = array();

$wgAjaxShowEditorsMessages['en'] = array(
	'ajax-se-title' => 'Currently editing:',
	'ajax-se-pending' => 'pending refresh ... (click this box or start editing)',
	'ajax-se-idling' => '($1s ago)',
);
$wgAjaxShowEditorsMessages['de'] = array(
	'ajax-se-title'   => 'Wird zur Zeit bearbeitet von:',
	'ajax-se-pending' => 'bevorstehende Aktualisierungen… (klicke auf diesen Kasten oder beginne mit der Bearbeitung)',
	'ajax-se-idling'  => '(vor $1 s)',
);
$wgAjaxShowEditorsMessages['fi'] = array(
	'ajax-se-title'   => 'Samanaikaiset muokkaajat:',
	'ajax-se-pending' => 'odotetaan päivitystä… (napsauta tästä tai aloita muokkaaminen)',
	'ajax-se-idling'  => '($1 s sitten)',
);
$wgAjaxShowEditorsMessages['id'] = array(
	'ajax-se-title'   => 'Sedang menyunting:',
	'ajax-se-pending' => 'pemuatan ulang ditunda ... (klik kotak ini atau mulai menyunting)',
	'ajax-se-idling'  => '($1d lalu)',
);
$wgAjaxShowEditorsMessages['nl'] = array(
	'ajax-se-title' => 'Bezig met het bewerken van:',
	'ajax-se-pending' => 'moet nog bijgewerkt worden ... (klik op deze knop of begin met bewerken)',
	'ajax-se-idling' => '($1s geleden)',
);
$wgAjaxShowEditorsMessages['zh-cn'] = array(
	'ajax-se-title' => '当前编辑中:',
	'ajax-se-pending' => '正在正在刷新 ... (单击这个框或开始编辑)',
	'ajax-se-idling' => '($1秒前)',
);
$wgAjaxShowEditorsMessages['zh-tw'] = array(
	'ajax-se-title' => '目前編輯中:',
	'ajax-se-pending' => '正在重新整理 ... (按一下這個框開始編輯)',
	'ajax-se-idling' => '($1秒前)',
);
$wgAjaxShowEditorsMessages['zh-yue'] = array(
	'ajax-se-title' => '而家編輯緊:',
	'ajax-se-pending' => '而家重新整理緊 ... (click this box or start editing)',
	'ajax-se-idling' => '($1秒前)',
);
$wgAjaxShowEditorsMessages['zh-hk'] = $wgAjaxShowEditorsMessages['zh-tw'];
$wgAjaxShowEditorsMessages['zh-sg'] = $wgAjaxShowEditorsMessages['zh-cn'];
?>
