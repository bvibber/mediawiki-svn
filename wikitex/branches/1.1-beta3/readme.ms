.RP
.DA
.TL
WikiTeX %VERSION% README
.AU
Peter Danenberg <pcd at wikitex dot org>
.AB
.PP
THIS PRODUCT IS IN BETA, AND AS SUCH SHOULD NOT BE DEPLOYED IN A PRODUCTION ENVIRONMENT.
.PP
Covers installation and expansion of the WikiTeX system.
.PP
NOTICE: Failure to implement all security measures in section 3.2 may expose your system to loss or mischief.
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
.XA 3
	3.2. Security
.XA 3
		3.2.1. Makefile
.XA 3
		3.2.2. Unprivileged User
.XA 3
		3.2.3. Disk Quota
.XA 4
		3.2.4. Cron
.XA 4
		3.2.5. Sudo
.XA 4
		3.2.6. Per Package
.XA 5
			3.2.6.1. LaTeX
.XA 5
		3.2.7. Audit
.XA 5
	3.3. Installation
.XA 6
4. Hacking
.XA 6
	4.1. Getting Started
.XA 6
	4.2. Extending WikiTeX
.XA 6
5. Help
.XA 7
6. Colophon
.XA 8
7. Copyright
.XE
.PX
.NH 1
Introduction
.RS
.PP
WikiTeX extends MediaWiki by allowing authors to manipulate graphical objects directly in their articles; support so far includes: math, music, chess, chemistry, go, plotting, relational diagrams and schematic capture.
.PP
Visit http://wikitex.org to learn more; or to see WikiTeX in action:
.QP
http://wikisophia.org
.RE
.NH 1
Acquisition
.RS
.NH 2
CVS
.RS
.PP
WikiTeX can be CVSed from MediaWiki's facility at SourceForge:*
.FS *
For actual development snapshots see Subversion below.
.FE
.RE
.LD
\%cvs -d:pserver:anonymous@cvs.sf.net:/cvsroot/wikipedia \\
co wikitex
.DE
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
svn co http://svn.wikitex.org/wikitex/branches/1.1 \\
wikitex
.DE
or visit http://wikitex.org/viewcvs/wikitex to browse the codebase via web.
.RE
.RE
.NH 1
Installation
.RS
.NH 2
Prerequisites
.RS
.PP
In a nutshell, WikiTeX requires a successful installation of:
.RS
.IP \(bu
PHP 5.1.0+ (php.net)
.IP \(bu
MediaWiki 1.6alpha1+ (wikipedia.sf.net)
.IP \(bu
LaTeX (latex-project.org)
.IP \(bu
ImageMagick (imagemagick.org)
.IP \(bu
dvipng (dvipng.sf.net)
.RE
.PP
As of writing, WikiTeX requires the CVS version of MediaWiki; to get it, issue:
.RE
.KS
.LD
cvs -z3 -d:pserver:anonymous@cvs.sourceforge.net:/cvsroot/wikipedia\\
co -P phase3
.DE
.KE
.RS
.PP
Lilypond (lilypond.org), Gnuplot (gnuplot.info), and Graphviz (graphviz.org) are required for music, plot, and graph; and as many of the packages under \(sc3.3.5 (see below) whose classes you would like to support.
.RE
.NH 2
Security
.RS
.PP
WikiTeX requires preparation to mitigate risk; failure to prepare in whole or in part may expose your system to loss and mischief.
.NH 3
Makefile
.RS
.PP
We've gathered all the editable parameters into one place; edit \(oqMakefile\(cq and run:
.QP
make
.LP
every time you've made changes.
.RE
.NH 3
Unprivileged User
.RS
.PP
WikiTeX runs as the unprivileged user \(oqwikitex\(cq to restrict access to the local file-system.  You must protect sensitive world-writable and -readable resources, however.
.LP
Create the user wikitex:
.QP
useradd -M -s /sbin/nologin wikitex
.RE
.NH 3
Disk Quota
.RS
.PP
To prevent runaway scripts or DoS attacks from usurping storage, institute disk quotas.  See:
.QP
\%http://www.tldp.org/HOWTO/Quota.html
.LP
for details.
.nr i 0 1
.IP \n+i.
Edit /etc/fstab and, next to "defaults," add "usrquota" for the partition on which WikiTeX runs (hereafter /part); e.g.:
.LD
/part /home reiserfs defaults,usrquota 1 1
.DE
.IP \n+i.
Remount /part:
.IP
.RS
mount -o remount /part
.RE
.IP \n+i.
Update quota data:
.IP
.RS
quotacheck -vguma
.RE
.IP \n+i.
Turn on quotas:
.IP
.RS
quotaon -av
.RE
.IP \n+i.
Implement quotas for user wikitex:
.IP
.RS
edquota -u wikitex
.RE
.IP
The following values will give WikiTeX one hundred megabytes and ten thousand files to play with (edit the values with asterisks):
.RE
.LD
Filesystem  blocks  soft  hard*   inodes  soft  hard*
/part       186     0     100000  30      0     10000
.DE
.NH 3
Cron
.RS
.PP
To prevent legitimate files from exceeding the disk quota, set up a crontab to purge unused files.
.PP
Determine your webserver's user (consult \(oqhttpd.conf\(cq for Apache), and execute:
.RS
.LD
crontab -u <apache-user> wikitex.cron
.DE
.RE
from extensions/wikitex.
.RE
.NH 3
Sudo
.RS
.PP
You will need sudo version 1.6.8 or above (verify by \(oqsudo -V\(cq), with support for NOEXEC compiled in.  Remove any WikiTeX cruft from /etc/sudoers, and issue:
.QP
cat wikitex.sudoers >> /etc/sudoers
.LP
and then:
.QP
visudo -c
.LP
If there are parse errors, run \(oqvisudo\(cq and correct any mistakes.
.RE
.NH 3
Per Package
.RS
.PP
Following are package-specific security measures.
.NH 4
LaTeX
.RS
.PP
Edit \(oqtexmf.cnf\(cq, modifying the following variables:
.QP
shell_escape = f
.br
openout_any = p
.br
openin_any = p
.RE
.RE
.NH 3
Audit
.RS
.PP
\(oqwikitex-audit.sh\(cq has been provided to assess the integrity of an installation; as root, run:
.QP
\&./wikitex-audit.sh
.LP
until you satisfy it.
.RE
.RE
.NH 2
Installation
.RS
.PP
NOTE: DON'T CONTINUE UNTIL wikitex-audit.sh EXECUTES SANS FATAL ERRORS.
.PP
Fulfill the security guidelines per \(sc3.2, and install MediaWiki (see 3.1); then, working from MediaWiki's base directory, perform the following:
.nr i 0 1
.IP \n+i.
Create /extensions/wikitex and copy thither the distribution.
.IP \n+i.
Add the following line to /LocalSettings.php before the terminal \(oq?>\(cq:
.QP
include\ \%'./extensions/wikitex/Wikitex.php';
.IP \n+i.
Render /extensions/wikitex/tmp scribable to the web server:*
.FS *
Consult your server config for the appropriate user and group id.
.FE
.QP
chown\ nobody:nobody\ \%/extensions/wikitex/tmp
.IP \n+i.
Disable PHP in /extensions/wikitex/tmp by issuing:
.RS
.QP
php_flag engine off
.LP
from .htaccess in the same directory; disable CGI, if necessary.
.RE
.IP \n+i.
Install as many of the following packages whose classes you would like to support, or add your own (see Expanding WikiTeX):
.RE
.TS
expand;
c c c c
l l l l .
Class	Package	Author	URL
_
amsmath	AMS-LaTeX	Amer. Math. Soc.	ams.org
chem	XyMTeX	Shinsaku Fujita	kit.ac.jp
chess	Skak	Torben Hoffmann	ctan.org
feyn	FenyMF	Thorsten Ohl	ctan.org
go	Go	Daniel Bump	stanford.edu
graph	Graphviz	Emden Gansner	research.att.com
greek	Ibycus	Pierre MacKay	tug.org
music	Lilypond	Han-Wen Nienhuys	lilypond.org
plot	Gnuplot	Nikos Drakos	gnuplot.info
svg	SVG lite	ImageMagick	imagemagick.org
teng	Tengwar	Ivan Derzhanski	quettar.org
ipa	TIPA	Rei Fukui	ctan.org
.TE
.RS
.IP \n+i.
(Optional) If you would like your users to be able to source uploaded files, specify the following in LocalSettings.php:
.RS
.QP
$wgStrictFileExtensions = false;
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
\%http://lists.wikitex.org/listinfo/wikitex-l
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
Devise a class name for the package, and add a template to /extensions/wikitex/template in this form:
.RS
.QP
<classname>.<ext>
.RE
.IP
The template should expose said package to the renderer; see the current templates for examples.
.IP \n+i.
If the class requires non-standard (extra-LaTeX) processing, add a new function in wikitex.sh under <class>.
.IP
Consult wikitex.sh for examples.
.IP \n+i.
Lastly, add a hook to \(oqwikitex.php\(cq and an element\(->hook mapping to \(oqwikitex.inc.php\(cq.
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
.NH 1
Colophon
.RS
.PP
This document was prepared in GNU troff using the ms macro package; preprocessed with tbl, the table formatter; and lastly filtered through col to remove reverse line feeds and tabs:
.QP
groff -ms -t -Tascii SOURCE | col -bx > OUT
.RE
.bp
