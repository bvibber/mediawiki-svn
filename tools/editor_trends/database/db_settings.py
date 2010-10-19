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

'''
This is a settings file that contains the layout of different tables. The main
key will be used as the tablename while it,s values contain tuples containing
fieldname and datatype This is only be used for sqlite.
'''
CONTRIBUTOR_TABLE = {'contributors': []}
CONTRIBUTOR_TABLE['contributors'].append(('contributor', 'VARCHAR(64)'))
CONTRIBUTOR_TABLE['contributors'].append(('article', 'INTEGER'))
CONTRIBUTOR_TABLE['contributors'].append(('timestamp', 'TEXT'))
CONTRIBUTOR_TABLE['contributors'].append(('bot', 'INTEGER'))

BOT_TABLE = {'bots': []}
BOT_TABLE['bots'].append(('language', 'VARCHAR(12)'))
BOT_TABLE['bots'].append(('name', 'VARCHAR(64)'))
BOT_TABLE['bots'].append(('edits_namespace_a', 'INTEGER'))
BOT_TABLE['bots'].append(('edits_namespace_x', 'INTEGER'))
BOT_TABLE['bots'].append(('rank_now', 'INTEGER'))
BOT_TABLE['bots'].append(('rank_prev', 'INTEGER'))
BOT_TABLE['bots'].append(('first_date', 'TEXT'))
BOT_TABLE['bots'].append(('days_first', 'INTEGER'))
BOT_TABLE['bots'].append(('last_date', 'TEXT'))
BOT_TABLE['bots'].append(('days_last', 'INTEGER'))
