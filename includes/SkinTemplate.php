<?php
if ( ! defined( 'MEDIAWIKI' ) )
	die( 1 );

# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License along
# with this program; if not, write to the Free Software Foundation, Inc.,
# 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
# http://www.gnu.org/copyleft/gpl.html

/**
 * Wrapper object for MediaWiki's localization functions,
 * to be passed to the template engine.
 *
 * @private
 * @ingroup Skins
 */
class MediaWiki_I18N {
	var $_context = array();

	function set( $varName, $value ) {
		$this->_context[$varName] = $value;
	}

	function translate( $value ) {
		wfProfileIn( __METHOD__ );

		// Hack for i18n:attributes in PHPTAL 1.0.0 dev version as of 2004-10-23
		$value = preg_replace( '/^string:/', '', $value );

		$value = wfMsg( $value );
		// interpolate variables
		$m = array();
		while( preg_match( '/\$([0-9]*?)/sm', $value, $m ) ) {
			list( $src, $var ) = $m;
			wfSuppressWarnings();
			$varValue = $this->_context[$var];
			wfRestoreWarnings();
			$value = str_replace( $src, $varValue, $value );
		}
		wfProfileOut( __METHOD__ );
		return $value;
	}
}

/**
 * Template-filler skin base class
 * Formerly generic PHPTal (http://phptal.sourceforge.net/) skin
 * Based on Brion's smarty skin
 * @copyright Copyright © Gabriel Wicke -- http://www.aulinx.de/
 *
 * @todo Needs some serious refactoring into functions that correspond
 * to the computations individual esi snippets need. Most importantly no body
 * parsing for most of those of course.
 *
 * @ingroup Skins
 */
class SkinTemplate extends Skin {
	/**#@+
	 * @private
	 */

	/**
	 * Name of our skin, it probably needs to be all lower case.  Child classes
	 * should override the default.
	 */
	var $skinname = 'monobook';

	/**
	 * Stylesheets set to use.  Subdirectory in skins/ where various stylesheets
	 * are located.  Child classes should override the default.
	 */
	var $stylename = 'monobook';

	/**
	 * For QuickTemplate, the name of the subclass which will actually fill the
	 * template.  Child classes should override the default.
	 */
	var $template = 'QuickTemplate';

	/**
	 * Whether this skin use OutputPage::headElement() to generate the <head>
	 * tag
	 */
	var $useHeadElement = false;

	/**#@-*/

	/**
	 * Add specific styles for this skin
	 *
	 * @param $out OutputPage
	 */
	function setupSkinUserCss( OutputPage $out ){
		$out->addStyle( 'common/shared.css', 'screen' );
		$out->addStyle( 'common/commonPrint.css', 'print' );
	}

	/**
	 * Create the template engine object; we feed it a bunch of data
	 * and eventually it spits out some HTML. Should have interface
	 * roughly equivalent to PHPTAL 0.7.
	 *
	 * @param $callback string (or file)
	 * @param $repository string: subdirectory where we keep template files
	 * @param $cache_dir string
	 * @return object
	 * @private
	 */
	function setupTemplate( $classname, $repository = false, $cache_dir = false ) {
		return new $classname();
	}

