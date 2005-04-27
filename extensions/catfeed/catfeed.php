<?
/**
 * CategoryFeed extension for MediaWiki 1.4+
 *
 * Copyright (C) 2005 Gabriel Wicke <wicke@wikidev.net>
 * http://wikidev.net
 * 
 * uses bits from recentchanges feeds
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @todo Create in-page version, especially useful for wikinews
 */


$wgExtensionFunctions[] = 'setupCatRSSExtension';
if( $wgCategoryMagicGallery ) 
	require_once('ImageGallery.php');

function setupCatRSSExtension() {

	global $IP;
	require_once( "$IP/includes/CategoryPage.php" );

	global $wgHooks;

	$wgHooks['CategoryPageView'][] = 'viewCatRSS';

	class CategoryFeed extends CategoryPage {
		/**
		* Feed for recently-added members of a category based on cl_timestamp
		* Uses bits of the recentchanges feeds (caching and formatting)
		* @package MediaWiki
		*/

		function CategoryFeed( &$CategoryPage ) {
			$this->mTitle = $CategoryPage->mTitle;
		}

		function view() {
			global $wgRequest;
			require_once("Feed.php");
			$this->mFeedFormat = $wgRequest->getVal( 'feed', '' );
			if ( $this->mFeedFormat == '') return true; # let CategoryPage::view continue, no feed requested
			$this->mMaxTimeStamp = 0;
			$limit = 50;
			$dbr =& wfGetDB( DB_SLAVE );
			$res = $dbr->select(
				array( 'cur', 'categorylinks' ),
				array( 'cur_title', 'cur_namespace', 'cur_text', 'cur_user_text', 'cl_sortkey', 'cl_timestamp' ),
				array( 'cl_from          =  cur_id',
				'cl_to'           => $this->mTitle->getDBKey(),
				'cur_is_redirect' => 0),
				$fname,
				array( 'ORDER BY' => 'cl_timestamp DESC, cl_sortkey ASC',
				'LIMIT'    => $limit ));
				$rows = array();
				while( $row = $dbr->fetchObject ( $res ) ) {
					$rows[] = $row;
					if ( $row->cl_timestamp > $this->mMaxTimeStamp ) {
						$this->mMaxTimeStamp = $row->cl_timestamp;
					}
				}
				$this->categoryOutputFeed( &$rows, $limit );
				# stop CategoryPage::view from continuing
				return false;
		}

		# strip images, links, tags
		function formatSummary ( $row ) {
			global $wgContLang;
			$prefixes = array_keys($wgContLang->getLanguageNames());
			$prefixes[] = $wgContLang->getNsText(NS_CATEGORY);
			$imgprefix = $wgContLang->getNsText(NS_IMAGE);
			$text = "\n".$row->cur_text;

			$rules = array(
				"/\[\[(".implode('|',$prefixes)."):[^\]]*\]\]/i" => "", # interwiki links, cat links
				"/\[\[(?!".$imgprefix.")([^\[\]]+)\|([^[\]\|]*)\]\]/" => "\$2", # piped links
				"/\[\[(?!".$imgprefix.")([^\[\]]+)\]\]/i" => "\$1", # links
				"/\[http:\/\/[^\s]+\s*(.*?)\]/" => "\$1", # external links
				"/\[\[".$imgprefix.":[^\]]*\]\]/i" => "", # images
				"/<br([^>]{1,60})>/i" => "\n", # break
				"/{{([^}]+)}}/s" => "", # templates
				"/<table[^<]{0,660}>(.*?)<\/table>/si" => "", # table
				"/\n{\|(.+)\n\|}/s" => "", # table
				"/\n===\s*(.*)\s*===\s*\n/" => "\n* \$1\n", # h3
				"/\n==\s*(.*)\s*==\s*\n/" => "\n* \$1\n", # h2
				"/\n=\s*(.*)\s*=\s*\n/" => "\n* \$1\n", # h1
				"/'''(.*)'''/" => "\$1", # bold
				"/''(.*)''/" => "\$1", # italic
				"/<([^>]{1,1500})>/s" => "", # any html tags
				"/__\w{1,60}__/i" => "", # __notoc__ etc
				"/(\n\s*)+/" => "\n" # many newlines
			);

			$text = preg_replace( array_keys($rules), array_values($rules), $text); 
			$shorttext = substr($text,1,145); # only return the first few chars for now
			return htmlspecialchars( $shorttext.'...');
		}

		function categoryOutputFeed( $rows, $limit ) {
			global $messageMemc, $wgDBname, $wgFeedCacheTimeout;
			global $wgFeedClasses, $wgTitle, $wgSitename, $wgContLanguageCode;

			if( !isset( $wgFeedClasses[$this->mFeedFormat] ) ) {
				wfHttpError( 500, "Internal Server Error", "Unsupported feed type." );
				return false;
			}

			$timekey = "$wgDBname:catfeed:" . $this->mTitle->getDBKey() . ":timestamp";
			$key = "$wgDBname:catfeed:" . $this->mTitle->getDBKey() . ":$this->mFeedFormat:limit:$limit";

			$feedTitle = $this->mTitle->getPrefixedText() . ' - ' . $wgSitename;
			$feed = new $wgFeedClasses[$this->mFeedFormat](
				$feedTitle,
				htmlspecialchars( wfMsgForContent( 'catfeedsummary' ) ),
				$wgTitle->getFullUrl() );

				/**
				* Loading and parsing cur_text for all added pages is slow, so we cache it
				*/
				$cachedFeed = false;
				if( $feedLastmod = $messageMemc->get( $timekey ) ) {
					/**
					* If the cached feed was rendered very recently, we may
					* go ahead and use it even if there have been edits made
					* since it was rendered. This keeps a swarm of requests
					* from being too bad on a super-frequently edited wiki.
					*/
					if( time() - wfTimestamp( TS_UNIX, $feedLastmod )
					< $wgFeedCacheTimeout
					|| wfTimestamp( TS_UNIX, $feedLastmod )
					> wfTimestamp( TS_UNIX, $this->mMaxTimeStamp ) ) {
						wfDebug( "CatFeed: loading feed from cache ($key; $feedLastmod; $this->mMaxTimeStamp)...\n" );
						$cachedFeed = $messageMemc->get( $key );
					} else {
						wfDebug( "CatFeed: cached feed timestamp check failed ($feedLastmod; $this->mMaxTimeStamp)\n" );
					}
				}
				/*if( is_string( $cachedFeed ) ) {
					wfDebug( "CatFeed: Outputting cached feed\n" );
					$feed->httpHeaders();
					echo $cachedFeed;
				} else {*/
					wfDebug( "CatFeed: rendering new feed and caching it\n" );
					ob_start();
					$this->catDoOutputFeed( $rows, $feed );
					$cachedFeed = ob_get_contents();
					ob_end_flush();

					$expire = 3600 * 24; # One day
					$messageMemc->set( $key, $cachedFeed );
					$messageMemc->set( $timekey, wfTimestamp( TS_MW ), $expire );
					#	}
				return true;
		}

		function catDoOutputFeed( $rows, &$feed ) {
			global $wgSitename, $wgFeedClasses, $wgContLanguageCode;

			$feed->outHeader();
			foreach( $rows as $row ) {
				$title = Title::makeTitle( $row->cur_namespace, $row->cur_title );
				$item = new FeedItem(
					$title->getPrefixedText(),
					$this->formatSummary( &$row ),
					$title->getFullURL(),
					$row->lc_timestamp,
					$row->cur_user_text,
					'' #$talkpage->getFullURL()
				);
				$feed->outItem( $item );
			}
			$feed->outFooter();
		}
	}

}

function viewCatRSS( &$CategoryPage ) {
	$catfeed = new CategoryFeed($CategoryPage);
	return $catfeed->view();
}
?>
