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

import sqlite3 as sqlite
from pymongo import Connection


import settings
from database import db_settings


def init_mongo_db(db):
    connection = Connection()
    db = connection[db]
    return db


def remove_documents_from_mongo_db(collection, ids):
    collection.remove(ids)


def add_index_to_collection(db, collection, key):
    '''
    @db is the name of the mongodb 
    @collection is the name of the 'table' in mongodb
    @key name of the field to create the index
    '''
    
    mongo = init_mongo_db(db)
    collection = mongo[collection]
    mongo.collection.create_index(key)
    mongo.collection.ensure_index(key)


def init_database(db=None):
    '''
    This function initializes the connection with a sqlite db.
    If the database already exists then it returns False to indicate
    that the db already exists, else it returns True to indicate
    that it's an empty database without tables.
    '''
    if db == None:
        db = settings.DATABASE_NAME

    return sqlite.connect(db, check_same_thread=False)


def create_tables(cursor, tables):
    '''
    Tables is expected to be a dictionary, with key
    table name and value another dictionary. This second
    dictionary contains variable names and datatypes.
    '''
    for table in tables:
        vars = '('
        for var, datatype in tables[table]:
            vars = vars + '%s %s,' % (var, datatype)
        vars = vars[:-1]
        vars = vars + ')'
        cursor.execute('CREATE TABLE IF NOT EXISTS ? ?' % (table, vars))


def debug():
    connection = init_database()
    cursor = connection.cursor()
    create_tables(cursor, settings.TABLES)


if __name__ == '__main__':
    debug()
