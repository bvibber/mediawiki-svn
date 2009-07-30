//the core javascript file of the wiki@home extension

//load msgs:
loadGM({
	'wah-menu-jobs'	: "Jobs",
	'wah-menu-stats': "Stats",
	'wah-menu-pref'	: "Prefrences",
	'wah-loading'	: 'loading wiki@home interface <blink>...</blink>',
	
	'wah-lookingforjob'	: "Looking For a Job <blink>...</blink>",
	'wah-nojobfound' 	: "No Job Found, Will retry in $1 seconds",
	'wah-notoken-login' : "Could not get a token. Are you logged in?",
	
	'wah-doing-job'		: "Job: <i>$1</i> on: <i>$2</i>",
	'wah-downloading'	: "Downloading File <i>$1%</i> done",
	'wah-needs-firefogg': "To particate in wiki@home you need to install firefogg."
	
});


wahConfig = {
	'wah_container'		: '#wah_container',
	//how many seconds to wait before looking for a job again (in seconds) 
	'jobsearch_delay'	: 90
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
					//if we load the interface oky then  
					if( _this.loadInterface() ){ 					
						//look for a job:
						_this.lookForJob();
						if(_this.assinedJob){
							_this.proccessJob();
						}else{
							//update interface that nothing is avalible (look again in 60 seconds) 
						}
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
				//_this.selectTab( $j(ui.tab).attr('id').replace('rsd_tab_', '') );
			}	
		}).find(".ui-tabs-nav").sortable({axis:'x'});			
			
		//set tabs to initial layout 
		$j('#tab-jobs').html( 
			'<h2 class="wah-gen-status"></h2>' + 
			'<div class="progress-bar" style="width:400px;heigh:20px;"></div>' + 
			'<div class="prograss-status" style="width:400px;heigh:20px;"></div>'  
		 );
		//make sure we have firefogg
			//check if we have firefogg installed (needed for transcoding jobs)
		this.myFogg = new mvFirefogg({
			'only_fogg':true
		});	
		
		if(!this.myFogg.firefoggCheck() ){			
			$j('#tab-jobs .progress-bar').hide().after( gM('wah-needs-firefogg') );			
							
			//if we don't have 3.5 firefox update link:
			if(!($j.browser.mozilla && $j.browser.version >= '1.9.1')) {
				$j('#tab-jobs .prograss-status').html(
					gM('fogg-use_latest_fox')
				);	
			}else{				
				//do firefogg install links:
				$j('#tab-jobs .prograss-status').html( 
					gM('fogg-please_install', _this.myFogg.getOSlink() )
				);	
			}
			return false;
		}
		return true;		
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
			if( data.wikiathome.nojobs ){				
				_this.delayLookForJob();
			}else{
				//we do have job proccess it
				_this.doProccessJob( data.wikiathome.job );
			}
		});
	},
	delayLookForJob:function(){
		var _this = this;
		var i=0;
		var delayJobUpdate = function(){
			i++;
			if(i == _this.jobsearch_delay){
				_this.lookForJob();
			}else{
				//update the delay msg:
				$j('#tab-jobs .wah-gen-status').html( gM( 'wah-nojobfound', seconds2npt(i)) );
			}
		}
		setTimeout(delayJobUpdate, 1000)
	},
	doProccessJob:function( job ){
		var _this = this;
		//update the status 
		$j('#tab-jobs .wah-gen-status').html(
			gM('wah-doing-job', [job.job_json.jobType, job.job_title] ) 
		);
		//set up the progressbar
		$j('#tab-jobs .progress-bar').progressbar({
			value: 0
		});
		//start proccessing the work flow based on work type
		if( job.job_json.jobType == 'transcode' ){
			//download the source footage
			_this.doTranscodeJob( job );
		} 		 	
	},
	doTranscodeJob : function( job ){
		var _this = this;		
				
		//get the url of the video we want to download
		do_api_req({
			'data':{
				'titles': job.job_fullTitle,
				'prop'	: 'imageinfo',
				'iiprop': 'url'
			}
		},function(data){
			for(var i in data.query.pages){
				_this.source_url = data.query.pages[i].imageinfo[0].url;
			}
			//have firefogg download the file:
			_this.myFogg.selectVideoUrl( _this.source_url );
			//check firefogg state and update status: 
			var updateDownoadState = function(){				
				if( _this.myFogg.state == 'downloading'){
					var percDone = _this.myFogg.downloadVideo.progress * 100; 
					$j('#tab-jobs .progress-bar').progressbar({
						value:  percDone
					});	
					$j('#tab-jobs .prograss-status').html(
						gM('wah-downloading',percDone)
					);
				}
			}
			setTimeout(updateDownoadState, 100);
		});
	
		
		//for transcode jobs we have to download (unless we already have the file)
		 
	}
}