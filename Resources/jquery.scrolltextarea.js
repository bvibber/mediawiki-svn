/**
 * Plugin that can get the byte offset of the cursor in a textarea, move the
 * cursor to a byte offset and scroll the textarea to the cursor's new position.
 */
(function($){
	$.fn.extend({
		// The getCaret(), getLineLength() and getCaretPosition()
		// functions were copied from Wikia's LinkSuggest extension and
		// modified slightly.
		// https://svn.wikia-code.com/wikia/trunk/extensions/wikia/LinkSuggest/LinkSuggest.js
		
		/**
		 * Get the byte position in a textarea 
		 */
		 bytePos: function() {
			function getCaret(control) {
				var caretPos = 0;
				// IE Support
				if($.browser.msie) {
					// This code was copied from
					// http://www.dedestruct.com/2008/03/22/howto-cross-browser-cursor-position-in-textareas/
					var selection_range = document.selection.createRange().duplicate();

					// Create three ranges, one containing all the text before the selection,
					// one containing all the text in the selection (this already exists), and one containing all
					// the text after the selection.
					var before_range = document.body.createTextRange();
					before_range.moveToElementText(control); // Selects all the text
					before_range.setEndPoint("EndToStart", selection_range); // Moves the end where we need it
					
					var after_range = document.body.createTextRange();
					after_range.moveToElementText(control); // Selects all the text
					after_range.setEndPoint("StartToEnd", selection_range); // Moves the start where we need it
					
					var before_finished = false, selection_finished = false, after_finished = false;
					var before_text, untrimmed_before_text, selection_text, untrimmed_selection_text, after_text, untrimmed_after_text;
					
					// Load the text values we need to compare
					before_text = untrimmed_before_text = before_range.text;
					selection_text = untrimmed_selection_text = selection_range.text;
					after_text = untrimmed_after_text = after_range.text;
					
					
					// Check each range for trimmed newlines by shrinking the range by 1 character and seeing
					// if the text property has changed. If it has not changed then we know that IE has trimmed
					// a \r\n from the end.
					do {
						if (!before_finished) {
							if (before_range.compareEndPoints("StartToEnd", before_range) == 0) {
								before_finished = true;
							} else {
								before_range.moveEnd("character", -1)
								if (before_range.text == before_text) {
									untrimmed_before_text += "\r\n";
								} else {
									before_finished = true;
								}
							}
						}
						if (!selection_finished) {
							if (selection_range.compareEndPoints("StartToEnd", selection_range) == 0) {
								selection_finished = true;
							} else {
								selection_range.moveEnd("character", -1)
								if (selection_range.text == selection_text) {
									untrimmed_selection_text += "\r\n";
								} else {
									selection_finished = true;
								}
							}
						}
						if (!after_finished) {
							if (after_range.compareEndPoints("StartToEnd", after_range) == 0) {
								after_finished = true;
							} else {
								after_range.moveEnd("character", -1)
								if (after_range.text == after_text) {
									untrimmed_after_text += "\r\n";
								} else {
									after_finished = true;
								}
							}
						}
					
					} while ((!before_finished || !selection_finished || !after_finished));
					
					caretPos = untrimmed_before_text.replace(/\r\n/g, "\n").length;
				// Firefox support
				} else if (control.selectionStart || control.selectionStart == '0') {
					caretPos = control.selectionStart;
				}
				return caretPos;
			}
			
			return getCaret( this.get( 0 ) );
		},

		/**
		 * Scroll a textarea to a certain offset
		 * @param pos Byte offset in the contents
		 */
		scrollToPosition: function( pos ) {
			function getLineLength(control) {
				var width = control.scrollWidth;
				return Math.floor(width/($.os.name == 'linux' ? 7 : 8));
			}
			
			function getCaretPosition(control) {
				var text = control.value.replace(/\r/g, "");
				var caret = $(control).bytePos();
				var lineLength = getLineLength(control);

				var row = 0;
				var charInLine = 0;
				var lastSpaceInLine = 0;

				for(i = 0; i < caret; i++) {
					charInLine++;
					if(text.charAt(i) == " ") {
						lastSpaceInLine = charInLine;
					} else if(text.charAt(i) == "\n") {
						lastSpaceInLine = 0;
						charInLine = 0;
						row++;
					}
					if(charInLine > lineLength) {
						if(lastSpaceInLine > 0) {
							charInLine = charInLine - lastSpaceInLine;

							lastSpaceInLine = 0;
							row++;
						}
					}
				}
				var nextSpace = 0;
				for(j = caret; j < caret + lineLength; j++) {
					if(text.charAt(j) == " " || text.charAt(j) == "\n" || caret == text.length) {
						nextSpace = j;
						break;
					}
				}

				if(nextSpace > lineLength && caret <= lineLength) {
					charInLine = caret - lastSpaceInLine;
					row++;
				}


				return ($.os.name == 'mac' ? 13 : ($.os.name == 'linux' ? 15 : 16))*row;
			}

			return this.each(function() {
				// Put the cursor at the desired position
				this.focus();
				if ( this.selectionStart || this.selectionStart == '0' ) { // Mozilla
					this.selectionStart = this.selectionEnd = pos;
				} else if ( document.selection && document.selection.createRange ) { // IE/Opera
					var range = document.selection.createRange();
					range.moveToElementText( this );
					range.collapse();
					//range.moveStart( 'character', pos );
					range.move( 'character', pos );
					//alert(range.text);
					range.select();
				}
				$(this).scrollTop( getCaretPosition( this ) );
				$(this).trigger( 'scrollToPosition' );
			});
		}
	});
})(jQuery);
