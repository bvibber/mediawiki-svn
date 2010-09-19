<?php
/**
 * Aliases for special pages
 *
 * @file
 * @ingroup Extensions
 */

$specialPageAliases = array();

/** English (English) */
$specialPageAliases['en'] = array(
	'SignDocument' => array( 'SignDocument' ),
	'CreateSignDocument' => array( 'CreateSignDocument' ),
);

/** Afrikaans (Afrikaans) */
$specialPageAliases['af'] = array(
	'SignDocument' => array( 'TekenDokument' ),
	'CreateSignDocument' => array( 'SkepTekenDokument' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'SignDocument' => array( 'توقيع_الوثيقة' ),
	'CreateSignDocument' => array( 'إنشاء_توقيع_الوثيقة' ),
);

/** Egyptian Spoken Arabic (مصرى) */
$specialPageAliases['arz'] = array(
	'SignDocument' => array( 'امضى_الوثيقه' ),
	'CreateSignDocument' => array( 'اعمل_امضة_الوثيقه' ),
);

/** Bosnian (Bosanski) */
$specialPageAliases['bs'] = array(
	'SignDocument' => array( 'PotpisivanjeDokumenta' ),
);

/** German (Deutsch) */
$specialPageAliases['de'] = array(
	'SignDocument' => array( 'Dokument signieren' ),
	'CreateSignDocument' => array( 'Signiertes Dokument erstellen' ),
);

/** Lower Sorbian (Dolnoserbski) */
$specialPageAliases['dsb'] = array(
	'SignDocument' => array( 'Dokument signěrowaś' ),
	'CreateSignDocument' => array( 'Signěrowany dokument napóraś' ),
);

/** Swiss German (Alemannisch) */
$specialPageAliases['gsw'] = array(
	'SignDocument' => array( 'Dokumänt signiere' ),
	'CreateSignDocument' => array( 'Signiert Dokumänt aalege' ),
);

/** Upper Sorbian (Hornjoserbsce) */
$specialPageAliases['hsb'] = array(
	'SignDocument' => array( 'Dokument signować' ),
	'CreateSignDocument' => array( 'Signowany dokument wutworić' ),
);

/** Interlingua (Interlingua) */
$specialPageAliases['ia'] = array(
	'SignDocument' => array( 'Signar documento' ),
	'CreateSignDocument' => array( 'Activar signatura de documentos' ),
);

/** Italian (Italiano) */
$specialPageAliases['it'] = array(
	'SignDocument' => array( 'FirmaDocumento' ),
);

/** Japanese (日本語) */
$specialPageAliases['ja'] = array(
	'SignDocument' => array( '文書署名' ),
	'CreateSignDocument' => array( '文書署名の有効化' ),
);

/** Colognian (Ripoarisch) */
$specialPageAliases['ksh'] = array(
	'SignDocument' => array( 'Dokemänt ongerschriive' ),
	'CreateSignDocument' => array( 'Ongerschrevve Dokemänt aanlääje' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'SignDocument' => array( 'Dokument_ënnerschreiwen' ),
	'CreateSignDocument' => array( 'Dokument_ënnerschreiwen_erméiglechen' ),
);

/** Lumbaart (Lumbaart) */
$specialPageAliases['lmo'] = array(
	'SignDocument' => array( 'FirmaDocüment' ),
);

/** Malayalam (മലയാളം) */
$specialPageAliases['ml'] = array(
	'SignDocument' => array( 'പ്രമാണത്തിലൊപ്പിടുക' ),
	'CreateSignDocument' => array( 'പ്രമാണത്തിലൊപ്പിടൽ സൃഷ്ടിക്കുക' ),
);

/** Marathi (मराठी) */
$specialPageAliases['mr'] = array(
	'SignDocument' => array( 'सहीदस्त' ),
	'CreateSignDocument' => array( 'दस्तसहीनर्मितीकरा' ),
);

/** Nedersaksisch (Nedersaksisch) */
$specialPageAliases['nds-nl'] = array(
	'SignDocument' => array( 'Dokement_ondertekenen' ),
	'CreateSignDocument' => array( 'Dokement_ondertekenen_anmaken' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'SignDocument' => array( 'DocumentOndertekenen' ),
	'CreateSignDocument' => array( 'DocumentOndertekenenAanmaken' ),
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬) */
$specialPageAliases['no'] = array(
	'SignDocument' => array( 'Signer dokument' ),
	'CreateSignDocument' => array( 'Opprett signaturdokument' ),
);

/** Pashto (پښتو) */
$specialPageAliases['ps'] = array(
	'SignDocument' => array( 'لاسوند لاسليک کول' ),
);

/** Slovak (Slovenčina) */
$specialPageAliases['sk'] = array(
	'SignDocument' => array( 'PodpísaťDokument' ),
	'CreateSignDocument' => array( 'VytvoriťPodpísanýDokument' ),
);

/** Swedish (Svenska) */
$specialPageAliases['sv'] = array(
	'SignDocument' => array( 'Signera dokument' ),
	'CreateSignDocument' => array( 'Skapa signeringsdokument' ),
);

/** Swahili (Kiswahili) */
$specialPageAliases['sw'] = array(
	'SignDocument' => array( 'HatiKusaini' ),
	'CreateSignDocument' => array( 'UndaHatizaKusaini' ),
);

/** Thai (ไทย) */
$specialPageAliases['th'] = array(
	'SignDocument' => array( 'เซ็นเอกสาร' ),
);

/** Tagalog (Tagalog) */
$specialPageAliases['tl'] = array(
	'SignDocument' => array( 'Lagdaan ang dokumento' ),
	'CreateSignDocument' => array( 'Likhain ang paglagda sa kasulatan' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;