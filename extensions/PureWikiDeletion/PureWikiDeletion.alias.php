<?php
$aliases = array();
 
/** English */

$specialPageAliases = array();

/** English (English) */
$specialPageAliases['en'] = array(
	'PureWikiDeletion' => array( 'PureWikiDeletion' ),
	'RandomExcludeBlank' => array( 'RandomExcludeBlank' ),
	'PopulateBlankedPagesTable' => array( 'PopulateBlankedPagesTable' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'PureWikiDeletion' => array( 'حذف_الويكي_النقي' ),
	'RandomExcludeBlank' => array( 'عشوائي_باستثناء_الفارغ' ),
	'PopulateBlankedPagesTable' => array( 'ملء_جدول_الصفحات_المفرغة' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'PureWikiDeletion' => array( 'PureWikiverwijdering' ),
	'RandomExcludeBlank' => array( 'WillekeurigZonderLeeg' ),
	'PopulateBlankedPagesTable' => array( 'TabelLegePagina\'sVullen', 'TabelLegePaginasVullen' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;