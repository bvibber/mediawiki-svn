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
		'Welcome',
		'DBConnect',
		'Upgrade',
		'DBSettings',
		'Name',
		'Options',
		'Install',
		'Complete',
	);

	/**
	 * Out of sequence pages, selectable by the user at any time
	 */
	var $otherPages = array(
		'Restart',
		'Readme',
		'ReleaseNotes',
		'Copying',
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

	var $helpId = 0;
	var $tabIndex = 1;

	var $currentPageName;

	/** Constructor */
	function __construct( $request ) {
		parent::__construct();
		$this->output = new WebInstallerOutput( $this );
		$this->request = $request;
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

		# Special case for Creative Commons partner chooser box
		if ( $this->request->getVal( 'SubmitCC' ) ) {
			$page = $this->getPageByName( 'Options' );
			$this->output->useShortHeader();
			$page->submitCC();
			return $this->finish();
		}
		if ( $this->request->getVal( 'ShowCC' ) ) {
			$page = $this->getPageByName( 'Options' );
			$this->output->useShortHeader();
			$this->output->addHTML( $page->getCCDoneBox() );
			return $this->finish();
		}

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
		$this->currentPageName = $page->getName();
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
		$msg = wfMsgReal( $msg, $args, false, false, false );
		$this->output->addHTML( $this->getErrorBox( $msg ) );
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
	 * Get the next tabindex attribute value
	 */
	function nextTabIndex() {
		return $this->tabIndex++;
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
			if ( in_array( $pageName, $this->pageSequence ) ||
				in_array( $pageName, $this->otherPages ) ) {
				if ( in_array( $currentPageName, $this->pageSequence ) ) {
					$query['lastPage'] = $currentPageName;
				}
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
	 * Get HTML for an error box with an icon
	 * @param string $text Wikitext, get this with wfMsgNoTrans()
	 */
	function getErrorBox( $text ) {
		return $this->getInfoBox( $text, 'critical-32.png', 'config-error-box' );
	}

	/**
	 * Get HTML for a warning box with an icon
	 * @param string $text Wikitext, get this with wfMsgNoTrans()
	 */
	function getWarningBox( $text ) {
		return $this->getInfoBox( $text, 'warning-32.png', 'config-warning-box' );
	}

	/**
	 * Get HTML for an info box with an icon
	 * @param string $text Wikitext, get this with wfMsgNoTrans()
	 * @param string $icon Icon name, file in skins/common/images
	 * @param string $class Additional class name to add to the wrapper div
	 */
	function getInfoBox( $text, $icon = 'info-32.png', $class = false ) {
		$s = 
			"<div class=\"config-info $class\">\n" .
				"<div class=\"config-info-left\">\n" .
				Xml::element( 'img', 
					array( 
						'src' => '../skins/common/images/' . $icon
					)
				) . "\n" . 
				"</div>\n" . 
				"<div class=\"config-info-right\">\n" .
					$this->parse( $text ) . "\n" .
				"</div>\n" .
				"<div style=\"clear: left;\"></div>\n" .
			"</div>\n";
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
		$text = wfMsgReal( $msg, $args, false, false, false );
		$html = $this->parse( $text, true );
		$id = $this->helpId++;

		return 
			"<div class=\"config-help-wrapper\">\n" . 
			"<div class=\"config-show-help\" id=\"config-show-help-$id\">\n" .
			Xml::openElement( 'a', array( 'href' => "javascript:showHelp($id,true)" ) ) .
			"<img src=\"../skins/common/images/help-22.png\"/>&nbsp;&nbsp;" .
			wfMsgHtml( 'config-show-help' ) .
			"</a></div>\n" .
			"<div class=\"config-help-message\" id=\"config-help-message-$id\">\n" .
			 $html. 
			"</div>\n" .
			"<div class=\"config-hide-help\" id=\"config-hide-help-$id\">\n" .
			Xml::openElement( 'a', array( 'href' => "javascript:showHelp($id,false)" ) ) .
			"<img src=\"../skins/common/images/help-22.png\"/>&nbsp;&nbsp;" .
			wfMsgHtml( 'config-hide-help' ) .
			"</a></div>\n</div>\n";
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
	 * Label a control by wrapping a config-input div around it and putting a 
	 * label before it
	 */
	function label( $msg, $forId, $contents ) {
		if ( strval( $msg ) == '' ) {
			$labelText = '&nbsp;';
		} else {
			$labelText = wfMsgHtml( $msg );
		}
		return 
			"<div class=\"config-input\">\n" .
			Xml::tags( 'label', 
				array( 'for' => $forId, 'class' => 'config-label' ), 
				$labelText ) . "\n" .
			$contents .
			"</div>\n";
	}

	/**
	 * Get a labelled text box to configure a variable
	 * @param array $params
	 *    Parameters are:
	 *      var:        The variable to be configured (required)
	 *      label:      The message name for the label (required)
	 *      attribs:    Additional attributes for the input element (optional)
	 *      controlName: The name for the input element (optional)
	 *      value:      The current value of the variable (optional)
	 */
	function getTextBox( $params ) {
		if ( !isset( $params['controlName'] ) ) {
			$params['controlName'] = 'config_' . $params['var'];
		}
		if ( !isset( $params['value'] ) ) {
			$params['value'] = $this->getVar( $params['var'] );
		}
		if ( !isset( $params['attribs'] ) ) {
			$params['attribs'] = array();
		}
		return
			$this->label( 
				$params['label'], 
				$params['controlName'],
				Xml::input(
					$params['controlName'],
					30, // intended to be overridden by CSS
					$params['value'],
					$params['attribs'] + array( 
						'id' => $params['controlName'], 
						'class' => 'config-input-text',
						'tabindex' => $this->nextTabIndex()
					)
				)
			);
	}

	/**
	 * Get a labelled password box to configure a variable
	 * Implements password hiding
	 * @param array $params
	 *    Parameters are:
	 *      var:        The variable to be configured (required)
	 *      label:      The message name for the label (required)
	 *      attribs:    Additional attributes for the input element (optional)
	 *      controlName: The name for the input element (optional)
	 *      value:      The current value of the variable (optional)
	 */
	function getPasswordBox( $params ) {
		if ( !isset( $params['value'] ) ) {
			$params['value'] = $this->getVar( $params['var'] );
		}
		if ( !isset( $params['attribs'] ) ) {
			$params['attribs'] = array();
		}
		$params['value'] = $this->getFakePassword( $params['value'] );
		$params['attribs']['type'] = 'password';
		return $this->getTextBox( $params );
	}

	/**
	 * Get a labelled checkbox to configure a boolean variable
	 * @param array $params
	 *    Parameters are:
	 *      var:        The variable to be configured (required)
	 *      label:      The message name for the label (required)
	 *      attribs:    Additional attributes for the input element (optional)
	 *      controlName: The name for the input element (optional)
	 *      value:      The current value of the variable (optional)
	 */
	function getCheckBox( $params ) {
		if ( !isset( $params['controlName'] ) ) {
			$params['controlName'] = 'config_' . $params['var'];
		}
		if ( !isset( $params['value'] ) ) {
			$params['value'] = $this->getVar( $params['var'] );
		}
		if ( !isset( $params['attribs'] ) ) {
			$params['attribs'] = array();
		}
		return 
			"<div class=\"config-input-check\">\n" .
			"<label>\n" .
			Xml::check( 
				$params['controlName'],
				$params['value'],
				$params['attribs'] + array(
					'id' => $params['controlName'],
					'class' => 'config-input-text',
					'tabindex' => $this->nextTabIndex(),
				)
			) .
			$this->parse( wfMsg( $params['label'] ) ) . "\n" .
			"</label>\n" .
			"</div>\n";
	}

	/**
	 * Get a set of labelled radio buttons
	 *
	 * @param array $params
	 *    Parameters are:
	 *      var:            The variable to be configured (required)
	 *      label:          The message name for the label (required)
	 *      itemLabelPrefix: The message name prefix for the item labels (required)
	 *      values:         List of allowed values (required)
	 *      itemAttribs     Array of attribute arrays, outer key is the value name (optional)
	 *      commonAttribs   Attribute array applied to all items
	 *      controlName:    The name for the input element (optional)
	 *      value:          The current value of the variable (optional)
	 */
	function getRadioSet( $params ) {
		if ( !isset( $params['controlName']  ) ) {
			$params['controlName'] = 'config_' . $params['var'];
		}
		if ( !isset( $params['value'] ) ) {
			$params['value'] = $this->getVar( $params['var'] );
		}
		if ( !isset( $params['label'] ) ) {
			$label = '';
		} else {
			$label = $this->parse( wfMsgNoTrans( $params['label'] ) );
		}
		$s = "<label class=\"config-label\">\n" .
			$label .
			"</label>\n" .
			"<ul class=\"config-settings-block\">\n";
		foreach ( $params['values'] as $value ) {
			$itemAttribs = array();
			if ( isset( $params['commonAttribs'] ) ) {
				$itemAttribs = $params['commonAttribs'];
			}
			if ( isset( $params['itemAttribs'][$value] ) ) {
				$itemAttribs = $params['itemAttribs'][$value] + $itemAttribs;
			}
			$checked = $value == $params['value'];
			$id = $params['controlName'] . '_' . $value;
			$itemAttribs['id'] = $id;
			$itemAttribs['tabindex'] = $this->nextTabIndex();
			$s .= 
				'<li>' . 
				Xml::radio( $params['controlName'], $value, $checked, $itemAttribs ) .
				'&nbsp;' .
				Xml::tags( 'label', array( 'for' => $id ), $this->parse( 
					wfMsgNoTrans( $params['itemLabelPrefix'] . strtolower( $value ) ) 
				) ) .
				"</li>\n";
		}
		$s .= "</ul>\n";
		return $s;
	}

	/**
	 * Output an error box using a Status object
	 */
	function showStatusErrorBox( $status ) {
		$text = $status->getWikiText();
		$this->output->addHTML( $this->getErrorBox( $text ) );
	}

	function showStatusError( $status ) {
		$text = $status->getWikiText();
		$this->output->addWikiText( 
			"<div class=\"config-message\">\n" .
			$text .
			"</div>"
		);
	}

	/**
	 * Convenience function to set variables based on form data.
	 * Assumes that variables containing "password" in the name are (potentially
	 * fake) passwords.
	 * @param array $varNames
	 * @param string $prefix The prefix added to variables to obtain form names
	 */
	function setVarsFromRequest( $varNames, $prefix = 'config_' ) {
		$newValues = array();
		foreach ( $varNames as $name ) {
			$value = $this->request->getVal( $prefix . $name );
			$newValues[$name] = $value;
			if ( $value === null ) {
				// Checkbox?
				$this->setVar( $name, false );
			} else {
				if ( stripos( $name, 'password' ) !== false ) {
					$this->setPassword( $name, $value );
				} else {
					$this->setVar( $name, $value );
				}
			}
		}
		return $newValues;
	}

	/**
	 * Get the starting tags of a fieldset
	 * @param string $legend Message name
	 */
	function getFieldsetStart( $legend ) {
		return "\n<fieldset><legend>" . wfMsgHtml( $legend ) . "</legend>\n";
	}

	/**
	 * Get the end tag of a fieldset
	 */
	function getFieldsetEnd() {
		return "</fieldset>\n";
	}

	/**
	 * Helper for Installer::docLink()
	 */
	function getDocUrl( $page ) {
		$url = "{$_SERVER['PHP_SELF']}?page=" . urlencode( $page );
		if ( in_array( $this->currentPageName, $this->pageSequence ) ) {
			$url .= '&lastPage=' . urlencode( $this->currentPageName );
		}
		return $url;
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
				array( 'name' => "enter-$continue", 'style' => 'display:none' ) ) . "\n";
		}
		if ( $id !== 0 ) {
			$s .= Xml::submitButton( wfMsg( 'config-back' ), 
				array( 
					'name' => 'submit-back',
					'tabindex' => $this->parent->nextTabIndex()
				) ) . "\n";
		}
		if ( $continue ) {
			$s .= Xml::submitButton( wfMsg( "config-$continue" ),
				array(
					'name' => "submit-$continue",
					'tabindex' => $this->parent->nextTabIndex(),
				) ) . "\n";
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

	function getVar( $var ) {
		return $this->parent->getVar( $var );
	}

	function setVar( $name, $value ) {
		$this->parent->setVar( $name, $value );
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
			$lifetime = intval( ini_get( 'session.gc_maxlifetime' ) );
			if ( !$lifetime ) {
				$lifetime = 1440; // PHP default
			}
			if ( $this->parent->getSession( 'test' ) === null ) {
				$requestTime = $r->getVal( 'LanguageRequestTime' );
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
					$this->setVar( '_UserLang', $userLang );
				}
				if ( isset( $languages[$contLang] ) ) {
					$this->setVar( 'wgLanguageCode', $contLang );
				}
				return 'continue';
			}
		} elseif ( $this->parent->showSessionWarning ) {
			# The user was knocked back from another page to the start
			# This probably indicates a session expiry
			$this->parent->showError( 'config-session-expired', $wgLang->formatTimePeriod( $lifetime ) );
		}

		$this->parent->setSession( 'test', true );
		
		if ( !isset( $languages[$userLang] ) ) {
			$userLang = $this->getVar( '_UserLang', 'en' );
		}
		if ( !isset( $languages[$contLang] ) ) {
			$contLang = $this->getVar( 'wgLanguageCode', 'en' );
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
		$s = Xml::openElement( 'select', array( 'id' => $name, 'name' => $name ) ) . "\n";
		
		$languages = Language::getLanguageNames();
		ksort( $languages );
		foreach ( $languages as $code => $name ) {
			$s .= "\n" . Xml::option( "$code - $name", $code, $code == $selectedCode );
		}
		$s .= "\n</select>\n";
		return $this->parent->label( $label, $name, $s );
	}

}

class WebInstaller_Welcome extends WebInstallerPage {
	function execute() {
		if ( $this->parent->request->wasPosted() ) {
			if ( $this->getVar( '_Environment' ) ) {
				return 'continue';
			}
		}
		$this->parent->output->addWikiText( wfMsgNoTrans( 'config-welcome' ) );
		$status = $this->parent->doEnvironmentChecks();
		if ( $status ) {
			$this->parent->output->addWikiText( wfMsgNoTrans( 'config-copyright' ) );
			$this->startForm();
			$this->endForm();
		}
	}
}

class WebInstaller_DBConnect extends WebInstallerPage {
	function execute() {
		$r = $this->parent->request;
		if ( $r->wasPosted() ) {
			$status = $this->submit();
			if ( $status->isGood() ) {
				$this->setVar( '_UpgradeDone', false );
				return 'continue';
			} else {
				$this->parent->showStatusErrorBox( $status );
			}
		}


		$this->startForm();

		$types = "<ul class=\"config-settings-block\">\n";
		$settings = '';
		$defaultType = $this->getVar( 'wgDBtype' );
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
			$this->parent->label( 'config-db-type', false, $types ) .
			$settings .
			"<script type=\"text/javascript\">resetDBArea();</script>\n"
		);

		$this->endForm();
	}

	function submit() {
		$r = $this->parent->request;
		$type = $r->getVal( 'DBType' );
		$this->setVar( 'wgDBtype', $type );
		$installer = $this->parent->getDBInstaller( $type );
		if ( !$installer ) {
			return Status::newFatal( 'config-invalid-db-type' );
		}
		return $installer->submitConnectForm();
	}
}

class WebInstaller_Upgrade extends WebInstallerPage {
	function execute() {
		if ( $this->getVar( '_UpgradeDone' ) ) {
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
		$type = $this->getVar( 'wgDBtype' );
		$installer = $this->parent->getDBInstaller( $type );

		if ( !$installer->needsUpgrade() ) {
			return 'skip';
		}

		if ( $this->parent->request->wasPosted() ) {
			if ( true || $installer->doUpgrade() ) {
				$this->setVar( '_UpgradeDone', true );
				$this->showDoneMessage();
				return 'output';
			}
		}

		$this->startForm();
		$this->parent->output->addHTML( $this->parent->getInfoBox( 
			wfMsgNoTrans( 'config-can-upgrade', $GLOBALS['wgVersion'] ) ) );
		$this->endForm();
	}

	function showDoneMessage() {
		$this->startForm();
		$this->parent->output->addHTML( 
			$this->parent->getInfoBox(
				wfMsgNoTrans( 'config-upgrade-done', 
					$GLOBALS['wgServer'] . 
						$this->getVar( 'wgScriptPath' ) . '/index' . 
						$this->getVar( 'wgScriptExtension' )
				), 'tick-32.png' 
			)
		);
		$this->endForm( 'regenerate' );
	}
}

class WebInstaller_DBSettings extends WebInstallerPage {
	function execute() {
		$installer = $this->parent->getDBInstaller( $this->getVar( 'wgDBtype' ) );

		$r = $this->parent->request;
		if ( $r->wasPosted() ) {
			$status = $installer->submitSettingsForm();
			if ( $status === false ) {
				return 'skip';
			} elseif ( $status->isGood() ) {
				return 'continue';
			} else {
				$this->parent->showStatusErrorBox( $status );
			}
		}

		$form = $installer->getSettingsForm();
		if ( $form === false ) {
			return 'skip';
		}

		$this->startForm();
		$this->parent->output->addHTML( $form );
		$this->endForm();
	}

}

class WebInstaller_Name extends WebInstallerPage {
	function execute() {
		$r = $this->parent->request;
		if ( $r->wasPosted() ) {
			if ( $this->submit() ) {
				return 'continue';
			}
		}

		$this->startForm();

		if ( $this->getVar( 'wgSitename' ) == $GLOBALS['wgSitename'] ) {
			$this->setVar( 'wgSitename', '' );
		}
		$js = 'enableControlArray("config__NamespaceType_other", ["config_wgMetaNamespace"])';
		$attribs = array( 'onclick' => $js );

		$this->parent->output->addHTML( 
			$this->parent->getTextBox( array(
				'var' => 'wgSitename',
				'label' => 'config-site-name',
				'attribs' => array(
					'onkeyup' => 'setProjectNamespace();',
					'onchange' => 'setProjectNamespace();'
				)
			) ) .
			$this->parent->getHelpBox( 'config-site-name-help' ) .
			$this->parent->getRadioSet( array(
				'var' => '_NamespaceType',
				'label' => 'config-project-namespace',
				'itemLabelPrefix' => 'config-ns-',
				'values' => array( 'site-name', 'generic', 'other' ),
				'commonAttribs' => $attribs,
			) ) .
			$this->parent->getTextBox( array(
				'var' => 'wgMetaNamespace',
				'label' => '',
			) ) .
			"<script type=\"text/javascript\">\nsetProjectNamespace();\n$js\n</script>\n" .
			$this->parent->getHelpBox( 'config-project-namespace-help' ) .
			$this->parent->getFieldsetStart( 'config-admin-box' ) .
			$this->parent->getTextBox( array(
				'var' => '_AdminName',
				'label' => 'config-admin-name'
			) ) .
			$this->parent->getPasswordBox( array(
				'var' => '_AdminPassword',
				'label' => 'config-admin-password',
			) ) .
			$this->parent->getPasswordBox( array(
				'var' => '_AdminPassword2',
				'label' => 'config-admin-password-confirm'
			) ) .
			$this->parent->getHelpBox( 'config-admin-help' ) .
			$this->parent->getTextBox( array(
				'var' => '_AdminEmail',
				'label' => 'config-admin-email'
			) ) .
			$this->parent->getHelpBox( 'config-admin-email-help' ) .
			$this->parent->getCheckBox( array(
				'var' => '_Subscribe',
				'label' => 'config-subscribe'
			) ) .
			$this->parent->getHelpBox( 'config-subscribe-help' ) .
			$this->parent->getFieldsetEnd() .
			$this->parent->getInfoBox( wfMsg( 'config-almost-done' ) ) .
			$this->parent->getRadioSet( array( 
				'var' => '_SkipOptional',
				'itemLabelPrefix' => 'config-optional-',
				'values' => array( 'continue', 'skip' )
			) )
		);

		$this->endForm();
		return 'output';
	}

	function submit() {
		$retVal = true;
		$this->parent->setVarsFromRequest( array( 'wgSitename', '_NamespaceType', 
			'_AdminName', '_AdminPassword', '_AdminPassword2', '_AdminEmail', 
			'_Subscribe', '_SkipOptional' ) );
		
		// Validate site name
		if ( strval( $this->getVar( 'wgSitename' ) ) === '' ) {
			$this->parent->showError( 'config-site-name-blank' );
			$retVal = false;
		}

		// Fetch namespace
		$nsType = $this->getVar( '_NamespaceType' );
		if ( $nsType == 'site-name' ) {
			$name = $this->getVar( 'wgSitename' );
			// Sanitize for namespace
			// This algorithm should match the JS one in WebInstallerOutput.php
			$name = preg_replace( '/[\[\]\{\}|#<>%+? ]/', '_', $name );
			$name = str_replace( '&', '&amp;', $name );
			$name = preg_replace( '/__+/', '_', $name );
			$name = ucfirst( trim( $name, '_' ) );
		} elseif ( $nsType == 'generic' ) {
			$name = wfMsg( 'config-ns-generic' );
		} else { // other
			$name = $this->getVar( 'wgMetaNamespace' );
		}

		// Validate namespace
		if ( strpos( $name, ':' ) !== false ) {
			$good = false;
		} else {
			// Title-style validation
			$title = Title::newFromText( $name );
			if ( !$title ) {
				$good = false;
			} else {
				$name = $title->getDBkey();
				$good = true;
			}
		}
		if ( !$good ) {
			$this->parent->showError( 'config-ns-invalid', $name );
			$retVal = false;
		}
		$this->setVar( 'wgMetaNamespace', $name );

		// Validate username for creation
		$name = $this->getVar( '_AdminName' );
		if ( strval( $name ) === '' ) {
			$this->parent->showError( 'config-admin-name-blank' );
			$cname = $name;
			$retVal = false;
		} else {
			$cname = User::getCanonicalName( $name, 'creatable' );
			if ( $cname === false ) {
				$this->parent->showError( 'config-admin-name-invalid', $name );
				$retVal = false;
			} else {
				$this->setVar( '_AdminName', $cname );
			}
		}

		// Validate password
		$msg = false;
		$pwd = $this->getVar( '_AdminPassword' );
		if ( strval( $pwd ) === '' ) {
			$msg = 'config-admin-password-blank';
		} elseif ( $pwd === $cname ) {
			$msg = 'config-admin-password-same';
		} elseif ( $pwd !== $this->getVar( '_AdminPassword2' ) ) {
			$msg = 'config-admin-password-mismatch';
		}
		if ( $msg !== false ) {
			$this->parent->showError( $msg );
			$this->setVar( '_AdminPassword', '' );
			$this->setVar( '_AdminPassword2', '' );
			$retVal = false;
		}
		return $retVal;
	}

}
class WebInstaller_Options extends WebInstallerPage {
	function execute() {
		if ( $this->getVar( '_SkipOptional' ) == 'skip' ) {
			return 'skip';
		}
		if ( $this->parent->request->wasPosted() ) {
			if ( $this->submit() ) {
				return 'continue';
			}
		}

		$licenseJs = 'showControlArray("config__LicenseCode_cc-choose", ["config-cc-wrapper"]);';
		$emailJs = 'enableControlArray("config_wgEnableEmail", ["config_wgPasswordSender"]);';
		$uploadJs = 'enableControlArray("config_wgEnableUploads", ["config_wgDeletedDirectory"]);';

		$this->startForm();
		$this->parent->output->addHTML(
			$this->parent->getRadioSet( array(
				'var' => '_RightsProfile',
				'label' => 'config-profile',
				'itemLabelPrefix' => 'config-profile-',
				'values' => array_keys( $this->parent->rightsProfiles ),
			) ) .
			$this->parent->getHelpBox( 'config-profile-help' ) .
			$this->parent->getRadioSet( array(
				'var' => '_LicenseCode',
				'label' => 'config-license',
				'itemLabelPrefix' => 'config-license-',
				'values' => array_keys( $this->parent->licenses ),
				'commonAttribs' => array( 'onclick' => $licenseJs )
			) ) .
			$this->getCCChooser() .
			$this->parent->getHelpBox( 'config-license-help' ) .
			$this->parent->getFieldsetStart( 'config-email-settings' ) .
			$this->parent->getCheckBox( array(
				'var' => 'wgEnableEmail',
				'label' => 'config-enable-email',
				'attribs' => array( 'onclick' => $emailJs ),
			) ) .
			$this->parent->getHelpBox( 'config-enable-email-help' ) .
			$this->parent->getTextBox( array(
				'var' => 'wgPasswordSender',
				'label' => 'config-email-sender'
			) ) .
			$this->parent->getHelpBox( 'config-email-sender-help' ) .
			$this->parent->getFieldsetEnd() .
			$this->parent->getFieldsetStart( 'config-upload-settings' ) .
			$this->parent->getCheckBox( array( 
				'var' => 'wgEnableUploads',
				'label' => 'config-upload-enable',
				'attribs' => array( 'onclick' => $uploadJs ), 
			) ) .
			$this->parent->getHelpBox( 'config-upload-help' ) .
			$this->parent->getTextBox( array( 
				'var' => 'wgDeletedDirectory',
				'label' => 'config-upload-deleted',
			) ) .
			$this->parent->getHelpBox( 'config-upload-deleted-help' ) .
			$this->parent->getTextBox( array(
				'var' => 'wgLogo',
				'label' => 'config-logo'
			) ) .
			$this->parent->getHelpBox( 'config-logo-help' ) .
			$this->parent->getFieldsetEnd() .
			"<script type=\"text/javascript\">$licenseJs $emailJs $uploadJs</script>\n"

		);
		$this->endForm();
	}

	function getCCPartnerUrl() {
		global $wgServer;
		$exitUrl = $wgServer . $this->parent->getUrl( array(
			'page' => 'Options',
			'SubmitCC' => 'indeed',
			'config__LicenseCode' => 'cc',
			'config_wgRightsUrl' => '[license_url]',
			'config_wgRightsText' => '[license_name]',
			'config_wgRightsIcon' => '[license_button]',
		) );
		$styleUrl = $wgServer . dirname( dirname( $this->parent->getUrl() ) ) .
			'/skins/common/config-cc.css';
		$iframeUrl = 'http://creativecommons.org/license/?' . 
			wfArrayToCGI( array(
				'partner' => 'MediaWiki',
				'exit_url' => $exitUrl,
				'lang' => $this->getVar( '_UserLang' ),
				'stylesheet' => $styleUrl,
			) );
		return $iframeUrl;
	}

	function getCCChooser() {
		$iframeAttribs = array( 
			'class' => 'config-cc-iframe',
			'name' => 'config-cc-iframe',
			'id' => 'config-cc-iframe',
			'frameborder' => 0,
			'width' => '100%',
			'height' => '100%',
		);
		if ( $this->getVar( '_CCDone' ) ) {
			$iframeAttribs['src'] = $this->parent->getUrl( array( 'ShowCC' => 'yes' ) );
		} else {
			$iframeAttribs['src'] = $this->getCCPartnerUrl();
		}

		return
			"<div class=\"config-cc-wrapper\" id=\"config-cc-wrapper\">\n" .
			Xml::element( 'iframe', $iframeAttribs, '', false /* not short */ ) .
			"</div>\n";
	}

	function getCCDoneBox() {
		$js = "parent.document.getElementById('config-cc-wrapper').style.height = '$1';";
		// If you change this height, also change it in config.css
		$expandJs = str_replace( '$1', '54em', $js );
		$reduceJs = str_replace( '$1', '70px', $js );
		return 
			'<p>'.
			Xml::element( 'img', array( 'src' => $this->getVar( 'wgRightsIcon' ) ) ) .
			'&nbsp;&nbsp;' . 
			htmlspecialchars( $this->getVar( 'wgRightsText' ) ) .
			"</p>\n" .
			"<p style=\"text-align: center\">" .
			Xml::element( 'a', 
				array( 
					'href' => $this->getCCPartnerUrl(),
					'onclick' => $expandJs,
				), 
				wfMsg( 'config-cc-again' ) 
			) .
			"</p>\n" .
			"<script type=\"text/javascript\">\n" .
			# Reduce the wrapper div height 
			htmlspecialchars( $reduceJs ) .
			"\n" .
			"</script>\n";
	}


	function submitCC() {
		$newValues = $this->parent->setVarsFromRequest( 
			array( 'wgRightsUrl', 'wgRightsText', 'wgRightsIcon' ) );
		if ( count( $newValues ) != 3 ) {
			$this->parent->showError( 'config-cc-error' );
			return;
		}
		$this->setVar( '_CCDone', true );
		$this->parent->output->addHTML( $this->getCCDoneBox() );
	}

	function submit() {
		$this->parent->setVarsFromRequest( array( '_RightsProfile', '_LicenseCode', 
			'wgEnableEmail', 'wgPasswordSender', 'wgEnableUpload', 'wgLogo' ) );

		if ( !in_array( $this->getVar( '_RightsProfile' ), 
			array_keys( $this->parent->rightsProfiles ) ) ) 
		{
			reset( $this->parent->rightsProfiles );
			$this->setVar( '_RightsProfile', key( $this->parent->rightsProfiles ) );
		}

		$code = $this->getVar( '_LicenseCode' );
		if ( $code == 'cc-choose' ) {
			if ( !$this->getVar( '_CCDone' ) ) {
				$this->parent->showError( 'config-cc-not-chosen' );
				return false;
			}
		} elseif ( in_array( $code, array_keys( $this->parent->licenses ) ) ) {
			$entry = $this->parent->licenses[$code];
			if ( isset( $entry['text'] ) ) {
				$this->setVar( 'wgRightsText', $entry['text'] );
			} else {
				$this->setVar( 'wgRightsText', wfMsg( 'config-license-' . $code ) );
			}
			$this->setVar( 'wgRightsUrl', $entry['url'] );
			$this->setVar( 'wgRightsIcon', $entry['icon'] );
		} else {
			$this->setVar( 'wgRightsText', '' );
			$this->setVar( 'wgRightsUrl', '' );
			$this->setVar( 'wgRightsIcon', '' );
		}

		return true;
	}
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
		$s = $this->parent->getWarningBox( wfMsgNoTrans( 'config-help-restart' ) );
		$this->parent->output->addHTML( $s );
		$this->endForm( 'restart' );
	}
}

abstract class WebInstaller_Document extends WebInstallerPage {
	abstract function getFileName();

	function execute() {
		$text = $this->getFileContents();
		$this->parent->output->addWikiText( $text );
		$this->startForm();
		$this->endForm( false );
	}
	
	function getFileContents() {
		return file_get_contents( dirname( __FILE__ ) . '/../../' . $this->getFileName() );
	}
}

class WebInstaller_Readme extends WebInstaller_Document { 
	function getFileName() { return 'README'; } 
}
class WebInstaller_ReleaseNotes extends WebInstaller_Document { 
	function getFileName() { return 'RELEASE-NOTES'; } 
	function getFileContents() {
		$text = parent::getFileContents();
		$text = preg_replace_callback('/\(bug (\d+)\)/', 'self::replaceBugLinks', $text );
		$text = preg_replace_callback('/(\$wg[a-z0-9_]+)/i', 'self::replaceConfigLinks', $text );
		return $text;
	}
	private static function replaceBugLinks( $matches ) {
		return '(<span class="config-buglink">[https://bugzilla.wikimedia.org/show_bug.cgi?id=' .
			$matches[1] . ' bug ' . $matches[1] . '])';
	}
	private static function replaceConfigLinks( $matches ) {
		return '<span class="config-buglink">[http://www.mediawiki.org/wiki/Manual:' .
			$matches[1] . ' ' . $matches[1] . ']';
	}
}
class WebInstaller_Copying extends WebInstaller_Document { 
	function getFileName() { return 'COPYING'; } 
}
