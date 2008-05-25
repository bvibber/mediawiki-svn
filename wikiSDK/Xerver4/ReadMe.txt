


	######################################################
	## IMPORTANT:                                       ##
	## There is a help folder in the Xerver directory.  ##
	## Start index.html in the help directory for help. ##
	######################################################



#######################################################################
########################## ABOUT THIS README  #########################
#######################################################################


  THIS DOCUMENT:
* How to start Xerver
* Xerver features
* Xerver version history


#######################################################################
######################### How to start Xerver #########################
#######################################################################

  WINDOWS USERS:
  Start Xerver with "StartXerver.exe"
  and run the setup with "Setup.exe".
  
  For advanced users: The the folder "otherStartupFiles" for
  alternative startup files (to run Xerver as a background process).


  NON WINDOWS USERS:
  Enter one of the following lines in your prompt:
  * java -jar xerver.jar		Start Xerver
  * java -jar xerver.jar Server		Start Xerver
  * java -jar xerver.jar Setup		Start Xerver Setup
  * java -jar xerver.jar FTPServer	Start Xerver FTP
  * java -jar xerver.jar FTPSetup	Start Xerver FTP Setup


  Actually, you also have these "subparameters" you can use as well
  (you can combine them as well):
  * java -jar xerver.jar FTPServer -pXX		Start server on port XX

  * java -jar xerver.jar FTPSetup -pXX		Start FTP setup on port XX
  * java -jar xerver.jar FTPSetup -r		Start FTP remote setup

  * java -jar xerver.jar Setup -pXX		Start setup on port XX
  * java -jar xerver.jar Setup -r		Start remote setup
  * java -jar xerver.jar Setup -nw		Start setup without a GUI

  * java -jar xerver.jar Server -nw		Is equivalent to -s0
  * java -jar xerver.jar Server -sX		Start server in mode X where X is an integer:
						0 = start with no GUI 
						1 = start with a basic AWT-interface
						2 = start with an advanced Swing-interface
                                                    (not minimized at startup)
						3 = start with an advanced Swing-interface
                                                    (minimized at startup)


  EXAMPLE:
  * java -jar xerver.jar Setup -nw -r -p1234	Start setup on port 1234 without a GUI
						and allow remote IPs to connect to setup.

#######################################################################
########################### Xerver features ###########################
#######################################################################


	USER FRIENDLY
* A very-easy-to-use setup web interface, including a Setup Wizard.
* "Check for updates" feature is included.


	FEATURES
* A complete FTP server is included.
* HTTP/0.9, HTTP/1.0 and HTTP/1.1 supported.
* All HTTP methods supported (GET, POST and HEAD).
* Advanced logging.
* Create aliases (virtual directories).
* Create password protected folders. (Create users and choose which folder(s) each user shall have access to).
* Run CGI-scripts
  (run your own scripts on your hard drive).
  Among other script types, Perl and PHP scripts can be run.
* Allow/deny directory listing.
* Choose which port Xerver shall use.
* Use your own error pages (for example "404 Not Found").
* Choose which files shall be considered index-files.
* Run Xerver as a background process.
* Remote administration available.


	SECURITY
* Allow users to download only files with
  certain file extensions.
* Choose which folder(s) you want to share.
* Choose if you want to share hidden files.
* If you create password protected folders,
  the crypto that is used by Xerver is a
  safe one-way crypto which by today has no
  known decrypting algorithms.


	ABOUT XERVER
* Xerver is freeware.
* Xerver is open source, released under the GNU General Public License (GPL). 
* Any computer/system (such as Windows, Linux, UNIX, Mac etc.)
  with a Java interpreter installed can run Xerver.
* Requires less than 1 Mb hard drive space.
* Requires very low system resources.
* Xerver is fast, free and reliable. Why use something else?



#######################################################################
######################## Xerver version history #######################
#######################################################################

Below you can see when different versions of Xerver have been released.
You can also see which components of Xerver that have been
added/removed/updated with each new release.


* 2002-01-14
  Xerver 1.00 released for BETA testing.

* 2002-01-15
  Xerver 1.01 released for BETA testing.
  * Setup page improved

* 2002-01-16
  Xerver 1.02 released for BETA testing.
  * Xerver Update Service added
  * Setup page improved
  * Xerver Kernel improved

* 2002-01-17
  Xerver 1.10 released for BETA testing.
  * Setup page improved
  * Xerver Kernel improved

* 2002-01-18
  Xerver 1.11 released for BETA testing.
  * Xerver Kernel improved

* 2002-01-18
  Xerver 1.12 released for BETA testing.
  * Xerver Update Service is now a part of the setup page
  * Xerver Kernel improved

* 2002-01-22
  Xerver 1.13 released for BETA testing.
  * Setup page improved

* 2002-01-26
  Xerver 1.14 released for BETA testing.
  * Setup page improved
  * Xerver Kernel improved
  * Some files were renamed

* 2002-01-27
  Xerver 1.20 released for BETA testing.
  * Setup page improved
  * Xerver Kernel improved
  * Install file added

