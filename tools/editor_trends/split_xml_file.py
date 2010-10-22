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

import xml.etree.cElementTree as cElementTree
import codecs
import utils
import re
import settings

try:
    import psyco
    psyco.full()
except ImportError:
    pass


RE_NUMERIC_CHARACTER = re.compile('&#(\d+);')


def remove_numeric_character_references(text):
    return re.sub(RE_NUMERIC_CHARACTER, lenient_deccharref, text).encode('utf-8')


def lenient_deccharref(m):
    return unichr(int(m.group(1)))


def remove_namespace(element, namespace):
    '''Remove namespace from the document.'''
    ns = u'{%s}' % namespace
    nsl = len(ns)
    for elem in element.getiterator():
        if elem.tag.startswith(ns):
            elem.tag = elem.tag[nsl:]
    return element


def parse_comments(xml, function):
    revisions = xml.findall('revision')
    for revision in revisions:
        comment = revision.find('comment')
        timestamp = revision.find('timestamp').text

#            text1 = remove_ascii_control_characters(text)
#            text2 = remove_numeric_character_references(text)
#            text3 = convert_html_entities(text)

        if comment != None and comment.text != None:
            comment.text = function(comment.text)
    return xml


def write_xml_file(element, fh, counter, language):
    '''Get file handle and write xml element to file'''
    size = len(cElementTree.tostring(element))
    fh, counter = create_xml_file_handle(fh, counter, size)
    fh.write(cElementTree.tostring(element))
    fh.write('\n')
    return fh, counter


def create_xml_file_handle(fh, counter, size):
    '''Create file handle if none is supplied or if file size > max file size.'''
    if not fh:
        counter = 0
        fh = codecs.open(settings.LOCATION + '/' + language + '/' + str(counter) + '.xml', 'w', encoding=settings.ENCODING)
        return fh, counter
    elif (fh.tell() + size) > settings.MAX_XML_FILE_SIZE:
        print 'Created chunk %s' % counter
        fh.close
        counter += 1
        fh = codecs.open(settings.LOCATION + '/' + language + '/' + str(counter) + '.xml', 'w', encoding=settings.ENCODING)
        return fh, counter
    else:
        return fh, counter


def split_xml(language):
    '''Reads xml file and splits it in N chunks'''
    result = utils.create_directory(language)
    if not result:
        return

    fh = None
    counter = None
    tag = '{%s}page' % settings.NAME_SPACE

    context = cElementTree.iterparse(settings.XML_FILE, events=('start', 'end'))
    context = iter(context)
    event, root = context.next() # get the root element of the XML doc

    for event, elem in context:
        if event == 'end':
            if elem.tag == tag:
                elem = remove_namespace(elem, settings.NAME_SPACE)
                elem = parse_comments(elem, remove_numeric_character_references)
                #elem = parse_comments(elem, convert_html_entities)
                #elem = parse_comments(elem, remove_ascii_control_characters)
                fh, counter = write_xml_file(elem, fh, counter, language)
                #print cElementTree.tostring(elem)
                root.clear()  # when done parsing a section clear the tree to safe memory


if __name__ == "__main__":
    split_xml('enwiki')