	/**
	 * initialize various variables and generate the template
	 *
	 * @param $out OutputPage
	 */
	function outputPage( OutputPage $out ) {
		global $wgArticle, $wgUser, $wgLang, $wgContLang;
		global $wgScript, $wgStylePath, $wgContLanguageCode;
		global $wgMimeType, $wgJsMimeType, $wgOutputEncoding, $wgRequest;
		global $wgXhtmlDefaultNamespace, $wgXhtmlNamespaces, $wgHtml5Version;
		global $wgDisableCounters, $wgLogo, $wgHideInterlanguageLinks;
		global $wgMaxCredits, $wgShowCreditsIfMax;
		global $wgPageShowWatchingUsers;
		global $wgUseTrackbacks, $wgUseSiteJs, $wgDebugComments;
		global $wgArticlePath, $wgScriptPath, $wgServer;

		wfProfileIn( __METHOD__ );

		$oldid = $wgRequest->getVal( 'oldid' );
		$diff = $wgRequest->getVal( 'diff' );
		$action = $wgRequest->getVal( 'action', 'view' );

		wfProfileIn( __METHOD__ . '-init' );
		$this->initPage( $out );

		$this->setMembers();
		$tpl = $this->setupTemplate( $this->template, 'skins' );

		#if ( $wgUseDatabaseMessages ) { // uncomment this to fall back to GetText
		$tpl->setTranslator( new MediaWiki_I18N() );
		#}
		wfProfileOut( __METHOD__ . '-init' );

		wfProfileIn( __METHOD__ . '-stuff' );
		$this->thispage = $this->mTitle->getPrefixedDBkey();
		$this->thisurl = $this->mTitle->getPrefixedURL();
		$query = array();
		if ( !$wgRequest->wasPosted() ) {
			$query = $wgRequest->getValues();
			unset( $query['title'] );
			unset( $query['returnto'] );
			unset( $query['returntoquery'] );
		}
		$this->thisquery = wfUrlencode( wfArrayToCGI( $query ) );
		$this->loggedin = $wgUser->isLoggedIn();
		$this->iscontent = ( $this->mTitle->getNamespace() != NS_SPECIAL );
		$this->iseditable = ( $this->iscontent and !( $action == 'edit' or $action == 'submit' ) );
		$this->username = $wgUser->getName();

		if ( $wgUser->isLoggedIn() || $this->showIPinHeader() ) {
			$this->userpageUrlDetails = self::makeUrlDetails( $this->userpage );
		} else {
			# This won't be used in the standard skins, but we define it to preserve the interface
			# To save time, we check for existence
			$this->userpageUrlDetails = self::makeKnownUrlDetails( $this->userpage );
		}

		$this->titletxt = $this->mTitle->getPrefixedText();
		wfProfileOut( __METHOD__ . '-stuff' );

		wfProfileIn( __METHOD__ . '-stuff-head' );
		if ( $this->useHeadElement ) {
			$pagecss = $this->setupPageCss();
			if( $pagecss )
				$out->addInlineStyle( $pagecss );
		} else {
			$this->setupUserCss( $out );

			$tpl->set( 'pagecss', $this->setupPageCss() );
			$tpl->setRef( 'usercss', $this->usercss );

			$this->userjs = $this->userjsprev = false;
			$this->setupUserJs( $out->isUserJsAllowed() );
			$tpl->setRef( 'userjs', $this->userjs );
			$tpl->setRef( 'userjsprev', $this->userjsprev );

			if( $wgUseSiteJs ) {
				$jsCache = $this->loggedin ? '&smaxage=0' : '';
				$tpl->set( 'jsvarurl',
						  self::makeUrl( '-',
										"action=raw$jsCache&gen=js&useskin=" .
										urlencode( $this->getSkinName() ) ) );
			} else {
				$tpl->set( 'jsvarurl', false );
			}

			$tpl->setRef( 'xhtmldefaultnamespace', $wgXhtmlDefaultNamespace );
			$tpl->set( 'xhtmlnamespaces', $wgXhtmlNamespaces );
			$tpl->set( 'html5version', $wgHtml5Version );
			$tpl->set( 'headlinks', $out->getHeadLinks() );
			$tpl->set( 'csslinks', $out->buildCssLinks() );

			if( $wgUseTrackbacks && $out->isArticleRelated() ) {
				$tpl->set( 'trackbackhtml', $out->getTitle()->trackbackRDF() );
			} else {
				$tpl->set( 'trackbackhtml', null );
			}
		}
		wfProfileOut( __METHOD__ . '-stuff-head' );

		wfProfileIn( __METHOD__ . '-stuff2' );
		$tpl->set( 'title', $out->getPageTitle() );
		$tpl->set( 'pagetitle', $out->getHTMLTitle() );
		$tpl->set( 'displaytitle', $out->mPageLinkTitle );
		$tpl->set( 'pageclass', $this->getPageClasses( $this->mTitle ) );
		$tpl->set( 'skinnameclass', ( 'skin-' . Sanitizer::escapeClass( $this->getSkinName() ) ) );

		$nsname = MWNamespace::exists( $this->mTitle->getNamespace() ) ?
					MWNamespace::getCanonicalName( $this->mTitle->getNamespace() ) :
					$this->mTitle->getNsText();

		$tpl->set( 'nscanonical', $nsname );
		$tpl->set( 'nsnumber', $this->mTitle->getNamespace() );
		$tpl->set( 'titleprefixeddbkey', $this->mTitle->getPrefixedDBKey() );
		$tpl->set( 'titletext', $this->mTitle->getText() );
		$tpl->set( 'articleid', $this->mTitle->getArticleId() );
		$tpl->set( 'currevisionid', isset( $wgArticle ) ? $wgArticle->getLatest() : 0 );

		$tpl->set( 'isarticle', $out->isArticle() );

		$tpl->setRef( 'thispage', $this->thispage );
		$subpagestr = $this->subPageSubtitle();
		$tpl->set(
			'subtitle', !empty( $subpagestr ) ?
			'<span class="subpages">'.$subpagestr.'</span>'.$out->getSubtitle() :
			$out->getSubtitle()
		);
		$undelete = $this->getUndeleteLink();
		$tpl->set(
			'undelete', !empty( $undelete ) ?
			'<span class="subpages">'.$undelete.'</span>' :
			''
		);

		$tpl->set( 'catlinks', $this->getCategories() );
		if( $out->isSyndicated() ) {
			$feeds = array();
			foreach( $out->getSyndicationLinks() as $format => $link ) {
				$feeds[$format] = array(
					'text' => wfMsg( "feed-$format" ),
					'href' => $link
				);
			}
			$tpl->setRef( 'feeds', $feeds );
		} else {
			$tpl->set( 'feeds', false );
		}

		$tpl->setRef( 'mimetype', $wgMimeType );
		$tpl->setRef( 'jsmimetype', $wgJsMimeType );
		$tpl->setRef( 'charset', $wgOutputEncoding );
		$tpl->setRef( 'wgScript', $wgScript );
		$tpl->setRef( 'skinname', $this->skinname );
		$tpl->set( 'skinclass', get_class( $this ) );
		$tpl->setRef( 'stylename', $this->stylename );
		$tpl->set( 'printable', $out->isPrintable() );
		$tpl->set( 'handheld', $wgRequest->getBool( 'handheld' ) );
		$tpl->setRef( 'loggedin', $this->loggedin );
		$tpl->set( 'notspecialpage', $this->mTitle->getNamespace() != NS_SPECIAL );
		/* XXX currently unused, might get useful later
		$tpl->set( "editable", ($this->mTitle->getNamespace() != NS_SPECIAL ) );
		$tpl->set( "exists", $this->mTitle->getArticleID() != 0 );
		$tpl->set( "watch", $this->mTitle->userIsWatching() ? "unwatch" : "watch" );
		$tpl->set( "protect", count($this->mTitle->isProtected()) ? "unprotect" : "protect" );
		$tpl->set( "helppage", wfMsg('helppage'));
		*/
		$tpl->set( 'searchaction', $this->escapeSearchLink() );
		$tpl->set( 'searchtitle', SpecialPage::getTitleFor( 'Search' )->getPrefixedDBKey() );
		$tpl->set( 'search', trim( $wgRequest->getVal( 'search' ) ) );
		$tpl->setRef( 'stylepath', $wgStylePath );
		$tpl->setRef( 'articlepath', $wgArticlePath );
		$tpl->setRef( 'scriptpath', $wgScriptPath );
		$tpl->setRef( 'serverurl', $wgServer );
		$tpl->setRef( 'logopath', $wgLogo );
		$tpl->setRef( 'lang', $wgContLanguageCode );
		$tpl->set( 'dir', $wgContLang->getDir() );
		$tpl->set( 'rtl', $wgContLang->isRTL() );
		$tpl->set( 'capitalizeallnouns', $wgLang->capitalizeAllNouns() ? ' capitalize-all-nouns' : '' );
		$tpl->set( 'langname', $wgContLang->getLanguageName( $wgContLanguageCode ) );
		$tpl->set( 'showjumplinks', $wgUser->getOption( 'showjumplinks' ) );
		$tpl->set( 'username', $wgUser->isAnon() ? null : $this->username );
		$tpl->setRef( 'userpage', $this->userpage );
		$tpl->setRef( 'userpageurl', $this->userpageUrlDetails['href'] );
		$tpl->set( 'userlang', $wgLang->getCode() );

		// Users can have their language set differently than the
		// content of the wiki. For these users, tell the web browser
		// that interface elements are in a different language.
		$attribs = $this->getAttributes();
		$tpl->set( 'userlangattributes', $attribs[0] );
		$tpl->set( 'specialpageattributes', $attribs[1] );

		$newtalks = $this->getNewtalks();

		wfProfileOut( __METHOD__ . '-stuff2' );

		wfProfileIn( __METHOD__ . '-stuff3' );
		$tpl->setRef( 'newtalk', $newtalks );
		$tpl->setRef( 'skin', $this );
		$tpl->set( 'logo', $this->logoText() );
		if ( $out->isArticle() and ( !isset( $oldid ) or isset( $diff ) ) and
			$wgArticle and 0 != $wgArticle->getID() ) {
			$tpl->set( 'viewcount', $this->getViewCount() );

			$tpl->set( 'numberofwatchingusers', $this->getNumberOfWatchingUsers() );

			$tpl->set( 'copyright', $this->getCopyright() );

			$this->credits = false;

			if( $wgMaxCredits != 0 ){
				$this->credits = Credits::getCredits( $wgArticle, $wgMaxCredits, $wgShowCreditsIfMax );
			} else {
				$tpl->set( 'lastmod', $this->lastModified() );
			}

			$tpl->setRef( 'credits', $this->credits );

		} elseif ( isset( $oldid ) && !isset( $diff ) ) {
			$tpl->set( 'copyright', $this->getCopyright() );
			$tpl->set( 'viewcount', false );
			$tpl->set( 'lastmod', false );
			$tpl->set( 'credits', false );
			$tpl->set( 'numberofwatchingusers', false );
		} else {
			$tpl->set( 'copyright', false );
			$tpl->set( 'viewcount', false );
			$tpl->set( 'lastmod', false );
			$tpl->set( 'credits', false );
			$tpl->set( 'numberofwatchingusers', false );
		}
		wfProfileOut( __METHOD__ . '-stuff3' );

		wfProfileIn( __METHOD__ . '-stuff4' );
		$tpl->set( 'copyrightico', $this->getCopyrightIcon() );
		$tpl->set( 'poweredbyico', $this->getPoweredBy() );
		$tpl->set( 'disclaimer', $this->disclaimerLink() );
		$tpl->set( 'privacy', $this->privacyLink() );
		$tpl->set( 'about', $this->aboutLink() );

		if ( $wgDebugComments ) {
			$tpl->setRef( 'debug', $out->mDebugtext );
		} else {
			$tpl->set( 'debug', '' );
		}

		$tpl->set( 'reporttime', wfReportTime() );
		$tpl->set( 'sitenotice', wfGetSiteNotice() );
		$tpl->set( 'bottomscripts', $this->bottomScripts() );

		$printfooter = "<div class=\"printfooter\">\n" . $this->printSource() . "</div>\n";
		$out->mBodytext .= $printfooter . $this->generateDebugHTML();
		$tpl->setRef( 'bodytext', $out->mBodytext );

		# Language links
		$langLinks = $this->fetchInterlanguageLinks();
		if ( $langLinks ) {
			$tpl->setRef( 'language_urls', $langLinks );
		} else {
			$tpl->set( 'language_urls', false );
		}
		wfProfileOut( __METHOD__ . '-stuff4' );

		wfProfileIn( __METHOD__ . '-stuff5' );
		# Personal toolbar
		$tpl->set( 'personal_urls', $this->buildPersonalUrls() );
		$content_actions = $this->buildContentActionUrls();
		$tpl->setRef( 'content_actions', $content_actions );

		$tpl->set( 'sidebar', $this->buildSidebar() );
		$tpl->set( 'nav_urls', $this->buildNavUrls() );

		// Set the head scripts near the end, in case the above actions resulted in added scripts
		if ( $this->useHeadElement ) {
			$tpl->set( 'headelement', $out->headElement( $this ) );
		} else {
			$tpl->set( 'headscripts', $out->getScript() );
		}

		// original version by hansm
		if( !wfRunHooks( 'SkinTemplateOutputPageBeforeExec', array( &$this, &$tpl ) ) ) {
			wfDebug( __METHOD__ . ": Hook SkinTemplateOutputPageBeforeExec broke outputPage execution!\n" );
		}

		// allow extensions adding stuff after the page content.
		// See Skin::afterContentHook() for further documentation.
		$tpl->set( 'dataAfterContent', $this->afterContentHook() );
		wfProfileOut( __METHOD__ . '-stuff5' );

		// execute template
		wfProfileIn( __METHOD__ . '-execute' );
		$res = $tpl->execute();
		wfProfileOut( __METHOD__ . '-execute' );

		// result may be an error
		$this->printOrError( $res );
		wfProfileOut( __METHOD__ );
	}

