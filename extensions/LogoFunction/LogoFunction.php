<?php
/**
 * WebIRC
 *
 * Add PaserFunctions about wiki's logo
 *
 * @link http://www.mediawiki.org/wiki/Extension:LogoFunction
 *
 * @author Devunt <devunt@devunt.kr>
 * @authorlink http://www.mediawiki.org/wiki/User:Devunt
 * @copyright Copyright © 2010 Devunt (Bae June Hyeon).
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
 
// If this is run directly from the web die as this is not a valid entry point.
if ( !defined( 'MEDIAWIKI' ) ) die('define error!');
 
// Extension credits.
$wgExtensionCredits[ 'parserhook' ][] = array(
    'name'           => 'LogoFunction',
    'author'         => 'Devunt (Bae June Hyeon)',
    'url'            => 'http://www.mediawiki.org/wiki/Extension:LogoFunction',
    'description'    => 'Add PaserFunctions about wiki\'s logo',
    'version'        => '0.9',
);
 
# Define a setup function
$wgHooks['ParserFirstCallInit'][] = 'efLogoFunction_Setup';
# Add a hook to initialise the magic word
$wgHooks['LanguageGetMagic'][]       = 'efLogoFunction_Magic';
 
function efLogoFunction_Setup( &$parser ) {
	# Set a function hook associating the "example" magic word with our function
	$parser->setFunctionHook( 'setlogo', 'efSetLogo_Render' );
	return true;
}
 
function efLogoFunction_Magic( &$magicWords, $langCode ) {
        # Add the magic word
        # The first array element is whether to be case sensitive, in this case (0) it is not case sensitive, 1 would be sensitive
        # All remaining elements are synonyms for our parser function
        $magicWords['setlogo'] = array( 0, 'setlogo' );
        # unless we return true, other parser functions extensions won't get loaded.
        return true;
}
 
function efSetLogo_Render( $parser, $logo = '') {
		global $wgLogo;
        # The parser function itself
        # The input parameters are wikitext with templates expanded
        # The output should be wikitext too
		$imageobj = wfFindFile($logo);
		if ($imageobj == null) return "<strong class=\"error\">File not exist error: [[File:$logo]] is not exist</strong>";
		$thumb_arr = array(
			'width' => 135,
			'height' => 135
		);
		$thumb = $imageobj->transform($thumb_arr);
		$wgLogo = $thumb->getUrl();
}