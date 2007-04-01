/* $Id$ */
/*
 * Six degrees of Wikipedia: Server.
 * This source code is released into the public domain.
 */

#include <iostream>

#include <boost/format.hpp>

#include "work_queue.h"

work_queue::work_queue(int nthreads)
{
	std::cout << boost::format("nthreads = %d\n") % nthreads;

	pthread_mutex_init(&lock, NULL);
	pthread_cond_init(&cond, NULL);

	pthread_t tid;
	while (nthreads--) {
		pthread_create(&tid, NULL, &work_queue::worker, this);
	}
}

void *
work_queue::worker(void *t)
{
	work_queue *q = static_cast<work_queue *>(t);

	for (;;) {
		std::deque<work_queue::work_t> work;

		pthread_mutex_lock(&q->lock);
		if (q->work.empty())
			pthread_cond_wait(&q->cond, &q->lock);

		work.swap(q->work);
		pthread_mutex_unlock(&q->lock);
		for (std::deque<work_queue::work_t>::iterator it = work.begin(), end = work.end();
		     it != end; ++it)
		{
			(*it)();
		}
	}
	return NULL;
}

void
work_queue::schedule(work_t w)
{
	pthread_mutex_lock(&lock);
	work.push_back(w);
	pthread_cond_signal(&cond);
	pthread_mutex_unlock(&lock);
}
