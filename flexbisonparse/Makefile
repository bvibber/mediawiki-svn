# Taken from CVS:phase3/extensions/wikidiff/Makefile and modified

php_wikiparse.so : lex.yy.c wikiparse.tab.c parsetree.c parsetree.h
	gcc -o wikiparse wikiparse.tab.c parsetree.c lex.yy.c

lex.yy.c : wikilex.l
	flex wikilex.l

wikiparse.tab.c : wikiparse.y
	bison -d wikiparse.y

clean :
	rm -f php_wikiparse.so
	rm -f wikiparse.tab.c
	rm -f wikiparse.tab.h
	rm -f wikiparse_wrap.c
	rm -f lex.yy.c

