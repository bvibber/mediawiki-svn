<?php
# MediaWiki InterlanguageCentral extension v1.0
#
# Copyright © 2010 Nikola Smolenski <smolensk@eunet.rs>
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
#
# For more information see
# http://www.mediawiki.org/wiki/Extension:Interlanguage

$wgExtensionFunctions[]="wfInterlanguageCentralExtension";
$wgJobClasses['purgeDependentWikis'] = 'InterlanguageCentralExtensionPurgeJob';
$wgExtensionCredits['parserhook'][] = array(
	'name'			=> 'Interlanguage Central',
	'author'			=> 'Nikola Smolenski',
	'url'				=> 'http://www.mediawiki.org/wiki/Extension:Interlanguage',
	'version'			=> '1.0',
	'descriptionmsg'	=> 'interlanguagecentral-desc',
);
$wgExtensionMessagesFiles['Interlanguagecentral'] = dirname(__FILE__) . '/InterlanguageCentral.i18n.php';
 
function wfInterlanguageCentralExtension() {
	global $wgHooks, $wgInterlanguageCentralExtension;

	$wgInterlanguageCentralExtension = new InterlanguageCentralExtension();
	$wgHooks['ArticleSave'][] = $wgInterlanguageCentralExtension;
	$wgHooks['ArticleSaveComplete'][] = $wgInterlanguageCentralExtension;
	//TODO: ArticleDelete etc.
}

class InterlanguageCentralExtension {
	//ILL = InterLanguageLinks
	var $oldILL = array();
	
	function onArticleSave() {	
		global $wgTitle;
		$this->oldILL = $this->getILL($wgTitle);
		return true;
	}
	
	function onArticleSaveComplete() {
		global $wgTitle;

		$newILL = $this->getILL($wgTitle);

		//Compare ILLs before and after the save; if nothing changed, there is no need to purge
		if(
			count(array_udiff_assoc(
				$this->oldILL,
				$newILL,
				"InterlanguageCentralExtension::arrayCompareKeys"
			)) || count(array_udiff_assoc(
				$newILL,
				$this->oldILL,
				"InterlanguageCentralExtension::arrayCompareKeys"
			))
		) {
			$ill = array_merge_recursive($this->oldILL, $newILL);
			$job = new InterlanguageCentralExtensionPurgeJob( $wgTitle, array('ill' => $ill) );
			$job->insert();
		}
	
		return true;
	
	}
	
	function getILL($title) {
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'langlinks', array( 'll_lang', 'll_title' ), array( 'll_from' => $title->mArticleID), __FUNCTION__);
		$a = array();
		while ( $row = $dbr->fetchObject( $res ) ) {
			if(!isset($a[$row->ll_lang])) {
				$a[$row->ll_lang] = array();
			}
			$a[$row->ll_lang][$row->ll_title] = true;
		}
		$dbr->freeResult( $res );
	
		return $a;
	}
	
	static function arrayCompareKeys($a, $b) {
		return count(array_diff_key($a, $b))? 1: (count(array_diff_key($b, $a))? -1: 0);
	}
}

//Based on http://www.mediawiki.org/wiki/Manual:Job_queue/For_developers
class InterlanguageCentralExtensionPurgeJob extends Job {
	public function __construct( $title, $params ) {
		parent::__construct( 'purgeDependentWikis', $title, $params );
	}
 
	/**
	 * Execute the job
	 *
	 * @return bool
	 */
	public function run() {
		global $wgInterlanguageCentralExtensionIndexUrl;

		//sleep() could be added here to reduce unnecessary use
		$ill = $this->params['ill'];

		foreach($ill as $lang => $pages) {
			//TODO: error handling
			$baseURL = sprintf($wgInterlanguageCentralExtensionIndexUrl, $lang) .
				"?action=purge&title=";
			foreach($pages as $page => $dummy) {			
				$url = $baseURL . urlencode(strtr($page, ' ', '_'));
				Http::post( $url );
			}
		}
 
		return true;
	}

	//TODO: custom insert with duplicate merging
}
