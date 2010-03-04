/**
 * JavaScript added for <storyboard> tags
 */

(function($) {
	$( document ).ready( function(){
		$( '.storyboard' ).ajaxScroll( {
			updateBatch: updateStoryboard,
			batchSize: 3,
			batchNum: 2
		} );
	});
	
	function updateStoryboard( $storyboard ){
		$.getJSON( wgScriptPath + '/api.php',
			{
				'action': 'query',
				'list': 'stories',
				'stcontinue': $storyboard.attr( 'offset' ),
				'stlimit': 5,
				'format': 'json'
			},
			function( data ) {
				var html = $( "<div />" );
				for (i in data.query.stories) {
					var story = data.query.stories[i];
					var storyBody = $( "<span />" ).addClass( "storyboard-box" );
					
					storyBody.append( $("<img />" )
						.attr( "src", "http://upload.wikimedia.org/wikipedia/mediawiki/9/99/SemanticMaps.png" )
						.addClass( "storyboard-image" )
						); // TODO: replace by wgScriptPath + path/to/scropped/img
					
					var header = $( "<div />" ).addClass( "storyboard-header" );
					header.append( $("<div />" ).addClass( "storyboard-title" ).text( story.title ) );
					
					var socialSharing = $( "<div />" ).addClass( "storyboard-sharing" );
					socialSharing.append( $("<div />").addClass( "storyboard-sharing-item" )
						.append( $("<a />").attr("target","_blank").attr("href", "http://delicious.com/save?jump=yes&url=" + "")
							.append( $("<img />").attr("src", wgScriptPath + "/extensions/Storyboard/images/storyboard-delicious.png") ) ) ); //TODO
					socialSharing.append( $("<div />").addClass( "storyboard-sharing-item" )
						.append( $("<a />").attr("target","_blank").attr("href", "http://www.facebook.com/sharer.php?u=" + "" + "&t=" + story.title)
							.append( $("<img />").attr("src", wgScriptPath + "/extensions/Storyboard/images/storyboard-facebook.png") ) ) ); //TODO
					socialSharing.append( $("<div />").addClass( "storyboard-sharing-item" )
						.append( $("<a />").attr("target","_blank").attr("href", "http://twitter.com/home?status=" + "")
							.append( $("<img />").attr("src", wgScriptPath + "/extensions/Storyboard/images/storyboard-twitter.png") ) ) ); //TODO
					
					header.append( socialSharing );
					storyBody.append( header );
					
					storyBody.append( $("<div />").text( story.text ).addClass( "storyboard-text" ) ); // Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris dapibus, nibh vitae blandit tincidunt, risus libero vehicula mauris, nec interdum mi lectus lobortis turpis. Suspendisse vel magna dapibus purus iaculis hendrerit nec ut lectus. Nullam in turpis sed elit volutpat accumsan id quis lectus. Phasellus a felis lectus. Donec erat neque, tincidunt sit amet tempus non, malesuada vel justo. Vestibulum eget magna enim, quis malesuada lorem. Integer rutrum scelerisque adipiscing. Proin feugiat tincidunt ultrices. Vivamus at justo turpis, ut porta mi. Fusce nisl eros, luctus non accumsan eget, varius id urna. Quisque hendrerit, neque eu varius sollicitudin, lacus nisl auctor odio, eget egestas turpis sapien ut diam. Quisque ultricies consequat erat in tempor. Nam ut libero ac massa volutpat vestibulum vel sed leo. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Aenean pharetra, nisi ut dignissim semper, nunc lectus elementum dolor, sit amet blandit mauris diam ut nisi. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Etiam fringilla ultricies dapibus. Donec tristique congue lorem eget varius. Nunc nunc orci, molestie et elementum eget, pulvinar ac ante. Nam fermentum est ut lorem luctus suscipit consectetur ligula gravida. Phasellus non magna sit amet lectus feugiat malesuada eu non lorem. Nam commodo fermentum mauris, sed vehicula risus molestie et. Sed nisl neque, mollis sit amet malesuada ac, bibendum vitae mauris. Quisque dictum viverra eros quis gravida. Etiam vitae augue risus. Donec at orci vitae mauris luctus porttitor.
					
					storyBody.append();
					
					html.append( storyBody );
				}
				$storyboard.html(html);
			}
		);
	}
})(jQuery);