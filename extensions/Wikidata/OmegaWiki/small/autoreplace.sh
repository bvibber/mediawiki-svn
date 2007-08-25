#!/bin/bash
export myfile=$1; 

perl -pe "s/wfMsg_sc/wfMsgSc/g"  $myfile > $myfile.new && 
	mv $myfile.new $myfile
