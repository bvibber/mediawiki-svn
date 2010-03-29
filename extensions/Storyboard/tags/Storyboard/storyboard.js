/**
 * JavaScript for <storyboard> tags.
 * 
 * @author Jeroen De Dauw
 * @ingroup Storyboard
 */

(function($) {
	$( document ).ready( function() {
		$( '.storyboard' ).ajaxScroll( {
			updateBatch: updateStoryboard,
			batchSize: 4,
			batchNum: 1
		} );
	} );
	
	function updateStoryboard( $storyboard ) {
		$.getJSON( wgScriptPath + '/api.php',
			{
				'action': 'query',
				'list': 'stories',
				'stcontinue': $storyboard.attr( 'offset' ),
				'stlimit': 4,
				'format': 'json'
			},
			function( data ) {
				var html = '';
				for ( var i in data.query.stories ) {
					var story = data.query.stories[i];
					var $storyBody = $( "<div />" ).addClass( "storyboard-box" );
					
					var $header = $( "<div />" ).addClass( "story-header" ).appendTo( $storyBody );
					$( "<div />" ).addClass( "story-title" ).text( story.title ).appendTo( $header );
					
					// TODO: move social sharing to a pop-up that's triggered by a link above each storyboard-box
					
					$( "<div />" )
						.addClass( "story-sharing" )
						.append(
							$( "<div />" ).addClass( "story-sharing-item" ).append(
								$( "<a />" ).attr( {
									"target": "_blank",
									"href": "http://delicious.com/save?jump=yes&url=" + ""
								} )
								.append( $( "<img />" ).attr( "src",
									wgScriptPath + "/extensions/Storyboard/images/storyboard-delicious.png"
								) )
							)
						) //TODO
						.append(
							$( "<div />" ).addClass( "story-sharing-item" ).append(
								$( "<a />" ).attr( {
									"target": "_blank",
									"href": "http://www.facebook.com/sharer.php?u=" + "" + "&t=" + story.title
								} )
								.append( $( "<img />" ).attr( "src",
									wgScriptPath + "/extensions/Storyboard/images/storyboard-facebook.png"
								) )
							)
						) //TODO
						.append(
							$( "<div />" ).addClass( "story-sharing-item" ).append(
								$( "<a />" ).attr( {
									"target": "_blank",
									"href": "http://twitter.com/home?status=" + ""
								 } )
								.append( $( "<img />" ).attr( "src",
									wgScriptPath + "/extensions/Storyboard/images/storyboard-twitter.png"
								) )
							)
						) //TODO
						.appendTo( $header );
					
					$storyBody.append( $( "<div />" ).addClass( "story-text" )
						.text( story["*"] )
						.prepend( $( "<img />" )
							// TODO: replace by wgScriptPath + path/to/scropped/img
							.attr( "src", "http://upload.wikimedia.org/wikipedia/mediawiki/9/99/SemanticMaps.png" )
							.addClass( "story-image" )
						)
					);
					
					// TODO: add delete button that hides the story from the storyboard (=unpublish+hide?)
					
					html += $storyBody;
				}
				$storyboard.html( html );
			}
		);
	}
})(jQuery);