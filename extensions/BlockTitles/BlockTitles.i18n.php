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
	'block_title_error' => 'Despensa, dai tinotogotan na itagama an titulo kaining artikulo.',
),

'br' => array(
	'block_title_error_page_title' => 'Titl direizh',
	'block_title_error' => 'Ho tigarez met n\'eo ket aotreet enrollañ titl ar pennad-mañ.',
),

'de' => array(
	'block_title_error_page_title' => 'Sperre Seitenname',
	'block_title_error'            => 'Entschuldigung, aber eine Seite mit diesem Namen darf nicht gespeichert werden.',
),

'el' => array(
	'block_title_error_page_title' => 'Παρεμποδισμένος τίτλος',
	'block_title_error' => 'Συγγνώμη, ο τίτλος αυτού του άρθρου δεν επιτρέπεται να αποθηκευτεί.',
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
	'block_title_error' => 'Dèsolâ, lo titro de cél articllo est pas ôtorisâ a étre sôvâ.',
),

'gl' => array(
	'block_title_error_page_title' => 'Título Bloqueado',
	'block_title_error' => 'Sentímolo, non se permite gardar o título deste artigo.',
),

'hsb' => array(
	'block_title_error_page_title' => 'Mjeno nastawka zablokowane',
	'block_title_error' => 'Wodaj, ale njeje dowolene nastawk z tutym mjenom składować.',
),

'hy' => array(
	'block_title_error_page_title' => 'Արգելափակված Անվանում',
	'block_title_error' => 'Ցավոք՝ այս հոդվածի անվանման հիշելը չի թույլատրվում։',
),

'it' => array(
	'block_title_error_page_title' => 'Titolo bloccato',
	'block_title_error' => 'Siamo spiacenti, non è consentito salvare una voce con questo titolo.',
),

'ja' => array(
	'block_title_error' => '申し訳ありませんが、この項目名で保存することはできません。',
),

'la' => array(
	'block_title_error_page_title' => 'Titulus obstructus',
	'block_title_error' => 'Paginam huius tituli servare non licet.',
),

'nl' => array(
	'block_title_error_page_title' => 'Geblokkeerde paginanaam',
	'block_title_error' => 'Sorry, het is niet toegestaan een pagina met deze naam op te slaan.',
),

'no' => array(
	'block_title_error_page_title' => 'Blokkert tittel',
	'block_title_error' => 'Beklager, tittelen på denne artikkelen kunne ikke lagres.',
),

'oc' => array(
	'block_title_error_page_title' => 'Títol invalid',
	'block_title_error' => 'O planhem, lo títol d\'aqueste article es pas autorizat a èsser salvagardat.',
),

'pl' => array(
	'block_title_error_page_title' => 'Zablokowany tytuł',
	'block_title_error' => 'Przepraszamy, zapisywanie tytułu tego artykułu jest niedozwolone.',
),

'pms' => array(
	'block_title_error_page_title' => 'Tìtol blocà',
	'block_title_error' => 'Ch\'a scusa, ma ës tìtol-sì as peul nen dovresse',
),

'scn' => array(
	'block_title_error_page_title' => 'Tìtulu bluccatu',
	'block_title_error' => 'Semu spiacenti, nun è cunzintitu sarvari na vuci cu chistu tìtulu.',
),

'sk' => array(
	'block_title_error_page_title' => '',
	'block_title_error' => 'Prepáčte, články s týmto názvom nie je povolené ukladať.',
),

'sv' => array(
	'block_title_error_page_title' => 'Blockerad titel',
	'block_title_error' => 'Det är inte tillåtet att spara en artikel under denna titel.',
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
