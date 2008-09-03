<?php
/**
 * Internationalisation file for DiscussionThreading extension.
 *
 * @addtogroup Extensions
*/

$wgDiscussionThreadMessages = array();

$wgDiscussionThreadMessages['en'] = array(
        'replysection' => 'reply',
        'replysectionhint' => "Reply to this Posting",
        'threadnewsection' => 'new',
        'threadnewsectionhint' => "Start a new thread"
);
$wgDiscussionThreadMessages['es'] = array(
        'replysection' => 'respuesta',
        'replysectionhint' => "Respuesta a este tema",
        'threadnewsection' => 'nuevo',
        'threadnewsectionhint' => "Empezar un nuevo tema"
);
$wgDiscussionThreadMessages['de'] = array(
        'replysection' => 'antworten',
        'replysectionhint' => "Auf diesen Eintrag antworten",
        'threadnewsection' => 'neu',
        'threadnewsectionhint' => "Neuen Eintrag erstellen"
);
/* Need to add 
	af,br,bs,ca,cs,cy,et,eu,fi,fr,ga,gl,he,hr,hsb,id,is,it,ja,kk-kz,kk-tr,kk-cn,kk,lv,nl,
	no,nn,oc,pt,pt-br,ro,ru,sk,sl,sq,uk,wa,zh-cn,zh-tw,zh-yue,zh-hk,zh-sg

	Would do this by adding a new $wgDiscussionThreadMessages array example:
	
$wgDiscussionThreadMessages['lang1'] = array(
	'replysection' => 'lang1tag',
	'replysectionhint' => "lang1hint",
);
*/
?>
