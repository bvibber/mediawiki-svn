/* apiProxy Loader / config */

// Wrap in mw to not pollute global namespace
( function( mw ) {
	
	// Set the default allowable domains for api proxy
	mw.setDefaultConfig({
		// Black list domains
		'ApiProxy.DomainBlackList' : [],
		                              
		// White list domains
		'ApiProxy.DomainWhiteList' 	: [ 
			'localhost', 
			'127.1.1.100' 
		]
	});
	
} )( window.mw );