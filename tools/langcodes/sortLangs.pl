#!/usr/bin/perl -w

#
# invoke this script on the included 'langs' file
# e.g. perl sortLangs.pl langs 
# to sort the entries by name, not by language code
# 

use strict;

my @lines = <>;
my %memo;


my @sorted = sort {		
	normalize($a) cmp normalize($b)
} @lines;

foreach my $line (@sorted) {
	chomp($line);
	my ($text, $code) = split(/\t/, $line, 2);
	print qq[\t\t{ code: "$code", ] . (" " x (12-length($code))) . qq[ text: "$text" },\n];
}

sub normalize {
	my ($s) = @_;
	if (defined $memo{$s}) {
		return $memo{$s};
	}
	my $norm = $s;
	$norm =~ s/\\u([\da-f]+)/"\"\\x{" . $1 . "}\""/gee;
	$memo{$s} = $norm;
	return $norm;
}

