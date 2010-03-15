function doStoryAction( sender, storyid, action ) {
	sender.disabled = true;
	
	jQuery.getJSON( wgScriptPath + '/api.php',
		{
			'action': 'storyreview',
			'format': 'json',
			'storyid': storyid,
			'storyaction': action
		},	
		function( data ) {
			if ( data.result ) {
				switch( data.result.action ) {
					case 'publish' : case 'unpublish' : case 'hide' :
						jQuery( '#story_' + data.result.id ).slideUp( 'slow', function () {
							jQuery( this ).remove();
						} );
						// TODO: would be neat to update the other list when doing an (un)publish here
						break;
					// TODO: add handling for the other actions
				}
			} else {
				alert( 'An error occured:\n' + data.error.info ); // TODO: i18n
			}
		}
	);
}

function deleteStoryImage( sender, storyid ) {
	if ( confirm( 'Are you sure you want to permanently delete this stories image?' ) ) { // TODO: i18n
		doStoryAction( sender, storyid, 'deleteimage' );
	}
}