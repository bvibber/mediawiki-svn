rem change into the project directory
cd /d %~dp0%
cd ..

rem remove the -Xmx option if your VM does not understand it
java -Xmx192m -cp build\classes;lib\bsh-2.0b2-fixed.jar;lib\lib-util.jar;lib\mwapi.jar;lib\minibpp.jar;lib\commons-httpclient-3.1.jar;lib\commons-logging-1.1.jar;lib\commons-codec-1.3.jar;lib\jericho-html-3.1.jar net.psammead.commonist.Commonist
