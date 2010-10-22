<?php 

/**
 * MwEmebed module resource list array
 */

return array(		
	'mw.jQueryUtil' => array( 'scripts' => 'components/mw.jQueryUtil.js' ),
	'mwEmbed' => array(
		'scripts' => 'mwEmbed.core.js',
		'dependencies' => array(
			'mediaWiki.messageParser',
			'mw.jQueryUtil'
		),
		'styles' => 'skins/common/mw.style.mwCommon.css',
		'group' => 'ext.mwEmbed',		
		'messages' => 'moduleFile',		
	)
);