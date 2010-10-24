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
__date__ = 'Oct 24, 2010'
__version__ = '0.1'

'''
This module provides a simple caching mechanism to speed-up the process of
inserting records to MongoDB. The caching bject works as follows:
1) Each edit from an author is added to a dictionary 
2) Every 50000 edits, the object returns %x with the most edits, and these are 
then stored in MongoDB. By packaging multiple edits in a single commit, 
processing time is significantly reduced. 

This caching mechanism does not create any benefits for authors with single or
very few edits.  

'''


import sys
import datetime

import settings
import db


class EditorCache(object):
    def __init__(self, collection):
        self.collection = collection
        self.editors = {}
        self.size = self.__sizeof__()
        self.cumulative_n = 0
        self.time_started = datetime.datetime.now()
        self.n = self.current_cache_size()
        self.emptied = 1


    def __repr__(self):
        pass


    def _store_editor(self, key, value):
        editor = self.collection.insert({'editor': key, 'edits': {}})
        self.editors[key]['id'] = str(editor)


    def current_cache_size(self):
        return sum([self.editors[k].get('obs', 0) for k in self.editors])


    def add(self, key, value):
        self.cumulative_n += 1
        if key not in self.editors:
            self.editors[key] = {}
            self.editors[key]['obs'] = 0
            self.editors[key]['edits'] = []

        else:
            id = str(self.editors[key]['obs'])
            self.editors[key]['edits'].append(value)
            self.editors[key]['obs'] += 1


        if self.cumulative_n % 25000 == 0:
            self.empty_all(5.0)


    def retrieve_top_k_editors(self, percentage):
        keys = self.editors.keys()
        obs = []
        for k in keys:
            weight = float(self.editors[k].get('obs', 0)) / self.n
            obs.append((weight, k))
        obs.sort()
        obs.reverse()
        l = int((len(obs) / 100.0) * percentage)
        if l == 0:
            l = 1
        obs = obs[:l]
        obs = [o[1] for o in obs]
        return obs


    def update(self, editor, values):
        self.collection.update({'editor': editor}, {'$pushAll': {'edits': values}}, upsert=True)


    def empty_all(self, percentage):
        self.n = self.current_cache_size()
        if percentage < 100.0:
            keys = self.retrieve_top_k_editors(percentage)
        else:
            keys = self.editors.keys()
        print 'Emptying cache %s time' % self.emptied
        self.emptied += 1
        for key in keys:
            if self.editors[key]['edits'] != {}:
                self.update(key, self.editors[key]['edits'])
                self.editors[key]['edits'] = []
                self.editors[key]['obs'] = 0.0


def debug():
    mongo = db.init_mongo_db('test')
    collection = mongo['test']
    cache = EditorCache(collection)
    import random
    for i in xrange(100000):
        cache.add(str(random.randrange(0, 5)), {'date': 'woensaag', 'article': '3252'})
    cache.empty_all(100)


if __name__ == '__main__':
    debug()
