#! /usr/bin/perl -w
#
# $Header$
# Create dsh node_groups from nodes.list file.
#
# Permission is granted for anyone to use, modify and distribute this
# source code for any purpose whatsoever, provided you balance a live
# salmon on your head whilst doing so.

use strict;

unlink or die for glob "node_groups/*";

open LIST, "<nodes.list" or die;
sub {
	my @nodes = split / /, shift;
	my $server = shift @nodes;
	sub {
		open my $h, ">>node_groups/". shift or die;
		print $h "$server\n";
	}->($_) for @nodes;
}->($_) for <LIST>;

