#!/usr/bin/python
# -*- coding: utf-8 -*-
'''
Copyright (C) 2010 by Diederik van Liere (dvanliere@gmail.com)
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License version 2
as published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details, at
http://www.fsf.org/licenses/gpl.html
'''

__author__ = '''\n'''.join(['Diederik van Liere (dvanliere@gmail.com)', ])
__author__email = 'dvanliere at gmail dot com'
__date__ = '2010-10-21'
__version__ = '0.1'

#Default Python libraries (Python => 2.6)
import sys
import os
import time
import codecs
import cStringIO
import re
import xml.etree.cElementTree as cElementTree
from multiprocessing import Queue
from Queue import Empty
import pymongo

# Custom written files
import settings
from utils import utils, models
from database import db_settings
from database import db
from wikitree import xml
from statistics import dataset
from utils import process_constructor as pc


try:
    import psyco
    psyco.full()
except ImportError:
    pass

#contributors = {}

RE_BOT = re.compile('bot', re.IGNORECASE)
RE_SCRIPT = re.compile('script', re.IGNORECASE)


def determine_username_is_bot(username, kwargs):
    ids = kwargs.get('bots', [])
    if ids == None:
        ids = []
    if username != None and username.text != None:
        id = username.text
        if id in ids:
            return 1
        else:
            return 0


def extract_contributor_id(contributor, kwargs):
    '''
    @contributor is the xml contributor node containing a number of attributes
    
    Currently, we are only interested in registered contributors, hence we
    ignore anonymous editors. If you are interested in collecting data on
    anonymous editors then add the string 'ip' to the tags variable.
    '''
    tags = ['id']
    if contributor.get('deleted'):
        return - 1 #Not sure if this is the best way to code deleted contributors.
    for elem in contributor:
        if elem.tag in tags:
            if elem.text != None:
                return elem.text.decode('utf-8')
            else:
                return - 1


def output_editor_information(elem, data_queue, **kwargs):
    tags = {'contributor': {'editor': extract_contributor_id, 'bot': determine_username_is_bot},
            'timestamp': {'date': xml.extract_text},
            }
    vars = {}

    revisions = elem.findall('revision')
    for revision in revisions:
        vars['article'] = elem.find('id').text.decode(settings.ENCODING)
        elements = revision.getchildren()
        for tag, functions in tags.iteritems():
            xml_node = xml.retrieve_xml_node(elements, tag)
            for var, function in functions.iteritems():
                vars[var] = function(xml_node, kwargs)

        #print '%s\t%s\t%s\t%s\t' % (vars['article'], vars['contributor'], vars['timestamp'], vars['bot'])
        if vars['bot'] == 0 and vars['editor'] != -1 and vars['editor'] != None:
            vars.pop('bot')
            vars['date'] = utils.convert_timestamp_to_date(vars['date'])
            data_queue.put(vars)
        vars = {}

def parse_editors(xml_queue, data_queue, pbar, bots, debug=False, separator='\t'):
    if settings.DEBUG:
        messages = {}
        vars = {}
    while True:
        try:
            if debug:
                file = xml_queue
            else:
                file = xml_queue.get(block=False)
            if file == None:
                print 'Swallowed a poison pill'
                break
            data = xml.read_input(utils.open_txt_file(settings.XML_FILE_LOCATION,
                                                      file, 'r',
                                                      encoding=settings.ENCODING))
            for raw_data in data:
                xml_buffer = cStringIO.StringIO()
                raw_data.insert(0, '<?xml version="1.0" encoding="UTF-8" ?>\n')
                try:
                    raw_data = ''.join(raw_data)
                    xml_buffer.write(raw_data)
                    elem = cElementTree.XML(xml_buffer.getvalue())
                    output_editor_information(elem, data_queue, bots=bots)
                except SyntaxError, error:
                    print error
                    '''
                    There are few cases with invalid tokens, they are fixed
                    here and then reinserted into the XML DOM
                    data = convert_html_entities(xml_buffer.getvalue())
                    elem = cElementTree.XML(data)
                    output_editor_information(elem)
                    '''
                    if settings.DEBUG:
                        utils.track_errors(xml_buffer, error, file, messages)
                except UnicodeEncodeError, error:
                    print error
                    if settings.DEBUG:
                        utils.track_errors(xml_buffer, error, file, messages)
                except MemoryError, error:
                    '''
                    There is one xml file causing an out of memory file, not
                    sure which one yet. This happens when raw_data = 
                    ''.join(raw_data) is called. 18-22
                    '''
                    print file, error
                    print raw_data[:12]
                    print 'String was supposed to be %s characters long' % sum([len(raw) for raw in raw_data])
                    if settings.DEBUG:
                        utils.track_errors(xml_buffer, error, file, messages)

            if pbar:
                #print xml_queue.qsize()
                utils.update_progressbar(pbar, xml_queue)
            if debug:
                break

        except Empty:
            break

    if settings.DEBUG:
        utils.report_error_messages(messages, lookup_new_editors)


