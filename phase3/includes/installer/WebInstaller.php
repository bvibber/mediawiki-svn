<?php

class WebInstaller extends Installer {
	/** WebRequest object */
	var $request;

	/** Cached session array */
	var $session;

	/** Captured PHP error text. Temporary.
	 */
	var $phpErrors;

	/**
	 * The main sequence of page names. These will be displayed in turn.
	 * To add one:
	 *    * Add it here
	 *    * Add a config-page-<name> message
	 *    * Add a WebInstaller_<name> class
	 */
	var $pageSequence = array(
		'Language',
		'Environment',
		'DBConnect',
		'Upgrade',
		'DBSettings',
		'Identity',
		'License',
		'Email',
		'Install',
		'Complete',
	);

	/**
	 * Out of sequence pages, selectable by the user at any time
	 */
	var $otherPages = array(
		'Restart',
	);

	/**
	 * Array of pages which have declared that they have been submitted, have validated
	 * their input, and need no further processing
	 */
	var $happyPages;

	/** 
	 * List of "skipped" pages. These are pages that will automatically continue
	 * to the next page on any GET request. To avoid breaking the "back" button,
	 * they need to be skipped during a back operation.
	 */
	var $skippedPages;

	/**
	 * Flag indicating that session data may have been lost
	 */
	var $showSessionWarning = false;

	/** Constructor */
	function __construct( $request ) {
		parent::__construct();
		$this->output = new WebInstallerOutput( $this );
		$this->request = $request;
		$this->internalDefaults['_UserLang'] = 'en';
	}

	/** 
	 * Main entry point.
	 * @param array $session Initial session array
	 * @return array New session array
	 */
	function execute( $session ) {
		$this->session = $session;
		if ( isset( $session['settings'] ) ) {
			$this->settings = $session['settings'] + $this->settings;
		}
		if ( isset( $session['happyPages'] ) ) {
			$this->happyPages = $session['happyPages'];
		} else {
			$this->happyPages = array();
		}
		if ( isset( $session['skippedPages'] ) ) {
			$this->skippedPages = $session['skippedPages'];
		} else {
			$this->skippedPages = array();
		}
		$lowestUnhappy = $this->getLowestUnhappy();


		# Get the page name
		$pageName = $this->request->getVal( 'page' );

		if ( in_array( $pageName, $this->otherPages ) ) {
			# Out of sequence
			$pageId = false;
			$page = $this->getPageByName( $pageName );
		} else {
			# Main sequence
			if ( !$pageName || !in_array( $pageName, $this->pageSequence ) ) {
				$pageId = $lowestUnhappy;
			} else {
				$pageId = array_search( $pageName, $this->pageSequence );
			}

			# If necessary, move back to the lowest-numbered unhappy page
			if ( $pageId > $lowestUnhappy ) {
				$pageId = $lowestUnhappy;
				if ( $lowestUnhappy == 0 ) {
					# Knocked back to start, possible loss of session data
					$this->showSessionWarning = true;
				}
			}
			$pageName = $this->pageSequence[$pageId];
			$page = $this->getPageByName( $pageName );
		}

		# If a back button was submitted, go back without submitting the form data
		if ( $this->request->wasPosted() && $this->request->getBool( 'submit-back' ) ) {
			if ( $this->request->getVal( 'lastPage' ) ) {
				$nextPage = $this->request->getVal( 'lastPage' );
			} elseif ( $pageId !== false ) {
				# Main sequence page
				# Skip the skipped pages
				$nextPageId = $pageId;
				do {
					$nextPageId--;
					$nextPage = $this->pageSequence[$nextPageId];
				} while( isset( $this->skippedPages[$nextPage] ) );
			} else {
				$nextPage = $this->pageSequence[$lowestUnhappy];
			}
			$this->output->redirect( $this->getUrl( array( 'page' => $nextPage ) ) );
			return $this->finish();
		}

		# Execute the page
		$this->startPageWrapper( $pageName );
		$result = $page->execute();
		$this->endPageWrapper();

		if ( $result == 'skip' ) {
			# Page skipped without explicit submission
			# Skip it when we click "back" so that we don't just go forward again
			$this->skippedPages[$pageName] = true;
			$result = 'continue';
		} else {
			unset( $this->skippedPages[$pageName] );
		}

		# If it was posted, the page can request a continue to the next page
		if ( $result === 'continue' ) {
			if ( $pageId !== false ) {
				$this->happyPages[$pageId] = true;
			}
			$lowestUnhappy = $this->getLowestUnhappy();

			if ( $this->request->getVal( 'lastPage' ) ) {
				$nextPage = $this->request->getVal( 'lastPage' );
			} elseif ( $pageId !== false ) {
				$nextPage = $this->pageSequence[$pageId + 1];
			} else {
				$nextPage = $this->pageSequence[$lowestUnhappy];
			}
			if ( array_search( $nextPage, $this->pageSequence ) > $lowestUnhappy ) {
				$nextPage = $this->pageSequence[$lowestUnhappy];
			}
			$this->output->redirect( $this->getUrl( array( 'page' => $nextPage ) ) );
		}
		return $this->finish();
	}

