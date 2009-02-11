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
			this[i] = tmpRSD[i];
		}
	}
}