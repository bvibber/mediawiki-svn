#!/bin/bash
export myfile=$1; 

small/raO.pl $myfile 		|
	diff -d $myfile - 	| 
	less
