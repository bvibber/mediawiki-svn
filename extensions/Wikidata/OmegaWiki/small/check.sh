#!/bin/bash
export myfile=$1; 

small/ra3.pl $myfile 		|
	diff -dy $myfile - 	| 
	less
