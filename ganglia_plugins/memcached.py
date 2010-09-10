#!/usr/bin/env python 
# encoding: utf-8
"""
memcached.py
Gather memcached data for ganglia.
Created by Ryan Lane on 2010-09-07.
"""

import memcache, math

stats_cache = {}
items_cache = {}
stats_descriptions = { 'curr_items': 'Number of items stored', 'bytes': 'Number of bytes used to store items', 'curr_connectons': 'Number of clients connected', 'global_hitrate': 'Hitrate', 'evictions': 'Number of evictions', 'threads': 'Number of worker threads requested', 'listen_disabled_num': 'Number of times connection limit hit', 'cmd_flush': 'Number of times flush_all called' }
items_descriptions = { 'median_age': 'Median age of items in the cache', 'minimum_age': 'Minimum age of items in the cache', 'median_evictions': 'Median number of evictions before expiration', 'maximum_evictions': 'Maximum number of evictions before expiration', 'median_outofmemory': 'Median number of out of memory errors', 'maximum_outofmemory': 'Maximum number out of memory errors' }
host = 'localhost'
port = '11211'

def metric_init(params):
	global stats_descriptions, items_descriptions
	metrics = []

	get_stats()

	for metric in stats_descriptions.keys():
		metric_properties = {
			'name': metric,
			'call_back': get_stats_value,
			'time_max': 15, 
			'value_type': 'uint',
			'units': 'N',
			'slope': 'positive',
			'format': '%u',
			'description': stats_descriptions[metric]
		}
		metrics.append(metric_properties)

	for metric in items_descriptions.keys():
		metric_properties = {
			'name': metric,
			'call_back': get_items_value,
			'time_max': 15, 
			'value_type': 'uint',
			'units': 'N',
			'slope': 'positive',
			'format': '%u',
			'description': items_descriptions[metric]
		}
		metrics.append(metric_properties)

	return metrics

def get_stats_value(metric):
	global stats_cache

	return int(stats_cache[metric])

def get_items_value(metric):
	global items_cache

	return int(items_cache[metric])

def get_stats():
	global stats_cache, items_cache, host, port

	mc = memcache.Client([host + ':' + port])

	stats = mc.get_stats()
	stats_cache = stats[0][1]
	hits = float(stats_cache['get_hits'])
	misses = float(stats_cache['get_misses'])
	stats_cache['global_hitrate'] = hits / ( hits + misses )

	items = mc.get_stats('items')
	items = items[0][1]

	total_age = 0
	min_age = -1
	total_evictions = 0
	max_evictions = 0
	total_outofmemory = 0
	max_outofmemory = 0
	items_len = len(items)
	for keytriple, val in items.iteritems():
		val = int(val)
		(ignored, slabclass, key) = keytriple.split(':')
		if key == "age":
			total_age = total_age + val
			if val < min_age or min_age == -1:
				min_age = val
		if key == "evicted":
			total_evictions = total_evictions + val
			if val > max_evictions:
				max_evictions = val
		if key == "outofmemory":
			total_outofmemory = total_outofmemory + val
			if val > max_outofmemory:
				max_outofmemory = val

	items_cache = { 'median_age': total_age / items_len, 'minimum_age': min_age, 'median_evictions': total_evictions / items_len,
			'maximum_evictions': max_evictions, 'median_outofmemory': total_outofmemory / items_len,
			'maximum_outofmemory': max_outofmemory }

def metric_cleanup():
	pass

if __name__ == '__main__':
	metrics = metric_init({})
	for metric_properties in metrics:
		print "\n\tmetric {\n\t\tname = '%(name)s'\n\t\ttitle = '%(description)s'\n\t}" % metric_properties
