<?php 
if ( !defined( 'MEDIAWIKI' ) ) die(1);

global $wgJSAutoloadLocalClasses, $wgScriptPath;	
		
	$mvjsp = 'js2/mwEmbed/';
	
	//the basis of the loader calls:
	$wgJSAutoloadLocalClasses['mv_embed']			= $mvjsp . 'mv_embed.js';	
		
	//core: 	
	$wgJSAutoloadLocalClasses['window.jQuery']		= $mvjsp . 'jquery/jquery-1.3.2.js';
	
	$wgJSAutoloadLocalClasses['mv_allpages']		= $mv_jspath . 'mv_allpages.js';
	$wgJSAutoloadLocalClasses['mv_search']			= $mv_jspath . 'mv_search.js';
	$wgJSAutoloadLocalClasses['mv_stream']			= $mv_jspath . 'mv_stream.js';
	
	$wgJSAutoloadLocalClasses['j.secureEvalJSON']	= $mvjsp . 'jquery/plugins/jquery.json-1.3.js';
	$wgJSAutoloadLocalClasses['j.ui']				= $mvjsp . 'jquery/jquery.ui-1.7.1/ui/ui.core.js';
	$wgJSAutoloadLocalClasses['j.ui.droppable']		= $mvjsp . 'jquery/jquery.ui-1.7.1/ui/ui.droppable.js';	
	$wgJSAutoloadLocalClasses['j.ui.draggable']		= $mvjsp . 'jquery/jquery.ui-1.7.1/ui/ui.droppable.js';	
	$wgJSAutoloadLocalClasses['j.ui.sortable']		= $mvjsp . 'jquery/jquery.ui-1.7.1/ui/ui.sortable.js';
	$wgJSAutoloadLocalClasses['j.ui.resizable']		= $mvjsp . 'jquery/jquery.ui-1.7.1/ui/ui.resizable.js';
	$wgJSAutoloadLocalClasses['j.cookie']			= $mvjsp . 'jquery/jquery.ui-1.7.1/external/jquery.cookie.js';
	
	$wgJSAutoloadLocalClasses['j.contextMenu']		= $mvjsp . 'jquery/plugins/jquery.contextMenu.js';
	$wgJSAutoloadLocalClasses['j.fn.autocomplete']	= $mvjsp . 'jquery/plugins/jquery.autocomplete.js';
	$wgJSAutoloadLocalClasses['j.fn.hoverIntent']	= $mvjsp . 'jquery/plugins/jquery.hoverIntent.js';
	$wgJSAutoloadLocalClasses['j.Jcrop'] 			= $mvjsp . 'jquery/plugins/Jcrop/js/jquery.Jcrop.js';	
	$wgJSAutoloadLocalClasses['Date.fromString']  	= $mvjsp . 'jquery/plugins/date.js';
	$wgJSAutoloadLocalClasses['j.fn.datePicker']	= $mvjsp . 'jquery/plugins/jquery.datePicker.js';
	
	//libAddMedia:
	$wgJSAutoloadLocalClasses['mvFirefogg'] 		= $mvjsp . 'libAddMedia/mvFirefogg.js';
	$wgJSAutoloadLocalClasses['mvUploader'] 		= $mvjsp . 'libAddMedia/mvUploader.js';
	$wgJSAutoloadLocalClasses['remoteSearchDriver'] = $mvjsp . 'libAddMedia/remoteSearchDriver.js';
	$wgJSAutoloadLocalClasses['seqRemoteSearchDriver'] = $mvjsp . 'libAddMedia/seqRemoteSearchDriver.js';
	$wgJSAutoloadLocalClasses['baseRemoteSearch'] 	= $mvjsp . 'libAddMedia/searchLibs/baseRemoteSearch.js';
	$wgJSAutoloadLocalClasses['mediaWikiSearch'] 	= $mvjsp . 'libAddMedia/searchLibs/mediaWikiSearch.js';
	$wgJSAutoloadLocalClasses['metavidSearch'] 		= $mvjsp . 'libAddMedia/searchLibs/metavidSearch.js';
	$wgJSAutoloadLocalClasses['archiveOrgSearch'] 	= $mvjsp . 'libAddMedia/searchLibs/archiveOrgSearch.js';	
	$wgJSAutoloadLocalClasses['baseRemoteSearch']	= $mvjsp . 'libAddMedia/searchLibs/baseRemoteSearch.js';	
	
	//libClipEdit:
	$wgJSAutoloadLocalClasses['mvClipEdit'] 		= $mvjsp . 'libClipEdit/mvClipEdit.js';
	
	//libEmbedObj:
	$wgJSAutoloadLocalClasses['embedVideo'] 		= $mvjsp . 'libEmbedObj/mv_baseEmbed.js';
	$wgJSAutoloadLocalClasses['flashEmbed'] 		= $mvjsp . 'libEmbedObj/mv_flashEmbed.js';
	$wgJSAutoloadLocalClasses['genericEmbed'] 		= $mvjsp . 'libEmbedObj/mv_genericEmbed.js';
	$wgJSAutoloadLocalClasses['htmlEmbed'] 			= $mvjsp . 'libEmbedObj/mv_htmlEmbed.js';
	$wgJSAutoloadLocalClasses['javaEmbed'] 			= $mvjsp . 'libEmbedObj/mv_javaEmbed.js';
	$wgJSAutoloadLocalClasses['nativeEmbed'] 		= $mvjsp . 'libEmbedObj/mv_nativeEmbed.js';
	$wgJSAutoloadLocalClasses['quicktimeEmbed'] 	= $mvjsp . 'libEmbedObj/mv_quicktimeEmbed.js';	
	$wgJSAutoloadLocalClasses['vlcEmbed'] 			= $mvjsp . 'libEmbedObj/mv_vlcEmbed.js';	

	//libSequencer:
	$wgJSAutoloadLocalClasses['mvPlayList'] 		= $mvjsp . 'libSequencer/mvPlayList.js';	
	$wgJSAutoloadLocalClasses['mvSequencer']		= $mvjsp . 'libSequencer/mvSequencer.js';	
	
	//libTimedText:
	$wgJSAutoloadLocalClasses['mvTextInterface']	= $mvjsp . 'libTimedText/mvTextInterface.js';
?>