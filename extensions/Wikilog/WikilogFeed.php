<?php
/**
 * MediaWiki Wikilog extension
 * Copyright © 2008, 2009 Juliano F. Ravasi
 * http://www.mediawiki.org/wiki/Extension:Wikilog
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
 */

/**
 * @addtogroup Extensions
 * @author Juliano F. Ravasi < dev juliano info >
 */

if ( !defined( 'MEDIAWIKI' ) )
	die();

/**
 * Syndication feed driver. Creates feeds from a list of wikilog articles,
 * given a format and a query object.
 */
class WikilogFeed
{
	/**
	 * Feed title (i.e., not Wikilog title). For Special:Wikilog,
	 * 'wikilog-specialwikilog-title' system message should be used.
	 */
	protected $mTitle;

	/**
	 * Feed format, either 'atom' or 'rss'.
	 * @warning Insecure string, from query string. Shouldn't be displayed. 
	 */
	protected $mFormat;

	/**
	 * Wikilog query object. Contains the options that drives the database
	 * queries.
	 */
	protected $mQuery;

	/**
	 * Number of feed items to output.
	 */
	protected $mLimit;

	/**
	 * Either if this is a site feed (Special:Wikilog) or not.
	 */
	protected $mSiteFeed;

	/**
	 * Database object.
	 */
	protected $mDb;

	/**
	 * Copyright notice.
	 */
	protected $mCopyright;

	/**
	 * List of query parameters that are allowed for feeds. Note that adding
	 * to this list means that feed caching should be revisited. Parameters
	 * must be listed as keys.
	 */
	public static $paramWhitelist = array( 'show' => true );

	/**
	 * WikilogFeed constructor.
	 *
	 * @param $title Feed title and URL.
	 * @param $format Feed format ('atom' or 'rss').
	 * @param $query WikilogItemQuery options.
	 * @param $limit Number of items to generate.
	 */
	public function __construct( $title, $format, WikilogItemQuery $query,
			$limit = false )
	{
		global $wgWikilogNumArticles, $wgUser;

		$this->mTitle = $title;
		$this->mFormat = $format;
		$this->mQuery = $query;
		$this->mLimit = $limit ? $limit : $wgWikilogNumArticles;
		$this->mSiteFeed = $this->mQuery->getWikilogTitle() === NULL;

		$this->mDb = wfGetDB( DB_SLAVE );

		# Retrieve copyright notice.
		$skin = $wgUser->getSkin();
		$saveExpUrls = WikilogParser::expandLocalUrls();
		$this->mCopyright = $skin->getCopyright( 'normal' );
		WikilogParser::expandLocalUrls( $saveExpUrls );
	}

	/**
	 * Execute the feed driver, generating the syndication feed and printing
	 * the results.
	 */
	public function execute() {
		global $wgOut;

		if ( !$this->checkFeedOutput() )
			return;

		$feed = $this->mSiteFeed
			? $this->getSiteFeedObject()
			: $this->getWikilogFeedObject( $this->mQuery->getWikilogTitle() );

		if ( $feed === false ) {
			wfHttpError( 404, "Not found",
				"There is no such wikilog feed available from this site." );
			return;
		}

		list( $timekey, $feedkey ) = $this->getCacheKeys();
		FeedUtils::checkPurge( $timekey, $feedkey );

		if ( $feed->isCacheable() ) {
			# Check if client cache is ok.
			if ( $wgOut->checkLastModified( $feed->getUpdated() ) ) {
				# Client cache is fresh. OutputPage takes care of sending
				# the appropriate headers, nothing else to do.
				return;
			}

			# Try to load the feed from our cache.
			$cached = $this->loadFromCache( $feed->getUpdated(), $timekey, $feedkey );

			if ( is_string( $cached ) ) {
				wfDebug( "Wikilog: Outputting cached feed\n" );
				$feed->httpHeaders();
				echo $cached;
			} else {
				wfDebug( "Wikilog: rendering new feed and caching it\n" );
				ob_start();
				$this->feed( $feed );
				$cached = ob_get_contents();
				ob_end_flush();
				$this->saveToCache( $cached, $timekey, $feedkey );
			}
		} else {
			# This feed is not cacheable.
			$this->feed( $feed );
		}
	}

	/**
	 * Generates the list of entries for a given feed and print the resulting
	 * feed document.
	 * @param $feed Prepared syndication feed object.
	 */
	public function feed( $feed ) {
		global $wgOut, $wgFavicon;

		$feed->outHeader();

		$this->doQuery();
		$numRows = min( $this->mResult->numRows(), $this->mLimit );

		wfDebug( "Wikilog: Feed query returned $numRows results.\n" );

		if ( $numRows ) {
			$this->mResult->rewind();
			for ( $i = 0; $i < $numRows; $i++ ) {
				$row = $this->mResult->fetchObject();
				$feed->outEntry( $this->feedEntry( $row ) );
			}
		}

		$feed->outFooter();
	}

