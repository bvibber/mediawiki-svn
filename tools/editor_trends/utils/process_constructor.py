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

from multiprocessing import Process, Queue
from Queue import Empty

import settings
import utils
import models

#3rd party dependency
import progressbar


def build_scaffolding(load_input_queue, main, obj, result_processor=False, result_queue=False, **kwargs):
    '''
    This a generic producer/consumer process launcher. It can launch two types
    of processes:
    a) Processes that take a task from a queue and do their thing
    b) Processes that take a task from a queue and put the result in the
    result_queue.
    If result_queue is False then a) is assumed.

    @load_input_queue is a function that is used to insert jobs into queue

    @main is the function that will process the input_queue

    @obj can be a pickled object or an enumerable variable that will be loaded
    into the input_queue

    @result_queue, if set to True will become a true queue and will be provided
    to main whose job it is to fill with new tasks. If False then this variable
    is ignored.

    @result_processor, name of the function to process the @result_queue

    @kwargs is a dictionary with optional variables. Used to supply to main
    '''

    input_queue = Queue()
    if result_queue:
        result_queue = Queue()

    load_input_queue(input_queue, obj, poison_pill=True)

    if settings.PROGRESS_BAR:
        pbar = progressbar.ProgressBar(maxval=input_queue.qsize()).start()
    else:
        pbar = False


    input_processes = [models.ProcessInputQueue(main, input_queue, result_queue,
                        **kwargs) for i in xrange(settings.NUMBER_OF_PROCESSES)]

    for input_process in input_processes:
        input_process.start()
    pids = [p.pid for p in input_processes]
    kwargs['pids'] = pids
    
    
    
    if result_queue:
        result_processes = [models.ProcessResultQueue(result_processor,
                result_queue, **kwargs) for i in xrange(1)]
        for result_process in result_processes:
            result_process.start()

    for input_process in input_processes:
        print 'Waiting for input process to finish'
        input_process.join()
        print 'Input process finished'

    if result_queue:
        for result_process in result_processes:
            print 'Waiting for result process to finish.'
            result_process.join()
            print 'Result process finished'

    if pbar:
        pbar.finish()
        print 'Total elapsed time: %s.' % (utils.humanize_time_difference(pbar.seconds_elapsed))


def load_queue(input_queue, obj, poison_pill=False):
    '''
    @input_queue should be an instance of multiprocessing.Queue

    @obj either pickled or enumerable variable that contains the tasks

    @returns: queue with tasks
    '''

    if isinstance(obj, type(list)):
        data = utils.load_object(obj)
    else:
        data = obj
    for d in data:
        input_queue.put(d)

    if poison_pill:
        for p in xrange(settings.NUMBER_OF_PROCESSES):
            input_queue.put(None)
    return input_queue
