( function( $ ) {
	$.ArticleAssessment = {
		'config': { 
			'authtoken': '',
			'userID': '',
			'pageID': wgArticleId,
			'revID': wgCurRevisionId
		},
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
			'ratingHTML': '<div class="article-assessment-rating"> \
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
					.replace( /\{INSTRUCTIONS\}/g, mw.usability.getMsg('articleassessment-pleaserate') )
					.replace( /\{FEEDBACK\}/g,  mw.usability.getMsg('articleassessment-featurefeedback') )
					.replace( /\{YOURFEEDBACK\}/g,  mw.usability.getMsg('articleassessment-yourfeedback') )
					.replace( /\{ARTICLERATING\}/g,  mw.usability.getMsg('articleassessment-articlerating' ) ) 
					.replace( /\{RESULTSHIDE\}/g,  mw.usability.getMsg('articleassessment-results-hide' ) ) 
					.replace( /\{RESULTSSHOW\}/g,  mw.usability.getMsg('articleassessment-results-show' ) ) );
				for( var field in settings.fieldMessages ) { 
					$output.find( '.article-assessment-rating-fields' )
						.append( $( settings.fieldHTML
							.replace( /\{LABEL\}/g, mw.usability.getMsg( settings.fieldPrefix + settings.fieldMessages[field] ) )
							.replace( /\{FIELD\}/g, mw.usability.getMsg( settings.fieldMessages[field] ) )
							.replace( /\{HINT\}/g, mw.usability.getMsg( settings.fieldPrefix + settings.fieldMessages[field] + settings.fieldHintSuffix ) ) ) );
					$output.find( '#article-assessment-ratings' )
						.append( $( settings.ratingHTML
							.replace( /\{LABEL\}/g, mw.usability.getMsg(settings.fieldPrefix + settings.fieldMessages[field]) )
							.replace( /\{VALUE\}/g, '0%' ) 
							.replace( /\{COUNT\}/g, mw.usability.getMsg( 'field-count' ) ) ) 
							);
				}
				
				$( '#catlinks' ).before( $output );
				
				// attempt to fetch the ratings 
				$.ArticleAssessment.fn.getRatingData();
				
				// attempt to fetch the user's past ratings if it looks like they may have rated this article before
				$.ArticleAssessment.fn.getUserRatingData();
				
				// initialize the star plugin 
				$( '.rating-field' ).each( function() {
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
				if( true /* replace with conditional based on returned results of past user ratings */ ) {
					// add the stale star class to each on star
					$( '.ui-stars-star-on' )
						.addClass( 'ui-stars-star-stale' );
					// add the stale message
					$( '.article-assessment-submit' )
						.append( settings.staleMSG.replace( /\{MSG\}/g, mw.usability.getMsg( 'articleassessment-stalemessage-revisioncount' ) ) );
				}
				// intialize the tooltips
				$( '.field-wrapper label[original-title]' ).each(function() {
					$( this )
						.after( $( '<span class="rating-field-hint" />' )
							.attr( 'original-title', $( this ).attr( 'original-title' ) )
							.tipsy( { gravity : 'se', opacity: '0.9',  } ) );
				} );
				// initialize the ratings 
				$( '.article-assessment-rating-field-value' ).each( function() {
					$( this )
						.css( {
							'width': 120 - ( 120 * ( parseInt( $( this ).text() ) / 100 ) ) + "px"
						} )
				} );
				// bind submit event to the form
				
				// prevent the submit button for being active until all ratings are filled out
				
			},
			'getRatingData': function() {
				var request = $j.ajax( {
					url: wgScriptPath + '/api.php',
					data: {
						'action': 'articleassessment',
						'getCumulativeResults': 1, 
						'pageId': wgArticleId,
						'revId': wgCurRevisionId
					},
					dataType: 'json',
					success: function( data ) {
						console.log(data);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log(XMLHttpRequest, textStatus, errorThrown);
					}
				} );
			},
			'getUserRatingData': function() {
				var request = $j.ajax( {
					url: wgScriptPath + '/api.php',
					data: {
						'action': 'articleassessment',
						'getUserResults': 1, 
						'userId': wgUserName || "",
						'pageId': wgArticleId,
						'revId': wgCurRevisionId
					},
					dataType: 'json',
					success: function( data ) {
						console.log(data);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						console.log(XMLHttpRequest, textStatus, errorThrown);
					}
				} );
			},
			'submit': function() {
				// clear out the stale message
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
		console.log(this);
		$.ArticleAssessment.fn.init( { 'endpoint': wgScriptPath + "/api.php" } );
	} ); //document ready
} )( jQuery );