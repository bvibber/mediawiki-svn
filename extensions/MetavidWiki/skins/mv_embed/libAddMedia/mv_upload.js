/* the upload javascript 
presently does hackery to work with Special:Upload page...

WILL BE REPLACED WITH CODE TO ACCESS THE upload api 
ONCE THAT IS READY
*/

gMsg['upload-enable-converter']		= 'Enable video converter (to upload source video footage not yet converted to theora format) <i>more info</i>';
gMsg['upload-fogg_not_installed']	= 'If you want to upload video consider installing <a href="http://firefogg.org">firefogg.org</a>, <i>more info</i>';
gMsg['upload-in-progress']			= 'Doing Transcode & Upload (do not close this window)';
gMsg['upload-transcoded-status']	= 'Transcoded';
gMsg['uploaded-status']				= 'Uploaded';
gMsg['upload-select-file']			= 'Select File...';

var default_upload_options = {
	'target_div':''
}

var mvUploader = function(initObj){
	return this.init( initObj );
}
mvUploader.prototype = {
	init:function( iObj ){
		var _this = this;	
		js_log('init uploader');
		for(var i in default_upload_options){
			if(iObj[i]){
				this[i] = iObj[i];
			}else{
				this[i] = default_upload_options[i];
			}
		}
		//grab firefogg.js: 
		mvJsLoader.doLoad({'upFirefogg' : 'libAddMedia/firefogg.js'},function(){	
			//for now grab the upload HTML (uggly but will be replaced by API shortly I promise )
			$j.get(wgArticlePath.replace(/\$1/, 'Special:Upload'), {}, function(data){
				//filter the data: 
				sp = data.indexOf('<div id="content">');
				se = data.indexOf('<!-- end content -->');			
				if(sp!=-1 && se !=-1){				
					$j('#'+_this.target_div).html( data.substr(sp, (se-sp) ) );
				}
				//add firebug html if not already there: ( same as $wgEnableFirebug )  
				if( $j('#fogg-video-file').length==0 ){
					_this.addFirebugHtml();
				}else{
					js_log('firefogg already there init:');					
				}				
				init_firefogg({'add_done_action':function( rTitle ){
						js_log('add_done_action callback for uploader');
						//call the parent insert resource preview
						
					}
				});			
				//set up the bindings
				$j('#mw-upload-form').submit(function(){
					//do an ajax submit
					
					//(don't do the normal submit)
					return false;
				});
			});				
		});
	},
	//same add code as specialUpload if($wgEnableFirefogg){
	addFirebugHtml:function(){						
		$j('#mw-upload-table .mw-input :first').wrap('<div id="wg-base-upload"></div>');
		//add in firefogg control			
		$j('#wg-base-upload').after('<p id="fogg-enable-item" >' + 
						'<input style="display:none" id="fogg-video-file" name="fogg-video-file" type="button" value="Select File..">' +
						'<span id="wgfogg_not_installed">' + 
							getMsg('upload-fogg_not_installed') +
						'</span>'+
						'<span id="wgfogg_installed"  style="display:none" >'+
							'<input id="wgEnableFirefogg" type="checkbox" name="wgEnableFirefogg" >' + 							
								getMsg('upload-enable-converter') +
						'<span><br></p>');
		alert('wtf');
		js_log('FOUND: ' + $j('#fogg-enable-item').length + ' wg: ' + $j('#wgEnableFirefogg').length );
		//add in loader dl box: 
		$j('#mw-upload-table').before('<div id="dlbox-centered" class="dlbox-centered" >'+			
				'<h5>' + getMsg('upload-in-progress') + '</h5>' +
				'<div id="fogg-pbar-container" style="border:solid thin gray;width:90%;height:15px;" >' +
					'<div id="fogg-progressbar" style="background:#AAC;width:0%;height:15px;"></div>' +			
				'</div>' +
				'<span id="fogg-pstatus">0%</span>' +
				'<span id="fogg-status-transcode">' + getMsg('upload-transcoded-status') + '</span>'+  
				'<span style="display:none" id="fogg-status-upload">' + getMsg('uploaded-status') + '</span>' +
			'</div>'+					
			'<div class="dlbox-overlay" ></div>');			
		//init firefogg (check for its existance)
		js_log('FOUND: ' + $j('#wgEnableFirefogg').length );
		js_log('run init_firefogg');					
	}
}