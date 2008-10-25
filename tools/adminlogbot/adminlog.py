site="wikitech.leuksman.com"
path="/"
user="More Bots"
password="..."
logname="Server admin log"

import mwclient
import datetime

months=["January","February","March","April","May","June","July","August","September","October","November","December"]

def log(message,author):
	site=mwclient.Site(site, path=path)
	site.login(user,password)
	page=site.Pages[logname]
	text=page.edit()
	lines=text.split('\n')
	position=0
	# Try extracting latest date header
	for line in lines:
		position+=1
		if line.startswith("=="):
			undef,month,day,undef=line.split(" ",3)
			break

	# Um, check the date
	now=datetime.datetime.utcnow()
	logline="* %02d:%02d %s: %s" % ( now.hour, now.minute, author, message )
	if months[now.month-1]!=month or now.day!=int(day):
		lines.insert(0,"")
		lines.insert(0,logline)
		lines.insert(0,"== %s %d =="%(months[now.month-1],now.day))
	else:
		lines.insert(position,logline)
	page.save('\n'.join(lines),"%s (%s)"%(message,author))
