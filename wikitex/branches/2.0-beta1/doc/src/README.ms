.ig
WikiTeX: expansible LaTeX module for MediaWiki
Copyright (C) 2004-7 Peter Danenberg
See doc/COPYING for details.
..
.so header.ms
.TI README "README covers hortation and installation."
.XH 1 Prerequisites S 0
.XH 2 Client
.IP \(bu
MediaWiki
.IP \(bu
PHP compiled with curl and xmlrpc\**
.FS
We'd like to remove the curl- and xmlrpc-requirements in the future by providing an (albeit slower) XML-RPC fallback like phpxmlrpc or Incutio.
.FE
.XH 2 Server
.IP \(bu
Apache
.IP \(bu
mod_python
.IP \(bu
teTeX and dvipng (or TeX Live)
.IP \(bu
Python 2.5\**
.FS
We take advantage of with-statements which will become native in 2.6.
.FE
.XH 1 Installation
.PP
If you'd merely like to connect to a WikiTeX server, read Client Installation and forget the rest.  If you'd like to set up your own server, on the other hand, read it all.
.XH 2 Client
.nr i 0 1
.IP \n+i.
Copy <wikitex>/src/client/mediawiki to:
.IP
.RS
<mediawiki>/extensions/wikitex
.RE
.IP \n+i.
Copy config/default.php to config/local.php.
.IP \n+i.
Edit local.php to satisfaction.
.IP \n+i.
To <mediawiki>/LocalSettings.php, add:
.IP
.RS
\%include_once('./extensions/wikitex/main.php');
.RE
.PP
There's also an optional install_client target included in the Python-distribution.
.XH 2 Server
.nr i 0 1
.IP \n+i.
Copy src/server/wikitex/config/default.py to:
.IP
.RS
src/server/wikitex/config/local.py
.RE
.IP \n+i.
Edit local.py to satisfaction.\**
.FS
See section 3, Configuration.
.FE
.IP \n+i.
Run:
.IP
.RS
python setup.py build
.RE
.IP
and:
.IP
.RS
sudo python setup.py install
.RE
.IP \n+i.
(Optional) Mount /usr/local/var/wikitex on its own partition with NOSUID.
.IP \n+i.
Set up a chroot environment in /usr/local/var/wikitex/latex for teTeX and dvipng.\**
.FS
See section 4, Chroot.
.FE
.IP \n+i.
Within the chroot, edit texmf.cnf to read:
.IP
.RS
shell_escape = f
.br
openout_any = p
.br
openin_any = p
.RE
.IP \n+i.
???
.IP \n+i.
Profit!
.XH 2 Handler
.PP
The Apache-handler mediates between the client and chrooted daemons; it requires mod_python.
.nr i 0 1
.IP \n+i.
Install mod_python,\**
.FS
http://www.modpython.org
.FE
if necessary.
.IP \n+i.
You may need to edit the .htaccess which was installed, by default, in:
.IP
.RS
\%/usr/local/apache2/htdocs/wikitex/.htaccess
.RE
.XH 1 Execution
.PP
`wikitex' and `wikitex-debug' are installed by default in /usr/local/bin.  They both take `start' and `stop' as arguments, but `wikitex-debug' provides some additional output to the syslog and keeps all work directories intact in /usr/local/var/wikitex/latex/tmp for inspection.
.PP
`wikitex', on the other hand, cleans up work directories (except in cases of resource-faults); and prints minimally to syslog.
.PP
`wikitex' and `wikitex-debug' need to be run as root to chroot into the rendering environment, but drop privileges immediately to wikitex:wikitex.
.XH 1 Configuration
.XH 2 Directories
.PP
WikiTeX allows relatively fine-tuned control over the install-location, and provides some reasonable defaults.
.XH 2 Users
.PP
WikiTeX also allows the ability to specify the nonprivileged user to whom it will drop after detaching from the console; it should be distinct from the Apache user, and ideally ad-hoc.
.XH 2 Facilities
.PP
Please inspect the facilities section to make sure that they correspond to your chroot environment; in particular, you will need to specify absolute paths to binaries.
.XH 2 Resources
.PP
WikiTeX's resource managament has three layers: internal, kernel-enforced and per-user.
.XH 3 Internal
.PP
The `default_wait' dictionary provides fine-grained control over signals sent to runaway processes; `default_wait' works over and above RLIMIT_CPU, whose effects can be hard to predict.
.XH 3 Kernel-Enforced
.PP
Read more about kernel-limits in `man setrlimit'; WikiTeX includes fairly Draconian policies which may need to be relaxed for production-sites.
.XH 3 Per-User
.PP
`ctime_unit' and `max_ctime_per_unit' provide ways to limit resource-usage from particular users.  WikiTeX calculates how much CPU-time a user has usurped within one `ctime_unit'; if its usage exceeds `max_ctime_per_unit' with that unit of time, the request is temporarily denied.
.PP
The upshot is that users may have to submit several requests to complete a particularly time-consuming document.
.XH 1 Chroot
.PP
Chrooting is art and science; and can be risky if, for instance, you leave suid-binaries lying around.
.PP
That said, jailkit\**
.FS
http://olivier.sessink.nl/jailkit
.FE
and makejail\**
.FS
http://www.floc.net/makejail
.FE
are useful utilities.
.XH 2 teTeX
.PP
By way of example, the following outlines how dvipng and teTeX were chrooted on our local machine.
.nr i 0 1
.IP \n+i.
Install teTeX relative to the chroot, passing the following to configure; where <root> corresponds to /usr/local/var/wikitex/latex (or similar):
.IP
.RS
\&./configure --disable-multiplatform \\
.br
--prefix=<root>/usr/local/teTeX
.RE
.IP \n+i.
Similarly for dvipng:
.IP
.RS
\&./configure --prefix=<root>/usr/local
.RE
.IP \n+i.
Finally, these libraries were necessary on Fedora Core 6 (your mileage may vary):
.RS
.LD
/lib/libm.so.6
/lib/libexpat.so.0
/lib/libdl-2.5.so
/lib/libexpat.so.0.5.0
/lib/libgcc_s.so.1
/lib/libstdc++.so.6
/lib/ld-2.5.so
/lib/libm-2.5.so
/lib/libc-2.5.so
/lib/ld-linux.so.2
/lib/libdl.so.2
/lib/libc.so.6
/lib/libtermcap.so.2
/usr/lib/libfontconfig.so.1
/usr/lib/libgd.so.2
/usr/lib/libjpeg.so.62
/usr/lib/libX11.so.6.2.0
/usr/lib/libz.so.1
/usr/lib/libgd.so.2.0.0
/usr/lib/libXpm.so.4.11.0
/usr/lib/libpng12.so.0.10.0
/usr/lib/libXau.so.6.0.0
/usr/lib/libfontconfig.so.1.1.0
/usr/lib/libXau.so.6
/usr/lib/libXpm.so.4
/usr/lib/libXdmcp.so.6.0.0
/usr/lib/libX11.so.6
/usr/lib/libXdmcp.so.6
/usr/lib/libfreetype.so.6.3.10
/usr/lib/libjpeg.so.62.0.0
/usr/lib/libz.so.1.2.3
/usr/lib/libpng12.so.0
/usr/lib/libfreetype.so.6
/etc/ld.so.cache
.DE
.RE
.XH 2 "TeX Live"
.PP
Alternatively, TeX Live\**
.FS
http://www.tug.org/texlive
.FE
provides a statically-linked dvipng in its full installation, and significantly reduces the library-overhead.
.PP
When running `install-tl.sh', supply the following (or similar) for TEXDIR:
.QP
\%/usr/local/var/wikitex/latex/usr/local/texlive
.PP
The latex-facility in local.py may need to be adjusted accordingly; for instance:
.QP
\%/usr/local/texlive/bin/i386-linux/latex
.LP
in lieu of /usr/local/teTeX/bin/latex.
.XH 1 Architecture
.PP
In short, an XML-RPC client (e.g., the MediaWiki-client) connects to the mod_python frontend (\(lqTrader Joe\(rq) served by Apache.
.PP
Trader Joe, in turn, connects to autonomous, unprivileged, chrooted d\(aemons sitting on arbitrary machines, that actually render the content.
.PP
After authentication,\**
.FS
Trader Joe uses basic-, but key-based-auth should be possible using curl's transport-layer.
.FE
but before Trader Joe befrays the rendering drones, he checks an IP->CPU-time database to see whether the client has used up their allotted rendering time.
.LD
                                         LaTeX-d\(aemon
                                        /
client<--(auth)-->traderjoe<--(resource)-->LilyPond-d\(aemon
                                        \\
                                         ...-d\(aemon
.DE
.PP
The d\(aemons should not be accessible to the intarweb at large.
.so footer.ms
