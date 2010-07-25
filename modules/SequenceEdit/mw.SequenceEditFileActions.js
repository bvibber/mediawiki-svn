/**
 * Handles dialogs for sequence edit file actions such as 
 * 	"save sequence",
 * 	"rename", 
 * 	"publish"
 *  
 * Hooks into sequenceEditApiProvider to run the actual api operations  
 */

mw.SequenceEditFileActions = function( sequenceEdit ) {
	return this.init( sequenceEdit );
};

mw.SequenceEditFileActions.prototype = {
	init: function( sequenceEdit ) {
		this.sequenceEdit = sequenceEdit; 
	},
	
	/**
	 * Sequencer "viewXml" action
	 * presents a dialog that displays the current smil xml document
	 */
	viewXml: function(){
		var _this = this;
		// for now just show the sequence output
		$viewSmilXmlDialog = mw.addDialog( 
			gM('mwe-sequenceedit-save-sequence'),
			$j('<div />').append(
				$j('<h3 />')
				.text( gM('mwe-sequenceedit-sequence-xml') ),
				
				$j('<span />')
				.text( gM('mwe-sequenceedit-sequence-xml-no-api-note') ),
				
				// Add a loading div
				$j('<div />')
				.addClass('syntaxhighlighter_loader')
				.loadingSpinner(),
				
				$j('<pre />')				
				.addClass( 'brush: xml; ruler: true;' )
				.css( 'display', 'none' )
				.html(    
					mw.escapeQuotesHTML( _this.sequenceEdit.smil.getXMLString() ) 
				)
			)
			.css({
				'width' : '640px',
				'height' : '420px',
				'overflow': 'auto'
			})
		)
		// load and run the syntax highlighter:
		$j( $viewSmilXmlDialog.find('pre') ).syntaxHighlighter(function(){
			$viewSmilXmlDialog.find('.syntaxhighlighter_loader').remove();
			$viewSmilXmlDialog.find('pre').fadeIn();
		});
		
	}
}