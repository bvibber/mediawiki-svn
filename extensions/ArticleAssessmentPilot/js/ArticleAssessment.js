( function( $ ) {
	
	/* startRating plugin
	 * 
	 * turns a numeric select input into a star field
	 *
	 */
	// $.fn.starRating = function( $$options ) {
	// 		// return if the function is called on an empty jquery object
	// 		if( !this.length ) return this;
	// 		//merge options into the defaults
	// 		var $settings = $.extend( {}, $.starRating.defaults, $$options );
	// 		// run the initialization on each jquery object		
	// 		this.each( function() {
	// 			$field = $( this );
	// 			// make the markup adjustments required
	// 			$.starRating.fn.init( $field );
	// 		} );
	// 		return this;
	// 	};
	// 	$.starRating = {
	// 		defaults: {
	// 			'fieldHTML': '<div class="star-rating-wrapper"> \
	// 				<input type="hidden" name="{NAME}" /> \
	// 				<div class="star-rating-field"> \
	// 				</div> \
	// 			</div>'
	// 		},
	// 		'fn': {
	// 			'init': function( $field ) {
	// 				var fieldName = $field.attr( 'name' );
	// 				// prep the markup
	// 				var $newField = $( $.starRating.defaults.fieldHTML
	// 						.replace( /\{NAME\}/g, name ) );
	// 				var $starContainer = $newField.find( '.star-rating-field' );
	// 				
	// 				$field.children().each( function() {
	// 					var $opt = $( this );
	// 					$starContainer
	// 						.append( $( '<a href="#"></a>' )
	// 						.text( $opt.text() )
	// 						.click( function() {
	// 							console.log( $opt.val() );
	// 							return false;
	// 						}));
	// 				});
	// 				// bind the click events
	// 				$field
	// 					.replaceWith( $newField );
	// 				// bind the mouseover events
	// 			}
	// 		}
	// 	}
	$.ArticleAssessment = {
		'config': { 
			'endpoint': wgScriptPath + '/api.php?',
			'authtoken': '',
			'userID': '',
			'pageID': '',
			'revID': ''
		},
		'settings': {},
		
		'fn' : {
			'init': function( $$options ) {
				console.log( this );
				// merge options with the config
				var settings = $.extend( {}, $.ArticleAssessment.config, $$options );
				console.log( $.ArticleAssessment.config );
				// load up the stored ratings and update the markup if the cookie exists
				var cookieSettings = $.cookie( 'mwArticleAssessment' );
				if ( cookieSettings == null ) {
					cookieSettings = {
						'ratings': { }
					};
					$.cookie( 'mwArticleAssessment', cookieSettings );
				}
				// initialize the star plugin 
				$( '.rating-field' ).each( function() {
					$( this )
						.wrapAll( '<div class="rating-field-wrapper"></div>' )
						.parent()
						.stars( { inputType: 'select' } );
				});
				// intialize the tooltips
				
				// bind submit event to the form
				
				// prevent the submit button for being active until all ratings are filled out
				
			},
			'submit': function() {
				var request = $j.ajax( {
					url: wgScriptPath + '/api.php',
					data: {
						'action': 'ratearticle',
						'data': ratingsData
					},
					dataType: 'json',
					success: function( data ) {
						$this.suggestions( 'suggestions', data[1] );
					}
				} );
			}
		}
	};
	// FIXME - this should be moved out of here
	$( document ).ready( function () {
		$.ArticleAssessment.fn.init( { 'endpoint': 'hello' });		
	} ); //document ready
} )( jQuery );