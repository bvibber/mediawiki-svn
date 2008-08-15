/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
/* $Id$ */

#ifndef UTIL_H
#define UTIL_H

#include	<deque>

#include	<pthread.h>

struct lock {
	lock(pthread_mutex_t &mtx)
		: mtx_(mtx)
	{
		pthread_mutex_lock(&mtx_);
	}

	~lock() {
		pthread_mutex_unlock(&mtx_);
	}

private:
	pthread_mutex_t &mtx_;
};

template<typename T>
struct work_queue {
	void add_work(T const &work) {
		pthread_mutex_lock(&wq_mutex);
		wq_work.push_back(work);
		pthread_cond_signal(&wq_cond);
		pthread_mutex_unlock(&wq_mutex);
	}

	T wait() {
		T work;

		pthread_mutex_lock(&wq_mutex);
		while (wq_work.empty())
			pthread_cond_wait(&wq_cond, &wq_mutex);

		work = wq_work.front();
		wq_work.pop_front();
		pthread_mutex_unlock(&wq_mutex);
		return work;
	}

	work_queue() {
		pthread_mutex_init(&wq_mutex, NULL);
		pthread_cond_init(&wq_cond, NULL);
	}

private:
	std::deque<T>	wq_work;
	pthread_cond_t	wq_cond;
	pthread_mutex_t	wq_mutex;
};

#endif	/* !UTIL_H */