def store_editors(data_queue, pids, dbname):
    mongo = db.init_mongo_db(dbname)
    collection = mongo['editors']
    mongo.collection.ensure_index('editor')
    while True:
        try:
            edit = data_queue.get(block=False)
            contributor = edit['editor']
            value = {'date':edit['date'], 'article': edit['article']}
            collection.update({'editor': contributor}, {'$inc': {'edit_count': 1},
                                                        '$push': {'edits': value}}, True)
        except Empty:
            '''
            This checks whether the Queue is empty because the preprocessors are
            finished or because this function is faster in emptying the Queue
            then the preprocessors are able to fill it. If this preprocessors
            are finished and this Queue is empty than break, else wait for the
            Queue to fill.
            '''
            if all([utils.check_if_process_is_running(pid) for pid in pids]):
                pass
                #print 'Empty queue or not %s?' % data_queue.qsize()
            else:
                break


def optimize_editors(dbname, input_queue, **kwargs):
    mongo = db.init_mongo_db(dbname)
    collection = mongo['editors']
    definition = kwargs.pop('definition')
    while True:
        try:
            id = input_queue.get(block=False)
            #id = '94033'
            editor = collection.find_one({'editor': id})
            edits = editor['edits']
            edits.sort()
            year = edits[0]['date'].year
            new_wikipedian = dataset.determine_editor_is_new_wikipedian(edits, defintion)
            collection.update({'editor': id}, {'$set': {'edits': edits, 'year_joined': year, 'new_wikipedian': new_wikipedian}})
        
        except Empty:
            break


def store_data_db(data_queue, pids):
    connection = db.init_database()
    cursor = connection.cursor()
    db.create_tables(cursor, db_settings.CONTRIBUTOR_TABLE)

    empty = 0

    values = []
    while True:
        try:
            chunk = data_queue.get(block=False)
            contributor = chunk['contributor'].encode(settings.ENCODING)
            article = chunk['article']
            timestamp = chunk['timestamp'].encode(settings.ENCODING)
            bot = chunk['bot']
            values.append((contributor, article, timestamp, bot))

            if len(values) == 50000:
                cursor.executemany('INSERT INTO contributors VALUES (?,?,?,?)', values)
                connection.commit()
                #print 'Size of queue: %s' % data_queue.qsize()
                values = []

        except Empty:
            if all([utils.check_if_process_is_running(pid) for pid in pids]):
                pass
            else:
                break
    connection.close()


def run_stand_alone(dbname):
    files = utils.retrieve_file_list(settings.XML_FILE_LOCATION, 'xml')
    #files = files[:2]
    kwargs = {'bots': ids,
              'dbname': dbname,
              'pbar': True,
              'definition': 'traditional'}

    mongo = db.init_mongo_db('bots')
    bots = mongo['ids']
    ids = {}
    cursor = bots.find()
    for bot in cursor:
        ids[bot['id']] = bot['name']
    
    pc.build_scaffolding(pc.load_queue, parse_editors, files, store_editors, True, **kwargs)
    ids = retrieve_ids_mongo_new(dbname, 'editors')
    pc.build_scaffolding(pc.load_queue, optimize_editors, ids, False, False, **kwargs)

def debug_lookup_new_editors():
    q = Queue()
    import progressbar
    pbar = progressbar.ProgressBar().start()
    #edits = db.init_mongo_db('editors')
    parse_editors('464.xml', q, None, None, True)
    store_data_mongo(q, [], 'test')
    #keys = ['editor']
    #for key in keys:
    #    db.add_index_to_collection('editors', 'editors', key)

if __name__ == "__main__":
    #optimize_editors('enwiki')
    #debug_lookup_new_editors()

    if settings.RUN_MODE == 'stand_alone':
        run_stand_alone()
        print 'Finished processing XML files.'
    else:
        run_hadoop()
