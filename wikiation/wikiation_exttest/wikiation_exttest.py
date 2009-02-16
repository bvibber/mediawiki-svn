#!/usr/bin/python
# -*- coding: utf-8  -*-

# This software, copyright (C) 2008-2009 by Wikiation.
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license

import sys,os
import settings

sys.path.append(settings.pywikipedia_path)
cwd=os.getcwd()
os.chdir(settings.pywikipedia_path)
import wikipedia
import login
import catlib
os.chdir(cwd)

import difflib
import imp
from exttest import Test, PageReference
from xml.etree import ElementTree 


class MissingDataException (Exception): 
	pass

class WikiWebTest:
	"""
	Run extension tests on a mediawiki wiki.
	"""

	"""open marker for test sequence , must be long and unique"""
	open=u"---START of wikiation test sequence---"
	"""close marker for  test sequence, must be long and unique"""
	close=u"---END of wikiation test sequence---"


	def __init__ (self,site):
		self.site=site
		self.html=None

	def feedWikiText (self,wikitext, pageName="Bot_test"):
		"""feed wikitext to the server, and get html back"""

		#make a new page with just the wikitext
		page=wikipedia.Page(self.site, pageName)
		page.put(wikitext)

		#get raw html for the page back
		address=self.site.get_address(pageName)
		html=self.site.getUrl(address)

		return html


	def genTest(self,codeToBeTested):
		"""enclose codeToBeTested in test sequence"""
		
		if codeToBeTested==None:
			raise MissingDataException("code to be tested is Null");

		testCode=WikiWebTest.open+codeToBeTested+WikiWebTest.close

		return testCode


	def cleanFeed(self,codeToBeTested, pageName="Bot_test"):
		"""like feedWikiText, but return just the bit we're actually
		interested in"""
		
		testCode=self.genTest(codeToBeTested)
		html=self.feedWikiText(testCode,pageName)
		enclosedHtml=self.getEnclosed(html)

		self.html=html
		return enclosedHtml
		

	def getEnclosed(self,html):
		"""pull the code-of-interest out from between the open and close markers"""

		startOpen=html.index(WikiWebTest.open)
		start=startOpen+len(WikiWebTest.open)

		end=html.index(WikiWebTest.close)

		return html[start:end]


	def test(self, wikiText, expectHtml, test=None, pageName="Bot_test"):
		"""do the test, report comparison as plain text for now
		I tried the htmltable, but mediawiki refuses to eat it :-/"""
		
		actualHtml=self.cleanFeed(wikiText, pageName)

		actualHtmlLines=actualHtml.splitlines(1);
		expectHtmlLines=expectHtml.splitlines(1);

		differ=difflib.Differ()
		diff_text_list= list(differ.compare(actualHtmlLines,expectHtmlLines))
		diff_text="\n".join(diff_text_list)

		if test:
			if actualHtml==expectHtml:
				test.ok=True
			
			if actualHtml!=expectHtml:
				test.ok=False

			test.diff=diff_text
			test.result=actualHtml

		return diff_text
	
def _title(title):
	"""
	-----------------------
	| make a pretty title |
	-----------------------
	"""
	titlerow='| '+title+' |'
	stripe="\n"+( "-"*len(titlerow) )+"\n"
	return stripe+titlerow+stripe

def runtestset(webtest, tests, pageName=None) :
	"""Take a List of Test-s, and feed them to WikiWebTest to run them"""
	
	if pageName==None:
		pageName=settings.pageName

	for test in tests:

		print _title(test.name+' :')
		if test.enable:
			diff=webtest.test(test.code, test.expect, test, pageName)
			print unicode(diff).encode('utf8')
			if test.ok:
				print "\n--OK!\n"
			elif test.known_bug:
				print "\n--OK, known bug.\n"
			else:
				print "\n--FAIL\n"
		else:
			print "test not enabled\n"
	

	print _title("TESTSET COMPLETED")


def _isCommented(text,offset,tagstart):
	"""
	For a given position in a string, determine if it is located
	in a comment block. Used here to check whether <exttest> or
	</exttest> is located inside a comment.
	'text' is the text to check,
	'offset' is the offset from start of string.
	'tagstart' is the position to check (start of our tag)
	"""
	endcommentp=0
	while True:
		commentp=text.find("<!--",offset,tagstart)
		if commentp!=-1:
			endcommentp=text.find("-->",commentp,tagstart)
			if endcommentp==-1:
				break
			offset=endcommentp+len("-->")
		else:
			break

	return endcommentp==-1



