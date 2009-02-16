# This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.

class Test:
	name=""
	code=""
	expect=""
	pageReference=None
	diff=""
	ok=None
	result=""
	enable=True
	known_bug=False
	

	def __init__(self, name="", code="", expect="", pageReference=None, diff="", result="", ok=None, enable=True, known_bug=False):
		self.name=name
		self.code=code
		self.expect=expect
		self.pageReference=None
		self.diff=diff
		self.result=result
		self.ok=ok
		self.enable=enable
		self.known_bug=known_bug

	def __repr__(self):
		return "Test(name='''"+self.name+\
			"''', code='''"+self.code+\
			"''', expect='''"+self.expect+\
			"''', pageReference="+repr(self.pageReference)+\
			", diff='''"+self.diff+\
			"''', ok="+str(self.ok)+\
			", result='''"+self.result+\
			"''', enable="+str(self.enable)+\
			", known_bug="+str(self.known_bug)+\
			")"
	
	def toXML(self):
		xml="<exttest>\n"
		xml+="	<name>"+self.name+"</name>\n"
		xml+="	<code><![CDATA["+self.code+"]]></code>\n"
		xml+="	<expect><![CDATA["+self.expect+"]]></expect>\n"
		if self.result:
			xml+="	<result><![CDATA["+self.result+"]]></result>\n"
		if self.diff:
			xml+="	<diff><![CDATA["+self.diff+"]]></diff>\n"
		if self.known_bug:
			xml+="  <known bug/>"
		xml+="</exttest>\n"
		return xml
	
class PageReference:
	startPosition=0
	endPosition=0
	pageTitle=""

	def __init__(self, pageTitle="", startPosition=0, endPosition=0):
		self.pageTitle=pageTitle
		self.startPosition=startPosition
		self.endPosition=endPosition
	
	def __repr__(self):
		return "PageReference(pageTitle='"+self.pageTitle+"', startPosition="+str(self.startPosition)+", endPosition="+str(self.endPosition)+")"


