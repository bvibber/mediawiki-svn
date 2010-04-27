# RPEDFileReader.py by Tisane, http://www.mediawiki.org/wiki/User:Tisane
#
# This script is free software that is available under the terms of the Creative Commons
# Attribution-Sharealike 3.0 license and the current version of the GNU General Public License.
#
# The purpose of this script is to read a text file (specifically, the list of page titles from
# Wikipedia's data dump) and add each page title to a database table.

FILES = ["enwiki-20100312-all-titles-in-ns0"]

# Get the Data
import MySQLdb
import sys

db= MySQLdb.connect(host='localhost', user='rpedorg' , passwd='password', db='rpedorg_beta2')
cursor= db.cursor()

numArgs=0
titleStart="foo"
notYetThere=False

# FIXME: This argument-parsing system totally blows and doesn't work for strings
# starting with unusual characters (e.g. parentheses)
for arg in sys.argv:
    numArgs+=1
    if numArgs==2:
	if arg!='-l':
	    if arg!='line':
		break
    if numArgs==3:
	titleStart=arg
	notYetThere=True
lnum=0

for file in FILES:
    infile = open(file,"r")
    while infile:
	line = infile.readline()
	line=line.rstrip("\n")
	if notYetThere==True:
	    if line==titleStart:
	    	notYetThere==False
	if notYetThere==False:
	    cursor.execute("INSERT INTO rped_page (rped_page_title) VALUES (%s)", (line,))
	lnum+=1
	if lnum%100==0:
	    print "%s: %s" % (lnum, line,)