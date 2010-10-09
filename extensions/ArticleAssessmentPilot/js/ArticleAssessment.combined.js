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
				if( $.cookie( 'mwArticleAssessmentHideFeedbackLink' ) ) {
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
				$dialogDiv.find( 'input[type=text], input[type=radio]:checked, input[type=checkbox]:checked, ' +
						'input[type=hidden], textarea' ).each( function() {
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
							$.cookie( 'mwArticleAssessmentHideFeedbackLink', true, { 'expires': 30, 'path': '/' } );
							
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
/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *			 used when the cookie was set.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *														 If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *														 If set to null or omitted, the cookie will be a session cookie and will not be retained
 *														 when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *												require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function(name, value, options) {
		if (typeof value != 'undefined') { // name and value given, set cookie
				options = options || {};
				if (value === null) {
						value = '';
						options.expires = -1;
				}
				var expires = '';
				if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
						var date;
						if (typeof options.expires == 'number') {
								date = new Date();
								date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
						} else {
								date = options.expires;
						}
						expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
				}
				// CAUTION: Needed to parenthesize options.path and options.domain
				// in the following expressions, otherwise they evaluate to undefined
				// in the packed version for some reason...
				var path = options.path ? '; path=' + (options.path) : '';
				var domain = options.domain ? '; domain=' + (options.domain) : '';
				var secure = options.secure ? '; secure' : '';
				document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
		} else { // only name given, get cookie
				var cookieValue = null;
				if (document.cookie && document.cookie != '') {
						var cookies = document.cookie.split(';');
						for (var i = 0; i < cookies.length; i++) {
								var cookie = jQuery.trim(cookies[i]);
								// Does this cookie string begin with the name we want?
								if (cookie.substring(0, name.length + 1) == (name + '=')) {
										cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
										break;
								}
						}
				}
				return cookieValue;
		}
};

/*!
 * jQuery Stars v1
 * adapted by Adam Miller (acm6603@gmail.com)
 * 
 * Adapted from jQuery UI Stars v3.0.1
 * Marek "Orkan" Zajac (orkans@gmail.com)
 * http://plugins.jquery.com/project/Star_Rating_widget
 *
 */
(function($) {
$.stars = {
	defaults :  {
		inputType: 'radio', // [radio|select]
		split: 0, // decrease number of stars by splitting each star into pieces [2|3|4|...]
		disabled: false, // set to [true] to make the stars initially disabled
		cancelTitle: 'Cancel Rating',
		cancelValue: 0, // default value of Cancel btn.
		cancelShow: true,
		disableValue: true, // set to [false] to not disable the hidden input when Cancel btn is clicked, so the value will present in POST data.
		oneVoteOnly: false,
		showTitles: false,
		captionEl: null, // jQuery object - target for text captions 
		callback: null, // function(ui, type, value, event)
		/*
		 * CSS classes
		 */
		starWidth: 16, // width of the star image
		cancelClass: 'ui-stars-cancel',
		starClass: 'ui-stars-star',
		starOnClass: 'ui-stars-star-on',
		starHoverClass: 'ui-stars-star-hover',
		starDisabledClass: 'ui-stars-star-disabled',
		cancelHoverClass: 'ui-stars-cancel-hover',
		cancelDisabledClass: 'ui-stars-cancel-disabled'
	},
	create: function( ) {
		var self = this, o = this.options, starId = 0;
		this.element.data('former.stars', this.element.html());
		o.isSelect = o.inputType == 'select';
		this.$form = $(this.element).closest('form');
		this.$selec = o.isSelect ? $('select', this.element)  : null;
		this.$rboxs = o.isSelect ? $('option', this.$selec)   : $(':radio', this.element);
		/*
		 * Map all inputs from $rboxs array to Stars elements
		 */
		this.$stars = this.$rboxs.map(function(i)
		{
			var el = {
				value:      this.value,
				title:      (o.isSelect ? this.text : this.title) || this.value,
				isDefault:  (o.isSelect && this.defaultSelected) || this.defaultChecked
			};
			if(i==0) {
				o.split = typeof o.split != 'number' ? 0 : o.split;
				o.val2id = [];
				o.id2val = [];
				o.id2title = [];
				o.name = o.isSelect ? self.$selec.get(0).name : this.name;
				o.disabled = o.disabled || (o.isSelect ? $(self.$selec).attr('disabled') : $(this).attr('disabled'));
			}
			/*
			 * Consider it as a Cancel button?
			 */
			if(el.value == o.cancelValue) {
				o.cancelTitle = el.title;
				return null;
			}
			o.val2id[el.value] = starId;
			o.id2val[starId] = el.value;
			o.id2title[starId] = el.title;
			if(el.isDefault) {
				o.checked = starId;
				o.value = o.defaultValue = el.value;
				o.title = el.title;
			}
			var $s = $('<div/>').addClass(o.starClass);
			var $a = $('<a/>').attr('title', o.showTitles ? el.title : '').text(el.value);
			/*
			 * Prepare division settings
			 */
			if(o.split) {
				var oddeven = (starId % o.split);
				var stwidth = Math.floor(o.starWidth / o.split);
				$s.width(stwidth);
				$a.css('margin-left', '-' + (oddeven * stwidth) + 'px');
			}
			starId++;
			return $s.append($a).get(0);
		});
		/*
		 * How many Stars?
		 */
		o.items = starId;
		/*
		 * Remove old content
		 */
		o.isSelect ? this.$selec.remove() : this.$rboxs.remove();
		/*
		 * Append Stars interface
		 */
		this.$cancel = $('<div/>').addClass(o.cancelClass).append( $('<a/>').attr('title', o.showTitles ? o.cancelTitle : '').text(o.cancelValue) );
		o.cancelShow &= !o.disabled && !o.oneVoteOnly;
		o.cancelShow && this.element.append(this.$cancel);
		this.element.append(this.$stars);
		/*
		 * Initial selection
		 */
		if(o.checked === undefined) {
			o.checked = -1;
			o.value = o.defaultValue = o.cancelValue;
			o.title = '';
		}
		/*
		 * The only FORM element, that has been linked to the stars control. The value field is updated on each Star click event
		 */
		this.$value = $("<input type='hidden' name='"+o.name+"' value='"+o.value+"' />");
		this.element.append(this.$value);
		/*
		 * Attach stars event handler
		 */
		this.$stars.bind('click.stars', function(e) {
			if(!o.forceSelect && o.disabled) return false;
			var i = self.$stars.index(this);
			o.checked = i;
			o.value = o.id2val[i];
			o.title = o.id2title[i];
			self.$value.attr({disabled: o.disabled ? 'disabled' : '', value: o.value});
			fillTo(i, false);
			self.disableCancel();
			!o.forceSelect && self.callback(e, 'star');
		})
		.bind('mouseover.stars', function() {
			if(o.disabled) return false;
			var i = self.$stars.index(this);
			fillTo(i, true);
		})
		.bind('mouseout.stars', function() {
			if(o.disabled) return false;
			fillTo(self.options.checked, false);
		});
		/*
		 * Attach cancel event handler
		 */
		this.$cancel.bind('click.stars', function(e) {
			if(!o.forceSelect && (o.disabled || o.value == o.cancelValue)) return false;
			o.checked = -1;
			o.value = o.cancelValue;
			o.title = '';
			self.$value.val(o.value);
			o.disableValue && self.$value.attr({disabled: 'disabled'});
			fillNone();
			self.disableCancel();
			!o.forceSelect && self.callback(e, 'cancel');
		})
		.bind('mouseover.stars', function() {
			if(self.disableCancel()) return false;
			self.$cancel.addClass(o.cancelHoverClass);
			fillNone();
			self.showCap(o.cancelTitle);
		})
		.bind('mouseout.stars', function() {
			if(self.disableCancel()) return false;
			self.$cancel.removeClass(o.cancelHoverClass);
			self.$stars.triggerHandler('mouseout.stars');
		});
		/*
		 * Attach onReset event handler to the parent FORM
		 */
		this.$form.bind('reset.stars', function(){
			!o.disabled && self.select(o.defaultValue);
		});
		/*
		 * Clean up to avoid memory leaks in certain versions of IE 6
		 */
		// CHANGE: Only do this in IE, so as not to break bfcache in Firefox --catrope
		if ( window.attachEvent && !window.addEventListener ) {
			$(window).unload(function(){
				self.$cancel.unbind('.stars');
				self.$stars.unbind('.stars');
				self.$form.unbind('.stars');
				self.$selec = self.$rboxs = self.$stars = self.$value = self.$cancel = self.$form = null;
			});
		}
		/*
		 * Star selection helpers
		 */
		function fillTo(index, hover) {
			if(index != -1) {
				var addClass = hover ? o.starHoverClass : o.starOnClass;
				var remClass = hover ? o.starOnClass    : o.starHoverClass;
				self.$stars.eq(index).prevAll('.' + o.starClass).andSelf().removeClass(remClass).addClass(addClass);
				self.$stars.eq(index).nextAll('.' + o.starClass).removeClass(o.starHoverClass + ' ' + o.starOnClass);
				self.showCap(o.id2title[index]);
			}
			else fillNone();
		};
		function fillNone() {
			self.$stars.removeClass(o.starOnClass + ' ' + o.starHoverClass);
			self.showCap('');
		};
		/*
		 * Finally, set up the Stars
		 */
		this.select( o.value );
		o.disabled && this.disable();
	},
	/*
	 * Private functions
	 */
	disableCancel: function() {
		var o = this.options, disabled = o.disabled || o.oneVoteOnly || (o.value == o.cancelValue);
		if(disabled)  this.$cancel.removeClass(o.cancelHoverClass).addClass(o.cancelDisabledClass);
		else          this.$cancel.removeClass(o.cancelDisabledClass);
		this.$cancel.css('opacity', disabled ? 0.5 : 1);
		return disabled;
	},
	disableAll: function() {
		var o = this.options;
		this.disableCancel();
		if(o.disabled)  this.$stars.filter('div').addClass(o.starDisabledClass);
		else            this.$stars.filter('div').removeClass(o.starDisabledClass);
	},
	showCap: function(s) {
		var o = this.options;
		if(o.captionEl) o.captionEl.text(s);
	},
	/*
	 * Public functions
	 */
	value: function() {
		return this.options.value;
	},
	select: function( val ) {
		var o = this.options, e = (val == o.cancelValue) ? this.$cancel : this.$stars.eq(o.val2id[val]);
		o.forceSelect = true;
		e.triggerHandler('click.stars');
		o.forceSelect = false;
	},
	selectID: function(id) {
		var o = this.options, e = (id == -1) ? this.$cancel : this.$stars.eq(id);
		o.forceSelect = true;
		e.triggerHandler('click.stars');
		o.forceSelect = false;
	},
	enable: function() {
		this.options.disabled = false;
		this.disableAll();
	},
	disable: function() {
		this.options.disabled = true;
		this.disableAll();
	},
	destroy: function() {
		this.$form.unbind('.stars');
		this.$cancel.unbind('.stars').remove();
		this.$stars.unbind('.stars').remove();
		this.$value.remove();
		this.element.unbind('.stars').html(this.element.data('former.stars')).removeData('stars');
		return this;
	},
	callback: function(e, type) {
		var o = this.options;
		o.callback && o.callback(this, type, o.value, e);
		o.oneVoteOnly && !o.disabled && this.disable();
	}
}
$.fn.stars = function ( ) {
	// convert the arguments to an array
	var args = Array.prototype.slice.call(arguments);
	// default value to return -- overwritten by api calls
	var out = $( this );
	$( this ).each( function() {
		// get the context if it's already been initialized
		var context = $( this ).data( 'stars-context' );
		if ( typeof context == 'undefined' || context == null ) {
			// setup the context if it hasn't been yet
			context = $.extend( {}, {
				element: $( this ),
				options: $.stars.defaults
			}, $.stars );
		}
		// Handle various calling styles
		if ( args.length > 0 ) {
			if ( typeof args[0] == 'object' ) {
				// merge the passed options into defaults
				context.options = $.extend( {}, context.options, args[0] );
				// initialize
				$.stars.create.call( context );
			} else if ( typeof args[0] == 'string' ) {
				// API call 
				var funcName = args[0];
				// call the function, and if it returns something, store the output in our return var
				out = $.stars[funcName].call( context, args.slice(1) ) || out;
			}
		} else {
			// initialize with the defaults
			$.stars.create.call( context );
		}
		// save our context, bay-bee
		$( this ).data( 'stars-context', context );
	} );
	
	return out;
};
} )( jQuery );
// tipsy, facebook style tooltips for jquery
// version 1.0.0a
// (c) 2008-2010 jason frame [jason@onehackoranother.com]
// released under the MIT license

(function($) {
		
		function Tipsy(element, options) {
				this.$element = $(element);
				this.options = options;
				this.enabled = true;
				this.fixTitle();
		}
		
		Tipsy.prototype = {
				show: function() {
						var title = this.getTitle();
						if (title && this.enabled) {
								var $tip = this.tip();
								
								$tip.find('.tipsy-inner')[this.options.html ? 'html' : 'text'](title);
								$tip[0].className = 'tipsy'; // reset classname in case of dynamic gravity
								$tip.remove().css({top: 0, left: 0, visibility: 'hidden', display: 'block'}).appendTo(document.body);
								
								var pos = $.extend({}, this.$element.offset(), {
										width: this.$element[0].offsetWidth,
										height: this.$element[0].offsetHeight
								});
								
								var actualWidth = $tip[0].offsetWidth, actualHeight = $tip[0].offsetHeight;
								var gravity = (typeof this.options.gravity == 'function')
																? this.options.gravity.call(this.$element[0])
																: this.options.gravity;
								
								var tp;
								switch (gravity.charAt(0)) {
										case 'n':
												tp = {top: pos.top + pos.height + this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
												break;
										case 's':
												tp = {top: pos.top - actualHeight - this.options.offset, left: pos.left + pos.width / 2 - actualWidth / 2};
												break;
										case 'e':
												tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth - this.options.offset};
												break;
										case 'w':
												tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width + this.options.offset};
												break;
								}
								
								if (gravity.length == 2) {
										if (gravity.charAt(1) == 'w') {
												tp.left = pos.left + pos.width / 2 - 15;
										} else {
												tp.left = pos.left + pos.width / 2 - actualWidth + 15;
										}
								}
								
								$tip.css(tp).addClass('tipsy-' + gravity);
								
								if (this.options.fade) {
										$tip.stop().css({opacity: 0, display: 'block', visibility: 'visible'}).animate({opacity: this.options.opacity});
								} else {
										$tip.css({visibility: 'visible', opacity: this.options.opacity});
								}
						}
				},
				
				hide: function() {
						if (this.options.fade) {
								this.tip().stop().fadeOut(function() { $(this).remove(); });
						} else {
								this.tip().remove();
						}
				},
				
				fixTitle: function() {
						var $e = this.$element;
						if ($e.attr('title') || typeof($e.attr('original-title')) != 'string') {
								$e.attr('original-title', $e.attr('title') || '').removeAttr('title');
						}
				},
				
				getTitle: function() {
						var title, $e = this.$element, o = this.options;
						this.fixTitle();
						var title, o = this.options;
						if (typeof o.title == 'string') {
								title = $e.attr(o.title == 'title' ? 'original-title' : o.title);
						} else if (typeof o.title == 'function') {
								title = o.title.call($e[0]);
						}
						title = ('' + title).replace(/(^\s*|\s*$)/, "");
						return title || o.fallback;
				},
				
				tip: function() {
						if (!this.$tip) {
								this.$tip = $('<div class="tipsy"></div>').html('<div class="tipsy-arrow"></div><div class="tipsy-inner"></div>');
						}
						return this.$tip;
				},
				
				validate: function() {
						if (!this.$element[0].parentNode) {
								this.hide();
								this.$element = null;
								this.options = null;
						}
				},
				
				enable: function() { this.enabled = true; },
				disable: function() { this.enabled = false; },
				toggleEnabled: function() { this.enabled = !this.enabled; }
		};
		
		$.fn.tipsy = function(options) {
				
				if (options === true) {
						return this.data('tipsy');
				} else if (typeof options == 'string') {
						var tipsy = this.data('tipsy');
						if (tipsy) tipsy[options]();
						return this;
				}
				
				options = $.extend({}, $.fn.tipsy.defaults, options);
				
				function get(ele) {
						var tipsy = $.data(ele, 'tipsy');
						if (!tipsy) {
								tipsy = new Tipsy(ele, $.fn.tipsy.elementOptions(ele, options));
								$.data(ele, 'tipsy', tipsy);
						}
						return tipsy;
				}
				
				function enter() {
						var tipsy = get(this);
						tipsy.hoverState = 'in';
						if (options.delayIn == 0) {
								tipsy.show();
						} else {
								tipsy.fixTitle();
								setTimeout(function() { if (tipsy.hoverState == 'in') tipsy.show(); }, options.delayIn);
						}
				};
				
				function leave() {
						var tipsy = get(this);
						tipsy.hoverState = 'out';
						if (options.delayOut == 0) {
								tipsy.hide();
						} else {
								setTimeout(function() { if (tipsy.hoverState == 'out') tipsy.hide(); }, options.delayOut);
						}
				};
				
				if (!options.live) this.each(function() { get(this); });
				
				if (options.trigger != 'manual') {
						var binder	 = options.live ? 'live' : 'bind',
								eventIn	 = options.trigger == 'hover' ? 'mouseenter' : 'focus',
								eventOut = options.trigger == 'hover' ? 'mouseleave' : 'blur';
						this[binder](eventIn, enter)[binder](eventOut, leave);
				}
				
				return this;
				
		};
		
		$.fn.tipsy.defaults = {
				delayIn: 0,
				delayOut: 0,
				fade: false,
				fallback: '',
				gravity: 'n',
				html: false,
				live: false,
				offset: 0,
				opacity: 0.8,
				title: 'title',
				trigger: 'hover'
		};
		
		// Overwrite this method to provide options on a per-element basis.
		// For example, you could store the gravity in a 'tipsy-gravity' attribute:
		// return $.extend({}, options, {gravity: $(ele).attr('tipsy-gravity') || 'n' });
		// (remember - do not modify 'options' in place!)
		$.fn.tipsy.elementOptions = function(ele, options) {
				return $.metadata ? $.extend({}, options, $(ele).metadata()) : options;
		};
		
		$.fn.tipsy.autoNS = function() {
				return $(this).offset().top > ($(document).scrollTop() + $(window).height() / 2) ? 's' : 'n';
		};
		
		$.fn.tipsy.autoWE = function() {
				return $(this).offset().left > ($(document).scrollLeft() + $(window).width() / 2) ? 'e' : 'w';
		};
		
})(jQuery);
