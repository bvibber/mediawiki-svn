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
 * Wikilog comments namespace handler class.
 *
 * Displays a threaded discussion about a wikilog article, using its talk
 * page, replacing the mess that is the usual wiki talk pages. This allows
 * a simpler and faster interface for commenting on wikilog articles, more
 * like how traditional blogs work. It also allows other interesting things
 * that are difficult or impossible with usual talk pages, like counting the
 * number of comments for each post and generation of syndication feeds for
 * comments.
 *
 * @note This class was designed to integrate with Wikilog, and won't work
 * for the rest of the wiki. If you wan't a similar interface for the other
 * talk pages, you may want to check LiquidThreads or some other extension.
 */
class WikilogCommentsPage
	extends Article
	implements WikilogCustomAction
{
	protected $mSkin;				///< Skin used for rendering the page.
	protected $mFormOptions;		///< Post comment form fields.
	protected $mUserCanPost;		///< User is allowed to post.
	protected $mUserCanModerate;	///< User is allowed to moderate.
	protected $mPostedComment;		///< Posted comment, from HTTP post data.
	protected $mCaptchaForm;		///< Captcha form fields, when saving comment.
	protected $mTrailing;			///< Trailing text in comments title page.

	public    $mItem;				///< Wikilog item the page is associated with.
	public    $mTalkTitle;			///< Main talk page title.
	public    $mSingleComment;		///< Used when viewing a single comment.

	/**
	 * Constructor.
	 *
	 * @param $title Title of the page.
	 * @param $wi WikilogInfo object with information about the wikilog and
	 *   the item.
	 */
	function __construct( Title &$title, WikilogInfo &$wi ) {
		global $wgUser, $wgRequest;

		parent::__construct( $title );
		wfLoadExtensionMessages( 'Wikilog' );

		$this->mSkin = $wgUser->getSkin();

		# Get item object relative to this comments page.
		$this->mItem = WikilogItem::newFromInfo( $wi );

		# Check if user can post.
		$this->mUserCanPost = $wgUser->isAllowed( 'wl-postcomment' ) ||
			( $wgUser->isAllowed( 'edit' ) && $wgUser->isAllowed( 'createtalk' ) );
		$this->mUserCanModerate = $wgUser->isAllowed( 'wl-moderation' );

		# Form options.
		$this->mFormOptions = new FormOptions();
		$this->mFormOptions->add( 'wlAnonName', '' );
		$this->mFormOptions->add( 'wlComment', '' );
		$this->mFormOptions->fetchValuesFromRequest( $wgRequest,
			array( 'wlAnonName', 'wlComment' ) );

		# This flags if we are viewing a single comment (subpage).
		$this->mTrailing = $wi->getTrailing();
		$this->mTalkTitle = $wi->getItemTalkTitle();
		if ( $this->mItem && $this->mTrailing ) {
			$this->mSingleComment =
				WikilogComment::newFromPageID( $this->mItem, $this->getID() );
		}
	}

	/**
	 * Handler for action=view requests.
	 */
	public function view() {
		global $wgRequest, $wgOut;

		# If diffing, don't show comments.
		if ( $wgRequest->getVal( 'diff' ) )
			return parent::view();

		# Normal page view, show talk page contents followed by comments.
		if ( $this->mItem ) {
			$this->viewHeader();
		}

		# Display talk page contents.
		parent::view();

		# Retrieve comments from database and display them.
		if ( $this->mItem ) {
			$this->viewComments();
		}

		# Set a more human-friendly title to the comments page.
		# NOTE (MW1.16+): Must come after parent::view().
		if ( !$this->mSingleComment ) {
			# Note: Sorry for the three-level cascade of wfMsg()'s...
			$fullPageTitle = wfMsg( 'wikilog-title-item-full',
					$this->mItem->mName,
					$this->mItem->mParentTitle->getPrefixedText()
			);
			$fullPageTitle = wfMsg( 'wikilog-title-comments', $fullPageTitle );
			$wgOut->setPageTitle( wfMsg( 'wikilog-title-comments', $this->mItem->mName ) );
			$wgOut->setHTMLTitle( wfMsg( 'pagetitle', $fullPageTitle ) );
		}
	}

	/**
	 * Wikilog comments page header.
	 */
	protected function viewHeader() {
		global $wgOut, $wgUser;

		if ( $this->mSingleComment ) {
			# When viewing a single comment, add comment metadata.
			$meta = $this->formatCommentMetadata( $this->mSingleComment );
			$wgOut->addHtml( Xml::tags(
				'div', array( 'class' => 'wl-comment-meta' ), $meta
			) );
		}

		# Add a backlink to the original article. Specially important in
		# single comment pages.
		$skin = $wgUser->getSkin();
		$link = $skin->link( $this->mItem->mTitle, $this->mItem->mName );
		$wgOut->setSubtitle( wfMsg( 'wikilog-backlink', $link ) );
	}

	/**
	 * Wikilog comments view. Retrieve comments from database and display
	 * them in threads.
	 */
	protected function viewComments() {
		global $wgOut, $wgRequest;

		$wgOut->addHtml( Xml::openElement( 'div', array( 'class' => 'wl-comments' ) ) );

		if ( $this->mSingleComment ) {
			$pid = $this->mSingleComment->getID();	# Post ID

			# == Replies ==
			$header = Xml::tags( 'h2',
				array( 'id' => 'wl-comments-header' ),
				wfMsgExt( 'wikilog-replies', array( 'parseinline' ) )
			);
			$wgOut->addHtml( $header );

			# Display comment replies.
			$replyTo = $wgRequest->getInt( 'wlParent', $pid );
			$replies = $this->formatComments( $this->mSingleComment, $replyTo );
			$wgOut->addHtml( $replies );

			# Display "post new reply" form, if appropriate.
			if ( $replyTo == $pid && $this->mUserCanPost ) {
				$wgOut->addHtml( $this->getPostCommentForm( $pid ) );
			}
		} else if ( !$this->mTrailing ) {
			# == Comments ==
			$header = Xml::tags( 'h2',
				array( 'id' => 'wl-comments-header' ),
				wfMsgExt( 'wikilog-comments', array( 'parseinline' ) )
			);
			$wgOut->addHtml( $header );

			# Display article comments.
			$replyTo = $wgRequest->getInt( 'wlParent' );
			$comments = $this->formatComments( NULL, $replyTo );
			$wgOut->addHtml( $comments );

			# Display "post new comment" form, if appropriate.
			if ( !$replyTo && $this->mUserCanPost ) {
				$wgOut->addHtml( $this->getPostCommentForm() );
			}
		}

		$wgOut->addHtml( Xml::closeElement( 'div' ) );
	}

	/**
	 * Handler for action=wikilog requests.
	 * Enabled via WikilogHooks::UnknownAction() hook handler.
	 */
	public function wikilog() {
		global $wgOut, $wgUser, $wgRequest;

		if ( !$this->mItem || !$this->mItem->exists() ) {
			$wgOut->showErrorPage( 'wikilog-error', 'wikilog-no-such-article' );
			return;
		}
		if ( !$wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) ) {
			$wgOut->showErrorPage( 'wikilog-error', 'sessionfailure' );
			return;
		}

		# Initialize a session, when an anonymous post a comment...
		if ( session_id() == '' ) {
			wfSetupSession();
		}

		if ( $wgRequest->wasPosted() ) {
			# HTTP post: either comment preview or submission.
			if ( !$this->mUserCanPost ) {
				$wgOut->permissionRequired( 'wl-postcomment' );
				return;
			}
			$this->mPostedComment = $this->getPostedComment();
			if ( $this->mPostedComment ) {
				if ( $wgRequest->getBool( 'wlActionCommentSubmit' ) ) {
					return $this->postComment( $this->mPostedComment );
				}
				if ( $wgRequest->getBool( 'wlActionCommentPreview' ) ) {
					return $this->view();
				}
			}
		} else {
			# Comment moderation, actions performed to single-comment pages.
			if ( $this->mSingleComment ) {
				# Check permissions.
				$title = $this->mSingleComment->getCommentArticleTitle();
				$permerrors = $title->getUserPermissionsErrors( 'wl-moderation', $wgUser );
				if ( count( $permerrors ) > 0 ) {
					$wgOut->showPermissionsErrorPage( $permerrors );
					return;
				}

				$approval = $wgRequest->getVal( 'wlActionCommentApprove' );

				# Approve or reject a pending comment.
				if ( $approval ) {
					return $this->setCommentApproval( $this->mSingleComment, $approval );
				}
			}
		}

		$wgOut->showErrorPage( 'nosuchaction', 'nosuchactiontext' );
	}

	/**
	 * Override Article::hasViewableContent() so that it doesn't return 404
	 * if the item page exists.
	 */
	public function hasViewableContent() {
		return parent::hasViewableContent() ||
			( $this->mItem !== NULL && $this->mItem->exists() );
	}

	/**
	 * Formats wikilog article comments in a threaded format.
	 *
	 * @param $parent Parent comment, if not NULL, only the thread below
	 *   the given comment will be displayed.
	 * @param $replyTo Comment ID to attach a reply form to.
	 * @return Generated HTML.
	 */
	public function formatComments( $parent = NULL, $replyTo = false ) {
		global $wgOut;

		$comments = $this->mItem->getComments( $parent ? $parent->mThread : NULL );
		$top = count( $stack = array() );

		$html = Xml::openElement( 'div', array( 'class' => 'wl-threads' ) );

		foreach ( $comments as $comment ) {
			while ( $top > 0 && $comment->mParent != $stack[$top - 1] ) {
				$html .= Xml::closeElement( 'div' );
				array_pop( $stack ); $top--;
			}

			$html .= Xml::openElement( 'div', array( 'class' => 'wl-thread' ) ) .
				$this->formatComment( $comment );

			if ( $comment->mID == $replyTo && $this->mUserCanPost ) {
				$html .= Xml::wrapClass( $this->getPostCommentForm( $comment->mID ),
					'wl-thread', 'div' );
			}

			$top = array_push( $stack, $comment->mID );
		}

		while ( array_pop( $stack ) ) {
			$html .= Xml::closeElement( 'div' );
		}

		$html .= Xml::closeElement( 'div' );	// wl-threads
		return $html;
	}

	/**
	 * Formats a single post in HTML.
	 *
	 * @param $comment Comment to be formatted.
	 * @return Generated HTML.
	 */
	protected function formatComment( $comment ) {
		global $wgUser, $wgOut;

		$hidden = WikilogComment::$statusMap[ $comment->mStatus ];

		/* div class */
		$divclass = array( 'wl-comment' );
		if ( !$comment->isVisible() ) {
			$divclass[] = "wl-comment-{$hidden}";
		}
		if ( $comment->mUserID ) {
			$divclass[] = 'wl-comment-by-user';
			if ( isset( $comment->mItem->mAuthors[$comment->mUserText] ) ) {
				$divclass[] = 'wl-comment-by-author';
			}
		} else {
			$divclass[] = 'wl-comment-by-anon';
		}

		/* body */
		if ( !$comment->isVisible() && !$this->mUserCanModerate ) {
			/* placeholder */
			$status = wfMsg( "wikilog-comment-{$hidden}" );
			$html = Xml::tags( 'div', array( 'class' => 'wl-comment-placeholder' ),
				$status );
		} else {
			$meta = $this->formatCommentMetadata( $comment );
			$text = $wgOut->parse( $comment->getText() );  // TODO: Optimize this.
			$html =
				Xml::tags( 'div', array( 'class' => 'wl-comment-meta' ), $meta ) .
				Xml::tags( 'div', array( 'class' => 'wl-comment-text' ), $text );
		}

		/* enclose everything in a div */
		return Xml::tags( 'div', array(
			'class' => implode( ' ', $divclass ),
			'id' => ( $comment->mID ? "c{$comment->mID}" : 'cpreview' )
		), $html );
	}

	protected function formatCommentMetadata( $comment ) {
		global $wgLang;

		if ( $comment->mUserID ) {
			$by = wfMsgExt( 'wikilog-comment-by-user',
				array( 'parseinline', 'replaceafter' ),
				'<span class="wl-comment-author">' . $this->mSkin->userLink( $comment->mUserID, $comment->mUserText ) . '</span>',
				$this->mSkin->userTalkLink( $comment->mUserID, $comment->mUserText ),
				$comment->mUserText
			);
		} else {
			$by = wfMsgExt( 'wikilog-comment-by-anon',
				array( 'parseinline', 'replaceafter' ),
				'<span class="wl-comment-author">' . $this->mSkin->userLink( $comment->mUserID, $comment->mUserText ) . '</span>',
				$this->mSkin->userTalkLink( $comment->mUserID, $comment->mUserText ),
				htmlspecialchars( $comment->mAnonName )
			);
		}

		$link = $this->getCommentPermalink( $comment );
		$tools = $this->getCommentToolLinks( $comment );
		$ts = $wgLang->timeanddate( $comment->mTimestamp, true );
		$meta = "{$link} {$by} &#8226; {$ts} &#8226; <small>{$tools}</small>";

		if ( !$comment->isVisible() ) {
			$hidden = WikilogComment::$statusMap[ $comment->mStatus ];
			$status = wfMsg( "wikilog-comment-{$hidden}" );
			$meta .= "<div class=\"wl-comment-status\">{$status}</div>";
		}
		if ( $comment->mUpdated != $comment->mTimestamp ) {
			$updated = wfMsg(
				'wikilog-comment-edited',
				$wgLang->timeanddate( $comment->mUpdated, true ),
				$this->getCommentHistoryLink( $comment ),
				$wgLang->date( $comment->mUpdated, true ),
				$wgLang->time( $comment->mUpdated, true )
			);
			$meta .= "<div class=\"wl-comment-edited\">{$updated}</div>";
		}

		return $meta;
	}

	protected function getCommentPermalink( $comment ) {
		if ( $comment->mID ) {
			$title = clone $this->getTitle();
			$title->setFragment( "#c{$comment->mID}" );
			return $this->mSkin->link( $title, '#',
				array( 'title' => wfMsg( 'permalink' ) ) );
		} else {
			return '#';
		}
	}

	protected function getCommentToolLinks( $comment ) {
		global $wgUser;
		$tools = array();

		if ( $comment->mID && $comment->mCommentTitle &&
				$comment->mCommentTitle->exists() ) {
			if ( $this->mUserCanPost && $comment->isVisible() ) {
				$tools[] = $this->getCommentReplyLink( $comment );
			}
			if ( $this->mUserCanModerate ) {
				$tools[] = $this->mSkin->link( $comment->mCommentTitle,
					wfMsg( 'wikilog-page-lc' ),
					array( 'title' => wfMsg( 'wikilog-comment-page' ) ),
					array( ), 'known' );
			}
			if ( $comment->mCommentTitle->quickUserCan( 'edit' ) ) {
				$tools[] = $this->mSkin->link( $comment->mCommentTitle,
					wfMsg( 'wikilog-edit-lc' ),
					array( 'title' => wfMsg( 'wikilog-comment-edit' ) ),
					array( 'action' => 'edit' ), 'known' );
			}
			if ( $comment->mCommentTitle->quickUserCan( 'delete' ) ) {
				$tools[] = $this->mSkin->link( $comment->mCommentTitle,
					wfMsg( 'wikilog-delete-lc' ),
					array( 'title' => wfMsg( 'wikilog-comment-delete' ) ),
					array( 'action' => 'delete' ), 'known' );
			}

			if ( $this->mUserCanModerate && $comment->mStatus == WikilogComment::S_PENDING ) {
				$token = $wgUser->editToken();
				$tools[] = $this->mSkin->link( $comment->mCommentTitle,
					wfMsg( 'wikilog-approve-lc' ),
					array( 'title' => wfMsg( 'wikilog-comment-approve' ) ),
					array(
						'action' => 'wikilog',
						'wlActionCommentApprove' => 'approve',
						'wpEditToken' => $token
					),
					'known' );
				$tools[] = $this->mSkin->link( $comment->mCommentTitle,
					wfMsg( 'wikilog-reject-lc' ),
					array( 'title' => wfMsg( 'wikilog-comment-reject' ) ),
					array(
						'action' => 'wikilog',
						'wlActionCommentApprove' => 'reject',
						'wpEditToken' => $token
					),
					'known' );
			}
		}

		if ( !empty( $tools ) ) {
			$tools = implode( wfMsg( 'comma-separator' ), $tools );
			return wfMsg( 'wikilog-brackets', $tools );
		} else {
			return '';
		}
	}

	protected function getCommentReplyLink( $comment ) {
		$title = clone $this->getTitle();
		$title->setFragment( "#c{$comment->mID}" );
		return $this->mSkin->link( $title, wfMsg( 'wikilog-reply-lc' ),
			array( 'title' => wfMsg( 'wikilog-reply-to-comment' ) ),
			array( 'wlParent' => $comment->mID ) );
	}

	protected function getCommentHistoryLink( $comment ) {
		return $this->mSkin->link( $comment->mCommentTitle,
			wfMsg( 'wikilog-history-lc' ),
			array( 'title' => wfMsg( 'wikilog-comment-history' ) ),
			array( 'action' => 'history' ), 'known' );
	}

	/**
	 * Generates and returns a "post new comment" form for the user to fill in
	 * and submit.
	 *
	 * @param $parent If provided, generates a "post reply" form to reply to
	 *   the given comment.
	 */
	public function getPostCommentForm( $parent = NULL ) {
		global $wgUser, $wgTitle, $wgScript, $wgRequest;
		global $wgWikilogModerateAnonymous;

		$comment = $this->mPostedComment;
		$opts = $this->mFormOptions;

		$preview = '';
		if ( $comment && $comment->mParent == $parent ) {
			$check = $this->validateComment( $comment );
			if ( $check ) {
				$preview = Xml::wrapClass( wfMsg( $check ), 'mw-warning', 'div' );
			} else {
				$preview = $this->formatComment( $this->mPostedComment );
			}
			$header = wfMsgHtml( 'wikilog-form-preview' );
			$preview = "<b>{$header}</b>{$preview}<hr/>";
		}

		$form =
			Xml::hidden( 'title', $this->getTitle()->getPrefixedText() ) .
			Xml::hidden( 'action', 'wikilog' ) .
			Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
			( $parent ? Xml::hidden( 'wlParent', $parent ) : '' );

		$fields = array();

		if ( $wgUser->isLoggedIn() ) {
			$fields[] = array(
				wfMsg( 'wikilog-form-name' ),
				$this->mSkin->userLink( $wgUser->getId(), $wgUser->getName() )
			);
		} else {
			$loginTitle = SpecialPage::getTitleFor( 'Userlogin' );
			$loginLink = $this->mSkin->makeKnownLinkObj( $loginTitle,
				wfMsgHtml( 'loginreqlink' ), 'returnto=' . $wgTitle->getPrefixedUrl() );
			$message = wfMsg( 'wikilog-posting-anonymously', $loginLink );
			$fields[] = array(
				Xml::label( wfMsg( 'wikilog-form-name' ), 'wl-name' ),
				Xml::input( 'wlAnonName', 25, $opts->consumeValue( 'wlAnonName' ),
					array( 'id' => 'wl-name', 'maxlength' => 255 ) ) .
					"<p>{$message}</p>"
			);
		}

		$fields[] = array(
			Xml::label( wfMsg( 'wikilog-form-comment' ), 'wl-comment' ),
			Xml::textarea( 'wlComment', $opts->consumeValue( 'wlComment' ),
				40, 5, array( 'id' => 'wl-comment' ) )
		);

		if ( $this->mCaptchaForm ) {
			$fields[] = array( '', $this->mCaptchaForm );
		}

		if ( $wgWikilogModerateAnonymous && $wgUser->isAnon() ) {
			$fields[] = array( '', wfMsg( 'wikilog-anonymous-moderated' ) );
		}

		$fields[] = array( '',
			Xml::submitbutton( wfMsg( 'wikilog-submit' ), array( 'name' => 'wlActionCommentSubmit' ) ) . '&nbsp;' .
			Xml::submitbutton( wfMsg( 'wikilog-preview' ), array( 'name' => 'wlActionCommentPreview' ) )
		);

		$form .= WikilogUtils::buildForm( $fields );

		foreach ( $opts->getUnconsumedValues() as $key => $value ) {
			$form .= Xml::hidden( $key, $value );
		}

		$form = Xml::tags( 'form', array(
			'action' => "{$wgScript}#wl-comment-form",
			'method' => 'post'
		), $form );

		$msgid = ( $parent ? 'wikilog-post-reply' : 'wikilog-post-comment' );
		return Xml::fieldset( wfMsg( $msgid ), $preview . $form,
			array( 'id' => 'wl-comment-form' ) ) . "\n";
	}

	protected function setCommentApproval( $comment, $approval ) {
		global $wgOut, $wgUser;

		# Check if comment is really awaiting moderation.
		if ( $comment->mStatus != WikilogComment::S_PENDING ) {
			$wgOut->showErrorPage( 'nosuchaction', 'nosuchactiontext' );
			return;
		}

		$log = new LogPage( 'wikilog' );
		$title = $comment->getCommentArticleTitle();

		if ( $approval == 'approve' ) {
			$comment->mStatus = WikilogComment::S_OK;
			$comment->saveComment();
			$log->addEntry( 'c-approv', $title, '' );
			$wgOut->redirect( $this->mTalkTitle->getFullUrl() );
		} else if ( $approval == 'reject' ) {
			$reason = wfMsgExt( 'wikilog-log-cmt-rejdel',
				array( 'content', 'parsemag' ),
				$comment->mUserText
			);
			$id = $title->getArticleID( GAID_FOR_UPDATE );
			if ( $this->doDeleteArticle( $reason, false, $id ) ) {
				$comment->deleteComment();
				$log->addEntry( 'c-reject', $title, '' );
				$wgOut->redirect( $this->mTalkTitle->getFullUrl() );
			} else {
				$wgOut->showFatalError( wfMsgExt( 'cannotdelete', array( 'parse' ) ) );
				$wgOut->addHTML( Xml::element( 'h2', null, LogPage::logName( 'delete' ) ) );
				LogEventsList::showLogExtract( $wgOut, 'delete', $this->mTitle->getPrefixedText() );
			}
		} else {
			$wgOut->showErrorPage( 'nosuchaction', 'nosuchactiontext' );
		}
	}

	/**
	 * Validates and saves a new comment. Redirects back to the comments page.
	 * @param $comment Posted comment.
	 */
	protected function postComment( WikilogComment &$comment ) {
		global $wgOut, $wgUser;
		global $wgWikilogModerateAnonymous;

		$check = $this->validateComment( $comment );

		if ( $check !== false ) {
			return $this->view();
		}

		# Check through captcha.
		if ( !WlCaptcha::confirmEdit( $this->getTitle(), $comment->getText() ) ) {
			$this->mCaptchaForm = WlCaptcha::getCaptchaForm();
			$wgOut->setPageTitle( $this->mTitle->getPrefixedText() );
			$wgOut->setRobotPolicy( 'noindex,nofollow' );
			$wgOut->addHtml( $this->getPostCommentForm( $comment->mParent ) );
			return;
		}

		# Limit rate of comments.
		if ( $wgUser->pingLimiter() ) {
			$wgOut->rateLimited();
			return;
		}

		# Set pending state if moderated.
		if ( $comment->mUserID == 0 && $wgWikilogModerateAnonymous ) {
			$comment->mStatus = WikilogComment::S_PENDING;
		}

		if ( !$this->exists() ) {
			# Initialize a blank talk page.
			$user = User::newFromName( wfMsgForContent( 'wikilog-auto' ), false );
			$this->doEdit(
				wfMsgForContent( 'wikilog-newtalk-text' ),
				wfMsgForContent( 'wikilog-newtalk-summary' ),
				EDIT_NEW | EDIT_SUPPRESS_RC, false, $user
			);
		}

		$comment->saveComment();

		$dest = $this->getTitle();
		$dest->setFragment( "#c{$comment->mID}" );
		$wgOut->redirect( $dest->getFullUrl() );
	}

	/**
	 * Returns a new non-validated WikilogComment object with the contents
	 * posted using the post comment form. The result should be validated
	 * using validateComment() before using.
	 */
	protected function getPostedComment() {
		global $wgUser, $wgRequest;

		$parent = $wgRequest->getIntOrNull( 'wlParent' );
		$anonname = trim( $wgRequest->getText( 'wlAnonName' ) );
		$text = trim( $wgRequest->getText( 'wlComment' ) );

		$comment = WikilogComment::newFromText( $this->mItem, $text, $parent );
		$comment->setUser( $wgUser );
		if ( $wgUser->isAnon() ) {
			$comment->setAnon( $anonname );
		}
		return $comment;
	}

	/**
	 * Checks if the given comment is valid for posting.
	 * @param $comment Comment to validate.
	 * @returns False if comment is valid, error message identifier otherwise.
	 */
	protected static function validateComment( WikilogComment &$comment ) {
		global $wgWikilogMaxCommentSize;

		$length = strlen( $comment->mText );

		if ( $length == 0  ) {
			return 'wikilog-comment-is-empty';
		}
		if ( $length > $wgWikilogMaxCommentSize ) {
			return 'wikilog-comment-too-long';
		}

		if ( $comment->mUserID == 0 ) {
			$anonname = User::getCanonicalName( $comment->mAnonName, 'usable' );
			if ( !$anonname ) {
				return 'wikilog-comment-invalid-name';
			}
			$comment->setAnon( $anonname );
		}

		return false;
	}
}
