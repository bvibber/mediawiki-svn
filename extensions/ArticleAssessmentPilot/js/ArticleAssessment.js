( function( $ ) {
	$.ArticleAssessment = {
		'config': { 
			'authtoken': '',
			'userID': '',
			'pageID': '',
			'revID': ''
		},
		'settings': {
			'endpoint': wgScriptPath + '/api.php?',
			'fieldMessages' : [
			'wellsourced',
			'aneutrality',
			'completeness',
			'readability'
			],
			'fieldHintSuffix': '-tooltip',
			'fieldPrefix': 'articleassessment-rating-',
			'fieldHTML': '<div class="field-wrapper"> \
				<label for="rating_{FIELD}" original-title="{HINT}" class="rating-field-label">{LABEL}</label> \
				<select id="rating_{FIELD}" name="rating[{FIELD}]" class="rating-field"> \
					<option value="1">1</option> \
					<option value="2">2</option> \
					<option value="3">3</option> \
					<option value="4" selected>4</option> \
					<option value="5">5</option> \
				</select> \
			</div>',
			'structureHTML': '<div class="article-assessment-wrapper"> \
				<form action="rate" method="post" id="article-assessment"> \
					<fieldset id="article-assessment-rate"> \
						<legend>{YOURFEEDBACK}</legend> \
						<span class="article-assessment-rate-instructions">{INSTRUCTIONS}</span> \
						<span class="article-assessment-rate-feedback">{FEEDBACK}</span> \
						<div class="article-assessment-rating-fields"></div> \
						<div class="article-assessment-submit"> \
							<input type="submit" value="Submit" /> \
						</div> \
					</fieldset> \
					<fieldset id="article-assessment-ratings"> \
						<legend>{ARTICLERATING}</legend> \
					</fieldset> \
				</form> \
			</div>',
			'ratingHTML': '<div class="article-assessment-rating"> \
					<span class="article-assessment-rating-field-name">{LABEL}</span> \
					<span class="article-assessment-rating-field-value">{VALUE}</span> \
					<span class="article-assessment-rating-count">{COUNT}</span> \
				</div>',
			'staleMSG': '<span class="article-assessment-stale-msg">{MSG}</span>'
		},
		
		'fn' : {
			'init': function( $$options ) {
				// merge options with the config
				var settings = $.extend( {}, $.ArticleAssessment.settings, $$options );
				// load up the stored ratings and update the markup if the cookie exists
				var cookieSettings = $.cookie( 'mwArticleAssessment' );
				if ( cookieSettings == null ) {
					cookieSettings = {
						'ratings': { }
					};
					$.cookie( 'mwArticleAssessment', cookieSettings );
				}
				// setup our markup
				var $output = $( settings.structureHTML
					.replace( /\{INSTRUCTIONS\}/g, 'articleassessment-pleaserate' )
					.replace( /\{FEEDBACK\}/g,  'articleassessment-yourfeedback' )
					.replace( /\{YOURFEEDBACK\}/g,  'articleassessment-featurefeedback' )
					.replace( /\{ARTICLERATING\}/g,  'articleassessment-articlerating' ) );
				for( var field in settings.fieldMessages ) { 
					$output.find( '.article-assessment-rating-fields' )
						.append( $( settings.fieldHTML
							.replace( /\{LABEL\}/g, settings.fieldPrefix + settings.fieldMessages[field] )
							.replace( /\{FIELD\}/g, settings.fieldMessages[field] )
							.replace( /\{HINT\}/g, settings.fieldPrefix + settings.fieldMessages[field] + settings.fieldHintSuffix ) ) );
					$output.find( '#article-assessment-ratings' )
						.append( $( settings.ratingHTML
							.replace( /\{LABEL\}/g, settings.fieldPrefix + settings.fieldMessages[field] )
							.replace( /\{VALUE\}/g, '0%' ) 
							.replace( /\{COUNT\}/g, 'field-count' ) ) 
							);
				}
				
				$( '#catlinks' ).before( $output );
				
				// initialize the star plugin 
				$( '.rating-field' ).each( function() {
					console.log(this);
					$( this )
						.wrapAll( '<div class="rating-field"></div>' )
						.parent()
						.stars( { 
							inputType: 'select', 
							callback: function( value, link ) {
								// remove any stale classes
								value.$stars.each( function() {
									$( this ).removeClass( 'ui-stars-star-stale' );
								} );
							}
						 } );
				});
				// if the rating is stale, add the stale class
				if( true /* replace with conditional */ ) {
					// add the stale star class to each on star
					$( '.ui-stars-star-on' )
						.addClass( 'ui-stars-star-stale' );
					// add the stale message
					$( '.article-assessment-submit' )
						.append( settings.staleMSG.replace( /\{MSG\}/g, 'articleassessment-stalemessage-revisioncount' ) );
				}
				// intialize the tooltips
				$( '.field-wrapper label[original-title]' ).each(function() {
					$( this )
						.after( $( '<span class="rating-field-hint" />' )
							.attr( 'original-title', $( this ).attr( 'original-title' ) )
							.tipsy( { gravity : 'se', opacity: '0.9' } ) );
				} );
				// initialize the ratings 
				
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
		mw.usability.load( [ '$j.ui' ], function() {
			$.getScript( wgScriptPath + '/extensions/ArticleAssessmentPilot/js/jquery.ui.stars.js', function() {
				$.ArticleAssessment.fn.init( { 'endpoint': wgScriptPath + "/api.php" } );
			} );
		} );
	} ); //document ready
} )( jQuery );