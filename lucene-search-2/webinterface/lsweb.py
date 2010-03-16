import string,cgi,time,urlparse,urllib2, urllib, cgi, copy
import re, time, math
from htmlentitydefs import name2codepoint
from os import curdir, sep
from BaseHTTPServer import BaseHTTPRequestHandler, HTTPServer
from urllib2 import URLError, HTTPError

#search_host = { 'enwiki' : "srv79:8123", '<default>': 'srv79:8123' }
search_host = {'<default>' : 'srv79:8123', 
               'jawiki' : "localhost:8123",
               'frwiki' : "localhost:8123", 
               'dewiki' : "localhost:8123",  
               'itwiki' : "localhost:8123",  
               'jawikiquote' : "localhost:8123",
               'wikilucene' : 'localhost:8123' }
#search_host = {'<default>' : 'localhost:8123'}

canon_namespaces = { 0 : '', 1: 'Talk', 2: 'User', 3: 'User_talk',
                    4 : 'Project', 5 : 'Project_talk', 6 : 'Image', 7 : 'Image_talk',
                    8 : 'MediaWiki', 9: 'MediaWiki_talk', 10: 'Template', 11: 'Template_talk',
                    12 : 'Help', 13: 'Help_talk', 14: 'Category', 15: 'Category_talk',
                    100: 'Portal', 101: 'Portal_talk', 102: 'Extension', 103: 'Extension_talk', 
                    104: 'Index', 105:' Index_talk', 112: 'Portal',  113: 'Portal_talk'}
prefix_aliases = { 'm': 0, 'mt' : 1, 'u' : 2, 'ut' : 3, 'p': 4, 'pt':5, 'i':6, 'it':7,
                   'mw':8, 'mwt':9, 't':10, 'tt':11, 'h':12, 'ht':13, 'c':14, 'ct': 15}

snippet_separator = " <b>...</b> ";
 
def make_link(params,offset,method='search',query=None):
    ''' Duplicate existing query (denoted by params), but with a different offset '''
    dupl = copy.copy(params)
    dupl['offset'] = [offset]
    if query != None:
        dupl['query'] = [query]
    return '/%s?%s' % (method,urllib.urlencode(dupl,True))


def rewrite_callback(match):
    namespaces = []
    for prefix in match.group(1).split(','):
        # check for canonical namespace names
        iscanonical = False
        for ns,name in canon_namespaces.iteritems():
            if name.lower() == prefix.lower():
                iscanonical = True # is there a way to continue outer loop in python?
                namespaces.append(str(ns))
                break
        if iscanonical:
            continue   
        # check aliases
        if prefix_aliases.has_key(prefix):
            namespaces.append(str(prefix_aliases[prefix]))
            continue
    
    if namespaces!=[]:
        return '[%s]:' % ','.join(namespaces)
    else:
        return match.group()
        

def rewrite_query(query):
    '''Rewrite query prefixes, port of php version in LuceneSearch extension'''
    query = query.decode('utf-8')
    prefix_re = re.compile('([a-zA-Z0-9_,]+):') # we will parse only canonical namespaces here
    
    return prefix_re.sub(rewrite_callback,query)

def make_wiki_link(line,dbname,caption=''):
    parts = line.split(' ')
    score = float(parts[0])
    title = ''
    if len(parts) == 3:
        ns = canon_namespaces[int(parts[1])]
        if ns != '':
            ns = ns +":"
        title = ns+parts[2]
    else:
        iw = parts[1]
        ns = canon_namespaces[int(parts[2])]
        if ns != '':
            ns = ns +":"
        title = iw+':'+ns+parts[3]
        
    if dbname == 'mediawikiwiki':
        link= 'http://www.mediawiki.org/wiki/%s' % (title)
    elif dbname == 'metawiki':
        link = 'http://meta.wikimedia.org/wiki/%s' % (title)
    elif dbname.endswith('wiktionary'):
        link = 'http://%s.wiktionary.org/wiki/%s' % (dbname[0:2],title)
    else:
        link = 'http://%s.wikipedia.org/wiki/%s' % (dbname[0:2],title)
    decoded = urllib.unquote(title.replace('_',' '))
    if caption !='':
        caption = ns+urllib.unquote(caption.replace('_',' '))
    else:
        caption = decoded
    return ['%1.2f -- <a href="%s">%s</a>' % (score,link,caption),title]

