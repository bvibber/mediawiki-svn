//the core javascript file of the wiki@home extension

//load msgs:
loadGM({
	
});

//js2AddOnloadHook ensures that the dom and core libraries are ready:
js2AddOnloadHook(function(){
	wikiAtHomeDriver.init();			
});

var wikiAtHomeDriver = {
	init: function(){
		var _this = this;
		//first get an edit token
		//look for a job:
		_this.lookForJob();
		if(_this.assinedJob){
			_this.proccessJob();
		}else{
			//update interface that nothing is avalible (look again in 20 seconds) 
		}		
	},
	lookForJob: function(){
		
		do_api_req({
			'data':{
				'action' 	: 'wikiathome',
				'getnewjob'	: true			
				}
		},function(data){
			//if we have a job update status to proccessing
			
		});
	},
	proccessJob:function(){
		var cat = _this.assinedJob; 
		debugger;
	}
}