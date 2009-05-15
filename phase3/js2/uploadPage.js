/*
 * uploadPage.js to be run on specialUpload page.
 * controls the invocation of the mvUploader class based on local config. 
 */  
mwAddOnloadHook( function(){
	//setup the upload handler for firefogg and normal uploads: 
	mvJsLoader.doLoad( {
		'mvFirefogg' : 'libAddMedia/mvFirefogg.js',	
		'mvUploader' : 'libAddMedia/mvUploader.js'
	},function(){		
		mvUp = new mvUploader( { 'api_url' : wgServer + wgScriptPath + '/api.php' } );
		setupUploadFormBindings( mvUp );		
	});
});
//set up the upoload form bindings once all dom manipluation is done
function setupUploadFormBindings( mvUp ){
	if( wgAjaxUploadDestCheck ){
		//do destination check: 		
		$j('#wpDestFile').change( mvUp.doDestCheck );
	}			
	//check if we have http enabled & setup enable/disable toggle:
	if($j('#wpUploadFileURL').length != 0){
		var toggleUpType = function( set ){
			$j('#wpSourceTypeFile').get(0).checked = set;
			$j('#wpUploadFile').get(0).disabled = !set;
			
			$j('#wpSourceTypeURL').get(0).checked = !set;
			$j('#wpUploadFileURL').get(0).disabled =  set;
		}		
		//set the initial toggleUpType		
		toggleUpType(true);	
		
		$j("input[name='wpSourceType']").click(function(){			
			toggleUpType( this.id == 'wpSourceTypeFile' );
		});	
	}
	
	$j('#wpUploadFile,#wpUploadFileURL').focus(function(){		
		toggleUpType( this.id == 'wpUploadFile' );	
	}).change(function(){ //also setup the onChange event binding: 				
		if ( wgUploadAutoFill ) {
			mvUp.doDestinationFill( this );		
		}		
	});
}
