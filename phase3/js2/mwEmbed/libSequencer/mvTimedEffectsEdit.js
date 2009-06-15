/*
* mvTimedEffectsEdit
* 
* for now just simple single stack transition control
*  
*/

var default_timedeffect_values = {
	'rObj':	null,		 // the resource object
	'clip_disp_ct':null,//target clip disp
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
			
		},
		'transout':{
			
		},
		'effects':{
			
		}
	},
	init:function(iObj){
		//init object: 
		for(var i in default_clipedit_values){
			if( iObj[i] ){   
				this[i] = iObj[i];
			}
		}
	}	
}