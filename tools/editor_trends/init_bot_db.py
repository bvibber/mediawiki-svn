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

import os
import cStringIO
import xml.etree.cElementTree as cElementTree


import settings
from wikitree import xml
from database import db
from database import db_settings
from utils import utils
from utils import process_constructor as pc

try:
    import psyco
    psyco.full()
except ImportError:
    pass


def create_bot_ids_db_mongo():
    ids = utils.create_dict_from_csv_file(add_id_to_botnames, settings.ENCODING)
    mongo = db.init_mongo_db('bots')
    collection = mongo['ids']

    db.remove_documents_from_mongo_db(collection, None)

    for id, name in ids.iteritems():
        collection.insert({'id': id, 'name': name})

    print collection.count()


def create_bots_db(db_name):
    '''
    This function reads the csv file provided by Erik Zachte and constructs a
    sqlite memory database. The reason for this is that I suspect I will need
    some simple querying capabilities in the future, else a dictionary would
    suffice.
    '''
    connection = db.init_database('db_name')
    #connection = db.init_database('data/database/bots.db')
    cursor = connection.cursor()
    db.create_tables(cursor, db_settings.BOT_TABLE)
    values = []
    fields = [field[0] for field in db_settings.BOT_TABLE['bots']]
    for line in utils.read_data_from_csv('data/csv/StatisticsBots.csv', settings.ENCODING):
        line = line.split(',')
        row = []
        for x, (field, value) in enumerate(zip(fields, line)):
            if db_settings.BOT_TABLE['bots'][x][1] == 'INTEGER':
                value = int(value)
            elif db_settings.BOT_TABLE['bots'][x][1] == 'TEXT':
                value = value.replace('/', '-')
            #print field, value
            row.append(value)
        values.append(row)

    cursor.executemany('INSERT INTO bots VALUES (?,?,?,?,?,?,?,?,?,?);', values)
    connection.commit()
    if db_name == ':memory':
        return cursor
    else:
        connection.close()


def retrieve_botnames_without_id(cursor, language):
    return cursor.execute('SELECT name FROM bots WHERE language=?', (language,)).fetchall()


def lookup_username(input_queue, result_queue, progressbar, bots, debug=False):
    '''
    This function is used to find the id's belonging to the different bots that
    are patrolling the Wikipedia sites.
    @input_queue contains a list of xml files to parse

    @result_queue should be set to false as the results are directly written to
    a csv file.

    @progressbar depends on settings

    @bots is a dictionary containing the names of the bots to lookup
    '''

    #if len(bots.keys()) == 1:
    bots = bots['bots']
    #print bots.keys()

    if settings.DEBUG:
        messages = {}

    while True:
        if debug:
            file = input_queue
        else:
            file = input_queue.get(block=False)

        if file == None:
            break

        data = xml.read_input(utils.open_txt_file(settings.XML_FILE_LOCATION +
                            file, 'r', encoding=settings.ENCODING))

        for raw_data in data:
            xml_buffer = cStringIO.StringIO()
            raw_data.insert(0, '<?xml version="1.0" encoding="UTF-8" ?>\n')
            raw_data = ''.join(raw_data)
            raw_data = raw_data.encode('utf-8')
            xml_buffer.write(raw_data)

            try:
                xml_nodes = cElementTree.XML(xml_buffer.getvalue())
                revisions = xml_nodes.findall('revision')
                for revision in revisions:
                    contributor = xml.retrieve_xml_node(revision, 'contributor')
                    username = contributor.find('username')
                    if username == None:
                        continue
                    username = xml.extract_text(username)
                    #print username.encode('utf-8')

                    if username in bots:
                        id = contributor.find('id')
                        id = xml.extract_text(id)
                        #print username.encode('utf-8'), id
                        utils.write_data_to_csv({username: [id]}, add_id_to_botnames, settings.ENCODING)
                        bots.pop(username)
                        if bots == {}:
                            print 'Mission accomplished'
                            return
            except Exception, error:
                print error
                if settings.DEBUG:
                    messages = utils.track_errors(xml_buffer, error, file, 
                        messages)

    if settings.DEBUG:
        utils.report_error_messages(messages, lookup_username)


def add_id_to_botnames():
    '''
    This is the worker function for the multi-process version of
    lookup_username.First, the names of the bots are retrieved, then the
    multiprocess is launched by makinga call to pc.build_scaffolding. This is a
    generic launcher that takes as input the function to load the input_queue,
    the function that will do the main work and the objects to be put in the
    input_queue. The launcher also accepts optional keyword arguments.
    '''
    cursor = create_bots_db(':memory')
    files = utils.retrieve_file_list(settings.XML_FILE_LOCATION, 'xml')

    botnames = retrieve_botnames_without_id(cursor, 'en')
    bots = {}
    for botname in botnames:
        bots[botname[0]] = 1
    pc.build_scaffolding(pc.load_queue, lookup_username, files, bots=bots)
    cursor.close()


def debug_lookup_username():
    '''
    This function launches the lookup_username function but then single
    threaded, this eases debugging. That's also the reason why the queue
    parameters are set to None. When launching this function make sure that
    debug=False when calling lookup_username
    '''
    cursor = create_bots_db(':memory')
    botnames = retrieve_botnames_without_id(cursor, 'en')
    bots = {}
    for botname in botnames:
        bots[botname[0]] = 1

    lookup_username('12.xml', None, None, bots, debug=True)
    cursor.close()


if __name__ == '__main__':
    #debug()
    #add_id_to_botnames()
    create_bot_ids_db_mongo()
