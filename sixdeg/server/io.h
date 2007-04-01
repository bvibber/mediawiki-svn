/*
 * Six degrees of Wikipedia: Server.
 * This source code is released into the public domain.
 */

#ifndef IO_H
#define IO_H

#include <vector>
#include <utility>

#include <boost/noncopyable.hpp>
#include <boost/function.hpp>

struct fde {
	int fd;
	boost::function<void (void)> readf;
	boost::function<void (void)> writef;
};

struct dispatcher {
	void append(boost::function<void (void)>);
	void run(void);

private:
	std::vector<boost::function<void (void)> > funcs;
};

struct poller : boost::noncopyable {
	poller(void);

	void read(int s, boost::function<void (void)>);
	void write(int s, boost::function<void (void)>);
	void run(void);

private:
	void do_poll(void);

	enum io_type { readio, writeio };
	std::vector<std::pair<int, io_type> > fds;
	std::vector<fde> fdtable;

	dispatcher d;
};

#endif	/* !IO_H */ 
