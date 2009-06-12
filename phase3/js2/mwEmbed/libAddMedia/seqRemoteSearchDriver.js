/*the sequence remote search driver
	 extends the base remote search driver with sequence specific stuff.		 could seperate this out into seperate lib.
*/
loadGM({
	
});
var seqRemoteSearchDriver = function(iObj){
	return this.init( iObj )
}
seqRemoteSearchDriver.prototype = {
	sequence_add_target:false,
	init:function( this_seq ){
		var _this = this;
		js_log("init:seqRemoteSearchDriver");
		//setup remote search driver with a seq parent: 
		this.pSeq = this_seq;
		var iObj = {			
			'target_container'	: '#cliplib_ic',
			'local_wiki_api_url': this_seq.getLocalApiUrl(),										
			'instance_name'		: this_seq.instance_name + '.mySearch',		
			'default_query'		: this_seq.plObj.title							
		}		
		//inherit the remoteSearchDriver properties:n		
		var tmpRSD = new remoteSearchDriver( iObj );
		for(var i in tmpRSD){
			if(this[i]){
				this['parent_'+i] = tmpRSD[i];
			}else{
				this[i] = tmpRSD[i];
			}
		}
		//extend actions:
		this.pSeq.parent_do_refresh_timeline = this.pSeq.do_refresh_timeline;
		this.pSeq.do_refresh_timeline = function(){
			js_log("seqRs refresh chain::" + _this.pSeq.disp_menu_item);
			//call the parent
			_this.pSeq.parent_do_refresh_timeline();
			//add our local bindings if our window is 'active'
			if(_this.pSeq.disp_menu_item == 'cliplib'){
				_this.addResultBindings();
			}
		}
	},	
	resourceEdit:function(){
		var _this = this;	
		
	},
	addResultBindings:function(){
		//set up seq:		
		var _this = this;
		//setup parent bindings:
		this.parent_addResultBindings();
		
		//add an additional drag binding					
		$j( '.rsd_res_item' ).draggable('destroy').draggable({
			helper:function(){
				return $j( this ).clone().appendTo('body').css({'z-index':9999}).get(0);
			},		
			revert:'invalid',						
			start:function(){
				js_log('start drag');
			}								
		});				
		$j(".mv_clip_drag").droppable( 'destroy' ).droppable({
			accept: '.rsd_res_item',
			over:function(event, ui){
				js_log("over : mv_clip_drag: " + $j(this).attr('id') );
				$j(this).css('border-right', 'solid thick red');				
			},
			out:function(event, ui){
				$j(this).css('border-right', 'solid thin white');				
			},
			drop: function(event, ui) {
				$j(this).css('border-right', 'solid thin white');
				js_log("Droped: "+ $j(ui.draggable).attr('id') +' on ' +  $j(this).attr('id') );
				_this.sequence_add_target =  $j(this).attr('id');
				//load the orginal draged item
				var rObj = _this.getResourceFromId( $j(ui.draggable).attr('id') );							
				_this.resourceEdit(rObj, ui.draggable);				
			}
		});		
	
	},
	insertResource:function(rObj){
		js_log("SEQ insert resource");
	},
	getClipEditControlActions:function(){
		var _this = this;	
		return {
			'insert_seq':function(rObj){
				_this.insertResource( rObj )
			},
			'cancel'	:function(rObj){
				_this.cancelClipEditCB( rObj )
			}
		};
	},
	resourceEdit:function(rObj, rsdElement){
		var _this = this;
		//don't resize to default (full screen behavior) 
		_this.dmodalCss = {};
		//open up a new target_contaienr: 
		if($j('#seq_resource_import').length == 0)
			$j('body').append('<div id="seq_resource_import" style="position:relative"></div>');
			
		$j('#seq_resource_import').dialog('destroy').dialog({
			bgiframe: true,
			width:640,
			height:480,
			modal: true,
			buttons: { 
				"Cancel": function() { 
						$j(this).dialog("close"); 
					} 
				}
		});
		_this.target_container = '#seq_resource_import';		
		//do parent resource edit (with updated target)
		this.parent_resourceEdit(rObj, rsdElement);				
	},
	cancelClipEditCB:function(){
		js_log('seqRSD:cancelClipEditCB');
		$j('#seq_resource_import').dialog('close').dialog('destroy').remove();			
	}
};