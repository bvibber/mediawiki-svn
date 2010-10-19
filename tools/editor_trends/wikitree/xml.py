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

from utils import utils
import settings


def convert_html_entities(text):
    return utils.unescape(text)


def extract_text(elem, kwargs):
    if elem != None and elem.text != None:
        return elem.text.decode(settings.ENCODING)
    return None


def retrieve_xml_node(xml_nodes, name):
    for xml_node in xml_nodes:
        if xml_node.tag == name:
            return xml_node
    return None #maybe this should be replaced with an NotFoundError


def read_input(file):
    lines = []
    for line in file:
        lines.append(line)
        if line.find('</page>') > -1:
            yield lines
            '''
            #This looks counter intuitive but Python continues with this call
            after it has finished the yield statement
            '''
            lines = []
    file.close()
