#!/usr/bin/perl

# Quickie script to pull bug info from sourceforge mails and dump them to a
# log to be used for irc bot.

# Hacked up by Brion Vibber, 2004-08-02
# Hacked some more for bugzilla, 2004-08-10 and 2004-08-15

$bugzilla = 0;
$sfnet = 0;
$done = 0;

$bug = 0;
$status = "";
$summary = "";
$url = "";

while ($x = <>) {
	if ($bug == 0 && $x =~ /^Bugs item #([0-9]+), was opened at/) {
		$bug = $1;
		$sfnet = 1;
	}
	if ($bug == 0 && $x =~ /^Subject:\s+\[Bug (\d+)\]\s+(New:\s+)?(.*)$/) {
		$bug = $1;
		$status = $2;
		$status = "NEW" if($status eq "New: ");
		$summary = $3;
		$bugzilla = 1;
	}
	next if ($done);
	if ($bugzilla) {
		if ($url eq "" && $x =~ /^(http:\/\/bugzilla.wiki.edia.org\/show_bug.cgi\?id=[0-9]+)/) {
			$url = $1;
		}
		if ($status eq "" && $x =~ /^\s*Status\|(\w+)\s*\|(\w+)/) {
			# Changes status
			$status = $2;
		}
		if ($x =~ /^Bug \d+ depends on bug (\d+), which changed state./) {
			# dependency...
			$status = 'dependency change';
			$done = 1;
		}
	}
	if ($sfnet) {
		if ($status eq "" && $x =~ /^>?Status: (.*)$/) {
			$status = $1;
		}
		if ($summary eq "" && $x =~ /^>?Summary: (.*)$/) {
			$summary = $1;
		}
		if ($url eq "" && $x =~ /^(https:.*)$/) {
			$url = $1;
			# $done = 1;
		}
	}
}

if ($bug != 0) {
	# (Closed) current events link in menu is wrong - https://sourceforge.net/tracker/?func=detail&atid=411192&aid=955496&group_id=34373
	open(OUT, ">>/var/wikibugs/wikibugs.log");
#	print OUT "($status) $summary - $url\n";
	$status = "modified" if ($status eq "");
	print OUT "\00303($status)\003 $summary - \00310$url\003\n";
}
