<?php
/**
 * InlineEditor base class, contains all the basic logic of the editor.
 * It handles hooks through static functions, and they can spawn an InlineEditor object using
 * an article object, and then render like a normal page, or as JSON. Reason for this is to be
 * able to pass this object to different hook functions.
 */
class InlineEditor {
	private static $fallbackReason; /// < reason for not using the editor, used for showing a message
	const REASON_BROWSER  = 1;      /// < reason is an incompatible browser
	const REASON_ADVANCED = 2;      /// < reason is editing an 'advanced' page, whatever that may be

	private $article;               /// < Article object to edit
	private $editModes;             /// < array of different edit modes, see addEditMode()
	private $extendedEditPage;      /// < ExtendedEditPage object we're using to handle editor logic

	/**
	 * Main entry point, hooks into MediaWikiPerformAction.
	 * Checks whether or not to spawn the editor, and does so if nessicary.
	 */
	public static function mediaWikiPerformAction( $output, $article, $title, $user, $request, $wiki ) {
		global $wgHooks;

		// the action of the page, i.e. 'view' or 'edit'
		$action = $wiki->getVal( 'Action' );

		// check if the editor could be used on this page, and if so, hide the [edit] links
		if ( self::isValidBrowser() && !self::isAdvancedPage( $article, $title ) ) {
			self::hideEditSection( $output );
		}

		// return if the action is not 'edit' or if it's disabled
		if ( $action != 'edit' || in_array( $action, $wiki->getVal( 'DisabledActions', array() ) ) )
		{
			return true;
		}

		// check if the 'fulleditor' parameter is set either in GET or POST
		if ( $request->getCheck( 'fulleditor' ) ) {
			// hook into the edit page to inject the hidden 'fulleditor' input field again
			$wgHooks['EditPage::showEditForm:fields'][] = 'InlineEditor::showEditFormFields';
			return true;
		}

		// terminate if the browser is not supported
		if ( !self::isValidBrowser() ) {
			self::$fallbackReason = self::REASON_BROWSER;
			return true;
		}

		// terminate if we consider this page 'advanced'
		if ( self::isAdvancedPage( $article, $title ) ) {
			self::$fallbackReason = self::REASON_ADVANCED;
			return true;
		}

		// start the session if needed
		if ( session_id() == '' ) {
			wfSetupSession();
		}

		// try to spawn the editor and render the page
		$editor = new InlineEditor( $article );
		if ( $editor->render( $output ) ) {
			return false;
		}
		else {
			// if rendering fails for some reason, terminate and show the advanced page notice
			self::$fallbackReason = self::REASON_ADVANCED;
			return true;
		}
	}

	/**
	 * Hooks into EditPage::showEditForm:initial. Shows a message if there is a fallback reason set.
	 * @param $editPage EditPage
	 */
	public static function showEditForm( &$editPage ) {
		global $wgExtensionAssetsPath, $wgOut, $wgRequest;

		// check for a fallback reason
		if ( isset( self::$fallbackReason ) ) {
			// add the style for fallback message
			$wgOut->addExtensionStyle( $wgExtensionAssetsPath . "/InlineEditor/EditForm.css?0" );

			// show the appropriate message at the top of the page
			switch( self::$fallbackReason ) {
				case self::REASON_BROWSER:
					self::prependFallbackMessage( wfMsgExt( 'inline-editor-redirect-browser', 'parseinline' ) );
					break;
				case self::REASON_ADVANCED:
					self::prependFallbackMessage( wfMsgExt( 'inline-editor-redirect-advanced', 'parseinline' ) );
					break;
			}
		}

		return true;
	}

	/**
	 * Prepends a fallback message at the top of the page.
	 * @param $html String with correct HTML
	 */
	private static function prependFallbackMessage( $html ) {
		global $wgOut;
		$wgOut->prependHTML( '<div class="inlineEditorMessage">' . $html . '</div>' );
	}

