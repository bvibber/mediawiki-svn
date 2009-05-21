/**
 * the base Upload Interface for uploading.
 * 
 * this base uploader is optionally extended by firefogg
 */
 
 loadGM({ 
	"upload-transcode-in-progress":"Doing Transcode & Upload (do not close this window)",
	"upload-in-progress": "Upload in Progress (do not close this window)",
	"upload-transcoded-status": "Transcoded",
	"uploaded-status": "Uploaded",
	
	"wgfogg_wrong_version": "You have firefogg installed but its outdated, <a href=\"http://firefogg.org\">please upgrade</a> ",
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
 
 
var default_bui_options = {
	'api_url':null,
	'parent_uploader':null,
	'edit_from':null
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
		_this.editForm = _this.getEditForm();

		if(_this.editForm){
			//set up the org_onsubmit if not set: 
			if( typeof( _this.org_onsubmit ) == 'undefined' )
				_this.org_onsubmit = _this.editForm.onsubmit;
			js_log('should overwite onsubmit here::');
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
				//put into a try catch so we are sure to return false: 		
				try{
					//get a clean loader: 
					_this.dispProgressOverlay();												
					
					//for some unknown reason we have to drop down the #p-search z-index:
					$j('#p-search').css('z-index', 1);								
					
					//select upload mode: 				
					_this.detectUploadMode();
				}catch(e){}
				 
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
				'asyncdownload': true				
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
				if(typeof JSON != 'undefined'){
					js_log('Error: apiRes: ' + JSON.stringify( apiRes) );
				}
				_this.updateUploadError( gM('unknown-error') + '<br>' + error_msg);	
			}else{
				gMsgLoadRemote(error_code, function(){
					js_log('send msg: ' + gM( error_code ));
					_this.updateUploadError( gM( error_code ));
				});
			}		
			js_log("api.erorr");		
			return ;		
		}
		//check for upload_session key for async upload:
		if( apiRes.upload && apiRes.upload.upload_session_key ){							
			//set the session key
			_this.upload_session_key = apiRes.upload.upload_session_key;
			
			//do ajax upload status: 
			_this.doAjaxUploadStatus();		
			js_log("set upload_session_key: " + _this.upload_session_key);	
			return ;
		}		
		
		if( apiRes.upload.imageinfo &&  apiRes.upload.imageinfo.descriptionurl ){
			_this.updateUploadDone( apiRes.upload.imageinfo.descriptionurl );
			js_log('apiRes.upload.imageinfo:: updateUploadDone');
			return ;
		}		
				
		//check for upload error: 
		if( apiRes.upload && apiRes.upload.error){
			js_log(' apiRes.upload.error: ' +  apiRes.upload.error );
			return ;
		}
		//check for known warnings: 
		if( apiRes.upload.warnings ){
			//debugger;	
			var wmsg = '<ul>';
			for(var wtype in apiRes.upload.warnings){
				var winfo = apiRes.upload.warnings[wtype]
				wmsg+='<li>';		
				switch(wtype){
					case 'duplicate':
					case 'exists':
						if(winfo[1] && winfo[1].title && winfo[1].title.mTextform){
							wmsg += gM('file-exists-duplicate') +' '+ 
									'<b>' + winfo[1].title.mTextform + '</b>';		
						}else{
							//misc error (weird that winfo[1] not present
							wmsg += gM('upload-misc-error') + ' ' + wtype;
						}							  					
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
		if(this.target_edit_from){
			return $j(this.target_edit_from).get(0);
		}
		//just return the first form fond on the page. 
		return $j('form :first').get(0);
	},
	updateProgress:function( perc ){		
		js_log('updateProgress::' + perc);
		$j( '#up-progressbar' ).css( 'width', parseInt( perc * 100 ) + '%' );		
		$j( '#up-pstatus' ).html( parseInt( perc * 100 ) + '% - ' );
	},
	/*update to jQuery.ui progress display type */
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
		//fade them in:
		$j('#dlbox-centered,#dlbox-overlay').show();
	}	
}
