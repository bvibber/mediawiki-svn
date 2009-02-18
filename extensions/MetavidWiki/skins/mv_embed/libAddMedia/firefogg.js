/**script to support firefog uploads
based on http://www.firefogg.org/dev/index.html
*/

//add on ready hook if not being called from uploader:
if(typeof mvUploader =='undefined'){
	addOnloadHook(function(){
		init_firefogg();					
	});
}
var min_firefogg_version = '0.9.3'; 
function init_firefogg( iObj ){		
	if(!iObj)
		iObj = {};
	//init based on if Firefogg is available 
	if(typeof(Firefogg) == 'undefined') {
		//alert('Firefogg ISS null');				
		e = document.getElementById('wgfogg_not_installed');
		if(e) 
			e.style.display = 'inline';
	
		//no support for firefogg
		return false;
	}else{		
		e = document.getElementById('wgfogg_not_installed');
		if(e) 
			e.style.display='none';				
		
		e = document.getElementById('wgfogg_wrong_version');
		if(e) 
			e.style.display='none';
		
		e = document.getElementById('wgfogg_installed');
		if(e) 
			e.style.display='inline';			
		
		fe = document.getElementById('wgEnableFirefogg');				
		if(fe){			
			js_log('wgEnableFirefogg found:');
			//get a new fogg object with default options
			var fogg = new upFirefogg( iObj );
						
			//do the version check: 
			var fv = fogg.fogg.version;
			if(fv.replace(/[^0-9]/gi, '') < min_firefogg_version.replace(/[^0-9]/gi, '') ){
				e = document.getElementById('wgfogg_wrong_version');
				if(e) 
					e.style.display='inline';
				
				e = document.getElementById('wgfogg_installed');
				if(e) 
					e.style.display='none';		
			}
			
			//make sure the checkbox accurately reflects the current state: 
			if(fogg.enabled == false)
				fe.checked = false;
			
			addHandler( fe, 'click', function(){			
				if( fogg.enabled==false ){						
					fogg.enable_fogg();			
				}else{
					fogg.disable_fogg();
				}
			});					
		}else{			
			js_log('could not find wgEnableFirefogg');
		}
	}		
	//we did init with support
	return true;
}

