/**
 * JavaScript added for <storyboard> tags
 */

$j( document ).ready( function(){
	$j( '#storyboard' ).ajaxScroll( {
		updateBatch: updateStoryboard,
		batchSize: 5,
		batchNum: 1
	} );
});

function updateStoryboard( $storyboard ){
	$j.getJSON( wgScriptPath + '/api.php',
		{
			'action': 'query',
			'list': 'stories',
			'stcontinue': $storyboard.attr( 'offset' ),
			'stlimit': 5,
			'format': 'json'
		},
		function( data ) {
			// TODO: use data to create stories html
		}
	);
}
