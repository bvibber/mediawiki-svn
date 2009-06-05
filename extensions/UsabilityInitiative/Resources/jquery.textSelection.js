/*
 * Ported from skins/common/edit.js by Trevor Parscal
 * (c) 2009 Wikimedia Foundation (GPLv2) - http://www.wikimedia.org
 */
(function($) {
    $.fn.extend({
		encapsulateSelection: function( pre, peri, post ) {
            var e = this.jquery ? this[0] : this;
			/**
			 * CLEAN THIS UP PLEASE!
			 */
			var selText;
			var isSample = false;
			if (document.selection  && document.selection.createRange) { // IE/Opera
		
				//save window scroll position
				if (document.documentElement && document.documentElement.scrollTop)
					var winScroll = document.documentElement.scrollTop
				else if (document.body)
					var winScroll = document.body.scrollTop;
				//get current selection
				e.focus();
				var range = document.selection.createRange();
				selText = range.text;
				//insert tags
				checkSelectedText();
				range.text = pre + selText + post;
				//mark sample text as selected
				if (isSample && range.moveStart) {
					if (window.opera)
						post = post.replace(/\n/g,'');
					range.moveStart('character', - post.length - selText.length);
					range.moveEnd('character', - post.length);
				}
				range.select();
				//restore window scroll position
				if (document.documentElement && document.documentElement.scrollTop)
					document.documentElement.scrollTop = winScroll
				else if (document.body)
					document.body.scrollTop = winScroll;
		
			} else if (e.selectionStart || e.selectionStart == '0') { // Mozilla
		
				//save textarea scroll position
				var textScroll = e.scrollTop;
				//get current selection
				e.focus();
				var startPos = e.selectionStart;
				var endPos = e.selectionEnd;
				selText = e.value.substring(startPos, endPos);
				//insert tags
				checkSelectedText();
				e.value = e.value.substring(0, startPos)
					+ pre + selText + post
					+ e.value.substring(endPos, e.value.length);
				//set new selection
				if (isSample) {
					e.selectionStart = startPos + pre.length;
					e.selectionEnd = startPos + pre.length + selText.length;
				} else {
					e.selectionStart = startPos + pre.length + selText.length + post.length;
					e.selectionEnd = e.selectionStart;
				}
				//restore textarea scroll position
				e.scrollTop = textScroll;
			}
			// Checks if the selected text is the same as the insert text
			function checkSelectedText(){
				if (!selText) {
					selText = peri;
					isSample = true;
				} else if (selText.charAt(selText.length - 1) == ' ') { //exclude ending space char
					selText = selText.substring(0, selText.length - 1);
					post += ' '
				}
			}
			/**
			 * /CLEAN THIS UP PLEASE!
			 */
		}
    });
})(jQuery);
