( function( $ ) {
	$.ArticleAssessment = {
		'config': { 
			'authtoken': '',
			'userID': '',
			'pageID': wgArticleId,
			'revID': wgCurRevisionId
		},
		'messages': {},
		'settings': {
			'endpoint': wgScriptPath + '/api.php?',
			'fieldMessages' : [
				'wellsourced',
				'neutrality',
				'completeness',
				'readability'
			],
			'fieldHintSuffix': '-tooltip',
			'fieldPrefix': 'articleassessment-rating-',
			'fieldHTML': '<div class="field-wrapper"> \
				<label class="rating-field-label"></label> \
				<select class="rating-field"> \
					<option value="1">1</option> \
					<option value="2">2</option> \
					<option value="3">3</option> \
					<option value="4">4</option> \
					<option value="5">5</option> \
				</select> \
			</div>',
			'structureHTML': '<div class="article-assessment-wrapper nonopopups"> \
				<form action="rate" method="post" id="article-assessment"> \
					<fieldset id="article-assessment-rate"> \
						<legend></legend> \
						<div class="article-assessment-information"> \
							<span class="article-assessment-rate-instructions"></span> \
							<span class="article-assessment-rate-feedback"></span> \
						</div> \
						<div class="article-assessment-rating-fields"></div> \
						<div class="article-assessment-submit"> \
							<input type="submit" value="Submit" /> \
						</div> \
					</fieldset> \
					<fieldset id="article-assessment-ratings"> \
						<legend></legend> \
						<div class="article-assessment-information"> \
							<span class="article-assessment-show-ratings"></span> \
							<span class="article-assessment-hide-ratings"></span> \
						</div> \
					</fieldset> \
				</form> \
			</div>',
			'ratingHTML': '<div class="article-assessment-rating"> \
					<span class="article-assessment-rating-field-name"></span> \
					<span class="article-assessment-rating-field-value-wrapper"> \
						<span class="article-assessment-rating-field-value">0%</span> \
					</span> \
					<span class="article-assessment-rating-count"></span> \
				</div>'
		},
		
		'fn' : {
			'init': function( $$options ) {
				// merge options with the config
				var settings = $.extend( {}, $.ArticleAssessment.settings, $$options );
				var config = $.ArticleAssessment.config;
				// if this is an anon user, get a unique identifier for them
				// load up the stored ratings and update the markup if the cookie exists
				var userToken = $.cookie( 'mwArticleAssessmentUserToken' );
				if ( typeof userToken == 'undefined' || userToken == null ) {
					function randomString( string_length ) {
						var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
						var randomstring = '';
						for ( var i = 0; i < string_length; i++ ) {
							var rnum = Math.floor( Math.random() * chars.length );
							randomstring += chars.substring( rnum, rnum + 1 );
						}
						return randomstring;
					}
					userToken = randomString( 32 );
					$.cookie( 'mwArticleAssessmentUserToken', userToken, { 'expires': 30, 'path': '/' } );
				}
				if ( !wgUserName ) {
					config.userID = userToken;
				}
				// setup our markup using the template variables in settings 
				var $structure = $( settings.structureHTML ),
					instructions = $.ArticleAssessment.fn.getMsg( 'articleassessment-pleaserate' ),
					feedback = $.ArticleAssessment.fn.getMsg( 'articleassessment-featurefeedback' ),
					yourfeedback = $.ArticleAssessment.fn.getMsg( 'articleassessment-yourfeedback'),
					articlerating = $.ArticleAssessment.fn.getMsg( 'articleassessment-articlerating' ),
					resultshide = $.ArticleAssessment.fn.getMsg( 'articleassessment-results-hide' ),
					resultsshow = $.ArticleAssessment.fn.getMsg( 'articleassessment-results-show' );
					submitbutton = $.ArticleAssessment.fn.getMsg( 'articleassessment-submit' );
				$structure
					.find( '#article-assessment-rate legend' )
						.html( yourfeedback )
						.end()
					.find( '.article-assessment-rate-instructions' )
						.html( instructions )
						.end()
					.find( '.article-assessment-rate-feedback' )
						.html( feedback )
							.find( '.feedbacklink' )
							.wrap( '<a href="#"></a>' )
								.parent()
									.click( $.ArticleAssessment.fn.showFeedback )
								.end()
							.end()
						.end()
					.find( '#article-assessment-ratings legend' )
						.html( articlerating )
						.end()
					.find( '.article-assessment-show-ratings' )
						.html( resultsshow )
							.find( '.showlink' )
							.wrap( '<a href="#"></a>' )
								.parent()
									.click( $.ArticleAssessment.fn.showRatings )
								.end()
							.end()
						.end()
					.find( '.article-assessment-hide-ratings' )
						.html( resultshide )
							.find ( '.hidelink' )
							.wrap( '<a href="#"></a>' )
								.parent()
									.click( $.ArticleAssessment.fn.hideRatings )
								.end()
							.end()
						.end()
					.find( '.article-assessment-submit input' )
						.val( submitbutton )
					.end();
				// hide the feedback link if we need to
				if( $.cookie( 'mwArticleAssessmentHideFeedback' ) ) {
					$structure
						.find( '.article-assessment-rate-feedback' )
						.hide();
				}
				for ( var i = 0; i < settings.fieldMessages.length; i++ ) { 
					var $field = $( settings.fieldHTML ),
						$rating = $( settings.ratingHTML ),
						label = $.ArticleAssessment.fn.getMsg( settings.fieldPrefix + settings.fieldMessages[i] ),
						field = settings.fieldMessages[i],
						hint = $.ArticleAssessment.fn.getMsg( settings.fieldPrefix + settings.fieldMessages[i] + settings.fieldHintSuffix ),
						count = $.ArticleAssessment.fn.getMsg( 'articleassessment-noratings', [0, 0] );
					// initialize the field html
					$field
						.attr( 'id', 'articleassessment-rate-' + field )
						.find( 'label' )
							.attr( 'for', 'rating_' + field )
							.attr( 'original-title', hint )
							.html( label )
							.end()
						.find( 'select' )
							.attr( 'id', 'rating_' + field )
							.attr( 'name', 'rating[' + field + ']' );
					// initialize the rating html
					$rating
						.attr( 'id',  'articleassessment-rating-' + field )
						.find( '.article-assessment-rating-field-name' )
							.html( label )
							.end()
						.find( '.article-assessment-rating-count' )
							.html( count );
					// append the field and rating html
					$structure
						.find( '.article-assessment-rating-fields' )
							.append( $field )
							.end()
						.find( '#article-assessment-ratings' )
							.append( $rating );
				}
				// store our settings and configuration for later
				$structure.find( '#article-assessment' ).data( 'articleAssessment-context', { 'settings': settings, 'config': config } );
				$( '#catlinks' ).before( $structure );
				// Hide the ratings initially
				$.ArticleAssessment.fn.hideRatings();

				
				// set the height of our smaller fieldset to match the taller
				if ( $( '#article-assessment-rate' ).height() > $( '#article-assessment-ratings' ).height() ) {
					$( '#article-assessment-ratings' ).css( 'minHeight',	$( '#article-assessment-rate' ).height() );
				} else {
					$( '#article-assessment-rate' ).css( 'minHeight',	$( '#article-assessment-ratings' ).height() );
				}
				// attempt to fetch the ratings 
				$.ArticleAssessment.fn.getRatingData();
				
				// initialize the star plugin 
				$( '.rating-field' ).each( function() {
					$( this )
						.wrapAll( '<div class="rating-field"></div>' )
						.parent()
						.stars( { 
							inputType: 'select', 
							callback: function( value, link ) {
								// remove any stale or rated classes
								value.$stars.each( function() {
									$( this )
										.removeClass( 'ui-stars-star-stale' )
										.removeClass( 'ui-stars-star-rated' );
									// enable our submit button if it's still disabled
									$( '#article-assessment input:disabled' ).removeAttr( 'disabled' ); 
								} );
							}
						 } );
				});
				// intialize the tooltips
				$( '.field-wrapper label[original-title]' ).each( function() {
					$( this )
						.after( $( '<span class="rating-field-hint" />' )
							.attr( 'original-title', $( this ).attr( 'original-title' ) )
							.tipsy( { gravity : 'se', opacity: '0.9' } ) );
				} );
				// bind submit event to the form
				$( '#article-assessment' )
					.submit( function() { $.ArticleAssessment.fn.submitRating(); return false; } );
				// prevent the submit button for being active until all ratings are filled out
				$( '#article-assessment input[type=submit]' )
					.attr( 'disabled', 'disabled' );
			},
			'showRatings': function() {
				$( '#article-assessment-ratings' )
					.removeClass( 'article-assessment-ratings-disabled' )
					.find( '.article-assessment-show-ratings' )
					.hide()
					.end()
					.find( '.article-assessment-hide-ratings' )
					.show();
				return false;
			},
			'hideRatings': function() {
				$( '#article-assessment-ratings' )
					.addClass( 'article-assessment-ratings-disabled' )
					.find( '.article-assessment-hide-ratings' )
					.hide()
					.end()
					.find( '.article-assessment-show-ratings' )
					.show();
				return false;

			},
			
			// Request the ratings data for the current article
			'getRatingData': function() {
				var config = $( '#article-assessment' ).data( 'articleAssessment-context' ).config;
				var requestData = {
					'action': 'query',
					'list': 'articleassessment',
					'aapageid': config.pageID,
					'aauserrating': 1,
					'format': 'json'
				}
				if ( config.userID.length == 32 ) {
					requestData.aaanontoken = config.userID;
				}

				var request = $.ajax( {
					url: wgScriptPath + '/api.php',
					data: requestData,
					dataType: 'json',
					success: $.ArticleAssessment.fn.afterGetRatingData,
					error: function( XMLHttpRequest, textStatus, errorThrown ) {
						$.ArticleAssessment.fn.flashNotice( $.ArticleAssessment.fn.getMsg( 'articleassessment-error' ),
							{ 'class': 'article-assessment-error-msg' } );
					}
				} );
			},
			'afterGetRatingData' : function( data ) {
				var settings = $( '#article-assessment' ).data( 'articleAssessment-context' ).settings,
					userHasRated = false;
				// add the correct data to the markup
				if ( typeof data.query != 'undefined' && typeof data.query.articleassessment != 'undefined' &&
						typeof data.query.articleassessment[0] != 'undefined' ) {
					for ( var r in data.query.articleassessment[0].ratings ) {
						var rating = data.query.articleassessment[0].ratings[r],
							$rating = $( '#' + rating.ratingdesc ),
							count = rating.count,
							total = ( rating.total / count ).toFixed( 1 ),
							label = $.ArticleAssessment.fn.getMsg( 'articleassessment-noratings', [total, count] );
						$rating
							.find( '.article-assessment-rating-field-value' )
							.text( total )
							.end()
							.find( '.article-assessment-rating-count' )
							.html( label );
						if( rating.userrating ) {
							userHasRated = true;
							// this user rated. Word. Show them their ratings
							var $rateControl = $( '#' + rating.ratingdesc.replace( 'rating', 'rate' ) + ' .rating-field' );
							$rateControl.stars( 'select', rating.userrating );
						}
					}
					// show the ratings if the user has rated
					if( userHasRated ) {
						$.ArticleAssessment.fn.showRatings();
					}
					// if the rating is more than 5 revisions old, mark it as stale
					if ( typeof data.query.articleassessment[0].stale != 'undefined' ) {
						// add the stale star class to each on star
						$( '.ui-stars-star-on' )
							.addClass( 'ui-stars-star-stale' );
						// add the stale message
						var msg = $.ArticleAssessment.fn.getMsg( 'articleassessment-stalemessage-norevisioncount' );
						$.ArticleAssessment.fn.flashNotice( msg, { 'class': 'article-assessment-stale-msg' } );
					} else {
						// if it's not a stale rating, we want to make the stars blue
						$( '.ui-stars-star-on' ).addClass( 'ui-stars-star-rated' );
					}
				} 
				// initialize the ratings 
				$( '.article-assessment-rating-field-value' ).each( function() {
					$( this )
						.css( {
							'width': 120 - ( 120 * ( parseFloat( $( this ).text() ) / 5 ) ) + 'px'
						} )
				} );
			},
			'submitRating': function() {
				var config = $( '#article-assessment' ).data( 'articleAssessment-context' ).config;
				// clear out the stale message
				$.ArticleAssessment.fn.flashNotice( );
				
				// lock the star inputs & submit
				$( '.rating-field' ).stars( 'disable' );
				$( '#article-assessment input' ).attr( 'disabled', 'disabled' ); 
				// get our results for submitting
				var results = {};
				$( '.rating-field input' ).each( function() {
					// expects the hidden inputs to have names like 'rating[field-name]' which we use to
					// be transparent about what values we're sending to the server
					var fieldName = $( this ).attr( 'name' ).match( /\[([a-zA-Z0-9\-]*)\]/ )[1];
					results[ fieldName ] = $( this ).val();
				} );
				var request = $.ajax( {
					url: wgScriptPath + '/api.php',
					type: 'POST',
					data: {
						'action': 'articleassessment',
						'revid': config.revID,
						'pageid': config.pageID,
						'r1' : results['wellsourced'],
						'r2' : results['neutrality'],
						'r3' : results['completeness'],
						'r4' : results['readability'],
						'anontoken': config.userID,
						'format': 'json'
					},
					dataType: 'json',
					success: $.ArticleAssessment.fn.afterSubmitRating,
					error: function( XMLHttpRequest, textStatus, errorThrown ) {
						$.ArticleAssessment.fn.flashNotice( $.ArticleAssessment.fn.getMsg( 'articleassessment-error' ),
							{ 'class': 'article-assessment-error-msg' } );
					}
				} );
			},
			'afterSubmitRating': function ( data ) {
				// update the ratings 
				$.ArticleAssessment.fn.getRatingData();
				// set the stars to rated status
				$( '.ui-stars-star-on' ).addClass( 'ui-stars-star-rated' );
				// unlock the stars & submit
				$( '.rating-field' ).stars( 'enable' );
				$( '#article-assessment input:disabled' ).removeAttr( 'disabled' ); 
				// update the results
				
				// show the results
				$.ArticleAssessment.fn.showRatings();
				// say thank you
				$.ArticleAssessment.fn.flashNotice( $.ArticleAssessment.fn.getMsg( 'articleassessment-thanks' ),
					{ 'class': 'article-assessment-success-msg' } );
			},
			// places a message on the interface
			'flashNotice': function( text, options ) {
				if ( arguments.length == 0 ) {
					// clear existing messages, but don't add a new one
					$( '#article-assessment .article-assessment-flash' ).remove();
				} else {
					// clear and add a new message
					$( '#article-assessment .article-assessment-flash' ).remove();
					var className = options['class'];
					// create our new message
					$msg = $( '<div />' )
						.addClass( 'article-assessment-flash' )
						.html( text );
					// if the class option was passed, add it
					if( options['class'] ) {
						$msg.addClass( options['class'] );
					}
					// place our new message on the page
					$( '#article-assessment .article-assessment-submit' )
						.append( $msg );
				}
			},
			'showFeedback': function() {
				$.ArticleAssessment.fn.withJUI( function() {
					var $dialogDiv = $( '#article-assessment-dialog' );
					if ( $dialogDiv.size() == 0 ) {
						$dialogDiv = $( '<div id="article-assessment-dialog" class="loading" />' )
							.dialog( {
								width: 600,
								height: 400,
								bgiframe: true,
								autoOpen: true,
								modal: true,
								title: $.ArticleAssessment.fn.getMsg( 'articleassessment-survey-title' ),
								close: function() {
									$( this )
										.dialog( 'option', 'height', 400 )
										.find( '.article-assessment-success-msg, .article-assessment-error-msg' )
										.remove()
										.end()
										.find( 'form' )
										.show();
								}
							} );
						$dialogDiv.load(
							wgScript + '?title=Special:SimpleSurvey&survey=articlerating&raw=1',
							function() {
								$( this ).find( 'form' ).bind( 'submit', $.ArticleAssessment.fn.submitFeedback );
								$( this ).removeClass( 'loading' );
							}
						);
					}
					$dialogDiv.dialog( 'open' );
				} );
				return false;
			},
			'submitFeedback': function() {
				var $dialogDiv = $( '#article-assessment-dialog' );
				$dialogDiv
					.find( 'form' )
					.hide()
					.end()
					.addClass( 'loading' );
				// Submit straight to the special page. Yes, this is a dirty dirty hack
				// Build request from form data
				var formData = {};
				$dialogDiv.find( 'input' ).each( function() {
					var name = $( this ).attr( 'name' );
					if ( name !== '' ) {
						if ( name.substr( -2 ) == '[]' ) {
							var trimmedName = name.substr( 0, name.length - 2 );
							if ( typeof formData[trimmedName] == 'undefined' ) {
								formData[trimmedName] = [];
							}
							formData[trimmedName].push( $( this ).val() );
						} else {
							formData[name] = $( this ).val();
						}
					}
				} );
				formData.title = 'Special:SimpleSurvey';
				
				$.ajax( {
					url: wgScript,
					type: 'POST',
					data: formData,
					dataType: 'html',
					success: function( data ) {
						// This is an evil screenscraping method to determine whether
						// the submission was successful
						var success = $( data ).find( '.simplesurvey-success' ).size() > 0;
						// TODO: Style success-msg, error-msg
						var $msgDiv = $( '<div />' )
							.addClass( success ? 'article-assessment-success-msg' : 'article-assessment-error-msg' )
							.html( $.ArticleAssessment.fn.getMsg( success? 'articleassessment-survey-thanks' : 'articleassessment-error' ) )
							.appendTo( $dialogDiv );
						$dialogDiv.removeClass( 'loading' );
						
						// This is absurdly unnecessary from the looks of it, but it seems this is somehow
						// needed in certain cases.
						$.ArticleAssessment.fn.withJUI( function() {
							$dialogDiv.dialog( 'option', 'height', $msgDiv.height() + 100 )
						} );
						
						if ( success ) {
							// Hide the dialog link
							$( '#article-assessment .article-assessment-rate-feedback' ).hide();
							// set a cookie to keep the dialog link hidden
							$.cookie( 'mwArticleAssessmentHideFeedback', true, { 'expires': 30, 'path': '/' } );
							
						}
					},
					error: function( XMLHttpRequest, textStatus, errorThrown ) {
						// TODO: Duplicates code, factor out, maybe
						var $msgDiv = $( '<div />' )
							.addClass( 'article-assessment-error-msg' )
							.html( $.ArticleAssessment.fn.getMsg( 'articleassessment-error' ) )
							.appendTo( $dialogDiv );
						$dialogDiv.removeClass( 'loading' );
						$.ArticleAssessment.fn.withJUI( function() {
							$dialogDiv.dialog( 'option', 'height', $msgDiv.height() + 100 )
						} );
					}
				} );
				return false;
			},
			'addMessages': function( messages ) {
				for ( var key in messages ) {
					$.ArticleAssessment.messages[key] = messages[key];
				}
			},
			/**
			 * Get a message
			 * FIXME: Parameter expansion is broken in all sorts of edge cases
			 */
			'getMsg': function( key, args ) {
				if ( !( key in $.ArticleAssessment.messages ) ) {
					return '[' + key + ']';
				}
				var msg = $.ArticleAssessment.messages[key];
				if ( typeof args == 'object' || typeof args == 'array' ) {
					for ( var i = 0; i < args.length; i++ ) {
						msg = msg.replace( new RegExp( '\\$' + ( parseInt( i ) + 1 ), 'g' ), args[i] );
					}
				} else if ( typeof args == 'string' || typeof args == 'number' ) {
					msg = msg.replace( /\$1/g, args );
				}
				return msg;
			},
			'withJUI': function( callback ) {
				if ( typeof $.ui == 'undefined' ) {
					$.getScript( wgArticleAssessmentJUIPath, callback );
				} else {
					callback();
				}
			}
		}
	};
	$( document ).ready( function () {
		$.ArticleAssessment.fn.init( );
	} ); //document ready
} )( jQuery );
