/* TemplateEditor module for wikiEditor */
( function( $ ) { $.wikiEditor.modules.templateEditor = {

/**
 * Event handlers
 */
evt: {
	mark: function( context, event ) {
		// Get references to the markers and tokens from the current context
		var markers = context.modules.highlight.markers;
		var tokenArray = context.modules.highlight.tokenArray;
		// Collect matching level 0 template call boundaries from the tokenArray
		var level = 0;
		
		var tokenIndex = 0;
		while ( tokenIndex < tokenArray.length ){
			while ( tokenIndex < tokenArray.length && tokenArray[tokenIndex].label != 'TEMPLATE_BEGIN' ) {
				tokenIndex++;
			}
			//open template
			if ( tokenIndex < tokenArray.length ) {
				var beginIndex = tokenIndex;
				var endIndex = -1; //no match found
				var openTemplates = 1;
				var templatesMatched = false;
				while ( tokenIndex < tokenArray.length && endIndex == -1 ) {
					tokenIndex++;
					if ( tokenArray[tokenIndex].label == 'TEMPLATE_BEGIN' ) {
						openTemplates++;
					} else if ( tokenArray[tokenIndex].label == 'TEMPLATE_END' ) {
						openTemplates--;
						if ( openTemplates == 0 ) {
							endIndex = tokenIndex;
						} //we can stop looping
					}
				}//while finding template ending
				if ( endIndex != -1 ) {
					markers.push( {
						start: tokenArray[beginIndex].offset,
						end: tokenArray[endIndex].offset,
						needsWrap: function( ca1, ca2 ) {
							return !$( ca1.parentNode ).is( 'div.wikiEditor-template' ) ||
								ca1.previousSibling != null || ca1.nextSibling != null;
						},
						afterWrap: $.wikiEditor.modules.templateEditor.fn.stylize
					} );
				} else { //else this was an unmatched opening
					tokenArray[beginIndex].label = 'TEMPLATE_FALSE_BEGIN';
					tokenIndex = beginIndex;
				}
			}//if opentemplates
		}
	}
},
/**
 * Regular expressions that produce tokens
 */
exp: [
	{ 'regex': /{{/, 'label': "TEMPLATE_BEGIN" },
	{ 'regex': /}}/, 'label': "TEMPLATE_END", 'markAfter': true }
],
/**
 * Internally used functions
 */
fn: {
	/**
	 * Creates template form module within wikieditor
	 * @param context Context object of editor to create module in
	 * @param config Configuration object to create module from
	 */
	create: function( context, config ) {
		// Initialize module within the context
		context.modules.templateEditor = {};
	},
	stylize: function( wrappedTemplate ) {
		$( wrappedTemplate ).each( function() {
			if ( typeof $(this).data( 'model' ) != 'undefined' ) {
				// We have a model, so all this init stuff has already happened
				return;
			}
			// Hide this
			$(this).addClass( 'wikiEditor-nodisplay wikiEditor-template' );
			// Build a model for this
			$(this).data( 'model' , new $.wikiEditor.modules.templateEditor.fn.model( $(this).text() ) );
			var model = $(this).data( 'model' );
			// Expand
			function expandTemplate( $displayDiv ) {
				// Housekeeping
				$displayDiv.removeClass( 'wikiEditor-template-collapsed' );
				$displayDiv.addClass( 'wikiEditor-template-expanded' );
				
				//$displayDiv.text( model.getText() );
				$keyValueTable = $("<table></table>")
					.appendTo( $displayDiv );
				$header_row = $("<tr></tr>")
					.appendTo( $keyValueTable );
				$("<th></th>")
					.attr('colspan', '2');
					.text( model.getName() )
					.appendTo( $header_row );
				for( param in model.getAllParamNames() ){
					$keyVal_row = $("<tr></tr>")
						.appendTo( $keyValueTable );
					$("<td></td>")
						.text( param )
						.appendTo( $keyVal_row );
					
					$("<td></td>")
						.text( model.getValue( param ) )
						.appendTo( $keyVal_row );
				}
			};
			// Collapse
			function collapseTemplate( $displayDiv ) {
				// Housekeeping
				$displayDiv.addClass( 'wikiEditor-template-collapsed' );
				$displayDiv.removeClass( 'wikiEditor-template-expanded' );
				$displayDiv.text( model.getName() );
			};
			// Build the collapsed version of this template
			var $visibleDiv = $( "<div />" ).addClass( 'wikiEditor-noinclude' );
			// Let these two know about each other
			$(this).data( 'display', $visibleDiv );
			$visibleDiv.data( 'wikitext', $(this) );
			$(this).after( $visibleDiv );
			// Add click handler
			$visibleDiv.mousedown( function() {
				// Is collapsed, switch to expand
				if ( $(this).hasClass( 'wikiEditor-template-collapsed' ) ) {
					expandTemplate( $(this) );
				} else {
					collapseTemplate( $(this) );
				}
			});
			collapseTemplate( $visibleDiv );
		});
	},
	/**
	 * Builds a template model from given wikitext representation, allowing object-oriented manipulation of the contents
	 * of the template while preserving whitespace and formatting.
	 * 
	 * @param wikitext String of wikitext content
	 */
	model: function( wikitext ) {
		
		/* Private Functions */
		
		/**
		 * Builds a Param object.
		 * 
		 * @param name
		 * @param value
		 * @param number
		 * @param nameIndex
		 * @param equalsIndex
		 * @param valueIndex
		 */
		function Param( name, value, number, nameIndex, equalsIndex, valueIndex ) {
			this.name = name;
			this.value = value;
			this.number = number;
			this.nameIndex = nameIndex;
			this.equalsIndex = equalsIndex;
			this.valueIndex = valueIndex;
		}
		/**
		 * Builds a Range object.
		 * 
		 * @param begin
		 * @param end
		 */
		function Range( begin, end ) {
			this.begin = begin;
			this.end = end;
		}
		/**
		 * Set 'original' to true if you want the original value irrespective of whether the model's been changed
		 * 
		 * @param name
		 * @param value
		 * @param original
		 */
		function getSetValue( name, value, original ) {
			var valueRange;
			var rangeIndex;
			var retVal;
			if ( isNaN( name ) ) {
				// It's a string!
				if ( typeof paramsByName[name] == 'undefined' ) {
					// Does not exist
					return "";
				}
				rangeIndex = paramsByName[name];
			} else {
				// It's a number!
				rangeIndex = parseInt( name );
			}
			if ( typeof params[rangeIndex]  == 'undefined' ) {
				// Does not exist
				return "";
			}
			valueRange = ranges[params[rangeIndex].valueIndex];
			if ( typeof valueRange.newVal == 'undefined' || original ) {
				// Value unchanged, return original wikitext
				retVal = wikitext.substring( valueRange.begin, valueRange.end );
			} else {
				// New value exists, return new value
				retVal = valueRange.newVal;
			}
			if ( value != null ) {
				ranges[params[rangeIndex].valueIndex].newVal = value;
			}
			return retVal;
		};
		
		/* Public Functions */
		
		/**
		 * Get template name
		 */
		this.getName = function() {
			if( typeof ranges[templateNameIndex].newVal == 'undefined' ) {
				return wikitext.substring( ranges[templateNameIndex].begin, ranges[templateNameIndex].end );
			} else {
				return ranges[templateNameIndex].newVal;
			}
		};
		/**
		 * Set template name (if we want to support this)
		 * 
		 * @param name
		 */
		this.setName = function( name ) {
			ranges[templateNameIndex].newVal = name;
		};
		/**
		 * Set value for a given param name / number
		 * 
		 * @param name
		 * @param value
		 */
		this.setValue = function( name, value ) {
			return getSetValue( name, value, false );
		};
		/**
		 * Get value for a given param name / number
		 * 
		 * @param name
		 */
		this.getValue = function( name ) {
			return getSetValue( name, null, false );
		};
		/**
		 * Get original value of a param
		 * 
		 * @param name
		 */
		this.getOriginalValue = function( name ) {
			return getSetValue( name, null, true );
		};
		/**
		 * Get a list of all param names (numbers for the anonymous ones)
		 */
		this.getAllParamNames = function() {
			return paramsByName;
		};
		/**
		 * Get the initial params
		 */
		this.getAllInitialParams = function(){
			return params;
		}
		/**
		 * Get original template text
		 */
		this.getOriginalText = function() {
			return wikitext;
		};
		/**
		 * Get modified template text
		 */
		this.getText = function() {
			newText = "";
			for ( i = 0 ; i < ranges.length; i++ ) {
				if( typeof ranges[i].newVal == 'undefined' ) {
					newText += wikitext.substring( ranges[i].begin, ranges[i].end );
				} else {
					newText += ranges[i].newVal;
				}
			}
			return newText;
		};
		
		// Whitespace* {{ whitespace* nonwhitespace:
		if ( wikitext.match( /\s*{{\s*\S*:/ ) ) {
			// We have a parser function!
		}
		/*
		 * Take all template-specific characters that are not particular to the template we're looking at, namely {|=},
		 * and convert them into something harmless, in this case 'X'
		 */
		// Get rid of first {{ with whitespace
		var sanatizedStr = wikitext.replace( /{{/, "  " );
		// Replace end
		endBraces = sanatizedStr.match( /}}\s*$/ );
		sanatizedStr = sanatizedStr.substring( 0, endBraces.index ) + "  " +
			sanatizedStr.substring( endBraces.index + 2 );
		// Match the open braces we just found with equivalent closing braces note, works for any level of braces
		while ( sanatizedStr.indexOf( '{{' ) != -1 ) {
			startIndex = sanatizedStr.indexOf( '{{' ) + 1;
			openBraces = 2;
			endIndex = startIndex;
			while ( openBraces > 0 ) {
				var brace = sanatizedStr[++endIndex];
				openBraces += brace == '}' ? -1 : brace == '{' ? 1 : 0;
			}
			sanatizedSegment = sanatizedStr.substring( startIndex,endIndex ).replace( /[{}|=]/g , 'X' );
			sanatizedStr =
				sanatizedStr.substring( 0, startIndex ) + sanatizedSegment + sanatizedStr.substring( endIndex );
		}
		/*
		 * Parse 1 param at a time
		 */
		var ranges = [];
		var params = [];
		var templateNameIndex = 0;
		var doneParsing = false;
		oldDivider = 0;
		divider = sanatizedStr.indexOf( '|', oldDivider );
		if ( divider == -1 ) {
			divider = sanatizedStr.length;
			doneParsing = true;
		}
		nameMatch = wikitext.substring( oldDivider, divider ).match( /[^{\s]+/ );
		if ( nameMatch != undefined ) {
			ranges.push( new Range( oldDivider,nameMatch.index ) ); //whitespace and squiggles upto the name
			templateNameIndex = ranges.push( new Range( nameMatch.index,
				nameMatch.index + nameMatch[0].length ) );
			templateNameIndex--; //push returns 1 less than the array
			ranges[templateNameIndex].old = wikitext.substring( ranges[templateNameIndex].begin,
				ranges[templateNameIndex].end );
		}
		params.push( ranges[templateNameIndex].old ); //put something in params (0)
		/*
		 * Start looping over params
		 */
		var currentParamNumber = 0;
		var valueEndIndex;
		var paramsByName = [];
		while ( !doneParsing ) {
			currentParamNumber++;
			oldDivider = divider;
			divider = sanatizedStr.indexOf( '|', oldDivider + 1 );
			if ( divider == -1 ) {
				divider = sanatizedStr.length;
				doneParsing = true;
			}
			currentField = sanatizedStr.substring( oldDivider+1, divider );
			if ( currentField.indexOf( '=' ) == -1 ) {
				// anonymous field, gets a number
				valueBegin = currentField.match( /\S+/ ); //first nonwhitespace character
				valueBeginIndex = valueBegin.index + oldDivider + 1;
				valueEnd = currentField.match( /[^\s]\s*$/ ); //last nonwhitespace character
				valueEndIndex = valueEnd.index + oldDivider + 2;
				ranges.push( new Range( ranges[ranges.length-1].end,
					valueBeginIndex ) ); //all the chars upto now
				nameIndex = ranges.push( new Range( valueBeginIndex, valueBeginIndex ) ) - 1;
				equalsIndex = ranges.push( new Range( valueBeginIndex, valueBeginIndex ) ) - 1;
				valueIndex = ranges.push( new Range( valueBeginIndex, valueEndIndex ) ) - 1;
				params.push( new Param(
					currentParamNumber,
					wikitext.substring( ranges[valueIndex].begin, ranges[valueIndex].end ),
					currentParamNumber,
					nameIndex,
					equalsIndex,
					valueIndex
				) );
				paramsByName[currentParamNumber] = currentParamNumber;
			} else {
				// There's an equals, could be comment or a value pair
				currentName = currentField.substring( 0, currentField.indexOf( '=' ) );
				// Still offset by oldDivider - first nonwhitespace character
				nameBegin = currentName.match( /\S+/ );
				if ( nameBegin == null ) {
					// This is a comment inside a template call / parser abuse. let's not encourage it
					divider++;
					currentParamNumber--;
					continue;
				}
				nameBeginIndex = nameBegin.index + oldDivider + 1;
				// Last nonwhitespace and non } character
				nameEnd = currentName.match( /[^\s]\s*$/ );
				nameEndIndex = nameEnd.index + oldDivider + 2;
				// All the chars upto now 
				ranges.push( new Range( ranges[ranges.length-1].end, nameBeginIndex ) );
				nameIndex = ranges.push( new Range( nameBeginIndex, nameEndIndex ) ) - 1;
				currentValue = currentField.substring( currentField.indexOf( '=' ) + 1);
				oldDivider += currentField.indexOf( '=' ) + 1;
				// First nonwhitespace character
				valueBegin = currentValue.match( /\S+/ );
				valueBeginIndex = valueBegin.index + oldDivider + 1;
				// Last nonwhitespace and non } character
				valueEnd = currentValue.match( /[^\s]\s*$/ );
				valueEndIndex = valueEnd.index + oldDivider + 2;
				// All the chars upto now
				equalsIndex = ranges.push( new Range( ranges[ranges.length-1].end, valueBeginIndex) ) - 1;
				valueIndex = ranges.push( new Range( valueBeginIndex, valueEndIndex ) ) - 1;
				params.push( new Param(
					wikitext.substring( nameBeginIndex, nameEndIndex ),
					wikitext.substring( valueBeginIndex, valueEndIndex ),
					currentParamNumber,
					nameIndex,
					equalsIndex,
					valueIndex
				) );
				paramsByName[wikitext.substring( nameBeginIndex, nameEndIndex )] = currentParamNumber;
			}
		}
		// The rest of the string
		ranges.push( new Range( valueEndIndex, wikitext.length ) );
		
		// Save vars
		this.ranges = ranges;
		this.wikitext = wikitext;
		this.params = params;
		this.paramsByName = paramsByName;
		this.templateNameIndex = templateNameIndex;
	} // model
}

}; } )( jQuery );
