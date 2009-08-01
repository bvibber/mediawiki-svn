echo "Removing combined files"
rm js/jquery.combined.*
rm css/combined.*
echo "Combined raw files"
cat js/jquery.js js/jquery.*.js > js/jquery.combined.js
cat css/*.css > css/combined.css
# For more info on JSMin, see: http://www.crockford.com/javascript/jsmin.html
echo "Minifying combined files"
jsmin < js/jquery.combined.js > js/jquery.combined.min.js
cat css/combined.css | sed -e 's/^[ 	]*//g; s/[ 	]*$//g; s/\([:{;,]\) /\1/g; s/ {/{/g; s/\/\*.*\*\///g; /^$/d' | sed -e :a -e '$!N; s/\n\(.\)/\1/; ta' >css/combined.min.css
