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
			batchNum: 2
		} );
	} );
	
	function updateStoryboard( $storyboard ) {
		// TODO: fix eternal load, broken when swicthing from .load to .getJSON.	
		$.getJSON( wgScriptPath + '/api.php',
			{
				'action': 'query',
				'list': 'stories', 
				'stcontinue': $storyboard.attr( 'offset' ) + '-0', // TODO: get id of last story here to break ties correctly
				'stlimit': 4,
				'format': 'json'
			},
			function( data ) {
				if ( data.query ) {
					addStories( $storyboard, data.query );
				} else {
					alert( 'An error occured:\n' + data.error.info ); // TODO: i18n
				}		
			}
		);
	}
	
	function addStories( $storyboard, query ) {
		// Remove the empty boxes.
		$storyboard.html('');
		
		for ( var i in query.stories ) {
			var story = query.stories[i];
			var $storyBody = $( "<div />" ).addClass( "storyboard-box" );
			
			var $header = $( "<div />" ).addClass( "story-header" ).appendTo( $storyBody );
			$( "<div />" ).addClass( "story-title" ).text( story.title ).appendTo( $header );
			
			// TODO: move social sharing to a jQuery UI pop-up that's triggered by a link above each storyboard-box
			
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
			
			var textAndImg = $( "<div />" ).addClass( "story-text" ).text( story["*"] );
			
			if ( story.imageurl ) {
				textAndImg.prepend(
					$( "<img />" ).attr( "src", story.imageurl ).addClass( "story-image" )
				);
			}
			
			$storyBody.append( textAndImg );
			
			// TODO: add hide button
			
			$storyboard.append( $storyBody );	
		}
	}
		
})(jQuery);