	function getLowestUnhappy() {
		if ( count( $this->happyPages ) == 0 ) {
			return 0;
		} else {
			return max( array_keys( $this->happyPages ) ) + 1;
		}
	}

	/**
	 * Start the PHP session. This may be called before execute() to start the PHP session.
	 */
	function startSession() {
		if( wfIniGetBool( 'session.auto_start' ) || session_id() ) {
			// Done already
			return true;
		}

		$this->phpErrors = array();
		set_error_handler( array( $this, 'errorHandler' ) );
		session_start();
		restore_error_handler();
		if ( $this->phpErrors ) {
			$this->showError( 'config-session-error', $this->phpErrors[0] );
			return false;
		}
		return true;
	}

	/**
	 * Show an error message in a box. Parameters are like wfMsg().
	 */
	function showError( $msg /*...*/ ) {
		$args = func_get_args();
		array_shift( $args );
		$args = array_map( 'htmlspecialchars', $args );
		$msg = wfMsgReal( $msg, $args );
		$this->output->addHTML( "<div class=\"config-error-top\">\n$msg\n</div>\n" );
	}

	/**
	 * Temporary error handler for session start debugging
	 */
	function errorHandler( $errno, $errstr ) {
		$this->phpErrors[] = $errstr;
	}

	/**
	 * Clean up from execute()
	 * @private.
	 */
	function finish() {
		$this->output->output();
		$this->session['happyPages'] = $this->happyPages;
		$this->session['skippedPages'] = $this->skippedPages;
		$this->session['settings'] = $this->settings;
		return $this->session;
	}

	/**
	 * Get a URL for submission back to the same script
	 */
	function getUrl( $query = array() ) {
		$url = $this->request->getRequestURL();
		# Remove existing query
		$url = preg_replace( '/\?.*$/', '', $url );
		if ( $query ) {
			$url .= '?' . wfArrayToCGI( $query );
		}
		return $url;
	}

	/**
	 * Get a WebInstallerPage from the main sequence, by ID
	 */
	function getPageById( $id ) {
		$pageName = $this->pageSequence[$id];
		$pageClass = 'WebInstaller_' . $pageName;
		return new $pageClass( $this );
	}

	/**
	 * Get a WebInstallerPage by name
	 */
	function getPageByName( $pageName ) {
		$pageClass = 'WebInstaller_' . $pageName;
		return new $pageClass( $this );
	}

	/**
	 * Get a session variable
	 */
	function getSession( $name, $default = null ) {
		if ( !isset( $this->session[$name] ) ) {
			return $default;
		} else {
			return $this->session[$name];
		}
	}

	/**
	 * Set a session variable
	 */
	function setSession( $name, $value ) {
		$this->session[$name] = $value;
	}

