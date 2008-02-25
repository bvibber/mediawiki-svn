/* semantic wiki extensions */
//force load jquery: 
mv_addLoadEvent(mv_pre_setup_smw_ext); 	

function mv_pre_setup_smw_ext(){
	//make sure we have jQuery 
	mvJsLoader.doLoad(mvEmbed.lib_jquery, function(){
 		_global['$j'] = jQuery.noConflict();
		mv_swm_rewrite();
	});
}

function mv_swm_rewrite(){
	//check each link for mvd, stream, or (eventually) sequence namespace 
	$j('a').each(function(){
		js_log( this.href);
	});
}