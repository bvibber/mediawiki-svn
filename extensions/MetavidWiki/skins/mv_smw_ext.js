/* semantic wiki extensions */
//force load jquery: 
mv_addLoadEvent(mv_pre_setup_smw_ext); 	

roe_url =null;
function mv_pre_setup_smw_ext(){
	//assin roe url: 
	roe_url = wgScript + ''
	//make sure we have jQuery 
	mvJsLoader.doLoad(mvEmbed.lib_jquery, function(){
 		_global['$j'] = jQuery.noConflict();
		mv_swm_rewrite();		
	});
}

function mv_swm_rewrite(){
	//check each link for mvd, stream, or (eventually) sequence namespace 
	//([^:]*)\/([0-9]+:[0-9]+:[^\/]+\/[0-9]+:[0-9]+:[^\/]+)
	var patt_mvd = new RegExp("MVD:([^:]*):([^\/]*)\/([0-9]+:[0-9]+:[^\/]+)\/?([0-9]+:[0-9]+:[^\/]+)?");
	$j('a').each(function(){
		res = this.href.match(patt_mvd);
		if(res){			
			js_log(this.href);
			js_log(res);
		}
	});
}