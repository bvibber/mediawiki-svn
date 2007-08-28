<?php

/**
 * Messages file for the Contributionseditcount extension
 */
 
function efContributionseditcountMessages()  {
	$messages = array(
	
/* English */
'en' => array(
	'contributionseditcount'  =>  'This user has $1 edits.',
),

'de' => array(
	'contributionseditcount'  =>  'Dieser Benutzer hat $1 Bearbeitungen.',
),

'fr' => array(
	'contributionseditcount'  =>  'Cet utilisateur a $1 éditions.',
),

'nl' => array(
	'contributionseditcount'  =>  'Deze gebruiker heeft $1 bewerkingen.',
),

'yue' => array(
	'contributionseditcount'  =>  '呢個用戶有 $1 次編輯。',
),

'zh-hans' => array(
	'contributionseditcount'  =>  '这个用户拥有 $1 次编辑。',
),

'zh-hant' => array(
	'contributionseditcount'  =>  '這個用戶擁有 $1 次編輯。',
),

	);

	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-sg'] = $messages['zh-hans'];
	$messages['zh-tw'] = $messages['zh-hant'];
	$messages['zh-yue'] = $messages['yue'];

	return $messages;
}
