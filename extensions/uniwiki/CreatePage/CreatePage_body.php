<?php
class SpecialCreatePage extends SpecialPage {

	function __construct() {
		SpecialPage::SpecialPage( 'CreatePage' );
	}

	public function execute( $params ) {
		global $wgOut, $wgRequest, $wgUser;

		wfLoadExtensionMessages( 'CreatePage' );

		$skin = $wgUser->getSkin();

		$thisPage = Title::newFromText ( "CreatePage", NS_SPECIAL );

		$target = $wgRequest->getVal ( "target", null );

		// check to see if we are trying to create a page
		if ( $target != null ) {
			$title = Title::newFromText ( $target );

			if ( $title->getArticleID() > 0 ) {

				// if the title exists then let the user know and give other options
				$wgOut->addWikiText ( wfMsg ( "createpage_titleexists", $title->getFullText() ) . "<br />" );
				$wgOut->addHTML ( "<a href='" . $title->getEditURL() . "'>" . wfMsg ( "createpage_editexisting" ) . "</a><br />"
					. $skin->makeLinkObj ( $thisPage, wfMsg ( "createpage_tryagain" ) )
				);
			} else {
				/* TODO - may want to search for closely named pages and give
				 * other options here... */

				// otherwise, redirect them to the edit page for their title
				$wgOut->redirect ( $title->getEditURL() );
			}

			return;
		}

		// if this is just a normal GET, then output the form

		// prefill the input with the title, if it was passed along
		$newTitle = $wgRequest->getVal( "newtitle", null );
		if ( $newTitle != null ) $newTitle = str_replace( "_", " ", $newTitle );

		// add some instructions
		$wgOut->addHTML( wfMsg( 'createpage_instructions' ) );

		// js for checking the form
		$wgOut->addHTML( "
			<script type='text/javascript' >
				function checkForm(){
						// check the title
						if (document.createpageform.target && document.createpageform.target.value == \"\") {
							alert('" . wfMsg( 'createpage_entertitle' ) . "');
							document.createpageform.target.focus();
							return false;
						}
					// everything is OK, return true
					return true;
				}
			</script>
		" );

		// output the form
		$wgOut->addHTML( "
			<form method=POST onsubmit='return checkForm()' name='createpageform'>
				<input type=text name=target size=50 value='$newTitle'><br /><br />
		" );

		$wgOut->addHTML( "
				<input type=submit value='" . wfMsg( 'createpage_submitbutton' ) . "'>
			</form>
		" );
	}
}
