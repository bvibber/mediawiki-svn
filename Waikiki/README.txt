INSTALLATION (WINDOWS)
You'll need a working web server (e.g., apache) and PHP for this.

Copy everything from the zip file into a directory, preferrably one accessible by apache. Then set that directory name in "wiki.php" as "$wd".

Copy a mysql dump of the cur table from wikipedia into that directory. Its file name should end on ".sql", e.g., "cur.sql".

On a command line, change into that directory. Type
	waikiki -mysql2sqlite="cur.sql"
which generates a "cur.sqlite" file (takes quite a while!).
Set this file name as "$db" in "wiki.php".

Type the URL of "wiki.php" into your browser. It will start with the article on the letter "B"...


-------

COMPILE IT YOURSELF!
Source available at sourceforge.net

This has been tested under Windows only. For a Linux build, you'll have to compile sqlite and make the appropriate change in TDatabase.h

You can use wiki.php as a front-end of waikiki. For proper display, put the wikipedia logo and the stylesheets into a "wiki" subdirectory in the same dir as wiki.php

