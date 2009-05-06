/* adds firefogg support. 
* autodetects: new upload api or old http POST.  
 */


var default_firefogg_options = {
	'upload_done_action':'redirect',
	'fogg_enabled':false,
	'api_url':null
}
var mvFirefogg = function(initObj){
	return this.init( initObj );
}
mvFirefogg.prototype = { //extends mvBaseUploadInterface

	min_firefogg_version : '0.9.5',
	fogg_enabled : false, 			//if firefogg is enabled or not. 	
	encoder_settings:{			//@@todo allow server to set this 
		'maxSize': 400, 
		'videoBitrate': 400,
		'noUpscaling':true
	},	
	
	init: function( iObj ){
		if(!iObj)
			iObj = {};
		//inherit iObj properties:
		for(var i in default_firefogg_options){
			if(iObj[i]){
				this[i] = iObj[i];
			}else{
				this[i] = default_firefogg_options[i];
			}
		}
		var myBUI = new mvBaseUploadInterface( iObj );
		//standard extends code: 
		for(var i in myBUI){			
			if(this[i]){
				this['pe_'+ i] = myBUI[i];
			}else{
				this[i] =  myBUI[i];
			}
		}			
	},
	setupForm: function(){		
		var _this = this;		
		//call the parent form setup
		_this.pe_setupForm();
		
		//do all firefogg form setup:
		if(typeof(Firefogg) == 'undefined'){ 
			$j('#wgfogg_not_installed').show();
			return false;
		}
		//make sure all the error msgs are hidden: 
		$j('#wgfogg_not_installed,#wgfogg_wrong_version').hide();
		
		//show firefogg enabler: 
		$j('#wgfogg_installed,#wgEnableFirefogg').show();
		
		if( $j('#wgEnableFirefogg').length > 0 ){
			_this.fogg = new Firefogg();	
			//do the version check:			
			if( this.fogg.version.replace(/[^0-9]/gi, '') < this.min_firefogg_version.replace(/[^0-9]/gi, '' ) ){
				//show wrong version error: 
				$j('#wgfogg_wrong_version').show();
				//hide the installed parent div: 
				$j('#wgfogg_installed').hide();
			}
			//make sure the checkbox accurately reflects the current state per config:  			
			$j('#wgEnableFirefogg').get(0).checked = this.fogg_enabled;
			
			//setup the click bindding: 
			$j('#wgEnableFirefogg').click( function(){
				if( _this.fogg_enabled ){						
					_this.disable_fogg();			
				}else{
					_this.enable_fogg();
				}
			});
		}else{
			js_log('could not find wgEnableFirefogg');
		}
	},
	enable_fogg:function(){	
		var _this = this;
			
		//enable the FOGG_TOGGLE
		this.fogg_enabled=true;
		
		//make sure file is "checked"
		if($j( '#wpSourceTypeFile' ).length != 0)
			$j( '#wpSourceTypeFile' ).get(0).checked = true;		
		
		//hide normal file upload stuff
		$j( '#wg-base-upload' ).hide();		
		//setup the form pointer:
		_this.getEditForm();		
			
		//show fogg & add click binding: 
		$j( '#fogg-video-file' ).unbind().show().click( function(){
			_this.select_fogg();
		});							
	},
	disable_fogg:function(){
		var _this = this;
		//not enabled: 
		this.fogg_enabled=false;		

		$j( '#wg-base-upload' ).show();
		
		//hide any errors warnings and video select:
		$j( '#wgfogg_waring_ogg_upload,#wgfogg_waring_bad_extension,#fogg-video-file' ).hide();					
	},	
	select_fogg:function(){			
		var _this = this;
		if( _this.fogg.selectVideo() ) {
			
			//update destination filename:
			if( _this.fogg.sourceFilename ){				
				var sf = _this.fogg.sourceFilename;						
				var ext = '';
				if(	sf.lastIndexOf('.') != -1){
					ext = sf.substring( sf.lastIndexOf('.')+1 ).toLowerCase();
				}
				//set upload warning				
				if( ext == 'ogg' || ext == 'ogv' ){		
					$j('#wgfogg_waring_ogg_upload').show();
					return false;
				}else if( ext == 'avi' || ext == 'mov' || ext == 'mp4' || ext=='mp2' ||
						  ext == 'mpeg' || ext == 'mpeg2' || ext == 'mpeg4' ||
						  ext == 'dv' || ext=='wmv' ){
					//hide ogg warning
					$j('#wgfogg_waring_ogg_upload').hide();									
					sf = sf.replace( ext, 'ogg' );
					$j('#wpDestFile').val( sf );
				}else{
					//not video extension error:	
					$j('#wgfogg_waring_bad_extension').show();					
					return false;			
				}
			}
			//run the onClick hanndle: 
			if( toggleFilenameFiller ) 		
				toggleFilenameFiller();												
		}
	},
	getProgressTitle:function(){
		//return the parent if we don't have fogg turned on: 
		if(! this.fogg_enabled )
			return this.pe_getProgressTitle();
			
		return gM('upload-transcode-in-progress');
	},	
	doUploadSwitch:function(){				
		var _this = this;
		//make sure firefogg is enabled otherwise do parent UploadSwich:		
		if( ! this.fogg_enabled )
			return _this.pe_doUploadSwitch();
		
		//check what mode to use firefogg in: 
		if( _this.upload_mode == 'post' ){
			_this.doEncUpload();
		}else if( _this.upload_mode == 'api' && _this.chunks_supported){ //if api mode and chunks supported do chunkUpload
			_this.doChunkUpload();
		}else{
			js_error( 'Error: unrecongized upload mode: ' + _this.upload_mode );
		}		
	},
	//doChunkUpload does both uploading and encoding at the same time and uploads one meg chunks as they are ready
	doChunkUpload : function(){
		var _this = this;				
		
		if( ! _this.api_url )
			return js_error( 'Error: can\'t autodetect mode without api url' );				
						
		//add chunk response hook to build the resultURL when uploading chunks		
		
		//build the api url: 
		var aReq ={
			'action'	: 'upload',
			'format'	: 'json',
			'filename'	: _this.formData['wpDestFile'],
			'comment'	: _this.formData['wpUploadDescription'],
			'enablechunks': true
		};
		
		if( _this.formData['wpWatchthis'] )
			aReq['watch'] =  _this.formData['wpWatchthis'];
		
		if(  _this.formData['wpIgnoreWarning'] )
			aReq['ignorewarnings'] = _this.formData['wpIgnoreWarning'];
		
		js_log('do fogg upload call: '+ _this.api_url + ' :: ' + JSON.stringify( aReq ) );			
			
		
		_this.fogg.upload( JSON.stringify( _this.encoder_settings ),  _this.api_url ,  JSON.stringify( aReq ) );		
			
		//update upload status:						
		_this.doUploadStatus();
	},
	//doEncUpload first encodes then uploads
	doEncUpload : function(){	
		var _this = this;				
		_this.fogg.encode( JSON.stringify( _this.encoder_settings ) );		  	
		
		 //show transcode status:
		$j('#up-status-state').html( gM('upload-transcoded-status') );
		
		//setup a local function for timed callback:
		var encodingStatus = function() {
			var status = _this.fogg.status();
			
			//update progress bar
			_this.updateProgress( _this.fogg.progress() );			
			
			//loop to get new status if still encoding
			if( _this.fogg.state == 'encoding' ) {
				setTimeout(encodingStatus, 500);
			}else if ( _this.fogg.state == 'encoding done' ) { //encoding done, state can also be 'encoding failed				
				// ignore warnings & set source type 
				//_this.formData[ 'wpIgnoreWarning' ]='true';
				_this.formData[ 'wpSourceType' ]='file';		
				_this.formData[ 'action' ] = 'submit';
				
				//send to the post url: 				
				//js_log('sending form data to : ' + _this.editForm.action);
				//for(var fk in _this.formData){
				//	js_log(fk + ' : ' +  _this.formData[fk]);
				//}			
				_this.fogg.post( _this.editForm.action, 'wpUploadFile', JSON.stringify( _this.formData ) );
				
				//update upload status:						
				_this.doUploadStatus();
				
			}else if(_this.fogg.state == 'encoding fail'){
				//@@todo error handling: 
					alert('encoding failed');
			}
		}
		encodingStatus();		  			
	},	
	doUploadStatus:function() {	
		var _this = this;
		$j('#up-status-state').html( gM('uploaded-status')  );
	    
		_this.oldResponseText = '';
		//setup a local function for timed callback: 				
		var uploadStatus = function(){
			//get the response text: 
			var response_text =  _this.fogg.responseText;
			if( !response_text){
	       		try{
	       			var pstatus = JSON.parse( _this.fogg.uploadstatus() );
	       			response_text = pstatus["responseText"];
	       		}catch(e){
	       			js_log("could not parse uploadstatus / could not get responseText");
	       		}
			}
		       		
			if( _this.oldResponseText != response_text){								        					      					        				
				js_log('new result text:' + response_text);
				_this.oldResponseText = response_text;				
				//try and pare the response see if we need to take action:
				   
			}		
		    //update progress bar
		    _this.updateProgress( _this.fogg.progress() );
		    		    
		    //loop to get new status if still uploading (could also be encoding if we are in chunk upload mode) 
		    if( _this.fogg.state == 'encoding' || _this.fogg.state == 'uploading') {
				setTimeout(uploadStatus, 100);
			}
		    //check upload state
		    else if( _this.fogg.state == 'upload done' ||  _this.fogg.state == 'done' ) {	
		       	js_log( 'firefogg:upload done: '); 			        		       			       			       	       		       			       	
		       	//if in "post" upload mode read the html response (should be depricated): 
		       	if( _this.upload_mode == 'post' ) {		       		
		       		//js_log( 'done upload response is: ' + cat["responseText"] );
		       		_this.procPageResponse( response_text );
		       			
		       	}else if( _this.upload_mode == 'api'){		       				       	
		       		if( _this.fogg.resultUrl ){		       		
		       			//should have an json result:
		       			_this.updateUploadDone( _this.fogg.resultUrl );	
		       		}else{
		       			//done state with error? ..not really possible given how firefogg works
		       			js_log(" upload done, in chunks mode, but no resultUrl!");
		       		}		       				       				       				       			       	
		       	}													
			}else{  
				//upload error: 
				alert('firefogg upload error: ' + _this.fogg.state );		
	       }
	   }
	   uploadStatus();
	},	
	/*
	procPageResponse should be faded out soon.. its all very fragile to read the html output and guess at stuff*/
	procPageResponse:function( result_page ){
		js_log('f:procPageResponse');
		var sstring = 'var wgTitle = "' + this.formData['wpDestFile'].replace('_',' ');		
		var result_txt = gM('mv_upload_done', 
							wgArticlePath.replace(/\$1/, 'File:' + this.formData['wpDestFile'] ) );						
		//set the error text in case we dont' get far along in processing the response 
		$j( '#dlbox-centered' ).html( gM('mv_upload_completed') + result_txt );
												
		if( result_page && result_page.toLowerCase().indexOf( sstring.toLowerCase() ) != -1){	
			js_log( 'upload done got redirect found: ' + sstring + ' r:' + _this.upload_done_action );										
			if( _this.upload_done_action == 'redirect' ){
			$j( '#dlbox-centered' ).html( '<h3>Upload Completed:</h3>' + result_txt + '<br>' + form_txt);
				window.location = wgArticlePath.replace( /\$1/, 'File:' + formData['wpDestFile'] );
			}else{
				//check if the add_done_action is a callback:
				if( typeof _this.upload_done_action == 'function' )
					_this.upload_done_action();
			}									
		}else{								
			//js_log( 'upload page error: did not find: ' +sstring + ' in ' + "\n" + result_page );					
			var form_txt = '';		
			if( !result_page ){
				//@@todo fix this: 
				//the mediaWiki upload system does not have an API so we can\'t read errors							
			}else{
				var res = grabWikiFormError( result_page );
							
				if(res.error_txt)
					result_txt = res.error_txt;
					
				if(res.form_txt)
					form_txt = res.form_txt;
			}		
			js_log( 'error text is: ' + result_txt );		
			$j( '#dlbox-centered' ).html( '<h3>' + gM('mv_upload_completed') + '</h3>' + result_txt + '<br>' + form_txt);
		}
	}
}
