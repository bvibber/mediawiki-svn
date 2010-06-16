<?php
/**
 * LogoFunctions
 *
 * Add Paser hooks about wiki's logo
 *
 * @link http://www.mediawiki.org/wiki/Extension:LogoFunctions
 *
 * @author Devunt <devunt@devunt.kr>
 * @authorlink http://www.mediawiki.org/wiki/User:Devunt
 * @copyright Copyright © 2010 Devunt (Bae June Hyeon).
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
 
if ( !defined( 'MEDIAWIKI' ) ) die('define error!');
 
$wgExtensionCredits[ 'parserhook' ][] = array(
    'name'           => 'LogoFunctions',
    'author'         => 'Devunt (Bae June Hyeon)',
    'url'            => 'http://www.mediawiki.org/wiki/Extension:LogoFunctions',
    'descriptionmsg'    => 'logofunctions-desc',
    'version'        => '0.9',
);
 
$wgHooks['ParserFirstCallInit'][] = 'efLogoFunctions_Setup';
$wgHooks['LanguageGetMagic'][]       = 'efLogoFunctions_Magic';
 
function efLogoFunctions_Setup( &$parser ) {
	$parser->setFunctionHook( 'setlogo', 'efSetLogo_Render' );
	return true;
}
 
function efLogoFunctions_Magic( &$magicWords, $langCode ) {
        $magicWords['setlogo'] = array( 0, 'setlogo' );
        return true;
}
 
function efSetLogo_Render( $parser, $logo = '') {
		global $wgLogo;
		$imageobj = wfFindFile($logo);
		if ($imageobj == null) return "<strong class=\"error\">File not exist error: [[File:$logo]] is not exist</strong>";
		$thumb_arr = array(
			'width' => 135,
			'height' => 135
		);
		$thumb = $imageobj->transform($thumb_arr);
		$wgLogo = $thumb->getUrl();
}