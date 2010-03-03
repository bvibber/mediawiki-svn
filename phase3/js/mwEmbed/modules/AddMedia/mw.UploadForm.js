/**
 * simple form output jquery binding
 * enables dynamic form output to a given target
 *
 */

mw.addMessages( {
	"mwe-select_file" : "Select file",
	"mwe-select_ownwork" : "I am uploading entirely my own work, and licensing it under:",
	"mwe-license_cc-by-sa" : "Creative Commons Share Alike (3.0)",
	"mwe-upload" : "Upload file",
	"mwe-destfilename" : "Destination filename:",
	"mwe-summary" : "Summary",
	"mwe-error_not_loggedin" : "You do not appear to be logged in or do not have upload privileges.",
	"mwe-watch-this-file" : "Watch this file",
	"mwe-ignore-any-warnings" : "Ignore any warnings",
	
	"mwe-i-would-like-to" : "I would like to ...",
	"mwe-upload-own-file" : "Upload my own work to $1",
	"mwe-upload-not-my-file" : "Upload media that is not my own work to $1"
} );

var default_form_options = {
	'enable_fogg'	 : true,
	'license_options': ['cc-by-sa'],
	'api_target' : false,
	'ondone_callback' : null
};
mw.UploadForm = { };

( function( $ ) {
	/**
	* Add a upload target selection menu
	* with binding to build update form target 
	*/
	mw.UploadForm.getUploadMenu = function( options ){
		if( ! options.target ){
			mw.log("Error no target for upload menu" );
			return false;
		}
		// Build out the menu
		$j( options.target ).empty().append(
			$j( '<span />' )
			.text(
				gM('mwe-i-would-like-to' )
			),
			
			$j( '<br />' )
		);
		$uploadTargetsList = $j( '<ul />'  );
		// Set provider Target		
		for( var i in options.uploadTargets ){
			var provider = options.uploadTargets[ i ]
			$uploadTargetsList.append(
				getProviderUploadLinks( provider )
			);
		}
		
		$j( options.target ).append( $uploadTargetsList );				
	};

	/** 
	* Add the Simple Upload From jQuery binding
	*
	* @param {Object} options Set of options for the upload 
	* overwriting default values in default_form_options
	*/
	mw.UploadForm.getForm = function( options  ) {
		var _this = this;
		// set the options:
		for ( var i in default_form_options ) {
			if ( !options[i] )
				options[i] = default_form_options[i];
		}

		// First do a reality check on the options:
		if ( !options.api_target ) {
			$j( options.target ).html( 'Error: Missing api target' );
			return false;
		}
		
		// Get an edit Token for "uploading"
		mw.getToken( options.api_target, 'File:MyRandomFileTokenCheck.jpg', function( eToken ) {
			if ( !eToken || eToken == '+\\' ) {
				$j( options.target ).html( gM( 'mwe-error_not_loggedin' ) );
				return false;
			}
			// Add the upload form html: 
			$j( options.target ).html(
				getUploadForm( options )
			);			
						
		
			// Set the target with the form output:
			//$( _this.selector ).html( o );
			
			// By default disable:
			$j( '#wpUploadBtn' ).attr( 'disabled', 'disabled' );

			// Set up basic license binding:
			$j( '#wpLicence' ).click( function() {
				if ( $j( this ).is( ':checked' ) ) {
					$j( '#wpUploadBtn' ).removeAttr( 'disabled' );
				} else {
					$j( '#wpUploadBtn' ).attr( 'disabled', 'disabled' );
				}
			} );
			
			// Do destination fill
			$j( "#wpUploadFile" ).change( function() {
				var path = $j( this ).val();
				// Find trailing part
				var slash = path.lastIndexOf( '/' );
				var backslash = path.lastIndexOf( '\\' );
				var fname;
				if ( slash == -1 && backslash == -1 ) {
					fname = path;
				} else if ( slash > backslash ) {
					fname = path.substring( slash + 1, 10000 );
				} else {
					fname = path.substring( backslash + 1, 10000 );
				}
				fname = fname.charAt( 0 ).toUpperCase().concat( fname.substring( 1, 10000 ) ).replace( / /g, '_' );
				
				// Output result
				$j( "#wpDestFile" ).val( fname );
				
				// Do destination check
				$j( "#wpDestFile" ).doDestCheck( {
					'warn_target':'#wpDestFile-warning'
				} );
			} );


			// Do destination check:
			$j( "#wpDestFile" ).change( function() {			
				$j( "#wpDestFile" ).doDestCheck( {
					'warn_target':'#wpDestFile-warning'
				} );
			} );

			if ( typeof options.ondone_callback == 'undefined' )
				options.ondone_callback = false;

			// Set up the binding per the config
			if ( options.enable_fogg ) {
				mw.load( 'AddMedia.firefogg', function() {
					$j( "#wpUploadFile" ).firefogg( {
						// An api url (we won't submit directly to action of the form)
						'apiUrl' : options.api_target,
											
						// MediaWiki API supports chunk uploads: 
						'enable_chunks' : false,
											
						'form_selector' : '#suf_upload',
						'selectFileCb' : function( fileName ) {
							$j( "#wpDestFile" ).val( oggName ).doDestCheck( {
								warn_target: "#wpDestFile-warning"
							} );
						},
						'onsubmit_cb' : function( ) {
							// Update with basic info template:	
							// TODO: it would be nice to have a template generator class
							var desc = $j('#wpUploadDescription').val();
							
							// Update the template if the user does not already have template code:
							if( desc.indexOf('{{Information') == -1) {
								$j('#wpUploadDescription').val( 
'== {{int:filedesc}} ==' + "\n" +
'{{Information' + "\n" +
'|Description={{en|' + desc + "\n}}\n" +
'|Author=[[User:' + wgUserName + '|' + wgUserName + ']]' + "\n" +
'|Source=' + "\n" +
'|Date=' + "\n" +
'|Permission=' + "\n" +
'|other_versions=' + "\n" + 
'}}' + "\n" +
'{{self|cc-by-sa-3.0}}' + "\n"
								);
							}
						},
						'done_upload_cb' : options.ondone_callback
					} );
				});
			}
		} );		
	}
	
	/**
	* Get a provider upload links for local upload and remote
	*/
	function getProviderUploadLinks( provider ){
		var apiUrl = provider.apiUrl;
		
		$uploadLinks = $j( '<div />' );
		
		// Upload your own file
		$uploadLinks.append(
			$j('<li />').append( 
				$j( '<a />' )
				.attr( {
					'href' : '#'
				} )
				.text(
					gM( 'mwe-upload-own-file', provider.title ) 
				)
				.click( function(){
					mw.log(" do interface for:" + provider.apiUrl );
				})
			)
		);		
		
		// Upload a file not your own ( link to special:upload for that api url )
		$uploadLinks.append (
			$j('<li />').append( 
				$j( '<a />' )
				.attr( {
					'href' : provider.uploadPage,
					'target' : '_new'
				} )
				.text( 
					gM( 'mwe-upload-not-my-file', provider.title ) 
				)
			)
		);
		
		return $uploadLinks;
	};	
	
	/**
	* Get a jquery built upload form 
	*/
	function getUploadForm( options ){
		// Build an upload form:			
		var $uploadForm = $j( '<form />' ).attr( {
			'id' : "suf_upload",
			'name' : "suf_upload", 
			'enctype' : "multipart/form-data",
			'action' : options.api_target,
			'method' : "post"
		} );
		// Add hidden input
		$uploadForm.append(
			$j( '<input />')
			.attr( { 
				'type' : "hidden",
				'name' : "action",
				'value' : "upload"
			}),
			
			$j( '<input />')
			.attr( { 
				'type' : "hidden",
				'name' : "format",
				'value' : "jsonfm"
			}),
			
			$j( '<input />')
			.attr( {
				'type' : "hidden",
				'id' : "wpEditToken",
				'name' : "wpEditToken",
				'value' : eToken
			}) 
		)
		
		// Add upload File input 
		$uploadForm.append(
			$j( '<label />' ).attr({ 
				'for' : "wpUploadFile"
			})
			.text( gM( 'mwe-select_file' ) ),
			
			$j( '<br />' ),
			
			$j( '<input />')
			.attr( {
				'id' : 'wpUploadFile',
				'type' : "file",
				'name' : "wpUploadFile",
				'size' : "15"					
			} )
			.css( 'display', 'inline' ),
			
			$j( '<br />' )
		);
		
		// Add upload description:
		$uploadForm.append(
			$j( '<label />' )
			.attr({
				'for' : "wpUploadDescription"
			})
			.text( gM( 'mwe-summary' ) ),
			
			$j( '<br />' ),
			
			$j( '<textarea />' )
			.attr( { 
				'id' : "wpUploadDescription",
				'cols' : "30",
				'rows' : "3",
				'name' : "wpUploadDescription",
				'tabindex' : "3"
			} ),
			
			$j( '<br />' )
		);
		
		// Add watchlist checkbox
		$uploadForm.append(
			$j('<input />')
			.attr({
				'type' : 'checkbox',
				'value' : 'true',
				'id' : 'wpWatchthis',
				'name' : 'watch', 
				'tabindex' : 7
			}),
			
			$j( '<label />' )
			.attr( {
				'for' : "wpWatchthis"
			} )
			.text( gM( 'mwe-watch-this-file' ) )
		);
		
		// Add ignore warning checkbox:
		$uploadForm.append(
			$j( '<input />' )
			.attr( {
				'type' : "checkbox",
				'value' : "true",
				'id' : "wpIgnoreWarning",
				'name' : "ignorewarnings",
				'tabindex' : "8"
			} ),
			
			$j( '<label />' )
			.attr({
				'for' : "wpIgnoreWarning"
			})
			.text(
				gM( 'mwe-ignore-any-warnings' ) 
			), 
			
			$j( '<br />' )
		); 
		
		// Add warning div: 
		$uploadForm.append(			
			$j( '<div />' )
			.attr({
				'id' : "wpDestFile-warning"
			}),
			
			$j( '<div />' )
			.css( {
				'clear' : 'both'
			}),
			
			$j( '<p />' )
		);
		// Add own work text and checkbox: 
		$uploadForm.append(	
			$j( '<span />')
			.text( gM( 'mwe-select_ownwork' ) ),
			
			$j( '<br />' ),
			
			$j( '<input />' )
			.attr( {
				'type' : "checkbox",
				'id' : "wpLicence",
				'name' : "wpLicence",
				'value' : "cc-by-sa"
			}),
			
			$j( '<span />' )
			.text( gM( 'mwe-license_cc-by-sa' ) ),
			
			$j( '<p />' )
		);
		
		// Add the submit button: 
		$uploadForm.append( 
			$j( '<input />' )
			.attr( {
				'type' : "submit",
				'accesskey' : "s",
				'value' : gM( 'mwe-upload' ),
				'name' : "wpUploadBtn",
				'id' : "wpUploadBtn",
				'tabindex' : "9"
			})
		);
		return $uploadFrom;
	}
	 

} )( window.mw.UploadForm );