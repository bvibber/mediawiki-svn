/**script to support firefog uploads
based on http://www.firefogg.org/dev/index.html
*/

//add on ready hook if not being called from uploader:
if(typeof mvUploader =='undefined'){
	addOnloadHook(function(){
		init_firefogg();					
	});
}
var min_firefogg_version = '0.9.2'; 
function init_firefogg( iObj ){		
	if(!iObj)
		iObj = {};
	//init based on if Firefogg is available 
	if(typeof(Firefogg) == 'undefined') {				
		e = document.getElementById('wgfogg_not_installed');
		if(e) 
			e.style.display='inline';
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
			var fv = fogg.ogg.version;
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
		this.ogg = new Firefogg();	
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
		if(_this.ogg.selectVideo()) {
			var editForm = document.getElementById( 'mw-upload-form' );
			_this.org_onsubmit = editForm.onsubmit;	
			editForm.onsubmit = function return_false(){
								return false;
							};			
			//set binding for "upload" button to call our transcode process
			addHandler( editForm, 'submit', function() {			
				//check if the title and or description are empty don't let the person submit
				e = document.getElementById('wpDestFile')
				if(typeof e.value == 'undefined' || e.value=='' || e.value.substr(-4) != '.ogg')
					return alert('destination file is empty or does not end with .ogg');
					
				e = document.getElementById('wpUploadDescription');
				if(e){
					if(typeof e.value == 'undefined' || e.value=='')
						return alert('Description is empty');
				}
				//for commons check wpDescText1
				e = document.getElementById('wpDescText1');
				if(e){
					if(typeof e.value == 'undefined' || e.value=='')
						return alert('Description is empty');
				}
						
				//display the loader:
				e = document.getElementById('dlbox-centered')
				e.style.display='block';
				
				e = document.getElementById('dlbox-overlay')
				e.style.display='block';			
				
				//for some unknown reason we have to drop down the #p-search z-index:
				e = document.getElementById('p-search');
				if(e) e.style.zIndex = 1;
				
				
				//@@todo read this from the config file rather than hard code it: 
				var options = JSON.stringify({'maxSize': 400, 'videoBitrate': 500});
			  	_this.ogg.encode(options);		  	
			  	var encodingStatus = function() {
			    	var status = _this.ogg.status();
			
			    	//update progress bar
			    	_this.fogg_update_progress( _this.ogg.progress() );
			
			    	//loop to get new status if still encoding
			    	if( _this.ogg.state == 'encoding' ) {
			      		setTimeout(encodingStatus, 500);
			    	}
			    	//encoding done, state can also be 'encoding failed'
			    	else if ( _this.ogg.state == 'encoding done' ) {
			    		//hide the fogg-status-transcode
			    		e = document.getElementById('fogg-status-transcode');
			    		e.style.display='none';		    		
			    		//show the fogg-status-upload
			    		e = document.getElementById('fogg-status-upload');
			    		e.style.display='inline';
			    					    		
						//get the input 
						var data = _this.getEditFormData( editForm );
												
						//hard code some values 
						data['wpSourceType']='file';						 						
						
						var data_str = JSON.stringify(data);					
						//alert('send data:'+ data_str);
						//send to the post url: 
						js_log('sending to:' + editForm.action);								
						_this.ogg.upload(editForm.action, 'wpUploadFile', data_str);
						var uploadStatus = function() {							
					        var status = _this.ogg.status();							        					      					        					
							//js_log(' up stats: ' + status + ' p:' + _this.ogg.progress() + ' state: '+ _this.ogg.state + ' result page:' + result_page);
							
					        //update progress bar
					       	_this.fogg_update_progress( _this.ogg.progress() );
					
					        //loop to get new status if still uploading
					        if(_this.ogg.state == 'uploading') {
					        	setTimeout(uploadStatus, 500);
					        }
					        //upload sucsefull, state can also be 'upload failed'
					        else if(_this.ogg.state == 'upload done') {	
					        	//js_log('upload done: ' + JSON.parse(_this.ogg.uploadstatus()).responseText);							        			        
					        	//@@todo handle errors same problem as #695 in mv_remote_media_search.js
					        	//we need to add image uploading to the api rather than parse the HTML output of the pages  
								var result_page = JSON.parse(_this.ogg.uploadstatus()).responseText;
								var sstring = 'var wgTitle = "' + data['wpDestFile'].replace('_',' ');
								if( result_page.toLowerCase().indexOf( sstring.toLowerCase() ) != -1){	
									js_log('upload done got redirect found: ' +sstring + ' r:' + _this.upload_done_action);										
									if(_this.upload_done_action == 'redirect'){
										window.location = wgArticlePath.replace(/\$1/, 'File:' + data['wpDestFile'] );
									}else{
										//check if the add_done_action is a callback:
										if(typeof _this.upload_done_action == 'function')
											_this.upload_done_action();
									}									
								}else{								
									js_log('upload page error: did not find: ' +sstring);	
									var error_txt = 'Unkown error';
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
									e = document.getElementById('dlbox-centered');
									if(e) 
										e.innerHTML = '<h3>Error:</h3>' + error_txt;
								}							
					        }
					        //upload error: 
					        else{
					        	alert('upload error: ' + _this.ogg.state);		
					        }
				      	}
				      	uploadStatus();
				    }else if(_this.ogg.state == 'encoding fail'){
				    	//@@todo error handling: 
				    	alert('encoding failed');
				    }
			  }
			  encodingStatus();
			  //don't submit the form (let firefogg handle it)	
			  return false;			
			});	//addHandler mapping
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