* 2002-01-28
  Xerver 1.30 released for BETA testing.
  * A new interface when running Xerver or Xerver setup 
  * Ability to run Xerver with extremely low system resources

* 2002-01-28
  Xerver 1.31 released for BETA testing.
  * Xerver Kernel improved

* 2002-01-28
  Xerver 1.32 released for BETA testing.
  * Setup page improved
  * Xerver Kernel improved

* 2002-02-05
  Xerver 1.33 released.
  * This version is not considered BETA.
  * Setup page improved

* 2002-02-15
  Xerver 2.00 released for BETA testing.
  * Xerver now supports CGI-scripts
  * Xerver now supports POST

* 2002-02-16
  Xerver 2.01 released for BETA testing.
  * Xerver Install improved

* 2002-02-24
  Xerver 2.10 released.
  * This version is not considered BETA.
  * Xerver Kernel improved
  * Setup page improved

* 2002-03-12
  Xerver 2.20 released.
  * Xerver Kernel improved

* 2002-07-29
  Xerver 3.00 released for BETA testing.
  * Not open source yet, I'm searching for a suitable license. Be patient ;)
  * Large parts of Xerver has been rewritten from scratch.
  * Minor bugfixes.
  * Much faster(!).
  * Run any script types you want.
  * Password protect folders with username/password.
  * Create aliases.
  * A brand new Setup, including a setup wizard.

* 2002-08-16
  Xerver 3.10 released for BETA testing.
  * Xerver is released under the GNU General Public License.
  * A few words were replaced during the setup.
  * Help files updated.
  * Setup HTML-pages have been updated.
  * Several classes have been updated.
  * Several new classes have been added, including:
    ReadInputStream
    ReadInputStreamSTDIN
    ReadInputStreamSTDOUT
    ReadInputStreamSTDERR
    DataInputStreamWithReadLine

* 2002-08-17
  Xerver 3.11 released for BETA testing.
  * Source has been modified. Unnecessery comments have been removed.
  * Minor bugfixes.
  * Minor layout improvements.

* 2002-08-24
  Xerver 3.12 released.
  * This version is not considered BETA.
  * A few words were replaced during the setup.
  * Additional help files created.
  * The following classes have been updated:
    InstallXerver
    NewConnection
    XerverKernel
    ExtractZipFile
  * One new class has been added:
    ExtractZipFileInstall
  * If a new update is available, the user will be notified
    about this when Xerver starts (in Swing mode).

* 2002-08-24
  Xerver 3.13 released.
  * The following classes have been updated:
    ProgramWindow
    Start

* 2003-01-11
  Xerver 3.20 released for BETA testing.
  * Many classes rewritten and optimized for faster performance.
  * A brand new install program with a very easy to use user interface.
  * New algorithms using less CPU.
  * Two new classes were added:
     MyInteger
     ComparatorIgnoreCase
  * Xerver Remote Administration tool updated.
  * Kernel improvments with chunked data transfer encoding and
    Keep-Alive connections (for faster surfing).
  * Several changes to the web interface setup program.
  * Small changes to the help files
  * Minor bugfixes: in the HTML setup pages. Fixed!
  * Minor bugfix: The PATH-variable was not always set when running
    CGI-script. Fixed!
  * Minor bugfix: Under certain circumstances Xerver did not send
    the "302 Found" header when needed. Fixed!


* 2003-01-11
  Xerver 3.21 released for BETA testing.
  * Minor bugfix: Under certain circumstances Xerver has to be restarted after
    changes have been made using Xerver Setup. Fixed!


* 2003-01-11
  Xerver 3.22 released for BETA testing.
  * Xerver is no longer cAsE sEnSiTiVe when looking for index-files.


* 2003-01-13
  Xerver 3.23 released for BETA testing.
  * Xerver can now recognize and understand certain invalid script headers.
  * Some major performance improvements has been done.


* 2003-01-25
  Xerver 3.30 released.
  * This version is not considered BETA.
  * A brand new installer has been created.
  * HTTP Error pages updated.


* 2003-02-06
  Xerver 3.31 released.
  * Minor update to the .exe-files for Windows users.


* 2003-09-07
  Xerver 4.00 released for BETA testing.
  Xerver is bounded with Xerver Free FTP Server 1.00.
  * Xerver Free FTP Server has been written and is now bounded
    with Xerver Free Web Server.
  * Minor bugfix: Under certain circumstances Xerver locked a visited file
    during a longer time than what was necessary. Fixed!
  * Minor changes to the Xerver Setup.
  * Bugfix: Under certain circumstances php-scripts didn't
    work with some versions of PHP. Fixed!
  * For the first time Xerver comes as a jar-package.
  * At startup, Xerver shows your outer IP.
  * In statistic field, Xerver shows both IP and host
    name on machines connecting to Xerver.