	/**
	 * Generates and returns a single feed entry.
	 * @param $row The wikilog article database entry.
	 * @return A new WlSyndicationEntry object.
	 */
	function feedEntry( $row ) {
		global $wgMimeType;
		global $wgWikilogFeedSummary, $wgWikilogFeedContent;
		global $wgWikilogFeedCategories, $wgWikilogFeedRelated;
		global $wgWikilogEnableComments;

		# Make titles.
		$wikilogName = str_replace( '_', ' ', $row->wlw_title );
		$wikilogTitle =& Title::makeTitle( $row->wlw_namespace, $row->wlw_title );
		$itemName = str_replace( '_', ' ', $row->wlp_title );
		$itemTitle =& Title::makeTitle( $row->page_namespace, $row->page_title );

		# Retrieve article parser output
		list( $article, $parserOutput ) = WikilogUtils::parsedArticle( $itemTitle, true );

		# Generate some fixed bits
		$authors = unserialize( $row->wlp_authors );

		# Create new syndication entry.
		$entry = new WlSyndicationEntry(
			self::makeEntryId( $itemTitle ),
			$itemName,
			$row->wlp_updated,
			$itemTitle->getFullUrl()
		);

		# Comments link.
		$cmtLink = array(
			'href' => $itemTitle->getTalkPage()->getFullUrl(),
			'type' => $wgMimeType
		);
		if ( $wgWikilogEnableComments ) {
			$cmtLink['thr:count'] = $row->wlp_num_comments;
			if ( !is_null( $row->_wlp_last_comment_timestamp ) ) {
				$cmtLink['thr:updated'] = wfTimestamp( TS_ISO_8601, $row->_wlp_last_comment_timestamp );
			}
		}
		$entry->addLinkRel( 'replies', $cmtLink );

		# Source feed.
		if ( $this->mSiteFeed ) {
			$privfeed = $this->getWikilogFeedObject( $wikilogTitle, true );
			if ( $privfeed ) {
				$entry->setSource( $privfeed );
			}
		}

		# Retrieve summary and content.
		list( $summary, $content ) = WikilogUtils::splitSummaryContent( $parserOutput );

		if ( $wgWikilogFeedSummary && $summary ) {
			$entry->setSummary( new WlTextConstruct( 'html', $summary ) );
		}
		if ( $wgWikilogFeedContent && $content ) {
			$entry->setContent( new WlTextConstruct( 'html', $content ) );
		}

		# Authors.
		foreach ( $authors as $user => $userid ) {
			$usertitle = Title::makeTitle( NS_USER, $user );
			$entry->addAuthor( $user, $usertitle->getFullUrl() );
		}

		# Automatic list of categories.
		if ( $wgWikilogFeedCategories ) {
			$this->addCategories( $entry, $row->wlp_page );
		}

		# Automatic list of related links.
		if ( $wgWikilogFeedRelated ) {
			$externals = array_keys( $parserOutput->getExternalLinks() );
			foreach ( $externals as $ext ) {
				$entry->addLinkRel( 'related', array( 'href' => $ext ) );
			}
		}

		if ( $row->wlp_publish ) {
			$entry->setPublished( $row->wlp_pubdate );
		}

		return $entry;
	}

	/**
	 * Performs the database query that returns the syndication feed entries
	 * and store the result wrapper in $this->mResult.
	 */
	function doQuery() {
		$this->mIndexField = 'wlp_pubdate';
		$this->mResult = $this->reallyDoQuery( $this->mLimit );
	}

	/**
	 * Performs the database query and return the result wrapper.
	 * @param $limit Maximum number of entries to return.
	 * @return The database query ResultWrapper object.
	 */
	function reallyDoQuery( $limit ) {
		$fname = __METHOD__ . ' (' . get_class( $this ) . ')';
		$info = $this->getQueryInfo();
		$tables = $info['tables'];
		$fields = $info['fields'];
		$conds = $info['conds'];
		$options = $info['options'];
		$joins = $info['join_conds'];
		$options['ORDER BY'] = $this->mIndexField . ' DESC';
		$options['LIMIT'] = intval( $limit );
		$res = $this->mDb->select( $tables, $fields, $conds, $fname, $options, $joins );
		return new ResultWrapper( $this->mDb, $res );
	}

	/**
	 * Returns the query information.
	 */
	function getQueryInfo() {
		return $this->mQuery->getQueryInfo( $this->mDb, 'last-comment-timestamp' );
	}

