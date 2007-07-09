from xdrlib import Packer
import time, re, sys, logging

""" Metric base class """

class Metric(object):
	
	def __init__(self, name, units = '', type = 'double'):
		self.name = name
		self.units = units
		self.type = type
		self.lastSendTime = 0

		self.slope = 'both'
		self.tmax = 60
		self.dmax = 0
		self.interval = 10

		self.value = 0
	
	def isReady(self):
		return time.time() - self.lastSendTime >= self.interval
	
	def send(self, sock, address):
		value = self.getValue()
		if value != None:
			packer = Packer()
			packer.pack_enum(0) # metric_user_defined
			packer.pack_string(self.type)
			packer.pack_string(self.name)
			packer.pack_string(str(value))
			packer.pack_string(self.units)
			if self.slope == 'zero':
				slope = 0
			else:
				slope = 3
			packer.pack_uint(slope)
			packer.pack_uint(self.tmax)
			packer.pack_uint(self.dmax)

			sock.sendto(packer.get_buffer(), address)
			self.lastSendTime = time.time()
	
	def getValue(self):
		return self.value

	def set(self, value):
		self.value = value

"""
A metric which works by querying a system counter. The counter typically 
increases monotonically, but may occasionally overflow. The difference 
between consecutive values is calculated, the result is a count per second. 
"""
class DeltaMetric(Metric):
	def __init__(self, name, units = '', type = 'double'):
		Metric.__init__(self, name, units, type)
		self.lastCounterValue = 0
		self.lastRefTime = None
		self.lastElapsed = None
	
	def getValue(self):
		counter, refTime, divideBy = self.getCounterValue()

		if self.lastRefTime is None:
			# Initial value
			value = None
		else:
			elapsed = refTime - self.lastRefTime
			self.lastElapsed = elapsed
			if elapsed == 0:
				# Time elapsed is too short
				value = None
			elif counter >= self.lastCounterValue:
				# Normal increment
				value = float(counter - self.lastCounterValue) / float(elapsed) / divideBy
			elif self.lastCounterValue > (1L << 32):
				# Assume 64-bit counter overflow
				value = float(counter + (1L<<64) - self.lastCounterValue) / float(elapsed) / divideBy
			else:
				# Assume 32-bit counter overflow
				value = float(counter + (1L<<32) - self.lastCounterValue) / float(elapsed) / divideBy
		
		self.lastRefTime = refTime
		self.lastCounterValue = counter
		return value

	def getCounterValue(self):
		raise NotImplementedError

"""
A rolling average metric
"""
class RollingMetric(Metric):
	def __init__(self, name, avPeriod = 60, units = '', type = 'double'):
		Metric.__init__(self, name, units, type)
		self.queue = []
		self.sum = 0
		self.targetSize = avPeriod / self.interval
		self.head = 0

	def getValue(self):
		if len(self.queue) == 0:
			return None
		else:
			return float(self.sum) / len(self.queue)

	def set(self, value):
		if value == None:
			self.queue = []
			return

		self.sum += value
		if len(self.queue) == self.targetSize:
			self.head = (self.head + 1) % self.targetSize
			self.sum -= self.queue[self.head]
			self.queue[self.head] = value
		else:
			self.queue.append(value)


"""
A metric which averages pushed values over the polling period
If no value is pushed during a given polling interval, the previous average is returned
"""
class PushMetric(Metric):
	def __init__(self, name, units = '', type = 'double'):
		Metric.__init__(self, name, units, type)
		self.lastAv = None
		self.sum = 0
		self.count = 0
	
	def set(self, value):
		self.sum += value
		self.count += 1
	
	def getValue(self):
		if self.count == 0:
			return self.lastAv
		else:
			self.lastAv = self.sum / self.count
			self.sum = 0
			self.count = 0
			return self.lastAv

"""
Simple delta metric class intended for use in metric collections
"""
class DeltaMetricItem(DeltaMetric):
	value = 0
	refTime = 0
	divideBy = 1

	def getCounterValue(self):
		return (self.value, self.refTime, self.divideBy)

	def set(self, value, refTime, divideBy = 1):
		self.value = value
		self.refTime = refTime
		self.divideBy = divideBy

