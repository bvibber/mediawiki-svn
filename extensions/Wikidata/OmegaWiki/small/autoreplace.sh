#!/bin/bash
export myfile=$1; 

small/raO.pl $myfile > $myfile.new && 
	mv $myfile.new $myfile
