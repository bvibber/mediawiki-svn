/* JavaScript for Toolbar extension */

$( document ).ready( function() {
	var imageUrl = wgScriptPath + '/extensions/UsabilityInitiative/Toolbar/images/';
	var toolbar = $( 'div#editing-toolbar' );
	var tools = {
		format: {
			bold: {
				icon: 'format-bold.png'
			},
			italic: {
				icon: 'format-italic.png'
			}
		},
		insert: {
			link: {
				icon: 'insert-link.png'
			},
			image: {
				icon: 'insert-image.png'
			},
			reference: {
				icon: 'insert-reference.png'
			}
		}
	};
	for ( group in tools ) {
		var groupDiv = $( '<div class="group" id="editing-toolbar-group-' + group + '"></div>' );
		groupDiv.appendTo( toolbar );
		for ( tool in tools[group] ) {
			var toolDiv = $( '<div class="tool" id="editing-toolbar-tool-' + tool + '"></div>' );
			toolDiv.appendTo( groupDiv );
			toolDiv.css( 'background-image', 'url(' + imageUrl + tools[group][tool]['icon'] + ')' );
		}
	}
});
