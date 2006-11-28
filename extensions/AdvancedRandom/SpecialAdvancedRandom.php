<?php
if ( ! defined( 'MEDIAWIKI' ) )
	die();
/**
 * Get a random page from the set of pages whos talk or subjectpage links to a
 * given page, can be used like Special:AdvancedRandom/Template:Featured/Talk
 * to get a random featured article or like
 * Special:AdvancedRandom/Template:Delete to get a random speedy deletion
 * candidate.
 *
 * Note: This is neat, but way too expensive to run on any serious site
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'AdvancedRandom',
	'description' => 'Get a random page from whos talk or subjectpage ' .
	                 'links to a given page, can be used like ' .
			 '[[Special:AdvancedRandom/Template:Featured/Talk]] ' .
			 'to get a random featured article or like ' .
			 '[[Special:AdvancedRandom/Template:GFDL/Image]] to ' .
			 'get a random GFDL file',
	'author' => 'Ævar Arnfjörð Bjarmason'
);

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/AdvancedRandom_body.php', 'AdvancedRandom', 'SpecialAdvancedRandom' );

?>