def make_title_link(line,dbname,caption=''):
    interwiki={'w':'wikipedia', 'wikt':'wiktionary', 's':'wikisource', 'b': 'wikibooks', 'n':'wikinews', 'v':'wikiversity', 'q':'wikiquote',
               'mw': 'mediawiki', 'meta': 'meta', 'wikinews': 'wikinews'};
    parts = line.split(' ')
    score = float(parts[0])
    title = ''
    iw = parts[1]
    # ns = canon_namespaces[int(parts[2])]    
    ns = urllib.unquote(parts[3])
    if ns != '':
        ns = ns +":"
    title = iw+':'+ns+parts[4]
    titleText = ns+parts[4]
    
    if dbname == 'mediawikiwiki':
        link= 'http://www.mediawiki.org/wiki/%s' % (title)
    elif dbname == 'metawiki':
        link = 'http://meta.wikimedia.org/wiki/%s' % (title)
    elif dbname.endswith('wiktionary'):
        link = 'http://%s.wiktionary.org/wiki/%s' % (dbname[0:2],title)
    else:
        link = 'http://%s.wikipedia.org/wiki/%s' % (dbname[0:2],title)
    decoded = urllib.unquote(titleText.replace('_',' '))
    if caption!='':
        caption = ns+urllib.unquote(caption.replace('_',' '))
    else:
        caption = decoded
    return ['%s : (%1.2f) <a href="%s">%s</a>' % (interwiki[iw],score,link,caption),title]

def extract_snippet(line,final_separator=True,originalIsKey=False):
    parts = line.split(' ')
    type = parts[0]
    splits = de_bracket_split(parts[1])
    highlight = de_bracket_split(parts[2])
    suffix = urllib.unquote_plus(de_bracket(parts[3]))
    text = urllib.unquote_plus(parts[4].strip())
    original = None
    if len(parts) > 5:
        original = urllib.unquote_plus(parts[5].strip())
    
    splits.append(len(text))
    start = 0
    snippet = ""
    hi = 0
    for sp in splits:
        sp = int(sp)
        while hi < len(highlight) and int(highlight[hi]) < sp:
            s = int(highlight[hi])
            e = int(highlight[hi+1])
            snippet += text[start:s] + "<b>" + text[s:e] + "</b>"
            start = e
            hi += 2
        snippet += text[start:sp]   
        if sp == len(text) and suffix != '':
            snippet += suffix
        elif final_separator:
            snippet += snippet_separator
        start = sp;
    if originalIsKey:
        origParts = original.split(":")
        origNs = canon_namespaces[int(origParts[0])]
        if origNs != '':
            origNs = origNs +":"
        original = origNs+origParts[1]
        snippet = origNs+snippet;
        
    return [snippet,original]

def extract_suggest(line):
    parts = line.split(' ')
    type = parts[0]
    highlight = de_bracket_split(parts[1])
    text = urllib.unquote_plus(parts[2].strip())
        
    start = 0
    snippet = ""
    hi = 0
    while hi < len(highlight):
        s = int(highlight[hi])
        e = int(highlight[hi+1])
        snippet += text[start:s] + "<i>" + text[s:e] + "</i>"
        start = e
        hi += 2
    if start < len(text):
        snippet += text[start:len(text)]
        
    for key,val in canon_namespaces.iteritems():
        snippet = snippet.replace('[%d]' % key, val)
    
    return [snippet,text]
    

def de_bracket(s):
    return s[1:len(s)-1]

def de_bracket_split(s):
    if s == '[]':
        return []
    else:
        return de_bracket(s).split(',')

