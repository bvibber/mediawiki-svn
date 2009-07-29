cd Resources
echo "Removing combined files"
rm jquery.combined.*
echo "Combined raw files"
cat jquery.js jquery.*.js > jquery.combined.js
# For more info on JSMin, see: http://www.crockford.com/javascript/jsmin.html
echo "Minifying combined files"
jsmin < jquery.combined.js > jquery.combined.min.js