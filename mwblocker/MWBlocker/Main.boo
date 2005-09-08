# created on 9/4/2005 at 7:46 PM

namespace MediaWiki.Blocker

import System
import System.Net

if argv.Length > 0:
	for ip as string in argv:
		Checker(Suspect(ip, "Command line")).Check()
else:
	print "No IPs specified; running daemon"
	BlockDaemon.Run()
