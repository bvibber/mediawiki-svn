/* the upload javascript 
presently does hackery to work with Special:Upload page...
will be replaced with upload API once that is ready
*/

loadGM({ 
	"upload-enable-converter" : "Enable video converter (to upload source video not yet converted to theora format) <a href=\"http://commons.wikimedia.org/wiki/Commons:Firefogg\">more info</a>",
	"upload-fogg_not_installed": "If you want to upload video consider installing <a href=\"http://firefogg.org\">firefogg.org</a>, <a href=\"http://commons.wikimedia.org/wiki/Commons:Firefogg\">more info</a>",
	"upload-transcode-in-progress":"Doing Transcode & Upload (do not close this window)",
	"upload-in-progress": "Upload in Progress (do not close this window)",
	"upload-transcoded-status": "Transcoded",
	"uploaded-status": "Uploaded",
	"upload-select-file": "Select File...",	
	"wgfogg_wrong_version": "You have firefogg installed but its outdated, <a href=\"http://firefogg.org\">please upgrade</a> ",
	"wgfogg_waring_ogg_upload": "You have selected an ogg file for conversion to ogg (this is probably unnessesary). Maybe disable the video converter?",
	"wgfogg_waring_bad_extension" : "You have selected a file with an unsuported extension. <a href=\"http://commons.wikimedia.org/wiki/Commons:Firefogg#Supported_File_Types\">More help</a>",
	"upload-stats-fileprogres": "$1 of $2",
	
	"mv_upload_done" 	  : "Your upload <i>should be</i> accessible <a href=\"$1\">here</a>",
	"upload-unknown-size": "Unknown size",	
	
	"successfulupload" : "Successful upload",
	"uploaderror" : "Upload error",
	"uploadwarning": "Upload warning",
	"unknown-error": "Unknown Error",
	"return-to-form": "Return to form",
	
	"file-exists-duplicate" : "This file is a duplicate of the following file",
	"fileexists" : "A file with this name exists already, please check <b><tt>$1</tt></b> if you are not sure if you want to change it.",
	"fileexists-thumb": "<center><b>Existing file</b></center>",
	"ignorewarning" : "Ignore warning and save file anyway",
	"file-thumbnail-no" :  "The filename begins with <b><tt>$1</tt></b>"	
});

var default_upload_options = {
	'target_div':'',
	'upload_done_action':'redirect',
	'api_url':false
}

