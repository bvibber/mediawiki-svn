/* $Id$ */
/*
 * Six degrees of Wikipedia: Server.
 * This source code is released into the public domain.
 */

#include <iostream>

#include <boost/format.hpp>

#include <poll.h>

#include "io.h"

void
poller::read(int s, boost::function<void (void)> f)
{
	fdtable[s].readf = f;
	fds.push_back(std::make_pair(s, readio));
}

void
poller::write(int s, boost::function<void (void)> f)
{
	fdtable[s].writef = f;
	fds.push_back(std::make_pair(s, writeio));
}

void
poller::run(void)
{
	for (;;) {
		do_poll();
	}
}

void
poller::do_poll(void)
{
	std::vector<pollfd> pfds(fds.size());
	for (std::size_t i = 0; i < fds.size(); ++i) {
		pfds[i].fd = fds[i].first;
		pfds[i].events = (fds[i].second == readio ? POLLRDNORM : POLLWRNORM);
	}

	if (poll(&pfds[0], pfds.size(), -1) < 0) {
		std::perror("poll");
		std::exit(1);
	}

	std::vector<std::pair<int, io_type> > tmp;
	tmp.swap(fds);

	for (std::size_t i = 0; i < pfds.size(); ++i) {
		if (pfds[i].revents == 0) {
			fds.push_back(tmp[i]);
			continue;
		}

		int fd = pfds[i].fd;
		if (pfds[i].revents & POLLRDNORM) {
			fdtable[fd].readf();
		}

		if (pfds[i].revents & POLLWRNORM) {
			fdtable[fd].writef();
		}
	}
}

void
dispatcher::append(boost::function<void (void)> f)
{
	funcs.push_back(f);
}

void
dispatcher::run(void)
{
	for (std::size_t i = 0; i < funcs.size(); ++i)
		funcs[i]();
	funcs.clear();
}

poller::poller(void)
{
	int nfds = getdtablesize();
	std::cout << boost::format("dtablesize = %d\n") % nfds;
	fdtable.resize(nfds);
}


 
