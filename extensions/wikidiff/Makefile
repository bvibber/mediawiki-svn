INSTALL_TARGET = `php-config --extension-dir`/php_wikidiff.so

# For Linux
SHARED = -shared

# For Mac OS X
# SHARED = -bundle

php_wikidiff.so : wikidiff.cpp wikidiff_wrap.cpp
	g++ -O2 `php-config --includes` $(SHARED) -o php_wikidiff.so wikidiff.cpp wikidiff_wrap.cpp

# The below _almost_ works. It gets unresolved symbol errors on load looking for _compiler_globals.
#	MACOSX_DEPLOYMENT_TARGET=10.3 g++ -O2 `php-config --includes` $(SHARED) -o php_wikidiff.so wikidiff.cpp wikidiff_wrap.cpp -undefined dynamic_lookup

wikidiff_wrap.cpp : wikidiff.i
	swig -php4 -c++ wikidiff.i

install : php_wikidiff.so
	cp php_wikidiff.so "$(INSTALL_TARGET)"

uninstall :
	rm -f "$(INSTALL_TARGET)"

clean :
	rm -f php_wikidiff.so
	rm -f wikidiff_wrap.cpp

test : php_wikidiff.so
	php test.php
