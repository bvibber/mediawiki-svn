/*the sequence remote search driver
 	extends the base remote search driver with sequence specific stuff. 	
 	could seperate this out into seperate lib.
*/
var seqRemoteSearchDriver = function(initObj){
	return this.init( initObj )
}
seqRemoteSearchDriver.prototype = {
	init:function( initObj ){
		//inherit the remoteSearchDriver properties: 
		var tmpRSD = new remoteSearchDriver( initObj );
		for(var i in tmpRSD){
			if(this[i]){
				this['parent_'+i] = tmpRSD[i];
			}else{
				this[i] = tmpRSD[i];
			}
		}
	},	
	addResultBindings:function(){
		//setup the default bindings
		this.parent_addResultBindings();
		//add an additional drag binding
		$j('.rsd_res_item').draggable({
				
		}); 
	},
	resourceEdit:function(rObj, rsdElement){
	
	}
}