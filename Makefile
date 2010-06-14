#
# Handy makefile to combine and minify css and javascript files
#
SHELL := /bin/bash

CSS := \
	css/suggestions.css\
	css/vector.collapsibleNav.css\
	css/vector.expandableSearch.css\
	css/wikiEditor.css\
	css/wikiEditor.dialogs.css\
	css/wikiEditor.toc.css\
	css/wikiEditor.toolbar.css\
	css/wikiEditor.preview.css

JUI := \
	js/js2stopgap/ui.core.js\
	js/js2stopgap/ui.datepicker.js\
	js/js2stopgap/ui.dialog.js\
	js/js2stopgap/ui.draggable.js\
	js/js2stopgap/ui.resizable.js\
	js/js2stopgap/ui.tabs.js

PLUGINS := \
	js/usability.js\
	js/plugins/jquery.async.js\
	js/plugins/jquery.autoEllipsis.js\
	js/plugins/jquery.browser.js\
	js/plugins/jquery.collapsibleTabs.js\
	js/plugins/jquery.color.js\
	js/plugins/jquery.cookie.js\
	js/plugins/jquery.delayedBind.js\
	js/plugins/jquery.expandableField.js\
	js/plugins/jquery.suggestions.js\
	js/plugins/jquery.textSelection.js\
	js/plugins/jquery.wikiEditor.js\
	js/plugins/jquery.wikiEditor.dialogs.js\
	js/plugins/jquery.wikiEditor.highlight.js\
	js/plugins/jquery.wikiEditor.preview.js\
	js/plugins/jquery.wikiEditor.publish.js\
	js/plugins/jquery.wikiEditor.templateEditor.js\
	js/plugins/jquery.wikiEditor.toc.js\
	js/plugins/jquery.wikiEditor.toolbar.js\
	js/thirdparty/contentCollector.js

WIKIEDITOR_MODULES := \
	WikiEditor/Modules/Highlight/Highlight.js\
	WikiEditor/Modules/Preview/Preview.js\
	WikiEditor/Modules/Publish/Publish.js\
	WikiEditor/Modules/Toc/Toc.js\
	WikiEditor/Modules/Toolbar/Toolbar.js\
	WikiEditor/Modules/TemplateEditor/TemplateEditor.js\
	WikiEditor/Modules/AddMediaWizard/AddMediaWizard.js

VECTOR_MODULES := \
	Vector/Modules/CollapsibleNav/CollapsibleNav.js\
	Vector/Modules/CollapsibleTabs/CollapsibleTabs.js\
	Vector/Modules/EditWarning/EditWarning.js\
	Vector/Modules/ExpandableSearch/ExpandableSearch.js\
	Vector/Modules/FooterCleanup/FooterCleanup.js\
	Vector/Modules/SimpleSearch/SimpleSearch.js

USABILITYINITIATIVE_HOOKS := \
	css/combined.css\
	css/combined.min.css\
	$(CSS)\
	js/plugins.combined.js\
	js/plugins.combined.min.js\
	$(PLUGINS)\
	js/js2stopgap/jui.combined.js\
	js/js2stopgap/jui.combined.min.js\
	$(JUI)

WIKIEDITOR_HOOKS := \
	$(WIKIEDITOR_MODULES)\
	WikiEditor/WikiEditor.combined.js\
	WikiEditor/WikiEditor.combined.min.js	

VECTOR_HOOKS := \
	$(VECTOR_MODULES)\
	Vector/Vector.combined.js\
	Vector/Vector.combined.min.js
	
all: \
	$(USABILITYINITIATIVE_HOOKS)\
	$(WIKIEDITOR_HOOKS)\
	UsabilityInitiative.hooks.php\
	WikiEditor/WikiEditor.hooks.php\
	Vector/Vector.hooks.php\
	

# JavaScript Combination

js/js2stopgap/jui.combined.js: $(JUI)
	cat $(JUI) > js/js2stopgap/jui.combined.js

