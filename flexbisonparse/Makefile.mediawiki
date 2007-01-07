# Taken from CVS:phase3/extensions/wikidiff/Makefile and modified

INSTALL_TARGET = `php-config --extension-dir`/php_wikiparse.so

# For Linux
SHARED = -shared

# For Mac OS X
# SHARED = -bundle

php_wikiparse.so : lex.yy.c wikiparse.tab.c parsetree.c parsetree.h wikiparse_wrap.c
	gcc `php-config --includes` $(SHARED) -o php_wikiparse.so wikiparse.tab.c parsetree.c lex.yy.c wikiparse_wrap.c

wikiparse_wrap.c : wikiparse.i
	swig -php4 wikiparse.i

lex.yy.c : wikilex.l
	flex wikilex.l

wikiparse.tab.c : wikiparse.y
	bison -d wikiparse.y

install : php_wikiparse.so
	cp php_wikiparse.so "$(INSTALL_TARGET)"

uninstall :
	rm -f "$(INSTALL_TARGET)"

clean :
	rm -f php_wikiparse.so
	rm -f wikiparse.tab.c
	rm -f wikiparse.tab.h
	rm -f wikiparse_wrap.c
	rm -f lex.yy.c

test : php_wikiparse.so
	php parse-test.php
