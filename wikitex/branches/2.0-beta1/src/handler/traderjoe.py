##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
"""Trader Joe striveth to evade the middleman; he becometh one here.

Quoth the Fearless Flyer: "I want to grow up to be just like Trader Joe, so I can eliminate the
middleman and get my cookies directly."

"""

from mod_python.apache import OK, HTTP_NOT_IMPLEMENTED, log_error, \
     import_module, HTTP_SERVICE_UNAVAILABLE
from sys import modules
from re import compile, search, DOTALL, IGNORECASE
from os import EX_DATAERR, EX_UNAVAILABLE, EX_PROTOCOL, EX_UNAVAILABLE
from xmlrpclib import Fault, ServerProxy, loads, dumps
from xml.parsers.expat import ExpatError
from gdbm import open as gdbm_open
from os import open as os_open, close as os_close, O_RDWR, O_CREAT
from fcntl import lockf, LOCK_EX, LOCK_NB
from time import time
from threading import Timer
from shelve import open as shelve_open

from wikitex.config import *

METHOD = 'POST'

def respond(request, respondendum):
    request.write(dumps(respondendum))
    return OK

def close_db(db, lock):
    if not db is None:
        db.close()
    if not lock is None:
        os_close(lock)
    return (None, None)

##
# Lock the lockfile for writing.
# @return Lockfile-filedescriptor
def get_write_lock():
    lock = os_open(Config.db_lock, O_RDWR | O_CREAT)
    lockf(lock, LOCK_EX)
    return lock

def open_db():
    lock = get_write_lock()
    db = None
    try:
        db = shelve_open(Config.db)
    except:
        close_db(db, lock)
        raise Exception(EX_UNAVAILABLE, 'Can\'t open database for writing')
    return (lock, db)

def handler(request):
    TIME = 't'
    CTIME = 'c'
    if request.method.upper() != METHOD.upper():
        return HTTP_NOT_IMPLEMENTED
    doc = request.read()
    db = lock = None
    response = OK
    try:
        args, action = loads(doc)
        if not action:
            return respond(request, Fault(EX_PROTOCOL, 'Invalid procedure-call'))
        document, = args
        content = str(document['content'])
        author = str(document['author'])
        thistime = time()
        lock, db = open_db()
        try:
            lastedit = db[author]
        except KeyError:
            lastedit = {TIME: thistime, CTIME: 0.0}
        db, lock = close_db(db, lock)
        lasttime = lastedit[TIME]
        ctime = lastedit[CTIME]
        delta = thistime - lasttime
        ctime_per_unit = 0.0
        if not delta == 0.0:
            ctime_per_unit = ctime / delta
        if delta > Config.ctime_unit:
            lasttime = thistime
        elif ctime_per_unit > Config.max_ctime_per_unit:
            # User has exceeded its CPU-ration; and instead
            # of returning a true 503, we'll fashion a more
            # descriptive fault and return OK.
            # return HTTP_SERVICE_UNAVAILABLE
            return respond(request,
                           Fault(EX_UNAVAILABLE, 'Thou hast, dear user, ' +
                                 'exceeded thine resource-allotment'))
                                          
        facility = Config.publicae[action]
        server = ServerProxy('http://%(host)s:%(port)d' %
                             {'host': facility.host,
                              'port': facility.port})
        cstart = time()
        response = respond(request, (server.__getattr__(action).__call__(content),))
        cdelta = time() - cstart
        ctime += cdelta
        thisedit = {TIME: lasttime, CTIME: ctime}
        lock, db = open_db()
        db[author] = thisedit
        db, lock = close_db(db, lock)
    except ExpatError:
        response = respond(request, Fault(EX_PROTOCOL, 'Invalid procedure-call'))
    except KeyError:
        response = respond(request, Fault(EX_DATAERR, 'Unknown procedure-call'))
    except Exception, message:
        response = respond(request, Fault(EX_UNAVAILABLE, str(message)))
    finally:
        db, lock = close_db(db, lock)
        return response
