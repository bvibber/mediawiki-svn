/**
* MediaWiki javascript language transformations 
*
* port of php language converters. 
* 
* Conversions are packaged into script-loader requests per
* the requested language.
* 
* The structure of mediaWiki language/classes is preserved 
* as much as possible
*/

/**
 * Plural form transformations, needed for some languages.
 * For example, there are 3 form of plural in Russian and Polish,
 * depending on "count mod 10". See [[w:Plural]]
 * For English it is pretty simple.
 *
 * Invoked by putting {{plural:count|wordform1|wordform2}}
 * or {{plural:count|wordform1|wordform2|wordform3}}
 *
 * Example: {{plural:{{NUMBEROFARTICLES}}|article|articles}}
 *
 * @param count Integer: non-localized number
 * @param forms Array: different plural forms
 * @return string Correct form of plural for count in this language
 */

mw.lang.convertPlural = function( count, forms ){	
	if ( !forms || forms.length == 0 ) { 
		return ''; 
	}	
	return ( parseInt( count ) == 1 ) ? forms[0] : forms[1];
};
