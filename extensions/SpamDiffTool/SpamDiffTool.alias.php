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
	'SpamDiffTool' => array( 'SpamDiffTool' ),
);

/** Arabic (العربية) */
$specialPageAliases['ar'] = array(
	'SpamDiffTool' => array( 'أداة_فرق_السبام' ),
);

/** Japanese (日本語) */
$specialPageAliases['ja'] = array(
	'SpamDiffTool' => array( 'スパム差分ツール' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'SpamDiffTool' => array( 'Tool_fir_Spam_z\'ënnersichen' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'SpamDiffTool' => array( 'Spamverschillenhulpmiddel' ),
);

/**
 * For backwards compatibility with MediaWiki 1.15 and earlier.
 */
$aliases =& $specialPageAliases;