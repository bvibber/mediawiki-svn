<?php
/**
 * MonoBook nouveau
 *
 * Translated from gwicke's previous TAL template version to remove
 * dependency on PHPTAL.
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

/**
 * Inherit main code from Skin, set the CSS and template filter.
 * @todo document
 * @ingroup Skins
 */
class SkinMonoBook extends Skin {

	/** @return string path to the skin stylesheet */
	public function getStylesheet() {
		return 'monobook/main.css';
	}

	/** @return string skin name */
	public function getSkinName() {
		return 'monobook';
	}

	/**
	 * Add skin specific stylesheets
	 * Overridden so that oldshared.css won't get added.
	 * @param $out OutputPage
	 */
	function setupSkinUserCss( OutputPage $out ) {
		global $wgHandheldStyle;

		$out->addStyle( 'common/shared.css' );
		$out->addStyle( 'common/commonPrint.css', 'print' );
		$out->addStyle( $this->getStylesheet() );
		$out->addStyle( 'common/common_rtl.css', '', '', 'rtl' );

		// Append to the default screen common & print styles...
		if( $wgHandheldStyle ) {
			// Currently in testing... try 'chick/main.css'
			$out->addStyle( $wgHandheldStyle, 'handheld' );
		}

		$out->addStyle( 'monobook/IE60Fixes.css', 'screen', 'IE 6' );
		$out->addStyle( 'monobook/IE70Fixes.css', 'screen', 'IE 7' );
		$out->addStyle( 'monobook/rtl.css', 'screen', '', 'rtl' );
	}

	/**
	 * Renders things that are displayed before the actual page content.
	 * For example, newtalk notifications, site notice etc.
	 * @return HTML
	 */
	function doBeforeContent() {
		global $wgContLang, $wgUser;
		wfProfileIn( __METHOD__ );

		$s = '';

		$attribs = $this->getAttributes();
		$s .= '<div id="globalWrapper">
		<div id="column-content">
	<div id="content"' . $attribs[1] . '>
		<a id="top"></a>' . "\n";

		// Site notice ([[MediaWiki:Sitenotice]])
		$notice = wfGetSiteNotice();
		if( $notice ) {
			$s .= "\n<div id=\"siteNotice\">$notice</div>\n";
		}

		$s .= $this->pageTitle() . "\n";
		$s .= '<div id="bodyContent">' . "\n";
		$s .= $this->pageSubtitle() . "\n";

		// Undelete link, if available
		$undelete = $this->getUndeleteLink();
		if ( !empty( $undelete ) ) {
			$s .= '<span class="subpages">' . $undelete . '</span>';
		}

		// New talk page messages, if any
		$newtalks = $this->getNewtalks();
		if ( !empty( $newtalks ) ) {
			$s .= '<div class="usermessage">' . $newtalks . '</div>';
		}

		// Jump to navigation links, if user has enabled them in their prefs.
		if ( $wgUser->getOption( 'showjumplinks' ) ) {
			$s .= "\n" . '<div id="jump-to-nav">' . wfMsg( 'jumpto' ) .
					' <a href="#column-one">' . wfMsg( 'jumptonavigation' ) .
					'</a>, <a href="#searchInput">' . wfMsg( 'jumptosearch' ) .
					'</a></div>' . "\n";
		}

		wfProfileOut( __METHOD__ );
		return $s;
	}

	/**
	 * Gets the page subtitle and site tagline.
	 * @return HTML
	 */
	function pageSubtitle() {
		global $wgOut;

		$s = '';
		$attribs = $this->getAttributes();
		$sub = $wgOut->getSubtitle();
		$s .= '<div id="contentSub"' . $attribs[0] . '>' . $sub . '</div>';
		if ( $sub == '' ) {
			global $wgExtraSubtitle;
			$sub = '<h3 id="siteSub">' . wfMsgExt( 'tagline', 'parsemag' ) . $wgExtraSubtitle . '</h3>';
		}
		$subpages = $this->subPageSubtitle();
		if ( !empty( $subpages ) ) {
			$sub .= '<span class="subpages">' . $subpages . '</span>' . $wgOut->getSubtitle();
		} else {
			$sub .= $wgOut->getSubtitle();
		}
		return $s;
	}

	/**
	 * Overridden pageTitle function to give the h1 element a new ID and class.
	 * @return HTML
	 */
	function pageTitle() {
		global $wgOut;
		$s = '<h1 id="firstHeading" class="firstHeading">'
			. $wgOut->getPageTitle() .
		'</h1>';
		return $s;
	}

	/**
	 * No-op, since the default has two div tags, which would mess up things.
	 * @return nothing
	 */
	function doAfterContent() {}

	/**
	 * Builds personal tools (links to own user/talk pages, contribs, etc.)
	 * @return HTML
	 */
	function getPersonalTools() {
		$attribs = $this->getAttributes();
		$s = '<div class="portlet" id="p-personal">
		<h5>' . wfMsg( 'personaltools' ) . '</h5>
		<div class="pBody">
			<ul' . $attribs[0] . '>'
				. $this->renderPersonalTools() .
			'</ul>
		</div>
	</div>';
		return $s;
	}

