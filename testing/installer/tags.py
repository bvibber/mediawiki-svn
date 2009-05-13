import sys,os
import os.path
import settings_handler as settings
import copy
import cPickle as pickle
import subprocess

class TagsException(Exception):
	pass

class Tags:
	"""keep track of extension tags in svn.
	the current repository is not optimised for the kinds
	of queries the installer needs. We do the query once 
	and cache it."""

	def __init__(self):
		# try to load preexisting cache
		self.cache=self.load_cache()
		# No joy? Let's generate one.
		if not self.cache:
			self.cache=self.update_cache()
		# Still no joy? 
		if not self.cache:
			raise Exception("Internal error: cannot obtain a tag cache")

	@classmethod
	def load_cache(self):
		"""load tags from cache"""
		if not os.path.isfile(settings.tagcache):
			return None

		cache=pickle.load(file(settings.tagcache))
		return cache

	@classmethod
	def svnlist(self,dir):
		"""generic obtain data from svn ls
			(TODO: refactor, overlaps with DownloadInstaller get_installers)"""
		
		command=['svn','ls',dir]
		process=subprocess.Popen(command,stdout=subprocess.PIPE, stderr=subprocess.PIPE)
		failp=process.stderr.read()
		if failp:
			return None
		l=list(process.stdout)
		# tidy l in place
		for i in range(len(l)):
			l[i]=l[i].strip()
			if l[i].endswith("/"):
				l[i]=l[i][:-1]
		return l

	def update_cache(self):
		"""update cache to disk and to memory"""
		self.cache=self.update_cache_file()
		return self.cache

	@classmethod
	def update_cache_file(self):
		"""update cache to disk. returns a cache. This is an expensive operation.
		   (cache format is {"extension name":["tag1", "tag2", "tag3", ...]}, ...)
		"""
		print "Updating tags cache, this takes a minute or so."
		print "(Items marked with '*' do not seem to contain extensions)"
		cache={}
		tags=self.svnlist(settings.tagsdir)
		for tag in tags:
			sys.stdout.write(tag)
			sys.stdout.flush()

			extensions=self.svnlist(settings.tagsdir+"/"+tag+"/"+settings.extensionssubdir)
			if extensions==None:
				extensions=[]
				sys.stdout.write('*')
				sys.stdout.flush()
			for extension in extensions:
				if extension not in cache:
					cache[extension]=[]
				cache[extension].append(tag)
			sys.stdout.write('; ')
			sys.stdout.flush()

		#store cache to disk
		pickle.dump(cache, file(settings.tagcache,"w"),pickle.HIGHEST_PROTOCOL)
		
		#make empty line
		print
		print "completed."
		return cache

	def gettags(self,extension):
		if extension not in self.cache:
			raise TagsException("Could not find extension "+str(extension)+".")
		
		tags=self.cache[extension]
		return copy.copy(tags)

if __name__=="__main__":
	tags=Tags()
	print "Imagemap: ",tags.gettags("ImageMap")
	print "Cite: ", tags.gettags("Cite")