class MyHandler(BaseHTTPRequestHandler):        
    def do_GET(self):
        try:
            s = urlparse.urlparse(self.path)
            if s[2] == '/search' or s[2] == '/related':
                method = s[2][1:]
                start_time = time.time()
                params = {}
                # parse key1=val1&key2=val2 syntax
                params = cgi.parse_qs(s[4])

                # defaults
                limit = 20
                offset = 0
                namespaces = []
                case = "ignore"
                
                # parameters 
                for key,val in params.iteritems():
                    if key == 'dbname':
                        dbname = val[0]
                    elif key == 'query':
                        query = val[0]
                    elif key == 'limit':
                        limit = int(val[0])
                    elif key == 'offset':
                        offset = int(val[0])
                    elif key.startswith('ns'):
                        namespaces.append(key[2:])
                
                rewritten = rewrite_query(query)

                if search_host.has_key(dbname):
                    host = search_host[dbname]
                else:
                    host = search_host['<default>']

                if dbname.endswith("-exact"):
                    case = "exact"
                    dbname = dbname[0:-6]

                # make search url for ls2
                search_url = 'http://%s/%s/%s/%s' % (host,method,dbname,urllib.quote(rewritten.encode('utf-8')))
                search_params = urllib.urlencode({'limit' : limit, 'offset' : offset, 'namespaces' : ','.join(namespaces), "case" : case}, True)
                
                # process search results
                try:    
                    results = urllib2.urlopen(search_url+"?"+search_params)
                    numhits = int(results.readline())
                    lasthit = min(offset+limit,numhits) 
                    # info
                    infoLine = results.readline()
                    # suggestions
                    suggest = results.readline()
                    suggestHl = ""
                    if suggest.startswith("#suggest "):                        
                        [suggestHl,suggest] = extract_suggest(suggest)
                    else:
                        suggest = ""
                    # interwiki
                    interwiki_count = results.readline();
                    interwiki_count = int(interwiki_count.split(' ')[1])
                    i = 0
                    interwiki = []
                    line = results.readline()
                    nextLine = ''
                    while not line.startswith("#results"):     
                        if not line.startswith('#'):
                            titleHl = ''
                            redirectHl = ''
                            redirectLink = None
                            nextLine = results.readline()
                            if nextLine.startswith('#h.title'):
                                [titleHl, orig] = extract_snippet(nextLine,False)
                                nextLine = results.readline()
                                if nextLine.startswith('#h.redirect'):
                                    [redirectHl, redirectLink] = extract_snippet(nextLine,False);
                                    if redirectLink != None:
                                        redirectLink = 'http://%s.wikipedia.org/wiki/%s' % (dbname[0:2],redirectLink)
                            elif nextLine.startswith('#h.redirect'):
                                [redirectHl, redirectLink] = extract_snippet(nextLine,False);
                                if redirectLink != None:
                                    redirectLink = 'http://%s.wikipedia.org/wiki/%s' % (dbname[0:2],redirectLink)
                                        
                            interwikiHtml = make_title_link(line,dbname,titleHl)[0]
                            if redirectLink != None:
                                interwikiHtml += '<small> (redirect <a href="%s">%s</a>)</small>' % (redirectLink.strip(), redirectHl)
                            interwiki.append(interwikiHtml)
                        
                        if nextLine == '':
                            line = results.readline()
                        else:
                            line = nextLine
                            nextLine = ''
                        if line.startswith('#h.date'):
                            line = results.readline() # just skip
                        if line.startswith('#h.wordcount'):
                            line = results.readline() # just skip
                            
                    # html headers
                    self.send_response(200)
                    self.send_header('Cache-Control','no-cache')
                    self.send_header('Content-type','text/html')
                    self.end_headers()
                    self.wfile.write('<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>LS2 search: %s</title></head>' % query)
                    if method == 'related':
                        self.wfile.write('<body>Articles related to article: %s <br>' % query)
                    else:
                        self.wfile.write('<body>Query: %s <br>' % query)
                    if suggest != "":
                        sparams = params.copy()
                        sparams['query'] = suggest;
                        slink = make_link(sparams,0,method)
                        self.wfile.write('Did you mean: <a href="%s">%s</a><br>' % (slink,suggestHl))
                    
                    # generate next/prev searchbar
                    if offset != 0:
                        link = make_link(params,max(offset-limit,0),method)
                        prev = '<a href="%s">&lt; Previous %s</a>' % (link,limit)
                    else:
                        prev = "&lt; Previous"
                    if numhits > lasthit:
                        link = make_link(params,offset+limit,method)
                        next = '<a href="%s">Next %s &gt;</a>' % (link,limit)
                    else:
                        next = "Next &gt;"
                    searchbar = '<a href="/">New search</a> | %s -- %s  | Total results: %d' % (prev, next, numhits)
                                        
                    
                    # show upper search bar
                    self.wfile.write(searchbar)
                    self.wfile.write('<hr>Showing results %d - %d<br>' % (offset,lasthit))
                    
                    # begin the main results table
                    self.wfile.write('<table><tr><td>')
                    
                    # show results
                    self.wfile.write('Score / Article<br>')
                    lines = []
                    for line in results:
                        lines.append(line)
                    i = 0
                    while i < len(lines):
                        scoreLine = lines[i];
                        # decode highlight info
                        textHl = ''
                        redirectHl = ''
                        redirectLink = None
                        sectionHl = ''
                        sectionLink = None
                        titleHl = ''
                        date = None;
                        wordcount = None;
                        [link,title] = make_wiki_link(scoreLine,dbname)
                        while i+1 < len(lines):                            
                            extra = lines[i+1]
                            if extra.startswith('#h.text'):
                                [newtext, orig] = extract_snippet(extra)
                                textHl += newtext
                            elif extra.startswith('#h.title'):
                                [titleHl, orig] = extract_snippet(extra,False)
                                [link,title] = make_wiki_link(scoreLine,dbname,titleHl)
                            elif extra.startswith('#h.redirect'):
                                [redirectHl, redirectLink] = extract_snippet(extra,False,True);
                                if redirectLink != None:
                                    redirectLink = 'http://%s.wikipedia.org/wiki/%s' % (dbname[0:2],redirectLink)
                            elif extra.startswith('#h.section'):
                                [sectionHl, sectionLink] = extract_snippet(extra,False);
                                if sectionLink != None:
                                    sectionLink = 'http://%s.wikipedia.org/wiki/%s#%s' % (dbname[0:2],title,sectionLink)
                            elif extra.startswith('#h.date'):
                                date = extra.split(' ')[1]
                            elif extra.startswith('#h.wordcount'):
                                wordcount = extra.split(' ')[1]
                            elif not extra.startswith('#h'):
                                break
                            i+=1
                                                    
                        self.wfile.write(link) # title link
                        if redirectLink != None:
                            self.wfile.write('<small> (redirect <a href="%s">%s</a>)</small>' % (redirectLink.strip(), redirectHl))
                        if sectionLink != None:
                            self.wfile.write('<small> (section <a href="%s">%s</a></small>)' % (sectionLink.strip(), sectionHl))
                        self.wfile.write('<br>');
                        if textHl != '':
                            textHl = textHl
                            self.wfile.write('<div style="width:500px"><font size="-1">%s</font></div>' % textHl)
                        if date != None:
                            self.wfile.write('<font size="-1"><i>Date: %s</i></font>' % date)
                        if wordcount != None:
                            dateprefix = ''
                            if date != None:
                                dateprefix = ' -- '
                            self.wfile.write('<font size="-1">%s<i>%s words</i></font>' % (dateprefix,wordcount))
                        if date != None or wordcount != None:
                            self.wfile.write(' -- ')
                        self.wfile.write('<font size="-1"><a href="%s">Related</a></font><br/>' % make_link(params,0,'related',urllib.unquote(title.replace('_',' '))))
                        i += 1 
                    
                    # write the grouped titles stuff    
                    self.wfile.write('</td><td width=35% valign=top>')
                    if interwiki != []:
                        self.wfile.write('From sister projects:<br/>')
                        self.wfile.write('<font size="-1">')
                        for iw in interwiki:
                            self.wfile.write(iw+'<br/>')
                        self.wfile.write('</font>')
                    self.wfile.write('</td></tr></table>')
                    self.wfile.write('<hr>')
                    # show lower search bar
                    self.wfile.write(searchbar)
                    self.wfile.write('</body></html>')
                except HTTPError:
                    self.send_error(400,'Bad request')
                    self.wfile.write("<div>Error in query</div>")
                except URLError:
                    self.send_error(500,'Internal Server Error')
                    self.wfile.write("<div>Cannot connect to lucene search 2 daemon</div>")
                delta_time = time.time() - start_time
                print '[%s] Processed query %s in %d ms' %(time.strftime("%Y-%m-%d %H:%M:%S"),self.path,int(delta_time*1000))
            elif s[2] == '/':
                # show the search form
                f = open(curdir + sep + "searchForm.html")
                search_form = f.read()
                f.close()
                self.send_response(200)
                self.send_header('Cache-Control','no-cache')
                self.send_header('Content-type','text/html')
                self.end_headers()
                self.wfile.write(search_form)
            elif s[2] == '/prefixQuery':
                # prefix search wrapper
                params = {}
                # parse key1=val1&key2=val2 syntax
                params = cgi.parse_qs(s[4])
                query = ''
                dbname = ''
                namespaces = ''
                for key,val in params.iteritems():
                    if key == 'dbname':
                        dbname = val[0]
                    elif key == 'query':
                        query = val[0]
                    elif key == 'namespaces':
                        namespaces = val[0]
                        
                if search_host.has_key(dbname):
                    host = search_host[dbname]
                else:
                    host = search_host['<default>']
                    
                search_url = 'http://%s/prefix/%s/%s?format=json' % (host,dbname,urllib.quote(query))
                if namespaces != '':
                    search_url += '&namespaces=%s' % namespaces
                    
                print(search_url)
                
                # forward json text
                try:
                    results = urllib2.urlopen(search_url)
                    self.send_response(200)
                    self.send_header('Cache-Control','no-cache')
                    self.send_header('Content-type','text/html')
                    self.end_headers()
                    for line in results:
                        self.wfile.write(line)                        
                except HTTPError:
                    self.send_error(400,'Bad request')
                    self.wfile.write("Error in query")
                except URLError:
                    self.send_error(500,'Internal Server Error')
                    self.wfile.write("Cannot connect to lucene search 2 daemon")                
            else:
                # showfile
                f = open(curdir + s[2])
                file = f.read()
                f.close()
                self.send_response(200)
                self.send_header('Cache-Control','no-cache')
                self.send_header('Content-type','text/html')
                self.end_headers()
                self.wfile.write(file)
            return                
        except IOError:
            self.send_error(500,'Internal Server Error')
            self.wfile.write('<a href="/">Back</a>')
            

            
try:
    server = HTTPServer(('', 8080), MyHandler)
    print 'Started webinterface at 8080...'
    server.serve_forever()
except KeyboardInterrupt:
    print '^C received, shutting down server'
    server.socket.close()
  
