/**
* Simple inline section edit example  h3 ->h3 h2
*/ 
mw.ready(function(){
	$j( '.editsection' ).click(function(){		
		// Get section number: 
		var href = $j( this ).children('a').attr( 'href');
		var sectionNumber = mw.parseUri( href ).queryKey['section'];
		
		// Get the H tag
		var $htop = $j(this).parents( 'h1,h2,h3,h4,h5,h6' ).filter(':first');
		var htagName = $htop.get(0).tagName;		
		var hlevel = parseInt( htagName.replace(/h/i, '') );
		
		//Set up the list of h tags that we break on: 
		hSet = [];
		for( var i = hlevel; i !=0 ; i -- ){
			hSet.push( 'h'+i );
		}
		
		// Remove all child elements until we reach the same level or higher
		$j(this).parent().nextAll().each(function(){			
			//Break if we reach the same h or higher
			for(var i =0; i < hSet.length; i++){
				if( $j(this).is( hSet[i] ) ) {
					return false;
				}
			};
			//Break if we reach the "end"
			if( $j(this).is( '.visualClear' ) ){
				return false;
			}
			
			// Otherwise remove the nodes until we find any of the above
			$j(this).remove();
		});
		
		// Add the section edit loader
		$htop.before( 
			$j('<div>')
			.addClass("sectionEdit")
			.attr('id', 'edit_section_' + sectionNumber )
			.loadingSpinner()
		)
		// Remove the parent htag	
		.remove();
		
		// Here we would setup the configuration something like: 
		//mw.setConfig('WikiEditor.modlue.{modName}', true );
		
		// @@NOTE: should refactor to use  mw.{set|get}Config instead of all this 
		// global context specific stuff 
		window.wgWikiEditorEnabledModules = {
			"highlight": true,
			"preview": true,
			"toc": true,
			"toolbar": true,
			"global": true
		};
		window.wgWikiEditorPreferences = {
			"highlight": {
				"enable": "1"
			},
			"preview": {
				"enable": "1"
			},
			"toc": {
				"enable": "1"
			},
			"toolbar": {
				"enable": "1",
				"dialogs": "1"
			}
		};
		window.wgWikiEditorIconVersion=0;
		window.wgNavigableTOCResizable=null;
		
		
		//Grab the section text from the api: 
		mw.getJSON({
			'prop' : 'revisions',
			'titles' : wgTitle,
			'rvprop' : 'content',
			'rvsection' : sectionNumber
		}, function( data ){
			if( data.query && data.query.pages ){
				for ( var i in data.query.pages ) {
					var page = data.query.pages[i];
					if ( page.revisions[0]['*'] ){
						doInlineWikiEditor( page.revisions[0]['*'],  sectionNumber ); 
					}
				}
			}
			//@@todo error out  			
		});				
		// don't folow the link:
		return false;		
	});
});
function doInlineWikiEditor( wikiText,  sectionNumber ){
	// Here we have to plop in a hidden wikitext box identical to edit page
	// We don't want to build out custom config 
	// ( too much config is stored in wikiEditor/Toolbar/Toolbar.js )
	$j('#edit_section_' + sectionNumber).append( 
		$j('<textarea>')
		.attr( {
		 	'name' : "wpTextbox1",
		 	'id' : "wpTextbox1", 
		 	'cols' : "80",
		 	'rows' : "20",
		 	'tabindex' : "1",
		 	'accesskey' : ","
		 } )
		 .val( wikiText )
		 .hide()
	);		
	// Load the wikitext module:
	mw.load( 'WikiEditor' , function(){	
		//Remove the loader ( callback on mw.load('WikiEditor') is having trouble atm ) 
		$j('.loading_spinner').remove();			
		// show the editor: 
		$j('#wpTextbox1').show( 'fast' );
		// add the add-media-wizard binding" via editPage js
		mw.load( 'editPage', function(){
			mw.log("editPage loaded");
		});
	});
}