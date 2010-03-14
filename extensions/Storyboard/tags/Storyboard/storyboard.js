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
			batchSize: 3,
			batchNum: 2
		} );
	} );
	
	function updateStoryboard( $storyboard ) {
		$.getJSON( wgScriptPath + '/api.php',
			{
				'action': 'query',
				'list': 'stories',
				'stcontinue': $storyboard.attr( 'offset' ),
				'stlimit': 5,
				'format': 'json'
			},
			function( data ) {
				var $div = $( "<div />" );
				for ( var i in data.query.stories ) {
					var story = data.query.stories[i];
					var $storyBody = $( "<div />" ).addClass( "storyboard-box" );
					
					var $header = $( "<div />" ).addClass( "storyboard-header" ).appendTo( $storyBody );
					$( "<div />" ).addClass( "storyboard-title" ).text( story.title ).appendTo( $header );
					
					$( "<div />" )
						.addClass( "storyboard-sharing" )
						.append(
							$( "<div />" ).addClass( "storyboard-sharing-item" ).append(
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
							$( "<div />" ).addClass( "storyboard-sharing-item" ).append(
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
							$( "<div />" ).addClass( "storyboard-sharing-item" ).append(
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
					
					$storyBody.append( $( "<div />" ).addClass( "storyboard-text" )
						.text( story["*"] )
						.prepend( $( "<img />" )
							// TODO: replace by wgScriptPath + path/to/scropped/img
							.attr( "src", "http://upload.wikimedia.org/wikipedia/mediawiki/9/99/SemanticMaps.png" )
							.addClass( "storyboard-image" )
						)
					);
					
					$div.append( $storyBody );
				}
				$storyboard.html( $div );
			}
		);
	}
})(jQuery);