	/**
	 * Called by execute() before page output starts, to show a page list
	 */
	function startPageWrapper( $currentPageName ) {
		$s = "<div class=\"config-page-wrapper\">\n" .
			"<div class=\"config-page-list\"><ul>\n";
		$lastHappy = -1;
		foreach ( $this->pageSequence as $id => $pageName ) {
			$happy = !empty( $this->happyPages[$id] );
			$s .= $this->getPageListItem( $pageName, 
				$happy || $lastHappy == $id - 1, $currentPageName );
			if ( $happy ) {
				$lastHappy = $id;
			}
		}
		$s .= "</ul><br/><ul>\n";
		foreach ( $this->otherPages as $pageName ) {
			$s .= $this->getPageListItem( $pageName, true, $currentPageName );
		}
		$s .= "</ul></div>\n". // end list pane
			"<div class=\"config-page\">\n" . 
			Xml::element( 'h2', array(), 
				wfMsg( 'config-page-' . strtolower( $currentPageName ) ) );

		$this->output->addHTMLNoFlush( $s );
	}

	/**
	 * Get a list item for the page list
	 */
	function getPageListItem( $pageName, $enabled, $currentPageName ) {
		$s = "<li class=\"config-page-list-item\">";
		$name = wfMsg( 'config-page-' . strtolower( $pageName ) );
		if ( $enabled ) {
			$query = array( 'page' => $pageName );
			if ( !in_array( $pageName, $this->pageSequence ) ) {
				$query['lastPage'] = $currentPageName;
				$link = Xml::element( 'a', 
					array( 
						'href' => $this->getUrl( $query )
					),
					$name
				);
			} else {
				$link = htmlspecialchars( $name );
			}
			if ( $pageName == $currentPageName ) {
				$s .= "<span class=\"config-page-current\">$link</span>";
			} else {
				$s .= $link;
			}
		} else {
			$s .= Xml::element( 'span', 
				array( 
					'class' => 'config-page-disabled' 
				), 
				$name 
			);
		}
		$s .= "</li>\n";
		return $s;
	}

	/**
	 * Output some stuff after a page is finished
	 */
	function endPageWrapper() {
		$this->output->addHTMLNoFlush( 
			"</div>\n" . 
			"<br clear=\"left\"/>\n" .
			"</div>" );
	}

	/**
	 * Get HTML for a warning box with an icon
	 */
	function getWarningBox( $msg ) {
		return $this->getInfoBox( $msg, 'warning-32.png' );
	}

	/**
	 * Get HTML for an info box with an icon
	 */
	function getInfoBox( $msg, $icon = 'info-32.png' ) {
		if ( is_array( $msg ) ) {
			$args = $msg;
			$msg = array_shift( $args );
			$text = wfMsgReal( $msg, $args, false, false, false );
		} else {
			$text = wfMsgNoTrans( $msg );
		}
		$s = "<div class=\"config-info\">\n" .
			"<div class=\"config-info-left\">\n" .
			Xml::element( 'img', 
				array( 
					'src' => '../skins/common/images/' . $icon
				)
			) . "\n" . 
			"</div>\n" . 
			"<div class=\"config-info-right\">\n" .
			$this->parse( $text ) .
			"</div></div>\n";
		return $s;
	}

	/**
	 * Get small text indented help for a preceding form field.
	 * Parameters like wfMsg().
	 */
	function getHelpBox( $msg /*, ... */ ) {
		$args = func_get_args();
		array_shift( $args );
		$args = array_map( 'htmlspecialchars', $args );
		return "<div class=\"config-desc\">\n" . 
			$this->parse( wfMsgReal( $msg, $args, false, false, false ) ) . 
			"</div>\n";
	}

	/**
	 * Output a help box
	 */
	function showHelpBox( $msg /*, ... */ ) {
		$args = func_get_args();
		$html = call_user_func_array( array( $this, 'getHelpBox' ), $args );
		$this->output->addHTML( $html );
	}

	/**
	 * Show a short informational message
	 * Output looks like a list.
	 */
	function showMessage( $msg /*, ... */ ) {
		$args = func_get_args();
		array_shift( $args );
		$html = '<div class="config-message">' . 
			$this->parse( wfMsgReal( $msg, $args, false, false, false ) ) .
			"</div>\n";
		$this->output->addHTML( $html );
	}

