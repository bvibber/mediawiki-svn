.ig
WikiTeX: expansible LaTeX module for MediaWiki
Copyright (C) 2004-7 Peter Danenberg
See doc/COPYING for details.
..
.so header.ms
.TI TODO "Things to do (exigenda) and things done (exacta) are recorded \
here."
.XH 1 Exigenda
.
.XH 2 Generic
.
.XH 2 Server
.IP Crontab
Crontab to delete daily cruft (left over from over-ambitious users who
outstrip resources, causing files.
.IP Actions
Plenary Classes
.IP Install
Replace ad-hoc installations by copytree; fail on directory existence.
.
.XH 2 Client
.
.XH 2 Documentation
.IP Territory
Copyright notices, etc.
.IP Permissions
Work with MediaWiki to restrict usage (optionally) to certain classes of
users.
.IP XML-RPC-lib
Add fallback-support for phpxmlrpc or Incutio for those with little
control over their installations, willing to take a performance hit.
.
.XH 2 Handler
.IP Database
Swap out home-brew lock system for some heavy-duty piece-of-shit (mysql,
say; postgre; or even sqlite3).
.
.XH 1 Exacta
.IP Packaging
Combine both server and client into a common package, or utterly segregated
(thus possibly not confusing the client-users)?
.IP
Latter: combined source; `src' directory: contains: `client', `server'.
.IP PHP-client
Basic request working; now to do something useful with it.
.IP Temp-dir
Does cleanup happen after exceptions? (The object is deleted, after all.)
.IP
Yes.
.IP "Python Apache-handler"
Trader Joe acts as a middle-man between locally running, unprivileged,
chrooted servers and the requesting public.
.IP
More efficient? Possibly; will rebuke the premature optimization for now.
.IP THANKS
Get Erik in there. Hephaistos, Hermes Charmophron, Apollon, Hestia, Hera,
Athena, dodeka Theoi.
.IP Footer/Header
WikiTeX <VERSION> in footers or headers, across from date
.IP "Honest work in subserver"
Invocation of latex, dvipng
.IP "Web install"
Installs into web-root and changes ownership.
.IP "Title page"
ms-macro to institute title page (include with .so)
.IP License
Includable ms-file for license (.so)
.
.XH 2 Generic
.
.XH 3 Cache
.IP Purview
Client-, server-side or both?
.IP Failure
Problem: in 1.0+, failed rendering don't get cached and are rerendered
every day.
.IP
Distinguish, therefore, between server- and syntax-failures; and cache
accordingly.
.IP
No: a simple flag PHP side will turn on/off error caching; functioning
systems can turn it off for performance, and for debugging purposes:
back on.
.IP
It may be useful to distinguish between temporary and permanent errors,
now that we have ad-hoc classes.
.
.XH 2 Server
.IP Debug
build_scripts command which takes --debug and disables optimization in
wikitex-main.py
.IP
No: rather a wikitex/main.py, and two trivial wrappers: wikitex and wikitex-
debug, which are invoked with `python -O' and `python' respectively.
.IP IP-throttle
Database of author vs. time
.IP setrlimit
Per-request (post droppriv)
.IP MIME-types
Currently a non-descript list in Constants; instantiate MIME-wrappers
with well-defined instance variables.
.IP Delivery
Client-server contract for the delivery of content (keys for XMLRPC
response); encode in a format parseable by PHP: XML, say?
.IP
Right now: using an implicit contract (image: content).  A low-tech
published API may suffice.
.
.XH 2 Client
.IP Source
`errors' directory to house the manifold.
.IP Authentication
CURL-lib on PHP can handle basic and key-based.
.IP Errors
Unified error presentation.
.IP Documentation
Own PHP-client documentation, or combined with server's (current).
.
.XH 3 Cache
.IP Purview
Use MediaWiki mechanism with its overhead, or some ad-hoc system?
.IP MediaWiki
MediaWiki mechanism: must be tweaked to forbid arbitrary upload; but can
detect orphaned files: Special:Unusedimages.
.IP
Must forge, apparently, a subclass of WebRequest, filling in artificial
values.
.IP Home-brew
Own mechanism: lightweight; but responsibility (cruft).
.
.XH 2 Documentation
.IP "README Mon Jan 22 08:36:11 PST 2007"
Installation
.IP "Build Sun Jan 21 01:46:55 PST 2007"
Build target in Setup.py which invokes the Makefile in doc.
.so footer.ms
