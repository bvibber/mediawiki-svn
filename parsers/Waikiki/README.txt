INSTALLATION (WINDOWS)
You'll need a working web server (e.g., apache).

Copy everything from the zip file into a directory, preferrably a subdirectory of the apache httpd (or something) directory.

Copy an unzipped mysql dump of the cur table from wikipedia into that directory. Its file name should end on ".sql", e.g., "cur.sql".

On a command line, change into that directory. Type
	waikiki -mysql2sqlite="cur.sql"
which generates a "cur.sqlite" file (takes quite a while!).
Set the resulting sqlite database in "waikiki.ini".

Enter the following lines into your httpd.conf:
   AddHandler cgi-script cgi exe
   <Directory "SOMEDIR">
       Options +ExecCGI
   </Directory>
where SOMEDIR is the directory where you put waikiki.exe. Use "/" instead of "\"!

Type in "SOMEDIR/waikiki.exe?title=A" to get to the article about the letter "A".


-------

COMPILE IT YOURSELF!
Source available at sourceforge.net

This has been tested under Windows only. For a Linux build, you'll have to compile sqlite and make the appropriate change in TDatabase.h

You can use wiki.php as a front-end of waikiki. For proper display, put the wikipedia logo and the stylesheets into a "wiki" subdirectory in the same dir as wiki.php