	/**
	 * Generates and populates a WlSyndicationFeed object for the site.
	 *
	 * @return Feed object.
	 */
	public function getSiteFeedObject() {
		global $wgContLanguageCode, $wgWikilogFeedClasses, $wgFavicon, $wgLogo;
		$title = wfMsgForContent( 'wikilog-specialwikilog-title' );
		$subtitle = wfMsgExt( 'wikilog-feed-description', array( 'parse', 'content' ) );

		$updated = $this->mDb->selectField( 'wikilog_wikilogs',
			'MAX(wlw_updated)', false, __METHOD__ );
		if ( !$updated ) $updated = wfTimestampNow();

		$feed = new $wgWikilogFeedClasses[$this->mFormat](
			$this->mTitle->getFullUrl(),
			wfMsgForContent( 'wikilog-feed-title', $title, $wgContLanguageCode ),
			$updated,
			$this->mTitle->getFullUrl()
		);
		$feed->setSubtitle( new WlTextConstruct( 'html', $subtitle ) );
		$feed->setLogo( wfExpandUrl( $wgLogo ) );
		if ( $wgFavicon !== false ) {
			$feed->setIcon( wfExpandUrl( $wgFavicon ) );
		}
		if ( $this->mCopyright ) {
			$feed->setRights( new WlTextConstruct( 'html', $this->mCopyright ) );
		}
		return $feed;
	}

	/**
	 * Generates and populates a WlSyndicationFeed object for the given
	 * wikilog. Caches objects whenever possible.
	 *
	 * @param $wikilogTitle Title object for the wikilog.
	 * @return Feed object, or NULL if wikilog doesn't exist.
	 */
	public function getWikilogFeedObject( $wikilogTitle, $forsource = false ) {
		static $wikilogCache = array();
		global $wgContLanguageCode, $wgWikilogFeedClasses;
		global $wgWikilogFeedCategories;

		$title = $wikilogTitle->getPrefixedText();
		if ( !isset( $wikilogCache[$title] ) ) {
			$row = $this->mDb->selectRow( 'wikilog_wikilogs',
				array(
					'wlw_page', 'wlw_subtitle',
					'wlw_icon', 'wlw_logo', 'wlw_authors',
					'wlw_updated'
				),
				array( 'wlw_page' => $wikilogTitle->getArticleId() ),
				__METHOD__
			);
			if ( $row !== false ) {
				$self = $forsource
					 ? $wikilogTitle->getFullUrl( "feed={$this->mFormat}" )
					 : NULL;
				$feed = new $wgWikilogFeedClasses[$this->mFormat](
					$wikilogTitle->getFullUrl(),
					wfMsgForContent( 'wikilog-feed-title', $title, $wgContLanguageCode ),
					$row->wlw_updated, $wikilogTitle->getFullUrl(), $self
				);
				if ( $row->wlw_subtitle ) {
					$st = @ unserialize( $row->wlw_subtitle );
					if ( is_array( $st ) ) {
						$feed->setSubtitle( new WlTextConstruct( $st[0], $st[1] ) );
					} else if ( is_string( $st ) ) {
						$feed->setSubtitle( $st );
					}
				}
				if ( $row->wlw_icon ) {
					$t = Title::makeTitle( NS_IMAGE, $row->wlw_icon );
					$feed->setIcon( wfFindFile( $t ) );
				}
				if ( $row->wlw_logo ) {
					$t = Title::makeTitle( NS_IMAGE, $row->wlw_logo );
					$feed->setLogo( wfFindFile( $t ) );
				}
				if ( $wgWikilogFeedCategories ) {
					$this->addCategories( $feed, $row->wlw_page );
				}
				if ( $row->wlw_authors ) {
					$authors = unserialize( $row->wlw_authors );
					foreach ( $authors as $user => $userid ) {
						$usertitle = Title::makeTitle( NS_USER, $user );
						$feed->addAuthor( $user, $usertitle->getFullUrl() );
					}
				}
				if ( $this->mCopyright ) {
					$feed->setRights( new WlTextConstruct( 'html', $this->mCopyright ) );
				}
			} else {
				$feed = false;
			}
			$wikilogCache[$title] =& $feed;
		}
		return $wikilogCache[$title];
	}

	/**
	 * Save feed output to cache.
	 *
	 * @param $feed Feed output.
	 * @param $timekey Object cache key for the cached feed timestamp.
	 * @param $feedkey Object cache key for the cached feed output.
	 */
	public function saveToCache( $feed, $timekey, $feedkey ) {
		global $messageMemc;
		$messageMemc->set( $feedkey, $feed );
		$messageMemc->set( $timekey, wfTimestamp( TS_MW ), 24 * 3600 );
	}