	/**
	 * Checks if the browser is supported.
	 * This function is borrowed from EditPage::checkUnicodeCompliantBrowser().
	 */
	private static function isValidBrowser() {
		global $wgInlineEditorBrowserBlacklist;
		if ( empty( $_SERVER["HTTP_USER_AGENT"] ) ) {
			// No User-Agent header sent? Trust it by default...
			return true;
		}
		$currentbrowser = $_SERVER["HTTP_USER_AGENT"];
		foreach ( $wgInlineEditorBrowserBlacklist as $browser ) {
			if ( preg_match( $browser, $currentbrowser ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Check if the page is 'advanced'. For now, that means it has to be in an allowed namespace.
	 * @param $article Article
	 * @param $title Title
	 * @return boolean
	 */
	private static function isAdvancedPage( &$article, &$title ) {
		global $wgInlineEditorAllowedNamespaces;
		if ( !empty( $wgInlineEditorAllowedNamespaces )
			&& !in_array( $title->getNamespace(), $wgInlineEditorAllowedNamespaces ) ) {
				return true;
		}
		return false;
	}

	/**
	 * Entry point for the 'Preview' function through Ajax.
	 * No real point in securing this, as nothing is actually saved.
	 * @param $json String JSON object from the client
	 * @param $pageName String The page we're editing
	 * @return String HTML
	 */
	public static function ajaxPreview( $json, $pageName ) {
		$title   = Title::newFromText( $pageName );
		$article = Article::newFromId( $title->getArticleId() );

		$editor = new InlineEditor( $article );
		return $editor->preview( $json );
	}

	/**
	 * Hide the [edit] links on the page by enabling a piece of CSS (instead of screwing with the parser cache).
	 * @param $output OutputPage
	 */
	public static function hideEditSection( &$output ) {
		global $wgExtensionAssetsPath;
		$output->addExtensionStyle( $wgExtensionAssetsPath . "/InlineEditor/HideEditSection.css?0" );
	}

	/**
	 * Add a 'fulleditor' hidden input field to the normal edit page
	 * @param $editpage EditPage
	 * @param $output OutputPage
	 */
	public static function showEditFormFields( &$editpage, &$output ) {
		$output->addHTML(
			HTML::rawElement( 'input', array( 'name' => 'fulleditor', 'type' => 'hidden', 'value' => '1' ) )
		);
		return true;
	}

	/**
	 * Constructor which takes only an Article object
	 * @param $article Article
	 */
	public function __construct( $article ) {
		$this->article = $article;
		$editmodes = array();
	}

	/**
	 * Render the editor.
	 * Spawns an ExtendedEditPage which is an EditPage with some specific logic for this editor.
	 * This is supplied with wikitext generated using InlineEditorText, from the posted JSON.
	 * If the page is being saved, the ExtendedEditPage will terminate the script itself, else
	 * the editing interface will show as usual.
	 * @param $output OutputPage
	 */
	public function render( &$output ) {
		global $wgParser, $wgHooks, $wgRequest, $wgExtensionAssetsPath;

		// create an InlineEditorText object which generates the HTML and JSON for the editor
		$text = new InlineEditorText( $this->article );

		// if the page is being saved, retrieve the wikitext from the JSON
		if ( $wgRequest->wasPosted() ) {
			$text->loadFromJson( $wgRequest->getVal( 'json' ) );
			$wgRequest->setVal( 'wpTextbox1', $text->getWikiOriginal() );
		}

		// try to init, or else return false, which will spawn an 'advanced page' notice
		$this->extendedEditPage = new ExtendedEditPage( $this->article );
		if ( $this->extendedEditPage->initInlineEditor() ) {
			// IMPORTANT: if the page was being saved, the script has been terminated by now!!

			// include the required JS and CSS files
			$output->includeJQuery();
			$output->addScriptFile( $wgExtensionAssetsPath . "/InlineEditor/jquery.inlineEditor.js?0" );
			$output->addScriptFile( $wgExtensionAssetsPath . "/InlineEditor/jquery.inlineEditor.basicEditor.js?0" );
			$output->addScriptFile( $wgExtensionAssetsPath . "/InlineEditor/jquery-ui-effects-1.8.4.min.js?0" );
			$output->addExtensionStyle( $wgExtensionAssetsPath . "/InlineEditor/InlineEditor.css?0" );

			// have the different kind of editors register themselves
			wfRunHooks( 'InlineEditorDefineEditors', array( &$this, &$output ) );

			// load the wikitext into the InlineEditorText object
			$text->loadFromWikiText( $this->extendedEditPage->getWikiText() );

			// add a large <div> around the marked wikitext to denote the editing position
			$parserOutput = $text->getParserOutputMarked();
			$parserOutput->setText( '<div id="editContent">' . $parserOutput->getText() . '</div>' );

			// put the marked output into the page
			$output->addParserOutput( $parserOutput );

			// add the different edit modes and initial JSON state in Javascript, and finally init the editor
			$output->addInlineScript(
				'jQuery( document ).ready( function() {
					jQuery.inlineEditor.editModes = ["' . implode( '","', array_keys( $this->editModes ) ) . '"];
					jQuery.inlineEditor.currentMode = "' . reset( $this->editModes ) . '";
					jQuery.inlineEditor.addInitialState( ' . $text->getWikiJson() . ' );
					jQuery.inlineEditor.init();
				} );'
			);

			// hook into SiteNoticeBefore to display the two boxes above the title
			// @todo: fix this in core, make sure that anything can be inserted above the title, outside #siteNotice
			$wgHooks['SiteNoticeBefore'][]  = array( $this, 'siteNoticeBefore' );
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Get the Article being edited
	 * @return Article
	 */
	public function getArticle() {
		return $this->article;
	}

	/**
	 * Pass JSON into an InlineEditorText object and return combined JSON (HTML + sentence representation)
	 * @param $json String
	 * @return String
	 */
	public function preview ( $json ) {
		$text = new InlineEditorText( $this->article );
		$text->loadFromJson( $json );
		return $text->getCombinedJson();
	}

	/**
	 * Hooks into SiteNoticeBefore. Renders the edit interface above the title of the page.
	 * @param $siteNotice String
	 */
	public function siteNoticeBefore( &$siteNotice ) {
		$siteNotice = $this->renderEditBox() . $this->renderEditModes();
		return false;
	}

	/**
	 * Add an edit mode to the list.
	 * @param $name String Name to be used in id-fields
	 * @param $caption String Name to be displayed
	 * @param $description String Description to be displayed when the mode is selected (*escaped* HTML only!)
	 */
	public function addEditMode( $name, $caption, $description ) {
		$this->editModes[$name] = array(
			'radioid'       => 'radio-' . $name,
			'descriptionid' => 'description-' . $name,
			'caption'       => $caption,
			'description'   => $description
		);
	}

	/**
	 * Generates "Edit box" (the first one)
	 * This looks like this:
	 * <div class="editbox">
	 *   inline-editor-editbox-top
	 *   <hr/>
	 *
	 *   inline-editor-editbox-changes-question
	 *   <input class="summary" name="summary" />
	 *   <div class="example">inline-editor-editbox-changes-example</div>
	 *   <hr/>
	 *
	 *   <div class="side">
	 *     inline-editor-editbox-publish-notice
	 *     <div class="terms">inline-editor-editbox-publish-terms</div>
	 *   </div>
	 *   <a id="publish">inline-editor-editbox-publish-caption</a></div>
	 * @return string HTML
	 */
	private function renderEditBox() {
		$top  = wfMsgExt( 'inline-editor-editbox-top', 'parseinline' );
		$top .= '<hr/>';

		$summary  = wfMsgExt( 'inline-editor-editbox-changes-question', 'parseinline' );
		$summary .= Html::input( 'wpSummary', $this->extendedEditPage->getSummary(),
			'text', array( 'class' => 'summary', 'maxlength' => 250 ) );
		$summary .= Html::rawElement( 'div', array( 'class' => 'example' ),
			wfMsgExt( 'inline-editor-editbox-changes-example', 'parseinline' ) );
		$summary .= '<hr/>';

		$terms    = Html::rawElement( 'div', array( 'class' => 'terms' ),
			// @todo FIXME: Create a link to content language copyrightpage with plain content
			//              link description.
			wfMsgExt( 'inline-editor-editbox-publish-terms', 'parseinline', '[[' . wfMsgForContent( 'copyrightpage' ) . ']]' ) );
		$publish  = Html::rawElement( 'div', array( 'class' => 'side' ),
			wfMsgExt( 'inline-editor-editbox-publish-notice', 'parseinline' ) . $terms );
		$publish .= Html::rawElement( 'a', array( 'id' => 'publish', 'href' => '#' ),
			wfMsgExt( 'inline-editor-editbox-publish-caption', 'parseinline' ) );
		$publish .= HTML::rawElement( 'input', array( 'id' => 'json', 'name' => 'json', 'type' => 'hidden' ) );

		$form = Html::rawElement( 'form', array(
			'id' => 'editForm',
			'method' => 'POST',
			'action' => $this->extendedEditPage->getSubmitUrl() ), $top . $summary . $publish );


		return Html::rawElement( 'div', array( 'class' => 'editbox' ), $form );
	}

	/**
	 * Generates "Edit mode" box (the second one)
	 * This looks like this:
	 * <div class="editmode">
	 *   <div class="header">
	 *     <div class="radio title">Edit mode: <!-- inline-editor-editmodes-caption --></div>
	 *     <div class="radio"><!-- radio button + label --></div>
	 *     <div class="radio"><!-- radio button + label --></div>
	 *     <!-- for every edit mode one button -->
	 *     <!-- and finally undo and redo buttons (with class 'button') -->
	 *   </div>
	 *   <div class="descriptionOuter">
	 *     <div class="descriptionInner" id="description-text">
	 *       <!-- for every edit mode a description -->
	 *     </div>
	 *   </div>
	 * @return string HTML
	 */
	private function renderEditModes() {
		if ( !isset( $this->editModes ) ) return '';

		$header = Html::rawElement( 'div', array( 'class' => 'radio title' ),
			wfMsgExt( 'inline-editor-editmodes-caption', 'parseinline' ) );

		$descriptions = '';
		$first = true;
		foreach ( $this->editModes as $editmode ) {
			$inputOptions = array( 'id' => $editmode['radioid'], 'class' => 'optionMode' );
			if ( $first ) {
				$inputOptions['checked'] = 'checked';
				$first = false;
			}
			$input = Html::input( 'optionMode', '', 'radio', $inputOptions );
			$label = Html::rawElement( 'label', array( 'for' => $editmode['radioid'] ), $editmode['caption'] );

			$header .= Html::rawElement( 'div', array( 'class' => 'radio' ), $input . $label );

			$descriptions .= Html::rawElement( 'div', array( 'class' => 'descriptionInner', 'id' => $editmode['descriptionid'] ),
				$editmode['description'] );
		}

		$header .= Html::rawElement( 'div', array( 'class' => 'button' ),
			Html::rawElement( 'a', array( 'id' => 'redo', 'href' => '#' ),
				wfMsgExt( 'inline-editor-editmodes-redo', 'parseinline' ) )
		);
		$header .= Html::rawElement( 'div', array( 'class' => 'button' ),
			Html::rawElement( 'a', array( 'id' => 'undo', 'href' => '#' ),
				wfMsgExt( 'inline-editor-editmodes-undo', 'parseinline' ) )
		);
		$header .= Html::rawElement( 'div', array( 'class' => 'button' ),
			Html::rawElement( 'div', array( 'id' => 'editCounter', 'href' => '#' ), '#0' )
		);

		return Html::rawElement( 'div', array( 'class' => 'editmode' ),
			Html::rawElement( 'div', array( 'class' => 'header' ), $header )
			. Html::rawElement( 'div', array( 'class' => 'descriptionOuter' ), $descriptions )
		);
	}
}
