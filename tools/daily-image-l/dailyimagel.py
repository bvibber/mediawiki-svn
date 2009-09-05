#!/usr/bin/python
# -*- coding: utf-8 -*-
 
 
wget = '''/usr/sfw/bin/wget -S -erobots=off -q -O - '''
 
todaypotd = r'http://commons.wikimedia.org/w/index.php?title=Commons:Picture_of_the_day/Today&action=purge'
urlbase = r'http://commons.wikimedia.org/wiki/'
#querycat = 'http://commons.wikimedia.org/w/query.php?what=categories&format=txt&titles='
querycat = 'http://commons.wikimedia.org/w/api.php?action=query&format=txt&prop=categories&titles='
#querylinks = r'http://commons.wikimedia.org/w/query.php?what=imagelinks&ilnamespace=4&format=txt&illimit=300&titles='
querylinks = r'http://commons.wikimedia.org/w/api.php?action=query&format=txt&iunamespace=4&iulimit=500&list=imageusage&iutitle='

 
import os,sys,re
from commands import getoutput
from datetime import date
 
repotdcontent = re.compile('<!-- start content -->(.*?)<!-- end content -->', re.DOTALL)
reimagename = re.compile('<div class="magnify"><a href="/wiki/([^"]*)" class="internal"')
recats =  re.compile('Category:(.*)')
refplinks = re.compile('Commons:Featured pictures/([^c].*)')
reqilinks = re.compile('Commons:Quality [Ii]mages/([^c].*)')
recaptions = re.compile('<ul>(.*?)</ul>', re.DOTALL)
reli = re.compile('</?li[^>]*>')
rea = re.compile('</?a[^>]*>')
rei = re.compile('</?i>')
renocaption = re.compile('\n[^:]*: Template:Potd[^)]*\)')
 
SENDMAIL = "/usr/sbin/sendmail"
 
mailfilename = "/projects/potd/dailyimagel.txt"
mailerror = "/projects/potd/mailerror.txt"
 
#mailto = "brianna.laugher@gmail.com"
mailto = "daily-image-l@lists.wikimedia.org"
#mailto = 'bryan.tongminh@gmail.com'
 
def createmail():
    '''
    Attempts to create an email at mailfilename.
    '''
    #print "starting"
    f = getoutput(wget + '--post-data submit "' + todaypotd + '"')
    #print "got wget output ok" 

    wgetfile = open('/projects/potd/wgetoutput.txt','w')
    wgetfile.write(f)
    wgetfile.close()
 
    #print "f:",f

    content = repotdcontent.findall(f)
 
    #print "got content ok"

    # extract image name/url
    #print len(content)
    #print "content[0]:",content[0]

    imagename = reimagename.findall(content[0])[0]
    imageurl = urlbase + imagename
 
    #print "got image name ok"

    # attempt to determine license status from categories
    catstext = getoutput(wget + '"' + querycat + imagename + '"')
    categories = recats.findall(catstext)

    #print "categories:", categories
 
    licenses = {"GFDL":"GNU Free Documentation License",
                "CC-BY-SA-2.5,2.0,1.0":"Creative Commons Attribution ShareAlike license, all versions",
                "CC-BY-SA-1.0":"Creative Commons Attribution ShareAlike license, version 1.0",
                "CC-BY-SA-2.0":"Creative Commons Attribution ShareAlike license, version 2.0",
                "CC-BY-SA-2.5":"Creative Commons Attribution ShareAlike license, version 2.5",
                "CC-BY-1.0":"Creative Commons Attribution license, version 1.0",
                "CC-BY-2.0":"Creative Commons Attribution license, version 2.0",
                "CC-BY-2.5":"Creative Commons Attribution license, version 2.5"
                }
 
    lic = ""
    if "Self-published work" in categories:
        lic = "Created by a Wikimedian (see image page for details); "
    for l in licenses.keys():
        if l in categories:
            lic += "Licensed under the " + licenses[l] +'. '
 
    if "Public domain" in categories:
        lic = "Public domain"
 
    for cat in categories:
        if cat.startswith("PD"):
            if cat=="PD-self":
                lic = "Created by a Wikimedian (see image page for details); released into the public domain."
            elif cat=="PD Art":
                lic = "Reproduction of a two-dimensional work of art whose copyright has expired (public domain)."
            elif cat=="PD Old":
                lic = "Public domain (copyright expired due to the age of the work)."
            else:
                lic = "Public domain as a work of the " + cat[3:] + " organisation."
 
    # determine FP category (or 'topic')
    linkstext = getoutput(wget + '"' + querylinks + imagename + '"')
    isFP = True
    try:
        topics = refplinks.findall(linkstext)[0]
    except IndexError:
        try:
            isFP = False
            topics = reqilinks.findall(linkstext)[0]
        except IndexError:
            print "Could not find FP or QI backlink, aborting"
            raise IndexError, 'Could not find FP or QI backlink'
 
    if '/' in topics:
        topic = topics.split('/')[0] + ' (' + topics.split('/')[1] + ')'
    else:
        topic = topics
 
    # extract multilingual captions
    try:
        captions = recaptions.findall(content[0])[0]
    except IndexError:
        raise IndexError, 'no captions??'
 
    #print captions
    captions = reli.sub('',captions)
    captions = rea.sub('',captions)
    captions = rei.sub('',captions)
    captions = renocaption.sub('',captions)
 
 
    # write info to file
    g= open(mailfilename,'w')
    g.write("To: " + mailto + '\n')
    g.write('Content-Type: text/plain; charset=utf-8\r\n')
    #don't need this?
    #g.write("From: brianna.laugher@gmail.com\n")
    g.write("Subject: " + str(date.today()) + '\r\n\r\n')
    g.write("Body of email:\r\n")
 
    g.write(imageurl + '\n')
    g.write('Copyright status: ' + lic +  '\n')
    if isFP:
        g.write('Featured Picture category: ' + topic + '\n\n')
    else:
        if 'Subject' in topic:
            g.write('Recognised as a Quality Image due to subject matter\n\n')
        else:
            g.write('Recognised as a Quality Image due to technical merit\n\n')
    g.write('Descriptions:\n')
    g.write(captions)
    g.close()
    return
 
###############################
error = None
try:
    createmail()
except:
    # some Python error, catch its name and send error mail
    error = sys.exc_info()[0]
    mailfilename = mailerror
 
# get the email message from a file
f = open(mailfilename, 'r')
mail = f.read()
f.close()
 
if error:
    mail += "Error information: " + str(error)
 
# open a pipe to the mail program and
# write the data to the pipe
p = os.popen("%s -t" % SENDMAIL, 'w')
p.write(mail)
exitcode = p.close()
if exitcode:
    print "sendmail error: Exit code: %s" % exitcode
