/*
* a library for doing remote media searches 
*  
* initial targeted archives are:
	the local wiki 
	wikimedia commons 
	metavid 
	and archive.org
*/

gMsg['mv_media_search']	= 'Media Search';
gMsg['rsd_box_layout'] 	= 'Box layout';
gMsg['rsd_list_layout'] = 'List Layout';
gMsg['rsd_results_desc']= 'Results <b>$0</b> of <b>$1</b>';
gMsg['rsd_layout'] = 	  'Layout:';
gMsg['rsd_resource_edit']='Edit Resource:';

var default_remote_search_options = {
	'profile':'mediawiki_edit',	
	'target_id':null, //the div that will hold the search interface
	
	'default_provider_id':'all', //all or one of the content_providers ids
	
	'caret_pos':null,
	'local_wiki_api_url':null,
	
	'target_textbox':null,
	'instance_name': null, //a globally accessible callback instance name
	'default_query':'', //default search query
	//specific to sequence profile
	'p_seq':null
}
var remoteSearchDriver = function(initObj){
	return this.init( initObj );
}
remoteSearchDriver.prototype = {
	results_cleared:false,
	//here we define the set of possible media content providers:
	main_search_options:{
		'selprovider':{
			'title': 'Select Providers'			
		},
		'advanced_search':{
			'title': 'Advanced Options'
		}		
	},
	content_providers:{				
		/*content_providers documentation: 			
			@enabled: whether the search provider can be selected
			@checked: whether the search provideer will show up as seletable tab (todo: user prefrence) 
			@d: 	  if the current cp should be displayed (only one should be the default) 
			@title:   the title of the search provider
			@desc: 	  can use html... todo: need to localize
			@api_url: the url to query against given the library type: 
			@lib: 	  the search library to use corresponding to the 
						search object ie: 'mediaWiki' = new mediaWikiSearchSearch() 
			@local : if the content provider assets need to be imported or not.  
		*/ 		 
		'this_wiki':{
			'enabled':0,
			'checked':0,
			'd'		:0,
			'title'	:'The Current Wiki',
			'desc'	: '(should be updated with the proper text)',
			'api_url': wgScriptPath + '/api.php',
			'lib'	:'mediaWiki',
			'local'	:true
		},
		'wiki_commons':{
			'enabled':1,
			'checked':1,
			'd'		:1,
			'title'	:'Wikipedia Commons',			
			'desc'	: 'Wikimedia Commons is a media file repository making available public domain '+
			 		'and freely-licensed educational media content (images, sound and video clips) to all.',
			'hompage': 'http://commons.wikimedia.org/wiki/Main_Page',		
			'api_url':'http://commons.wikimedia.org/w/api.php',
			'lib'	:'mediaWiki',
			'search_title':false, //disable title search
			'local'	:false,
			'resource_prefix': '' //what prefix to use on imported resources 
		},
		'metavid':{
			'enabled':1,
			'checked':1,
			'd'		:0,			
			'title'	:'Metavid.org',
			'homepage':'http://metavid.org',
			'desc'	: 'Metavid hosts thousands of hours of US house and senate floor proceedings',
			'api_url':'http://localhost/wiki/index.php?title=Special:MvExportSearch',
			'lib'	: 'metavid',
			'local'	:false,
			'resource_prefix': 'MV_' //what prefix to use on imported resources 
		},
		'archive_org':{
			'enabled':0,
			'checked':0,
			'd'		:0,
			'title' : 'Archive.org',
			'desc'	: 'The Internet Archive, a digital library of cultural artifacts',
			'homepage':'http://archive.org',
			'lib'	: 'archive',
			'local'	: false,
			'resource_prefix': 'AO_'
		}
	},	
	//some default layout values:		
	thumb_width 		: 80,
	image_edit_width	: 600,
	video_edit_width	: 400,
	insert_text_pos		: 0, //insert at the start (will be overwiten by the user cursor pos) 
	result_display_mode : 'box', //box or list or preview
	
	init:function( initObj ){
		js_log('remoteSearchDriver:init');
		for( var i in default_remote_search_options ) {
			if( initObj[i]){
				this[ i ] = initObj[i];
			}else{
				this[ i ] =default_remote_search_options[i]; 
			}			
		}		
		//set up the content provider config: 
		if(this.cpconfig){
			for(var cpc in cpconfig){
				for(var cinx in this.cpconfig[cpc]){
					if( this.content_providers[cpc] )						
						this.content_providers[ cpc ][ cinx ] = this.cpconfig[cpc][ cinx];					
				}
			}
		}
		
		//overwrite the default query if a text selection was made: 
		if(this.target_textbox)
			this.getTexboxSelection();
		
		this.init_interface_html();
		this.add_interface_bindings();
	},
	//gets the in and out points for insert position or grabs the selected text for search	
	getTexboxSelection:function(){			
		if(this.caret_pos.s && this.caret_pos.e &&
			(this.caret_pos.s != this.caret_pos.e))
			this.default_query = $j('#'+this.target_textbox).val().substring(this.caret_pos.s, this.caret_pos.e).replace(/ /g, '\xa0') || '\xa0'		
	},
	//sets up the initial html interface 
	init_interface_html:function(){
		var out = '<div class="rsd_control_container" style="width:100%">' + 
					'<table style="width:100%">' +
						'<tr>'+
							'<td style="width:110px">'+
								'<h3> Media Search </h3>'+
							'</td>'+
							'<td style="width:190px">'+
								'<input type="text" tabindex="1" value="' + this.default_query + '" maxlength="512" id="rsd_q" name="rsd_q" '+ 
									'size="20" autocomplete="off"/>'+
							'</td>'+
							'<td style="width:115px">'+
								'<input type="submit" value="' + getMsg('mv_media_search') + '" tabindex="2" '+
									' id="rms_search_button"/>'+
							'</td>'+
							'<td>';
			out += '<a href="#" id="mso_selprovider" >Select Providers</a><br>';
			out += '<a href="#" id="mso_cancel" >Cancel</a><br>';
			out +=			'</td>'+
						'</tr>'+
					'</table>';			
		js_log('out: ' + out);									
				
		out+='<div id="rsd_options_bar" style="display:none;width:100%;height:0px;background:#BBB">';
			//set up the content provider selection div (do this first to get the default cp)
			out+= '<div id="cps_options">';												
			for( var cp_id in this.content_providers ){
				var cp = this.content_providers[cp_id];				 
				var checked_attr = ( cp.checked ) ? 'checked':'';					  
				out+='<div  title="' + cp.title + '" '+ 
						' style="float:left;cursor:pointer;">'+
						'<input class="mv_cps_input" type="checkbox" name="mv_cps" '+ checked_attr+'>';

				out+= '<img alt="'+cp.title+'" src="' + mv_embed_path + 'skins/' + mv_skin_name + '/remote_search/' + cp_id + '_tab.png">'; 				
				out+='</div>';
			}		 		
			out+='<div style="clear:both"/><a id="mso_selprovider_close" href="#">'+getMsg('close')+'</a></div>';
		out+='</div>';				
		//close up the control container: 
		out+='</div>';
		//search provider tabs based on "checked" and "enabled" and "combined tab"
		out+='<div id="rsd_results_container">';				
		out+='</div>';							
		$j('#'+ this.target_id ).html( out );
		//draw the tabs: 
		this.drawTabs();
		//run the default search: 
		this.runSearch();
	}, 
	add_interface_bindings:function(){
		var _this = this;
		js_log("add_interface_bindings:");		
		//setup for this.main_search_options:
		$j('#mso_cancel').click(function(){
			$j('#modalbox').fadeOut("normal",function(){
				$j(this).remove();
				$j('#mv_overlay').remove();
			});
		});
		
		$j('#mso_selprovider,#mso_selprovider_close').click(function(){
			if($j('#rsd_options_bar:hidden').length !=0 ){
				$j('#rsd_options_bar').animate({
					'height':'110px',
					'opacity':1
				}, "normal");
			}else{
				$j('#rsd_options_bar').animate({
					'height':'0px',
					'opacity':0					
				}, "normal", function(){
					$j(this).hide();
				});
			}
		});						
		//setup binding for search provider check box: 
		//search button: 
		$j('#rms_search_button').click(function(){
			_this.runSearch();
		});		
	},
	runSearch: function(){
		var _this = this;						
		//set loading div: 
		$j('#rsd_results').append('<div style="position:absolute;top:0px;left:0px;height:100%;width:100%;'+
			'background-color:#FFF;margin:30px">' + 			
				mv_get_loading_img('padding:30px') + 
			'</div>');		
		//get a remote search object for each search provider and run the search
		for(var cp_id in  this.content_providers){
			cp = this.content_providers[ cp_id ];
			//only run the search for default item (unless combined is selected) 
			if( !cp.d || this.disp_item == 'combined' )
				continue;			
			//check if we need to update: 
			if(typeof cp.sObj != 'undefined'){
				if(cp.sObj.last_query == $j('#rsd_q').val())
					continue;					
			}			
			//else we need to run the search: 
			var iObj = {'cp':cp, 'rsd':this};			
			eval('cp.sObj = new '+cp.lib+'Search(iObj);');
			if(!cp.sObj)
				js_log('Error: could not find search lib for ' + cp_id);						
			//do search:
			cp.sObj.getSearchResults();							
		}	
		this.checkResultsDone();
	},	
	checkResultsDone: function(){
		var _this = this;
		var loading_done = true;
		for(var cp_id in  this.content_providers){
			cp = this.content_providers[ cp_id ];
			if(typeof cp['sObj'] != 'undefined'){
				if( cp.sObj.loading )
					loading_done=false; 
			}
		}
		if(loading_done){
			this.drawOutputResults();
		}else{			
			setTimeout( _this.instance_name + '.checkResultsDone()', 250);
		}		 
	},
	drawTabs: function(){
		var _this = this;
		//add the tabs to the rsd_results container: 
		var o= '<ul class="rsd_cp_tabs" >';
			o+='<li id="rsd_tab_combined" ><img src="' + mv_embed_path + 'skins/'+mv_skin_name+ '/remote_search/combined_tab.png"></li>';		 			 	
			for(var cp_id in  this.content_providers){
				var cp = this.content_providers[cp_id];
				if( cp.enabled && cp.checked){
					var class_attr = (cp.d)?'class="rsd_selected"':'';
					o+='<li id="rsd_tab_'+cp_id+'" ' + class_attr + '><img src="' + mv_embed_path + 'skins/' + mv_skin_name + '/remote_search/' + cp_id + '_tab.png">';
				}
			}
		o+='</ul>';		
		//outout the resource results holder	
		o+='<div id="rsd_results" />';				
		$j('#rsd_results_container').html(o);
		
		//setup bindings for tabs: 
		$j('.rsd_cp_tabs li').click(function(){
			_this.selectTab( $j(this).attr('id').replace(/rsd_tab_/, '') );
		});
	},			
	//@@todo we could load the id with the content provider id to find the object faster...
	getResourceFromId:function( rid ){
		//strip out /res/ if preset: 
		rid = rid.replace(/res_/, '');
		for(var cp_id in  this.content_providers){
			cp = this.content_providers[ cp_id ];		
			if(	cp['sObj']){
				for(var rInx in cp.sObj.resultsObj){				
					if(rInx == rid)
						return cp.sObj.resultsObj[rInx];
				};
			}
		}
		js_log("ERROR: could not find " + rid);
		return false;
	},
	drawOutputResults: function(){		
		js_log('f:drawOutputResults');				
		var _this = this;			
		var o='';
		$j('#rsd_results').empty();
		//output the results bar / controls
		_this.setResultBarControl();				 
		
		//output all the results (hide based on tab selection) 
		for(var cp_id in  this.content_providers){
			cp = this.content_providers[ cp_id ];
			//output results based on display mode & input: 
			if(typeof cp['sObj'] != 'undefined'){
				$j.each(cp.sObj.resultsObj, function(rInx, rItem){					
					var disp = ( cp.d ) ? '' : 'display:none;';
					if( _this.result_display_mode == 'box' ){
						o+='<div id="mv_result_' + rInx + '" class="mv_clip_box_result" style="' + disp + 'width:' +
								_this.thumb_width + 'px;height:'+ (_this.thumb_width-20) +'px">';
							o+='<img title="'+rItem.title+'" class="rsd_res_item" id="res_' + rInx +'" style="width:' + _this.thumb_width + 'px;" src="' + rItem.poster + '">';
						o+='</div>';
					}else if(_this.result_display_mode == 'list'){
						o+='<div id="mv_result_' + rInx + '" class="mv_clip_list_result res_' + rInx +'" style="' + disp + 'width:90%">';
							o+='<img class="rsd_res_item" id="res_' + cp_id +'" style="float:left;width:' + _this.thumb_width + 'px; padding:5px;" src="' + rItem.poster + '">';			
							o+= rItem.desc ;							
						o+='</div>';
						o+='<div style="clear:both" />';
					}			
				});	
			}						
		}				
		//put in the new output:  
		$j('#rsd_results').append( o )		
		//remove rss only display class if present
		$j('#rsd_results .mv_rss_view_only').remove();
		this.addResultBindings();
	},
	addResultBindings:function(){
		var _this = this;
		$j('.mv_clip_box_result').hover(function(){
			$j(this).addClass('mv_clip_box_result_over');
		},function(){
			$j(this).removeClass('mv_clip_box_result_over');
		});
		//resource click action: (bring up the resource editor) 		
		$j('.rsd_res_item').click(function(){		
			//get the resource obj:
			rObj = _this.getResourceFromId( this.id );						
			//remove any existing resource edit interface: 
			$j('#rsd_resource_edit').remove();						
			//append to the top level of model window: 
			$j( '#'+ _this.target_id ).append('<div id="rsd_resource_edit" '+ 
				'style="position:absolute;top:0px;left:0px;width:100%;height:100%;background-color:#FFF;">' +
					'<h3 style="margin:4px;">' + getMsg('rsd_resource_edit') + ' ' + rObj.title +'</h3>'+
					'<div id="clip_edit_disp" style="position:absolute;top:30px;left:0px;bottom:0px;'+
						'width:' + (_this.image_edit_width+30) + 'px;overflow:auto;" >' +
							mv_get_loading_img('position:absolute;top:30px;left:30px', 'mv_img_loader') + 
					'</div>'+
					'<div id="clip_edit_ctrl" style="position:absolute;border:solid thin blue;'+
						'top:30px;left:' + (_this.image_edit_width+30) +'px;bottom:0px;right:0px;">'+
						mv_get_loading_img('padding:30px') +  					
					'</div>'+
				'</div>');
			$j('#rsd_resource_edit').css('opacity',0);
			
			$j('#rsd_edit_img').remove();//remove any existing rsd_edit_img 
			
			//left side holds the image right size the controls /														
			$j(this).clone().attr('id', 'rsd_edit_img').appendTo('#clip_edit_disp').css({
				'position':'absolute',
				'top':'40%',
				'left':'20%',
				'opacity':0	
			});															
			if(rObj.mime.indexOf('image')!=-1){	 			
				//set width to default image_edit_width
				var maxWidth = _this.image_edit_width;		
				var mediaType = 'image';										
			}else{
				//set to default video size: 
				var maxWidth = _this.video_edit_width;
				var mediaType = 'video';
			}
			//assume we keep aspect ratio for the thumbnail that we clicked:			
			var tRatio = $j(this).height() / $j(this).width();
			if(	! tRatio )		
				var tRatio = 1; //set ratio to 1 if the width of the thumbnail can't be found for some reason
			
			js_log('set from ' +  $j('#rsd_edit_img').width()+'x'+ $j('#rsd_edit_img').height() + ' to init thumbimage to ' + maxWidth + ' x ' + parseInt( tRatio * maxWidth) );	
			//scale up image and swap with high res version
			$j('#rsd_edit_img').animate({
				'opacity':1,
				'top':'0px',
				'left':'0px',
				'width': maxWidth + 'px',
				'height': parseInt( tRatio * maxWidth)  + 'px'
			}, "slow"); // do it slow to give it a chance to finish loading the HQ version
			_this.loadHQImg(rObj, {'width':maxWidth}, 'rsd_edit_img', function(){
				$j('.mv_img_loader').remove();
			});
			//also fade in the container: 
			$j('#rsd_resource_edit').animate({
				'opacity':1,
				'background-color':'#FFF',
				'z-index':99
			});			
			_this.doMediaEdit( rObj , mediaType );			
		});
	},
	loadHQImg:function(rObj, size, target_img_id, callback){		
		//get the HQ image url: 
		rObj.pSobj.getImageObj( rObj, size, function( imObj ){			
			rObj['url'] = imObj.url;
			//see if we need to animate some transition
			var newSize = false;
			if( size.width != imObj.width ){ 
				js_log('loadHQImg:size mismatch: ' + size.width + ' != ' + imObj.width );
				newSize={
					'width':imObj.width + 'px',
					'height':imObj.height + 'px'
				}
				//update the rObj (hopefully this happens before people select their crop)
				rObj['width'] = imObj.width;
				rObj['height'] = imObj.height;
				//set the target id to the new size: 
				$j('#'+target_img_id).animate( newSize );
			}else{		
				js_log('using req size: ' + imObj.width + 'x' + imObj.height);
				$j('#'+target_img_id).animate( {'width':imObj.width+'px', 'height' : imObj.height + 'px'});
			}
			//don't swap it in untill its loaded: 
			var img = new Image();		
			// load the image image: 				
			$j(img).load(function () { 
	                 $j('#'+target_img_id).attr('src', imObj.url);                 
	                 //let the caller know we are done and what size we ended up with: 
	                 callback();	                 
				}).error(function () { 
					js_log("Error with:  " +  imObj.url);
				}).attr('src', imObj.url);   
		});		
	},
	//loads the media editor:
	doMediaEdit:function( rObj , mediaType){
		var _this = this;
		var mvClipInit = {
				'rObj':rObj, //the resource object	
				'parent_ct':'rsd_resource_edit',
				'clip_disp_ct':'clip_edit_disp',
				'control_ct': 'clip_edit_ctrl',
				'media_type': mediaType,
				'p_rsdObj': _this							
						
		};
		var loadLibs =  {'mvClipEdit':'libSequencer/mv_clipedit.js'};		
		if( mediaType == 'image'){
			//load the croping library:
			loadLibs['$j.Jcrop']='jquery/plugins/Jcrop/js/jquery.Jcrop.js';
			//@@todo integrate css calls into mvJsLoader or move jcrop css
			loadExternalCss( mv_embed_path + 'jquery/plugins/Jcrop/css/jquery.Jcrop.css');				
		}
		//load the library:
		mvJsLoader.doLoad( loadLibs,
			function(){
				js_log('done loading libs: mvClipEdit + Jcrop');
				//run the image clip tools 
				_this.cEdit = new mvClipEdit( mvClipInit );
		});
	},
	checkImportResource:function( rObj, callback){
		//check if the resource is "locally accesible" 
		if( rObj.pSobj.local ){
		 	callback( rObj );
		}else{
			var _this = this;
			var cp = rObj.pSobj.cp;
			
			//first check if the resource is not already on this wiki: 
			var target_resource_title = cp.resource_prefix +  rObj.titleKey;
			reqObj={'action':'query', titles:target_resource_title};
			do_api_req( reqObj, this.local_wiki_api_url, function(data){				
				if( ! data.query.pages['-1'] ){
					//resource is already present: 
					callback( rObj );
				}else{
					var base_resource_desc = '{Information '+
					'|Description= ' + rObj.title + ' imported from ' + '[' + cp.homepage + 
								 ' ' + cp.title+']' + "\n" +
					'|Source=' + '[' + rObj.link +' Original Source]'+ "\n" +
					'|Author= US government' +"\n"+
					'|Date= October 1st 2008' +"\n"+
					'|Permission=' +"\n"+
					'|other_versions=' +"\n"+
					'}}';
					//@@ show user dialog to import the resource
					$j( '#'+ _this.target_id ).append('<div id="rsd_resource_import" '+ 
					'style="position:absolute;top:0px;left:0px;width:100%;height:100%;background-color:#FFF;">' +
						'<h3>Resource: ' + rObj.title + ' needs to be imported to this wiki</h3>'+										
							rObj.pSobj.getEmbedHTML( rObj, {'max_height':'300'} )+ //get embedHTML:
							'<br>'+
							//output the rendered and non-renderd version of description for easy swiching:
							'<strong>Resource Description:</strong>'+
							'<a id="rsd_import_aedit" href="#">Edit</a>'+			
							'<a style="display:none;" id="rsd_import_apreview" href="#">Preview</a>'+		
							'<div id="rsd_impoart_desc" syle="width:60%;border:solid thin black;height:300px;overflow:auto;">'+
								mv_get_loading_img('position:absolute;top:5px;left:5px', 'mv_img_loader') +
							'</div>'+ 
							'<textarea id="rsd_import_ta" style="display:none" id="mv_img_desc" rows="4" cols="30">'+
								base_resource_desc + 
							'</textarea><br>'+									
						'<input id="rsd_import_doimport" type="button" value="Do Import Resource">'+
						'<a href="#">Cancel Import</a>'+				 
					'</div>');			
					//load the preview text: 
					_this.getParsedWikiText( base_resource_desc, function( o ){
						$j('#rsd_impoart_desc').html(o);
					});
					//add bidings: 
					$j('#rsd_import_aedit').click(function(){
						$j(this).hide();
						$j('#rsd_impoart_desc').hide();
						$j('#rsd_import_apreview,#rsd_import_ta').show();				
					});
					$j('#rsd_import_apreview').click(function(){
						$j(this).hide();
						$j('#rsd_impoart_desc').show().html(
							mv_get_loading_img('position:absolute;top:5px;left:5px', 'mv_img_loader') 
						);
						//load the preview text: 
						_this.getParsedWikiText( $j('#rsd_import_ta').val() , function( o ){
							$j('#rsd_impoart_desc').html(o);
						});
					});
					$j('#rsd_import_doimport').click(function(){
						//replace the parent with progress bar: 
						$j('rsd_resource_import').html(
							'<h3>Importing asset</h3>'+
							mv_get_loading_img('position:absolute;top:5px;left:5px', 'mv_img_loader') 
						);			
						//get an edittoken: 
						var reqObj = {'action':'query','prop':'info','intoken':'edit','titles': rObj.titleKey };
						do_api_req( reqObj, cp.api_url,function(data){
							js_log('edit token: ' + data.page[0]['edittoken']);
						});
						
					});		
				}				
			});													
		}
	},
	previewResource:function( rObj ){
		var _this = this;
		this.checkImportResource( rObj, function(){		
			//put another window ontop:
			$j( '#'+ _this.target_id ).append('<div id="rsd_resource_preview" '+ 
					'style="position:absolute;top:0px;left:0px;width:100%;height:100%;background-color:#FFF;">' +
						'<h3>preview resource: ' + rObj.title + '</h3>'+
						'<div id="rsd_preview_display" style="position:absolute;width:100%;bottom:30px;>'+
							mv_get_loading_img('position:absolute;top:30px;left:30px', 'mv_img_loader') + 
						'</div>'+
						'<div id="rsd_preview_control" style="position:absolute;width:60%;left:40%;bottom:0px;height:30px;">'+
							'<input type="button" id="preview_do_insert" value="Do Insert">'+
							'<a href="#" id="preview_close">Close Preview</a>'+
						'</div>'+
					'</div>');
			//add bindings: 
			$j('#preview_do_insert').click(function(){
				_this.insertResource( rObj );
			});
			$j('#preview_close').click(function(){
				$j('#rsd_resource_preview').remove();
			});
		});
	},	
	getParsedWikiText:function( wikitext, title,  callback ){
		var reqObj = {
			'action':'parse', 
			'text':wikitext
		};
		do_api_req( reqObj,  this.local_wiki_api_url, function(data){			
			callback( data.parse['*'] );
		});	
	},	
	insertResource:function( rObj){
		
	},
	setResultBarControl:function( ){
		var _this = this;
		var box_dark_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/box_layout_icon_dark.png';
		var box_light_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/box_layout_icon.png';
		var list_dark_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/list_layout_icon_dark.png';
		var list_light_url 	= mv_embed_path + 'skins/' + mv_skin_name + '/images/list_layout_icon.png';
		
		
		$j('#rsd_results').append('<div id="rds_results_bar">'+
			'<span style="position:relative;top:-5px;font-style:italic;">'+
				getMsg('rsd_layout')+' '+
			'</span>'+
				'<img id="msc_box_layout" ' +
					'title = "' + getMsg('rsd_box_layout') + '" '+ 
					'src = "' +  ( (_this.result_display_mode=='box')?box_dark_url:box_light_url ) + '" ' +			
					'style="width:20px;height:20px;cursor:pointer;"> ' + 
				'<img id="msc_list_layout" '+
					'title = "' + getMsg('rsd_list_layout') + '" '+
					'src = "' +  ( (_this.result_display_mode=='list')?list_dark_url:list_light_url ) + '" '+			
					'style="width:20px;height:20px;cursor:pointer;">'+			
			'<span style="position:absolute;right:5px;">'+ getMsg('rsd_results_desc', new Array('1 - 20', '420'))+'</span>'+
			'</div>'
		);
				
		$j('#msc_box_layout').hover(function(){			
			$j(this).attr("src", box_dark_url );
		}, function(){ 
			$j(this).attr("src",  ( (_this.result_display_mode=='box')?box_dark_url:box_light_url ) );		
		}).click(function(){	
			$j(this).attr("src", box_dark_url);
			$j('#msc_list_layout').attr("src", list_light_url);
			_this.setDispMode('box');
		});
		
		$j('#msc_list_layout').hover(function(){
			$j(this).attr("src", list_dark_url);
		}, function(){
			$j(this).attr("src", ( (_this.result_display_mode=='list')?list_dark_url:list_light_url ) );		
		}).click(function(){
			$j(this).attr("src", list_dark_url);
			$j('#msc_box_layout').attr("src", box_light_url);
			_this.setDispMode('list');
		});
	},
	selectTab:function( selected_cp_id ){
		js_log('select tab: ' + selected_cp_id);
		this.disp_item =selected_cp_id;
		//set display to unselected: 
		for(var cp_id in  this.content_providers){
			cp = this.content_providers[ cp_id ];
			if( (selected_cp_id == 'combined' && cp.checked ) || selected_cp_id == cp_id){
				cp.d = 1;
			}else{
				cp.d = 0;
			}			
		}	
		//redraw tabs
		this.drawTabs();
		//update the search results: 
		this.runSearch();	 		
	},
	setDispMode:function(mode){
		js_log('setDispMode:' + mode);
		this.result_display_mode=mode;	
		//run /update search display:
		this.drawOutputResults();
	}
}
//default values: 
// tag_name@{attribute}
var rsd_default_rss_item_mapping = {
	'poster'	: 'media:thumbnail@url',
	'roe_url'	: 'media:roe_embed@url',
	'title'		: 'title',
	'link'		: 'link',
	'desc'		: 'description'
}
var mvBaseRemoteSearch = function(initObj) {
	return this.init(initObj);
};
mvBaseRemoteSearch.prototype = {
	
	completed_req:0,
	num_req:0,	
		
	resultsObj:{},
	//init the object: 
	init:function( initObj ){		
		js_log('mvBaseRemoteSearch:init');
		for(var i in initObj){
			this[i] = initObj[i];
		}
		return this;
	},
	/*
	* Parses and adds video rss based input format
	* @data XML 		data to parse
	* @provider_url 	the source url (used to generate absolute links)  
	*/
	addRSSData:function( data , provider_url ){
		var _this = this;
		var http_host = '';
		var http_path = '';		
		if(provider_url){
			pUrl =  parseUri( provider_url );
			http_host = pUrl.protocol +'://'+ pUrl.authority;  
			http_path = pUrl.directory;
		}
		items = data.getElementsByTagName('item');
		$j.each(data.getElementsByTagName('item'), function(inx, item){		
			var rObj ={};			
			for(var i in rsd_default_rss_item_mapping){								
				var selector = rsd_default_rss_item_mapping[i].split('@');
				
				var tag_name = selector[0];
				var attr_name = null;								
				
				if( selector[1] )
					attr_name = selector[1];
				
				//grab the first match 
				var node = item.getElementsByTagName( tag_name )[0];
				//js_log('node: ' + node +  ' nv:' +  $j(node).html() + ' nv[0]'+ node.innerHTML + 
				//' cn' + node.childNodes[0].nodeValue  );				
					
				if( node!=null && attr_name == null ){
					if( node.childNodes[0] != null){			
						rObj[i] =  node.textContent;						
					}			
				}	
								
				if( node!=null && attr_name != null)
					rObj[i] = $j(node).attr( attr_name );									
			}	
			//make relative urls absolute:
			var url_param = new Array('src', 'poster'); 
			for(var j=0; j < url_param.length; j++){
				var p = url_param[j];
				if(typeof rObj[p] != 'undefined'){
					if( rObj[p].substr(0,1)=='/' ){				
						rObj[p] = http_host + rObj[p];
					}
					if( parseUri( rObj[i] ).host ==  rObj[p]){
						rObj[p] = http_host + http_path + rObj[p];
					}
				}
			}			
			//add pointer to parent serach obj:
			rObj['pSobj'] = _this;
			//add the result to the result set: 
			_this.resultsObj[inx] = rObj;			
		});
	},
	//by default just retrun the existing image: 
	getImageObj:function( rObj, size, callback){
		callback( {'url':rObj.poster} );
	}
}
/*
* api modes (implementations should call these objects which inherit the mvBaseRemoteSearch  
*/
var metavidSearch = function(initObj) {		
	return this.init(initObj);
};
metavidSearch.prototype = {
	reqObj:{  //set up the default request paramaters
		'order':'recent',
		'feed_format':'rss'		
	},
	init:function( initObj ){
		//init base class and inherit: 
		var baseSearch = new mvBaseRemoteSearch( initObj );
		for(var i in baseSearch){
			if(typeof this[i] =='undefined'){
				this[i] = baseSearch[i];
			}else{
				this['parent_'+i] =  baseSearch[i];
			}
		}
	},
	getImageObj:function( rObj, size, callback ){
		js_log('metavidSearch:getImageObj:'+size + ' s:' + size ); 
		//metavid uses a dynamic image request url:
		var pparts = this.parseURI( rObj.poster );		
		callback( {'url':rObj.poster} );
	},
	getSearchResults:function(){
		var _this = this;
		//start loading:
		_this.loading= 1;
		js_log('metavidSearch::getSearchResults()');
		//proccess all options
		url = this.cp.api_url;
		//add on the req_param
		for(var i in this.reqObj){
			url += '&' + i + '=' + this.reqObj[i];
		}
		//do basic query:
		this.last_query = $j('#rsd_q').val();
		url += '&f[0][t]=match&f[0][v]=' + $j('#rsd_q').val();
		do_request(url, function(data){ 
			//should have an xml rss data object:
			_this.addRSSData( data , url );
			//done loading: 
			_this.loading=0;
		});
	}	
}

