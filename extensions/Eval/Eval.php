<?php
if ( ! defined( 'MEDIAWIKI' ) ) die();
/**
 * An special page extension that provides a public interface to PHP's eval()
 * function, supersecure, install it on your production servers, no really!
 *
 * @file
 * @addtogroup Extensions
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionCredits['specialpage'][] = array(
	'path'           => __FILE__,
	'name'           => 'Eval',
	'author'         => 'Ævar Arnfjörð Bjarmason',
	'descriptionmsg' => 'eval-desc'
);

$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['Eval'] = $dir . 'Eval.i18n.php';
$wgExtensionAliasesFiles['Eval'] = $dir . 'Eval.alias.php';

$wgSpecialPages['Eval'] = 'SpecialEval';
$wgAutoloadClasses['SpecialEval'] = $dir . 'Eval.class.php';

