<?php
/**
 * Internationalisation file for CheckUser extension.
 *
 * @file
 * @ingroup Extensions
 */

$messages = array();

/** English
 * @author Tim Starling
 * @author Aaron Schulz
 */
$messages['en'] = array(
	'checkuser-summary'          => 'This tool scans recent changes to retrieve the IP addresses used by a user or show the edit/user data for an IP address.
Users and edits by a client IP address can be retrieved via XFF headers by appending the IP address with "/xff". IPv4 (CIDR 16-32) and IPv6 (CIDR 96-128) are supported.
No more than 5,000 edits will be returned for performance reasons.
Use this in accordance with policy.',
	'checkuser-desc'             => 'Grants users with the appropriate permission the ability to check user\'s IP addresses and other information',
	'checkuser-logcase'          => 'The log search is case sensitive.',
	'checkuser'                  => 'Check user',
	'checkuser-contribs'         => 'check user IP addresses',
	'group-checkuser'            => 'Check users',
	'group-checkuser-member'     => 'Check user',
	'right-checkuser'            => "Check user's IP addresses and other information",
	'right-checkuser-log'        => 'View the checkuser log',
	'grouppage-checkuser'        => '{{ns:project}}:Check user',
	'checkuser-reason'           => 'Reason:',
	'checkuser-showlog'          => 'Show log',
	'checkuser-log'              => 'CheckUser log',
	'checkuser-query'            => 'Query recent changes',
	'checkuser-target'           => 'IP address or username:',
	'checkuser-users'            => 'Get users',
	'checkuser-edits'            => 'Get edits from IP address',
	'checkuser-ips'              => 'Get IP addresses',
	'checkuser-account'          => 'Get account edits',
	'checkuser-search'           => 'Search',
	'checkuser-period'           => 'Duration:',
	'checkuser-week-1'           => 'last week',
	'checkuser-week-2'           => 'last two weeks',
	'checkuser-month'            => 'last 30 days',
	'checkuser-all'              => 'all',
	'checkuser-cidr-label'       => 'Find common range and affected IP addresses for a list of IP addresses',
	'checkuser-cidr-res'         => 'Common CIDR:',
	'checkuser-empty'            => 'The log contains no items.',
	'checkuser-nomatch'          => 'No matches found.',
	'checkuser-nomatch-edits'    => 'No matches found.
Last edit was on $1 at $2.',
	'checkuser-check'            => 'Check',
	'checkuser-log-fail'         => 'Unable to add log entry',
	'checkuser-nolog'            => 'No log file found.',
	'checkuser-blocked'          => 'Blocked',
	'checkuser-gblocked'         => 'Blocked globally',
	'checkuser-locked'           => 'Locked',
	'checkuser-wasblocked'       => 'Previously blocked',
	'checkuser-localonly'        => 'Not unified',
	'checkuser-massblock'        => 'Block selected users',
	'checkuser-massblock-text'   => 'Selected accounts will be blocked indefinitely, with autoblocking enabled and account creation disabled.
	IP addresses will be blocked for 1 week for IP users only and with account creation disabled.',
	'checkuser-blocktag'         => 'Replace user pages with:',
	'checkuser-blocktag-talk'    => 'Replace talk pages with:',
	'checkuser-massblock-commit' => 'Block selected users',
	'checkuser-block-success'    => '\'\'\'The {{PLURAL:$2|user|users}} $1 {{PLURAL:$2|is|are}} now blocked.\'\'\'',
	'checkuser-block-failure'    => '\'\'\'No users blocked.\'\'\'',
	'checkuser-block-limit'      => 'Too many users selected.',
	'checkuser-block-noreason'   => 'You must give a reason for the blocks.',
	'checkuser-noreason'         => 'You must give a reason for this query.',
	'checkuser-accounts'         => '$1 new {{PLURAL:$1|account|accounts}}',
	'checkuser-too-many'         => 'Too many results (according to query estimate), please narrow down the CIDR.
Here are the IPs used (5000 max, sorted by address):',
	'checkuser-user-nonexistent' => 'The specified user does not exist.',
	'checkuser-search-form'      => 'Find log entries where the $1 is $2',
	'checkuser-search-submit'    => 'Search',
	'checkuser-search-initiator' => 'initiator',
	'checkuser-search-target'    => 'target',
	'checkuser-ipeditcount'      => '~$1 from all users',
	'checkuser-log-subpage'      => 'Log',
	'checkuser-log-return'       => 'Return to CheckUser main form',
	'checkuser-log-user2ip'      => '$1 got IP addresses for $2',
	'checkuser-log-ip2edits'      => '$1 got edits for $2',
	'checkuser-log-ip2user'      => '$1 got users for $2',
	'checkuser-log-ip2edits-xff'  => '$1 got edits for XFF $2',
	'checkuser-log-ip2user-xff'  => '$1 got users for XFF $2',
	'checkuser-log-user2edits'    => '$1 got edits for $2',

	'checkuser-autocreate-action' => 'was automatically created',
	'checkuser-email-action'     => 'sent an email to user "$1"',
	'checkuser-reset-action'     => 'reset password for user "$1"',

	'checkuser-toollinks'        => '----
	<span class="plainlinks">[http://openrbl.org/query?$1 RDNS] &bull;
[http://www.robtex.com/rbls/$1.html RBLs] &bull;
[http://www.dnsstuff.com/tools/tracert.ch?ip=$1 Traceroute] <br />
[http://www.ip2location.com/$1 Geolocate] &bull;
[http://toolserver.org/~overlordq/scripts/checktor.fcgi?ip=$1 Tor] &bull;
[http://ws.arin.net/whois/?queryinput=$1 WHOIS]</span>', # do not translate or duplicate this message to other languages
	'checkuser-xff'              => 'XFF',
	'checkuser-blockedby'        => 'Blocked by [[User:$1|$1]] on $3 with reason: "<nowiki>$2</nowiki>". $4',
	'checkuser-type'             => 'Type of check:',
	
	'checkuser-cuc_ip'           => 'IP',
	'checkuser-allusers'         => 'All edits',
	'checkuser-count'            => 'Edits',
	'checkuser-first'            => 'First use',
	'checkuser-last'             => 'Last use',
	'checkuser-blockinfo'        => 'Block info',
	'checkuser-expires'          => 'Expires',
	'checkuser-limit'            => 'Results to show:',
);