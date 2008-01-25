<?php

/**
 * Extension for allowing a boilerplate to be selected from a drop down box at
 * the top of the article edit page.
 *
 * @addtogroup Extensions
 *
 * @link http://www.mediawiki.org/wiki/Extension:MultiBoilerplate
 *
 * @author MinuteElectron <minuteelectron@googlemail.com>
 * @copyright Copyright © 2007-2008 MinuteElectron.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

// If this is run directly from the web die as this is not a valid entry point.
if( !defined( 'MEDIAWIKI' ) ) {
	die( 'Invalid entry point.' );
}

// Extension credits.
$wgExtensionCredits[ 'other' ][] = array(
	'name' => 'MultiBoilerplate',
	'description' => 'Displays a box at the top of the edit page to select and load a boilerplate.',
	'author' => 'MinuteElectron',
	'url' => 'http://www.mediawiki.org/wiki/Extension:MultiBoilerplate',
	'version' => '1.3',
);

// Hook into EditPage::showEditForm:initial to modify the edit page header.
$wgHooks[ 'EditPage::showEditForm:initial' ][] = 'wfMultiBoilerplate';

// Set extension messages file.
$wgExtensionMessagesFiles[ 'MultiBoilerplate' ] = dirname( __FILE__ ) . '/MultiBoilerplate.i18n.php';

// Default configuration variables.
// Array of boilerplate names to boilerplate pages to load, for example:
// e.g. $wgMultiBoilerplateThings[ 'My Boilerplate' ] = 'Template:My_Boilerplate';
$wgMultiBoilerplateThings = array(); 
// Whether or not to show the form when editing pre-existing pages.
$wgMultiBoilerplateOverwrite = false;

/**
 * Generate the form to be displayed at the top of the edit page and insert it into the page.
 * @param $form EditPage object.
 * @return true
 */
function wfMultiBoilerplate( $form ) {

	// Get various variables needed for this extension.
	global $wgMultiBoilerplateThings, $wgMultiBoilerplateOverwrite, $wgArticle, $wgTitle, $wgRequest;

	// Load messages into the message cache.
	wfLoadExtensionMessages( 'MultiBoilerplate' );

	/* If $wgMultiBoilerplateAllowOverwrite is ture connect to the database to check if the page currently
	 * being edited exists, if it doesn't render the box else don't (to prevent users inadvertantly overwriting
	 * an existing page).
	 * @todo Check if there is a pre-existing function to do this in the Article or Title objects.
	 */
	if( !$wgMultiBoilerplateOverwrite ) {
		$dbr =& wfGetDB( DB_SLAVE );
		$res = $dbr->select( 'page', 'page_id', array( 'page_id' => $wgTitle->getArticleID() ) );
		$row = $dbr->fetchRow( $res );
		if( is_array( $row ) ) return true; // Return true to end execution of this function.
		$dbr->freeResult( $res );
	}

	// Generate the options list used inside the boilerplate selection box.
	$options = '';
	foreach( $wgMultiBoilerplateThings as $name => $template ) {
		if( $wgRequest->getVal( 'boilerplate' ) == $template ) $selected = true;
		$options .= Xml::option( $name, $template, $selected );
	}

	// Append the selection form to the top of the edit page.
	$form->editFormPageTop .=
		Xml::openElement( 'form', array( 'id' => 'multiboilerplateform', 'name' => 'multiboilerplateform', 'method' => 'get', 'action' => $wgTitle->getEditURL() ) ) .
			Xml::openElement( 'fieldset' ) .
				Xml::element( 'legend', NULL, wfMsg( 'multiboilerplate-legend' ) ) .
				Xml::openElement( 'label' ) .
					wfMsg( 'multiboilerplate-label' ) .
					Xml::openElement( 'select', array( 'name' => 'boilerplate' ) ) .
						$options .
					Xml::closeElement( 'select' ) .
				Xml::closeElement( 'label' ) .
				' ' .
				Xml::hidden( 'action', 'edit' ) .
				Xml::hidden( 'title', $wgRequest->getText( 'title' ) ) .
				Xml::submitButton( wfMsg( 'multiboilerplate-submit' ) ) .
			Xml::closeElement( 'fieldset' ) .
		Xml::closeElement( 'form' );

	// If the Load button has been pushed replace the article text with the boilerplate.
	if( $wgRequest->getText( 'boilerplate', false ) ) {
		$plate = new Article( Title::newFromURL( $wgRequest->getVal( 'boilerplate' ) ) );
		$form->textbox1 = $plate->fetchContent();
	}

	// Return true so things don't break.
	return true;

}