	/**
	 * Get a label element using a message name
	 */
	function getLabel( $msg, $for ) {
		return Xml::element( 'label', 
			array( 'for' => $for, 'class' => 'config-label' ), 
			wfMsg( $msg ) ) . "\n";
	}

	/**
	 * Get a text box
	 */
	function getTextBox( $name, $value = '', $type = 'text' ) {
		return Xml::element( 'input', 
			array(
				'type' => $type,
				'name' => $name,
				'id' => $name,
				'value' => $value,
				'class' => 'config-input-text',
			)
		);
	}

	/**
	 * Get a checkbox
	 */
	function getCheckBox( $name, $value, $attribs ) {
		return Xml::element( 'input',
			$attribs + array(
				'type' => 'checkbox',
				'name' => $name,
				'id' => $name,
				'checked' => $value ? '1'  : '',
				'class' => 'config-input-text',
			)
		);
	}

	/**
	 * Output an error box using a Status object
	 */
	function showStatusErrorBox( $status ) {
		$text = $status->getWikiText();
		$this->parent->output->addWikiText( 
			"<div class=\"config-error-top\">\n" .
			$text .
			"</div>"
		);
	}

	function showStatusError( $status ) {
		$text = $status->getWikiText();
		$this->parent->output->addWikiText( 
			"<div class=\"config-message\">\n" .
			$text .
			"</div>"
		);
	}
}

class WebInstallerPage {
	function __construct( $parent ) {
		$this->parent = $parent;
	}

	function startForm() {
		$this->parent->output->addHTML( 
			"<div class=\"config-section\">\n" .
			Xml::openElement( 
				'form', 
				array( 
					'method' => 'post', 
					'action' => $this->parent->getUrl( array( 'page' => $this->getName() ) ) 
				)
			) . "\n"
		);
	}

	function endForm( $continue = 'continue' ) {
		$s = "<div class=\"config-submit\">\n";
		$id = $this->getId();
		if ( $id === false ) {
			$s .= Xml::hidden( 'lastPage', $this->parent->request->getVal( 'lastPage' ) );
		}
		if ( $continue ) {
			// Fake submit button for enter keypress
			$s .= Xml::submitButton( wfMsg( "config-$continue" ), 
				array( 'name' => "enter-$continue", 'style' => 'display:none' ) );
		}
		if ( $id !== 0 ) {
			$s .= Xml::submitButton( wfMsg( 'config-back' ), array( 'name' => 'submit-back' ) );
		}
		if ( $continue ) {
			$s .= Xml::submitButton( wfMsg( "config-$continue" ),
				array( 'name' => "submit-$continue" ) );
		}
		$s .= "</div></form></div>\n";
		$this->parent->output->addHTML( $s );
	}

	function getName() {
		return str_replace( 'WebInstaller_', '', get_class( $this ) );
	}

	function getId() {
		return array_search( $this->getName(), $this->parent->pageSequence );
	}

	function execute() {
		if ( $this->parent->request->wasPosted() ) {
			return 'continue';
		} else {
			$this->startForm();
			$this->parent->output->addHTML( 'Mockup' );
			$this->endForm();
		}
	}
}

