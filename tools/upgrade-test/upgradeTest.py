import sys, os, stat, re
from ConfigParser import SafeConfigParser
from subprocess import *
from xml.dom.minidom import *
from xml.dom import *

# Configure by creating local.conf in the release-tools directory and overriding 
# the entries in default.conf

class UpgradeError(Exception): pass

def versionCompare( v1, v2 ):
	parts1 = v1.split( '.' )
	parts2 = v2.split( '.' )
	numParts = max((len(parts1), len(parts2)))
	for i in range(numParts):
		if i >= len(parts1):
			part1 = 0
		else:
			part1 = int(parts1[i])
		if i >= len(parts2):
			part2 = 0
		else:
			part2 = int(parts2[i])

		if part1 < part2:
			return -1
		if part1 > part2:
			return 1
	return 0

class UpgradeTest:
	def __init__( self ):
		self.conf = {}
		self.versionConf = {}
		self.baseDir = os.path.dirname( os.path.abspath( sys.argv[0] ) )
		self.currentVersion = None

	def run( self ):
		if len(sys.argv) <= 1:
			sys.stderr.write( "Usage: python upgradeTest.py <version>\n" )
			sys.exit( 1 )

		# Configure
		settings = ['php', 'svnroot', 'dbUser', 'dbPassword', 'runUpdate', 
				'runRebuildMessages', 'runOldUpdate']
		booleans = ['runUpdate', 'runRebuildMessages', 'runOldUpdate']
		confParser = SafeConfigParser()
		confParser.read( [ self.baseDir + '/default.conf', self.baseDir + '/local.conf' ] )
		for key in settings:
			if key in booleans:
				value = confParser.getboolean( 'main', key )
			else:
				value = confParser.get( 'main', key )
			self.conf[key] = value

		for version in confParser.sections():
			self.versionConf[version] = {}
			for key in settings:
				if confParser.has_option( version, key ):
					if key in booleans:
						value = confParser.getboolean( version, key )
					else:
						value = confParser.get( version, key )
					self.versionConf[version][key] = value


		# Initialise file structure
		self.dumpFile( 'mysql-client.conf', """
[client]
user = %s
password = %s
""" % (self.conf['dbUser'], self.conf['dbPassword']) )

		os.chmod(self.baseDir + '/mysql-client.conf', stat.S_IRUSR | stat.S_IWUSR)

		if not os.path.exists( self.baseDir + '/schemas' ):
			os.mkdir( self.baseDir + '/schemas' )
		for path in os.listdir( self.baseDir + '/schemas' ):
			os.unlink( self.baseDir + '/schemas/' + path )

		# Execute tests
		targetVersion = sys.argv[1];

		versions = self.getAllVersions()
		prevVersion = None
		for version in versions:
			self.currentVersion = version
			if versionCompare( version, targetVersion ) >= 0:
				break
			
			self.testUpgrade( version, targetVersion )
			self.dumpSchema( version )
			if prevVersion != None:
				self.compareSchemas( prevVersion, version )
			prevVersion = version

	def getConf( self, key ):
		v = self.currentVersion
		if v != None and ( v in self.versionConf ) and ( key in self.versionConf[v] ):
			return self.versionConf[v][key]
		elif key in self.conf:
			return self.conf[key]
		else:
			return None

	def getAllVersions( self ):
		print "Getting version list"
		versions = []
		proc = Popen( ['svn', 'ls', '--xml', self.conf['svnroot'] + '/branches'], stdout=PIPE )
		xml = proc.communicate()[0]
		if proc.returncode:
			raise UpgradeError( 'svn ls returned exit status ' + proc.returncode )

		doc = parseString(xml)
		entries = doc.getElementsByTagName( 'entry' )
		for entry in entries:
			name = entry.firstChild
			while name != None and ( name.nodeType != Node.ELEMENT_NODE or name.tagName != 'name' ):
				name = name.nextSibling
			if name == None or name.firstChild.nodeType != Node.TEXT_NODE:
				raise UpgradeError( 'Invalid response from svn ls' )
			nameText = name.firstChild.data
			if re.match( '^REL[0-9]+_[0-9]+$', nameText ):
				version = self.branchToVersion( 'branches/' + nameText )
				if versionCompare( version, '1.2' ) >= 0:
					versions.append( version )
		versions.sort( versionCompare )
		print "OK: " + ', '.join( versions )
		return versions

	def versionToDB( self, version ):
		return 'uptest' + version.replace('.', '_')

	def versionToBranch( self, version ):
		return 'branches/REL' + version.replace( '.', '_' )

	def branchToVersion( self, branch ):
		return branch.replace( 'branches/REL', '').replace( '_', '.' )

	def dumpSchema( self, version ):
		self.runCommand(
			[
				'mysqldump', '--defaults-file=' + self.baseDir + '/mysql-client.conf', 
				'--no-data', self.versionToDB( version )
			], 
			stdout = open( self.baseDir + '/schemas/' + version + '.sql', 'w' ))

	def compareSchemas( self, v1, v2 ):
		self.runCommand(
			[ 
				'diff', '-u', 
				v1 + '.sql', 
				v2 + '.sql'
			],
			cwd = self.baseDir + '/schemas',
			stdout = open( self.baseDir + '/schemas/diffs', 'a' ),
			)

	def runCommand( self, args, **options ):
		print ' '.join( args )
		sys.stdout.flush() # misordering of output observed without this
		retval = Popen(args, stderr = sys.stderr, **options).wait()
		if retval and args[0] != 'diff':
			raise UpgradeError("Nonzero return code (%d)! Aborting." % retval)
		return retval

	def readFile( self, filename ):
		file = open( self.baseDir + '/' + filename, "r" )
		text = file.read()
		file.close()
		return text

	def dumpFile( self, filename, text ):
		"""Dump a string to a file."""
		print "Writing to %s" % filename
		file = open( self.baseDir + '/' + filename, "wt" )
		file.write(text)
		file.close()

	def writeTemplate( self, template, output, data ):
		filled = self.readFile(template) % data
		self.dumpFile(output, filled)

	def sqlStatement( self, statement ):
		self.runCommand(
			[ 'mysql', '--defaults-file=' + self.baseDir + '/mysql-client.conf', '-e', statement ],
			stdout = sys.stdout)

	def sqlFile( self, dbname, filename ):
		self.runCommand(
			[ 'mysql', '--defaults-file=' + self.baseDir + '/mysql-client.conf', dbname ],
			stdin = open( self.baseDir + '/' + filename, 'r' ),
			stdout = sys.stdout)

	def makeDatabase( self, dbname ):
		self.sqlStatement("DROP DATABASE IF EXISTS %s; CREATE DATABASE %s;" % (dbname, dbname))

	def dropDatabase( self, dbname ):
		self.sqlStatement("DROP DATABASE %s;" % dbname)

	def runPhp( self, dir, filename, *moreArgs, **kw):
		args = self.getConf( 'php' ).split( ' ' )
		args.append( filename )
		if len( moreArgs ):
			args.extend( moreArgs )
		fullDir = self.baseDir + '/' + dir
		if not os.path.exists( fullDir + '/' + filename ):
			raise UpgradeError( "File not found: " + fullDir + '/' + filename )

		self.runCommand( args, cwd = fullDir, stdout = sys.stdout, **kw)

	def makeConfig( self, version, dbname ):
		data = {
			"dbName": dbname,
			"dbUser": self.getConf( 'dbUser' ),
			"dbPassword" : self.getConf( 'dbPassword' ) }
		branch = self.versionToBranch( version )
		self.writeTemplate("LocalSettings.in", branch + "/phase3/LocalSettings.php", data)
		self.writeTemplate("AdminSettings.in", branch + "/phase3/AdminSettings.php", data)

	def clearConfig( self, version ):
		branch = self.versionToBranch( version )
		os.remove(self.baseDir + '/' + branch + "/phase3/LocalSettings.php")
		os.remove(self.baseDir + '/' + branch + "/phase3/AdminSettings.php")

	def runUpdater( self, version, dbname ):
		self.makeConfig( version, dbname )
		branch = self.versionToBranch( version )
		self.currentVersion = version
		if ( self.getConf( 'runUpdate' ) ):
			self.runPhp(
				branch + "/phase3/maintenance", 
				"update.php", "--quick")

		if ( self.getConf( 'runRebuildMessages' ) ):
			self.runPhp(
				branch + "/phase3/maintenance", 
				"rebuildMessages.php", 
				stdin = open( self.baseDir + '/' + "rebuildMessages.in" ) )

	def svnCheckout( self, version ):
		branch = self.versionToBranch( version )
		if os.path.exists( self.baseDir + '/' + branch ):
			self.runCommand(
				[ 'svn', 'up', '-q'], 
				cwd = self.baseDir + '/' + branch + '/phase3',
				stdout = sys.stdout )
		else:
			self.runCommand(
				[ 
					'svn', 'co', '-q', self.getConf( 'svnroot' ) + '/' + branch + '/phase3',
					branch + '/phase3'
				],
				cwd = self.baseDir,
				stdout = sys.stdout )


	def testUpgrade(self, sourceVersion, destVersion):
		"""Try building an empty database for the given version, then upgrading."""
		print "\n\nTesting " + sourceVersion + "\n--------------------------------------------------------"
		self.svnCheckout(destVersion)
		self.svnCheckout(sourceVersion)
		
		# Initialise database
		dbname = self.versionToDB( sourceVersion )
		sourceBranch = self.versionToBranch( sourceVersion )
		destBranch = self.versionToBranch( destVersion )
		self.makeDatabase( dbname )
		self.sqlFile( dbname, sourceBranch + "/phase3/maintenance/tables.sql" )
		if os.path.exists( self.baseDir + '/' + sourceBranch + "/phase3/maintenance/indexes.sql"):
			self.sqlFile(dbname, sourceBranch + "/phase3/maintenance/indexes.sql")
		
		# Normalise some content in it
		if self.getConf( 'runOldUpdate' ):
			self.runUpdater(sourceVersion, dbname)
		
		# Try the upgrade
		self.runUpdater(destVersion, dbname)


tester = UpgradeTest()
tester.run()
