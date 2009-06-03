/*
 * Functions getSelection and replaceSelection based on:
 * 		(c) 2006 Alex Brem <alex@0xab.cd> - http://blog.0xab.cd
 * All other code:
 * 		(c) 2009 Wikimedia Foundation (GPLv2) - http://www.wikimedia.org
 */
(function($) {
    $.fn.extend({
		getSelection: function() {
            var e = this.jquery ? this[0] : this;
			if ( 'selectionStart' in e ) {
				/* Mozilla / DOM 3.0 */
				var l = e.selectionEnd - e.selectionStart;
				return { start: e.selectionStart, end: e.selectionEnd, length: l, text: e.value.substr( e.selectionStart, l ) };
			} else if ( document.selection ) {
				/* Internet Explorer */
				e.focus();
				var r = document.selection.createRange();
				if (r == null) {
					return { start: 0, end: e.value.length, length: 0, text: null }
				}
				var re = e.createTextRange();
				var rc = re.duplicate();
				re.moveToBookmark( r.getBookmark() );
				rc.setEndPoint( 'EndToStart', re );
				return { start: rc.text.length, end: rc.text.length + r.text.length, length: r.text.length, text: r.text };
			} else {
				/* Browser not supported */
				return { start: 0, end: e.value.length, length: 0, text: null };
			}
        },
		replaceSelection: function() {
            var e = this.jquery ? this[0] : this;
			var text = arguments[0] || '';
			if ( 'selectionStart' in e ) {
				/* Mozilla / DOM 3.0 */
				e.value = e.value.substr( 0, e.selectionStart ) + text + e.value.substr( e.selectionEnd, e.value.length );
			} else if ( document.selection ) {
				/* Internet Explorer */
				e.focus();
				document.selection.createRange().text = text;
			} else {
				/* Browser not supported  */
				e.value += text;
			}
			return this;
        },
		encapsulateSelection: function( pre, post, insert ) {
			var obj = $( this );
			obj.replaceSelection( ( pre || '' ) + ( obj.getSelection().text || insert ) + ( post || pre || '' ) );
			return this;
        }
    });
})(jQuery);