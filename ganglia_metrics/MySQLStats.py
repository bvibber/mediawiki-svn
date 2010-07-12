import logging, subprocess, time, pprint
from GangliaMetrics import *
from xml.dom.minidom import parseString

"""
A collection of metrics from MySQL, using SHOW STATUS and SHOW PROCESSLIST
"""
class MySQLStats(MetricCollection):
	def __init__(self, user, password):
		self.metrics = {
			'mysql_questions': DeltaMetricItem(
				'mysql_questions', 
				{
					'TITLE': 'MySQL queries',
					'DESC': 'Queries per second received at this MySQL server',
					'GROUP': 'mysql'
				},
				'q/s'),
			'mysql_threads_connected': RollingMetric(
				'mysql_threads_connected',
				{
					'TITLE': 'MySQL threads connected',
					'DESC': 'Number of threads connected to this MySQL server',
					'GROUP': 'mysql'
				},
				60),
			'mysql_threads_running': RollingMetric(
				'mysql_threads_running', 
				{
					'TITLE': 'MySQL threads running',
					'DESC': 'Number of MySQL threads in a non-sleep state',
					'GROUP': 'mysql'
				},
				60)
		}
		self.user = user
		self.password = password

		if self.query('select 1') == None:
			self.disabled = True
			logger = logging.getLogger('GangliaMetrics')
			logger.warning('Unable to run query, disabling MySQL statistics')
		else:
			self.disabled = False
			lag = self.getLag()
			if lag != None:
				self.addLagMetric()

	def addLagMetric(self):
		self.metrics['mysql_slave_lag'] = Metric(
			'mysql_slave_lag', 
			{
				'TITLE': 'MySQL slave lag',
				'DESC': 'MySQL slave lag in seconds (may be zero if replication is broken)',
				'GROUP': 'mysql'
			},
			's') 

	def update(self):
		if self.disabled:
			return False

		refTime = time.time()
		status = self.showStatus()
		if status:
			self.metrics['mysql_questions'].set(int(status['Questions']), refTime)
			self.metrics['mysql_threads_connected'].set(int(status['Threads_connected']))
			self.metrics['mysql_threads_running'].set(int(status['Threads_running']))

		lag = self.getLag()
		if lag != None:
			if 'mysql_slave_lag' not in self.metrics:
				self.addLagMetric()
			self.metrics['mysql_slave_lag'].set(int(lag))

		return True

	def query(self, sql):
		global conf
		proc = subprocess.Popen(
			[
				conf['mysqlclient'], '-XB', 
				'--user=' + self.user,
				'--password=' + self.password,
				'-e', sql
			],
			stdout = subprocess.PIPE,
			stderr = subprocess.PIPE )
		(out, stderr) = proc.communicate()
		if proc.returncode:
			logger = logging.getLogger('GangliaMetrics')
			logger.warning("SQL error: " + stderr.rstrip())
			return None

		try:
			dom = parseString(out)
		except:
			logger = logging.getLogger('GangliaMetrics')
			logger.warning("SQL error: Unable to parse XML result")
			return None
		return dom

	def markDown(self):
		self.metrics['mysql_questions'].set(None, None)
		self.metrics['mysql_threads_connected'].set(None)
		self.metrics['mysql_threads_running'].set(None)
		self.metrics['mysql_slave_lag'].set(None)

	def showStatus(self):
		result = self.query("SHOW /*!50002 GLOBAL */ STATUS")
		if not result:
			return None

		resultHash = {}
		for row in result.documentElement.getElementsByTagName('row'):
			name = None
			value = None
			for field in row.childNodes:
				if field.nodeName != 'field' or field.firstChild == None:
					continue
				if field.getAttribute('name') == 'Variable_name':
					name = field.firstChild.data
				elif field.getAttribute('name') == 'Value':
					value = field.firstChild.data
			if name != None and value != None:
				resultHash[name] = value
		return resultHash

	def getLag(self):
		result = self.query("SHOW SLAVE STATUS")
		if not result:
			return None

		fields = result.documentElement.getElementsByTagName('field')
		if not fields.length:
			return None

		for field in fields:
			if field.getAttribute('name') == 'Seconds_Behind_Master' and field.firstChild:
				return field.firstChild.data

		return None

