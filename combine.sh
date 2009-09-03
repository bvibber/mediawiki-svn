echo "Removing combined scripts and styles"
rm js/js2.combined.*
rm js/plugins.combined.*
rm css/combined.*
echo "Merging raw scripts and styles"
# Explicitly including scripts is important, because loading order is important
cat js/js2/jquery-1.3.2.js js/js2/jquery-ui-1.7.2.js js/js2/js2.js > js/js2.combined.js
cat js/plugins/jquery.async.js js/plugins/jquery.browser.js js/plugins/jquery.cookie.js js/plugins/jquery.suggestions.js js/plugins/jquery.textSelection.js js/plugins/jquery.wikiEditor.js js/plugins/jquery.wikiEditor.dialogs.js js/plugins/jquery.wikiEditor.toolbar.js js/plugins/jquery.wikiEditor.toc.js > js/plugins.combined.js
# Styles can be loaded in any order
cat css/*.css > css/combined.css
# For more info on JSMin, see: http://www.crockford.com/javascript/jsmin.html
echo "Minifying merged scripts and styles"
jsmin < js/js2.combined.js > js/js2.combined.min.js
jsmin < js/plugins.combined.js > js/plugins.combined.min.js
cat css/combined.css | sed -e 's/^[ 	]*//g; s/[ 	]*$//g; s/\([:{;,]\) /\1/g; s/ {/{/g; s/\/\*.*\*\///g; /^$/d' >css/combined.min.css
