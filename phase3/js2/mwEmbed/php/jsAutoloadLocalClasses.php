<?php 
if ( !defined( 'MEDIAWIKI' ) ) die(1);

global $wgJSAutoloadLocalClasses, $mwEmbedDirectory;			
	
	//the basis of the loader calls:
	$wgJSAutoloadLocalClasses['mv_embed']			= $mwEmbedDirectory . 'mv_embed.js';	
		
	//core: 	
	$wgJSAutoloadLocalClasses['window.jQuery']		= $mwEmbedDirectory . 'jquery/jquery-1.3.2.js';
	
	$wgJSAutoloadLocalClasses['j.secureEvalJSON']	= $mwEmbedDirectory . 'jquery/plugins/jquery.json-1.3.js';
	
	$wgJSAutoloadLocalClasses['j.cookie']			= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/external/jquery.cookie.js';
	
	$wgJSAutoloadLocalClasses['j.contextMenu']		= $mwEmbedDirectory . 'jquery/plugins/jquery.contextMenu.js';
	$wgJSAutoloadLocalClasses['j.fn.autocomplete']	= $mwEmbedDirectory . 'jquery/plugins/jquery.autocomplete.js';
	$wgJSAutoloadLocalClasses['j.fn.hoverIntent']	= $mwEmbedDirectory . 'jquery/plugins/jquery.hoverIntent.js';
	$wgJSAutoloadLocalClasses['j.Jcrop'] 			= $mwEmbedDirectory . 'jquery/plugins/Jcrop/js/jquery.Jcrop.js';	
	$wgJSAutoloadLocalClasses['Date.fromString']  	= $mwEmbedDirectory . 'jquery/plugins/date.js';
	$wgJSAutoloadLocalClasses['j.fn.datePicker']	= $mwEmbedDirectory . 'jquery/plugins/jquery.datePicker.js';
	
	//jquery.ui
	$wgJSAutoloadLocalClasses['j.ui']				= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.core.js';
	
	$wgJSAutoloadLocalClasses['j.effects.blind']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.blind.js';
	$wgJSAutoloadLocalClasses['j.effects.drop']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.drop.js';
	$wgJSAutoloadLocalClasses['j.effects.pulsate']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.pulsate.js';
	$wgJSAutoloadLocalClasses['j.effects.transfer']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.transfer.js';	
	$wgJSAutoloadLocalClasses['j.ui.droppable']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.droppable.js';
	$wgJSAutoloadLocalClasses['j.ui.slider']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.slider.js';
	$wgJSAutoloadLocalClasses['j.effects.bounce']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.bounce.js';
	$wgJSAutoloadLocalClasses['j.effects.explode']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.explode.js';
	$wgJSAutoloadLocalClasses['j.effects.scale']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.scale.js';
	$wgJSAutoloadLocalClasses['j.ui.datepicker']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.datepicker.js';
	$wgJSAutoloadLocalClasses['j.ui.progressbar']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.progressbar.js';
	$wgJSAutoloadLocalClasses['j.ui.sortable']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.sortable.js';
	$wgJSAutoloadLocalClasses['j.effects.clip']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.clip.js';
	$wgJSAutoloadLocalClasses['j.effects.fold']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.fold.js';
	$wgJSAutoloadLocalClasses['j.effects.shake']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.shake.js';
	$wgJSAutoloadLocalClasses['j.ui.dialog']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.dialog.js';
	$wgJSAutoloadLocalClasses['j.ui.resizable']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.resizable.js';
	$wgJSAutoloadLocalClasses['j.ui.tabs']			= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.tabs.js';
	$wgJSAutoloadLocalClasses['j.effects.core']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.core.js';
	$wgJSAutoloadLocalClasses['j.effects.highlight']= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.highlight.js';
	$wgJSAutoloadLocalClasses['j.effects.slide']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/effects.slide.js';
	$wgJSAutoloadLocalClasses['j.ui.accordion']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.accordion.js';
	$wgJSAutoloadLocalClasses['j.ui.draggable']		= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.draggable.js';
	$wgJSAutoloadLocalClasses['j.ui.selectable']	= $mwEmbedDirectory . 'jquery/jquery.ui-1.7.1/ui/ui.selectable.js';
	
	
	//libAddMedia:
	$wgJSAutoloadLocalClasses['mvFirefogg'] 		= $mwEmbedDirectory . 'libAddMedia/mvFirefogg.js';
	$wgJSAutoloadLocalClasses['mvAdvFirefogg'] 		= $mwEmbedDirectory . 'libAddMedia/advFirefogg.js';
	$wgJSAutoloadLocalClasses['mvUploader'] 		= $mwEmbedDirectory . 'libAddMedia/mvUploader.js';
	$wgJSAutoloadLocalClasses['remoteSearchDriver'] = $mwEmbedDirectory . 'libAddMedia/remoteSearchDriver.js';
	$wgJSAutoloadLocalClasses['seqRemoteSearchDriver'] = $mwEmbedDirectory . 'libAddMedia/seqRemoteSearchDriver.js';
	$wgJSAutoloadLocalClasses['baseRemoteSearch'] 	= $mwEmbedDirectory . 'libAddMedia/searchLibs/baseRemoteSearch.js';
	$wgJSAutoloadLocalClasses['mediaWikiSearch'] 	= $mwEmbedDirectory . 'libAddMedia/searchLibs/mediaWikiSearch.js';
	$wgJSAutoloadLocalClasses['metavidSearch'] 		= $mwEmbedDirectory . 'libAddMedia/searchLibs/metavidSearch.js';
	$wgJSAutoloadLocalClasses['archiveOrgSearch'] 	= $mwEmbedDirectory . 'libAddMedia/searchLibs/archiveOrgSearch.js';	
	$wgJSAutoloadLocalClasses['baseRemoteSearch']	= $mwEmbedDirectory . 'libAddMedia/searchLibs/baseRemoteSearch.js';	
	
	//libClipEdit:
	$wgJSAutoloadLocalClasses['mvClipEdit'] 		= $mwEmbedDirectory . 'libClipEdit/mvClipEdit.js';
	
	//libEmbedObj:
	$wgJSAutoloadLocalClasses['embedVideo'] 		= $mwEmbedDirectory . 'libEmbedObj/mv_baseEmbed.js';
	$wgJSAutoloadLocalClasses['flashEmbed'] 		= $mwEmbedDirectory . 'libEmbedObj/mv_flashEmbed.js';
	$wgJSAutoloadLocalClasses['genericEmbed'] 		= $mwEmbedDirectory . 'libEmbedObj/mv_genericEmbed.js';
	$wgJSAutoloadLocalClasses['htmlEmbed'] 			= $mwEmbedDirectory . 'libEmbedObj/mv_htmlEmbed.js';
	$wgJSAutoloadLocalClasses['javaEmbed'] 			= $mwEmbedDirectory . 'libEmbedObj/mv_javaEmbed.js';
	$wgJSAutoloadLocalClasses['nativeEmbed'] 		= $mwEmbedDirectory . 'libEmbedObj/mv_nativeEmbed.js';
	$wgJSAutoloadLocalClasses['quicktimeEmbed'] 	= $mwEmbedDirectory . 'libEmbedObj/mv_quicktimeEmbed.js';	
	$wgJSAutoloadLocalClasses['vlcEmbed'] 			= $mwEmbedDirectory . 'libEmbedObj/mv_vlcEmbed.js';	

	//libSequencer:
	$wgJSAutoloadLocalClasses['mvPlayList'] 		= $mwEmbedDirectory . 'libSequencer/mvPlayList.js';	
	$wgJSAutoloadLocalClasses['mvSequencer']		= $mwEmbedDirectory . 'libSequencer/mvSequencer.js';	
	
	//libTimedText:
	$wgJSAutoloadLocalClasses['mvTextInterface']	= $mwEmbedDirectory . 'libTimedText/mvTextInterface.js';
?>