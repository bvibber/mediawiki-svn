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

import os
import sys
import urllib2
import httplib

import progressbar

import settings
import utils



def determine_remote_filesize(url, filename):
    '''
    @url is the full path of the file to be downloaded
    @filename is the name of the file to be downloaded
    '''
    if url.startswith('http://'):
        url = url[7:]
    conn = httplib.HTTPConnection(url, 80)
    conn.request('HEAD', filename)
    res = conn.getresponse()
    conn.close()
    if res.status == 200:
        return int(res.getheader('content-length', -1))
    else:
        return - 1


def download_wp_dump(domain, path, filename, location, filemode, pbar):
    '''
    This is a very simple replacement for wget and curl because Windows does
    support these tools. 
    @url location of the file to be downloaded
    @filename name of the file to be downloaded
    @location indicates where to store the file locally
    @filemode indicates whether we are downloading a binary or ascii file.
    @pbar is an instance of progressbar.ProgressBar()
    '''
    chunk = 4096
    if filemode == 'w':
        fh = utils.open_txt_file(location, filename, filemode, settings.ENCODING)
    else:
        fh = utils.open_binary_file(location, filename, filemode)

    filesize = determine_remote_filesize(domain, path + filename)


    if filesize != -1 and pbar:
        widgets = ['%s: ' % filename, progressbar.Percentage(), ' ',
                   progressbar.Bar(marker=progressbar.RotatingMarker()),' ', 
                   progressbar.ETA(), ' ', progressbar.FileTransferSpeed()]

        pbar = progressbar.ProgressBar(widgets=widgets,maxval=filesize).start()
    else:
        pbar = False

    req = urllib2.Request(domain + path + filename)
    try:
        response = urllib2.urlopen(req)
        while True:
            data = response.read(chunk)
            if not data:
                print 'Finished downloading %s%s%s.' % (domain, path, filename)
                break
            fh.write(data)

            if pbar:
                filesize -= chunk
                if filesize < 0:
                    chunk = chunk + filesize
                pbar.update(pbar.currval + chunk)

    except urllib2.URLError, error:
        print 'Reason: %s' % error.reason
    except urllib2.HTTPError, error:
        print 'Error: %s' % error.code
    finally:
        fh.close()


if __name__ == '__main__':
    pbar = progressbar.ProgressBar()
    download_wp_dump('http://download.wikimedia.org/enwiki/latest', 'bla.xml', settings.XML_FILE_LOCATION, pbar)
