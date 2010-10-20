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
The utils module contains helper functions that will be needed throughout.
It provides functions to read / write data to text and binary files, fix markup
and track error messages.
'''

import re
import htmlentitydefs
import cPickle
import datetime
import codecs
import os
import ctypes

import settings


try:
    import psyco
    psyco.full()
except ImportError:
    pass


RE_ERROR_LOCATION = re.compile('\d+')
RE_NUMERIC_CHARACTER = re.compile('&#?\w+;')


def convert_timestamp_to_date(timestamp):
    return datetime.datetime.strptime(timestamp[:10], settings.DATE_FORMAT)


def convert_timestamp_to_datetime(timestamp):
    return datetime.datetime.strptime(timestamp, settings.DATETIME_FORMAT)


def check_if_process_is_running(pid):
    try:
        if settings.OS == 'Windows':
            PROCESS_TERMINATE = 1
            handle = ctypes.windll.kernel32.OpenProcess(PROCESS_TERMINATE, False, pid)
            ctypes.windll.kernel32.CloseHandle(handle)
            if handle != 0:
                return True
            else:
                return False
        else:
            os.kill(pid, 0)
            return Tru
    except Exception, error:
        print error
        return False


# error tracking related functions
def track_errors(xml_buffer, error, file, messages):
    text = extract_offending_string(xml_buffer.getvalue(), error)

    vars = {}
    vars['file'] = file
    vars['error'] = error
    vars['text'] = text
    #print file, error, text
    key = remove_error_specific_information(error)
    if key not in messages:
        messages[key] = {}
    if messages[key] == {}:
        c = 0
    else:
        counters = messages[key].keys()
        counters.sort()
        counters.reverse()
        c = counters[-1]

    messages[key][c] = {}
    for var in vars:
        messages[key][c][var] = vars[var]

    return messages


def report_error_messages(messages, function):
    store_object(messages, settings.ERROR_MESSAGE_FILE_LOCATION, function.func_name)
    errors = messages.keys()
    for error in errors:
        for key, value in messages[error].iteritems():
            print error, key, value


def remove_error_specific_information(e):
    pos = e.args[0].find('line')
    if pos > -1:
        return e.args[0][:pos]
    else:
        return e.args[0]


def extract_offending_string(text, error):
    '''
    This function determines the string that causes an error when feeding it to
    the XML parser. This is only useful for debugging purposes.
    '''
    location = re.findall(RE_ERROR_LOCATION, error.args[0])
    if location != []:
        location = int(location[0]) - 1
        text = text.split('\n')[location]
        text = text.decode('utf-8')
        return text
    else:
        return ''


# read / write data related functions
def read_data_from_csv(filename, encoding):
    if hasattr(filename, '__call__'):
        filename = construct_filename_from_function(filename)

    fh = open_txt_file(filename, 'r', encoding=encoding)
    for line in fh:
        yield line

    fh.close()


def write_data_to_csv(data, location, function, encoding):
    filename = construct_filename_from_function(function, '.csv')
    fh = open_txt_file(location, filename, 'a', encoding=encoding)
    keys = data.keys()
    for key in keys:
        fh.write('%s' % key)
        for obs in data[key]:
            if getattr(obs, '__iter__', False):
                for o in obs:
                    fh.write('\t%s' % o)
            else:
                fh.write('\t%s' % (obs))
        fh.write('\n')
    fh.close()


def open_txt_file(location, filename, mode, encoding):
    return codecs.open(location+filename, mode, encoding=encoding)

def construct_filename_from_function(function, extension):
    return function.func_name + extension

def check_file_exists(location, filename):
    if hasattr(filename, '__call__'):
        filename = construct_filename_from_function(filename, '.bin') 
    if os.path.exists(location + filename):
        return True
    else:
        return False


def store_object(object, location, filename):
    if hasattr(filename, '__call__'):
        filename = construct_filename_from_function(filename, '.bin')
    if not filename.endswith('.bin'):
        filename = filename + '.bin'
    fh = open(location + filename, 'wb')
    cPickle.dump(object, fh)
    fh.close()


def load_object(location, filename):
    if hasattr(filename, '__call__'):
        filename = construct_filename_from_function(filename, '.bin')    
    if not filename.endswith('.bin'):
        filename = filename + '.bin'
    fh = open(location + filename, 'rb')
    obj = cPickle.load(fh)
    fh.close()
    return obj


def clean_string(string):
    string = string.replace('\n', '')
    return string


def create_dict_from_csv_file(filename, encoding):
    d = {}
    for line in read_data_from_csv(filename, encoding):
        line = clean_string(line)
        value, key = line.split('\t')
        d[key] = value

    return d


def retrieve_file_list(location, extension):
    all_files = os.listdir(location)
    if not extension.startswith('.'):
        extension = '.' + extension
    files = []
    for file in all_files:
        if file.endswith(extension):
            files.append(file)

    return files


# Progress bar related functions
def update_progressbar(pbar, queue):
    '''
    Updates the progressbar by determining how much work is left in a queue
    '''
    x = pbar.maxval - queue.qsize()
    '''
    Currently, calling the pbar.update function gives the following error:
    File "build\bdist.win32\egg\progressbar.py", line 352, in update
        self.fd.write(self._format_line() + '\r')
    ValueError: I/O operation on closed file
    Not sure how to fix this, that's why the line is commented.
    '''
    #pbar.update(x)


def humanize_time_difference(seconds_elapsed):
    """
    Returns a humanized string representing time difference.
    It will only output the first two time units, so days and
    hours, or hours and minutes, except when there are only
    seconds.
    """
    seconds_elapsed = int(seconds_elapsed)
    humanized_time = {}
    time_units = [('days', 86400), ('hours', 3600), ('minutes', 60), ('seconds', 1)]
    for time, unit in time_units:
        dt = seconds_elapsed / unit
        if dt > 0:
            humanized_time[time] = dt
            seconds_elapsed = seconds_elapsed - (unit * humanized_time[time])
    #humanized_time['seconds'] = seconds_elapsed

    x = 0
    if len(humanized_time) == 1:
        return '%s %s' % (humanized_time['seconds'], 'seconds')
    else:
        obs = []
        for time, unit in time_units:
            if time in humanized_time:
                unit = humanized_time.get(time, None)
                if humanized_time[time] == 1:
                    time = time[:-1]
                obs.append((time, unit))
                x += 1
                if x == 2:
                    return '%s %s and %s %s' % (obs[0][1], obs[0][0], obs[1][1], obs[1][0])


def debug():
    dt = humanize_time_difference(64)
    print dt

if __name__ == '__main__':
    debug()
