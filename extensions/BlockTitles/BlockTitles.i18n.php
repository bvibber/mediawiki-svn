<?php

/**
 * Messages file for the BlockTitles extension
 */
 
/**
 * Fetch extension messages
 *
 * @return array
 */
function efBlockTitlesMessages() {
	$messages = array(
	
/**
 * English (Travis Derouin)
 */
'en' => array(
	'block_title_error_page_title' => 'Blocked Title',
	'block_title_error' => 'Sorry, the title of this article is not allowed to be saved.',
),
 
'ar' => array(
	'block_title_error_page_title' => 'عنوان ممنوع',
	'block_title_error' => 'عذرا، عنوان هذه المقالة غير مسموح بحفظه.',
),

'bcl' => array(
	'block_title_error_page_title' => 'Pigbágat na Titulo',
),

'de' => array(
	'block_title_error_page_title' => 'Sperre Artikelname',
	'block_title_error'            => 'Entschuldigung, aber ein Artikel mit diesem Namen darf nicht gespeichert werden.',
),

/**
 * French (Bertrand Grondin)
 */
'fr' => array(
	'block_title_error_page_title' => 'Titre invalide',		
	'block_title_error' => 'Désolé, le titre de cet article n’est pas autorisé à être sauvegardé.',
),

'frp' => array(
	'block_title_error_page_title' => 'Titro envalido',
	'block_title_error' => 'Dèsolâ, lo titro de cél articllo est pas ôtorisâ a étre sôvo.',
),

'hsb' => array(
	'block_title_error_page_title' => 'Mjeno nastawka zablokowane',
	'block_title_error' => 'Wodaj, ale njeje dowolene nastawk z tutym mjenom składować.',
),

'nl' => array(
	'block_title_error_page_title' => 'Geblokkeerde paginanaam',
	'block_title_error' => 'Sorry, het is niet toegestaan een pagina met deze naam op te slaan.',
),

'oc' => array(
	'block_title_error_page_title' => 'Títol invalid',
	'block_title_error' => 'O planhem, lo títol d\'aqueste article es pas autorizat a èsser salvagardat.',
),

/**
 * 
 * Cantonese (Shinjiman)
 */
'yue' => array(
	'block_title_error_page_title' => '已經封鎖嘅標題',		
	'block_title_error' => '對唔住，呢篇文章嘅標題唔容許保存。',
),

/**
 * Chinese (Simplified) (Shinjiman)
 */
'zh-hans' => array(
	'block_title_error_page_title' => '已封锁的标题',		
	'block_title_error' => '抱歉，这篇文章的标题不允许保存。',
),

/**
 * Chinese (Traditional) (Shinjiman)
 */
'zh-hant' => array(
	'block_title_error_page_title' => '已封鎖的標題',		
	'block_title_error' => '抱歉，這篇文章的標題不允許保存。',
),
	
	);

	/* Chinese defaults, fallback to zh-hans or zh-hant */
	$messages['zh'] = $messages['zh-hans'];
	$messages['zh-cn'] = $messages['zh-hans'];
	$messages['zh-hk'] = $messages['zh-hant'];
	$messages['zh-tw'] = $messages['zh-hans'];
	$messages['zh-sg'] = $messages['zh-hant'];
	/* Cantonese default, fallback to yue */
	$messages['zh-yue'] = $messages['yue'];

	return $messages;	
}