	/**
	 * Load feed output from cache.
	 *
	 * @param $tsData Timestamp of the last change of the local data.
	 * @param $timekey Object cache key for the cached feed timestamp.
	 * @param $feedkey Object cache key for the cached feed output.
	 * @return The cached feed output if cache is good, false otherwise.
	 */
	public function loadFromCache( $tsData, $timekey, $feedkey ) {
		global $messageMemc, $wgFeedCacheTimeout;
		$tsCache = $messageMemc->get( $timekey );

		if ( ( $wgFeedCacheTimeout > 0 ) && $tsCache ) {
			$age = time() - wfTimestamp( TS_UNIX, $tsCache );

			# XXX: Minimum feed cache age check disabled. This code is
			# shadowed from ChangesFeed::loadFromCache(), but Vitaliy Filippov
			# noticed that this causes the old cached feed to output with the
			# updated last-modified timestamp, breaking cache behavior.
			# For now, it is disabled, since this is just a performance
			# optimization.
			/* if ( $age < $wgFeedCacheTimeout ) {
				wfDebug( "Wikilog: loading feed from cache -- " .
					"too young: age ($age) < timeout ($wgFeedCacheTimeout) " .
					"($feedkey; $tsCache; $tsData)\n" );
				return $messageMemc->get( $feedkey );
			} else */ if ( $tsCache >= $tsData ) {
				wfDebug( "Wikilog: loading feed from cache -- " .
					"not modified: cache ($tsCache) >= data ($tsData)" .
					"($feedkey)\n" );
				return $messageMemc->get( $feedkey );
			} else {
				wfDebug( "Wikilog: cached feed timestamp check failed -- " .
					"cache ($tsCache) < data ($tsData)\n" );
			}
		}
		return false;
	}

	/**
	 * Returns the keys for the timestamp and feed output in the object cache.
	 */
	function getCacheKeys() {
		$title = $this->mQuery->getWikilogTitle();
		$id = $title ? 'id:' . $title->getArticleId() : 'site';
		$ft = 'show:' . $this->mQuery->getPubStatus() .
			':limit:' . $this->mLimit;
		return array(
			wfMemcKey( 'wikilog', $this->mFormat, $id, 'timestamp' ),
			wfMemcKey( 'wikilog', $this->mFormat, $id, $ft )
		);
	}

	/**
	 * Shadowed from FeedUtils::checkFeedOutput(). The difference is that
	 * this version checks against $wgWikilogFeedClasses instead of
	 * $wgFeedClasses.
	 */
	public function checkFeedOutput() {
		global $wgOut, $wgFeed, $wgWikilogFeedClasses;
		if ( !$wgFeed ) {
			$wgOut->addWikiMsg( 'feed-unavailable' );
			return false;
		}
		if ( !isset( $wgWikilogFeedClasses[$this->mFormat] ) ) {
			wfHttpError( 500, "Internal Server Error", "Unsupported feed type." );
			return false;
		}
		return true;
	}

	/**
	 * Find and add categories for the given feed or entry.
	 */
	private function addCategories( WlSyndicationBase $obj, $pageid ) {
		$scheme = SpecialPage::getTitleFor( 'Categories' )->getFullUrl();
		$res = $this->mDb->select(
			array( 'categorylinks', 'page', 'page_props' ),
			array( 'page_title' ),
			array( /* conds */
				'cl_from' => $pageid,
				'page_title IS NOT NULL',
				'pp_value IS NULL'
			), __METHOD__,
			array( /* options */ ),
			array( /* joins */
				'page' => array( 'LEFT JOIN', array(
					'page_namespace' => NS_CATEGORY,
					'page_title = cl_to'
				) ),
				'page_props' => array( 'LEFT JOIN', array(
					'pp_propname' => 'hiddencat',
					'pp_page = page_id'
				) )
			)
		);
		foreach ( $res as $row ) {
			$term = $row->page_title;
			$label = preg_replace( '/(?:.*\/)?(.+?)(?:\s*\(.*\))?/', '$1', $term );
			$label = str_replace( '_', ' ', $label );
			$obj->addCategory( $term, $scheme, $label );
		}
	}

	/**
	 * Creates an unique ID for a feed entry. Tries to use $wgTaggingEntity
	 * if possible in order to create an RFC 4151 tag, otherwise, we use the
	 * page URL.
	 */
	public static function makeEntryId( $title ) {
		global $wgTaggingEntity;
		if ( $wgTaggingEntity ) {
			$qstr = wfArrayToCGI( array( 'wk' => wfWikiID(), 'id' => $title->getArticleId() ) );
			return "tag:{$wgTaggingEntity}:/MediaWiki/Wikilog?{$qstr}";
		} else {
			return $title->getFullUrl();
		}
	}
}
