/**
 * JavaScript for the Storyboard extension.
 * 
  * @file storyboard.js
  * @ingroup Storyboard
  *
  * @author Jeroen De Dauw
 */


function stbMsg( key ) {
	return wgStbMessages[key];
}

function stbMsgExt( key, values ) {
	var message = stbMsg( key );

	var n = values.length;
	for ( var i = 0; i < n; i++ ) {
		message = message.replace( '$' + ( i + 1 ), values[i] );
	}
	
	return message;
}

/**
 * Story submission/editting functions
 */

/**
 * Validates a story, and will update the provided UI elements depending on the result.
 * 
 * @param textarea Textarea object in which the story resides.
 * @param lowerLimit Min numbers of chars a story can be.
 * @param upperLimit Max numbers of chars a story can be.
 * @param infodiv Id of a div to put info about the characters amount in.
 * @param submissionButton Id of the button that will submit the story.
 */
function stbValidateStory( textarea, lowerLimit, upperLimit, infodiv, submissionButton ) {
	var button = document.getElementById( submissionButton );
	button.disabled = !stbLimitChars( textarea, lowerLimit, upperLimit, infodiv );
}

/**
 * Validates the amount of characters of a story and outputs the result in an infodiv.
 * 
 * @param textarea Textarea object in which the story resides.
 * @param lowerLimit Min numbers of chars a story can be.
 * @param upperLimit Max numbers of chars a story can be.
 * @param infodiv Id of a div to put info about the characters amount in.
 * 
 * @return Boolean indicating whether the story is valid.
 */
function stbLimitChars( textarea, lowerLimit, upperLimit, infodiv ) {
	var text = textarea.value; 
	var textlength = text.length;
	var info = document.getElementById( infodiv );
	
	if( textlength > upperLimit ) {
		info.innerHTML = stbMsgExt( 'storyboard-charstomany', [-( upperLimit - textlength )] );
		return false;
	} else if ( textlength < lowerLimit ) {
		info.innerHTML = stbMsgExt( 'storyboard-morecharsneeded', [lowerLimit - textlength] );
		return false;
	} else {
		info.innerHTML = stbMsgExt( 'storyboard-charactersleft', [upperLimit - textlength] );
		return true;
	}
}

/**
 * Checks if the story can actually be submitted, and shows and error when this is not the case.
 * 
 * @param termsCheckbox Id of a terms of service checkbox that needs to be checked.
 * 
 * @return Boolean indicating whether the submission is valid.
 */
function stbValidateSubmission( termsCheckbox ) {
	var agreementValid = document.getElementById( termsCheckbox ).checked;
	if ( !agreementValid ) {
		alert( stbMsg( 'storyboard-needtoagree' ) );
	}
	return agreementValid;
}



/**
 * Story review functions
 */

function stbShowReviewBoard( tab, state ) {
	tab.html( jQuery( "<div />" )
		.addClass( "storyreviewboard" )
		.attr( { "style": "height: 420px; width: 100%;", "id": "storyreviewboard-" + state } ) );
	
	window.reviewstate = state;
	
	jQuery( '#storyreviewboard-' + state ).ajaxScroll( {
		updateBatch: stbUpdateReviewBoard,
		maxOffset: 500,
		batchSize: 8,
		batchClass: "batch",
		boxClass: "storyboard-box",
		emptyBatchClass: "storyboard-empty",
		scrollPaneClass: "scrollpane"
	} );	
}

function stbUpdateReviewBoard( $storyboard ) {
	requestArgs = {
		'action': 'query',
		'list': 'stories',
		'format': 'json',
		'stlimit': 8,
		'stlanguage': window.storyboardLanguage,
		'streview': 1,
		'ststate': window.reviewstate
	};
	
	jQuery.getJSON( wgScriptPath + '/api.php',
		requestArgs,
		function( data ) {
			if ( data.query ) {
				stbAddStories( $storyboard, data.query );
			} else {
				alert( stbMsgExt( 'storyboard-anerroroccured', [data.error.info] ) );
			}		
		}
	);	
}

