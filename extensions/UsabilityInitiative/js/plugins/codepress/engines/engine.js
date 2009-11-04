gecko = false;
if ( $.browser.safari || $.browser.mozilla ) {
		gecko = true;
}

WikiEditorEngine = {
	scrolling : false,
	autocomplete : true,
	
	initialize : function() {
		if ( typeof ( editor ) == 'undefined' && !arguments[0] )
			return;
		
		if ( gecko ) {
			body = document.getElementsByTagName( 'body' )[0];
			body.innerHTML = body.innerHTML.replace( /\n/g, "" );
		}
		
		chars = '|32|46|62|'; // charcodes that trigger syntax highlighting
		
		if ( gecko ) {
			chars += '8|';
		}
		
		cc = '\u2009'; // caret char
		editorTag = 'pre';
		if ( $.browser.opera ) {
			editorTag = 'body';
		}
		editor = document.getElementsByTagName( editorTag )[0];
		
		if ( $.browser.msie ) {
			editor.contentEditable = 'true';
			document.getElementsByTagName( 'body' )[0].onfocus = function() {
				editor.focus();
			}
			document.attachEvent( 'onkeydown', this.metaHandler );
			document.attachEvent( 'onkeypress', this.keyHandler );
			window.attachEvent( 'onscroll', function() {
				if ( !WikiEditorEngine.scrolling )
					setTimeout( function() {
						WikiEditorEngine.syntaxHighlight( 'scroll' )
					}, 1 )
			} );
		} else { // !$.browser.msie
			document.designMode = 'on';
			if ( gecko ) {
				document.addEventListener( 'keypress', this.keyHandler, true );
			}
			if ( $.browser.opera ) {
				document.addEventListener( 'keyup', this.keyHandler, true );
			}
			window.addEventListener( 'scroll', function() {
				if ( !WikiEditorEngine.scrolling )
					WikiEditorEngine.syntaxHighlight( 'scroll' )
			}, false );
		}
		
		completeChars = this.getCompleteChars();
		if ( !$.browser.opera ) {
			completeEndingChars = this.getCompleteEndingChars();
		}
		if ( $.browser.msie ) {
			setTimeout( function() {
				window.scroll( 0, 0 )
			}, 50 ); // scroll IE to top
		}
	},
	
	keyHandler : function( evt ) {
		if ( gecko ) {
			keyCode = evt.keyCode;
			charCode = evt.charCode;
			fromChar = String.fromCharCode( charCode );
			
			if ( ( evt.ctrlKey || evt.metaKey ) && evt.shiftKey && charCode != 90 ) {
				// shortcuts = ctrl||appleKey+shift+key!=z(undo)
				WikiEditorEngine.shortcuts( charCode ? charCode : keyCode );
			} else if ( ( completeEndingChars.indexOf( '|' + fromChar + '|' ) != -1 ||
					completeChars.indexOf( '|' + fromChar + '|' ) != -1 ) &&
					WikiEditorEngine.autocomplete ) {
				// autocomplete
				if ( !WikiEditorEngine.completeEnding( fromChar ) )
					WikiEditorEngine.complete( fromChar );
			} else if ( chars.indexOf( '|' + charCode + '|' ) != -1 || keyCode == 13 ) {
				// syntax highlighting
				top.setTimeout( function() {
					WikiEditorEngine.syntaxHighlight( 'generic' );
				}, 100 );
			} else if ( keyCode == 9 || evt.tabKey ) {
				// snippets activation (tab)
				WikiEditorEngine.snippets( evt );
			} else if ( keyCode == 46 || keyCode == 8 ) {
				// save to history when delete or backspace pressed
				WikiEditorEngine.actions.history[WikiEditorEngine.actions.next()] = editor.innerHTML;
			} else if ( ( charCode == 122 || charCode == 121 || charCode == 90 ) && evt.ctrlKey ) {
				// undo and redo
				( charCode == 121 || evt.shiftKey ) ?
					WikiEditorEngine.actions.redo() :
					WikiEditorEngine.actions.undo();
				evt.preventDefault();
			} else if ( charCode == 118 && evt.ctrlKey ) {
				// handle paste
				top.setTimeout( function() {
					WikiEditorEngine.syntaxHighlight( 'generic' );
				}, 100 );
			} else if ( charCode == 99 && evt.ctrlKey ) {
				// handle cut
				// alert(window.getSelection().getRangeAt(0).toString().replace(/\t/g,'FFF'));
			}
		} else if ( $.browser.opera ) {
			keyCode = evt.keyCode;
			charCode = evt.charCode;
			
			if ( ( evt.ctrlKey || evt.metaKey ) && evt.shiftKey && charCode != 90 ) {
				// shortcuts = ctrl||appleKey+shift+key!=z(undo)
				WikiEditorEngine.shortcuts( charCode ? charCode : keyCode );
			} else if ( completeChars.indexOf( '|' + String.fromCharCode( charCode ) + '|' ) != -1 &&
					WikiEditorEngine.autocomplete ) {
				// autocomplete
				WikiEditorEngine.complete( String.fromCharCode( charCode ) );
			} else if ( chars.indexOf( '|' + charCode + '|' ) != -1 || keyCode == 13 ) {
				// syntax highlighting
				WikiEditorEngine.syntaxHighlight( 'generic' );
			} else if ( keyCode == 9 || evt.tabKey ) {
				// snippets activation (tab)
				WikiEditorEngine.snippets( evt );
			} else if ( keyCode == 46 || keyCode == 8 ) {
				// save to history when delete or backspace pressed
				WikiEditorEngine.actions.history[WikiEditorEngine.actions.next()] = editor.innerHTML;
			} else if ( ( charCode == 122 || charCode == 121 || charCode == 90 )
					&& evt.ctrlKey ) {
				// undo and redo
				( charCode == 121 || evt.shiftKey ) ?
					WikiEditorEngine.actions.redo() :
					WikiEditorEngine.actions.undo();
				evt.preventDefault();
			} else if ( keyCode == 86 && evt.ctrlKey ) {
				// paste
				// TODO: pasted text should be parsed and highlighted
			}
		}

		else if ( $.browser.msie ) {
			charCode = evt.keyCode;
			fromChar = String.fromCharCode( charCode );
			
			if ( ( completeEndingChars.indexOf( '|' + fromChar + '|' ) != -1 ||
					completeChars.indexOf( '|' + fromChar + '|' ) != -1 ) &&
					WikiEditorEngine.autocomplete ) {
				// autocomplete
				if ( !WikiEditorEngine.completeEnding( fromChar ) )
					WikiEditorEngine.complete( fromChar );
			} else if ( chars.indexOf( '|' + charCode + '|' ) != -1 || charCode == 13 ) {
				// syntax highlighting
				WikiEditorEngine.syntaxHighlight( 'generic' );
			}
		}
	},
	
	// IE SPECIFIC FN
	metaHandler : function( evt ) {
			keyCode = evt.keyCode;
			
			if ( keyCode == 9 || evt.tabKey ) {
				WikiEditorEngine.snippets();
			} else if ( ( keyCode == 122 || keyCode == 121 || keyCode == 90 ) && evt.ctrlKey ) {
				// undo and redo
				( keyCode == 121 || evt.shiftKey ) ?
					WikiEditorEngine.actions.redo() :
					WikiEditorEngine.actions.undo();
				evt.returnValue = false;
			} else if ( keyCode == 34 || keyCode == 33 ) {
				// handle page up/down for IE
				self.scrollBy( 0, ( keyCode == 34 ) ? 200 : -200 );
				evt.returnValue = false;
			} else if ( keyCode == 46 || keyCode == 8 ) {
				// save to history when delete or backspace pressed
				WikiEditorEngine.actions.history[WikiEditorEngine.actions.next()] = editor.innerHTML;
			} else if ( ( evt.ctrlKey || evt.metaKey ) && evt.shiftKey && keyCode != 90 ) {
				// shortcuts = ctrl||appleKey+shift+key!=z(undo)
				WikiEditorEngine.shortcuts( keyCode );
				evt.returnValue = false;
			} else if ( keyCode == 86 && evt.ctrlKey ) {
				// handle paste
				window.clipboardData.setData( 'Text', window.clipboardData.getData(
					'Text' ).replace( /\t/g, '\u2008' ) );
				top.setTimeout( function() {
					WikiEditorEngine.syntaxHighlight( 'paste' );
				}, 10 );
			} else if ( keyCode == 67 && evt.ctrlKey ) {
				// handle cut
				// window.clipboardData.setData('Text',x[0]);
				// code = window.clipboardData.getData('Text');
			}
	},
	
	// put cursor back to its original position after every parsing
	findString : function() {
		if ( gecko ) {
			if ( self.find( cc ) )
				window.getSelection().getRangeAt( 0 ).deleteContents();
		} else if ( $.browser.opera ) {
			var sel = window.getSelection();
			var range = window.document.createRange();
			var span = window.document.getElementsByTagName( 'span' )[0];
			
			range.selectNode( span );
			sel.removeAllRanges();
			sel.addRange( range );
			span.parentNode.removeChild( span );
			// if(self.find(cc))
			// window.getSelection().getRangeAt(0).deleteContents();
		} else if ( $.browser.msie ) {
			range = self.document.body.createTextRange();
			if ( range.findText( cc ) ) {
				range.select();
				range.text = '';
			}
		}
	},
	
	// split big files, highlighting parts of it
	split : function( code, flag ) {
		if ( gecko ) {
			if ( flag == 'scroll' ) {
				this.scrolling = true;
				return code;
			} else {
				this.scrolling = false;
				mid = code.indexOf( cc );
				if ( mid - 2000 < 0 ) {
					ini = 0;
					end = 4000;
				} else if ( mid + 2000 > code.length ) {
					ini = code.length - 4000;
					end = code.length;
				} else {
					ini = mid - 2000;
					end = mid + 2000;
				}
				code = code.substring( ini, end );
				return code;
			}
		} else if ( $.browser.opera ) {
			if ( flag == 'scroll' ) {
				this.scrolling = true;
				return code;
			} else {
				this.scrolling = false;
				mid = code.indexOf( '<SPAN>' );
				if ( mid - 2000 < 0 ) {
					ini = 0;
					end = 4000;
				} else if ( mid + 2000 > code.length ) {
					ini = code.length - 4000;
					end = code.length;
				} else {
					ini = mid - 2000;
					end = mid + 2000;
				}
				code = code.substring( ini, end );
				return code;
			}
		} else if ( $.browser.msie ) {
			if ( flag == 'scroll' ) {
				this.scrolling = true;
				return code;
			} else {
				this.scrolling = false;
				mid = code.indexOf( cc );
				if ( mid - 2000 < 0 ) {
						ini = 0;
						end = 4000;
				} else if ( mid + 2000 > code.length ) {
						ini = code.length - 4000;
						end = code.length;
				} else {
						ini = mid - 2000;
						end = mid + 2000;
				}
				code = code.substring( ini, end );
				return code.substring( code.indexOf( '<P>' ),
					code.lastIndexOf( '</P>' ) + 4 );
			}
		}
	},
	
	// GECKO SPECIFIC
	getEditor : function() {
		if ( !document.getElementsByTagName( 'pre' )[0] ) {
			body = document.getElementsByTagName( 'body' )[0];
			if ( !body.innerHTML )
				return body;
			if ( body.innerHTML == "<br>" )
				body.innerHTML = "<pre> </pre>";
			else
				body.innerHTML = "<pre>" + body.innerHTML + "</pre>";
		}
		return document.getElementsByTagName( 'pre' )[0];
	},
	
	// syntax highlighting parser
	syntaxHighlight : function( flag ) {
		if ( gecko ) {
			if ( flag != 'init' ) {
				window.getSelection().getRangeAt( 0 ).insertNode(
					document.createTextNode( cc ) );
			}
			editor = WikiEditorEngine.getEditor();
			o = editor.innerHTML;
			o = o.replace( /<br>/g, '\n' );
			o = o.replace( /<.*?>/g, '' );
			x = z = this.split( o, flag );
			x = x.replace( /\n/g, '<br>' );
			
			if ( arguments[1] && arguments[2] )
				x = x.replace( arguments[1], arguments[2] );
			
			for ( i = 0; i < Language.syntax.length; i++ )
				x = x.replace( Language.syntax[i].input,
					Language.syntax[i].output );
			
			editor.innerHTML = this.actions.history[this.actions.next()] = ( flag == 'scroll' ) ?
				x : o.split( z ).join( x );
			if ( flag != 'init' )
				this.findString();
		} else if ( $.browser.opera ) {
			if ( flag != 'init' ) {
				var span = document.createElement( 'span' );
				window.getSelection().getRangeAt( 0 ).insertNode( span );
			}
			
			o = editor.innerHTML;
			
			o = o.replace( /<(?!span|\/span|br).*?>/gi, '' );
			x = z = this.split( o, flag );
			x = x.replace( /\t/g, '		' );
			
			if ( arguments[1] && arguments[2] )
				x = x.replace( arguments[1], arguments[2] );
			
			for ( i = 0; i < Language.syntax.length; i++ )
				x = x.replace( Language.syntax[i].input,
					Language.syntax[i].output );
			
			editor.innerHTML = this.actions.history[this.actions.next()] = ( flag == 'scroll' ) ?
				x : o.split( z ).join( x );
			
			if ( flag != 'init' )
				this.findString();
		} else if ( $.browser.msie ) {
			if ( flag != 'init' )
				document.selection.createRange().text = cc;
			o = editor.innerHTML;
			if ( flag == 'paste' ) { // fix pasted text
				o = o.replace( /<BR>/g, '\r\n' );
				o = o.replace( /\u2008/g, '\t' );
			}
			o = o.replace( /<P>/g, '\n' );
			o = o.replace( /<\/P>/g, '\r' );
			o = o.replace( /<.*?>/g, '' );
			o = o.replace( /&nbsp;/g, '' );
			o = '<PRE><P>' + o + '</P></PRE>';
			o = o.replace( /\n\r/g, '<P></P>' );
			o = o.replace( /\n/g, '<P>' );
			o = o.replace( /\r/g, '<\/P>' );
			o = o.replace( /<P>(<P>)+/, '<P>' );
			o = o.replace( /<\/P>(<\/P>)+/, '</P>' );
			o = o.replace( /<P><\/P>/g, '<P><BR/></P>' );
			x = z = this.split( o, flag );
			
			if ( arguments[1] && arguments[2] )
				x = x.replace( arguments[1], arguments[2] );
			
			for ( i = 0; i < Language.syntax.length; i++ )
				x = x.replace( Language.syntax[i].input,
						Language.syntax[i].output );
			
			editor.innerHTML = this.actions.history[this.actions.next()] = ( flag == 'scroll' ) ?
				x : o.replace( z, x );
			if ( flag != 'init' )
				this.findString();
		}
	},
	
	getLastWord : function() {
		var rangeAndCaret = WikiEditorEngine.getRangeAndCaret();
		words = rangeAndCaret[0].substring( rangeAndCaret[1] - 40,
			rangeAndCaret[1] );
		words = words.replace( /[\s\n\r\);\W]/g, '\n' ).split( '\n' );
		return words[words.length - 1].replace( /[\W]/gi, '' ).toLowerCase();
	},
	
	snippets : function( evt ) {
		var snippets = Language.snippets;
		var trigger = this.getLastWord();
		for ( var i = 0; i < snippets.length; i++ ) {
			if ( snippets[i].input == trigger ) {
				var content = snippets[i].output.replace( /</g, '&lt;' );
				content = content.replace( />/g, '&gt;' );
				if ( content.indexOf( '$0' ) < 0 )
						content += cc;
				else
						content = content.replace( /\$0/, cc );
				content = content.replace( /\n/g, '<br>' );
				var pattern = new RegExp( trigger + cc, 'gi' );
				if ( !$.browser.msie ) {
					// prevent the tab key from being added
					evt.preventDefault();
				}
				this.syntaxHighlight( 'snippets', pattern, content );
			}
		}
	},
	
	readOnly : function() {
		if ( $.browser.msie ) {
			editor.contentEditable = ( arguments[0] ) ? 'false' : 'true';
		} else if ( gecko || $.browser.opera ) {
			document.designMode = ( arguments[0] ) ? 'off' : 'on';
		}
	},
	
	complete : function( trigger ) {
		window.getSelection().getRangeAt( 0 ).deleteContents();
		var complete = Language.complete;
		for ( var i = 0; i < complete.length; i++ ) {
			if ( complete[i].input == trigger ) {
				var pattern = new RegExp( '\\' + trigger + cc );
				var content = complete[i].output.replace( /\$0/g, cc );
				parent.setTimeout( function() {
					WikiEditorEngine.syntaxHighlight( 'complete', pattern, content );
				}, 0 ); // wait for char to appear on screen
			}
		}
	},
	
	getCompleteChars : function() {
		var cChars = '';
		for ( var i = 0; i < Language.complete.length; i++ )
			cChars += '|' + Language.complete[i].input;
		return cChars + '|';
	},
	
	// not in Opera
	getCompleteEndingChars : function() {
		var cChars = '';
		for ( var i = 0; i < Language.complete.length; i++ )
			cChars += '|' + Language.complete[i].output
				.charAt( Language.complete[i].output.length - 1 );
		return cChars + '|';
	},
	
	// also not in opera
	completeEnding : function( trigger ) {
		if ( gecko ) {
			var range = window.getSelection().getRangeAt( 0 );
			try {
				range.setEnd( range.endContainer, range.endOffset + 1 )
			} catch ( e ) {
				return false;
			}
			var next_character = range.toString()
			range.setEnd( range.endContainer, range.endOffset - 1 )
			if ( next_character != trigger )
				return false;
			else {
				range.setEnd( range.endContainer, range.endOffset + 1 )
				range.deleteContents();
				return true;
			}
		} else if ( $.browser.msie ) {
			var range = document.selection.createRange();
			try {
				range.moveEnd( 'character', 1 )
			} catch ( e ) {
				return false;
			}
			var next_character = range.text
			range.moveEnd( 'character', -1 )
			if ( next_character != trigger )
				return false;
			else {
				range.moveEnd( 'character', 1 )
				range.text = ''
				return true;
			}
		}
	},
	
	shortcuts : function() {
		var cCode = arguments[0];
		if ( cCode == 13 )
			cCode = '[enter]';
		else if ( cCode == 32 )
			cCode = '[space]';
		else
			cCode = '[' + String.fromCharCode( charCode ).toLowerCase() + ']';
		for ( var i = 0; i < Language.shortcuts.length; i++ )
			if ( Language.shortcuts[i].input == cCode )
				this.insertCode( Language.shortcuts[i].output, false );
	},
	
	getRangeAndCaret : function() {
		if ( $.browser.msie ) {
			var range = document.selection.createRange();
			var caret = Math.abs( range.moveStart( 'character', -1000000 ) + 1 );
			range = this.getCode();
			range = range.replace( /\n\r/gi, '  ' );
			range = range.replace( /\n/gi, '' );
			return [ range.toString(), caret ];
		} else if ( !$.browser.msie ) {
			var range = window.getSelection().getRangeAt( 0 );
			var range2 = range.cloneRange();
			var node = range.endContainer;
			var caret = range.endOffset;
			range2.selectNode( node );
			return [ range2.toString(), caret ];
		}
	},
	
	insertCode : function( code, replaceCursorBefore ) {
		if ( $.browser.msie ) {
			var repdeb = '';
			var repfin = '';
			
			if ( replaceCursorBefore ) {
				repfin = code;
			} else {
				repdeb = code;
			}
			
			if ( typeof document.selection != 'undefined' ) {
				var range = document.selection.createRange();
				range.text = repdeb + repfin;
				range = document.selection.createRange();
				range.move( 'character', -repfin.length );
				range.select();
			}
		} else if ( !$.browser.msie ) {
			var range = window.getSelection().getRangeAt( 0 );
			var node = window.document.createTextNode( code );
			var selct = window.getSelection();
			var range2 = range.cloneRange();
			// Insert text at cursor position
			selct.removeAllRanges();
			range.deleteContents();
			range.insertNode( node );
			// Move the cursor to the end of text
			range2.selectNode( node );
			range2.collapse( replaceCursorBefore );
			selct.removeAllRanges();
			selct.addRange( range2 );
		}
	},
	
	getCode : function() {
		if ( gecko && !document.getElementsByTagName( 'pre' )[0] || editor.innerHTML == '' )
			editor = WikiEditorEngine.getEditor();
		var code = editor.innerHTML;
		code = code.replace( /<br>/g, '\n' );
		code = code.replace( /\u2009/g, '' );
		code = code.replace( /<.*?>/g, '' );
		code = code.replace( /&lt;/g, '<' );
		code = code.replace( /&gt;/g, '>' );
		code = code.replace( /&amp;/gi, '&' );
		
		if ( $.browser.msie ) {
			code = code.replace( /<\/p>/gi, '\r' );
			code = code.replace( /<p>/i, '' ); // IE first line fix
			code = code.replace( /<p>/gi, '\n' );
			code = code.replace( /&nbsp;/gi, '' );
		}
		return code;
	},
	
	setCode : function() {
		var code = arguments[0];
		code = code.replace( /\u2009/gi, '' );
		code = code.replace( /&/gi, '&amp;' );
		code = code.replace( /</g, '&lt;' );
		code = code.replace( />/g, '&gt;' );
		if ( $.browser.msie ) {
			editor.innerHTML = '<pre>' + code + '</pre>';
		} else if ( !$.browser.msie ) {
			editor.innerHTML = code;
		}
		if ( gecko && code == '' ) {
			document.getElementsByTagName( 'body' )[0].innerHTML = '';
		}
	},
	
	// g
	actions : {
		pos : -1, // actual history position
		history : [], // history vector
		
		undo : function() {
			if ( gecko ) {
				editor = WikiEditorEngine.getEditor();
			}
			if ( editor.innerHTML.indexOf( cc ) == -1 ) {
				if ( opera || ( gecko && editor.innerHTML != " " ) )
					window.getSelection().getRangeAt( 0 ).insertNode(
						document.createTextNode( cc ) );
				if ( $.browser.msie ) {
					document.selection.createRange().text = cc;
				}
				this.history[this.pos] = editor.innerHTML;
			}
			this.pos--;
			if ( typeof ( this.history[this.pos] ) == 'undefined' )
				this.pos++; // in gecko it was pos ++ with a space. necessary?
			editor.innerHTML = this.history[this.pos];
			if ( gecko && editor.innerHTML.indexOf( cc ) > -1 )
				editor.innerHTML += cc;
			WikiEditorEngine.findString();
		},
		
		redo : function() {
			// editor = WikiEditorEngine.getEditor();
			this.pos++;
			if ( typeof ( this.history[this.pos] ) == 'undefined' )
				this.pos--;
			editor.innerHTML = this.history[this.pos];
			WikiEditorEngine.findString();
		},
		
		next : function() { // get next vector position and clean old ones
			if ( this.pos > 20 )
				this.history[this.pos - 21] = undefined;
			return ++this.pos;
		}
	}
}
// end WikiEditorEngine obj

Language = {};
if ( $.browser.msie ) {
	window.attachEvent( 'onload', function() {
		WikiEditorEngine.initialize( 'new' );
	} );
} else if ( !$.browser.msie ) {
	window.addEventListener( 'load', function() {
		WikiEditorEngine.initialize( 'new' );
	}, true );
}