var mvUploader = function(initObj){
	return this.init( initObj );
}
mvUploader.prototype = {
	init:function( iObj ){
		var _this = this;	
		js_log('init uploader');
		if(!iObj)
			iObj = {};
		for(var i in default_upload_options){
			if(iObj[i]){
				this[i] = iObj[i];
			}else{
				this[i] = default_upload_options[i];
			}
		}
		//check if we are on the uplaod page: 
		this.on_upload_page = ( wgPageName== "Special:Upload")?true:false;					
		js_log('f:mvUploader: onuppage:' + this.on_upload_page);
		//grab firefogg.js: 
		mvJsLoader.doLoad({
				'mvFirefogg' : 'libAddMedia/mvFirefogg.js'
			},function(){
				//if we are not on the upload page grab the upload html via ajax:
				//@@todo refactor with 		
				if( !_this.on_upload_page){					
					$j.get(wgArticlePath.replace(/\$1/, 'Special:Upload'), {}, function(data){
						//add upload.js: 
						$j.getScript( stylepath + '/common/upload.js', function(){ 	
							//really _really_ need an "upload api"!
							wgAjaxUploadDestCheck = true;
							wgAjaxLicensePreview = false;
							wgUploadAutoFill = true;									
							//strip out inline scripts:
							sp = data.indexOf('<div id="content">');
							se = data.indexOf('<!-- end content -->');	
							if(sp!=-1 && se !=-1){		
								result_data = data.substr(sp, (se-sp) ).replace('/\<script\s.*?\<\/script\>/gi',' ');
								js_log("trying to set: " + result_data );																			
								//$j('#'+_this.target_div).html( result_data );
							}						
							_this.setupFirefogg();
						});	
					});				
				}else{
					//@@could check if firefogg is enabled here: 
					_this.setupFirefogg();			
					//if only want httpUploadFrom help enable it here: 		
				}							
			}
		);
	},
	/**
	 * setupBaseUpInterface supports intefaces for progress indication if the browser supports it
	 * also sets up ajax progress updates for http posts
	 * //pre
	 */	 
	setupBaseUpInterface:function(){	
		//check if this feature is not false (we want it on by default (null) instances that don't have the upload api or any modifications)  			
		this.upForm = new mvBaseUploadInterface( {
				'api_url' : this.api_url,
				'parent_uploader': this
			} 
		);		
		this.upForm.setupForm();		
	},
	setupFirefogg:function(){
		var _this = this;
		//add firefogg html if not already there: ( same as $wgEnableFirebug added in SpecialUpload.php )  
		if( $j('#fogg-video-file').length==0 ){
			js_log('add addFirefoggHtml');
			_this.addFirefoggHtml();
		}else{
			js_log('firefogg already init:');					
		}	
		//set up the upload_done action 
		//redirect if we are on the upload page  
		//do a callback if in called from gui) 
		var intFirefoggObj = ( this.on_upload_page )? 
				{'upload_done_action':'redirect'}:
				{'upload_done_action':function( rTitle ){
						js_log( 'add_done_action callback for uploader' );
						//call the parent insert resource preview	
						_this.upload_done_action( rTitle );		
					}
				};
				
		if( _this.api_url )
			intFirefoggObj['api_url'] =  _this.api_url;
		
		js_log('new mvFirefogg  extends mvUploader (this)');		
		this.fogg = new mvFirefogg( intFirefoggObj );		
		this.fogg.setupForm();					
	},
	//same add code as specialUpload if($wgEnableFirefogg){
	addFirefoggHtml:function(){		
		var itd_html = $j('#mw-upload-table .mw-input:first').html();			
		$j('#mw-upload-table .mw-input').eq(0).html('<div id="wg-base-upload">' + itd_html + '</div>');
		//add in firefogg control			
		$j('#wg-base-upload').after('<p id="fogg-enable-item" >' + 
			'<input style="display:none" id="fogg-video-file" name="fogg-video-file" type="button" value="' + gM('upload-select-file') + '">' +
			"<span id='wgfogg_not_installed'>" + 
				gM('upload-fogg_not_installed') +
			"</span>" +
			"<span class='error' id='wgfogg_wrong_version'  style='display:none;'><br>" +
				gM('wgfogg_wrong_version') +
			"<br>" +
			"</span>" +
			"<span class='error' id='wgfogg_waring_ogg_upload' style='display:none;'><br>"+
				gM('wgfogg_waring_ogg_upload') +
			"<br>" +
			"</span>" + 
			"<span class='error' id='wgfogg_waring_bad_extension' style='display:none;'><br>"+
				gM('wgfogg_waring_bad_extension') + 						
			"<br>" +
			"</span>" +  
			"<span id='wgfogg_installed' style='display:none' >"+
				'<input id="wgEnableFirefogg" type="checkbox" name="wgEnableFirefogg" >' + 							
					gM('upload-enable-converter') +
			'</span><br></p>');					
	},
	/**
	 * doDestCheck checks the destination
	 * @@todo we should be able to configure its "targets" via parent config
	 */
	doDestCheck:function(){		
		var _this = this;
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
			},
			'url': _this.api_url
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
	},
	/**
	 * doDestinationFill fills in a destination file-name based on a source asset name. 
	 * @@todo we should be able to configure its "targets" via parent config
	 */
	doDestinationFill:function( targetElm ){
		js_log("doDestinationFill")
		//remove any previously flagged errors
		$j('#mw-upload-permitted,#mw-upload-prohibited').hide();					
		
		var path = $j(targetElm).val();
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
		if( wgFileExtensions && $j(targetElm).attr('id') != 'wpUploadFileURL' ){		
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
				$j(targetElm).val('');
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
		this.doDestCheck();
	}
}
/**
 * the base Upload Interface extended via firefogg 
 */
