<?php

/**
 * Abstract class allowing access to a list of changes to pages, drawn from the
 * recentchanges table.
 *
 * @addtogroup SpecialPage
 * @author Simetrical
 */

abstract class ChangesPage extends SpecialPage {
	/**
	 * $mTimespan takes the form array( begintime, endtime ), where each of
	 * begintime and endtime is either a standard MW timestamp or null (since
	 * beginning/to present: i.e., no limit).
	 */
	protected $mTimespan = array();

	/**
	 * The maximum number of results to show per page, to be passed to the
	 * Pager.
	 */
	protected $mPerPage = 50;

	/**
	 * An integer namespace number or array of namespace numbers to return
	 * results from.  null for all namespaces.  Derived classes may wish to
	 * restrict this variable's values for efficiency by overriding
	 * setNamespaces().
	 */
	private $mNamespaces = null;
	
	/**
	 * An array of boolean options to control what edits get displayed.
	 * Derived classes should change these defaults if appropriate.
	 */
	protected $mShow = array(
		'own'       => true,
		'bot'       => true,
		'anon'      => true,
		'loggedin'  => true,
		'patrolled' => true,
		'minor'     => true
	);

	/**
	 * Is this an RSS/Atom/whatever feed?
	 */
	protected $mFeed = false;

	/**
	 * Derived classes should call this constructor to initialize stuff.
	 *
	 * @param $restriction What permission, if any, is needed to access
	 * @param $listed      Whether to list the page on Special:Specialpages.
	 * @param $includable  Whether the page can be included in non-special
	 *   pages
	 */
	protected __construct( $restriction='', $listed=true, $includable=false ) {
		wfProfileIn( __METHOD__ );
		parent::__construct( '', $restriction, $listed, false, 'default', $includable );
		
		// Calculating all these variants is confusing and error-prone to do by
		// hand, so this is split off into a function.
		$this->calcShow( 'own', array( 'hidemyself', 'hideown' );
		$this->calcShow( 'bots', array( 'hidebots', 'hideBots' ) );
		$this->calcShow( 'anon', 'hideanons' );
		$this->calcShow( 'loggedin', 'hideliu' );
		$this->calcShow( 'patrolled', 'hidepatrolled' );
		$this->calcShow( 'minor', 'hideminor' );
		
		// It makes no sense to hide both anons and logged-in users.  Show
		// anons if this occurs.
		if( $this->mShow['anon'] and $this->mShow['loggedin'] ) {
			$this->mShow['anon'] = true;
		}
		
		wfProfileOut( __METHOD__ );
	}

	/**
	 * Based on the query string, decide whether a particular class of edits
	 * should be hidden or shown and set member variables appropriately.  The
	 * preferred URL variant is the first element of the $positive array;
	 * others are kept for compatibility.  If different parameters conflict,
	 * the edits in question are shown.
	 *
	 * @param $positive Array of strings to check for in the URL, that indicate
	 *   the class of edits should be shown.  The first is the index into
	 *   $this->mShow and the preferred form; the others are deprecated.
	 * @param $negative Array of strings to check for in the URL, that indicate
	 *   the class of edits should not be shown.  All are deprecated.
	 * @return nothing
	 */
	private static function calcShow( $positive, $negative ) {
		global $wgRequest;
	
		if( !is_array( $positive ) ) {
			$positive = array( $positive );
		}
		if( !is_array( $negative ) ) {
			$negative = array( $negative );
		}
		$key = $positive[0];
		foreach( array( $positive, $negative ) as $arr ) {
			foreach( $arr as $param ) {
				$val = $wgRequest->getBoolOrNull( $param );
				if( $val !== null and $arr == $negative ) {
					$val = !$val;
				}
				if( $val === null ) {
					continue;
				} else {
					$this->mShow[$key] = $val;
					if( $val ) {
						// Short-circuit on true values
						return;
					}
				}
			}
		}
	}

	/**
	 * Show the special page
	 */
	public function execute() {
		wfProfileIn( __METHOD__ );
		
		
		
		wfProfileOut( __METHOD__ );
	}

	/**
	 * @return an object belonging to a subclass of ChangesPager to do the
	 * paging.
	 */
	abstract protected function getPager();
}

/**
 * @addtogroup Pager
 */
abstract class ChangesPager extends ReverseChronologicalPager {
	/** The instance of the ChangesPage class that this corresponds to. */
	protected $mChangesPage = null;

	/** The user's skin object. */
	protected $mSkin = null;
	
	public function __construct( ChangesPage &$changesPage, Skin &$skin ) {
		parent::__construct();
		$this->mChangesPage = &$changesPage;
		$this->mSkin = &$skin;
	}
	
	public function formatRow( $row ) {
		wfProfileIn( __METHOD__ );
	
		if( !$title->userCan( 'read' ) ) {
			// This should be dealt with on the query level, ideally, but that
			// might not always be possible?  This will result in an incorrect
			// number of rows being returned if we just blank it, so return
			// a message instead . . .
			wfProfileOut(__METHOD__);
			return '<li class="mw-'.$this->getRowClass()' mw-hidden">' .
				wfMsgExt( 'hidden-changes-result', array( 'parseinline' ) ) .
			'</li>';
		}
		$ret = '<li class="mw-'.$this->getRowClass()'">';
		if( $this->hasOldId( $row ) ) {
			$ret .= '<a href="' .
					$this->getDiffLink( $row ) .
				'" title="' .
					$this->getPageName( $row ) .
				'" tabindex=';
		}
		
		wfProfileOut( __METHOD__ );
	}
	
