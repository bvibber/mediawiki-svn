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
			'fieldHTML': '<div class="field-wrapper" id="articleassessment-rate-{FIELD}"> \
				<label for="rating_{FIELD}" original-title="{HINT}" class="rating-field-label">{LABEL}</label> \
				<select id="rating_{FIELD}" name="rating[{FIELD}]" class="rating-field"> \
					<option value="1">1</option> \
					<option value="2">2</option> \
					<option value="3">3</option> \
					<option value="4">4</option> \
					<option value="5">5</option> \
				</select> \
			</div>',
			'structureHTML': '<div class="article-assessment-wrapper"> \
				<form action="rate" method="post" id="article-assessment"> \
					<fieldset id="article-assessment-rate"> \
						<legend>{YOURFEEDBACK}</legend> \
						<div class="article-assessment-information"> \
							<span class="article-assessment-rate-instructions">{INSTRUCTIONS}</span> \
							<span class="article-assessment-rate-feedback">{FEEDBACK}</span> \
						</div> \
						<div class="article-assessment-rating-fields"></div> \
						<div class="article-assessment-submit"> \
							<input type="submit" value="Submit" /> \
						</div> \
					</fieldset> \
					<fieldset id="article-assessment-ratings"> \
						<legend>{ARTICLERATING}</legend> \
						<div class="article-assessment-information"> \
							<span class="article-assessment-show-ratings">{RESULTSSHOW}</span> \
							<span class="article-assessment-hide-ratings">{RESULTSHIDE}</span> \
						</div> \
					</fieldset> \
				</form> \
			</div>',
			'ratingHTML': '<div class="article-assessment-rating" id="articleassessment-rating-{FIELD}"> \
					<span class="article-assessment-rating-field-name">{LABEL}</span> \
					<span class="article-assessment-rating-field-value-wrapper"> \
						<span class="article-assessment-rating-field-value">{VALUE}</span> \
					</span> \
					<span class="article-assessment-rating-count">{COUNT}</span> \
				</div>',
			'staleMSG': '<span class="article-assessment-stale-msg">{MSG}</span>'
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
						for (var i=0; i<string_length; i++) {
							var rnum = Math.floor(Math.random() * chars.length);
							randomstring += chars.substring(rnum,rnum+1);
						}
						return randomstring;
					}
					userToken = randomString( 32 );
					$.cookie( 'mwArticleAssessmentUserToken', userToken );
				}
				if ( ! wgUserName ) {
					config.userID = userToken;
				}
				// setup our markup using the template varibales in settings 
				var $output = $( settings.structureHTML
					.replace( /\{INSTRUCTIONS\}/g, $.ArticleAssessment.fn.getMsg('articleassessment-pleaserate') )
					.replace( /\{FEEDBACK\}/g,	$.ArticleAssessment.fn.getMsg('articleassessment-featurefeedback')
						.replace( /\[\[([^\|\]]*)\|([^\|\]]*)\]\]/, '<a href="' + wgArticlePath + '">$2</a>' ) )
					.replace( /\{YOURFEEDBACK\}/g,	$.ArticleAssessment.fn.getMsg('articleassessment-yourfeedback') )
					.replace( /\{ARTICLERATING\}/g,	 $.ArticleAssessment.fn.getMsg('articleassessment-articlerating' ) ) 
					.replace( /\{RESULTSHIDE\}/g,	 $.ArticleAssessment.fn.getMsg('articleassessment-results-hide' )
						.replace( /\[\[\|([^\]]*)\]\]/, '<a href="#">$1</a>' ) ) 
					.replace( /\{RESULTSSHOW\}/g,	 $.ArticleAssessment.fn.getMsg('articleassessment-results-show' )
						.replace( /\[\[\|([^\]]*)\]\]/, '<a href="#">$1</a>' ) ) );
				for( var field in settings.fieldMessages ) { 
					$output.find( '.article-assessment-rating-fields' )
						.append( $( settings.fieldHTML
							.replace( /\{LABEL\}/g, $.ArticleAssessment.fn.getMsg( settings.fieldPrefix + settings.fieldMessages[field] ) )
							.replace( /\{FIELD\}/g, settings.fieldMessages[field] )
							.replace( /\{HINT\}/g, $.ArticleAssessment.fn.getMsg( settings.fieldPrefix + settings.fieldMessages[field] + settings.fieldHintSuffix ) ) ) );
					$output.find( '#article-assessment-ratings' )
						.append( $( settings.ratingHTML
							.replace( /\{LABEL\}/g, $.ArticleAssessment.fn.getMsg(settings.fieldPrefix + settings.fieldMessages[field]) )
							.replace( /\{FIELD\}/g, settings.fieldMessages[field] )
							.replace( /\{VALUE\}/g, '0%' ) 
							.replace( /\{COUNT\}/g, $.ArticleAssessment.fn.getMsg( 'articleassessment-noratings', [0, 0] ) ) ) 
							);
				}
				// store our settings and configuration for later
				$output.find( '#article-assessment' ).data( 'articleAssessment-context', { 'settings': settings, 'config': config } );
				// bind the ratings show/hide handlers
				$output
					.find( '.article-assessment-show-ratings a' )
					.click( function() {
						$( this )
							.parent()
							.hide();
						$output
							.find( '#article-assessment-ratings' )
							.removeClass( 'article-assessment-ratings-disabled' )
							.end()
							.find( '.article-assessment-hide-ratings' )
							.show();
							return false;
					} )
					.end()
					.find( '.article-assessment-hide-ratings a' )
					.click( function() {
						$( this )
							.parent()
							.hide();
						$output
							.find( '#article-assessment-ratings' )
							.addClass( 'article-assessment-ratings-disabled' )
							.end()
							.find( '.article-assessment-show-ratings' )
							.show();
							return false;
					} )
					.click();
				$( '#catlinks' ).before( $output );
				
				// set the height of our smaller fieldset to match the taller
				if( $( '#article-assessment-rate' ).height() > $( '#article-assessment-ratings' ).height() ) {
					$( '#article-assessment-ratings' ).css( 'minHeight',	$( '#article-assessment-rate' ).height() );
				} else {
					$( '#article-assessment-rate' ).css( 'minHeight',	 $( '#article-assessment-ratings' ).height() );
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
								$( '#article-assessment input:disabled' ).removeAttr( "disabled" ); 
								} );
							}
						 } );
				});
				// intialize the tooltips
				$( '.field-wrapper label[original-title]' ).each(function() {
					$( this )
						.after( $( '<span class="rating-field-hint" />' )
							.attr( 'original-title', $( this ).attr( 'original-title' ) )
							.tipsy( { gravity : 'se', opacity: '0.9',	 } ) );
				} );
				// bind submit event to the form
				$( '#article-assessment' )
					.submit( function() { $.ArticleAssessment.fn.submitRating(); return false; } );
				// prevent the submit button for being active until all ratings are filled out
				$( '#article-assessment input[type=submit]' )
					.attr( 'disabled', 'disabled' );
			},
			// Request the ratings data for the current article
			'getRatingData': function() {
				var config = $( '#article-assessment' ).data( 'articleAssessment-context' ).config;
				var requestData = {
					'action': 'query',
					'list': 'articleassessment',
					'aarevid': config.revID,
					'aapageid': config.pageID,
					'aauserrating': 1,
					'format': 'json'
				}
				if( config.userID.length == 32 ) {
					requestData.aaanontoken = config.userID;
				}
				var request = $.ajax( {
					url: wgScriptPath + '/api.php',
					data: requestData,
					dataType: 'json',
					success: function( data ) {
						$.ArticleAssessment.fn.afterGetRatingData( data );
					},
					error: function( XMLHttpRequest, textStatus, errorThrown ) {
						$.ArticleAssessment.fn.flashNotice( $.ArticleAssessment.fn.getMsg( 'articleassessment-error' ),
							{ 'class': 'article-assessment-error-msg' } );
					}
				} );
			},
			'afterGetRatingData' : function( data ) {
				var settings = $( '#article-assessment' ).data( 'articleAssessment-context' ).settings;
				// add the correct data to the markup
				if( data.query.articleassessment && data.query.articleassessment.length > 0 ) {
					for( rating in data.query.articleassessment[0].ratings) {
						var rating = data.query.articleassessment[0].ratings[rating],
							$rating = $( '#' + rating.ratingdesc ),
							count = rating.count,
							total = ( rating.total / count ).toFixed( 1 ),
							label = $.ArticleAssessment.fn.getMsg( 'articleassessment-noratings', [total, count] );
						$rating
							.find( '.article-assessment-rating-field-value' )
							.text( total )
							.end()
							.find( '.article-assessment-rating-count' )
							.text( label );
						if( rating.userrating ) {
							var $rateControl = $( '#' + rating.ratingdesc.replace( 'rating', 'rate' ) + ' .rating-field' );
							$rateControl.stars( 'select', rating.userrating );
						}
					}
					// if the rating is stale, add the stale class
					if( data.query.articleassessment.stale ) {
						// add the stale star class to each on star
						$( '.ui-stars-star-on' )
							.addClass( 'ui-stars-star-stale' );
						// add the stale message
						var msg = $.ArticleAssessment.fn.getMsg( 'articleassessment-stalemessage-revisioncount' )
							.replace( /'''([^']*)'''/g, '<strong>$1</strong>' )
							.replace( /''([^']*)''/g, '<em>$1</em>' );
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
							'width': 120 - ( 120 * ( parseFloat( $( this ).text() ) / 5 ) ) + "px"
						} )
				} );
			},
			'submitRating': function() {
				var config = $( '#article-assessment' ).data( 'articleAssessment-context' ).config;
				// clear out the stale message
				$.ArticleAssessment.fn.flashNotice( );
				
				// lock the star inputs & submit
				$( '.rating-field' ).stars( 'disable' );
				$( '#article-assessment input' ).attr( "disabled", "disabled" ); 
				// get our results for submitting
				var results = {};
				$( '.rating-field input' ).each( function() {
					// expects the hidden inputs to have names like 'rating[field-name]' which we use to
					// be transparent about what values we're sending to the server
					var fieldName = $( this ).attr('name').match(/\[([a-zA-Z0-9\-]*)\]/)[1];
					results[ fieldName ] = $( this ).val();
				} );
				var request = $.ajax( {
					url: wgScriptPath + '/api.php',
					data: {
						'action': 'articleassessment',
						'aarevid': config.revID,
						'aapageid': config.pageID,
						'aar1' : results['wellsourced'],
						'aar2' : results['neutrality'],
						'aar3' : results['completeness'],
						'aar4' : results['readability'],
						'aaanontoken': config.userID,
						'format': 'json'
					},
					dataType: 'json',
					success: function( data ) {
						// update the ratings 
						$.ArticleAssessment.fn.getRatingData();
						// set the stars to rated status
						$( '.ui-stars-star-on' ).addClass( 'ui-stars-star-rated' );
						// unlock the stars & submit
						$( '.rating-field' ).stars( 'enable' );
						$( '#article-assessment input:disabled' ).removeAttr( "disabled" ); 
						// update the results
						
						// show the results
						$( '#article-assessment .article-assessment-show-ratings a' ).click();
						// say thank you
						$.ArticleAssessment.fn.flashNotice( $.ArticleAssessment.fn.getMsg( 'articleassessment-thanks' ),
							{ 'class': 'article-assessment-success-msg' } );
					},
					error: function( XMLHttpRequest, textStatus, errorThrown ) {
						$.ArticleAssessment.fn.flashNotice( $.ArticleAssessment.fn.getMsg( 'articleassessment-error' ),
							{ 'class': 'article-assessment-error-msg' } );
					}
				} );
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
					$msg = $( '<span />' )
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
			'addMessages': function( messages ) {
				for ( var key in messages ) {
					$.ArticleAssessment.messages[key] = messages[key];
				}
			},
			/**
			 * Get a message
			 */
			'getMsg': function( key, args ) {
				if ( !( key in $.ArticleAssessment.messages ) ) {
					return '[' + key + ']';
				}
				var msg = $.ArticleAssessment.messages[key];
				if ( typeof args == 'object' || typeof args == 'array' ) {
					for ( var argKey in args ) {
						msg = msg.replace( '\$' + (parseInt( argKey ) + 1), args[argKey] );
					}
				} else if ( typeof args == 'string' || typeof args == 'number' ) {
					msg = msg.replace( '$1', args );
				}
				return msg;
			}
		}
	};
	$( document ).ready( function () {
		$.ArticleAssessment.fn.init( );
	} ); //document ready
} )( jQuery );