var default_bui_options = {
	'api_url':null,
	'parent_uploader':null
}
var mvBaseUploadInterface = function( iObj ){
	return this.init( iObj );
}
mvBaseUploadInterface.prototype = {
	parent_uploader:false,
	formData:{}, //the form to be submitted
	upload_mode:'autodetect', 	//can be 'post', 'chunks' or autodetect. (autodetect issues an api call)   
	warnings_sessionkey:null,
	chunks_supported:false,
	form_post_override:false,
	init: function( iObj ){
		if(!iObj)
			iObj = {};
		//inherit iObj properties:
		for(var i in default_bui_options){
			if(iObj[i]){
				this[i] = iObj[i];
			}else{
				this[i] = default_bui_options[i];
			}
		}		
	},
	setupForm:function(){
		var _this = this;
		//set up the local pointer to the edit form:
		_this.getEditForm();
						
		if(_this.editForm){
			//set up the org_onsubmit if not set: 
			if( typeof( _this.org_onsubmit ) == 'undefined' )
				_this.org_onsubmit = _this.editForm.onsubmit;
						
			//have to define the onsubmit function inline or its hard to pass the "_this" instance
			_this.editForm.onsubmit = function(){								
				//run the original onsubmit (if not run yet set flag to avoid excessive chaining ) 
				if( typeof( _this.org_onsubmit ) == 'function' ){										  
					if( ! _this.org_onsubmit() ){
						//error in org submit return false;
						return false;					
					}
				}				
				//check for post action override: 															
				if( _this.form_post_override ){
					//alert('woudld submit here');
					return true;
				}									
				//get the input form data in flat json: 										
				var tmpAryData = $j( _this.editForm ).serializeArray();					
				for(var i=0; i < tmpAryData.length; i++){
					if( tmpAryData[i]['name'] )
						_this.formData[ tmpAryData[i]['name'] ] = tmpAryData[i]['value'];
				}							
						
				//get a clean loader: 
				_this.dispProgressOverlay();												
				
				//for some unknown reason we have to drop down the #p-search z-index:
				$j('#p-search').css('z-index', 1);								
				
				//select upload mode: 				
				_this.detectUploadMode();
				
				//don't submit the form we will do the post in ajax
				return false;	
			};							
		}
					
	},	
	detectUploadMode:function( callback ){
		var _this = this;
		js_log('detectUploadMode::' + _this.upload_mode + ' api:' + _this.api_url);
		//check the upload mode: 
		if( _this.upload_mode == 'autodetect' ){
			if( ! _this.api_url )
				return js_error( 'Error: can\'t autodetect mode without api url' );
			do_api_req( {
				'data':{ 'action':'paraminfo','modules':'upload' },
				'url' :_this.api_url 
			}, function(data){
				if( typeof data.paraminfo == 'undefined' || typeof data.paraminfo.modules == 'undefined' )
					return js_error( 'Error: bad api results' );
				if( typeof data.paraminfo.modules[0].classname == 'undefined'){
					js_log( 'Autodetect Upload Mode: \'post\' ');
					_this.upload_mode = 'post';
				}else{		
					js_log( 'Autodetect Upload Mode: api ' );
					_this.upload_mode = 'api';
					//check to see if chunks are supported:			
					for( var i in data.paraminfo.modules[0].parameters ){						
						var pname = data.paraminfo.modules[0].parameters[i].name;
						if( pname == 'enablechunks' ){
							js_log( 'this.chunks_supported = true' );
							_this.chunks_supported = true;							
							break;
						}
					}																
				}				
				_this.doUploadSwitch();
			});
		}else{
			_this.doUploadSwitch();
		}
	},
	doUploadSwitch:function(){		
		
		js_log('mvUPload:doUploadSwitch()');
		var _this = this;			
		//issue a normal post request 		
		if( _this.upload_mode == 'post' || $j('#wpSourceTypeFile').get(0).checked ){			
			js_log('do normal submit form');
			//update the status
			_this.updateEmptyLoadingStatus();		
						
			//do normal post upload no status indicators (also since its a file I think we have to submit the form)
			_this.form_post_override = true;
			
			//trick the browser into thinking the wpUpload button was pressed (there might be a cleaner way to do this) 
			$j(_this.editForm).append('<input type="hidden" name="wpUpload" value="' + $j('#wpUpload').val() + '"/>');
			
			//@@todo support firefox 3.0 ajax file upload progress
			//http://igstan.blogspot.com/2009/01/pure-javascript-file-upload.html
			
			//do the submit :			
			_this.editForm.submit();
			return true;
		}else if( _this.upload_mode == 'api' && $j('#wpSourceTypeURL').get(0).checked){	
			js_log('doHttpUpload (no form submit) ');
			//if the api is supported.. && source type is http do upload with http status updates
			_this.doHttpUpload();			
		}else{
			js_error( 'Error: unrecongized upload mode: ' + _this.upload_mode );
		}				
		return false;
	},
	doHttpUpload:function(){
		var _this = this;
		//set the http box to loading (in case we don't get an update for some time) 
		$j('#dlbox-centered').html( '<h5>' + _this.getProgressTitle() + '</h5>' + 
			mv_get_loading_img( 'left:40%;top:20%')
		);	
		//build the api query:	
		do_api_req({
			'data':{ 
				'action'	: 'upload',
				'url'		: $j('#wpUploadFileURL').val(),
				'filename'	: $j('#wpDestFile').val(),
				'comment' 	: $j('#wpUploadDescription').val(),
				'asyncdownload': 'true'
			},
			'url' : _this.api_url 
		}, function( data ){
			_this.processApiResult( data );		
		});			
	},
	doAjaxWarningIgnore:function(){
		var _this = this;
		if( !_this.upload_session_key )
			return js_error('missing upload_session_key (can\'t ignore warnigns');
		//do the ignore warnings submit to the api: 
		do_api_req({
			'data':{
				'ignorewarnings' : 'true',
				'sessionkey'	 :!_this.upload_session_key
			},
			'url': _this.api_url
		},function(data){
			_this.processApiResult(data);
		});
	},
	doAjaxUploadStatus:function() {
		var _this = this;	
		
		//set up the progress display for status updates: 
		_this.dispProgressOverlay();
		
		var uploadStatus = function(){
			//do the api request: 
			do_api_req({
				'data':{
					'action'	 : 'upload',
					'httpstatus' : 'true',
					'sessionkey' : _this.upload_session_key
				},
				'url' : _this.api_url
			}, function( data ){									
				//@@check if we are done
				if( data.upload['apiUploadResult'] ){
					//update status to 100%
					_this.updateProgress( 1 );
					if(typeof JSON == 'undefined'){
						//we need to load the jQuery json parser: (older browsers don't have JSON.parse 
						mvJsLoader.doLoad({
							'$j.secureEvalJSON':'jquery/plugins/jquery.json-1.3.js'
						},function(){
							var  apiResult = $j.secureEvalJSON( data.upload['apiUploadResult'] );
							_this.processApiResult( apiResult );
						});
					}else{
						var apiResult = {};
						try{
							apiResult = JSON.parse ( data.upload['apiUploadResult'] ) ;
						}catch (e){
							//could not parse api result
							js_log('errro: could not parse apiUploadResult ')						
						}
						_this.processApiResult( apiResult );						
					}
					return ;				
				}
				
				//@@ else update status:
				if( data.upload['content_length'] &&  data.upload['loaded'] ){
					//we have content length we can show percentage done: 
					var perc =  data.upload['loaded'] / data.upload['content_length'];
					//update the status:
					_this.updateProgress( perc );
					//special case update the file progress where we have data size: 
					$j('#upload-stats-fileprogres').html( 
						gM('upload-stats-fileprogres', [ 
							formatSize( data.upload['loaded'] ), 
							formatSize( data.upload['content_length'] )
							]  
						)
					);
				}else if( data.upload['loaded'] ){					
					//for lack of content-length requests: 
					$j('#upload-stats-fileprogres').html( 
						gM('upload-stats-fileprogres', [
							formatSize( data.upload['loaded'] ),
							gM('upload-unknown-size')
							]
						)
					);
				}
				//(we got a result) set it to 100ms + your server update interval (in our case 2s)
				setTimeout(uploadStatus, 2100); 		
			});			
		}
		uploadStatus();
	},
	processApiResult: function( apiRes ){	
		var _this = this;			
		//check for upload api error:
		// {"upload":{"result":"Failure","error":"unknown-error","code":{"status":5,"filtered":"NGC2207%2BIC2163.jpg"}}}
		if( apiRes.error || ( apiRes.upload && apiRes.upload.result == "Failure" ) ){
			
			//check a few places for the error code: 
			var error_code=0;
			if( apiRes.error && apiRes.error.code ){				
				error_code = apiRes.error.code;
			}else if( apiRes.upload.code ){
				if(typeof apiRes.upload.code == 'object'){				
					if(apiRes.upload.code[0]){
						error_code = apiRes.upload.code[0];
					}
					if(apiRes.upload.code['status']){
						error_code = apiRes.upload['status'];
					}
				}else{
					apiRes.upload.code;
				}
			}	
			
			var error_msg = '';		
			if(typeof apiRes.error == 'string')
				error_msg = apiRes.error;		
			//error space is too large so we don't front load it
			//do a remote call to get the error msg: 
			if(!error_code || error_code == 'unknown-error'){
				js_log('Error: apiRes: ' + JSON.stringify( apiRes) );
				_this.updateUploadError( gM('unknown-error') + '<br>' + error_msg);	
			}else{
				gMsgLoadRemote(error_code, function(){
					js_log('send msg: ' + gM( error_code ));
					_this.updateUploadError( gM( error_code ));
				});
			}				
			return ;		
		}
		//check for upload_session key for async upload:
		if( apiRes.upload && apiRes.upload.upload_session_key ){							
			//set the session key
			_this.upload_session_key = apiRes.upload.upload_session_key;
			js_log("set session key: " + _this.upload_session_key);
			//do ajax upload status: 
			_this.doAjaxUploadStatus();			
			return ;
		}		
		
		if( apiRes.upload.imageinfo &&  apiRes.upload.imageinfo.descriptionurl ){
			_this.updateUploadDone( apiRes.upload.imageinfo.descriptionurl );
			return ;
		}		
				
		//check for upload error: 
		if( apiRes.upload && apiRes.upload.error){
			js_log(' apiRes.upload.error: ' +  apiRes.upload.error );
			return ;
		}
		//check for known warnings: 
		if( apiRes.upload.warnings ){	
			var wmsg = '<ul>';
			for(var wtype in apiRes.upload.warnings){
				var winfo = apiRes.upload.warnings[wtype]
				wmsg+='<li>';		
				switch(wtype){
					case 'duplicate':
					case 'exists':
						wmsg += gM('file-exists-duplicate') +' '+ 
									'<b>' + winfo[1].title.mTextform + '</b>';									  					
					break;
					case 'file-thumbnail-no':
						wmsg += gM('file-thumbnail-no', winfo);
					break;
					default:
						wmsg += gM('upload-misc-error') + ' ' + wtype;
					break;
				}			
				wmsg+='</li>';														
			}
			wmsg+='</ul>';			
			if( apiRes.upload.warnings.sessionkey)
			 	_this.warnings_sessionkey = apiRes.upload.warnings.sessionkey;			 	
			_this.updateUploadWarning( wmsg );
			return false;
		}							
		
		//nothing fits assume unkown error:
		js_log('could not parse upload api request result');
		_this.updateUploadError( gM('unknown-error'));
		return false; 
		
	},
	updateUploadWarning:function( msg ){
		$j( '#dlbox-centered' ).html( '<h3>' + gM('uploadwarning') + '</h3>' +
				msg + '<p>' + 
				'<a id="mv-ignore-warnings">' + gM('ignorewarning') + '</a>'  
		);
		//setup ignore warnings binding and ajax query:
	},	
	updateUploadError:function( msg ){
		$j( '#dlbox-centered' ).html( '<h3>' + gM('uploaderror') + '</h3>' +
			msg  + '<p>' + 
			'<a id="mv-return-to-form" href="#" >' + gM('return-to-form') + '</a>');	
		$j('#mv-return-to-form').click(function(){
			//hide / close up shop
			$j('#dlbox-overlay,#dlbox-centered').hide();
			return false;
		});
	},	
	updateUploadDone:function( url ){
		$j( '#dlbox-centered' ).html( '<h3>' + gM('successfulupload') + '</h3>' +
			gM( 'mv_upload_done', url) );	
	},
	updateEmptyLoadingStatus:function(){
		$j('#dlbox-centered').html( '<h5>' + _this.getProgressTitle() + '</h5>' + 
			mv_get_loading_img( 'left:40%;top:20%')
		);
	},
	getProgressTitle:function(){
		return gM('upload-in-progress');
	},	
	getEditForm:function(){
		this.editForm = $j( '#mw-upload-form' ).get(0);
	},
	updateProgress:function( perc ){		
		$j( '#up-progressbar' ).css( 'width', parseInt( perc * 100 ) + '%' );		
		$j( '#up-pstatus' ).html( parseInt( perc * 100 ) + '% - ' );
	},
	dispProgressOverlay:function(){
		var _this = this;
		//remove old instance: 
		$j('#dlbox-centered,#dlbox-overlay').remove(); 	
		//hard code style (since not always easy to import style sheets)
		$j('body').append('<div id="dlbox-centered" class="dlbox-centered" style="'+
				'position:fixed;background:#DDD;border:3px solid #AAA;font-size:115%;width:40%;'+
				'height:300px;padding: 10px;z-index:100;top:100px;bottom:40%;left:20%;" >'+		
					'<h5>' + _this.getProgressTitle() + '</h5>' +
					'<div id="up-pbar-container" style="border:solid thin gray;width:90%;height:15px;" >' +
						'<div id="up-progressbar" style="background:#AAC;width:0%;height:15px;"></div>' +			
					'</div>' +
					'<span id="up-pstatus">0% - </span> ' +						 
					'<span id="up-status-state">' + gM('uploaded-status') + '</span> ' +				
					'<span id="upload-stats-fileprogres"></span>'+		
			'</div>' +					
			'<div id="dlbox-overlay" class="dlbox-overlay" style="background:#000;cursor:wait;height:100%;'+
						'left:0;top:0;position:fixed;width:100%;z-index:99;filter:alpha(opacity=60);'+
						'-moz-opacity: 0.6;	opacity: 0.6;" ></div>');		
	}	
}
