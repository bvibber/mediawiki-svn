#!/usr/bin/python

#Page view counter
#	Reads squid logs (https://wikitech.leuksman.com/view/Squid_log_format)
#	Normalizes page name, aggregates them for a configurable time window, shoves the 
#	aggregates into a database.
# Usage: ./counter.py [list of allowed pages] < logfile
# Be sure sampleHits is set correctly

#Notes:
# * Requires pyjudy (http://www.dalkescientific.com/Python/PyJudy.html)
#   (python dicts and sets use too much darn memory)
# * The final incomplete aggregation window is discarded.
# * Fixed aggregation windows that align to time of day may be more useful than the current
#   behavior.

import MySQLdb
import re
import sys
import urllib
import time
import pyjudy

sampleHits = 100    # Number of hits to record per sample
aggThresh = 3600  # Number of sample seconds needed to trigger a data export

globalConnection = None
aggCounter = pyjudy.JudySLInt()
aggRange = (sys.maxint,0)

def runLoop(inputFile, targetPages=None):
	for line in inputFile:
		# Skip lines that are just going to be hitting the upload server
		# or common skin files
		if line.find(" GET http://upload.wikimedia.org/") == -1 \
			and line.find(".org/skins-1.5/") == -1:
			page,timestamp = extractPage(line)
			if page and (targetPages == None or page in targetPages):
				recordHit(page,timestamp)
	closeConnection()

def extractPage(line):
	# Extract the page name from the URL.
	# A check should probably be placed here to toss requests with
	# page names larger than the maximum length.
	url,timestamp = extractUrl(line)
	if url and \
			"?" not in url and \
			url[0:7] == "http://":
		bits = url[7:].split("/", 2)
		if len(bits) == 3 and bits[1] == "wiki":
			host = bits[0]
			page = normalizePage(bits[2])
			return (host + ":" + page, timestamp)
	return None

def extractUrl(line):
	# https://wikitech.leuksman.com/view/Squid_log_format
	# $hostname %sn %ts.%03tu %tr %>a %Ss/%03Hs %<st %rm %ru %Sh/%<A %mt %{Referer}>h %{X-Forwarded-For}>h %{User-Agent}>h
	# ...
	# 3. Seconds (and milliseconds) since epoch
	# ...
	# 9. URL
	bits = line.split(" ", 10)
	if len(bits) > 8 and bits[7] == "GET":
		return (bits[8],int(round(float(bits[2]))))
	else:
		return None

def normalizePage(page):
	return urllib.unquote(page).replace("_", " ")

def recordHit(page,timestamp):
	global aggCounter
	global aggRange
	global aggThresh

	if (max(timestamp,aggRange[1])-aggRange[0] >= aggThresh):
		for item in aggCounter.items():
			(site, pagename) = item[0].split(":", 1)
			conn = getConnection()
			conn.cursor().execute(
				"INSERT INTO hit_counter (hc_tsstart, hc_tsend, hc_site, hc_page, hc_count) VALUES (%s, %s, %s, %s, %s)",
				(time.strftime("%Y-%m-%d %H:%M:%S",time.gmtime(aggRange[0])),time.strftime("%Y-%m-%d %H:%M:%S",time.gmtime(aggRange[1])),site, pagename, item[1]))
			conn.commit()
		aggRange=(aggRange[1],aggRange[1])
		aggCounter.FreeArray()	
	
	if page in aggCounter:
		aggCounter[page] += sampleHits
	else:
		aggCounter[page] = sampleHits
	aggRange=(min(timestamp,aggRange[0]),max(timestamp,aggRange[1]))
	
	

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

def setFromFile(filename):
	"""Read list of lines from a file"""
	infile = open(filename)
	out = pyjudy.JudySLInt()
	for line in infile:
		if line.strip()!="":
			out.Ins(line.strip(),1)
	infile.close()
	return out

if __name__ == "__main__":
	if len(sys.argv) > 1:
		targetPages = setFromFile(sys.argv[1])
		runLoop(sys.stdin, targetPages)
	else:
		runLoop(sys.stdin)
