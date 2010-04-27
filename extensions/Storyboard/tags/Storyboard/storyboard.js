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
			maxOffset: 500,
			batchSize: 4,
			batchNum: 1,
			batchClass: "batch",
			boxClass: "storyboard-box",
			emptyBatchClass: "storyboard-empty",
			scrollPaneClass: "scrollpane"
		} );
	} );
	
	function updateStoryboard( $storyboard ) {
		$.getJSON( wgScriptPath + '/api.php',
			{
				'action': 'query',
				'list': 'stories', 
				'stcontinue': $storyboard.attr( 'offset' ) + '-0', // TODO: get modification date and id of the last story
				'stlimit': 4,
				'stlanguage': window.storyboardLanguage,
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
			
			var deliciousUrl = "http://delicious.com/save?jump=yes&url=" + encodeURIComponent( story.permalink ) + "&title=" + encodeURIComponent( story.title );
			var facebookUrl = "http://www.facebook.com/sharer.php?u=" + encodeURIComponent( story.permalink ) + '&t=' + encodeURIComponent( story.title );
			
			$( "<div />" )
				.addClass( "story-sharing" )
				.append(
					$( "<div />" ).addClass( "story-sharing-item" ).append(
						$( "<a />" ).attr( {
							"target": "_blank",
							"rel": "nofollow",
							"href": deliciousUrl,
							"onclick": "window.open( '" + deliciousUrl + "', 'delicious-sharer', 'toolbar=0, status=0, width=850, height=650' ); return false;"
						} )
						.append( $( "<img />" ).attr( "src",
							wgScriptPath + "/extensions/Storyboard/images/storyboard-delicious.png"
						) )
					)
				)
				.append(
					$( "<div />" ).addClass( "story-sharing-item" ).append(
						$( "<a />" ).attr( {
							"target": "_blank",
							"rel": "nofollow",
							"href": facebookUrl,
							"onclick": "window.open( '" + facebookUrl + "', 'facebook-sharer', 'toolbar=0, status=0, width=626, height=436' ); return false;"
						} )
						.append( $( "<img />" ).attr( "src",
							wgScriptPath + "/extensions/Storyboard/images/storyboard-facebook.png"
						) )
					)
				)
				.append(
					$( "<div />" ).addClass( "story-sharing-item" ).append(
						$( "<a />" ).attr( {
							"target": "_blank",
							"rel": "nofollow",
							"href": "http://twitter.com/home?status=" + encodeURIComponent( story.permalink )
						 } )
						.append( $( "<img />" ).attr( "src",
							wgScriptPath + "/extensions/Storyboard/images/storyboard-twitter.png"
						) )
					)
				)
				.appendTo( $header );
			
			var textAndImg = $( "<div />" ).addClass( "story-text" ).text( story["*"] );
			
			if ( story.imageurl ) {
				textAndImg.prepend(
					$( "<img />" ).attr( "src", story.imageurl ).addClass( "story-image" )
				);
			}
			
			$storyBody.append( textAndImg );
			
			$storyBody.append( // TODO: get the actual message here
				$( "<div />" ).addClass( "story-metadata" ).append(
					$("<span />").addClass( "story-metadata" ).text( " Submitted by $1 from $2 on $3, $4.")
				)
			);
			
			// TODO: add hide and delete buttons
			
			$storyboard.append( $storyBody );	
		}
	}
	
	function fbsClick( url, title ) {
		alert('');
		
		return false;
	}	
		
})(jQuery);