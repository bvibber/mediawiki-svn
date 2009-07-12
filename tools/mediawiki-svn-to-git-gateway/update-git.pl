#!/usr/bin/env perl
use feature ':5.10';
use strict;
use warnings;

my $repository = '/home/avar/src/mediawiki';

chdir $repository or die $!;

my @last_irc_revs = last_irc_revs();
my $last_git_rev = last_git_rev();

if ($last_irc_revs[-1] != $last_git_rev) {
    say "Need to update from $last_irc_revs[-1] to $last_git_rev (" . ($last_irc_revs[-1] - $last_git_rev) . " revisions)";
    system 'git svn rebase';
}

sub last_irc_revs
{
    my @lines = `tail -n1000 /home/avar/.irssi/logs/freenode/#mediawiki.log*`;
    my @rev = map { /r(\d+)/; $1 } grep { /^\d+:\d+ < CIA-\d+> \S+ \* r(\d+).*/ } @lines;
    return @rev;
}

sub last_git_rev
{
    my ($ver) = `git svn log --oneline --limit 1` =~ /r(\d+)/;
    return $ver;
}
