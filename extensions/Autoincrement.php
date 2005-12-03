<?php
if (!defined('MEDIAWIKI')) die();
/**
 * An example parser hook that defines a new variable, {{AUTOINCREMENT}},
 * useful for maintaining a citation count with {{ref|}} and {{note|}} pairs
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['variable'][] = array(
	'name' => 'Autoincrement',
	'description' => 'a variable hook that adds an autoincrementing variable, <nowiki>{{AUTOINCREMENT}}</nowiki>',
	'author' => 'Ævar Arnfjörð Bjarmason'
);

class Autoincrement {
	var $mCount;
	
	function Autoincrement() {
		global $wgHooks;
		
		$this->mCount = 0;
		
		$wgHooks['MagicWordMagicWords'][] = array( $this , 'hookWords' );
		$wgHooks['MagicWordwgVariableIDs'][] = array( $this, 'wfAutoincrementHookVariables' );
		$wgHooks['LanguageGetMagic'][] = array( $this, 'wfAutoincrementHookRaw' );
		$wgHooks['ParserGetVariableValueSwitch'][] = array( $this, 'wfAutoincrementHookSwitch' );
	}

	function hookWords( &$magicWords ) {
		$magicWords[] = 'MAG_AUTOINCREMENT';
	
		return true;
	}
	
	function wfAutoincrementHookVariables( &$wgVariableIDs ) {
		$wgVariableIDs[] = MAG_AUTOINCREMENT;

		return true;
	}
	
	function wfAutoincrementHookRaw( &$raw ) {
		$raw[MAG_AUTOINCREMENT] = array( 0, 'AUTOINCREMENT' );;

		return true;
	}

	function wfAutoincrementHookSwitch( &$parser, &$varCache, &$index, &$ret ) {
		if ( $index === MAG_AUTOINCREMENT )
			$ret = ++$this->mCount; // No formatNum() just like url autonumbering
	
		return true;
	}
}

new Autoincrement;