class WebInstaller_Language extends WebInstallerPage {
	function execute() {
		global $wgLang;
		$r = $this->parent->request;
		$userLang = $r->getVal( 'UserLang' );
		$contLang = $r->getVal( 'ContLang' );

		if ( $r->wasPosted() ) {
			# Do session test
			if ( $this->parent->getSession( 'test' ) === null ) {
				$requestTime = $r->getVal( 'LanguageRequestTime' );
				$lifetime = intval( ini_get( 'session.gc_maxlifetime' ) );
				if ( !$lifetime ) {
					$lifetime = 1440;
				}
				if ( !$requestTime ) {
					// The most likely explanation is that the user was knocked back 
					// from another page on POST due to session expiry
					$msg = 'config-session-expired';
				} elseif ( time() - $requestTime > $lifetime ) {
					$msg = 'config-session-expired';
				} else {
					$msg = 'config-no-session';
				}
				$this->parent->showError( $msg, $wgLang->formatTimePeriod( $lifetime ) );
			} else {
				$languages = Language::getLanguageNames();
				if ( isset( $languages[$userLang] ) ) {
					$this->parent->setVar( '_UserLang', $userLang );
				}
				if ( isset( $languages[$contLang] ) ) {
					$this->parent->setVar( 'wgLanguageCode', $contLang );
				}
				return 'continue';
			}
		} elseif ( $this->parent->showSessionWarning ) {
			# The user was knocked back from another page to the start
			# This probably indicates a session expiry
			$this->parent->showError( 'config-session-expired' );
		}

		$this->parent->setSession( 'test', true );
		
		if ( !isset( $languages[$userLang] ) ) {
			$userLang = $this->parent->getVar( '_UserLang', 'en' );
		}
		if ( !isset( $languages[$contLang] ) ) {
			$contLang = $this->parent->getVar( 'wgLanguageCode', 'en' );
		}
		$this->startForm();
		$s = 
			Xml::hidden( 'LanguageRequestTime', time() ) .
			$this->getLanguageSelector( 'UserLang', 'config-your-language', $userLang ) .
			$this->parent->getHelpBox( 'config-your-language-help' ) .
			$this->getLanguageSelector( 'ContLang', 'config-wiki-language', $contLang ) .
			$this->parent->getHelpBox( 'config-wiki-language-help' );

		
		$this->parent->output->addHTML( $s );
		$this->endForm();
	}

	/**
	 * Get a <select> for selecting languages
	 */
	function getLanguageSelector( $name, $label, $selectedCode ) {
		$s = "<div class=\"config-input\">\n" .
			$this->parent->getLabel( $label, $name ) .
			Xml::openElement( 'select', array( 'id' => $name, 'name' => $name ) ) . "\n";
		
		$languages = Language::getLanguageNames();
		ksort( $languages );
		foreach ( $languages as $code => $name ) {
			$s .= "\n" . Xml::option( "$code - $name", $code, $code == $selectedCode );
		}
		$s .= "\n</select>\n</div>\n";
		return $s;
	}

}

class WebInstaller_Environment extends WebInstallerPage {
	function execute() {
		if ( $this->parent->request->wasPosted() ) {
			if ( $this->parent->getVar( '_Environment' ) ) {
				return 'continue';
			}
		}
		$status = $this->parent->doEnvironmentChecks();
		if ( $status ) {
			$this->startForm();
			$this->endForm();
		}
	}
}

class WebInstaller_DBConnect extends WebInstallerPage {
	function execute() {
		$r = $this->parent->request;
		if ( $this->parent->request->wasPosted() ) {
			$status = $this->submit();
			if ( $status->isGood() ) {
				$this->parent->setVar( '_UpgradeDone', false );
				return 'continue';
			} else {
				$error = $status->getWikiText();
				$this->parent->output->addWikiText( 
					"<div class=\"config-error-top\">\n" .
					$error .
					"</div>"
				);
			}
		}


		$this->startForm();

		$types = "<label class=\"config-label\">" .
			wfMsg( 'config-db-type' ) .
			"</label>" .
			"<ul class=\"config-settings-block\">\n";
		$settings = '';
		$defaultType = $this->parent->getVar( 'wgDBtype' );
		foreach ( $this->parent->getDBTypes() as $type ) {
			$installer = $this->parent->getDBInstaller( $type );
			$encType = Xml::encodeJsVar( $type );
			$types .= 
				'<li>' .
				Xml::radioLabel(
					$installer->getReadableName(),
					'DBType',
					$type,
					'DBType_' . $type,
					$type == $defaultType,
					array( 'onclick' => "showDBArea($encType);" )
				) .
				"</li>\n";

			$settings .= 
				Xml::openElement( 'div', array( 'id' => 'DB_wrapper_' . $type ) ) .
				Xml::element( 'h3', array(), wfMsg( 'config-header-' . $type ) ) .
				$installer->getConnectForm() .
				"</div>\n";
		}
		$types .= "</ul><br clear=\"left\"/>\n";
		$encType = Xml::encodeJsVar( $defaultType );

		$this->parent->output->addHTML( 
			$types .
			$settings .
			"<script>resetDBArea();</script>\n"
		);

		$this->endForm();
	}

