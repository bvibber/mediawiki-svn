C# port of Kate Turner's Lucene-based search daemon for MediaWiki.
Ported and further developed by Brion Vibber
spring 2005

More documentation to come..........


This is being developed and tested with Mono 1.1.6 and MonoDevelop 0.6.

The main MonoDevelop solution file is Search.mds.
A Makefile builder should also be provided at some point, perhaps.


At the moment, the daemon is out of sync with the Java version and is
not too spiffy. I'm working mainly on the updater at this time, as the
Java updater runs horribly slow under GCJ, but it might be worth topping
up the daemon as well to avoid maintaining two copies.

