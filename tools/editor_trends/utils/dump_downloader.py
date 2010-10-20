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
import sys
import urllib2
import httplib

import progressbar

import utils
import settings


def determine_remote_filesize(url, filename):
    '''
    @url is the full path of the file to be downloaded
    @filename is the name of the file to be downloaded
    '''
    conn = httplib.HTTPConnection(url)
    conn.request('HEAD', filename)
    res = conn.getresponse()
    if res.status == 200:
        return res.getheader('content-length', -1)
    else:
        return - 1


def download_wp_dump(url, filename, location, pbar):
    '''
    This is a very simple replacement for wget and curl because Windows does
    support these tools. 
    @url location of the file to be downloaded
    @filename name of the file to be downloaded
    @location indicates where to store the file locally
    @pbar is an instance of progressbar.ProgressBar()
    '''
    chunk = 4096
    fh = utils.open_txt_file(location, filename, 'w', settings.ENCODING)
    req = urllib2.Request(url + filename)
    filesize = determine_remote_filesize(url, filename)
    if filesize != -1:
        pbar(maxval=filesize).start()
    try:
        response = urllib2.urlopen(req)
        i = 0
        while True:
            data = response.read(chunk)
            if not data:
                print 'Finished downloading %s%s.' % (url, filename)
                break
            f.write(data)

            if pbar:
                pbar.update(i * chunk)
            i += 1
    except URLError, error:
        print 'Reason: %s' % error.reason
    except HTTPError, error:
        print 'Error: %s' % error.code


if __name__ == '__main__':
    pbar = progressbar.ProgressBar()
    download_wp_dump('http://download.wikimedia.org/enwiki/latest', 'bla.xml', settings.XML_FILE_LOCATION, pbar)
