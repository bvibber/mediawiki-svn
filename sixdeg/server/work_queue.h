/* $Id$ */
/*
 * Six degrees of Wikipedia: Server.
 * This source code is released into the public domain.
 */

#ifndef WORK_QUEUE_H
#define WORK_QUEUE_H

#include <deque>

#include <boost/noncopyable.hpp>
#include <boost/function.hpp>

#include <pthread.h>

struct work_queue : boost::noncopyable {
	typedef boost::function<void (void)> work_t;

	work_queue(int);

	void schedule(work_t);

private:
	static void *worker(void *);

	pthread_mutex_t lock;
	pthread_cond_t cond;
	std::deque<work_t> work;
};

#endif	/* !WORK_QUEUE_H */
 
