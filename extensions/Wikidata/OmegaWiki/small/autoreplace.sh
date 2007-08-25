#!/bin/bash
export myfile=$1; 

small/wfMsg.pl $myfile > $myfile.new && 
	mv $myfile.new $myfile
