#!/bin/bash
echo "Content-type: text/html"

echo '<html>'
echo '<body>'
echo '<h1> performance statistics </h1>'
echo '<pre>'
echo '=== Qcache ==='
echo 'show status like "Qcache%";' |  mysql

echo 
echo '== Innodb =='
echo 'show status like "%buffer%";' | mysql

echo '== Memory (1 Mb blocks) =='
free -m

echo '</pre>'
echo '</body>'
echo '</html>'