	function submit() {
		$r = $this->parent->request;
		$type = $r->getVal( 'DBType' );
		$this->parent->setVar( 'wgDBtype', $type );
		$installer = $this->parent->getDBInstaller( $type );
		if ( !$installer ) {
			return Status::newFatal( 'config-invalid-db-type' );
		}
		return $installer->submitConnectForm();
	}
}

class WebInstaller_Upgrade extends WebInstallerPage {
	function execute() {
		if ( $this->parent->getVar( '_UpgradeDone' ) ) {
			if ( $this->parent->request->wasPosted() ) {
				// Done message acknowledged
				return 'continue';
			} else {
				// Back button click
				// Show the done message again
				// Make them click back again if they want to do the upgrade again
				$this->showDoneMessage();
				return 'output';
			}
		}

		// wgDBtype is generally valid here because otherwise the previous page 
		// (connect) wouldn't have declared its happiness
		$type = $this->parent->getVar( 'wgDBtype' );
		$installer = $this->parent->getDBInstaller( $type );

		// There's no guarantee the connection will still succeed though
		$conn = $installer->getConnection();
		if ( $conn instanceof Status ) {
			$this->startForm();
			$this->showStatusErrorBox( $conn );
			$this->endForm();
			return 'output';
		}

		$ok = $conn->selectDB( $this->parent->getVar( 'wgDBname' ) );
		if ( !$ok ) {
			// No DB exists yet
			return 'skip';
		}
		if ( !$conn->tableExists( 'cur' ) && !$conn->tableExists( 'revision' ) ) {
			// Nothing to upgrade
			return 'skip';
		}

		if ( $this->parent->request->wasPosted() ) {
			if ( true || $installer->doUpgrade() ) {
				$this->parent->setVar( '_UpgradeDone', true );
				$this->showDoneMessage();
				return 'output';
			}
		}

		$this->startForm();
		$this->parent->output->addHTML( $this->parent->getInfoBox( 
			array( 'config-can-upgrade', $GLOBALS['wgVersion'] ) ) );
		$this->endForm();
	}

	function showDoneMessage() {
		$this->startForm();
		$this->parent->output->addHTML( 
			$this->parent->getInfoBox(
				array( 
					'config-upgrade-done', 
					$GLOBALS['wgServer'] . 
					$this->parent->getVar( 'wgScriptPath' ) . '/index' . 
						$this->parent->getVar( 'wgScriptExtension' )
				), 'tick-32.png' 
			)
		);
		$this->endForm( 'regenerate' );
	}
}

class WebInstaller_DBSettings extends WebInstallerPage {
	function execute() {
		$installer = $this->parent->getDBInstaller( $this->parent->getVar( 'wgDBtype' ) );
		$form = $installer->getSettingsForm();
		if ( $form === false ) {
			return 'skip';
		}
		$this->startForm();
		$this->parent->output->addHTML( $form );
		$this->endForm();
	}

}

class WebInstaller_Identity extends WebInstallerPage {
}
class WebInstaller_License extends WebInstallerPage {
}
class WebInstaller_Email extends WebInstallerPage {
}
class WebInstaller_Install extends WebInstallerPage {
}
class WebInstaller_Complete extends WebInstallerPage {
}
class WebInstaller_Restart extends WebInstallerPage {
	function execute() {
		$r = $this->parent->request;
		if ( $r->wasPosted() ) {
			$really = $r->getVal( 'submit-restart' );
			if ( $really ) {
				$this->parent->session = array();
				$this->parent->happyPages = array();
				$this->parent->settings = array();
			}
			return 'continue';
		}

		$this->startForm();
		$s = $this->parent->getWarningBox( 'config-help-restart' );
		$this->parent->output->addHTML( $s );
		$this->endForm( 'restart' );
	}
}

