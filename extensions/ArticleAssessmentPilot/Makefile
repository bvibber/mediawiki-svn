#
# Handy makefile to combine and minify css and javascript files
#
SHELL := /bin/bash

JS := \
	js/ArticleAssessment.js\
	js/jquery.cookie.js\
	js/jquery.stars.js\
	js/jquery.tipsy.js

all: \
	js/ArticleAssessment.combined.min.js
	

# JavaScript Combination
js/ArticleAssessment.combined.js: $(JS)
	cat $(JS) > js/ArticleAssessment.combined.js

# JavaScript Minification

js/ArticleAssessment.combined.min.js: js/ArticleAssessment.combined.js jsmin
	if [ -e ./jsmin ]; then ./jsmin < js/ArticleAssessment.combined.js > js/ArticleAssessment.combined.min.js;\
	else jsmin < js/ArticleAssessment.combined.js > js/ArticleAssessment.combined.min.js; fi

# JSMin - For more info on JSMin, see: http://www.crockford.com/javascript/jsmin.html

jsmin:
	type -P jsmin &>/dev/null || ( wget http://www.crockford.com/javascript/jsmin.c; gcc jsmin.c -o jsmin )

# Actions

distclean: clean
	rm -rf jsmin
	rm -rf jsmin.c

clean:
	rm -f js/ArticleAssessment.combined.*

