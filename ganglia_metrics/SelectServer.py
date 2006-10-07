import select, errno, socket

class SelectServer(object):
	def __init__(self):
		self.readers = []
		self.writers = []
		self.exceptionals = []
	
	def addReader(self, obj):
		self.readers.append(obj)
	
	def addWriter(self, obj):
		self.writers.append(obj)
	
	def addExceptional(self, obj):
		self.exceptionals.append(obj)
	
	def removeReader(self, obj):
		self.remove(self.readers, obj)
	
	def removeWriter(self, obj):
		self.remove(self.writers, obj)
	
	def removeExceptional(self, obj):
		self.remove(self.exceptionals, obj)
	
	def remove(self, list, obj):
		i = list.index(obj)
		del list[i]
	
	def select(self, timeout):
		readyTriple = select.select(self.readers, self.writers, self.exceptionals, timeout)
		for readyList in readyTriple:
			for readySocket in readyList:
				readySocket.onSelect(self)

class ListenSocket(socket.socket):
	def onSelect(self, server):
		sock, addr = self.accept()
		server.addReader(self.makeReader(sock, server))

	def makeReader(self, sock, server):
		raise NotImplementedError

class LineServerConnection(object):
	allowedErrors = (
		errno.EWOULDBLOCK, 
		errno.EAGAIN, 
		errno.EINTR)
	
	def __init__(self, parentSocket, server):
		self.parentSocket = parentSocket
		self.parentSocket.setblocking(0)
		self.file = parentSocket.makefile()
		self.server = server
		self.buffer = ''
	
	def fileno(self):
		return self.parentSocket.fileno()

	def onEof(self):
		self.parentSocket.close()
		self.server.removeReader(self)

	def onSelect(self, server):
		currentRead = ''
		again = False
		try:
			currentRead = self.parentSocket.recv(1048576)
		except socket.error:
			type, value = sys.exc_info()[:2]
			if not isinstance(value, tuple):
				raise
			if value[1] not in self.allowedErrors:
				raise
			again = True

		if again:
			pass
		elif currentRead == '':
			# EOF
			self.onEof()
		else:
			self.buffer += currentRead
			# Have we got a whole line?
			p = self.buffer.find("\n")
			while p != -1:
				if p > 0 and self.buffer[p-1] == "\r":
					self.onLine(self.buffer[0:p-1])
				else:
					self.onLine(self.buffer[0:p])
				self.buffer = self.buffer[p+1:]
				p = self.buffer.find("\n")
	
	def onLine(self, line):
		raise NotImplementedError