var default_firefogg_options = {
	'upload_done_action':'redirect',
	'enabled':false
}
var upFirefogg = function(iObj){
	return this.init( iObj );
}
upFirefogg.prototype = {
	init:function( iObj ){
		for(var i in default_firefogg_options){
			if(iObj[i]){
				this[i] = iObj[i];
			}else{
				this[i] = default_firefogg_options[i];
			}
		}
		//init the Firefogg obj
		this.fogg = new Firefogg();	
	},
	enable_fogg:function(){	
		var _this = this;
			
		//enable the FOGG_TOGGLE
		_this.enabled=true;
		
		//make sure file is "checked"
		e = document.getElementById('wpSourceTypeFile');
		if(e) e.checked = true;
		
		//hide normal file upload stuff (this would all be much shorter code with jquery) 
		e = document.getElementById('wg-base-upload');
		if(e) e.style.display='none';
		
		//show fogg stuff:
		sfe = document.getElementById('fogg-video-file');
		if(sfe) sfe.style.display='inline';	
		
		
		addHandler( sfe, 'click', function(){
			//add binding: 
			_this.select_fogg();
		});
	},
	select_fogg:function(){			
		var _this = this;
		if(_this.fogg.selectVideo()) {											
			//update destination filename:
			if( _this.fogg.sourceFilename ){
				var destFileInput = document.getElementById( 'wpDestFile');
				var sf = _this.fogg.sourceFilename;		
				
				var ext = '';
				if(	sf.lastIndexOf('.') != -1){
					ext = sf.substring( sf.lastIndexOf('.')+1 );
				}
				//set upload warning				
				if( ext == 'ogg' || ext == 'ogv' ){		
					e = document.getElementById('wgfogg_waring_ogg_upload');	
					if(e) 
						e.style.display='block';
					return false;
				}else if( ext == 'avi' || ext == 'mov' || ext == 'mp4' || ext=='mp2' ||
						  ext == 'mpeg' || ext == 'mpeg2' || ext == 'mpeg4' ||
						  ext == 'dv' ){
					//hide ogg warning
					e = document.getElementById('wgfogg_waring_ogg_upload');
					if(e) 
						e.style.display='none';					
					sf = sf.replace(ext, 'ogg');
					destFileInput.value = sf;
				}else{
					//not video extension error:	
					e = document.getElementById('wgfogg_waring_bad_extension');
					if(e) 
						e.style.display='block';
					return false;			
				}								
			} 				
			//setup the form handling 
			var editForm = document.getElementById( 'mw-upload-form' );
			//set up the org_onsubmit if not set: 
			if( typeof( _this.org_onsubmit ) == 'undefined' )
				_this.org_onsubmit = editForm.onsubmit;
					
			editForm.onsubmit = function() {	
				//run the original onsubmit (if not run yet set flag to avoid excessive chaining ) 
				if( typeof( _this.org_onsubmit ) == 'function' ){					
					//error in org submit return false;   
					if( ! _this.org_onsubmit()){
						return false;					
					}
				}												
				//get the input 
				var formData = _this.getEditFormData( editForm );				
						
				//display the loader:
				e = document.getElementById('dlbox-centered')
				e.style.display='block';
				
				e = document.getElementById('dlbox-overlay')
				e.style.display='block';			
				
				//for some unknown reason we have to drop down the #p-search z-index:
				e = document.getElementById('p-search');
				if(e) e.style.zIndex = 1;
				
				
				//@@todo read this from the config file rather than hard code it: 
				var options = JSON.stringify({'maxSize': 400, 'videoBitrate': 400});
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
			    		e = document.getElementById('fogg-status-transcode');
			    		e.style.display='none';		    		
			    		//show the fogg-status-upload
			    		e = document.getElementById('fogg-status-upload');
			    		e.style.display='inline';			    					    								
												
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
					        else if(_this.fogg.state == 'upload done') {	
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
	},
	getEditFormData:function( editForm ){
		var data = {};							      	
      	//get all the form fields: 
		var inputs = editForm.getElementsByTagName('input');					
		for(var i=0;i < inputs.length; i++){
			if( inputs[i].getAttribute('name') != 'wpUploadFile'){
				if(	inputs[i].getAttribute('type')=='checkbox'){
					if(inputs[i].checked){
						data[ inputs[i].getAttribute('name') ] = 'true';
					}else{
						data[ inputs[i].getAttribute('name') ] = 'false';
					}
				}else{
					data[ inputs[i].getAttribute('name') ] = inputs[i].value;
				}
			}
		}
		var inputs = editForm.getElementsByTagName('textarea');					
		for(var i=0;i < inputs.length; i++){
			data[ inputs[i].getAttribute('name') ] = inputs[i].value;
		}
		return data;
	},
	disable_fogg:function(){
		//show normal file upload
		e = document.getElementById('wg-base-upload');
		if(e) e.style.display='inline';	
		
		//hide ogg warning
		e = document.getElementById('wgfogg_waring_ogg_upload');
		if(e) 
			e.style.display='none';		
		
		//hide not ogg extension error	
		e = document.getElementById('wgfogg_waring_bad_extension');
		if(e) 
			e.style.display='block';		
		
		//hide fogg stuff
		e = document.getElementById('fogg-video-file');
		if(e) e.style.display='none';
				
		if( this.upload_done_action == 'redirect' ){
			var editForm = document.getElementById( 'mw-upload-form' );
			//restore the original form action 			
			editForm.onsubmit = this.org_onsubmit;
		}	
		//disable the fogg:
		this.enabled=false;
	},
	fogg_update_progress:function(progress){
		var progressbar = document.getElementById('fogg-progressbar');
		if(progressbar)
			progressbar.style.width= parseInt(progress*100) +'%';
		var progstatus = document.getElementById('fogg-pstatus');
		if(progstatus)
			progstatus.innerHTML = parseInt(progress*100) + '% - ';
	}
}