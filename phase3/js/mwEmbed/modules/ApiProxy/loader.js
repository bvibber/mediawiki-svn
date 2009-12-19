/* apiProxy Loader */

mw.addClassFilePaths( {
	"mw.proxy"		: "modules/ApiProxy/mw.proxy.js",
	"JSON"			: "modules/ApiProxy/json2.js"
} );

mw.addModuleLoader( 'ApiProxy', function( callback ){
	mw.load( [
		'mw.proxy',
		'JSON'
	], function() {
		callback( 'ApiProxy' );
	});
});
