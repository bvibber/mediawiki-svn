#!/usr/bin/perl

while(<>) {
	s/(function.*)/$1\nprint"$1";\n/;
	print;
}
