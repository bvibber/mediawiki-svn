#!/usr/bin/python

import re
import sys

lineSplit = re.compile(r"^[\w.]+ \d+ [0-9.]+ \d+ [\d.]+ \w+/\d+ (\d+) \w+ (\S+)")

matchTypes = [
	("upload", re.compile(r"^http://upload.wikimedia.org/")),
	("article", re.compile(r"^http://[^/]+/wiki/")),
	("wikifiles", re.compile(r"^http://[^/]+/w/")),
	("skins-css", re.compile(r"^http://[^/]+/skins-1.5/.*\.css")),
	("skins-js", re.compile(r"^http://[^/]+/skins-1.5/.*\.js")),
	("skins-image", re.compile(r"^http://[^/]+/skins-1.5/.*\.(?:png|gif|jpg)"))]
groups = [group for (group, regex) in matchTypes]
groups.append("other")

totalHits = 0
totalBytes = 0
hits = {}
bytes = {}
for group in groups:
	hits[group] = 0
	bytes[group] = 0

for line in sys.stdin:
	matches = lineSplit.match(line)
	if matches:
		size = int(matches.group(1))
		url = matches.group(2)
		totalHits += 1
		totalBytes += size
		for (group, regex) in matchTypes:
			if regex.match(url):
				hits[group] += 1 
				bytes[group] += size
				break
		else:
			hits["other"] += 1
			bytes["other"] += size

if totalHits == 0 or totalBytes == 0:
	print "no hits detected :("

for group in groups:
	if hits[group] > 0:
		print "%12s %8d hits (%6.2f%%) %16d bytes (%6.2f%%) %10.1f bytes/hit" % (
			group,
			hits[group],
			100.0 * float(hits[group]) / float(totalHits),
			bytes[group],
			100.0 * float(bytes[group]) / float(totalBytes),
			float(bytes[group]) / float(hits[group]))