def article2testset(article):
	"""parse a pywikipedia Page (article), to yield a testset"""
	text=article.get()
	testset=[]
	count=0
	offset=0
	while True:
		startPosition=text.find("<exttest>",offset)
		if startPosition==-1:
			break
		
		if _isCommented(text,offset,startPosition):
			offset=startPosition+1
			continue

		endTagPosition=text.find("</exttest>",offset)

		if endTagPosition==-1:
			raise Exception("malformed xml in page "+str(article))
			
		if _isCommented(text,offset,endTagPosition):
			offset=endTagPosition+1
			continue

		endPosition=endTagPosition+len("</exttest>")

		xmlString=text[startPosition:endPosition]
		try:
			test=xml2testset(unicode(xmlString).encode('utf8'))
		except  Exception, e:
			raise Exception("Problem loading page [["+article.title()+"]]: ",e)
		test[0].pageReference=PageReference(
			pageTitle=article.title(),
			startPosition=startPosition,
			endPosition=endPosition)
		count+=1
		if settings.tests_per_page!=0 and count>settings.tests_per_page:
			test[0].enable=False

		testset+=test

		offset=endPosition

	return testset

def tests2page(tests, site, pageTitle):
	"""Write a test report back to a wikipage.
	Only operates between <extresult></extresult> tags;
	this way, if something goes wrong, hopefully we won't
	b0rk the _entire_ page.
	tests is the tests relevant to this page, site is the pywikipedia
	site object for this website, and pageTitle is the title of the
	page on the site."""
	page=wikipedia.Page(site,pageTitle)
	text=page.get()
	offset=0
	for test in tests:
		ref=test.pageReference

		s=text.find("<extresult>",ref.endPosition+offset)
		startPosition=s+len("<extresult>")
		endPosition=text.find("</extresult>",startPosition)
		if s!=-1 and endPosition!=-1:
			before=text[:startPosition]
			middle="\n<!-- BOT OUTPUT-->\n;Result "
			if test.ok:
				middle+='<span style="color:green">OK</span>'
			elif test.ok==False:
				if test.known_bug:
					middle+='<span style="color:yellow">Known Bug</span>'
				else:	
					middle+='<span style="color:red">Differs from expected</span>'
			else:
				middle+='<span style="color:orange">unknown</span>'
			if test.enable:
				middle+="\n;html output\n"
				middle+="<pre>\n"+test.result+"\n</pre>"
				middle+="\n;diff\n"
				middle+="<pre>\n"+test.diff+"</pre>"
			else:
				middle+="\n;Only "+str(settings.tests_per_page)+" tests are allowed per page"
			middle+="\n<!-- END OF BOT OUTPUT-->\n"
			after=text[endPosition:]
			text=before+middle+after
			offset+=len(middle)-(endPosition-startPosition)
				
	page.put(text,"testresult")

def testset2pages(testset, site):
	"""Goes through testset, and writes results back to each page
	on the pywikipedia site.
	In the case case where multiple tests per page are permitted,
	testset results are grouped by page, so that only 1 write
	needs to be done per page."""
	byPage={}
	# reorganize tests by page so we can
	# do a page at a time
	for test in testset:
		title=test.pageReference.pageTitle
		if title not in byPage:
			byPage[title]=[]
		byPage[title].append(test)
	
	for title, tests in byPage.iteritems():
		tests2page(tests, site, title)



def category2testset(site,categoryName="Automated tests"):
	"""Scan a category on the given pywikipedia site for valid 
	extension tests, and generate a testset (a list of Test objects)"""
	category=catlib.Category(site,categoryName)
	testset=[]
	for article in category.articles():
		testset+=article2testset(article)

	return testset

def runcategorytestset(source_site, webtest, categoryName="Automated tests"):
	"""Scan a category for valid extension tests,
	and run all the tests using the webtest object."""
	testset=category2testset(source_site,categoryName)
	runtestset(webtest,testset)

	return testset

def runcategoryandreport(source_site, webtest, categoryName="Automated tests"):
	"""Scan a category for valid extension tests,
	run all the tests using the webtest object, and report 
	the results back to the relevant pages on the wiki.
	"""
	testset=runcategorytestset(source_site, webtest, categoryName)
	testset2pages(testset, source_site)
	return testset

def _elementText(element,tag):
	"""find the relevant element contents in an elementtree element,
	and strip spurious whitespace from the ends if settings.strip_whitespace
	is set"""
	text=element.findtext(tag)
	#This strips spurious whitespace from CDATA, due to users inserting it.
	#ick.
	if text and settings.strip_whitespace:
		text=text.strip()
	return text

