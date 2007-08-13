#!/bin/bash
export myfile=$1; 

small/ra3.pl $myfile > $myfile.new && 
	mv $myfile.new $myfile
