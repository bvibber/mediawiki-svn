echo "Removing combined scripts and styles"
rm js/js2.combined.*
rm js/plugins.combined.*
rm css/combined.*
echo "Merging raw scripts and styles"
cat js/js2/*.js > js/js2.combined.js
cat js/plugins/*.js > js/plugins.combined.js
cat css/*.css > css/combined.css
# For more info on JSMin, see: http://www.crockford.com/javascript/jsmin.html
echo "Minifying merged scripts and styles"
jsmin < js/js2.combined.js > js/js2.combined.min.js
jsmin < js/plugins.combined.js > js/plugins.combined.min.js
cat css/combined.css | sed -e 's/^[ 	]*//g; s/[ 	]*$//g; s/\([:{;,]\) /\1/g; s/ {/{/g; s/\/\*.*\*\///g; /^$/d' | sed -e :a -e '$!N; s/\n\(.\)/\1/; ta' >css/combined.min.css
