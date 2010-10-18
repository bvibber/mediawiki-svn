<?php 

/**
 * MwEmebed module resource list array
 */

return array(
	'mw.Language' => array ( 'scripts' => 'components/mwEmbed.js' ),
	'mw.Parser' => array ( 'scripts' => 'components/mw.Parser.js' ),
	'mw.Api' => array ( 'scripts' => 'components/mw.Api.js' ),
	'mwEmbed' => array( 
		'scripts' => 'mwEmbed.js',
		'skinStyles' => array(
			'default' => 'skins/common/mw.style.mwCommon.css'
		),
		'group' => 'ext.mwEmbed',
		'dependencies' => array(
			'jquery',
			'mw.Language',
			'mw.Parser',
			'mw.Api',
		),
		'messages' => 'moduleFile',		
		'languageScripts' => array(
			'am' => 'languages/classes/LanuageAm.js',
			'ar' => 'languages/classes/LanuageAr.js',
			'bat_smg' => 'languages/classes/LanuageBat_smg.js',
			'be' => 'languages/classes/LanuageBe.js',
			'be_tarask' => 'languages/classes/LanuageBe_tarask.js',
			'bh' => 'languages/classes/LanuageBh.js',
			'bs' => 'languages/classes/LanuageBs.js',
			'cs' => 'languages/classes/LanuageCs.js',
			'cu' => 'languages/classes/LanuageCu.js',
			'cy' => 'languages/classes/LanuageCy.js',
			'dsb' => 'languages/classes/LanuageDsb.js',
			'fr' => 'languages/classes/LanuageFr.js',
			'ga' => 'languages/classes/LanuageGa.js',
			'gd' => 'languages/classes/LanuageGd.js',
			'gv' => 'languages/classes/LanuageGv.js',
			'he' => 'languages/classes/LanuageHe.js',
			'hi' => 'languages/classes/LanuageHi.js',
			'hr' => 'languages/classes/LanuageHr.js',
			'hsb' => 'languages/classes/LanuageHsb.js',
			'hy' => 'languages/classes/LanuageHy.js',
			'ksh' => 'languages/classes/LanuageKsh.js',
			'ln' => 'languages/classes/LanuageLn.js',
			'lt' => 'languages/classes/LanuageLt.js',
			'lv' => 'languages/classes/LanuageLv.js',
			'mg' => 'languages/classes/LanuageMg.js',
			'mk' => 'languages/classes/LanuageMk.js',
			'mo' => 'languages/classes/LanuageMo.js',
			'mt' => 'languages/classes/LanuageMt.js',
			'nso' => 'languages/classes/LanuageNso.js',
			'pl' => 'languages/classes/LanuagePl.js',
			'pt_br' => 'languages/classes/LanuagePt_br.js',
			'ro' => 'languages/classes/LanuageRo.js',
			'ru' => 'languages/classes/LanuageRu.js',
			'se' => 'languages/classes/LanuageSe.js',
			'sh' => 'languages/classes/LanuageSh.js',
			'sk' => 'languages/classes/LanuageSk.js',
			'sl' => 'languages/classes/LanuageSl.js',
			'sma' => 'languages/classes/LanuageSma.js',
			'sr_ec' => 'languages/classes/LanuageSr_ec.js',
			'sr_el' => 'languages/classes/LanuageSr_el.js',
			'sr' => 'languages/classes/LanuageSr.js',
			'ti' => 'languages/classes/LanuageTi.js',
			'tl' => 'languages/classes/LanuageTl.js',
			'uk' => 'languages/classes/LanuageUk.js',
			'wa' => 'languages/classes/LanuageWa.js',
		)
	) 
);