	/**
	 * Output the string, or print error message if it's
	 * an error object of the appropriate type.
	 * For the base class, assume strings all around.
	 *
	 * @param mixed $str
	 * @private
	 */
	function printOrError( $str ) {
		echo $str;
	}

	function makeArticleUrlDetails( $name, $urlaction = '' ) {
		$title = Title::newFromText( $name );
		$title= $title->getSubjectPage();
		self::checkTitle( $title, $name );
		return array(
			'href' => $title->getLocalURL( $urlaction ),
			'exists' => $title->getArticleID() != 0 ? true : false
		);
	}

	/**
	 * Generate strings used for xml 'id' names
	 * @return string
	 * @private
	 */
	function getNameSpaceKey() {
		return $this->mTitle->getNamespaceKey();
	}

	/**
	 * @private
	 */
	function setupUserJs( $allowUserJs ) {
		global $wgRequest, $wgJsMimeType;
		wfProfileIn( __METHOD__ );

		$action = $wgRequest->getVal( 'action', 'view' );

		if( $allowUserJs && $this->loggedin ) {
			if( $this->mTitle->isJsSubpage() and $this->userCanPreview( $action ) ) {
				# XXX: additional security check/prompt?
				$this->userjsprev = '/*<![CDATA[*/ ' . $wgRequest->getText( 'wpTextbox1' ) . ' /*]]>*/';
			} else {
				$this->userjs = self::makeUrl( $this->userpage . '/' . $this->skinname . '.js', 'action=raw&ctype=' . $wgJsMimeType );
			}
		}
		wfProfileOut( __METHOD__ );
	}

