
var flashEmbed = {    
	instanceOf:'flashEmbed',
    getEmbedHTML : function (){
    	var controls_html ='';
    	js_log('embedObj control is: '+this.controls);
		if(this.controls){
			controls_html+= this.getControlsHtml('play_head') +					
						this.getControlsHtml('play_or_pause') + 
						this.getControlsHtml('stop') +
   						this.getControlsHtml('info_span');
		}
        setTimeout('document.getElementById(\''+this.id+'\').postEmbedJS()', 150);
		return this.wrapEmebedContainer( this.getEmbedObj() )+ controls_html;
    },
    getEmbedObj:function(){
    	if(!this.duration)this.duration=30;
        return '<div id="FlowPlayerAnnotationHolder_'+this.pid+'"></div>'+"\n";
    },
    postEmbedJS : function()
    {
        var script = document.createElement("script");
        script.src = mv_embed_path + 'flashembed.js';
        script.type="text/javascript";
        document.getElementsByTagName("head")[0].appendChild(script);
        setTimeout('document.getElementById(\''+this.id+'\').doFlashEmbed()', 150);
    },
    doFlashEmbed : function()
    {
        var clip = flashembed('FlowPlayerAnnotationHolder_'+this.pid,
        { src: mv_embed_path + 'FlowPlayerDark.swf', width: this.width, height: this.height, id: this.pid},
        { config: { autoPlay: true, showStopButton: false, showPlayButton: false,
           videoFile: this.media_element.selected_source.uri } });
    },
    /* js hooks/controls */
    play : function(){
    	if(this.thumbnail_disp)
        {
	    	//call the parent
    		this.parent_play();
    	}else{
            this.getPluginEmbed().DoPlay();
			this.paused=false;
    	}
    },
    pause : function(){
		this.getPluginEmbed().Pause();
    }
}

function locateFlashEmbed(clip)
{
    for(var i in global_ogg_list)
    {
        var embed = document.getElementById(global_ogg_list[i]);
        if(embed.media_element.selected_source.uri.match(clip.fileName))
        {
            js_log('found flash embed');
            return embed;
        }
    }
}

/* flowplayer callbacks */
function onFlowPlayerReady()
{
    js_log('onFlowPlayerReady');
}

function onClipDone(clip)
{
    var embed = locateFlashEmbed(clip);
    embed.setStatus("Clip Done...");
}

function onLoadBegin(clip)
{
    var embed = locateFlashEmbed(clip);
    embed.setStatus("Loading Begun...");
}

function onPlay(clip)
{
    var embed = locateFlashEmbed(clip);
    embed.setStatus("Playing...");
}

function onStop(clip)
{
    var embed = locateFlashEmbed(clip);
    embed.setStatus("Stopped...");
}

function onPause(clip)
{
    var embed = locateFlashEmbed(clip);
    embed.setStatus("Paused...");
}

function onResume(clip)
{
    var embed = locateFlashEmbed(clip);
    embed.setStatus("Resumed...");
}

function onStartBuffering(clip)
{
    var embed = locateFlashEmbed(clip);
    embed.setStatus("Buffering Started...");
}
