import logging, commands, time
from GangliaMetrics import *
from xml.dom.minidom import parseString

"""
A collection of metrics from MySQL, using SHOW STATUS and SHOW PROCESSLIST
"""
class MySQLStats(MetricCollection):
	def __init__(self, user, password):
		self.metrics = {
			'mysql_questions':          DeltaMetricItem('mysql_questions', 'q/s'),
			'mysql_threads_connected':  RollingMetric('mysql_threads_connected', 60),
			'mysql_threads_running':    RollingMetric('mysql_threads_running', 60),
			'mysql_slave_lag':          Metric('mysql_slave_lag', 's') 
		}
		self.user = user
		self.password = password
		self.pipes = None

		if self.query('select 1') == None:
			self.disabled = True
		else:
			self.disabled = False
			logger = logging.getLogger('GangliaMetrics')
			logger.warning('Unable to run query, disabling MySQL statistics')

	def update(self):
		if disabled:
			return False

		refTime = time.time()
		status = self.showStatus()
		if not status:
			self.markDown()
			return False

		lag = self.getLag()

		self.metrics['mysql_questions'].set(int(status['Questions']), refTime)
		self.metrics['mysql_threads_connected'].set(int(status['Threads_connected']))
		self.metrics['mysql_threads_running'].set(int(status['Threads_running']))
		self.metrics['mysql_slave_lag'].set(float(lag)) # float = wishful thinking
		return True

	def escapeshellarg(self, s):
		return s.replace( "\\", "\\\\").replace( "'", "'\\''")

	def query(self, sql):
		out = commands.getoutput("mysql -XB -u '%s' -p'%s' -e '%s'" % (
			self.escapeshellarg(self.user), 
			self.escapeshellarg(self.password),
			self.escapeshellarg(sql)
			))
		try:
			dom = parseString(out)
		except:
			logger = logging.getLogger('GangliaMetrics')
			logger.warning("SQL error: Unable to parse XML result\n")
			return None
		return dom

	def markDown(self):
		self.metrics['mysql_questions'].set(None, None)
		self.metrics['mysql_threads_connected'].set(None)
		self.metrics['mysql_threads_running'].set(None)
		self.metrics['mysql_slave_lag'].set(None)
		self.conn = None

	def showStatus(self):
		result = self.query("SHOW STATUS")
		if not result:
			return None

		resultHash = {}
		for row in result.documentElement.getElementsByTagName('row'):
			name = row.getElementsByTagName('Variable_name')[0].childNodes[0].data
			value = row.getElementsByTagName('Value')[0].childNodes[0].data
			resultHash[name] = value
		return resultHash

	def getLag(self):
		result = self.query("SHOW PROCESSLIST")
		if not result:
			return None

		for row in result.documentElement.getElementsByTagName('row'):
			user = row.getElementsByTagName('User')[0].childNodes[0].data
			time = row.getElementsByTagName('Time')[0].childNodes[0].data
			state = row.getElementsByTagName('State')[0].childNodes[0].data
			if user == 'system user' and \
				state != 'Waiting for master to send event' and \
				state != 'Connecting to master' and \
				state != 'Queueing master event to the relay log' and \
				state != 'Waiting for master update' and \
				state != 'Requesting binlog dump':
					return time
		return None
		
