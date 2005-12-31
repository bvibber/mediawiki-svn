<?php
/**
 * An extension that adds a purge tab on each page
 *
 * @author Ævar Arnfjörð Bjarmason <avarab@gmail.com>
 * @copyright Copyright © 2005, Ævar Arnfjörð Bjarmason
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

$wgExtensionFunctions[] = 'wfPurge';
$wgExtensionCredits['other'][] = array(
	'name' => 'Purge',
	'author' => 'Ævar Arnfjörð Bjarmason',
	'description' => 'Adds a purge tab on all normal pages and bypasses the purge check for anonymous users allowing for quick purging of the cache'
);


function wfPurge() {
	class PurgeAction {
		public function __construct() {
			global $wgMessageCache, $wgHooks;
			
			$wgMessageCache->addMessage( 'purge', 'Purge' );
			
			$wgHooks['SkinTemplateContentActions'][] = array( &$this, 'contentHook' );
			$wgHooks['ArticlePurge'][] = array( &$this, 'purgeHook' );
		}
		
		public static function contentHook( array &$content_actions ) {
			global $wgRequest, $wgTitle;

			if ( $wgTitle->getNamespace() !== NS_SPECIAL ) {
				$action = $wgRequest->getText( 'action' );
				
				$content_actions['purge'] = array(
					'class' => $action === 'purge' ? 'selected' : false,
					'text' => wfMsg( 'purge' ),
					'href' => $wgTitle->getLocalUrl( 'action=purge' )
				);
			}

			return true;
		}

		public static function purgeHook( Article &$article ) {
			return false;
		}
	}

	new PersistentObject( new PurgeAction );
}
