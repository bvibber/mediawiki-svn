PRE = cat 
POST = copying.inc.ms | groff -t -ms -Tascii - | col -bx >

all: README NEWS COPYING MANIFEST THANKS

README: readme.ms
	$(PRE) $? $(POST) $@

NEWS: news.ms
	$(PRE) $? $(POST) $@

COPYING: copying.ms
	$(PRE) $? $(POST) $@

MANIFEST: manifest.ms
	$(PRE) $? $(POST) $@

THANKS: thanks.ms
	$(PRE) $? $(POST) $@

clean:
	rm *~
