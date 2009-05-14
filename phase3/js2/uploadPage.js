
loadGM({
	"fileexists" : "A file with this name exists already, please check <i><tt>$1</tt></i> if you are not sure if you want to change it.",
	"fileexists-thumb": "<center><b>Existing file<b></center>"
});

// simple script to invoke the upload with config:  
mwAddOnloadHook( function(){
	//setup the upload handler for firefogg and normal uploads: 
	mvJsLoader.doLoad( {
		'mvFirefogg' : 'libAddMedia/mvFirefogg.js',	
		'mvUploader' : 'libAddMedia/mvUploader.js'
	},function(){		
		mvUp = new mvUploader( { 'api_url' : wgServer + wgScriptPath + '/api.php' } );
		setupUploadFormBindings();		
	});
	//set up the upoload form bindings once all dom manipluation is done
	var setupUploadFormBindings = function(){
		if( wgAjaxUploadDestCheck ){
			//do destination check: 
			var doDestCheck = function(){
				$j('#wpDestFile-warning').empty();
				//show loading
				$j('#wpDestFile').after('<img id = "mw-spinner-wpDestFile" src ="'+ stylepath + '/common/images/spinner.gif" />');
				//try and get a thumb of the current file (check its destination)				
				do_api_req({
					'data':{ 
						'titles': 'File:' + $j('#wpDestFile').val(),//@@todo we may need a more clever way to get a the filename
						'prop':  'imageinfo',
						'iiprop':'url|mime|size',
						'iiurlwidth': 150
					} 
				},function(data){
					$j('#mw-spinner-wpDestFile').remove();
					if(data && data.query && data.query.pages){
						if( data.query.pages[-1] ){
							//all good no file there
						}else{
							for(var page_id in data.query.pages){
								if( data.query.normalized){
									var ntitle = data.query.normalized[0].to;
								}else{
									var ntitle = data.query.pages[ page_id ].title;
								}	
								var img = data.query.pages[ page_id ].imageinfo[0];								
								$j('#wpDestFile-warning').html(
									'<ul>' +
										'<li>'+
											gM('fileexists', ntitle) + 
										'</li>'+
										'<div class="thumb tright">' +
											'<div style="width: ' + ( parseInt(img.thumbwidth)+2 ) + 'px;" class="thumbinner">' +
												'<a title="' + ntitle + '" class="image" href="' + img.descriptionurl + '">' +
													'<img width="' + img.thumbwidth + '" height="' + img.thumbheight + '" border="0" class="thumbimage" ' +
													'src="' + img.thumburl + '"' +
													'	 alt="' + ntitle + '"/>' +
												'</a>' +
												'<div class="thumbcaption">' +
													'<div class="magnify">' +
														'<a title="' + gM('thumbnail-more') + '" class="internal" ' +
															'href="' + img.descriptionurl +'"><img width="15" height="11" alt="" ' +
															'src="' + stylepath +"/>" +
														'</a>'+
													'</div>'+
													gM('fileexists-thumb') +
												'</div>' +
											'</div>'+
										'</div>' +
									'</ul>'
								);
							}
						}
					}
				});			
			};
			$j('#wpDestFile').change(doDestCheck);
		}		
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
	
		
		$j('#wpUploadFile,#wpUploadFileURL').focus(function(){		
			toggleUpType( this.id == 'wpUploadFile' );	
		}).change(function(){ //also setup the onChange event binding: 				
			if (!wgUploadAutoFill) {
				return;
			}
			//remove any previously flagged errors
			$j('#mw-upload-permitted,#mw-upload-prohibited').hide();					
			
			var path = $j(this).val();
			// Find trailing part
			var slash = path.lastIndexOf('/');
			var backslash = path.lastIndexOf('\\');
			var fname;
			if (slash == -1 && backslash == -1) {
				fname = path;
			} else if (slash > backslash) {
				fname = path.substring(slash+1, 10000);
			} else {
				fname = path.substring(backslash+1, 10000);
			}		
			//urls are less likely to have a usefull extension don't include them in the extention check
			if( wgFileExtensions && $j(this).attr('id') != 'wpUploadFileURL' ){		
				var found = false;		
				if( fname.lastIndexOf('.')!=-1 ){		
					var ext = fname.substr( fname.lastIndexOf('.')+1 );			
					for(var i=0; i < wgFileExtensions.length; i++){						
						if(  wgFileExtensions[i].toLowerCase()   ==  ext.toLowerCase() )
							found = true;
					}
				}
				if(!found){
					//clear the upload set mw-upload-permitted to error
					$j(this).val('');
					$j('#mw-upload-permitted,#mw-upload-prohibited').show().addClass('error');												
					//clear the wpDestFile as well: 
					$j('#wpDestFile').val('');								
					return false;
				}		
			}				
			// Capitalise first letter and replace spaces by underscores
			fname = fname.charAt(0).toUpperCase().concat(fname.substring(1,10000)).replace(/ /g, '_');	
			// Output result
			$j('#wpDestFile').val( fname );		
			//do a destination check 
			doDestCheck();
		});
	}
});