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

from multiprocessing import Queue
from Queue import Empty
import sqlite3

import progressbar

import settings
from utils import models, utils
from database import db
from utils import process_constructor as pc

try:
    import psyco
    psyco.full()
except ImportError:
    pass


def retrieve_editor_ids_mongo():
    if utils.check_file_exists(settings.BINARY_OBJECT_FILE_LOCATION,
                               retrieve_editor_ids_mongo):
        ids = utils.load_object(settings.BINARY_OBJECT_FILE_LOCATION,
                                retrieve_editor_ids_mongo)
    else:
        mongo = db.init_mongo_db('editors')
        editors = mongo['editors']
        contributors = set()
        #ids = editors.find().distinct('editor')
        ids = editors.find()
        for x,id in enumerate(ids):
            contributors.add(id['editor'])
            if len(contributors) % 25000 == 0:
                print x, len(contributors)
        if ids != set():
            utils.store_object(ids, settings.BINARY_OBJECT_FILE_LOCATION, retrieve_editor_ids_mongo)
    return ids


def generate_editor_dataset(input_queue, data_queue, pbar, kwargs):
    definition = kwargs.pop('definition')
    limit = kwargs.pop('limit')
    debug = kwargs.pop('debug')
    mongo = db.init_mongo_db('editors')
    editors = mongo['editors']
    while True:
        try:
            if debug:
                id = u'99797'
            else:
                id = input_queue.get(block=False)


            if definition == 'Traditional':
                obs = editors.find({'editor': id}).sort('date').limit(limit)
                contributors = []
                for ob in obs:
                    contributors.append(ob['date'])
            else:
                obs = editors.find({'editor': id}).sort('date')
                contributors = set()
                for ob in obs:
                    if len(contributors) == limit:
                        break
                    else:
                        contributors.add(ob['date'])

            if len(contributors) < limit:
                new_wikipedian = False
            else:
                new_wikipedian = True
            data = {id: [contributors, new_wikipedian]}
            utils.write_data_to_csv(data, settings.DATASETS_FILE_LOCATION, generate_editor_dataset, settings.ENCODING)

        except Empty:
            break


def retrieve_editor_ids_db():
    contributors = set()
    connection = db.init_database()
    cursor = connection.cursor()
    if settings.PROGRESS_BAR:
        cursor.execute('SELECT MAX(ROWID) FROM contributors')
        for id in cursor:
            pass
        pbar = progressbar.ProgressBar(maxval=id[0]).start()

    cursor.execute('SELECT contributor FROM contributors WHERE bot=0')

    print 'Retrieving contributors...'
    for x, contributor in enumerate(cursor):
        contributors.add(contributor[0])
        if x % 100000 == 0:
            pbar.update(x)
    print 'Serializing contributors...'
    utils.store_object(contributors, 'contributors')
    print 'Finished serializing contributors...'

    if pbar:
        pbar.finish()
        print 'Total elapsed time: %s.' % (utils.humanize_time_difference(pbar.seconds_elapsed))

    connection.close()


def retrieve_edits_by_contributor(input_queue, result_queue, pbar):
    connection = db.init_database()
    cursor = connection.cursor()

    while True:
        try:
            contributor = input_queue.get(block=False)
            if contributor == None:
                break

            cursor.execute('SELECT contributor, timestamp, bot FROM contributors WHERE contributor=?', (contributor,))
            edits = {}
            edits[contributor] = set()
            for edit, timestamp, bot in cursor:
                date = utils.convert_timestamp_to_date(timestamp)
                edits[contributor].add(date)
                #print edit, timestamp, bot

            utils.write_data_to_csv(edits, retrieve_edits_by_contributor)
            if pbar:
                utils.update_progressbar(pbar, input_queue)

        except Empty:
            pass

    connection.close()


def retrieve_edits_by_contributor_launcher():
    pc.build_scaffolding(pc.load_queue, retrieve_edits_by_contributor, 'contributors')


def debug_retrieve_edits_by_contributor_launcher():
    input_queue = Queue()
    kwargs = {'definition':'Traditional',
              'limit': 10,
              'debug': True
              }
    generate_editor_dataset(input_queue, False, False, kwargs)
    #generate_editor_dataset_launcher()
    #retrieve_list_contributors()
    #retrieve_edits_by_contributor()

def generate_editor_dataset_launcher():
    ids = retrieve_editor_ids_mongo()
    pc.build_scaffolding(pc.load_queue, generate_editor_dataset, ids, False, False, definition='Traditional', limit=10)


if __name__ == '__main__':
    generate_editor_dataset_launcher()
    #debug_retrieve_edits_by_contributor_launcher()