	/**
	 * Code for extensions to hook into to provide per-page CSS, see
	 * extensions/PageCSS/PageCSS.php for an implementation of this.
	 *
	 * @private
	 */
	function setupPageCss() {
		wfProfileIn( __METHOD__ );
		$out = false;
		wfRunHooks( 'SkinTemplateSetupPageCss', array( &$out ) );
		wfProfileOut( __METHOD__ );
		return $out;
	}

	public function commonPrintStylesheet() {
		return false;
	}
}

/**
 * Generic wrapper for template functions, with interface
 * compatible with what we use of PHPTAL 0.7.
 * @ingroup Skins
 */
abstract class QuickTemplate {
	/**
	 * Constructor
	 */
	public function QuickTemplate() {
		$this->data = array();
		$this->translator = new MediaWiki_I18N();
	}

	/**
	 * Sets the value $value to $name
	 * @param $name
	 * @param $value
	 */
	public function set( $name, $value ) {
		$this->data[$name] = $value;
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function setRef( $name, &$value ) {
		$this->data[$name] =& $value;
	}

	/**
	 * @param $t
	 */
	public function setTranslator( &$t ) {
		$this->translator = &$t;
	}

	/**
	 * Main function, used by classes that subclass QuickTemplate
	 * to show the actual HTML output
	 */
	abstract public function execute();

	/**
	 * @private
	 */
	function text( $str ) {
		echo htmlspecialchars( $this->data[$str] );
	}

	/**
	 * @private
	 */
	function jstext( $str ) {
		echo Xml::escapeJsString( $this->data[$str] );
	}

	/**
	 * @private
	 */
	function html( $str ) {
		echo $this->data[$str];
	}

	/**
	 * @private
	 */
	function msg( $str ) {
		echo htmlspecialchars( $this->translator->translate( $str ) );
	}

	/**
	 * @private
	 */
	function msgHtml( $str ) {
		echo $this->translator->translate( $str );
	}

	/**
	 * An ugly, ugly hack.
	 * @private
	 */
	function msgWiki( $str ) {
		global $wgParser, $wgOut;

		$text = $this->translator->translate( $str );
		$parserOutput = $wgParser->parse( $text, $wgOut->getTitle(),
			$wgOut->parserOptions(), true );
		echo $parserOutput->getText();
	}

	/**
	 * @private
	 */
	function haveData( $str ) {
		return isset( $this->data[$str] );
	}

	/**
	 * @private
	 */
	function haveMsg( $str ) {
		$msg = $this->translator->translate( $str );
		return ( $msg != '-' ) && ( $msg != '' ); # ????
	}
}
