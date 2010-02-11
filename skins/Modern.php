<?php
/**
 * Modern skin, derived from Monobook template.
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
class SkinModern extends Skin {

	/**
	 * We don't want common/wikiprintable.css, we have our own print stylesheet.
	 */
	public function commonPrintStylesheet() {
		return false;
	}

	/** @return string skin name */
	public function getSkinName() {
		return 'modern';
	}

	/**
	 * We don't like the default getPoweredBy, the icon clashes with the
	 * skin L&F.
	 */
	function getPoweredBy() {
		global $wgVersion;
		return "<div class=\"mw_poweredby\">Powered by MediaWiki $wgVersion</div>";
	}

	/**
	 * Add skin specific stylesheets
	 * Overridden because we have our own print style.
	 * @param $out OutputPage
	 */
	function setupSkinUserCss( OutputPage $out ) {
		$out->addStyle( 'common/shared.css', 'screen' );
		$out->addStyle( 'modern/main.css', 'screen' );
		$out->addStyle( 'modern/print.css', 'print' );
		$out->addStyle( 'modern/rtl.css', 'screen', '', 'rtl' );
	}

	function pageTitle() {
		global $wgOut;
		$s = '<!-- heading -->' . "\n";
		$s .= '<div id="mw_header"><h1 id="firstHeading">'
			. $wgOut->getPageTitle() .
		'</h1></div>' . "\n";
		return $s;
	}

	/**
	 * Gets the page subtitle and site tagline.
	 * @todo FIXME/CHECKME: maybe not necessary?
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
		$sub .= !empty( $subpages ) ? '<span class="subpages">' . $subpages . '</span>' : $wgOut->getSubtitle();
		return $s;
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

		$s .= $this->pageTitle() . "\n";
		$s .= '<div id="mw_main">
	<div id="mw_contentwrapper">
	<!-- navigation portlet -->';
		$s .= $this->getContentActions();

		$attribs = $this->getAttributes();
		$s .= '<!-- content -->
	<div id="mw_content">
	<!-- contentholder does nothing by default, but it allows users to style the text inside
	     the content area without affecting the meaning of "em" in #mw_content, which is used
	     for the margins -->
	<div id="mw_contentholder"' . $attribs[1] . '>
		<div class="mw-topboxes">
			<div id="mw-js-message" style="display:none;"' . $attribs[0] . '></div>';
		// New talk page messages, if any
		$newtalks = $this->getNewtalks();
		if ( !empty( $newtalks ) ) {
			$s .= '<div class="usermessage mw-topbox">' . $newtalks . '</div>';
		}

		// Site notice ([[MediaWiki:Sitenotice]])
		$notice = wfGetSiteNotice();
		if( $notice ) {
			$s .= "\n<div class=\"mw-topbox\" id=\"siteNotice\">$notice</div>\n";
		}
		$s .= '</div>';
		$s .= $this->pageSubtitle();

		// Undelete link, if available
		$undelete = $this->getUndeleteLink();
		if ( !empty( $undelete ) ) {
			$s .= '<span class="subpages">' . $undelete . '</span>';
		}

		// Jump to navigation links, if user has enabled them in their prefs.
		if ( $wgUser->getOption( 'showjumplinks' ) ) {
			$s .= "\n" . '<div id="jump-to-nav">' . wfMsg( 'jumpto' ) .
					' <a href="#mw_portlets">' . wfMsg( 'jumptonavigation' ) .
					'</a>, <a href="#searchInput">' . wfMsg( 'jumptosearch' ) .
					'</a></div>' . "\n";
		}

		wfProfileOut( __METHOD__ );
		return $s;
	}

	/**
	 * No-op, since the default has two div tags, which would mess up things.
	 * @return nothing
	 */
	function doAfterContent() {}

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
		$out->out( '<div class="mw_clear"></div>' . "\n" );
		$out->out( $this->getCategories() . "\n" );
		$out->out( '<!-- end content -->' . "\n" );

		$out->out( $this->afterContent() );

		$out->out( $afterContent );

		$out->out( '</div><!-- mw_contentholder -->
	</div><!-- mw_content -->
	</div><!-- mw_contentwrapper -->'
		);

		$attribs = $this->getAttributes();
		$out->out( '<div id="mw_portlets"' . $attribs[0] . '>' . "\n" );
		$out->out( "\n" . '<!-- portlets -->' . "\n" );
		$out->out( $this->doSidebar() . "\n" );
		$out->out( '</div><!-- mw_portlets -->' . "\n" );
		$out->out( "\n\n" . '</div><!-- main -->' . "\n" );

		$out->out( '<div class="mw_clear"></div>' . "\n" );

		$out->out( '<!-- personal portlet -->' . "\n" );
		$out->out( $this->getPersonalTools() . "\n" );

		$out->out( '<!-- footer -->' . "\n" );
		$out->out( $this->footer() . "\n" );

		$out->out( $this->bottomScripts() . "\n" );

		$out->out( wfReportTime() . "\n" );

		$out->out( "\n</body></html>" );
		wfProfileOut( __METHOD__ );
	}

} // end of class
