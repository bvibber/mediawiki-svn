/* firefogg refactor using jQuery
	invovked using the 
 */
 
var default_firefogg_options = {
	'upload_done_action':'redirect',
	'enabled':false
}
var mvFirefogg = function(initObj){
	return this.init( initObj );
}
mvFirefogg.prototype = {

	min_firefogg_version : '0.9.3',
	enabled : false, 			//if firefogg is enabled or not. 
	upload_mode:'autodetect', 	//can be 'post', 'chunks' or autodetect. (autodetect issues an api call)   
	encoder_settings:{			//@@todo maybe allow server to set this? 
		'maxSize': 400, 
		'videoBitrate': 400
	},	
	init : function( iObj ){
		if(!iObj)
			iObj = {};
			js_log("wtf");
		//inherit iObj properties:
		for(var i in default_firefogg_options){
			if(iObj[i]){
				this[i] = iObj[i];
			}else{
				this[i] = default_firefogg_options[i];
			}
		}
		this.setupFirefogg();
	},
	setupFirefogg : function(){
		var _this = this;		
		if(typeof(Firefogg) == 'undefined'){ 
			$j('#wgfogg_not_installed').show();
			return false;
		}
		//make sure all the error msgs are hidden: 
		$j('#wgfogg_not_installed,#wgfogg_wrong_version').hide();
		
		//show firefogg enabler: 
		$j('#wgfogg_installed,#wgEnableFirefogg').show();
		
		if( $j('#wgEnableFirefogg').length > 0 ){
			this.fogg = new Firefogg();	
			//do the version check:			
			if( this.fogg.version.replace(/[^0-9]/gi, '') < this.min_firefogg_version.replace(/[^0-9]/gi, '' ) ){
				//show wrong version error: 
				$j('#wgfogg_wrong_version').show();
				//hide the installed parent div: 
				$j('#wgfogg_installed').hide();
			}
			//make sure the checkbox accurately reflects the current state per config:  			
			$j('#wgEnableFirefogg').get(0).checked = this.enabled;
			
			//setup the click bindding: 
			$j('#wgEnableFirefogg').click( function(){
				if( _this.enabled ){						
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
		this.enabled=true;
		
		//make sure file is "checked"
		$j('#wpSourceTypeFile').get(0).checked = true;		
		
		//hide normal file upload stuff (this would all be much shorter code with jquery) 
		$j('#wg-base-upload').hide();
		
		//show fogg & add click binding: 
		$j('#fogg-video-file').show().click( function(){
			_this.select_fogg();
		});							
	},
	disable_fogg:function(){
		//not enabled: 
		this.enabled=false;		

		$j('#wg-base-upload').show();
		
		//hide any errors warnings and video select:
		$j('#wgfogg_waring_ogg_upload,#wgfogg_waring_bad_extension,#fogg-video-file').hide();		
	},
	fogg_update_progress:function(progress){
		$j('#fogg-progressbar').css('width', parseInt(progress*100) +'%');		
		$j('#fogg-pstatus').html( parseInt(progress*100) + '% - ');
	},
	select_fogg:function(){			
		var _this = this;
		if(_this.fogg.selectVideo()) {											
			//update destination filename:
			if( _this.fogg.sourceFilename ){				
				var sf = _this.fogg.sourceFilename;						
				var ext = '';
				if(	sf.lastIndexOf('.') != -1){
					ext = sf.substring( sf.lastIndexOf('.')+1 );
				}
				//set upload warning				
				if( ext == 'ogg' || ext == 'ogv' ){		
					$j('#wgfogg_waring_ogg_upload').show();
					return false;
				}else if( ext == 'avi' || ext == 'mov' || ext == 'mp4' || ext=='mp2' ||
						  ext == 'mpeg' || ext == 'mpeg2' || ext == 'mpeg4' ||
						  ext == 'dv' ){
					//hide ogg warning
					$j('#wgfogg_waring_ogg_upload').hide();									
					sf = sf.replace(ext, 'ogg');
					$j('#wpDestFile').val( sf );
				}else{
					//not video extension error:	
					$j('#wgfogg_waring_bad_extension').show();					
					return false;			
				}								
			}
			 				
			//setup the form handling 
			var editForm = $j('#mw-upload-form').get(0);
			
			//set up the org_onsubmit if not set: 
			if( typeof( _this.org_onsubmit ) == 'undefined' )
				_this.org_onsubmit = editForm.onsubmit;
					
			editForm.onsubmit = function() {	
				//run the original onsubmit (if not run yet set flag to avoid excessive chaining ) 
				if( typeof( _this.org_onsubmit ) == 'function' ){										  
					if( ! _this.org_onsubmit() ){
						//error in org submit return false;
						return false;					
					}
				}												
				//get the input 
				//var formData = _this.getEditFormData( editForm );				
				var tmpAryData = $j( editForm ).serializeArray();				
				for(var i=0; i < tmpAryData.length; i++){
					if( tmpAryData[i]['name'] )
						formData[ mpAryData[i]['name'] ] = mpAryData[i]['value'];
				}
			
				//display the loader:
				$j('#dlbox-centered,#dlbox-overlay').show();				
				
				//for some unknown reason we have to drop down the #p-search z-index:
				$j('#p-search').css('z-index', 1);								
				
				//check the upload mode: 
				if( _this.upload_mode == 'autodetect'){
					
				}else{
					
				}
				var options = JSON.stringify( _this.encoder_settings );
			  	_this.fogg.encode(options);		  	
			  	
			  	var encodingStatus = function() {
			    	var status = _this.fogg.status();
			
			    	//update progress bar
			    	_this.fogg_update_progress( _this.fogg.progress() );
			
			    	//loop to get new status if still encoding
			    	if( _this.fogg.state == 'encoding' ) {
			      		setTimeout(encodingStatus, 500);
			    	}
			    	//encoding done, state can also be 'encoding failed'
			    	else if ( _this.fogg.state == 'encoding done' ) {
			    		//hide the fogg-status-transcode
			    		$j('#fogg-status-transcode').hide();
			    			
			    		//show the fogg-status-upload
			    		$j('#fogg-status-upload').show();			    			    					    							
												
						//hard code some values 
						formData['wpSourceType']='file';						 						
						
						var data = JSON.stringify( formData );						
						//send to the post url: 							
						_this.fogg.post( editForm.action, 'wpUploadFile', data);
						var uploadStatus = function() {							
					        var status = _this.fogg.status();							        					      					        					
							//js_log(' up stats: ' + status + ' p:' + _this.fogg.progress() + ' state: '+ _this.fogg.state + ' result page:' + result_page);
							
					        //update progress bar
					       	_this.fogg_update_progress( _this.fogg.progress() );
					
					        //loop to get new status if still uploading
					        if(_this.fogg.state == 'uploading') {
					        	setTimeout(uploadStatus, 500);
					        }
					        //upload sucesfull, state can also be 'upload failed'
					        else if( _this.fogg.state == 'upload done' ) {	
					        	//js_log( 'firefogg:upload done: ');							        			        
					        	//@@todo handle errors same problem as #695 in remoteSearchDriver.js
					        	//we need to add image uploading to the api rather than parse the HTML output of the pages  
								var result_page = _this.fogg.responseText;
								var sstring = 'var wgTitle = "' + formData['wpDestFile'].replace('_',' ');								
								if( result_page && result_page.toLowerCase().indexOf( sstring.toLowerCase() ) != -1){	
									js_log('upload done got redirect found: ' +sstring + ' r:' + _this.upload_done_action);										
									if( _this.upload_done_action == 'redirect'){
										window.location = wgArticlePath.replace(/\$1/, 'File:' + formData['wpDestFile'] );
									}else{
										//check if the add_done_action is a callback:
										if( typeof _this.upload_done_action == 'function' )
											_this.upload_done_action();
									}									
								}else{								
									js_log('upload page error: did not find: ' +sstring);	
									var error_txt = 'Unkown error';
									if(!result_page){
										//@@todo fix this: 
										//the mediaWiki upload system does not have an API so we can\'t accuratly read errors 
										error_txt = 'Your upload should be accessible <a href="' + 
													wgArticlePath.replace(/\$1/, 'File:' + formData['wpDestFile'] ) + '">'+
													'here</a> \n';
									}else{
										sp = result_page.indexOf('<span class="error">');
										if(sp!=-1){
											se = result_page.indexOf('</span>', sp);
											error_txt = result_page.substr(sp, (sp-se));
										}else{
											//look for warning: 
											sp = result_page.indexOf('<ul class="warning">')
											if(sp!=-1){
												se = result_page.indexOf('</ul>', sp);
												error_txt = result_page.substr(sp, (sp-se));
											}
										}			
									}						
									e = document.getElementById('dlbox-centered');
									if(e) 
										e.innerHTML = '<h3>Upload Completed:</h3>' + error_txt;
								}							
					        }
					        //upload error: 
					        else{
					        	alert('firefogg upload error: ' + _this.fogg.state);		
					        }
				      	}
				      	uploadStatus();
				    }else if(_this.fogg.state == 'encoding fail'){
				    	//@@todo error handling: 
				    	alert('encoding failed');
				    }
			  }
			  encodingStatus();
			  //don't submit the form (let firefogg handle it)	
			  return false;			
			};	//addHandler mapping
		}else{
			//remove upload binding if no file was selected
		}	 
	}	
}