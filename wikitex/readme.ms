.RP
.DA
.TL
WikiTeX 1.0.1 README
.AU
Peter Danenberg <pcd at wikitex dot org>
.AB
Covers installation and expansion of the WikiTeX system.
.AE
.XS 1
1. Introduction
.XA 1
2. Acquisition
.XA 1
	2.1 CVS
.XA 2
	2.2 FTP
.XA 2
	2.3 HTTP
.XA 2
	2.4 Subversion
.XA 2
3. Installation
.XA 2
	3.1. Prerequisites
.XA 2
	3.2. Proceeding
.XA 3
	3.2.1. Java SVG (Batik)
.XA 4
4. Hacking
.XA 4
	4.1. Getting Started
.XA 4
	4.2. Extending WikiTeX
.XA 5
5. Help
.XA 6
6. Copyright
.XE
.PX
.NH 1
Introduction
.RS
.PP
WikiTeX extends MediaWiki by allowing authors to manipulate graphical objects directly in their articles; support so far includes: math, music, chess, chemistry, go, plots and relational diagrams.  To learn more about WikiTeX, please visit:
.QP
http://wikitex.org
.LP
or http://wikisophia.org to see it in action.
.RE
.NH 1
Acquisition
.RS
.NH 2
CVS
.RS
.PP
WikiTeX can be CVS'ed from MediaWiki's facility at SourceForge:*
.RE
.LD
\%cvs -d:pserver:anonymous@cvs.sf.net:/cvsroot/wikipedia \\
co wikitex
.DE
.FS *
For actual development snapshots see Subversion below.
.FE
.NH 2
FTP
.RS
.PP
The latest stable releases can be downloaded from:
.QP
ftp://wikitex.org
.RE
.NH 2
HTTP
.RS
.PP
The latest releases can be browsed and downloaded from:
.QP
http://wikitex.org/wikitex
.RE
.NH 2
Subversion
.RS
.PP
Use your Subversion client (http://subversion\:.tigris.org) to get the latest development snapshot:
.LD
svn co http://svn.wikitex.org/wikitex/trunk wikitex
.DE
or visit http://svn.wikitex.org/wikitex to browse the codebase via web.
.RE
.RE
.NH 1
Installation
.RS
.NH 2
Prerequisites
.RS
.PP
WikiTeX's installation requires at least Media\%Wiki (wikipedia.sourceforge.net) and LaTeX, which comes preinstalled in many Linux distributions.  Depending on which classes you'd like to support, additional packages may be required (see below).
.PP
We'll eventually call for ImageMagick and dvipng, whose former is also pretty standard fare.
.RE
.NH 2
Process
.RS
.PP
Working from MediaWiki's base installation directory, perform the following:
.nr i 0 1
.IP \n+i.
Create /extensions/wikitex and copy there the distribution.
.IP \n+i.
Add the following line to /LocalSettings.php before the terminal '?>':
.QP
include\ \%'./extensions/wikitex/wikitex.php';
.IP \n+i.
Render /extensions/wikitex/tmp scribable to the web server, but beware to disable scripting.
.IP \n+i.
Verify that ImageMagick (imagemagick.org) is present, and acquire Jan-Aoke Larsson's dvipng:
.RS
.QP
\%http://sourceforge.net/projects/dvipng/
.RE
.IP \n+i.
Install as many of the following packages as you would like to support, or add your own (see Expanding WikiTeX):
.RE
.KS
.TS
expand;
c c c c
l l l l .
Class	Package	Author	URL
_
batik	SVG Java	Apache	apache.org
chem	PPCH-TeX	Hans Hagen	pragma-ade.com
chess	LaTeX Chess	Piet Tutelaers	tug.org
feyn	Feynman	Michael Levine	ctan.org
go	Go	Daniel Bump	stanford.edu
graph	Graphviz	Emden Gansner	research.att.com
greek	Ibycus	Pierre MacKay	tug.org
ling	AVM	Christopher Manning	stanford.edu
	Parsetree	Eirik Hektoen	essex.ac.uk
amsmath	AMS-LaTeX	Amer. Math. Soc.	ams.org
music	Lilypond	Han-Wen Nienhuys	lilypond.org
plot	Gnuplot	Nikos Drakos	gnuplot.info
svg	SVG lite	ImageMagick	imagemagick.org
teng	Tengwar	Ivan Derzhanski	quettar.org
tipa	TIPA	Rei Fukui	ctan.org
xym	XyMTeX	Shinsaku Fujita	kit.ac.jp
.TE
.KE
.RS
.IP \n+i.
Lastly, ensure that your server's $PATH variable includes /usr/bin and /usr/local/bin, or wherever your binaries are installed.
.NH 3
Installing Batik
.RS
.PP
Installing Batik (the Java-based SVG toolkit) requires several unique steps:
.nr i 0 1
.IP \n+i.
Install Java (java.sun.com).
.IP \n+i.
Install Batik (xml.apache.org/batik); if elsewhere than /usr/local/batik, configure wikitex.inc.sh.
.IP \n+i.
If you have XWindows, the Xserver Virtual Frame Buffer should be included; configure Xvfb to start up at runtime by:
.RS
.nr j 0 1
.af j a
.IP \n+j.
Copying the provided xvfb script to /etc/init.d
.IP \n+j.
Linking xvfb at the prompt:
.LP
ln\ -s\ /etc/init.d/xvfb\ /etc/rc2.d/S98xvfb
.RE
.IP \n+i.
Add the following to apachectl or equivalent:
.QP
.RS
\%DISPLAY=localhost:1.0
.br
\%export DISPLAY
.RE
.RE
.RE
.RE
.NH 1
Hacking WikiTeX
.RS
.NH 2
Getting Started
.RS
.PP
Join the WikiTeX-l mailing list, where you can communicate with other users and developers:
.QP
http://lists.wikitex.org/wikitex-l
.LP
and familiarize yourself with Subversion (http://subversion.tigris.org), our collaborative development tool.
.RE
.NH 2
Extending WikiTeX
.RS
.PP
Adding novel packages to WikiTeX can be achieved in several discrete steps; after you have downloaded and installed the package:
.nr i 0 1
.IP \n+i.
Devise a class name for the package, and add a template to /extensions/wikitex in this form:
.RS
.QP
wikitex.<classname>.inc.<ext>
.RE
.IP
The template should expose said package to the renderer; see the current templates for examples.
.IP \n+i.
Unless the class requires non-standard processing (confer Lilypond's midi), skip to step three; else, add a new function in wikitex.sh under <class>.
.IP
Consult wikitex.sh for examples.
.IP \n+i.
Lastly, if the package contains undesirable or insecure directives, add them to:
.RS
.QP
\%objRend::strPost()::arrBlack[<class>]
.LP
in wikitex.php; the which see for details.
.RE
.RE
.RE
.NH 1
Getting Help
.RS
.PP
A great way to meet users and developers is by joining WikiTeX-l (see 4.1 above); alternatively, visit:
.QP
http://archives.wikitex.org/wikitex-l
.LP
to browse the archives.
.RE
.bp
