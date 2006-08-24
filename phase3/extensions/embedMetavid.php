<?php
if(!defined('MEDIAWIKI'))
   die();
# Example WikiMedia extension
# with WikiMedia's extension mechanism it is possible to define
# new tags of the form
# <TAGNAME> some text </TAGNAME>
# the function registered by the extension gets the text between the
# tags as input and can transform it into arbitrary HTML code.
# Note: The output is not interpreted as WikiText but directly
#       included in the HTML output. So Wiki markup is not supported.
# To activate the extension, include it from your LocalSettings.php
# with: include("extensions/YourExtensionName.php");

$wgExtensionFunctions[] = "wfEmbedMetavid";

$metavid_server = 'http://metavid.ucsc.edu/bleeding_edge/';

function wfEmbedMetavid() {
    global $wgParser;
    # register the extension with the WikiText parser
    # the first parameter is the name of the new tag.
    # In this case it defines the tag <example> ... </example>
    # the second parameter is the callback function for
    # processing the text between the tags
    $wgParser->setHook( "embed_metavid", "renderMetavid" );
}

# The callback function for converting the input text to HTML output
function renderMetavid( $input, $argv ) {
	global $wgOut;
    # $argv is an array containing any arguments passed to the
    # extension like <example argument="foo" bar>..
    # Put this on the sandbox page:  (works in MediaWiki 1.5.5)
    #   <example argument="foo" argument2="bar">Testing text **example** in between the new tags</example>
	
	//treat input as wiki text
   // $output = "$input";
	//int valid args and default values: 
	$args = array('stream_name'=>'null', 'display_type'=>'video', 'start_time'=>'00:00:00', 'end_time'=>'00:01:00');
	//first take out all the wiki tag:
	//arg .. ugly: @todo cleaner reg-exp parse:
	$input_lines = explode ("\n", $input);
	$wikiBuffer='';
	foreach($input_lines as $line){
		$arg_match=false;
		//check if it matches a any arg: 
		foreach($args as $arg=>$na){
			if(substr($line, 0, strlen($arg))==$arg){
				$tmp = split("=", $line);
				$args[$arg]=str_replace('"','',$tmp[1]);
				$arg_match=true;
			}
		}		
		if($arg_match==false){
			$wikiBuffer.=$line;
		}		
	}	
	if($args['stream_name']!='null'){
		$out=generate_metavid_html($args);
	}else{
		$out.='<b>error</b> no stream provided';
	}
	//run wiki parser on wikiBuffer: 
	$out.=$wgOut->parse($wikiBuffer);
	
	return $out;	
}
function generate_metavid_html($args){
	global $metavid_server;
	//proccess args: 
	/*
  	 stream_name - the name of the stream to be included such as house_proceeding_05-25-06_01 for example
     display_type - how the embed segment is to be displayed:
          o video - displays only the video
          o full_source - displays full source meta data: the close caption text, the video and the person name 
     start_time - the start time of the stream in hh:mm:ss format.
     end_time - the end of the given stream segment in hh:mm:ss format. 		
	*/
	//buid url 
	$url = $metavid_server . 'overlay/archive_browser/service_stream_detail';
	$url.='?stream_name='.trim($args['stream_name']);
	$url.='&t='.trim($args['start_time']).'/'.trim($args['end_time']);
	$out= file_get_contents($url);
	return $out;
}
?>