function stbAddStories( $storyboard, query ) {
	// Remove the empty boxes.
	$storyboard.html( '' );

	// TODO: create the review blocks html with jQuery
	for ( var i in query.stories ) {
		var story = query.stories[i];
		var $storyBody = jQuery( "<div />" ).addClass( "storyboard-box" );
		
		var $header = jQuery( "<div />" ).addClass( "story-header" ).appendTo( $storyBody );
		jQuery( "<div />" ).addClass( "story-title" ).text( story.title ).appendTo( $header );
		
		var textAndImg = jQuery( "<div />" ).addClass( "story-text" ).text( story["*"] );
		
		if ( story.imageurl ) {
			textAndImg.prepend(
				jQuery( "<img />" ).attr( "src", story.imageurl ).addClass( "story-image" )
			);
		}
		
		$storyBody.append( textAndImg );
		
		var metaDataText; 
		if ( story.location != '' ) {
			metaDataText = stbMsgExt( 'storyboard-storymetadatafrom', [story.author, story.location, story.creationtime, story.creationdate] );
		}
		else {
			metaDataText = stbMsgExt( 'storyboard-storymetadata', [story.author, story.creationtime, story.creationdate] );
		}
		
		$storyBody.append( // TODO: get the actual message here
			jQuery( "<div />" ).addClass( "story-metadata" ).append(
				jQuery("<span />").addClass( "story-metadata" ).text( metaDataText )
			)
		);
		
		// TODO: add review controls
		$storyBody.append(
			jQuery( "<div />" ).append( jQuery( "<button />" ).text( "edit" ).attr( "onclick", "window.location='" + story.modifyurl + "'" ) )
		);
		
		$storyboard.append( $storyBody );	
	}
}

/**
 * Calls the StoryReview API module to do actions on a story and handles updating of the page in the callback.
 * 
 * @param sender The UI element invocing the action, typically a button.
 * @param storyid Id identifying the story.
 * @param action The action that needs to be performed on the story.
 */
function stbDoStoryAction( sender, storyid, action ) {
	sender.innerHTML = stbMsg( 'storyboard-working' );
	sender.disabled = true;
	
	jQuery.getJSON( wgScriptPath + '/api.php',
		{
			'action': 'storyreview',
			'format': 'json',
			'storyid': storyid,
			'storyaction': action
		},
		function( data ) {
			if ( data.storyreview ) {
				switch( data.storyreview.action ) {
					case 'publish' : case 'unpublish' : case 'hide' :
						sender.innerHTML = stbMsg( 'storyboard-done' );
						jQuery( '#story_' + data.storyreview.id ).slideUp( 'slow', function () {
							jQuery( this ).remove();
						} );
						// TODO: would be neat to update the other list when doing an (un)publish here
						break;
					case 'hideimage' : case 'unhideimage' :
						stbToggeShowImage( sender, data.storyreview.id, data.storyreview.action );
						break;
					case 'deleteimage' :
						sender.innerHTML = stbMsg( 'storyboard-imagedeleted' );
						jQuery( '#story_image_' + data.storyreview.id ).slideUp( 'slow', function () {
							jQuery( this ).remove();
						} );
						document.getElementById( 'image_button_' + data.storyreview.id  ).disabled = true;				
						break;
				}
			} else {
				alert( stbMsgExt( 'storyboard-anerroroccured', [data.error.info] ) );
			}
		}
	);
}

/**
 * Updates the show/hide image button after a hideimage/unhideimage
 * action has completed sucesfully. Also updates the image on the 
 * page itself accordingly, and hooks up the correct event to the button. 
 * 
 * @param sender The button that invoked the completed action.
 * @param storyId The id of the story that has been affected.
 * @param completedAction The name ofthe action that has been performed.
 */
function stbToggeShowImage( sender, storyId, completedAction ) {
	if ( completedAction == 'hideimage' ) {
		jQuery( '#story_image_' + storyId ).slideUp( 'slow', function () {
			sender.innerHTML = stbMsg( 'storyboard-showimage' );
			sender.onclick = function() {
				stbDoStoryAction( sender, storyId, 'unhideimage' );
			};
			sender.disabled = false;
		} );
	} else {
		jQuery( '#story_image_' + storyId ).slideDown( 'slow', function () {
			sender.innerHTML = stbMsg( 'storyboard-hideimage' );
			sender.onclick = function() {
				stbDoStoryAction( sender, storyId, 'hideimage' );
			};
			sender.disabled = false;
		} );
	}
}

/**
 * Asks the user to confirm the deletion of an image, and if confirmed, calls stbDoStoryAction with action=delete.
 * 
 * @param sender The UI element invocing the action, typically a button.
 * @param storyid Id identifying the story.
 * 
 * @return Boolean indicating whether the deletion was confirmed.
 */
function stbDeleteStoryImage( sender, storyid ) {
	var confirmed = confirm( stbMsg( 'storyboard-imagedeletionconfirm' ) );
	if ( confirmed ) { 
		stbDoStoryAction( sender, storyid, 'deleteimage' );
	}
	return confirmed;
}