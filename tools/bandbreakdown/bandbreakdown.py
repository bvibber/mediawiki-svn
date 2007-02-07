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

interval = 1000

hits = {"total": 0}
bytes = {"total": 0}
for group in groups:
	hits[group] = 0
	bytes[group] = 0

def dump(groups, hits, bytes):
	print "--"
	if hits["total"] == 0 or bytes["total"] == 0:
		print "no hits detected :("

	for group in groups:
		if hits[group] > 0:
			print "%12s %8d hits (%6.2f%%) %16d bytes (%6.2f%%) %10.1f bytes/hit" % (
				group,
				hits[group],
				100.0 * float(hits[group]) / float(hits["total"]),
				bytes[group],
				100.0 * float(bytes[group]) / float(bytes["total"]),
				float(bytes[group]) / float(hits[group]))

for line in sys.stdin:
	matches = lineSplit.match(line)
	if matches:
		size = int(matches.group(1))
		url = matches.group(2)
		hits["total"] += 1
		bytes["total"] += size
		for (group, regex) in matchTypes:
			if regex.match(url):
				hits[group] += 1 
				bytes[group] += size
				break
		else:
			hits["other"] += 1
			bytes["other"] += size
	if hits["total"] % interval == 0:
		dump(groups, hits, bytes)

if hits["total"] % interval != 0:
	dump(groups, hits, bytes)