* 2003-09-19
  Xerver 4.01 released for BETA testing.
  Xerver is bounded with Xerver Free FTP Server 1.01.
  * Updated ReadMe.txt-file documentation.
  * Minor improvements to FTP server.
  * Minor bugfix: Under certain circumstances some scripts
    could not run. Fixed!
  * Bugfix: Xerver FTP-server did not correctly interpret paths given from client.
  * Xerver FTP-server is now case sensitive to paths when needed.


* 2003-10-25
  Xerver 4.02 released.
  Xerver is bounded with Xerver Free FTP Server 1.01.
  * This version is not considered BETA.
  * New format for server name when sent as response.
  * More flags allowed when starting Xerver from prompt.
  * Minor bugfix: Under Solaris, the setup would under certain circumstances
    fail to work correctly. Fixed!
  * Some stability improvements since last beta version.


* 2003-10-30
  Xerver 4.03 released.
  Xerver is bounded with Xerver Free FTP Server 1.01.
  * Minor bugfix: Xerver setup could stop work
    during some setup steps. Fixed!


* 2003-11-22
  Xerver 4.04 released.
  Xerver is bounded with Xerver Free FTP Server 1.01.
  * Additional CGI environment variables are now generated
    when running CGI scripts for Windows users.
  * Xerver did not always tell its version number in responses.
    Now Xerver do.


* 2004-12-23
  Xerver 4.10 released.
  Xerver is bounded with Xerver Free FTP Server 1.02.
  * A new GUI showing both the outer and local IP.
  * StartXerver.exe updated (for Windows users).
  * Setup.exe updated (for Windows users).
  * Minor changes to the CGI environment has been made.
  * Minor changes to the responses sent by the FTP server.
  * WAP support added.
  * Max limit for how long time CGI-scripts can run.
  * Minor bugfix: Problems to read certain files with very long file names. Fixed!
  * Shortcut to Xerver has a new icon for Windows users.


* 2004-12-30
  Xerver 4.11 released.
  Xerver is bounded with Xerver Free FTP Server 1.03.
  * New exe files for Windows users. The exe files will detect if Java
    is not installed on the computer and inform the user that Java is required.
  * FTP server now automatically detects what IP to show when
    receiving a PASV command.
  * New help pages added.


* 2005-01-03
  Xerver 4.12 released.
  Xerver is bounded with Xerver Free FTP Server 1.03.
  * New exe files for Windows users.
  * Xerver install asks if user want autostart Xerver.
  * Minor changes to GUI.
  * Help pages updated.
  * Minor bugfix: In Xerver Setup: Under certain circumstances,
    some file extensions could not be added to the list. Fixed!


* 2005-01-04
  Xerver 4.13 released.
  Xerver is bounded with Xerver Free FTP Server 1.03.
  * Help pages updated.


* 2005-01-10
  Xerver 4.14 released.
  Xerver is bounded with Xerver Free FTP Server 1.03.
  * Minor changes to Xerver Setup.
  * New comments added to source.
  * Bugfix: When very large amount of data was sent using POST Xerver
    could under certain circumstances have problems reading the data. Fixed!
  * New exe files created.


* 2005-01-22
  Xerver 4.15 released.
  Xerver is bounded with Xerver Free FTP Server 1.03.
  * New Mime Types added.


* 2005-04-10
  Xerver 4.16 released.
  Xerver is bounded with Xerver Free FTP Server 1.04.
  * New Mime Types added.
  * Bugfix: During some circumstances Xerver FTP Server
    used more system resources than necessary. Fixed!


* 2005-08-10
  Xerver 4.17 released.
  Xerver is bounded with Xerver Free FTP Server 1.05.
  * FTP Server now recognizes more ways in which clients
    can ask for a directory listing.
  * Xerver now better handles scripts that are running for a very long time.
  * Bugfix: Some menu items in the FTP web setup could under
    certain circumstances stop working. Fixed!
  * BETA users of Xerver will no longer be notified of that there is
    an other version of Xerver to download.


* 2005-10-08
  Xerver 4.20 released.
  Xerver is bounded with Xerver Free FTP Server 1.05.
  * Bugfix: Bad or incorrect PATH:s are now handled better.


* 2006-12-30
  Xerver 4.30 released.
  Xerver is bounded with Xerver Free FTP Server 2.00.
  * A brand new installation for Windows users.
  * Several new settings in the Setup.
  * Xerver supports more file extensions.
  * Log file for HTTP server.
  * Log file for FTP server.
  * Local IP can be specified manually.
  * Outer IP can be specified manually.
  * User can specify port range to be used for FTP server when
    passive mode is used.
  * Changes to how Xerver runs CGI scripts.
  * Support for more commands in the FTP server.
  * Changes to the LIST response the FTP server sends.
  * The FTP server will be more robust to broken FTP clients.
  * Xerver icon on all executable files in Windows.
  * Bugfix: Certain file names couldn't be viewed. Fixed!
  * Bugfix: Sometimes settings for the FTP server did not
    show up correctly in the web setup pages. Fixed!
  * Several other minor changes.


* 2007-01-13
  Xerver 4.31 released.
  Xerver is bounded with Xerver Free FTP Server 2.00.
  * Changes to the MIME types.

