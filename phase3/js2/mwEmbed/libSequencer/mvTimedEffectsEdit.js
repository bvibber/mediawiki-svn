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


var default_timedeffect_values = {
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
		for(var i in default_clipedit_values){
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
		var o='';		
		var tabc ='';					
		o+= '<div id="mv_submenu_timedeffect">';
		o+='<ul>';		 
		var inx =0;		
		$j.each(this.menu_items, function(sInx, na){					
			//check if the given editType is valid for our given media type		
			o+=	'<li>'+ 
					'<a id="mv_te_'+sInx+'" href="#te_' + sInx + '">' + gM('te_' + sInx ) + '</a>'+
				'</li>';															
			tabc += '<div id="te_' + sInx + '" style="overflow:auto;" ></div>';																									
		});
		o+= '</ul>' + tabc;
		o+= '</div>';
		//add sub menu container with menu html: 		
		$j('#'+this.control_ct).html( o ) ;			
		//set up bindings:	 
		$j('#mv_submenu_timedeffect').tabs({
			selected: 0,
			select: function(event, ui) {									
				_this.doDisplayEdit( $j(ui.tab).attr('id').replace('mv_te_', '') );
			}				
		}).addClass('ui-tabs-vertical ui-helper-clearfix');
		//close left: 
		$j("#mv_submenu_clipedit li").removeClass('ui-corner-top').addClass('ui-corner-left');		
		
		//update the default edit display (if we have a target)
		var tTarget = 'transin';
		if(cClip.transOut)
			tTarget = 'transout';
		if(cClip.effects)
			tTarget = 'effects';
		
		_this.doDisplayEdit( 'transin' );
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
		//check if we have a transition
		if(!cClip[ this.menu_items[ target_item ].clip_attr ]){
			this.getTransitionList();
			return ;
		}
		cTran = cClip[ this.menu_items[ target_item ].clip_attr ];
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
		//set up bidings: 
		$j(apendTarget).append(o).children('.te_select_type')
			.change(function(){
				//update subtype listing: 
				var o = '';			
				$j(apendTarget + ' .te_select_subtype').html();
			});
		$j('te_' + target_item).html(o);
	}		
}