/* apiProxy Loader */

mw.addClassFilePaths( {
	"mw.ApiProxy"	: "modules/ApiProxy/mw.ApiProxy.js",
	"JSON"			: "modules/ApiProxy/json2.js"
} );

mw.addModuleLoader( 'ApiProxy', function( callback ) {
	mw.load( [
		'mw.ApiProxy',
		'JSON'
	], function() {
		callback( 'ApiProxy' );
	});
});
