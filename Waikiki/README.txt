INSTALLATION (WINDOWS)
You'll need a working web server (e.g., apache) and PHP for this.
copy everything from the zip file into a directory, preferrably one accessible by apache. Then set that directory name in "wiki.php" as "$wd".


COMPILE IT YOURSELF!
This has been tested under Windows only. For a Linux build, you'll have to compile sqlite and make the appropriate change in TDatabase.h

To convert a MySQL dump of the cur table into a sqlite file, use
  waikiki -mysql2sqlite="sql_dump_file.sql"
which generates a "sql_dump_file.sqlite" file (takes a while!).

You can use wiki.php as a front-end of waikiki. For proper display, put the wikipedia logo and the stylesheets into a "wiki" subdirectory in the same dir as wiki.php