var mediaWikiSearch = function( initObj ) {		
	return this.init( initObj );
};
mediaWikiSearch.prototype = {
	init:function( initObj ){
		//init base class and inherit: 
		var baseSearch = new mvBaseRemoteSearch( initObj );
		for(var i in baseSearch){
			if(typeof this[i] =='undefined'){
				this[i] = baseSearch[i];
			}else{
				this['parent_'+i] =  baseSearch[i];
			}
		}
	},
	getSearchResults:function(){
		var _this = this;
		this.loading=true;
		js_log('f:getSearchResults for:' + $j('#'+this.target_input).val() );		
		//empty out the current results: 
		this.resultsObj={};
		//do two queries against the Image / File / MVD namespace: 								
		//build the image request object: 
		var reqObj = {
			'action':'query', 
			'generator':'search',
			'gsrsearch': encodeURIComponent( $j('#rsd_q').val() ),  
			'gsrnamespace':6, //(only search the "file" namespace (audio, video, images)
			'gsrwhat':'title',
			'gsrlimit':30,
			'prop':'imageinfo|revisions|categories',
			'iiprop':'url|mime',
			'iiurlwidth': parseInt( this.rsd.thumb_width ),
			'rvprop':'content'
		};				
		//set up the number of request: 
		this.completed_req=0;
		this.num_req=1;
		this.last_query = $j('#rsd_q').val();
		//setup the number of requests result flag: 				
		//do_api_req( reqObj, this.cp.api_url , function(data){			
			//parse the return data
		//	_this.addResults( data);				
		//	_this.checkRequestDone();			
		//});							
		//also do a request for page titles (would be nice if api could query both at the same time) 
		reqObj['gsrwhat']='text';
		do_api_req( reqObj, this.cp.api_url , function(data){
			//parse the return data
			_this.addResults( data);
			//_this.checkRequestDone(); //only need if we do two queries one for title one for text
			_this.loading = false;
		});			
	},	
	addResults:function( data ){	
		var _this = this
		//make sure we have pages to idoerate: 
		if(data.query && data.query.pages){
			for(var page_id in  data.query.pages){
				var page =  data.query.pages[ page_id ];				
				this.resultsObj[page_id]={
					'titleKey':page.title,
					'link':'null',					
					'title':page.title.replace(/File:|.jpg|.png|.svg|.ogg|.ogv/ig, ''),
					'poster':page.imageinfo[0].thumburl,
					'thumbwidth':page.imageinfo[0].thumbwidth,
					'thumbheight':page.imageinfo[0].thumbheight,
					'mime':page.imageinfo[0].mime,
					'src':page.imageinfo.url,
					'desc':page.revisions[0]['*'],		
					//add pointer to parent serach obj:
					'pSobj':_this,			
					'meta':{
						'categories':page.categories
					}
				}
				for(var i in this.resultsObj[page_id]){
					//js_log('added '+ i +' '+ this.resultsObj[page_id][i]);
				}
			}
		}else{
			js_log('no results:' + data);
		}
	},	
	//check request done used for when we have multiple requests to check before formating results. 
	checkRequestDone:function(){
		//display output if done: 
		this.completed_req++;
		if(this.completed_req == this.num_req){
			this.loading = 0;
		}
	},	
	getImageObj:function( rObj, size, callback ){			
		//build the query to get the req size image: 
		var reqObj = {
			'action':'query',
			'format':'json',
			'titles':rObj.titleKey,
			'prop':'imageinfo',
			'iiprop':'url|size|mime' 
		}
		//set the width: 
		if(size.width)
			reqObj['iiurlwidth']= size.width;				 
 
		do_api_req( reqObj, this.cp.api_url , function(data){
			var imObj = {};
			for(var page_id in  data.query.pages){
				var iminfo =  data.query.pages[ page_id ].imageinfo[0];
				//check if thumb size > than image size and is jpeg or png (it will not scale well above its max res) 
				if( ( iminfo.mime=='image/jpeg' || iminfo=='image/png' ) &&
					iminfo.thumbwidth > iminfo.width ){ 		
					imObj['url'] = iminfo.url;
					imObj['width'] = iminfo.width;
					imObj['height'] = iminfo.height;					
				}else{					
					imObj['url'] = iminfo.thumburl;
					imObj['width'] = iminfo.thumbwidth;
					imObj['height'] = iminfo.thumbheight;
				}
			}
			js_log('getImageObj: get: ' + size.width + ' got url:' + imObj.url);			
			callback( imObj ); 
		});
	},
	//the insert image function   
	insertImage:function( cEdit ){
		if(!cEdit)
			var cEdit = _this.cEdit;		
	},
	getEmbedHTML: function( rObj , options) {
		//set up the output var with the default values: 
		var outOpt = { 'width': rObj.width, 'height': rObj.height, 'src' : rObj.url};
		if( options.max_height ){			
			outOpt.height = (options.max_height > rObj.height) ? rObj.height : options.max_height;	
			outOpt.width = (rObj.width / rObj.height) *outOpt.height;	
		}		
		if(rObj.mime.indexOf('image')!=-1){
			return '<img src="' + outOpt.src  + '" style="width:' + outOpt.width + 'px;height:' + outOpt.height +'px">';
		}
		js_log('ERROR:unsupored mime type: ' + rObj.mime);
	},
	//returns the inline wikitext for insertion (template based crops for now) 
	getEmbedWikiText: function( rObj , callback ){		
			//set default layout to right justified
			var layout = ( rObj.layout)? rObj.layout:"right"
			//if crop is null do simple output: 
			if( rObj.crop == null)
				callback( '[[' + rObj.titleKey + '|layout' + '|'+rObj.width + 'px|' + rObj.inlineDesc + ']]' ); 	
			
			//using the preview crop template: http://en.wikipedia.org/wiki/Template:Preview_Crop
			//should be replaced with server side cropping 
			callback( '{{Preview Crop '+
'|Image   = ' + rObj.titleKey + "\n" +
'|bSize   = ' + rObj.width + "\n" + 
'|cWidth  = ' + rObj.crop.w + "\n" +
'|cHeight = ' + rObj.crop.h + "\n" +
'|oTop    = ' + rObj.crop.y + "\n" +
'|oLeft   = ' + rObj.crop.x + "\n" +
'|Location =' + layout + "\n" +
'|Description =' + rObj.inlineDesc + "\n" +
'}}');
	}
}