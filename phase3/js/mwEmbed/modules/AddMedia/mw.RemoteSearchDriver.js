/*
 * remoteSearchDriver
 * Provides a base interface for the Add-Media-Wizard
 * supporting remote searching of http archives for free images/audio/video assets
 *
 * Is optionally extended by Sequence Remote Search Driver
 */

mw.addMessages( {
	"mwe-add_media_wizard" : "Add media wizard",
	"mwe-media_search" : "Media search",
	"rsd_box_layout" : "Box layout",
	"rsd_list_layout" : "List layout",
	"rsd_results_desc" : "Results $1 to $2",
	"rsd_results_desc_total" : "Results $1 to $2 of $3",
	"rsd_results_next" : "next",
	"rsd_results_prev" : "previous",
	"rsd_no_results" : "No search results for <b>$1<\/b>",
	"mwe-upload_tab" : "Upload",
	"rsd_layout" : "Layout:",
	"rsd_resource_edit" : "Edit resource: $1",
	"mwe-resource_description_page" : "Resource description page",
	"mwe-link" : "link",
	"rsd_local_resource_title" : "Local resource title",
	"rsd_do_insert" : "Do insert",
	"mwe-cc_title" : "Creative Commons",
	"mwe-cc_by_title" : "Attribution",
	"mwe-cc_nc_title" : "Noncommercial",
	"mwe-cc_nd_title" : "No Derivative Works",
	"mwe-cc_sa_title" : "Share Alike",
	"mwe-cc_pd_title" : "Public Domain",
	"mwe-unknown_license" : "Unknown license",
	"mwe-no_import_by_url" : "This user or wiki <b>cannot<\/b> import assets from remote URLs.<p>Do you need to login?<\/p><p>Is upload_by_url permission set for you?<br \/>Does the wiki have <a href=\"http:\/\/www.mediawiki.org\/wiki\/Manual:$wgAllowCopyUploads\">$wgAllowCopyUploads<\/a> enabled?<\/p>",
	"mwe-results_from" : "Results from <a href=\"$1\" target=\"_new\" >$2<\/a>",
	"mwe-missing_desc_see_source" : "This asset is missing a description. Please see the [$1 original source] and help describe it.",
	"rsd_config_error" : "Add media wizard configuration error: $1",
	"mwe-your_recent_uploads" : "Your recent uploads to $1",
	"mwe-upload_a_file" : "Upload a new file to $1",
	"mwe-resource_page_desc" : "Resource page description:",
	"mwe-edit_resource_desc" : "Edit wiki text resource description:",
	"mwe-local_resource_title" : "Local resource title:",
	"mwe-watch_this_page" : "Watch this page",
	"mwe-do_import_resource" : "Import resource",
	"mwe-update_preview" : "Update resource page preview",
	"mwe-cancel_import" : "Cancel import",
	"mwe-importing_asset" : "Importing asset",
	"mwe-preview_insert_resource" : "Preview insert of resource: $1",
	"mwe-checking-resource" : "Checking for resource",
	"mwe-resource-needs-import" : "Resource $1 needs to be imported to $2",
	"mwe-ftype-svg" : "SVG vector file",
	"mwe-ftype-jpg" : "JPEG image file",
	"mwe-ftype-png" : "PNG image file",
	"mwe-ftype-oga" : "Ogg audio file",
	"mwe-ftype-ogg" : "Ogg video file",
	"mwe-ftype-unk" : "Unknown file format",

	"rsd-wiki_commons-title": "Wikimedia Commons",
	"rsd-wiki_commons": "Wikimedia Commons, an archive of freely-licensed educational media content (images, sound and video clips)",

	"rsd-kaltura-title" : "Kaltura search",
	"rsd-kaltura" : "Kaltura agragated search for free-licenced media across multiple search providers",

	"rsd-this_wiki-title" : "This wiki",
	"rsd-this_wiki-desc" : "The local wiki install",

	"rsd-archive_org-title": "Archive.org",
	"rsd-archive_org-desc" : "The Internet Archive, a digital library of cultural artifacts",

	"rsd-flickr-title" : "Flickr.com",
	"rsd-flickr-desc" : "Flickr.com, a online photo sharing site",
	"rsd-metavid-title" : "Metavid.org",
	"rsd-metavid-desc" : "Metavid.org, a community archive of US House and Senate floor proceedings",
	
	"rsd-search-timeout" : "The search request did not complete. The server may be down experiencing heavy load. You can try again later"
} );

/**
* default_remote_search_options
* 
* Options for initialising the remote search driver
*/
var default_remote_search_options = {

	// The div that will hold the search interface
	'target_container': null, 

	// The target button or link that will invoke the search interface
	'target_invoke_button': null, 
	
	// The local wiki api url usually: wgServer + wgScriptPath + '/api.php'
	'local_wiki_api_url': null,

	/**
	* import_url_mode
	*  Can be 'api', 'autodetect', 'remote_link'
	*  api: uses the mediawiki api to insert the media asset
	*  autodetect: checks for api support before using the api to insert the media asset
	*  remote_link: hot-links the media directly into the page as html
	*/	
	'import_url_mode': 'api',
	
	// Target title used for previews of wiki page usally: wgPageName
	'target_title': null,

	// Edit tools (can be an array of tools or keyword 'all')
	'enabled_tools': 'all',

	// Target text box 
	'target_textbox': null,
	
	// Where output render should go:
	'target_render_area': null,
	 
	// Default search query
	'default_query': null, 

	// Canonical namespace prefix for images/ files
	'canonicalFileNS': 'File', 	                 

	// The api target can be "local" or the url or remote api entry point
	'upload_api_target': 'local', 
	
	// Name of the upload target
	'upload_api_name': null,
	
	// Name of the remote proxy page that will host the iframe api callback 
	'upload_api_proxy_frame': null,  

	// Enabled providers can be keyword 'all' or an array of enabled content provider keys
	'enabled_providers': 'all', 
	
	// Set a default provider 
	'default_provider': null,
	
	// Current provider (used internally) 
	'current_provider': null,
	
	// The timeout for search providers ( in seconds )
	'search_provider_timeout': 10
};

/*
* Set the jQuery bindings: 
*/ 
( function( $ ) {

	$.fn.addMediaWizard = function( options, callback ) {
		options['target_invoke_button'] = this.selector;
		options['instance_name'] = 'rsdMVRS';
		window['rsdMVRS'] = new mw.RemoteSearchDriver( options );
		if( callback ) {
			callback( window['rsdMVRS'] );
		}
	}
	
	$.addMediaWizard = function( options ){
		$.fn.addMediaWizard ( options, function( amwObj ) {
			// do the add-media-wizard display
			amwObj.createUI();
		} )
	}
	
} )( jQuery );


/*
* Set the mediaWiki globals if unset
*/
if ( typeof wgServer == 'undefined' )
	wgServer = '';
if ( typeof wgScriptPath == 'undefined' )
	wgScriptPath = '';
if ( typeof stylepath == 'undefined' )
	stylepath = '';

/*
 * Base remoteSearch Driver interface
 */
mw.RemoteSearchDriver = function( options ) {
	return this.init( options );
}

