import MySQLdb
import re
import sys
import urllib

globalConnection = None

def runLoop(inputFile, targetPages=None):
	for line in inputFile:
		# Skip lines that are just going to be hitting the upload server
		# or common skin files
		if line.find(" GET http://upload.wikimedia.org/") == -1 \
			and line.find(".org/skins-1.5/") == -1:
			page = extractPage(line)
			if page and (targetPages == None or page in targetPages):
				recordHit(page)
	closeConnection()

def extractPage(line):
	url = extractUrl(line)
	if url and \
			"?" not in url and \
			url[0:7] == "http://":
		bits = url[7:].split("/", 2)
		if len(bits) == 3 and bits[1] == "wiki":
			host = bits[0]
			page = normalizePage(bits[2])
			return host + ":" + page
	return None

def extractUrl(line):
	# https://wikitech.leuksman.com/view/Squid_log_format
	# $hostname %sn %ts.%03tu %tr %>a %Ss/%03Hs %<st %rm %ru %Sh/%<A %mt %{Referer}>h %{X-Forwarded-For}>h %{User-Agent}>h
	# ...
	# 9. URL
	bits = line.split(" ", 9)
	if len(bits) > 8 and bits[7] == "GET":
		return bits[8]
	else:
		return None

def normalizePage(page):
	return urllib.unquote(page).replace("_", " ")

def recordHit(page):
	(site, pagename) = page.split(":", 1)
	conn = getConnection()
	# fixme: format timestamp from the log line
	conn.cursor().execute(
		"INSERT INTO hit_counter (hc_ts, hc_site, hc_page) " +
		"VALUES (CURRENT_TIMESTAMP(), %s, %s)",
		(site, pagename))
	conn.commit()

def getConnection():
	global globalConnection
	if not globalConnection:
		globalConnection = openConnection()
	return globalConnection

def openConnection():
	return MySQLdb.connect(host="localhost", user="root", passwd="", db="counter")

def closeConnection():
	global globalConnection
	if globalConnection:
		globalConnection.close()
		globalConnection = None

def listFromFile(filename):
	"""Read list of lines from a file"""
	infile = open(filename)
	out = [line.strip() for line in infile if line.strip() != ""]
	infile.close()
	out.sort()
	return out

if __name__ == "__main__":
	if len(sys.argv) > 1:
		targetPages = listFromFile(sys.argv[1])
		runLoop(sys.stdin, targetPages)
	else:
		runLoop(sys.stdin)