	/**
	 * Provide the class that should be used for rows (minus 'mw-' prefix).
	 */
	abstract private function getRowClass();
}

?>
<?php

/**
 * Abstract class allowing access to a list of changes to pages, drawn from the
 * recentchanges table.
 *
 * @addtogroup SpecialPage
 * @author Simetrical
 */

abstract class ChangesPage extends SpecialPage {
	/**
	 * $mTimespan takes the form array( begintime, endtime ), where each of
	 * begintime and endtime is either a standard MW timestamp or null (since
	 * beginning/to present: i.e., no limit).
	 */
	protected $mTimespan = array();

	/**
	 * The maximum number of results to show per page, to be passed to the
	 * Pager.
	 */
	protected $mPerPage = 50;

	/**
	 * An integer namespace number or array of namespace numbers to return
	 * results from.  null for all namespaces.  Derived classes may wish to
	 * restrict this variable's values for efficiency by overriding
	 * setNamespaces().
	 */
	private $mNamespaces = null;
	
	/**
	 * An array of boolean options to control what edits get displayed.
	 * Derived classes should change these defaults if appropriate.
	 */
	protected $mShow = array(
		'own'       => true,
		'bot'       => true,
		'anon'      => true,
		'loggedin'  => true,
		'patrolled' => true,
		'minor'     => true
	);

	/**
	 * Is this an RSS/Atom/whatever feed?
	 */
	protected $mFeed = false;

	/**
	 * Derived classes should call this constructor to initialize stuff.
	 *
	 * @param $restriction What permission, if any, is needed to access
	 * @param $listed      Whether to list the page on Special:Specialpages.
	 * @param $includable  Whether the page can be included in non-special
	 *   pages
	 */
	protected __construct( $restriction='', $listed=true, $includable=false ) {
		wfProfileIn( __METHOD__ );
		parent::__construct( '', $restriction, $listed, false, 'default', $includable );
		
		// Calculating all these variants is confusing and error-prone to do by
		// hand, so this is split off into a function.
		$this->calcShow( 'own', array( 'hidemyself', 'hideown' );
		$this->calcShow( 'bots', array( 'hidebots', 'hideBots' ) );
		$this->calcShow( 'anon', 'hideanons' );
		$this->calcShow( 'loggedin', 'hideliu' );
		$this->calcShow( 'patrolled', 'hidepatrolled' );
		$this->calcShow( 'minor', 'hideminor' );
		
		// It makes no sense to hide both anons and logged-in users.  Show
		// anons if this occurs.
		if( $this->mShow['anon'] and $this->mShow['loggedin'] ) {
			$this->mShow['anon'] = true;
		}
		
		wfProfileOut( __METHOD__ );
	}

	/**
	 * Based on the query string, decide whether a particular class of edits
	 * should be hidden or shown and set member variables appropriately.  The
	 * preferred URL variant is the first element of the $positive array;
	 * others are kept for compatibility.  If different parameters conflict,
	 * the edits in question are shown.
	 *
	 * @param $positive Array of strings to check for in the URL, that indicate
	 *   the class of edits should be shown.  The first is the index into
	 *   $this->mShow and the preferred form; the others are deprecated.
	 * @param $negative Array of strings to check for in the URL, that indicate
	 *   the class of edits should not be shown.  All are deprecated.
	 * @return nothing
	 */
	private static function calcShow( $positive, $negative ) {
		global $wgRequest;
	
		if( !is_array( $positive ) ) {
			$positive = array( $positive );
		}
		if( !is_array( $negative ) ) {
			$negative = array( $negative );
		}
		$key = $positive[0];
		foreach( array( $positive, $negative ) as $arr ) {
			foreach( $arr as $param ) {
				$val = $wgRequest->getBoolOrNull( $param );
				if( $val !== null and $arr == $negative ) {
					$val = !$val;
				}
				if( $val === null ) {
					continue;
				} else {
					$this->mShow[$key] = $val;
					if( $val ) {
						// Short-circuit on true values
						return;
					}
				}
			}
		}
	}

	/**
	 * Show the special page
	 */
	public function execute() {
		wfProfileIn( __METHOD__ );
		
		
		
		wfProfileOut( __METHOD__ );
	}

	/**
	 * @return an object belonging to a subclass of ChangesPager to do the
	 * paging.
	 */
	abstract protected function getPager();
}

/**
 * @addtogroup Pager
 */
abstract class ChangesPager extends ReverseChronologicalPager {
	/** The instance of the ChangesPage class that this corresponds to. */
	protected $mChangesPage = null;

	/** The user's skin object. */
	protected $mSkin = null;
	
	public function __construct( ChangesPage &$changesPage, Skin &$skin ) {
		parent::__construct();
		$this->mChangesPage = &$changesPage;
		$this->mSkin = &$skin;
	}
	
	public function formatRow( $row ) {
		wfProfileIn( __METHOD__ );
	
		if( !$title->userCan( 'read' ) ) {
			// This should be dealt with on the query level, ideally, but that
			// might not always be possible?  This will result in an incorrect
			// number of rows being returned if we just blank it, so return
			// a message instead . . .
			wfProfileOut(__METHOD__);
			return '<li class="mw-'.$this->getRowClass()' mw-hidden">' .
				wfMsgExt( 'hidden-changes-result', array( 'parseinline' ) ) .
			'</li>';
		}
		$ret = '<li class="mw-'.$this->getRowClass()'">';
		if( $this->hasOldId( $row ) ) {
			$ret .= '<a href="' .
					$this->getDiffLink( $row ) .
				'" title="' .
					$this->getPageName( $row ) .
				'" tabindex=';
		}
		
		wfProfileOut( __METHOD__ );
	}
	
	/**
	 * Provide the class that should be used for rows (minus 'mw-' prefix).
	 */
	abstract private function getRowClass();
}

?>
