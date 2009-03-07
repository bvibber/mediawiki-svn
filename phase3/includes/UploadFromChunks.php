<?php
/*
* first destination checks are made (if ignorewarnings is not checked) errors / warning is returned. 
* 
* we return the uploadUrl
* we then accept chunk uploads from the client.
* return chunk id on each POSTED chunk
* once the client posts done=1 concatenated the files together.
* more info at: http://firefogg.org/dev/chunk_post.html
*/
class UploadFromChunks extends UploadBase {
	var $chunk_state; //init, chunk, done 
	function initializeFromParams( $param ) {
		//start of a chunk request init the upload check destination file name
		//setup chunk folder  
		if( !$param['sessionkey'] && !$param['chunk_inx'] && !$parm['done'] ){
			
		}
		
		//we are receiving a chunk process as an upload and stash it the folder with its index number.		
		if( $param['sessionkey'] && $param['chunk_inx'] && !$parm['done']){

		}
		
		//this is the last chunk
		if( $param['sessionkey'] && $param['chunk_inx'] && $parm['done']){

		}
			
	}
}
