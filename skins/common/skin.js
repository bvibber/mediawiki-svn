// Function to update skin content

function wgSkinUpdate( newPageName ) {

	// Mediawiki global variables
    wgTitle = mPageTitle;
    wgOldPageName = wgPageName;
    wgPageName = newPageName;

	if( skin == 'monobook' ) {
		wgMonobookUpdate();
	}

	var elCactions = document.getElementById( 'l-cactions' );
	// TODO: go through the <li><a> and change the href.

	return;
}

// Update monobook links
function wgMonobookUpdate( ) {
//wgPageName


}

