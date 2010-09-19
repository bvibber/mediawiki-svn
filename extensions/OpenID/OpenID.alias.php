<?php
/**
 * Aliases for special pages
 *
 * @file
 * @ingroup Extensions
 */

$specialPageAliases = array();

/** English
 * @author IAlex
 */
$specialPageAliases['en'] = array(
	'OpenIDLogin' => array( 'OpenIDLogin' ),
	'OpenIDConvert' => array( 'OpenIDConvert' ),
	'OpenIDServer' => array( 'OpenIDServer' ),
	'OpenIDXRDS' => array( 'OpenIDXRDS' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'OpenIDLogin' => array( 'دخول_الهوية_المفتوحة' ),
	'OpenIDConvert' => array( 'تحويل_الهوية_المفتوحة' ),
	'OpenIDServer' => array( 'خادم_الهوية_المفتوحة' ),
	'OpenIDXRDS' => array( 'إكسردس_الهوية_المفتوحة' ),
);

/** Egyptian Spoken Arabic (مصرى) */
$specialPageAliases['arz'] = array(
	'OpenIDLogin' => array( 'دخول_Open_ID' ),
	'OpenIDConvert' => array( 'تحويل_Open_ID' ),
	'OpenIDServer' => array( 'خادم_Open_ID' ),
	'OpenIDXRDS' => array( 'إكس_آر_دى_إس_Open_ID' ),
);

/** Finnish (Suomi) */
$specialPageAliases['fi'] = array(
	'OpenIDLogin' => array( 'OpenID-kirjautuminen' ),
	'OpenIDConvert' => array( 'OpenID-muunnos' ),
	'OpenIDServer' => array( 'OpenID-palvelin' ),
);

/** Interlingua (Interlingua) */
$specialPageAliases['ia'] = array(
	'OpenIDLogin' => array( 'Aperir_session_OpenID' ),
	'OpenIDConvert' => array( 'Converter_a_OpenID' ),
	'OpenIDServer' => array( 'Servitor_OpenID' ),
	'OpenIDXRDS' => array( 'XRDS_OpenID' ),
);

/** Japanese (日本語) */
$specialPageAliases['ja'] = array(
	'OpenIDLogin' => array( 'OpenIDログイン', 'ＯｐｅｎＩＤログイン' ),
	'OpenIDConvert' => array( 'OpenID変換', 'ＯｐｅｎＩＤ変換' ),
	'OpenIDServer' => array( 'OpenIDサーバー', 'ＯｐｅｎＩＤサーバー' ),
);

/** Korean (한국어) */
$specialPageAliases['ko'] = array(
	'OpenIDLogin' => array( 'OpenID로그인' ),
	'OpenIDConvert' => array( 'OpenID번역' ),
	'OpenIDServer' => array( 'OpenID서버' ),
);

/** Colognian (Ripoarisch) */
$specialPageAliases['ksh'] = array(
	'OpenIDLogin' => array( 'OpenIDAanmedung' ),
	'OpenIDConvert' => array( 'OpenIDÖmwandele' ),
);

/** Ladino (Ladino) */
$specialPageAliases['lad'] = array(
	'OpenIDLogin' => array( 'EntradaConIdentidadAvierta' ),
	'OpenIDConvert' => array( 'MudarIdentidadAvierta' ),
	'OpenIDServer' => array( 'Servidor_de_identidad_avierta' ),
	'OpenIDXRDS' => array( 'IDXRDS_Avierta' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'OpenIDLogin' => array( 'Login_fir_OpenID' ),
	'OpenIDConvert' => array( 'OpenID_ëmwandelen' ),
);

/** Malayalam (മലയാളം) */
$specialPageAliases['ml'] = array(
	'OpenIDLogin' => array( 'ഓപ്പൺഐ.ഡി.പ്രവേശനം' ),
	'OpenIDServer' => array( 'ഓപ്പൺഐ.ഡി.സെർവർ' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'OpenIDLogin' => array( 'OpenIDAanmelden' ),
	'OpenIDConvert' => array( 'OpenIDConverteren' ),
);

/** Norwegian (bokmål)‬ (‪Norsk (bokmål)‬) */
$specialPageAliases['no'] = array(
	'OpenIDLogin' => array( 'OpenID-innlogging' ),
	'OpenIDConvert' => array( 'OpenID-konvertering' ),
	'OpenIDServer' => array( 'OpenID-tjener' ),
	'OpenIDXRDS' => array( 'OpenID-XRDS' ),
);

/** Polish (Polski) */
$specialPageAliases['pl'] = array(
	'OpenIDLogin' => array( 'Logowanie_OpenID' ),
	'OpenIDConvert' => array( 'Konwersja_na_OpenID' ),
	'OpenIDServer' => array( 'Serwer_OpenID' ),
	'OpenIDXRDS' => array( 'DXRDS_OpenID' ),
);

/** Sanskrit (संस्कृत) */
$specialPageAliases['sa'] = array(
	'OpenIDLogin' => array( 'उद्घटपरिचयपत्रप्रवेश' ),
	'OpenIDConvert' => array( 'उद्घटपरिचयपत्रांतर' ),
	'OpenIDServer' => array( 'उद्घटपरिचयपत्रविदादाता' ),
	'OpenIDXRDS' => array( 'उद्घटपरिचयपत्रXRDS' ),
);

/** Slovak (Slovenčina) */
$specialPageAliases['sk'] = array(
	'OpenIDLogin' => array( 'OpenIDPrihlásenie' ),
	'OpenIDConvert' => array( 'OpenIDKonverzia' ),
);

/** Tagalog (Tagalog) */
$specialPageAliases['tl'] = array(
	'OpenIDLogin' => array( 'Paglagda sa OpenID' ),
	'OpenIDConvert' => array( 'Pagpapalit ng OpenID' ),
	'OpenIDServer' => array( 'Serbidor ng OpenID' ),
	'OpenIDXRDS' => array( 'XRDS ng OpenID' ),
);

/** Turkish (Türkçe) */
$specialPageAliases['tr'] = array(
	'OpenIDLogin' => array( 'AçıkKimlikOturumAçma' ),
	'OpenIDConvert' => array( 'AçıkKimlikDönüştürme' ),
	'OpenIDServer' => array( 'AçıkKimlikSunucusu' ),
	'OpenIDXRDS' => array( 'AçıkKimlikXRDS' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;