/*
	hanndles clip edit controls 
	'inoutpoints':0,	//should let you set the in and out points of clip
	'panzoom':0, 		//should allow setting keyframes and tweening modes			
	'overlays':0, 		//should allow setting "locked to clip" overlay tracks 
	'audio':0			//should allow controlling the audio volume (with keyframes) 
*/

gMsg['mv_crop']='Crop Image';
gMsg['mv_apply_crop']='Apply Crop to Image';
gMsg['mv_reset_crop']='Rest Crop';
gMsg['mv_insert_image_page']='Insert Into page';
gMsg['mv_preview_insert']= 'Preview Insert';
gMsg['mv_cancel_image_insert']='Cancel Image Insert';

var default_clipedit_values = {
	'rObj':	null, 		// the resource object
	'clip_disp_ct':null,//target clip disp
	'control_ct':null, 	//control container
	'media_type': null, //media type
	'p_rsdObj': null,	//parent remote search object
	'parent_ct': null 	//parent container
}
var mvClipEdit = function(initObj) {		
	return this.init(initObj);
};
mvClipEdit.prototype = {

	selTool:null, //selected tool
	crop: null, //the crop values
	base_img_src:null,
	
	init:function( initObj){
		//init object: 
		for(var i in default_clipedit_values){
			if( initObj[i] ){   
				this[i] = initObj[i];
			}
		}
		//check the media_type:
		js_log('mvClipEdit:: media type:' + this.media_type + 'base width: ' + rObj.width + ' bh: ' + rObj.height);			
		
		
		//could seperate out into media Types objects for now just call method
		if(this.media_type == 'image'){
			this.setUpImageCtrl();
		}else if(this.media_type=='jpeg'){
			this.setUpVideoCtrl();
		}		
	},
	setUpImageCtrl:function(){
		var _this = this;
		//by default apply Crop tool 
		$j('#'+this.control_ct).html(
			'<h3>Edit tools</h3>' + 				
					'<div class="mv_edit_button mv_crop_button_base" id="mv_crop_button" alt="crop" title="'+getMsg('mv_crop')+'"/>'+
					'<a href="#" class="mv_crop_msg">' + getMsg('mv_crop') + '</a> '+
					'<a href="#" style="display:none" class="mv_apply_crop">' + getMsg('mv_apply_crop') + '</a> '+
					'<a href="#" style="display:none" class="mv_rest_crop">' + getMsg('mv_reset_crop') + '</a> '+
				'<br style="clear:both"><br>'+
				
				'<div class="mv_edit_button mv_scale_button_base" id="mv_scale_button" alt="crop" title="'+getMsg('mv_scale')+'"></div>'+
				'<a href="#" class="mv_scale_msg">' + getMsg('mv_scale') + '</a><br>'+
				'<a href="#" style="display:none" class="mv_apply_scale">' + getMsg('mv_apply_scale') + '</a> '+
				'<a href="#" style="display:none" class="mv_rest_scale">' + getMsg('mv_reset_scale') + '</a> '+				
				
			'<h3>Inline Caption Description</h3>'+				
				'<textarea id="mv_img_desc" rows="4" cols="30"></textarea><br>'+
			'<h3>Actions</h3>'+
			'<input type="button" class="mv_insert_image_page" value="' + getMsg('mv_insert_image_page') + '"> '+				
			'<input type="button" style="font-weight:bold" class="mv_preview_insert" value="' + getMsg('mv_preview_insert')+ '"> '+		
			'<a href="#" class="mv_cancel_img_edit" title="' + getMsg('mv_cancel_image_insert')+'">' + getMsg('mv_cancel_image_insert') + '</a> '			
		);
		//add bidings: 
		$j('#mv_crop_button,.mv_crop_msg,.mv_apply_crop').click(function(){
			js_log('click:mv_crop_button: base width: ' + rObj.width + ' bh: ' + rObj.height);
			if($j('#mv_crop_button').hasClass('mv_crop_button_selected')){				
				_this.applyCrop();
			}else{
				js_log('click:turn on');
				_this.enableCrop();
			}
		}); 
		$j('.mv_rest_crop').click(function(){
			$j('.mv_apply_crop,.mv_rest_crop').hide();
			$j('.mv_crop_msg').show();
			$j('#mv_crop_button').removeClass('mv_crop_button_selected').addClass('mv_crop_button_base').attr('title',getMsg('mv_crop'));
			_this.rObj.crop=null;
			$j('#'+_this.clip_disp_ct ).empty().html(
				'<img src="'+ _this.rObj.url + '" id="rsd_edit_img">'
			)
		});
		$j('.mv_insert_image_page').click(function(){
			_this.applyCrop();
			//have the (parent remote search object) do the insert
			_this.p_rsdObj.insertResource( rObj );
		});
		$j('.mv_preview_insert').click(function(){
			_this.applyCrop();
			//copy over the desc text to the resouce object
			_this.p_rsdObj.previewResource( rObj );
		});
		$j('.mv_cancel_img_edit').click( function(){
			$j('#' + _this.parent_ct).fadeOut("fast");
		});
	},
	applyCrop:function(){
		var _this = this;
		$j('.mv_apply_crop').hide();
		$j('.mv_crop_msg').show();
		$j('#mv_crop_button').removeClass('mv_crop_button_selected').addClass('mv_crop_button_base').attr('title',getMsg('mv_crop'));
		js_log('click:turn off');
		if(_this.rObj.crop){
			//empty out and display croped:
			$j('#'+_this.clip_disp_ct ).empty().html(
				'<div id="mv_cropcotainer" style="overflow:hidden;position:absolute;'+
					'width:' + _this.rObj.crop.w + 'px;'+
					'height:' + _this.rObj.crop.h + 'px;">'+
					'<div id="mv_crop_img" style="position:absolute;'+
						'top:-' + _this.rObj.crop.y +'px;'+
						'left:-' + _this.rObj.crop.x + 'px;">'+
						'<img src="' + _this.rObj.url + '">'+
					'</div>'+
				'</div>'						
			);
		}
	},
	enableCrop:function(){
		var _this = this;
		$j('.mv_crop_msg').hide();
		$j('.mv_rest_crop,.mv_apply_crop').show();				
		$j('#mv_crop_button').removeClass('mv_crop_button_base').addClass('mv_crop_button_selected').attr('title',getMsg('mv_crop_done'));				
		$j('#' + _this.clip_disp_ct + ' img').Jcrop({
		 		onSelect: function(c){
		 			js_log('on select:' + c.x +','+ c.y+','+ c.x2+','+ c.y2+','+ c.w+','+ c.h);
		 			_this.rObj.crop = c;
		 		},
          			onChange: function(c){            				
          			}        				
		});
	},
	setUpVideoCtrl:function(){
		
	}
}