	/**
	 * Gets content action tabs (edit, move, history, watch...).
	 * @return HTML
	 */
	function getContentActions() {
		$s = '<div id="p-cactions" class="portlet">
		<h5>' . wfMsg( 'views' ) . '</h5>
		<div class="pBody">
			<ul>' . $this->renderContentActions() . '</ul>
		</div>
	</div>';
		return $s;
	}

	/**
	 * Gets the interlanguage links box.
	 * @return HTML
	 */
	function getInterlanguageLinksBox() {
		$s = '';
		$language_urls = $this->fetchInterlanguageLinks();
		// Display the interlanguage links if we have any
		if( count( $language_urls ) ) {
			$attribs = $this->getAttributes();
			$s .= '<div id="p-lang" class="portlet">
		<h5' . $attribs[0] . '>' . wfMsg( 'otherlanguages' ) . '</h5>
		<div class="pBody">
			<ul>';
			foreach( $language_urls as $langlink ) {
				$s .= '<li class="' . htmlspecialchars( $langlink['class'] ) . '">
					<a href="' . htmlspecialchars( $langlink['href'] ) . '">'
						. $langlink['text'] .
					'</a>
				</li>';
			}
			$s .= '</ul>
		</div>
	</div>';
		}
		return $s;
	}

	/**
	 * Gets the logo portlet.
	 * @return HTML
	 */
	function getLogoPortlet() {
		global $wgLogo;
		$navUrls = $this->buildNavUrls();
		$s = '<div class="portlet" id="p-logo">
		<a style="background-image: url(' . $wgLogo . ');" href="' .
			htmlspecialchars( $navUrls['mainpage']['href'] ) . '"' .
			$this->tooltipAndAccesskey( 'p-logo' ) . '></a>
	</div>';
		return $s;
	}

	/**
	 * Main search form. searchBox() does the UI stuff.
	 * @return HTML
	 */
	function searchForm() {
		global $wgScript, $wgRequest, $wgUseTwoButtonsSearchForm;

		$searchTitle = SpecialPage::getTitleFor( 'Search' )->getPrefixedDBkey();
		$searchQuery = trim( $wgRequest->getVal( 'search' ) );

		$s = '<form action="' . $wgScript . '" id="searchform">
			<input type="hidden" name="title" value="' . $searchTitle . '" />';
		$s .= Html::input(
			'search',
			isset( $searchQuery ) ? $searchQuery : '', 'search',
			array(
				'id' => 'searchInput',
				'title' => $this->titleAttrib( 'search' ),
				'accesskey' => $this->accesskey( 'search' )
			)
		) . "\n";

		$s .= '<input type="submit" name="go" class="searchButton" id="searchGoButton"	value="' . wfMsg( 'searcharticle' ) . '"' . $this->tooltipAndAccesskey( 'search-go' ) . ' />';
		if ( $wgUseTwoButtonsSearchForm ) {
			$s .= '&nbsp;
				<input type="submit" name="fulltext" class="searchButton" id="mw-searchButton" value="' . wfMsg( 'searchbutton' ) . '"' . $this->tooltipAndAccesskey( 'search-fulltext' ) . ' />';
		} else {
			$s .= '<div><a href="' . $this->escapeSearchLink() . '" rel="search">'
				. wfMsg( 'powersearch-legend' ) . '</a></div>';
		}

		$s .= '</form>';
		return $s;
	}

	function outputPage( OutputPage $out ) {
		global $wgDebugComments;
		wfProfileIn( __METHOD__ );

		$this->setMembers();
		$this->initPage( $out );

		// See self::afterContentHook() for documentation
		$afterContent = $this->afterContentHook();

		$out->out( $out->headElement( $this ) );

		if ( $wgDebugComments ) {
			$out->out(
				"<!-- Wiki debugging output:\n" .
				$out->mDebugtext . "-->\n"
			);
		}

		$out->out( $this->beforeContent() );

		$out->out( '<!-- start content -->' . "\n" );
		$out->out( $out->mBodytext . "\n" );
		$out->out( $this->getCategories() . "\n" );
		$out->out( '<!-- end content -->' . "\n" );

		$out->out( $this->afterContent() );

		$out->out( $afterContent );

		$attribs = $this->getAttributes();
		$out->out( '<div class="visualClear"></div>
		</div><!-- #bodyContent -->
	</div><!-- #content -->
		</div><!-- #column-content -->
		<div id="column-one"' . $attribs[0] . '>' . "\n" );

		$out->out( $this->getContentActions() . "\n" );

		$out->out( $this->getPersonalTools() . "\n" );

		$out->out( $this->getLogoPortlet() . "\n" );

		global $wgJsMimeType;
		$out->out( '<script type="' . $wgJsMimeType . '"> if ( window.isMSIE55 ) fixalpha(); </script>' . "\n" );

		$out->out( $this->doSidebar() . "\n" );

		$out->out(
			"\t\t" . '</div><!-- end of the left (by default at least) column (column-one) -->' . "\n" .
			'<div class="visualClear"></div>' . "\n"
		);

		$out->out( $this->footer() . "\n" );
		$out->out( '</div><!-- #globalWrapper -->' . "\n" );

		$out->out( $this->bottomScripts() . "\n" );

		$out->out( wfReportTime() . "\n" );

		$out->out( "\n</body></html>" );
		wfProfileOut( __METHOD__ );
	}

} // end of class