The waikiki CGI program can be built standalone on Mac OS X with the Unix
makefile:
  % make -f Makefile.unix

== WaikikiX ==

Or, in the WaikikiX directory you'll find a project for a standalone
application bundle which runs a minimal web server, calling the waikiki
CGI executable to do its dirty work.


=== Copyright and license ===

WaikikiX is Copyright 2003 by Brion Vibber and released under the GNU General
Public License.

It links with LibHTTPD, which is Copyright 2001 Hughes Technologies Pty Ltd.,
also under GNU General Public License.

=== Building ===

Build requirements:

* Apple Developers' Tools (tested with Project Builder, not yet with XCode)
* LibHTTPD: http://www.hughes.com.au/products/libhttpd/
* SQLite: http://www.sqlite.org/

Build steps:

1. Go in a terminal and install LibHTTPD and SQLite with the usual
   "./configure && make && make install" sequence.

2. Put a demo database into the waikiki (not WaikikiX) source directory
   called 'test.sqlite'. This will be copied into the application bundle
   at build time.

3. Open WaikikiX.pbproj in Project Builder.

4. Click the 'Targets' tab and select "waikiki"; this is the CGI program.
   Hit Cmd+B to build.

5. Select the 'WaikikiX' target; this is the front end. Hit Cmd+B to build.
   It will complain if you haven't built the CGI program or copied the
   database file into place.

6. You should have a pretty WaikikiX.app in the build directory. Enjoy!


=== Usage ===

1. Open the app 'WaikikiX'
2. If desired, change the options: serve to localhost only, and port number
   (default is 8080; note that ports <1024 are off-limits).
3. Hit 'Start' to start the web server.
4. Hit 'Launch browser' to bring up your default web browser with the main page.


=== Known issues ===

* This is my first Mac OS X program so it's probably in very poor style. :)

* No POST support yet, or GET for anything but the title 'A'. ;)

* The HTTP headers from the CGI end up in the output text. :P

* The wiki icon is missing... possibly case sensitivity issue.

* It doesn't handle error conditions very gracefully, and sometimes if you
  restart the server it doesn't come up right anymore.

* Sometimes the child processes aren't killed properly. If in doubt, go to
  a terminal and run 'killall waikiki; killall WaikikiX'

* The app doesn't quit if you close the main window.

* SQLite library isn't included in the bundle yet and must be installed
  as on the build machine.

* The port number doesn't always take if you just type something in and click
  the start button; if you hit 'enter' it seems to do it.