mw.RemoteSearchDriver.prototype = {

	// Result cleared flag
	results_cleared: false,
	
	// Caret position of target text area ( lazy initialised )
	caretPos: null, 
	
	// Text area value ( lazy initialised )
	textboxValue: null, 

	/** the default content providers list.
	 *
	 * (should be note that special tabs like "upload" and "combined" don't go into the content providers list:
	 * @note do not use double underscore in content providers names (used for id lookup)
	 *
	 * @@todo we will want to load more per user-preference and per category lookup
	 */
	content_providers: {
		/** 
		*  Content_providers documentation
		*
		*	@enabled: whether the search provider can be selected
		*
		*	@checked: whether the search provider will show up as selectable tab
		*	
		*	@default: default: if the current cp should be displayed (only one should be the default)
		*	
		*	@title: the title of the search provider
		*	
		*	@desc: can use html
		*	
		* 	@homepage: the homepage url for the search provider
		*
		*	@api_url: the url to query against given the library type:
		*	
		*	@lib: the search library to use corresponding to the
		*	    search object ie: 'mediaWiki' = new mediaWikiSearchSearch()
		*	    
		*	@tab_img: the tab image (if set to false use title text)
		*	    if === "true" use standard location skin/images/{cp_id}_tab.png
		*	    if === string use as url for image
		*
		*	@linkback_icon default is: /wiki/skins/common/images/magnify-clip.png
		*
		*	//domain insert: two modes: simple config or domain list:
		*	@local : if the content provider assets need to be imported or not.
		*
		*	@local_domains : sets of domains for which the content is local
		*
		*	@resource_prefix : A string to prepend to the title key
		*
		* 	@check_shared :  if we should check for shared repository asset
		*
		 */
		 
		/*
		* Local wiki search
		*/
		'this_wiki': {
			'enabled': 1,
			'checked': 1,
			'api_url':  ( wgServer && wgScriptPath ) ? 
				wgServer + wgScriptPath + '/api.php' : null,
			'lib': 'mediaWiki',
			'local': true,
			'tab_img': false
		},
		
		/**
		* Wikipedia Commons search provider configuration
		*/
		'wiki_commons': {
			'enabled': 1,
			'checked': 1,
			'homepage': 'http://commons.wikimedia.org/wiki/Main_Page',
			'api_url': 'http://commons.wikimedia.org/w/api.php',
			'lib': 'mediaWiki',
			'tab_img': true,
			'resource_prefix': 'WC_', // prefix on imported resources (not applicable if the repository is local)

			// Commons can be enabled as a remote repo do check shared 
			'check_shared': true,

			// List all the domains where commons is local:
			'local_domains': [ 'wikimedia', 'wikipedia', 'wikibooks' ],					
			
			// Specific to wiki commons config:
			// If we should search the title
			'search_title': false 
		
		},
		
		/*
		* Kaltura aggregated search
		*/ 
		'kaltura': {
			'enabled': 1,
			'checked': 1,
			'homepage': 'http://kaltura.com',
			'api_url': 'http://kaldev.kaltura.com/michael/federator.php',
			'lib': 'kaltura',
			'tab_image':false
		},
		
		/**
		* Internet archive search provider configuration
		*/
		'archive_org': {
			'enabled': 1,
			'checked': 1,
			'homepage': 'http://www.archive.org/about/about.php',

			'api_url': 'http://homeserver7.us.archive.org:8983/solr/select',
			'lib': 'archiveOrg',
			'local': false,
			'resource_prefix': 'AO_',
			'tab_img': true
		},
		
		/**
		* Flickr search provider configuration
		*/
		'flickr': {
			'enabled': 1,
			'checked': 1,
			'homepage': 'http://www.flickr.com/about/',

			'api_url': 'http://www.flickr.com/services/rest/',
			'lib': 'flickr',
			'local': false,
			// Just prefix with Flickr_ for now.
			'resource_prefix': 'Flickr_',
			'tab_img': true
		},
		
		/**
		* Metavid search provider configuration
		*/
		'metavid': {
			'enabled': 1,
			'checked': 1,
			'homepage': 'http://metavid.org/wiki/Metavid_Overview',
			'api_url': 'http://metavid.org/w/index.php?title=Special:MvExportSearch',
			'lib': 'metavid',			
			'local': false, 
			
			// MV prefix for metavid imported resources
			'resource_prefix': 'MV_', 
			
			// if the domain name contains metavid
			// no need to import metavid content to metavid sites
			'local_domains': ['metavid'], 
			
			// which stream to import, could be mv_ogg_high_quality
			// or flash stream, see ROE xml for keys
			'stream_import_key': 'mv_ogg_low_quality', 
			
			// if running the remoteEmbed extension no need to copy local
			// syntax will be [remoteEmbed:roe_url link title]
			'remote_embed_ext': false, 
			
			'tab_img': true
		},
		
		/**
		* Special Upload tab provider 
		*/ 
		'upload': {
			'enabled': 1,
			'checked': 1,
			'title': 'Upload'
		}
	},

	/**
	* License define: 	
	* 
	* NOTE: we only support creative commons type licenses
	*
	* Based on listing: http://creativecommons.org/licenses/
	*/
	licenses: {
		'cc': {
			'base_img_url':'http://upload.wikimedia.org/wikipedia/commons/thumb/',
			'base_license_url': 'http://creativecommons.org/licenses/',
			'licenses': [
				'by',
				'by-sa',
				'by-nc-nd',
				'by-nc',
				'by-nd',
				'by-nc-sa',
				'by-sa',
				'pd'
			],
			'license_images': {
				'by': {
					'image_url': '1/11/Cc-by_new_white.svg/20px-Cc-by_new_white.svg.png'
				},
				'nc': {
					'image_url': '2/2f/Cc-nc_white.svg/20px-Cc-nc_white.svg.png'
				},
				'nd': {
					'image_url': 'b/b3/Cc-nd_white.svg/20px-Cc-nd_white.svg.png'
				},
				'sa': {
					'image_url': 'd/df/Cc-sa_white.svg/20px-Cc-sa_white.svg.png'
				},
				'pd': {
					'image_url': '5/51/Cc-pd-new_white.svg/20px-Cc-pd-new_white.svg.png'
				}
			}
		}
	},

	// Width of image resources 
	thumb_width: 80,
	
	// The width of an image when editing 
	image_edit_width: 400,
	
	// The width of the video embed while editing the resource 
	video_edit_width: 400,
	
	// The insert position of the asset (overwritten by cursor position) 
	insert_text_pos: 0, 
	
	// Default display mode of search results
	displayMode : 'box', // box or list

	// The ClipEdit Object
	clipEdit: null,
	
	// A flag for proxy setup. 
	proxySetupDone: null,
	
	/**
	* The initialisation function
	*
	* @param {Object} options Options to override: default_remote_search_options
	*/
	init: function( options ) {
		var _this = this;
		mw.log( 'remoteSearchDriver:init' );
		// Add in a local "id" reference to each provider
		for ( var cp_id in this.content_providers ) {
			this.content_providers[ cp_id ].id = cp_id;
		}
		// Merge in the options		
		$j.extend( _this, default_remote_search_options, options );

		// Quick fix for cases where {object} ['all'] is used instead of {string} 'all' for enabled_providers:
		if ( _this.enabled_providers.length == 1 && _this.enabled_providers[0] == 'all' )
			_this.enabled_providers = 'all';

		// Set the current_provider from default_provider
		if( this.default_provider && this.content_providers[ this.default_provider ] ){
			this.current_provider = this.default_provider;
		}

		// Set up content_providers
		for ( var provider_id in this.content_providers ) {
			var provider = this.content_providers[ provider_id ];
			// Set the provider id
			provider[ 'id' ] = provider_id
				
			if ( _this.enabled_providers == 'all' && !this.current_provider && provider.api_url ) {
				this.current_provider = provider_id;
				break;
			} else {
				if ( $j.inArray( provider_id, _this.enabled_providers ) != -1 ) {
					// This provider is enabled
					this.content_providers[ provider_id ].enabled = true;
					// Set the current provider to the first enabled one
					if ( !this.current_provider ) {
						this.current_provider = provider_id;
					}
				} else {
					// This provider is disabled
					if ( _this.enabled_providers != 'all' ) {
						this.content_providers[ provider_id ].enabled = false;
					}
				}
			}
		}

		// Set the upload target name if unset
		if ( _this.upload_api_target == 'local' 
			&& ! _this.upload_api_name 
			&& typeof wgSiteName != 'undefined' )
		{
			_this.upload_api_name =  wgSiteName;
		} else {
			// Disable upload tab if no target is avaliable 
			this.content_providers[ 'upload' ].enabled = false;
		}

		// Set the target to "proxy" if a proxy frame is configured
		if ( _this.upload_api_proxy_frame )
			_this.upload_api_target = 'proxy';

		// Set up the local API upload URL
		if ( _this.upload_api_target == 'local' ) {
			if ( ! _this.local_wiki_api_url ) {
				$j( '#tab-upload' ).html( gM( 'rsd_config_error', 'missing_local_api_url' ) );
				return false;
			} else {
				_this.upload_api_target = _this.local_wiki_api_url;
			}
		}		
		
		// Set up the "add media wizard" button, which invokes this object
		if ( !this.target_invoke_button || $j( this.target_invoke_button ).length == 0 ) {
			mw.log( "RemoteSearchDriver:: no target invocation provided " + 
				"(will have to run your own createUI() )" );				
		} else {
			if ( this.target_invoke_button ) {				
				$j( this.target_invoke_button )
					.css( 'cursor', 'pointer' )
					.attr( 'title', gM( 'mwe-add_media_wizard' ) )
					.click( function() {						
						_this.createUI();
					} );
			}
		}
		return this;
	},

	/**
	 * Get license icon html
	 * @param license_key  the license key (ie "by-sa" or "by-nc-sa" etc)
	 */
	getLicenseIconHtml: function( licenseObj ) {
		// mw.log('output images: '+ imgs);
		return '<div class="rsd_license" title="' + licenseObj.title + '" >' +
			'<a target="_new" href="' + licenseObj.lurl + '" ' +
			'title="' + licenseObj.title + '">' +
			licenseObj.img_html +
			'</a>' +
	  		'</div>';
	},

	/**
	 * Get License From License Key
	 * @param license_key the key of the license (must be defined in: this.licenses.cc.licenses)
	 */
	getLicenseFromKey: function( license_key, force_url ) {
		// Set the current license pointer:
		var cl = this.licenses.cc;
		var title = gM( 'mwe-cc_title' );
		var imgs = '';
		var license_set = license_key.split( '-' );
		for ( var i = 0; i < license_set.length; i++ ) {
			var lkey = license_set[i];
			if ( !cl.license_images[ lkey ] ) {
				mw.log( "MISSING::" + lkey );
			}

			title += ' ' + gM( 'mwe-cc_' + lkey + '_title' );
			imgs += '<img class="license_desc" width="20" src="' +
				cl.base_img_url + cl.license_images[ lkey ].image_url + '">';
		}
		var url = ( force_url ) ? force_url : cl.base_license_url + cl.licenses[ license_key ];
		return {
			'title': title,
			'img_html': imgs,
			'key': license_key,
			'lurl': url
		};
	},

	/**
	 * Get license key from a license Url
	 *
	 * @param license_url the url of the license
	 */
	getLicenseFromUrl: function( license_url ) {
		// Check for some pre-defined url types:
		if ( license_url == 'http://www.usa.gov/copyright.shtml' ||
			license_url == 'http://creativecommons.org/licenses/publicdomain' )
			return this.getLicenseFromKey( 'pd' , license_url );
		
		// First do a direct lookup check:
		for ( var j = 0; j < this.licenses.cc.licenses.length; j++ ) {
			var jLicense = this.licenses.cc.licenses[ j ];
			// Special 'pd' case:
			if ( jLicense == 'pd' ) {
				var keyCheck = 'publicdomain';
			} else {
				var keyCheck = jLicense;
			}
			// Check the license_url for a given key
			if ( mw.parseUri( license_url ).path.indexOf( '/' + keyCheck + '/' ) != -1 ) {
				return this.getLicenseFromKey( jLicense , license_url );
			}
		}
		// Could not find it return mwe-unknown_license
		return {
			'title': gM( 'mwe-unknown_license' ),
			'img_html': '<span>' + gM( 'mwe-unknown_license' ) + '</span>',
			'lurl': license_url
		};
	},

	/**
	* Get mime type icon from a provided mime type
	* @param str mime type of the requested file
	*/
	getTypeIcon: function( mimetype ) {
		var type = 'unk';
		switch ( mimetype ) {
			case 'image/svg+xml':
				type = 'svg';
				break;
			case 'image/jpeg':
				type = 'jpg'
				break;
			case 'image/png':
				type = 'png';
				break;
			case 'audio/ogg':
				type = 'oga';
			case 'video/ogg':
			case 'application/ogg':
				type = 'ogg';
				break;
		}

		if ( type == 'unk' ) {
			mw.log( "unkown ftype: " + mimetype );
			return '';
		}

		return '<div ' + 
			'class="rsd_file_type ui-corner-all ui-state-default ui-widget-content" ' + 
			'title="' + gM( 'mwe-ftype-' + type ) + '">' +
			type  +
			'</div>';
	},
	
	/**
	* createUI
	*
	* Creates the remote search driver User Interface  
	*/
	createUI: function() {
		var _this = this;

		this.clearTextboxCache();
		
		// Setup the parent container:
		this.createDialogContainer();
		
		// Setup remote search dialog & bindings 
		this.initDialog();

		// Update the target binding to just un-hide the dialog:
		if ( this.target_invoke_button ) {
			$j( this.target_invoke_button )
				.unbind()
				.click( function() {
					mw.log( "createUI:target_invoke_button: click showDialog" );
					 _this.showDialog();
				 } );
		}
	},
	
	/**
	* showDialog
	* Displays a dialog 
	*/
	showDialog: function() {
		var _this = this;
		mw.log( "showDialog::" );
		
		// Check if dialog target is present: 
		if( $j( _this.target_container ).length == 0 ){
			this.createUI();
			return ;
		}
		
		_this.clearTextboxCache();
		var query = _this.getDefaultQuery();
		if ( query !=  $j( '#rsd_q' ).val() ) {
			$j( '#rsd_q' ).val( query );
			_this.showCurrentTab();
		}
		// $j(_this.target_container).dialog("open");
		$j( _this.target_container ).parents( '.ui-dialog' ).fadeIn( 'slow' );
		// re-center the dialog:
		$j( _this.target_container ).dialog( 'option', 'position', 'center' );
	},
	
	/**
	* Clears the textbox cache.  
	*/
	clearTextboxCache: function() {
		this.caretPos = null;
		this.textboxValue = null;
	},

	/**
	* Get the current position of the text cursor
	*/
	getCaretPos: function() {
		if ( this.caretPos == null ) {
			if ( this.target_textbox ) {
				this.caretPos = $j( this.target_textbox ).getCaretPosition();
			} else {
				this.caretPos = false;
			}
		}
		return this.caretPos;
	},
	
	/**
	* Get the value of the target textbox.  
	*/
	getTextboxValue: function() {
		if ( this.textboxValue == null ) {
			if ( this.target_textbox ) {
				this.textboxValue = $j( this.target_textbox ).val();
			} else {
				this.textboxValue = '';
			}
		}
		return this.textboxValue;
	},
	
	/**
	* Get the default query from the text selection
	*/
	getDefaultQuery: function() {
		if ( this.default_query == null ) {
			if ( this.target_textbox ) {
				var ts = $j( this.target_textbox ).textSelection();
				if ( ts != '' ) {
					this.default_query = ts;
				} else {
					this.default_query = '';
				}
			}
		}
		// If the query is still empty try the page title:
		if( this.default_query != '' && typeof wgTitle != 'undefined' )
			this.default_query = wgTitle;
						
		return this.default_query;
	},
	
	/**
	* Creates the dialog container
	*/
	createDialogContainer: function() {
		mw.log( "createDialogContainer" );
		var _this = this;
		// add the parent target_container if not provided or missing
		if ( _this.target_container && $j( _this.target_container ).length != 0 ) {
			mw.log(  'dialog already exists' );
			return;
		}

		_this.target_container = '#rsd_modal_target';
		$j( 'body' ).append(
			$j('<div>')
				.attr({
					'id' : 'rsd_modal_target',
					'title' : gM( 'mwe-add_media_wizard' ) 
				})
				.css( {
					'position' : 'absolute',
					'top' : '3em',
					'left' : '0px',
					'bottom' : '3em',
					'right' : '0px'
				})
		);
		// Get layout
		mw.log( 'width: ' + $j( window ).width() +  ' height: ' + $j( window ).height() );
		
		// Build cancel button 
		var cancelButton = {};
		cancelButton[ gM( 'mwe-cancel' ) ] = function() {
			_this.onCancelClipEdit();
		}
		
		$j( _this.target_container ).dialog( {
			bgiframe: true,
			autoOpen: true,
			modal: true,
			draggable: false,
			resizable: false,
			buttons: cancelButton,
			close: function() {
				// if we are 'editing' a item close that
				// @@todo maybe prompt the user?
				_this.onCancelClipEdit();
				$j( this ).parents( '.ui-dialog' ).fadeOut( 'slow' );
			}
		} );		
		$j( _this.target_container ).dialogFitWindow();
		
		// Add the window resize hook to keep dialog layout
		$j( window ).resize( function() {
			$j( _this.target_container ).dialogFitWindow();
		} );

		// Add cancel callback and updated button with icon
		_this.onCancelClipEdit();
	},

	/*
	* Sets up the initial html interface
	*/ 
	initDialog: function() {
		mw.log( 'initDialog' );
		var _this = this;
		mw.log( 'f::initDialog' );

		var o = '<div class="rsd_control_container" style="width:100%">' +
			'<form id="rsd_form" action="javascript:return false;" method="GET">' +
			'<input ' + 
				'class="ui-widget-content ui-corner-all" ' + 
				'type="text" ' + 
				'tabindex="1" ' + 
				'value="' + this.getDefaultQuery() + '" ' + 
				'maxlength="512" ' + 
				'id="rsd_q" ' + 
				'name="rsd_q" ' +
				'size="20" ' + 
				'autocomplete="off" />' +
			$j.btnHtml( gM( 'mwe-media_search' ), 'rms_search_button', 'search' ) +
			'</form>';
			
		// Close up the control container:
		o += '</div>';

		// search provider tabs based on "checked" and "enabled" and "combined tab"
		o += '<div ' + 
				'id="rsd_results_container" ' + 
				'style="top:0px;bottom:0px;left:0px;right:0px;">' + 
			'</div>';
		$j( this.target_container ).html( o );
		// add simple styles:
		$j( this.target_container + ' .rms_search_button' ).btnBind().click( function() {
			_this.showCurrentTab();
		} );

		// Draw the tabs:
		this.createTabs();
		// run the default search:
		if ( this.getDefaultQuery() )
			this.showCurrentTab();

		// Add bindings
		$j( '#mso_selprovider,#mso_selprovider_close' )
			.unbind()
			.click( function() {
				if ( $j( '#rsd_options_bar:hidden' ).length != 0 ) {
					$j( '#rsd_options_bar' ).animate( {
						'height': '110px',
						'opacity': 1
					}, "normal" );
				} else {
					$j( '#rsd_options_bar' ).animate( {
						'height': '0px',
						'opacity': 0
					}, "normal", function() {
						$j( this ).hide();
					} );
				}
			} );
		// Set form bindings
		$j( '#rsd_form' )
			.unbind()
			.submit( function() {
				_this.showCurrentTab();
				// Don't submit the form
				return false;
			} );
	},
	
	/**
	* Shows the upload tab loader and issues a call to showUploadForm
	*/
	showUploadTab: function() {
		mw.log( "showUploadTab::" );
		var _this = this;
		// set it to loading:
		$j( '#tab-upload' ).loadingSpinner();
		// Do things async to keep interface snappy
		setTimeout(
			function() {
				// check if we need to setup the proxy::
				if ( _this.upload_api_target == 'proxy' ) {
					_this.setupProxy( function() {
						_this.showUploadForm();
					} );
				} else {
					_this.showUploadForm();
				}
			}, 
			1 );
	},
	
	/** 
	* Shows the upload from
	*/
	showUploadForm: function() {
		var _this = this;
		mw.load( ['$j.fn.simpleUploadForm'], function() {			
			var provider = _this.content_providers['this_wiki'];

			// check for "this_wiki" enabled
			/*if(!provider.enabled){
				$j('#tab-upload')
					.html('error this_wiki not enabled (can\'t get uploaded file info)');
				return false;
			}*/

			// Load this_wiki search system to grab the resource
			_this.loadSearchLib( provider, function() {
				_this.showUploadForm_internal( provider );
			} );
		} );
	},
	
	/**
	* Once the uploadForm is ready display it for the upload provider
	* 
	* @param {Object} provider Provider object for Upload From
	*/
	showUploadForm_internal: function( provider ) {
		var _this = this;
		var uploadMsg = gM( 'mwe-upload_a_file', _this.upload_api_name );
		var recentUploadsMsg = gM( 'mwe-your_recent_uploads', _this.upload_api_name );
		
		// Do basic layout form on left upload "bin" on right
		$j( '#tab-upload' ).html( 
			'<table>' +
			'<tr>' +
			'<td valign="top" style="width:350px; padding-right: 12px;">' +
			'<h4>' + uploadMsg + '</h4>' +
			'<div id="upload_form">' +
				mw.loading_spinner() +
			'</div>' +
			'</td>' +
			'<td valign="top" id="upload_bin_cnt">' +
			'<h4>' + recentUploadsMsg + '</h4>' +
			'<div id="upload_bin">' +
				mw.loading_spinner() +
			'</div>' +
			'</td>' +
			'</tr>' +
			'</table>' 
		);

		// Fill in the user uploads:
		if ( typeof wgUserName != 'undefined' && wgUserName ) {
			// Load the upload bin with anything the current user has uploaded
			provider.sObj.getUserRecentUploads( wgUserName, function( ) {				
				_this.showResults();
			} );
		} else {
			$j( '#upload_bin_cnt' ).empty();
		}

		// Deal with the api form upload form directly:
		$j( '#upload_form' ).simpleUploadForm( {
			"api_target" 	  : _this.upload_api_target,
			"ondone_callback" : function( resultData ) {
				var wTitle = resultData['filename'];
				// Add a loading div
				_this.addResourceEditLoader();

				//Add the uploaded result
				provider.sObj.addByTitle( wTitle, function( resource ) {
					// Redraw ( with added result if new )
					_this.showResults();					
					// Pull up resource editor:
					_this.showResourceEditor( resource, $j( '#res_upload__' + resource.id ).get( 0 ) );
				} );
				// Return false to close progress window:
				return false;
			}
		} );
	},
	
	/**
	* Show the current tab ( based on current_provider var ) 
	*/
	showCurrentTab: function() {
		if ( this.current_provider == 'upload' ) {
			this.showUploadTab();
		} else {
			this.showSearchTab( this.current_provider, false );
		}
	},
	
	/**
	* Show the search tab for a given providerName
	*
	* @param {String} providerName name of the content provider
	* @param {Bollean} resetPaging if the pagging should be reset
	*/
	showSearchTab: function( providerName, resetPaging ) {
		mw.log( "f:showSearchTab::" + providerName );

		var draw_direct_flag = true;

		// Else do showSearchTab
		var provider = this.content_providers[ providerName ];

		// Check if we need to update:
		if ( typeof provider.sObj != 'undefined' ) {
			if ( provider.sObj.last_query == $j( '#rsd_q' ).val() 
				&& provider.sObj.last_offset == provider.offset ) 
			{
				mw.log( 'last query is: ' + provider.sObj.last_query + 
					' matches: ' +  $j( '#rsd_q' ).val() );
			} else {
				mw.log( 'last query is: ' + provider.sObj.last_query + 
					' not match: ' +  $j( '#rsd_q' ).val() );
				draw_direct_flag = false;
			}
		} else {
			draw_direct_flag = false;
		}
		
		if ( !draw_direct_flag ) {
			// See if we should reset the paging
			if ( resetPaging ) {
				provider.sObj.offset = provider.offset = 0;
			}

			// Set the content to loading while we do the search:
			$j( '#tab-' + providerName ).html( mw.loading_spinner() );
						
			// Make sure the search library is loaded and issue the search request
			this.getLibSearchResults( provider );
		}
	},

	/*
	* Issue a api request & cache the result this check can be avoided by setting the
	* this.import_url_mode = 'api' | 'form' | instead of 'autodetect' or 'none'
	* 
	* @param {function} callback function to be called once we have checked for copy by url support 
	*/ 	 
	checkForCopyURLSupport: function ( callback ) {
		var _this = this;
		mw.log( 'checkForCopyURLSupport:: ' );

		// See if we already have the import mode:
		if ( this.import_url_mode != 'autodetect' ) {
			mw.log( 'import mode: ' + _this.import_url_mode );
			callback();
		}
		// If we don't have the local wiki api defined we can't auto-detect use "link"
		if ( ! _this.upload_api_target ) {
			mw.log( 'import mode: remote link (no import_wiki_api_url)' );
			_this.import_url_mode = 'remote_link';
			callback();
		}
		if ( this.import_url_mode == 'autodetect' ) {
			var request = {
				'action': 'paraminfo',
				'modules': 'upload'
			}
			mw.getJSON( _this.upload_api_target, request, function( data ) {
					_this.checkCopyURLApiResult( data, callback ) 
			} );
		}
	},
	
	/**
	* Evaluate the result of an api copyURL permision request
	*
	* @param {Object} data Result data to be checked
	* @param {Function} callback Function to call once api returns value
	*/
	checkCopyURLApiResult: function( data, callback ) {
		var _this = this;
		// Api checks:
		for ( var i in data.paraminfo.modules[0].parameters ) {
			var pname = data.paraminfo.modules[0].parameters[i].name;
			if ( pname == 'url' ) {
				mw.log( 'Autodetect Upload Mode: api: copy by url:: ' );
				// Check permission  too:
				_this.checkForCopyURLPermission( function( canCopyUrl ) {
					if ( canCopyUrl ) {
						_this.import_url_mode = 'api';
						mw.log( 'import mode: ' + _this.import_url_mode );
						callback();
					} else {
						_this.import_url_mode = 'none';
						mw.log( 'import mode: ' + _this.import_url_mode );
						callback();
					}
				} );
				// End the pname search once we found the the "url" param 
				break; 
			}
		}
	},
	
	/**
	 * checkForCopyURLPermission:
	 * not really necessary the api request to upload will return appropriate error 
	 * if the user lacks permission. or $wgAllowCopyUploads is set to false
	 * (use this function if we want to issue a warning up front)
	 *
	 * @param {Function} callback Function to call with URL permission
	 * @return 
	 * 	false callback user does not have permission 	   
	 */
	checkForCopyURLPermission: function( callback ) {
		var _this = this;
		// do api check:
		var request = { 
			'meta' : 'userinfo', 
			'uiprop' : 'rights' 
		};
		mw.getJSON( _this.upload_api_target, request, function( data ) {
			for ( var i in data.query.userinfo.rights ) {
				var right = data.query.userinfo.rights[i];
				// mw.log('checking: ' + right ) ;
				if ( right == 'upload_by_url' ) {
					callback( true );
					return true; // break out of the function
				}
			}
			callback( false );
		} );
	},
	
	/**
	* Get the search results for a given content provider
	* 
	* Sets up binding to showResults once search providers results are ready
	* 
	* @param {Object} provider the provider to be searched. 
	*/
	getLibSearchResults: function( provider ) {
		var _this = this;
		mw.log('f: getLibSearchResults ' );
		// First check if we should even run the search at all (can we import / insert 
		// into the page? )
		if ( !this.isProviderLocal( provider ) && this.import_url_mode == 'autodetect' ) {
			// provider is not local check if we can support the import mode:
			this.checkForCopyURLSupport( function() {
				_this.getLibSearchResults( provider );
			} );
			return false;
		} else if ( !this.isProviderLocal( provider ) && this.import_url_mode == 'none' ) {
			if (  this.current_provider == 'combined' ) {
				// combined results are harder to error handle just ignore that repo
				provider.sObj.loading = false;
			} else {
				$j( '#tab-' + this.current_provider ).html( 
					'<div style="padding:10px">' + 
					gM( 'mwe-no_import_by_url' ) + 
					'</div>' );
			}
			return false;
		}							
		_this.loadSearchLib( provider, function( provider ) {
			// Do the search:									
			provider.sObj.getSearchResults( $j( '#rsd_q' ).val() );
			
			_this.waitForResults( function( resultStatus ) {
				if( resultStatus == 'ok' ){
					_this.showResults();
				}else{
					_this.showFailure( resultStatus )
				}
			} );
		} );
	},
	
	/**
	* Loads a providers search library
	* 
	* @param {Object} provider content provider to be loaded
	* @param {Function} callback Function to call once provider is loaded 
	* ( provider is passed back in callback to avoid possible concurancy issues in multiple load calls)
	*/
	loadSearchLib: function( provider, callback ) {
		var _this = this;
		mw.log( ' loadSearchLib: ' + provider );
		// Set up the library req:
		mw.load( [
			'baseRemoteSearch',
			provider.lib + 'Search'
		], function() {
			mw.log( "loaded lib:: " + provider.lib );
			// Else we need to run the search:
			var options = {
				'provider': provider,
				'rsd': _this
			};
			provider.sObj = new window[ provider.lib + 'Search' ]( options );
			if ( !provider.sObj ) {
				mw.log( 'Error: could not find search lib for ' + cp_id );
				return false;
			}

			// inherit defaults if not set:
			provider.limit = provider.limit ? provider.limit : provider.sObj.limit;
			provider.offset = provider.offset ? provider.offset : provider.sObj.offset;
			callback( provider );
		} );
	},

	/**
	* Waits for all results to be finished then calls the callback
	* 
	* @param {Function} callback called once loading is done. calls callback with:
	* 	'ok' search results retrived
	* 	'error_key' search results not retrived 
	* @param {Null} _callNumber Used internally to keep track of wait time 
	*/
	waitForResults: function( callback, _callNumber ) {
		// mw.log('rsd:waitForResults');		
		var _this = this;
		var loading_done = true;
		
		if( !_callNumber )
			_callNumber = 1;

		for ( var cp_id in this.content_providers ) {
			var cp = this.content_providers[ cp_id ];
			if ( typeof cp['sObj'] != 'undefined' ) {
				if ( cp.sObj.loading )
					loading_done = false;
			}		
		}		
		if( this.search_provider_timeout &&
			 (50/1000 * _callNumber) >  this.search_provider_timeout ){
			callback( 'timeout' )
			return ;
		}
		
		if ( loading_done ) {
			callback( 'ok' );
			return ;
		}
		
		setTimeout( 
			function() {
				_callNumber++;
				_this.waitForResults( callback, _callNumber );
			}, 
			50 
		);
	},
	
	/**
	* Creates the tabs based on the remote search configuration
	*/
	createTabs: function() {
		var _this = this;

		// Add the tabs to the rsd_results container:
		var s = '<div id="rsd_tabs_container" style="width:100%;">';
		var selected_tab = 0;
		var index = 0;
		s += '<ul>';
		var content = '';
		for ( var providerName in this.content_providers ) {
			var provider = this.content_providers[ providerName ];
			var tabImage = mw.getMwEmbedPath() + '/skins/common/remote_cp/' + providerName + '_tab.png';
			if ( provider.enabled && provider.checked && provider.api_url ) {
				// Add selected default if set
				if ( this.current_provider == providerName )
					selected_tab = index;

				s += '<li class="rsd_cp_tab">';
				s += '<a id="rsd_tab_' + providerName + '" href="#tab-' + providerName + '">';
				if ( provider.tab_img === true ) {
					s += '<img alt="' + gM( 'rsd-' + providerName + '-title' ) + '" ' + 
						'src="' + tabImage + '">';
				} else {
					s += gM( 'rsd-' + providerName + '-title' );
				}
				s += '</a>';
				s += '</li>';
				index++;
			}
			content += '<div id="tab-' + providerName + '" class="rsd_results"/>';
		}
		
		// Do an upload tab if enabled:
		if ( this.content_providers['upload'].enabled ) {
			s += '<li class="rsd_cp_tab" >' + 
				'<a id="rsd_tab_upload" href="#tab-upload">' + 
				gM( 'mwe-upload_tab' ) + 
				'</a></li>';
			content += '<div id="tab-upload" />';
			if ( this.current_provider == 'upload' )
				selected_tab = index++;
		}
		s += '</ul>';
		
		// Output the tab content containers:
		s += content;
		s += '</div>'; // close tab container

		// Output the respective results holders
		$j( '#rsd_results_container' ).html( s );
		// Setup bindings for tabs make them sortable: (@@todo remember order)
		mw.log( 'selected tab is: ' + selected_tab );
		$j( "#rsd_tabs_container" )
			.tabs( {
				selected: selected_tab,
				select: function( event, ui ) {
					_this.selectTab( $j( ui.tab ).attr( 'id' ).replace( 'rsd_tab_', '' ) );
				}
			})
			// Add sorting support
			.find( ".ui-tabs-nav" ).sortable( { axis: 'x' } );		
	},	

	/**
	* Get a resource object from a resource id
	*
	* NOTE: We could bind resource objects to html elements to avoid this lookup
	*
	* @param {String} id Id attribute the resource object
	*/
	getResourceFromId: function( id ) {
		var parts = id.replace( /^res_/, '' ).split( '__' );
		var providerName = parts[0];
		var resIndex = parts[1];

		// Set the upload helper providerName (to render recent uploads by this user)
		if ( providerName == 'upload' )
			providerName = 'this_wiki';

		var provider = this.content_providers[providerName];
		if ( provider && provider['sObj'] && provider.sObj.resultsObj[resIndex] ) {
			return provider.sObj.resultsObj[resIndex];
		}
		mw.log( "ERROR: could not find " + resIndex );
		return false;
	},

	/**
	* Show Results for the current_provider
	*/
	showResults: function() {
		mw.log( 'f:showResults::' + this.current_provider );
		var _this = this;
		var o = '';
		var tabSelector = '';

		if ( this.current_provider == 'upload' ) {
			tabSelector = '#upload_bin';
			var provider = _this.content_providers['this_wiki'];
		} else {
			var provider = this.content_providers[ this.current_provider ];
			tabSelector = '#tab-' + this.current_provider;
			// Output the results bar / controls
		}
		
		// Empty the existing results:
		$j( tabSelector ).empty();
				
		if ( this.current_provider != 'upload' ) {
			_this.showResultsHeader();
		}

		var numResults = 0;

		// Output all the results for the current current_provider
		if ( typeof provider['sObj'] != 'undefined' ) {
			$j.each( provider.sObj.resultsObj, function( resIndex, resource ) {
				o += _this.getResultHtml( provider, resIndex, resource );				
				numResults++;
			} );			
			// Put in the tab output (plus clear the output)
			$j( tabSelector ).append( o + '<div style="clear:both"/>' );
		}

		mw.log( 'did numResults :: ' + numResults + 
			' append: ' + $j( '#rsd_q' ).val() );

		// Remove any old search res
		$j( '#rsd_no_search_res' ).remove();
		if ( numResults == 0 ) {
			$j( '#tab-' + provider.id ).append( 
				'<span style="padding:10px">' + 
				gM( 'rsd_no_results', $j( '#rsd_q' ).val() ) + 
				'</span>' );
		}
		this.addResultBindings();
	},
	
	/**
	 * Show failure 
	 */
	showFailure : function( resultStatus ){
		//only one type of resultStatus right now: 
		if( resultStatus == 'timeout' )
			$j( '#tab-' + this.current_provider ).text(
				gM('rsd-search-timeout')		
			)
	},
	
	/**
	* Get result html, calls getResultHtmlBox or
	
	* @param {Object} provider the content provider for result
	* @param {Number} resIndex the resource index to build unique ids
	* @param {Object} resource the resource object 
	*/
	getResultHtml: function( provider, resIndex, resource ) {
		if ( this.displayMode == 'box' ) {
			return this.getResultHtmlBox( provider, resIndex, resource );
		}else{
			return this.getResultHtmlList(  provider, resIndex, resource );
		}		
	},
	
	/**
	* Get result html for box layout (see getResultHtml for params) 
	*/
	getResultHtmlBox: function( provider, resIndex, resource ) {
		var o = '';	
		o += '<div id="mv_result_' + resIndex + '" ' + 
				'class="mv_clip_box_result" ' + 
				'style="' + 
				'width:' + this.thumb_width + 'px;' + 
				'height:' + ( this.thumb_width - 20 ) + 'px;' + 
				'position:relative;">';
		
		// Check for missing poster types for audio
		if ( resource.mime == 'audio/ogg' && !resource.poster ) {
			resource.poster = mw.getConfig( 'skin_img_path' ) + 'sound_music_icon-80.png';
		}
		
		// Get a thumb with proper resolution transform if possible:
		var thumbUrl = provider.sObj.getImageTransform( resource, 
			{ 'width' : this.thumb_width } );
		
		o += '<img title="' + resource.title  + '" ' +
			'class="rsd_res_item" id="res_' + provider.id + '__' + resIndex + '" ' +
			'style="width:' + this.thumb_width + 'px;" ' + 
			'src="' + thumbUrl + '">';
			
		// Add a linkback to resource page in upper right:
		if ( resource.link ) {
			o += '<div class="' + 
					'rsd_linkback ui-corner-all ui-state-default ui-widget-content" >' +
				'<a target="_new" title="' + gM( 'mwe-resource_description_page' ) +
				'" href="' + resource.link + '">' + gM( 'mwe-link' ) + '</a>' +
				'</div>';
		}

		// Add file type icon if known
		if ( resource.mime ) {
			o += this.getTypeIcon( resource.mime );
		}

		// Add license icons if present
		if ( resource.license )
			o += this.getLicenseIconHtml( resource.license );

		o += '</div>';
		return o;
	},
	
	/**
	* Get result html for list layout (see getResultHtml for params) 	
	*/
	getResultHtmlList:function( provider, resIndex, resource ) {
		var o = '';
		o += '<div id="mv_result_' + resIndex + '" class="mv_clip_list_result" style="width:90%">';
		o += '<img ' + 
				'title="' + resource.title + '" ' + 
				'class="rsd_res_item" id="res_' + provider.id + '__' + resIndex + '" ' + 
				'style="float:left;width:' + this.thumb_width + 'px;" ' +
				'src="' + provider.sObj.getImageTransform( resource, { 'width': this.thumb_width } ) + '">';
				
		// Add license icons if present
		if ( resource.license )
			o += this.getLicenseIconHtml( resource.license );

		o += resource.desc ;
		o += '<div style="clear:both" />';
		o += '</div>';	
		return o;
	},
	
	/**
	* Add result bindings
	*
	* called after results have been displayed
	* Set bindings to showResourceEditor 
	*/
	addResultBindings: function() {
		var _this = this;
		$j( '.mv_clip_' + _this.displayMode + '_result' ).hover( 
			function() {
				$j( this ).addClass( 'mv_clip_' + _this.displayMode + '_result_over' );
				// Also set the animated image if available
				var res_id = $j( this ).children( '.rsd_res_item' ).attr( 'id' );
				var resource = _this.getResourceFromId( res_id );
				if ( resource.poster_ani )
					$j( '#' + res_id ).attr( 'src', resource.poster_ani );
			}, function() {
				$j( this ).removeClass( 
					'mv_clip_' + _this.displayMode + '_result_over' );
				var res_id = $j( this ).children( '.rsd_res_item' ).attr( 'id' );
				var resource = _this.getResourceFromId( res_id );
				// Restore the original (non animated)
				if ( resource.poster_ani )
					$j( '#' + res_id ).attr( 'src', resource.poster );
			} 
		);
		
		// Resource click action: (bring up the resource editor)
		$j( '.rsd_res_item' ).unbind().click( function() {		
			var resource = _this.getResourceFromId( $j( this ).attr( "id" ) );
			_this.showResourceEditor( resource, this );
		} );
	},
	
	/**
	* Add Resource edit layout and display a loader.
	*/
	addResourceEditLoader: function( maxWidth, overflowStyle ) {
		var _this = this;
		if ( !maxWidth ) maxWidth = 400;
		if ( !overflowStyle ) overflowStyle = 'overflow:auto;';
		// Remove any old instance:
		$j( _this.target_container ).find( '#rsd_resource_edit' ).remove();

		// Hide the results container
		$j( '#rsd_results_container' ).hide();

		var pt = $j( _this.target_container ).html();
		// Add the edit layout window with loading place holders
		$j( _this.target_container ).append( 
			'<div id="rsd_resource_edit" ' +
				'style="position:absolute;top:0px;left:0px;' + 
					'bottom:0px;right:4px;background-color:#FFF;"> ' +
			'<div id="clip_edit_ctrl" ' + 
				'class="ui-widget ui-widget-content ui-corner-all" ' + 
				'style="position:absolute;left:2px;top:5px;bottom:10px;' + 
				'width:' + ( maxWidth + 5 ) + 'px;overflow:auto;padding:5px;" >' +
			'</div>' +
			'<div id="clip_edit_disp" ' +
				'class="ui-widget ui-widget-content ui-corner-all"' +
				'style="position:absolute;' + overflowStyle + ';' + 
				'left:' + ( maxWidth + 20 ) + 'px;right:0px;top:5px;bottom:10px;' + 
				'padding:5px;" >' +
					mw.loading_spinner( 'position:absolute;top:30px;left:30px' ) +
			'</div>' +
			'</div>' );
	},
	
	/**
	* Get the edit width of a resource
	* 
	* @param {Object} resource get width of resource
	*/
	getMaxEditWidth: function( resource ) {
		var mediaType = this.getMediaType( resource );
		if ( mediaType == 'image' ) {
			return this.image_edit_width;
		} else {
			return this.video_edit_width;
		}
	},
	
	/**
	* Get the media Type of a resource
	*
	* @param {Object} resource get media type of resource
	*/
	getMediaType: function( resource ) {
		if ( resource.mime.indexOf( 'image' ) != -1 ) {
			return 'image';
		} else if ( resource.mime.indexOf( 'audio' ) != -1 ) {
			return 'audio';
		} else {
			return 'video';
		}
	},
	
	/**
	* Removes the resource editor
	*/
	removeResourceEditor: function() {
		$j( '#rsd_resource_edit' ).remove();
		$j( '#rsd_resource_edit' ).css( 'opacity', 0 );
		$j( '#rsd_edit_img' ).remove();
	},

	/**
	* Show the resource editor
	* @param {Object} resource Resource to be edited
	* @param {Object} rsdElement Element Image to be swaped with "edit" version of resource
	*/ 
	showResourceEditor: function( resource, rsdElement ) {
		mw.log( 'f:showResourceEditor:' + resource.title );
		var _this = this;

		// Remove any existing resource edit interface
		_this.removeResourceEditor();

		var mediaType = _this.getMediaType( resource );
		var maxWidth = _this.getMaxEditWidth( resource );

		// So that transcripts show on top
		var overflow_style = ( mediaType == 'video' ) ? '' : 'overflow:auto;';
		// Append to the top level of model window:
		_this.addResourceEditLoader( maxWidth, overflow_style );
		// update add media wizard title:
		var dialogTitle = gM( 'mwe-add_media_wizard' ) + ': ' + 
			gM( 'rsd_resource_edit', resource.title );
		$j( _this.target_container ).dialog( 'option', 'title', dialogTitle );
		mw.log( 'did append to: ' + _this.target_container );

		// Left side holds the image right size the controls /
		$j( rsdElement )
			.clone()
			.attr( 'id', 'rsd_edit_img' )
			.appendTo( '#clip_edit_disp' )
			.css( {
				'position':'absolute',
				'top':'40%',
				'left':'20%',
				'cursor':'default',
				'opacity':0
			} );
			
		// Try and keep aspect ratio for the thumbnail that we clicked:			
		var imageRatio = null;
		try {			
			imageRatio = $j( rsdElement ).get(0).height / $j( rsdElement ).get(0).width;
		} catch( e ) {
			mw.log( 'Errro: browser could not read height or width attribute' ) ;
		}
		if ( !imageRatio ) {
			var imageRatio = 1; // set ratio to 1 if tRatio did not work.
		}
		
		mw.log( 'Set from ' +  imageRatio + ' to init thumbimage to ' + 
			maxWidth + ' x ' + parseInt( imageRatio * maxWidth ) );
		// Scale up image and to swap with high res version
		$j( '#rsd_edit_img' ).animate( 
			{
				'opacity': 1,
				'top': '5px',
				'left': '5px',
				'width': maxWidth + 'px',
				'height': parseInt( imageRatio * maxWidth )  + 'px'
			}, 
			"slow" ); // Do it slow to give it a chance to finish loading the high quality version

		if ( mediaType == 'image' ) {
			_this.loadHighQualityImage( 
				resource, 
				{ 'width': maxWidth }, 
				'rsd_edit_img', 
				function() {
					$j( '.loading_spinner' ).remove();
				}
			);
		}
		// Also fade in the container:
		$j( '#rsd_resource_edit' ).animate( {
			'opacity': 1,
			'background-color': '#FFF',
			'z-index': 99
		} );

		// Show the editor itself
		if ( mediaType == 'image' ) {
			_this.showImageEditor( resource );
		} else if ( mediaType == 'video' || mediaType == 'audio' ) {
			_this.showVideoEditor( resource );
		}
	},
	
	/*
	* Loads a higher quality image 
	*
	* @param {Object} resource requested resource for higher quality image
	* @param {Object} size the requested size of the higher quality image
	* @param {string} target the image id to replace with higher quality image
	* @param {Function} callback the function to be calle once the image is loaded 
	*/
	loadHighQualityImage: function( resource, size, target_img_id, callback ) {
		// Get the high quality image url:
		resource.pSobj.getImageObj( resource, size, function( imObj ) {
			resource['edit_url'] = imObj.url;

			mw.log( "edit url: " + resource.edit_url );
			// Update the resource
			resource['width'] = imObj.width;
			resource['height'] = imObj.height;

			// See if we need to animate some transition
			if ( size.width != imObj.width ) {
				mw.log( 'loadHighQualityImage:size mismatch: ' + size.width + ' != ' + imObj.width );
				// Set the target id to the new size:
				$j( '#' + target_img_id ).animate( {
					'width': imObj.width + 'px',
					'height': imObj.height + 'px'
				});
			} else {
				mw.log( 'use req size: ' + imObj.width + 'x' + imObj.height );
				$j( '#' + target_img_id ).animate( {
					'width': imObj.width + 'px', 
					'height': imObj.height + 'px' 
				});
			}
			// Don't swap it in until its loaded:
			var img = new Image();
			// Load the image image:
			$j( img ).load( function () {
					 $j( '#' + target_img_id ).attr( 'src', resource.edit_url );
					 // Let the caller know we are done and what size we ended up with:
					 callback();
				} ).error( function () {
					mw.log( "Error with:  " +  resource.edit_url );
				} ).attr( 'src', resource.edit_url );
		} );
	},
	
	/**
	* Do cancel edit callbacks and interface updates. 
	*/
	onCancelClipEdit: function() {
		var _this = this;
		mw.log( 'onCancelClipEdit' );
		var b_target = _this.target_container + '~ .ui-dialog-buttonpane';
		$j( '#rsd_resource_edit' ).remove();
		
		// Remove preview if its 'on'
		$j( '#rsd_preview_display' ).remove();
		
		// Restore the resource container:
		$j( '#rsd_results_container' ).show();

		// Restore the title:
		$j( _this.target_container ).dialog( 'option', 'title', gM( 'mwe-add_media_wizard' ) );
		mw.log( "should update: " + b_target + ' with: cancel' );
		// Restore the buttons:
		$j( b_target )
			.html( $j.btnHtml( gM( 'mwe-cancel' ) , 'mv_cancel_rsd', 'close' ) )
			.children( '.mv_cancel_rsd' )
			.btnBind()
			.click( function() {
				$j( _this.target_container ).dialog( 'close' );
			} );
	},

	/** 
	 * Get the control actions for clipEdit with relevant callbacks
	 * @param {Object} provider the provider object to 
	 */
	getClipEditControlActions: function( provider ) {
		var _this = this;
		var actions = { };

		actions['insert'] = function( resource ) {
			_this.insertResource( resource );
		}
		// If not directly inserting the resource is support a preview option:
		if ( _this.import_url_mode != 'remote_link' ) {
			actions['preview'] = function( resource ) {
				_this.showPreview( resource )
			};
		}
		actions['cancel'] = function() {
			_this.onCancelClipEdit()
		}
		return actions;
	},
	
	/**
	* Clip edit options
	*/
	getClipEditOptions: function( resource ) {
		return {
			'resource' : resource,
			'parent_container': 'rsd_modal_target',
			'target_clip_display': 'clip_edit_disp',
			'target_control_display': 'clip_edit_ctrl',
			'media_type': this.getMediaType( resource ),
			'parentRemoteSearchDriver': this,
			'controlActionsCallback': this.getClipEditControlActions( resource.pSobj.cp ),
			'enabled_tools': this.enabled_tools
		};
	},

	/**
	 * Internal function called by showResourceEditor() to show an image editor
	 * @param {Object} resource Resource for Image Editor display
	 */
	showImageEditor: function( resource ) {
		var _this = this;
		var options = _this.getClipEditOptions( resource );
		
		// Display the mvClipEdit obj once we are done loading:
		mw.load( 'mw.ClipEdit', function() {			
			// Run the image clip tools
			_this.clipEdit = new mw.ClipEdit( options );
		} );
	},

	/**
	 * Internal function called by showResourceEditor() to show a video or audio
	 * editor.
	 * @param {Object} resource Show video editor for this resource
	 */
	showVideoEditor: function( resource ) {
		var _this = this;
		var options = _this.getClipEditOptions( resource );
		var mediaType = this.getMediaType( resource );

		mw.log( 'media type:: ' + mediaType );
		
		// Get any additional embedding helper meta data prior to doing the actual embed
		// normally this meta should be provided in the search result 
		// (but archive.org has another query for more media meta)
		resource.pSobj.addResourceInfoCallback( resource, function() {		
			var runFlag = false;
			// Make sure we have the 'EmbedPlayer' module:
			mw.load( 'EmbedPlayer', function() {
				// Strange concurrency issue with callbacks
				// @@todo try and figure out why this callback is fired twice
				if ( !runFlag ) {
					runFlag = true;
				} else {
					mw.log( 'Error: embedPlayerCheck run twice' );
					return false;
				}
				var embedHtml = resource.pSobj.getEmbedHTML( resource, 
					{ id : 'embed_vid' } );
				mw.log( 'append html: ' + embedHtml );
				$j( '#clip_edit_disp' ).html( embedHtml );
				
				mw.log( "about to call $j.embedPlayer::embed_vid" );							
				// Rewrite by id
				$j( '#embed_vid').embedPlayer ( function() {
				
					// Grab information available from the embed instance
					resource.pSobj.addEmbedInfo( resource, 'embed_vid' );

					// Add libraries resizable and hoverIntent to support video edit tools
					var librarySet = [
						'mw.ClipEdit', 
						'$j.ui.resizable',
						'$j.fn.hoverIntent'
					] 
					mw.load( librarySet, function() {
						// Make sure the rsd_edit_img is removed:
						$j( '#rsd_edit_img' ).remove();
						// Run the image clip tools
						_this.clipEdit = new mw.ClipEdit( options );
					} );
				} );
			} );
		} );
	},
	
	/**
	* Checks if a given content provider is local.  
	*/
	isProviderLocal: function( provider ) {
		if ( provider.local ) {
			return true;
		} else {
			// Check if we can embed the content locally per a domain name check:
			var localHost = mw.parseUri( this.local_wiki_api_url ).host;
			if ( provider.local_domains ) {
				for ( var i = 0; i < provider.local_domains.length; i++ ) {
					var domain = provider.local_domains[i];
					if ( localHost.indexOf( domain ) != -1 )
						return true;
				}
			}
			return false;
		}
	},

	/**
	 * Check if the file is either a local upload, or if it has already been 
	 * imported under the standard filename scheme. 
	 *
	 * Calls the callback with two parameters:
	 *     callback( resource, status )
	 *
	 * resource: a resource object pointing to the local file if there is one,
	 *    or false if not
	 *
	 * status: may be 'local', 'shared', 'imported' or 'missing'
	 */
	isFileLocallyAvailable: function( resource, callback ) {
		var _this = this;
		// Add a loader on top
		$j.addLoaderDialog( gM( 'mwe-checking-resource' ) );

		// Extend the callback, closing the loader dialog before chaining
		var myCallback = function( status ) {
			$j.closeLoaderDialog();
			if ( typeof callback == 'function' ) {
				callback( status );
			}
		}

		// @@todo get the localized File/Image namespace name or do a general {NS}:Title
		var provider = resource.pSobj.provider;
		var _this = this;

		// Clone the resource. Not sure why this not-working clone was put here... 
		// using the actual resource does not really affect things
		/*
		var proto = {};
		proto.prototype = resource;
		var myRes = new proto;
		*/		
		
		// Update base target_resource_title:
		resource.target_resource_title = resource.titleKey.replace( /^(File:|Image:)/ , '' )

		// Check if local repository
		// or if import mode if just "linking" ( we should already have the 'url' )

		if ( this.isProviderLocal( provider ) || this.import_url_mode == 'remote_link' ) {
			// Local repo, jump directly to the callback:
			myCallback( 'local' );
		} else {
			// Check if the file is local ( can be shared repo )
			if ( provider.check_shared ) {
				_this.findFileInLocalWiki( resource.target_resource_title, function( imagePage ) {
					if ( imagePage && imagePage['imagerepository'] == 'shared' ) {
						myCallback( 'shared' );
					} else {
						_this.isFileAlreadyImported( resource, myCallback );
					}
				} );
			} else {
				_this.isFileAlreadyImported( resource, myCallback );
			}
		}
	},

	/**
	 * Check if the file is already imported with this extension's filename scheme
	 *
	 * Calls the callback with two parameters:
	 *     callback( resource, status )
	 *
	 * If the image is found, the status will be 'imported' and the resource
	 * will be the new local resource.
	 *
	 * If the image is not found, the status  will be 'missing' and the resource 
	 * will be false.
	 */
	isFileAlreadyImported: function( resource, callback ) {
		mw.log( '::isFileAlreadyImported:: ' );
		var _this = this;

		// Clone the resource 
		//( not really needed and confuses the resource pointer role) 
		/*var proto = {};
		proto.prototype = resource;
		var myRes = new proto;
		*/
		var provider = resource.pSobj.provider;

		// Update target_resource_title with resource repository prefix:
		resource.target_resource_title = provider.resource_prefix + resource.target_resource_title;
		
		// Check if the file exists:
		_this.findFileInLocalWiki( resource.target_resource_title, function( imagePage ) {			
			if ( imagePage ) {			
				// Update to local src
				resource.local_src = imagePage['imageinfo'][0].url;				
				// @@todo maybe  update poster too?
				resource.local_poster = imagePage['imageinfo'][0].thumburl;				
				// Update the title:
				resource.target_resource_title = imagePage.title.replace(/^(File:|Image:)/ , '' );
				callback( 'imported' );
			} else {
				callback( 'missing' );
			}
		} );
	},
	
	/**
	* Show Import User Interface 
	* 
	* @param {Object} resource Resource Object to be imported
	* @param {Function} callback Function to be called once the resource is imported 
	*/
	showImportUI: function( resource, callback ) {
		var _this = this;
		mw.log( "showImportUI:: update:" + _this.canonicalFileNS + ':' + 
			resource.target_resource_title );

		// setup the resource description from resource description:
		// FIXME: i18n, namespace
		var desc = '{{Information ' + "\n";

		if ( resource.desc ) {
			desc += '|Description= ' + resource.desc + "\n";
		} else {
			desc += '|Description= ' + gM( 'mwe-missing_desc_see_source', resource.link ) + "\n";
		}

		// Output search specific info
		desc += '|Source=' + resource.pSobj.getImportResourceDescWiki( resource ) + "\n";

		if ( resource.author )
			desc += '|Author=' + resource.author + "\n";

		if ( resource.date )
			desc += '|Date=' + resource.date + "\n";

		// Add the Permission info:
		desc += '|Permission=' + resource.pSobj.getPermissionWikiTag( resource ) + "\n";

		if ( resource.other_versions )
			desc += '|other_versions=' + resource.other_versions + "\n";

		desc += '}}';

		// Get any extra categories or helpful links
		desc += resource.pSobj.getExtraResourceDescWiki( resource );


		$j( '#rsd_resource_import' ).remove();// remove any old resource imports

		// Show user dialog to import the resource
		$j( _this.target_container ).append( 
			'<div id="rsd_resource_import" ' +
				'class="ui-widget-content" ' +
				'style="position:absolute;top:0px;left:0px;right:0px;bottom:0px;z-index:5">' +
			'<h3 style="color:red;padding:5px;">' + 
			gM( 'mwe-resource-needs-import', [resource.title, _this.upload_api_name] ) + 
			'</h3>' +
			'<div id="rsd_preview_import_container" ' + 
				'style="position:absolute;width:50%;bottom:0px;left:5px;' + 
					'overflow:auto;top:30px;">' +
			
			// Get embedHTML with small thumb:
			resource.pSobj.getEmbedHTML( resource, {
				'id': _this.target_container + '_rsd_pv_vid',
				'max_height': '220',
				'only_poster': true
			} ) + 
			
			'<br style="clear both"/>' +
			'<strong>' + gM( 'mwe-resource_page_desc' ) + '</strong>' +
			'<div id="rsd_import_desc" style="display:inline;">' +
				mw.loading_spinner( 'position:absolute;top:5px;left:5px' ) +
			'</div>' +
			'</div>' +
			'<div id="rds_edit_import_container" ' + 
				'style="position:absolute; ' + 
				'left:50%;bottom:0px;top:30px;right:0px;overflow:auto;">' +
			'<strong>' + gM( 'mwe-local_resource_title' ) + '</strong>' + 
			'<br/>' +
			'<input type="text" size="30" value="' + resource.target_resource_title + '" />' + 
			'<br/>' +
			'<strong>' + gM( 'mwe-edit_resource_desc' ) + '</strong>' +
			'<textarea id="rsd_import_ta" ' + 
				'style="width:90%;" rows="8" cols="50">' +
			desc +
			'</textarea>' + 
			'<br/>' +
			'<input type="checkbox" value="true" id="wpWatchthis" ' + 
				'name="wpWatchthis" tabindex="7" />' +
			'<label for="wpWatchthis">' + gM( 'mwe-watch_this_page' ) + '</label> ' + 
			'<br/><br/><br/>' +
			$j.btnHtml( gM( 'mwe-update_preview' ), 'rsd_import_apreview', 'refresh' ) + 
			' ' +
			'</div>' +			
			// Output the rendered and non-rendered version of description for easy switching:
			'</div>' );
			
		var buttonPaneSelector = _this.target_container + '~ .ui-dialog-buttonpane';
		$j( buttonPaneSelector ).html (
			// Add the buttons to the bottom:
			$j.btnHtml( gM( 'mwe-do_import_resource' ), 'rsd_import_doimport', 'check' ) + 
			' ' +
			$j.btnHtml( gM( 'mwe-cancel_import' ), 'rsd_import_acancel', 'close' ) + ' '
		);

		// Update video tag (if a video)
		if ( resource.mime.indexOf( 'video/' ) !== -1 ){
			var target_rewrite_id = $j( _this.target_container ).attr( 'id' ) + '_rsd_pv_vid';
			$j('#' + target_rewrite_id ).embedPlayer();
		}

		// Load the preview text:
		_this.parse(
			desc, _this.canonicalFileNS + ':' + resource.target_resource_title, 
			function( descHtml ) {
				$j( '#rsd_import_desc' ).html( descHtml );
			} 
		);
		
		// Add bindings:
		$j( _this.target_container + ' .rsd_import_apreview' )
			.btnBind()
			.click( function() {
				mw.log( " Do preview asset update" );
				$j( '#rsd_import_desc' ).html( mw.loading_spinner() );
				// load the preview text:
				_this.parse( 
					$j( '#rsd_import_ta' ).val(), 
					_this.canonicalFileNS + ':' + resource.target_resource_title, 
					function( o ) {
						mw.log( 'got updated preview: ' );
						$j( '#rsd_import_desc' ).html( o );
					} 
				);
			} );
		
		$j( buttonPaneSelector + ' .rsd_import_doimport' )
			.btnBind()
			.click( function() {
				mw.log( "do import asset:" + _this.import_url_mode );
				// check import mode:
				if ( _this.import_url_mode == 'api' ) {
					if ( _this.upload_api_target == 'proxy' ) {
						_this.setupProxy( function() {
							_this.doApiImport( resource, callback );
						} );
					} else {
						_this.doApiImport( resource, callback );
					}
				} else {
					mw.log( "Error: import mode is not form or API (can not copy asset)" );
				}
			} );
		$j( buttonPaneSelector + ' .rsd_import_acancel' )
			.btnBind()
			.click( function() {
				$j( '#rsd_resource_import' ).fadeOut( "fast", function() {
					$j( this ).remove();
					// restore buttons (from the clipEdit object::)
					_this.clipEdit.updateInsertControlActions();
					$j( buttonPaneSelector ).removeClass( 'ui-state-error' );
				} );
			} );
	},

	/**
	* Sets up the proxy for the remote inserts
	* 
	* @param {Function} callbcak Function to call once proxy is setup. 
	*/
	setupProxy: function( callback ) {
		var _this = this;

		if ( _this.proxySetupDone ) {
			if ( callback )
				callback();
			return;
		}
		// setup the the proxy via  $j.apiProxy loader:
		if ( !_this.upload_api_proxy_frame ) {
			mw.log( "Error:: remote api but no proxy frame target" );
			return false;
		} else {
			$j.apiProxy(
				'client',
				{
					'server_frame': _this.upload_api_proxy_frame
				}, function() {
					_this.proxySetupDone = true
					if ( callback )
						callback();
				}
			);
		}
	},
	
	/**
	* Check the local wiki for a given fileName 
	*
	* @param {String} fileName File Name of the requested file 
	* @param {Function} callback 
	* 	Called with the result api result object OR
	* 	Callback is called with "false" if the file is not found
	*/
	findFileInLocalWiki: function( fileName, callback ) {
		mw.log( "findFileInLocalWiki::" + fileName );
		var _this = this;
		var request = {
			'action': 'query',
			'titles': _this.canonicalFileNS + ':' + fileName,
			'prop': 'imageinfo',
			'iiprop': 'url',
			'iiurlwidth': '400'
		};
		// First check the api for imagerepository
		mw.getJSON( this.local_wiki_api_url, request, function( data ) {
			if ( data.query.pages ) {
				for ( var i in data.query.pages ) {
					for ( var j in data.query.pages[i] ) {
						if ( j == 'missing' 
							&& data.query.pages[i].imagerepository != 'shared' ) 
						{
							mw.log( fileName + " not found" );
							callback( false );
							return;
						}
					}
					// else page is found:
					mw.log( fileName + "  found" );					
					callback( data.query.pages[i] );
				}
			}
		} );
	},
	
	/**
	* Do import a resource via API import call
	* 
	* @param {Object} resource Resource to import
	* @param {Function} callback Function to be called once api import call is done
	*/
	doApiImport: function( resource, callback ) {
		var _this = this;		
		mw.log( ":doApiImport:" );
		$j.addLoaderDialog( gM( 'mwe-importing_asset' ) );
		
		// Load the BaseUploadInterface:
		mw.load( 
			[
				'mw.BaseUploadInterface',
				'$j.ui.progressbar'
			], 
			function() {
				mw.log( 'mvBaseUploadInterface ready' );
				// Initiate a upload object ( similar to url copy ):
				// ( mvBaseUploadInterface handles upload errors ) 
				var uploader = new mw.BaseUploadInterface( {
					'api_url' : _this.upload_api_target,
					'done_upload_cb':function() {
						mw.log( 'doApiImport:: run callback::' );
						// We have finished the upload:

						// Close up the rsd_resource_import
						$j( '#rsd_resource_import' ).remove();
						// return the parent callback:
						return callback();
					}
				} );
				// Get the edit token
				_this.getEditToken( function( token ) {
					uploader.editToken = token;

					// Close the loader now that we are ready to present the progress dialog::
					$j.closeLoaderDialog();
					uploader.doHttpUpload( {
						'url': resource.src,
						'filename': resource.target_resource_title,
						'comment': $j( '#rsd_import_ta' ).val()
					} );
				} );
			}
		);
	},
	
	/**
	* get an edit Token
	* depends on upload_api_target being initialized
	* 
	* @param {Function} callback Function to be called once the token is available  
	*/
	getEditToken: function( callback ) {
		var _this = this;
		if ( _this.upload_api_target != 'proxy' ) {
			// (if not a proxy) first try to get the token from the page:
			var editToken = $j( "input[name='wpEditToken']" ).val();
			if ( editToken ) {
				callback( editToken );
				return;
			}
		}
		// @@todo try to load over ajax if( _this.local_wiki_api_url ) is set
		// ( for cases where inserting from a normal page view (that did not have wpEditToken)
		mw.getToken( _this.upload_api_target, function( token ) {
			callback( token );
		} );
	},
	
	/**
	* Shows a preview of the given resource
	*/
	showPreview: function( resource ) {
		var _this = this;
		this.isFileLocallyAvailable( resource, function( status ) {
		
			// If status is missing show import UI
			if ( status === 'missing' ) {
				_this.showImportUI( resource, function(){
					// Once the image is imported re-issue the showPreview request: 
					_this.showPreview( resource );
				} );
				return;
			}

			// Put another window ontop:
			$j( _this.target_container ).append( 
				$j('<div>').attr({
					'id': 'rsd_preview_display'
				})
				.css({
					'position' : 'absolute',
					'overflow' : 'auto',
					'z-index' : 4,
					'top' : '0px',
					'bottom' : '0px',
					'right' : '0px',
					'left' : '0px',
					'background-color' : '#FFF'
				})				
			)

			var buttonPaneSelector = _this.target_container + '~ .ui-dialog-buttonpane';
			var origTitle = $j( _this.target_container ).dialog( 'option', 'title' );

			// Update title:
			$j( _this.target_container ).dialog( 'option', 'title', 
				gM( 'mwe-preview_insert_resource', resource.title ) );

			// Update buttons preview:
			$j( buttonPaneSelector )
				.html(
					$j.btnHtml( gM( 'rsd_do_insert' ), 'preview_do_insert', 'check' ) + ' ' )
				.children( '.preview_do_insert' )
				.click( function() {
					_this.insertResource( resource );
				} );
				
			// Update cancel button
			$j( buttonPaneSelector )
				.append( '<a href="#" class="preview_close">Do More Modification</a>' )
				.children( '.preview_close' )
				.click( function() {
					$j( '#rsd_preview_display' ).remove();
					// restore title:
					$j( _this.target_container ).dialog( 'option', 'title', origTitle );
					// restore buttons (from the clipEdit object::)
					_this.clipEdit.updateInsertControlActions();
				} );

			// Get the preview wikitext
			_this.parse( 
				_this.getPreviewText( resource ),
				_this.target_title,
				function( phtml ) {
					$j( '#rsd_preview_display' ).html( phtml );
					if( mw.documentHasPlayerTags() ){
						mw.load( 'EmbedPlayer', function(){							
							// Update the display of video tag items (if any) 
							$j( mw.getConfig( 'rewritePlayerTags' ) ).embedPlayer();
						});
					}
				}
			);
		} );
	},
	
	/**
	* Get the embed code
	*
	* based on import_url_mode:
	* calls the resource providers getEmbedHTML method
	* 	or 
	* calls the resource providers getEmbedWikiCode method
	*/	
	getEmbedCode: function( resource ) {
		if ( this.import_url_mode == 'remote_link' ) {
			return resource.pSobj.getEmbedHTML( resource, {'insert_description': true } );
		} else {
			return resource.pSobj.getEmbedWikiCode( resource );
		}
	},
	
	/**
	* Get the preview text for a given resource
	* 
	* builds the wikitext represnetation and 
	* issues an api call to gennerate a preview
	* 
	* @param {Object} resource Resource to get preview text for.
	*/
	getPreviewText: function( resource ) {
		var _this = this;
		var text;

		// Insert at start if textInput cursor has not been set (ie == length)
		var insertPos = _this.getCaretPos();
		var originalText = _this.getTextboxValue();
		var embedCode = _this.getEmbedCode( resource );
		if ( insertPos !== false && originalText ) {
			if ( originalText.length == insertPos ) {
				insertPos = 0;
			}
			text = originalText.substring( 0, insertPos ) +
				embedCode + originalText.substring( insertPos );
		} else {
			text = $j( _this.target_textbox ).val() + embedCode;
		}
		// check for missing </references>
		if ( text.indexOf( '<references/>' ) == -1 && text.indexOf( '<ref>' ) != -1 ) {
			text = text + '<references/>';
		}
		return text;
	},
	
	/**
	* issues the wikitext parse call 
	* 
	* @param {String} wikitext Wiki Text to be parsed by mediaWiki api call
	* @param {String} title Context title of the content to be parsed
	* @param {Function} callback Function called with api parser output 
	*/
	parse: function( wikitext, title, callback ) {		
		mw.getJSON( this.local_wiki_api_url, 
			{
				'action': 'parse',
				'title' : title,
				'text': wikitext
			}, function( data ) {
				callback( data.parse.text['*'] );
			}
		);
	},
	
	/**
	* Insert a resource
	*
	* Calls updateTextArea with the passed resource  
	* once we confirm the resource is available
	* 
	* @param {Object} resource Resource to be inserted
	*/	
	insertResource: function( resource ) {
		mw.log( 'insertResource: ' + resource.title );
		var _this = this;		
		// Double check that the resource is present:
		this.isFileLocallyAvailable( resource, function( status ) {			
			if ( status === 'missing' ) {
				_this.showImportUI( resource, function() {
					_this.insertResourceToOutput( resource );
				} );
				return;
			}
			if ( status === 'local' || status === 'shared' || status === 'imported' ) {
				_this.insertResourceToOutput( resource );
			}
			//NOTE: should hannlde errors or other status states?			
		} );
	},
	
	/**
	* Finish up the insertResource request outputing the resource to output targets
	*
	* @param {Object} resource Resource to be inserted into the output targets
	*/
	insertResourceToOutput: function( resource ){
		var _this = this;
		$j( _this.target_textbox ).val( _this.getPreviewText( resource ) );
		_this.clearTextboxCache();

		// Update the render area for HTML output of video tag with mwEmbed "player"
		var embedCode = _this.getEmbedCode( resource );
		if ( _this.target_render_area && embedCode ) {
			
			// Output with some padding:
			$j( _this.target_render_area )
				.append( embedCode + '<div style="clear:both;height:10px">' )

			// Update the player if video or audio:
			if ( resource.mime.indexOf( 'audio' ) != -1 ||
				resource.mime.indexOf( 'video' ) != -1 ||
				resource.mime.indexOf( '/ogg' ) != -1 ) 
			{
				// Re-load the player module ( will scan page for mw.getConfig( 'rewritePlayerTags' ) )
				$j.embedPlayers();
			}
		}
		
		// Close up the add-media-wizard dialog
		_this.closeAll();
	},
		
	/**
	* Close up the remote search driver
	*/
	closeAll: function() {
		var _this = this;
		mw.log( "close all:: "  + _this.target_container );
		_this.onCancelClipEdit();
		
		$j( _this.target_container ).dialog( 'close' );		
		// Give a chance for the events to complete
		// (somehow at least in firefox a rare condition occurs where
		// the modal of the edit-box stick around even after the
		// close request has been issued. )		
		setTimeout( 
			function() {
				$j( _this.target_container ).dialog( 'close' );
				$j( '#rsd_modal_target').remove();
			}, 25 
		);
	},
	/**
	* Show Results Header includes controls like box vs list view and
	* issues a call to showPagingHeader
	*/ 
	showResultsHeader: function() {
		var _this = this;
		var darkBoxUrl = mw.getConfig( 'skin_img_path' ) + 'box_layout_icon_dark.png';
		var lightBoxUrl = mw.getConfig( 'skin_img_path' ) + 'box_layout_icon.png';
		var darkListUrl = mw.getConfig( 'skin_img_path' ) + 'list_layout_icon_dark.png';
		var lightListUrl = mw.getConfig( 'skin_img_path' ) + 'list_layout_icon.png';

		if ( !this.content_providers[ this.current_provider ] ) {
			return;
		}
		var cp = this.content_providers[this.current_provider];
		var resultsFromMsg = gM( 'mwe-results_from', 
			[ cp.homepage, gM( 'rsd-' + this.current_provider + '-title' ) ] );
		var defaultBoxUrl, defaultListUrl;
		if ( _this.displayMode == 'box' ) {
			defaultBoxUrl = darkBoxUrl;
			defaultListUrl = lightListUrl;
		} else {
			defaultBoxUrl = lightBoxUrl;
			defaultListUrl = darkListUrl;
		}

		var about_desc = '<span style="position:relative;top:0px;font-style:italic;">' +
			'<i>' + resultsFromMsg + '</i></span>';

		$j( '#tab-' + this.current_provider ).append( '<div id="rds_results_bar">' +
			'<span style="float:left;top:0px;font-style:italic;">' +
			gM( 'rsd_layout' ) + ' ' +
			'<img id="msc_box_layout" ' +
				'title = "' + gM( 'rsd_box_layout' ) + '" ' +
				'src = "' +  defaultBoxUrl + '" ' +
				'style="width:20px;height:20px;cursor:pointer;"> ' +
			'<img id="msc_list_layout" ' +
				'title = "' + gM( 'rsd_list_layout' ) + '" ' +
				'src = "' +  defaultListUrl + '" ' +
				'style="width:20px;height:20px;cursor:pointer;">' +
			about_desc +
			'</span>' +
			'<span id="rsd_paging_ctrl" style="float:right;"></span>' +
			'</div>'
		);

		// Get paging with bindings:
		this.showPagingHeader( '#rsd_paging_ctrl' );

		$j( '#msc_box_layout' )
			.hover( 
				function() {
					$j( this ).attr( "src", darkBoxUrl );
				}, 
				function() {
					$j( this ).attr( "src",  defaultBoxUrl );
				} )
			.click( function() {
				$j( this ).attr( "src", darkBoxUrl );
				$j( '#msc_list_layout' ).attr( "src", lightListUrl );
				_this.setDisplayMode( 'box' );
			} );

		$j( '#msc_list_layout' )
			.hover( 
				function() {
					$j( this ).attr( "src", darkListUrl );
				}, 
				function() {
					$j( this ).attr( "src", defaultListUrl );
				} )
			.click( function() {
				$j( this ).attr( "src", darkListUrl );
				$j( '#msc_box_layout' ).attr( "src", lightBoxUrl );
				_this.setDisplayMode( 'list' );
			} );
	},
	
	/**
	* Shows pagging for a given target for a given current_provider
	*
	* @param {String} target jQuery Selector for pagging Header output  
	*/
	showPagingHeader: function( target ) {
		var _this = this;
		if ( _this.current_provider == 'upload' ) {
			var provider = _this.content_providers['this_wiki'];
		} else {
			var provider = _this.content_providers[ _this.current_provider ];
		}
		var search = provider.sObj;
		mw.log( 'showPagingHeader:' + _this.current_provider + ' len: ' + search.num_results );
		var to_num = ( provider.limit > search.num_results ) ?
			( parseInt( provider.offset ) + parseInt( search.num_results ) ) :
			( parseInt( provider.offset ) + parseInt( provider.limit ) );
		var out = '';

		// @@todo we should instead support the wiki number format template system instead of inline calls
		if ( search.num_results != 0 ) {
			if ( search.num_results  >  provider.limit ) {
				out += gM( 'rsd_results_desc_total', [( provider.offset + 1 ), to_num, 
					mw.lang.formatNumber( search.num_results )] );
			} else {
				out += gM( 'rsd_results_desc', [( provider.offset + 1 ), to_num] );
			}
		}
		// check if we have more results (next prev link)
		if ( provider.offset >= provider.limit ) {
			out += ' <a href="#" id="rsd_pprev">' + gM( 'rsd_results_prev' ) + ' ' + provider.limit + '</a>';
		}

		if ( search.more_results ) {
			out += ' <a href="#" id="rsd_pnext">' + gM( 'rsd_results_next' ) + ' ' + provider.limit + '</a>';
		}

		$j( target ).html( out );

		// set bindings
		$j( '#rsd_pnext' ).click( function() {
			provider.offset += provider.limit;
			_this.showCurrentTab();
		} );

		$j( '#rsd_pprev' ).click( function() {
			provider.offset -= provider.limit;
			if ( provider.offset < 0 )
				provider.offset = 0;
			_this.showCurrentTab();
		} );
	},
	
	/**
	* Select a given search provider
	* @param {String} provider_id Provider id to select and display  
	*/
	selectTab: function( provider_id ) {
		mw.log( 'select tab: ' + provider_id );
		this.current_provider = provider_id;
		if ( this.current_provider == 'upload' ) {
			this.showUploadTab();
		} else {
			// update the search results:
			this.showCurrentTab();
		}
	},
	
	/*
	* Sets the dispaly mode
	* @param {String} mode Either "box" or "list" 
	*/	
	setDisplayMode: function( mode ) {
		mw.log( 'setDisplayMode:' + mode );
		this.displayMode = mode;
		// run /update search display:
		this.showResults();
	}
};