def xml2testset(xmlString):
	"""converts xml string to a testset (a list of Test objects).
	Next to xml that actually contains a testset (obviously), this function 
	will also eat an xml string containing only a single exttest element
	(intended for internal use only, ymmv). If settings.strip_whitespace
	is set, spurious whitespace will be stripped from the start and end of
	the element contents, even inside CDATA!"""
	if not xmlString:
		raise Exception("xml2testset: Empty xmlString.")
	tests=[]
	elements=ElementTree.XML(xmlString)
	for testElement in elements.getiterator("exttest"):
		test=Test()
		test.name=_elementText(testElement, "name")
		test.code=_elementText(testElement, "code")
		test.expect=_elementText(testElement,"expect")
		test.result=_elementText(testElement, "result")
		test.diff=_elementText(testElement, "diff")
		if testElement.find("known_bug") is not None:
			test.known_bug=True
		
		if not test.name:
			print "WARNING: <name> element missing"
		if not test.expect:
			print "WARNING:",
			if test.name:
				print "in "+test.name+":",
			print " <expect> element missing"

		if test.code:
			tests.append(test)
		else:
			report=""
			if test.name:
				report+="In"+test.name+" "
			else:
				report+="In <unnamed test>: (<name> element missing or left blank, so reports might be unclear)"
			if not test.code:
				report +="; missing <code> element (required)"
			if not test.expect:
				report +="; missing <expect> element (not required, but typically recommended)"
			report +="."
			raise MissingDataException(report)

	return tests
	
def runxmltestset(webtest, xmlstring):
	"""Take a testset defined in xml format and run it with WikiWebtest 
	webtest"""
	testset=xml2testset(xmlstring)
	runtestset(webtest,testset)
	return testset

def dumppy(testset,filename):
	"""write testset to file in python .py format.
	This file can be import-ed normally in python to obtain 
	the testset contents.
	Note that the file has test.py as a dependency (in case you
	want to ship it elsewhere)."""
	f=file(filename,"w")
	f.write("from exttest import *\n")
	f.write("tests="+repr(testset))
	f.close()

def dumpxml(testset,filename):
	"""write testset to file in xml format."""
	xml='<?xml version="1.0"?>\n'
	xml+="<exttests>\n"
	for test in testset:
		xml+=test.toXML()
	xml+="</exttests>\n"
	f=file(filename,"w")
	f.write(xml)
	f.close()

def do_login(site_language, site_name, site_password):
	raise Exception("do_login is Deprecated. Use LoginData instead")

def main():
	testset=[]
	category_name=settings.category_name
	allOk=False

	if "--category-name" in sys.argv:
		category_name=sys.argv[ sys.argv.index("--category-name")+1]
		allOk=True
	if "--pyfile" in sys.argv:
		target_site=settings.target_login.login()
		webtest=WikiWebTest(target_site)
		filename=sys.argv[ sys.argv.index("--pyfile")+1]
		print "loading '"+filename+"'"
		testmodule=imp.load_source("testmodule",filename)			
		tests=testmodule.tests
		testset=runtestset(webtest,tests)
		allOk=True
	elif "--xmlfile" in sys.argv:
		target_site=settings.target_login.login()
		webtest=WikiWebTest(target_site)
		filename=sys.argv[ sys.argv.index("--xmlfile")+1]
		print "loading '"+filename+"'"
		xmlfile=file(filename)
		xmlstring=xmlfile.read()
		xmlfile.close()
		testset=runxmltestset(webtest, xmlstring)
		allOk=True
	elif "--category" in sys.argv:
		print "testing category"

		source_site=settings.source_login.login()
		target_site=settings.target_login.login()
		webtest=WikiWebTest(target_site)
		testset=runcategorytestset(source_site, webtest, category_name)
		allOk=True
	elif "--category-and-write" in sys.argv:
		print "running cat and writing back to the wiki"
		source_site=settings.source_login.login()
		target_site=settings.target_login.login()
		webtest=WikiWebTest(target_site)
		testset=runcategoryandreport(source_site, webtest, category_name)
		allOk=True
	elif "--test" in sys.argv:
		print webtest.test("{{SERVER}} yet another test","<a something> yet another test")
		allOk=True
	elif "--help" in sys.argv:
		print file("HELP").read()
		allOk=True

	if "--dump-xml" in sys.argv:
		filename=sys.argv[ sys.argv.index("--dump-xml")+1]
		print "saving xml to '"+filename+"'."
		dumpxml(testset,filename)
		allOk=True
	
	if "--dump-py" in sys.argv:
		filename=sys.argv[ sys.argv.index("--dump-py")+1]
		print "saving py to '"+filename+"'."
		dumppy(testset,filename)
		allOk=True

	if not allOk:
		print "I don't understand. Try --help for help?"


if __name__=="__main__":
	try:
		main()
	finally:
		wikipedia.stopme()


