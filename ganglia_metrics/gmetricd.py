#! /usr/bin/env python

from xdrlib import Packer
import sys, socket, re, GangliaMetrics, DiskStats, MySQLStats, time, os, signal, pwd, logging
import StringIO, ConfigParser
from SelectServer import *

# Configuration

configParser = ConfigParser.ConfigParser( {
	'gmondconf': '/etc/gmond.conf',
	'sock': '/tmp/gmetric.sock',
	'log': '/var/log/gmetricd/gmetricd.log',
	'pid': '/var/run/gmetricd.pid',
	'user': 'gmetric',
	'dbuser': '',
	'dbpassword': '',
	'mysqlclient': 'mysql',
} )

try:
	configFile = open('/etc/gmetricd.conf')
except:
	configFile = False
if configFile:
	configData = "[DEFAULT]\n"
	configData += configFile.read()
	configFile.close()
	configParser.readfp(StringIO.StringIO(configData))

conf = {}
for name, value in configParser.items('DEFAULT'):
	conf[name] = value

unixSocket = None

class GmetricListenSocket(ListenSocket):
	def makeReader(self, sock, server):
		return GmetricConnection(sock, server)

class GmetricConnection(LineServerConnection):
	def __init__(self, parentSocket, server):
		LineServerConnection.__init__(self, parentSocket, server)
		self.setFloatRegex = re.compile(r"^setfloat ([\w]+) ([0-9.E+\-]+)$", re.IGNORECASE)
	
	def onLine(self, line):
		global pushMetrics
		m = self.setFloatRegex.match(line)
		if (m is None):
			try: self.parentSocket.send("error\n")
			except socket.error: pass
			return
		
		name = m.group(1)
		value = m.group(2)
		try: 
			value = float(value) 
		except ValueError:
			try: self.parentSocket.send("error\n")
			except socket.error: pass
			return
		if name not in pushMetrics:
			pushMetrics.add(GangliaMetrics.PushMetric(name))
		
		pushMetrics.metrics[name].set(value)
		self.parentSocket.send("ok\n")

def termHandler(sig, frame):
	global unixSocket, conf
	try: 
		if unixSocket: 
			unixSocket.close()
	except: pass
	try: os.unlink(conf['sock'])
	except: pass
	try: 
		logger = logging.getLogger('gmetricd')
		logger.info('Received TERM signal, exiting')
	except: pass
	os._exit(0)

# Determine user to run as
if conf['user']:
	try:
		userPwd = pwd.getpwnam(conf['user'])
	except KeyError:
		sys.stderr.write("User \"%s\" does not exist, exiting\n" % conf['user'])
		sys.exit(1)

	userId = userPwd.pw_uid
	groupId = userPwd.pw_gid

# Create log directory
logDir = os.path.dirname(conf['log'])
if not os.path.exists(logDir):
	os.mkdir(logDir)
	os.chown(logDir, userId, groupId)

# Open the PID file
pidFile = open(conf['pid'], 'w+')
previousPid = pidFile.read().strip()
if previousPid != '':
	try: previousPid = int(previousPid)
	except: previousPid = 0
else:
	previousPid = 0

# Is it still running?
if previousPid:
	try:
		cmdLineFile = open('/proc/%d/cmdline' % previousPid, 'r')
		cmdLine = cmdLineFile.read()
		previousArgv = cmdLine.split('\0', 1)
		if len(previousArgv) > 0 and previousArgv[0] == sys.argv[0]:
			sys.stderr.write("gmetricd is already running, with PID %d\n" % previousPid)
			sys.exit(1)
	except: pass

# Switch to unprivileged user
os.setuid(userId)

selectServer = SelectServer()

# Determine the multicast address
gmondFile = open(conf['gmondconf'])
addrRegex = re.compile(r"^\s*mcast_join\s*=\s*([0-9.:]+)")
portRegex = re.compile(r"^\s*port\s*=\s*([0-9]+)")
ttlRegex  = re.compile(r"^\s*ttl\s*=\s*([0-9]+)")
addr = None
port = None
ttl = 1
for line in gmondFile:
	m = addrRegex.match(line)
	if m != None:
		addr = m.group(1)
		continue
	
	m = portRegex.match(line)
	if m != None:
		port = m.group(1)
		continue
	
	m = ttlRegex.match(line)
	if m != None:
		ttl = m.group(1)
		continue
	
gmondFile.close()

if addr == None or port == None:
	sys.stderr.write("Unable to determine multicast address\n")
	sys.exit(1)

# Configure the logger
logger = logging.getLogger('gmetricd')
handler = logging.FileHandler(conf['log'])
handler.setFormatter(logging.Formatter('%(asctime)s  %(message)s', '%Y-%m-%d %H:%M:%S'))
logger.addHandler(handler)
logger.setLevel(logging.INFO)
logger = logging.getLogger('GangliaMetrics')
logger.addHandler(handler)
logger.setLevel(logging.INFO)

# Create a socket for metric transmission
transmitSocket = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
transmitSocket.setsockopt(socket.IPPROTO_IP, socket.IP_MULTICAST_TTL, int(ttl))
transmitAddress = (addr, int(port))

# Create unix socket for volatile push metrics (e.g. HTTP request time)
try: os.unlink(conf['sock'])
except: pass
unixSocket = GmetricListenSocket(socket.AF_UNIX)
unixSocket.bind(conf['sock'])
os.chmod(conf['sock'], 0777)
unixSocket.listen(10)

selectServer.addReader(unixSocket)

# Create the metrics
diskStats = DiskStats.DiskStats()
pushMetrics = GangliaMetrics.MetricCollection()

mysqlStats = MySQLStats.MySQLStats( conf['dbuser'], conf['dbpassword'] )
allMetrics = (diskStats, pushMetrics, mysqlStats)

# Daemonize
pid = os.fork()
if pid != 0:
	# Write PID
	pidFile.seek(0)
	pidFile.truncate(0)
	pidFile.write("%s\n" % pid)
	pidFile.close()
	sys.exit(0)
pidFile.close()

logger.info('gmetricd started, PID = %d' % os.getpid())
try:
	os.chdir('/')
	sys.stdin.close()
	sys.stdout.close()
	sys.stderr.close()
	os.close(0)
	os.close(1)
	os.close(2)

	os.setsid()
	signal.signal(signal.SIGTERM, termHandler)

	# Tick length in seconds
	# We sleep for this long before doing metric operations
	tick = 10
	currentTime = lastTime = time.time()
except:
	logger.exception('Exception on startup: ')
	sys.exit(1)

except_count = 0
iter = 0
while 1:
	try:
		iter += 1
		if iter > 1000:
			iter = 0
			except_count = 0
		
		done = False
		while not done:
			currentTime = time.time()
			elapsed = currentTime - lastTime
			remaining = tick - elapsed
			if remaining > tick:
				# Clock set back?
				remaining = 0
		
			if remaining > 0:
				selectServer.select(remaining)
			else:
				done = True
		
		lastTime = currentTime

		# Process metrics
		for metricSequence in allMetrics:
			for metric in metricSequence:
				if metric.isReady():
					metric.send(transmitSocket, transmitAddress)
	except:
		except_count += 1
		if except_count > 100:
			logger.critical('Too many exceptions, stopping')
			sys.exit(1)
		else:
			logger.exception('Exception: ')
