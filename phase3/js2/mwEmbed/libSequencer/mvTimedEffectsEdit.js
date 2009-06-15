/*
* mvTimedEffectsEdit
* 
* for now just simple single stack transition control
*  
*/

//add our local msgs
loadGM({ 
	"transition_in"  : "Transition In",
	"transition_out" : "Transition Out",
	"effects"		 : "Effects Stack"
});


var default_timed_effect_values = {
	'rObj':	null,		 // the resource object
	'clip_disp_ct':null, //target clip disp
	'control_ct':null,	 //control container
	 	
	'parent_ct': null,	 //parent container
	'p_seqObj': null,	 //parent sequence Object
	
	'edit_action': null, //the requested edit action						
}
var mvTimedEffectsEdit =function(iObj) {		
	return this.init(iObj);
};
//set up the mvSequencer object
mvTimedEffectsEdit.prototype = {
	//the menu_items Object contains: default html, js setup/loader functions
	menu_items : {
		'transin':{
			'title':gM('transition_in'),
			'clip_attr':'transIn',
			'doEdit':function(_this){
				_this.doTransitionDisplayEdit('transin');
			}						
		},
		'transout':{
			'title':gM('transition_out'),
			'clip_attr':'transOut',
			'doEdit':function(_this){
				_this.doTransitionDisplayEdit('transout');	
			}				
		},
		'effects':{
			'title':gM('effects'),
			'clip_attr':'Effects',
			'doEdit':function(_this){
				//display 	
			}	
		}
	},
	init:function(iObj){
		//init object: 
		for(var i in default_timed_effect_values){
			if( iObj[i] ){   
				this[i] = iObj[i];
			}
		}		
		this.doEditMenu();
	},	
	doEditMenu:function(){
		var _this = this;
		//add in subMenus if set
		//check for submenu and add to item container
		
		//update the default edit display (if we have a target)
		var tTarget = 'transin';
		if(this.rObj.transOut)
			tTarget = 'transout';
		if(this.rObj.effects)
			tTarget = 'effects';		
			
		var o='';		
		var tabc ='';					
		o+= '<div id="mv_submenu_timedeffect" style="width:90%">';
		o+='<ul>';		  
		var inx =0;		
		$j.each(this.menu_items, function(sInx, mItem){					
			//check if the given editType is valid for our given media type		
			o+=	'<li>'+ 
					'<a id="mv_te_'+sInx+'" href="#te_' + sInx + '">' + mItem.title + '</a>'+
				'</li>';															
			tabc += '<div id="te_' + sInx + '" style="overflow:auto;" ></div>';																									
		});
		o+= '</ul>' + tabc;
		o+= '</div>';
		//add sub menu container with menu html: 			
		$j('#'+this.control_ct).html( o ) ;		
		js_log('should have set: #'+this.control_ct + ' to: ' + o);		
		
				
		//set up bindins:	 
		$j('#mv_submenu_timedeffect').tabs({
			selected: 0,
			select: function(event, ui) {									
				_this.doDisplayEdit( $j(ui.tab).attr('id').replace('mv_te_', '') );
			}				
		}).addClass('ui-tabs-vertical ui-helper-clearfix');
		js_log('setup tabs #' + this.control_ct);
		
		//close left: 
		$j("#mv_submenu_clipedit li").removeClass('ui-corner-top').addClass('ui-corner-left');								
	},
	doDisplayEdit:function( tab_id ){		
		if( !this.menu_items[ tab_id ] ){
			js_log('error: doDisplayEdit missing item:' + tab_id);  	
		}else{
			//use the menu_item config to map to function display
			this.menu_items[tab_id].doEdit(this);
		}					
	},
	doTransitionDisplayEdit:function(target_item){
		var apendTarget = 'te_' + target_item;
		//check if we have a transition
		if(!this.rObj[ this.menu_items[ target_item ].clip_attr ]){
			this.getTransitionList( apendTarget );
			return ;
		}
		cTran = this.rObj[ this.menu_items[ target_item ].clip_attr ];
		var o='<h3>Edit Transition</h3>';
		o+='Type: ' +
			'<select class="te_select_type">';
		for(var typeKey in mvTransLib.type){			
			var selAttr = (cTran.type == typeKey)?' selected':'';
			o+='<option	value="'+typeKey+'"'+ selAttr +'>'+typeKey+'</option>';
		}	
		o+='</select>';
		o+='<span class="te_select_subtype">Sub Type:'+
		   '<select class="te_select_subtype">';
		for(var subTypeKey in mvTransLib.type[ cTran.type ]){
			var selAttr = (cTran.subtype == typeKey)?' selected':'';
			o+='<option	value="'+subTypeKey+'"'+ selAttr +'>'+typeKey+'</option>';
		}
		o+='</select>'+		
		   '</span>';
		js_log("update: " + apendTarget);
		//set up bidings: 
		$j(apendTarget).append(o).children('.te_select_type')
			.change(function(){
				//update subtype listing: 
				var o = '';			
				$j(apendTarget + ' .te_select_subtype').html();
			});
		$j('te_' + target_item).html(o);
	},
	getTransitionList:function(target_out){
		js_log("getTransitionList");
		var o= '';
		for(var type in mvTransLib['type']){
			js_log('on tran type: ' + i);			
			var base_trans_name = i;
			var tLibSet = mvTransLib['type'][ type ];
			for(var subtype in tLibSet){			
				o+='<img style="float:left;padding:10px;" '+
					'src="' + mvTransLib.getTransitionIcon(type, subtype)+ '">';		
			}
		}	
		$j(target_out).html(o);
	}		
}