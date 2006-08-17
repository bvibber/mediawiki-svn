<?php
/**
 * Internationalization file for DynamicPageList2 extension.
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author m:User:Dangerman <cyril.dangerville@gmail.com>
*/

$wgDPL2Messages = array();

$wgDPL2Messages['en'] = array(
	/*
		Debug
	*/
	// (FATAL) ERRORS
	/**
	 * $0: 'namespace' or 'notnamespace'
	 * $1: wrong parameter given by user
	 * $3: list of possible titles of namespaces (except pseudo-namespaces: Media, Special)
	 */
	'dpl2_debug_' . DPL2_ERR_WRONGNS => "ERROR: Wrong '$0' parameter: '$1'! Help:  <code>$0= <i>empty string</i> (Main)$3</code>.",
	/**
	 * $0: max number of categories that can be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOMANYCATS => 'ERROR: Too many categories! Maximum: $0. Help: increase <code>$wgDPL2MaxCategoryCount</code> to specify more categories or set <code>$wgDPL2AllowUnlimitedCategories=true</code> for no limitation. (Set the variable in <code>LocalSettings.php</code>, after including <code>DynamicPageList2.php</code>.)',
	/**
	 * $0: min number of categories that have to be included
	*/
	'dpl2_debug_' . DPL2_ERR_TOOFEWCATS => 'ERROR: Too few categories! Minimum: $0. Help: decrease <code>$wgDPL2MinCategoryCount</code> to specify fewer categories. (Set the variable preferably in <code>LocalSettings.php</code>, after including <code>DynamicPageList2.php</code>.)',
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTNOINCLUDEDCATS => "ERROR: You need to include at least one category if you want to use 'addfirstcategorydate=true' or 'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_CATDATEBUTMORETHAN1CAT => "ERROR: If you include more than one category, you cannot use 'addfirstcategorydate=true' or 'ordermethod=categoryadd'!",
	'dpl2_debug_' . DPL2_ERR_MORETHAN1TYPEOFDATE => 'ERROR: You cannot add more than one type of date at a time!',
	/**
	 * $0: param=val that is possible only with $1 as last 'ordermethod' parameter
	 * $1: last 'ordermethod' parameter required for $0
	*/
	'dpl2_debug_' . DPL2_ERR_WRONGORDERMETHOD => "ERROR: You can use '$0' with 'ordermethod=[...,]$1' only!",
	
	// WARNINGS
	/**
	 * $0: param name
	 * $1: wrong param value given by user
	 * $2: default param value used instead by program
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGCOUNT => "WARNING: Wrong '$0' parameter: '$1'! Using default: '$2' (no count limit). Help: <code>$0= <i>empty string</i> (no count limit) | n</code>, with <code>n</code> a positive integer.",
	'dpl2_debug_' . DPL2_WARN_WRONGINLINESYMBOL => "WARNING: Wrong '$0' parameter: '$1' (tags ignored)! Using default: '$2'. Help: <code>$0= symbol</code>, with <code>symbol</code> anything but blank (tags ignored).",
	/**
	 * $3: list of valid param values separated by ' | '
	*/
	'dpl2_debug_' . DPL2_WARN_WRONGPARAM => "WARNING: Wrong '$0' parameter: '$1'! Using default: '$2'. Help: <code>$0= $3</code>.",
	'dpl2_debug_' . DPL2_WARN_NORESULTS => 'WARNING: No results!',
	'dpl2_debug_' . DPL2_WARN_NOINCLUDEDCATSORNS => 'WARNING: It is strongly recommended to include at least one category or namespace. If not, the generation of the page list can be quite resource and time-consuming.',
	'dpl2_debug_' . DPL2_WARN_CATOUTPUTBUTWRONGPARAMS => "WARNING: Add* parameters ('adduser', 'addeditdate', etc.)' have no effect with 'mode=category'. Only the page namespace/title can be viewed in this mode.",
	/**
	 * $0: 'headingmode' value given by user
	 * $1: value used instead by program (which means no heading)
	*/
	'dpl2_debug_' . DPL2_WARN_HEADINGBUTSIMPLEORDERMETHOD => "WARNING: 'headingmode=$0' has no effect with 'ordermethod' on a single component. Using: '$1'. Help: you can use not-$1 'headingmode' values with 'ordermethod' on multiple components. The first component is used for headings. E.g. 'ordermethod=category,<i>comp</i>' (<i>comp</i> is another component) for category headings.",
	/**
	 * $0: 'debug' value
	*/
	'dpl2_debug_' . DPL2_WARN_DEBUGPARAMNOTFIRST => "WARNING: 'debug=$0' is not in first position in the DPL element. The new debug settings are not applied before all previous parameters have been parsed and checked.",
					
	// OTHERS
	/**
	 * $0: SQL query executed to generate the dynamic page list
	*/
	'dpl2_debug_' . DPL2_QUERY => 'QUERY: <code>$0</code>',

	/*
	   Output formatting
	*/
	/**
	 * $1: number of articles
	*/
	'dpl2_articlecount1' => 'There is $1 article under this heading.',
	'dpl2_articlecount' => 'There are $1 articles under this heading.'
);

?>
