import os

class UpgradeError(Exception): pass

def runCommand(command):
	print command
	retval = os.system(command)
	if retval:
		raise UpgradeError("Nonzero return code (%d)! Aborting." % retval)
	return retval


def shellEscape(param):
	"""Escape a string parameter, or set of strings, for the shell."""
	if isinstance(param, basestring):
		return "'" + param.replace("'", "'\\''") + "'"
	elif param is None:
		# A blank string might actually be needed; None means we can leave it out
		return ""
	else:
		return tuple([shellEscape(x) for x in param])

def readFile(filename):
	file = open(filename, "r")
	text = file.read()
	file.close()
	return text

def dumpFile(filename, text):
	"""Dump a string to a file."""
	print "Writing to %s" % filename
	file = open(filename, "wt")
	file.write(text)
	file.close()

def writeTemplate(template, output, data):
	filled = readFile(template) % data
	dumpFile(output, filled)




def sqlStatement(statement):
	# hack!
	runCommand("echo %s | mysql -u root" % shellEscape(statement))

def sqlFile(dbname, filename):
	runCommand("mysql -u root %s <%s" % shellEscape((dbname, filename)))

def makeDatabase(dbname):
	sqlStatement("DROP DATABASE IF EXISTS %s; CREATE DATABASE %s;" % (dbname, dbname))

def dropDatabase(dbname):
	sqlStatement("DROP DATABASE %s;" % dbname)

def runPhp(dir, filename, options=""):
	runCommand("cd %s && php %s %s" % (
		shellEscape(dir),
		shellEscape(filename),
		options))

def makeConfig(dbname, branch):
	data = {
		"dbname": dbname,
		"dbuser": "root",
		"dbpassword" : ""}
	writeTemplate("LocalSettings.in", branch + "/phase3/LocalSettings.php", data)
	writeTemplate("AdminSettings.in", branch + "/phase3/AdminSettings.php", data)

def clearConfig(branch):
	os.remove(branch + "/phase3/LocalSettings.php")
	os.remove(branch + "/phase3/AdminSettings.php")

def runUpdater(dbname, branch):
	makeConfig(dbname, branch)
	if branch == "branches/REL1_2" or branch == "branches/REL1_3":
		# These old versions didn't have update.php.
		# Run rebuildMessages directly
		runPhp(branch + "/phase3", "maintenance/rebuildMessages.php", "<../../../rebuildMessages.in")
	else:
		runPhp(branch + "/phase3", "maintenance/update.php", "--quick")
	#clearConfig("trunk")


def svnCheckout(branch):
	if os.path.exists(branch):
		runCommand("cd %s/phase3 && svn up -q" % branch)
	else:
		runCommand("svn co -q http://svn.wikimedia.org/svnroot/mediawiki/%s/phase3 %s/phase3" % (branch, branch))


def testUpgrade(dbname, branch):
	"""Try building an empty database for the given version, then upgrading."""
	svnCheckout("trunk")
	svnCheckout(branch)
	
	# Initialise database
	makeDatabase(dbname)
	sqlFile(dbname, branch + "/phase3/maintenance/tables.sql")
	if os.path.exists(branch + "/phase3/maintenance/indexes.sql"):
		sqlFile(dbname, branch + "/phase3/maintenance/indexes.sql")
	
	# Normalise some content in it
	runUpdater(dbname, branch)
	
	# Try the upgrade
	runUpdater(dbname, "trunk")
	
	#dropDatabase(dbname)

testUpgrade("uptest113", "branches/REL1_13")
testUpgrade("uptest112", "branches/REL1_12")
testUpgrade("uptest111", "branches/REL1_11")
testUpgrade("uptest110", "branches/REL1_10")
testUpgrade("uptest19", "branches/REL1_9")
testUpgrade("uptest18", "branches/REL1_8")
testUpgrade("uptest17", "branches/REL1_7")
testUpgrade("uptest16", "branches/REL1_6")
testUpgrade("uptest15", "branches/REL1_5")
testUpgrade("uptest14", "branches/REL1_4")
testUpgrade("uptest13", "branches/REL1_3")
testUpgrade("uptest12", "branches/REL1_2")