"""
Metric collection base class
"""
class MetricCollection(object):
	def __init__(self):
		self.metrics = {}

	def __iter__(self):
		if self.update():
			return self.metrics.values().__iter__()
		else:
			return [].__iter__()
	
	def update(self):
		return True
	
	def add(self, metric):
		self.metrics[metric.name] = metric

"""
Utilisation metric for DiskStats
"""
class DiskUtilItem(DeltaMetricItem):
	def __init__(self, name):
		DeltaMetricItem.__init__(self, name, '%')
	
	def getValue(self):
		# Get the time spent doing I/O, in milliseconds per second
		value = DeltaMetricItem.getValue(self)
		if self.lastElapsed and value != None:
			# Convert to a percentage of the elapsed time
			return value / 10
		else:
			return None

"""
Load metric for DiskStats
"""
class DiskLoadItem(DeltaMetricItem):
	def __init__(self, name):
		DeltaMetricItem.__init__(self, name)
	
	def getValue(self):
		# Get the time spent doing I/O, in milliseconds per second
		value = DeltaMetricItem.getValue(self)
		if self.lastElapsed and value != None:
			# Convert to a plain ratio
			return value / 1000
		else:
			return None

"""
Statistics from /proc/diskstats
Requires Linux 2.6+
"""
class DiskStats(MetricCollection):
	# Field indexes
	BLANK_INITIAL_SPACE = 0
	MAJOR = 1
	MINOR = 2
	NAME = 3
	READS = 4
	READS_MERGED = 5
	SECTORS_READ = 6
	MS_READING = 7
	WRITES = 8
	WRITES_MERGED = 9
	SECTORS_WRITTEN = 10
	MS_WRITING = 11
	REQS_PENDING = 12
	MS_TOTAL = 13
	MS_WEIGHTED = 14
	
	def __init__(self):
		self.metrics = {
			'diskio_read_bytes':    DeltaMetricItem('diskio_read_bytes', 'bytes/sec'),
			'diskio_write_bytes':   DeltaMetricItem('diskio_write_bytes', 'bytes/sec'),
			'diskio_read_load':     DiskLoadItem('diskio_read_load'),
			'diskio_write_load':    DiskLoadItem('diskio_write_load'),
			'diskio_total_load':    DiskLoadItem('diskio_total_load'),
			'diskio_util':          DiskUtilItem('diskio_util')
		}
		self.delimiterRegex = re.compile(r"\s+")
		self.deviceRegex = re.compile(r"^[sh]d[a-z]$")
		self.disabled = False

	def update(self):
		if self.disabled:
			return False
		
		try:
			procfile = open('/proc/diskstats', 'r')
		except IOError:
			type, value = sys.exc_info()[:2]
			logger = logging.getLogger('GangliaMetrics')
			logger.warning("Unable to open /proc/diskstats: %s\n" % value)
			self.disabled = True
			return False
		
		contents = procfile.read(100000)
		refTime = time.time()
		procfile.close()
		lines = contents.splitlines()

		devCount = 0
		sums = None
		for line in lines:
			fields = self.delimiterRegex.split(line)
			if self.deviceRegex.search(fields[self.NAME]) == None or \
			len(fields) < self.MS_WEIGHTED or \
			fields[self.READS] == 0:
				continue
			
			if sums == None:
				sums = [0] * len(fields)

			# Sum the summable stats
			for i in xrange(len(fields)):
				if fields[i].isdigit():
					sums[i] += long(fields[i])
			devCount += 1
		
		# Put the summed stats into metrics
		if devCount:
			# The sector size in this case is hard-coded in the kernel as 512 bytes
			# There doesn't appear to be any simple way to retrieve that figure
			self.metrics['diskio_read_bytes'].set(sums[self.SECTORS_READ] * 512, refTime)
			self.metrics['diskio_write_bytes'].set(sums[self.SECTORS_WRITTEN] * 512, refTime)
			
			self.metrics['diskio_read_load'].set(sums[self.MS_READING], refTime, devCount)
			self.metrics['diskio_write_load'].set(sums[self.MS_WRITING], refTime, devCount)
			self.metrics['diskio_total_load'].set(sums[self.MS_WEIGHTED], refTime, devCount)
			self.metrics['diskio_util'].set(sums[self.MS_TOTAL], refTime, devCount)

		return devCount != 0