js/plugins.combined.js: $(PLUGINS)
	cat $(PLUGINS) > js/plugins.combined.js

WikiEditor/WikiEditor.combined.js: $(WIKIEDITOR_MODULES)
	cat $(WIKIEDITOR_MODULES) > WikiEditor/WikiEditor.combined.js

Vector/Vector.combined.js: $(VECTOR_MODULES)
	cat $(VECTOR_MODULES) > Vector/Vector.combined.js

# JavaScript Minification

js/js2stopgap/jui.combined.min.js: js/js2stopgap/jui.combined.js jsmin
	if [ -e ./jsmin ]; then ./jsmin < js/js2stopgap/jui.combined.js > js/js2stopgap/jui.combined.min.js;\
	else jsmin < js/js2stopgap/jui.combined.js > js/js2stopgap/jui.combined.min.js; fi

js/plugins.combined.min.js : js/plugins.combined.js jsmin 
	if [ -e ./jsmin ]; then ./jsmin < js/plugins.combined.js > js/plugins.combined.min.js;\
	else jsmin < js/plugins.combined.js > js/plugins.combined.min.js; fi

WikiEditor/WikiEditor.combined.min.js: WikiEditor/WikiEditor.combined.js
	if [ -e ./jsmin ]; then ./jsmin < WikiEditor/WikiEditor.combined.js > WikiEditor/WikiEditor.combined.min.js;\
	else jsmin < WikiEditor/WikiEditor.combined.js > WikiEditor/WikiEditor.combined.min.js; fi

Vector/Vector.combined.min.js: Vector/Vector.combined.js
	if [ -e ./jsmin ]; then ./jsmin < Vector/Vector.combined.js > Vector/Vector.combined.min.js;\
	else jsmin < Vector/Vector.combined.js > Vector/Vector.combined.min.js; fi

# CSS Combination

css/combined.css: $(CSS)
	cat $(CSS) > css/combined.css

# CSS Minification

css/combined.min.css : css/combined.css
	cat css/combined.css | sed -e 's/^[ 	]*//g; s/[ 	]*$$//g; s/\([:{;,]\) /\1/g; s/ {/{/g; s/\/\*.*\*\///g; /^$$/d'\
	> css/combined.min.css

# JSMin - For more info on JSMin, see: http://www.crockford.com/javascript/jsmin.html

jsmin:
	type -P jsmin &>/dev/null || ( wget http://www.crockford.com/javascript/jsmin.c; gcc jsmin.c -o jsmin )

# Simple incrementer of versions

UsabilityInitiative.hooks.php: $(USABILITYINITIATIVE_HOOKS)
WikiEditor/WikiEditor.hooks.php: $(WIKIEDITOR_HOOKS)
Vector/Vector.hooks.php: $(VECTOR_HOOKS)

# - Metarule for the above three files -
# For each modified file (make $?):
#  Set $basefile to the path relative to the file being modified (ie. strip the folder).
#  Use sed to replace the line for that file with '+1' appended to the version.
#  Note that $${basefile//\//\\/} expands in the shell to $basefile with all slashes escaped.
# End for
# For each group of summing numbers inside the target file, use bc to calculate the sum and replace with sed.
%.hooks.php: $(WIKIEDITOR_HOOKS)
	for file in $?; do basefile="$${file#$(shell echo $* | sed "s/\([^\/]*\/\).*/\\1/")}"; sed -i -e "s/\(.*'src' => '$${basefile//\//\\/}', 'version' => \)\([0-9+]*\)\(.*\)/\\1\\2+1\\3/" $@; \
											for i in $$(grep --only-matching -P " ([0-9]+(\+[0-9]))+ " $@); do sed -i -e "s/ $$i / $$(($$i)) /" $@; done; done 

# Actions

distclean: clean
	rm -rf jsmin
	rm -rf jsmin.c

clean:
	rm -f js/plugins.combined.*
	rm -f css/combined.*
