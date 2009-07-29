//the core javascript file of the wiki@home extension

//load msgs:
loadGM({
	'wah-menu-jobs'	: "Jobs",
	'wah-menu-stats': "Stats",
	'wah-menu-pref'	: "Prefrences",
	'wah-loading'	: 'loading wiki@home interface <blink>...</blink>',
	
	'wah-lookingforjob'	: "Looking For a Job <blink>...</blink>",
	'wah-nojobfound' 	: "No Job Found, Will retry in $1 seconds",
	'wah-notoken-login' : "Could not get a token. Are you logged in?"
	
});


wahConfig = {
	'wah_container': '#wah_container'
};

//js2AddOnloadHook ensures that the dom and core libraries are ready:
js2AddOnloadHook(function(){
	//set up the dependency load request: 
	var depReq = [
		[
			'mvBaseUploadInterface',
			'mvFirefoggRender',
			'mvFirefogg',
			'$j.ui'											 														
		], 
		[				
			'$j.ui.sortable',
			'$j.ui.progressbar',		
			'$j.ui.tabs',
			'$j.cookie'	
		]
	];				
	mvJsLoader.doLoadDepMode( depReq, function(){
		WikiAtHome.init( wahConfig );			
	});
});

var WikiAtHome = {
	menu_items:['jobs','stats', 'pref'],		
	init: function(){
		var _this = this;
		//proc config: 
		for(var i in wahConfig){
			_this[i] = wahConfig[i];
		}
		//fist see if we are even logged in: 
		if( !wgUserName ){
			$j( _this.wah_container ).html( gM('wah-notoken-login'));
		}else{
			//first get an edit token (title and api_url not needed
			//@@todo we should request a token for uploading/wikiathome?
			get_mw_token(false, false, function(token){
				if(!token){
					$j( _this.wah_container ).html( gM('wah-notoken-login'));
				}else{
					_this.eToken = token;
					//if we got an edit token load the interface tabs: 
					_this.loadInterface(); 
					
					//look for a job:
					_this.lookForJob();
					if(_this.assinedJob){
						_this.proccessJob();
					}else{
						//update interface that nothing is avalible (look again in 60 seconds) 
					}
				}
			});
		}
	},
	loadInterface: function(){
		var _this = this;
		var listHtml ='<div id="wah-tabs">'+
					'<ul>';
		var contHtml='';		
		//output the menu itmes:
		for(var i in _this.menu_items){
			var item = _this.menu_items[i];
			listHtml+='<li><a href="#tab-' + item + '">' + gM('wah-menu-'+item)+'</a></li>';
			contHtml+='<div id="tab-' + item + '" class="tab-content">' +					 				
				'</div>'; 				
		}
		listHtml+='</ul>';
		contHtml+='</div>';			
		$j( _this.wah_container ).html( listHtml +  contHtml );
		//apply bidings
		$j('#wah-tabs').tabs({
			select: function(event, ui) {									
				_this.selectTab( $j(ui.tab).attr('id').replace('rsd_tab_', '') );
			}	
		}).find(".ui-tabs-nav").sortable({axis:'x'});			
			
		//set tabs to initial layout 
		$j('#tab-jobs').html( 
			'<h2 class="wah-gen-status"></h2>' + 
			'<div class="progress-bar"></div>' + 
			'<div class="prograss-status"></div>'  
		 );
				
		
	},	
	lookForJob: function(){
		var _this = this;
		//set the big status 
		$j('#tab-jobs .wah-gen-status').html( gM('wah-lookingforjob') );				
			
		do_api_req({
			'data':{
				'action' 	: 'wikiathome',
				'getnewjob'	: true,
				'token'		: _this.eToken			
				}
		},function(data){
			//if we have a job update status to proccessing
			
		});
	},
	proccessJob:function(){
		var cat = _this.assinedJob; 		
	}
}