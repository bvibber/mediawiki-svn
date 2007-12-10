<?php
if (!defined('MEDIAWIKI')) die();
/**
 * @addtogroup Extensions
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'Filepath',
	'version' => '1.1',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => '[[Special:Filepath|a special page]] to get the full path of a file from its name',
	'url' => 'http://www.mediawiki.org/wiki/Extension:Filepath',
);

# Internationalisation file
require_once( dirname(__FILE__) . '/SpecialFilepath.i18n.php' );

if ( !function_exists( 'extAddSpecialPage' ) ) {
	require( dirname(__FILE__) . '/../ExtensionFunctions.php' );
}
extAddSpecialPage( dirname(__FILE__) . '/SpecialFilepath_body.php', 'Filepath', 'SpecialFilepath' );
