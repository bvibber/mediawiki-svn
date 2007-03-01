/*
 * Six degrees of Wikipedia: Server (request handler).
 * This source code is released into the public domain.
 */

#include <cstdio>
#include <cstdlib>
#include <vector>
#include <iostream>

#include <sys/types.h>
#include <inttypes.h>
#include <pthread.h>

#include "request_dispatcher.h"
#include "pathfinder.h"

request_dispatcher::request_dispatcher(pathfinder &f)
	: finder(f)
{
}

struct request_data {
	int s;
	request_dispatcher *d;
};

void
request_dispatcher::dispatch(int s)
{		
	pthread_attr_t attr;
	pthread_attr_init(&attr);
	pthread_attr_setdetachstate(&attr, PTHREAD_CREATE_DETACHED);
	pthread_t tid;

	request_data *rd = new request_data;
	rd->s = s;
	rd->d = this;
	if (pthread_create(&tid, &attr, start_request, rd) == -1) {
		std::perror("pthread_create");
		std::exit(1);
	}
}

void *
request_dispatcher::start_request(void *arg)
{
	request_data *rd = static_cast<request_data *>(arg);
	int s = rd->s;
	uint32_t		sz;
	int			i;
	std::vector<char>	buf;
	if ((i = read(s, &sz, sizeof(sz))) < sizeof(sz)) {
		if (i == -1)
			std::perror("read");
		close(s);
		return 0;
	}

	try {
		buf.resize(sz);
	} catch (std::bad_alloc& e) {
		std::cerr << "out of memory for client request!\n";
		close(s);
		return 0;
	}

	read(s, &buf[0], sz);
	rd->d->handle_request(s, &buf[0], buf.size());
	close(s);
	return 0;
}

void
request_dispatcher::handle_request(int s, char *argp, size_t argz)
{
	/*
	 * Data format:
	 *   "<uint32>From<uint32>To"
	 * The ints contain the size of from and to, respectively.
	 *
	 * Result format:
	 *   "<char><text...>"
	 * <char> might be:
	 *   0: From article did not exist
	 *   1: To article did not exist
	 *   2: No search result was found.
	 *   3: Some other error occured.
	 *   4: Result okay, data follows.
	 *
	 * In case of 3, the rest of the buffer contains a series of items:
	 *   "<uint32><text>"
	 * where uint32 is the length of the next item, and text is the article name.
	 */
	std::string from, to;
	std::vector<char> result;
	bool ign_date = false;
	char *p = argp;
	uint32_t l;
	if (argz < 8) {
		char err[4];
		*(uint32_t *)err = 3;
		result.resize(4);
		result.assign(err, err + 4);
		l = result.size();
		write(s, &l, sizeof(l));
		write(s, &result[0], result.size());
		return;
	}
	l = *(uint32_t *)argp;
	argz -= 4;
	argp += 4;
	if (l > argz) {
		char err[4];
		*(uint32_t *)err = 3;
		result.resize(4);
		result.assign(err, err + 4);
		l = result.size();
		write(s, &l, sizeof(l));
		write(s, &result[0], result.size());
		return;
	}
	/* This means ignore dates - it's ugly, but avoids an API change */
	if (*argp == '#') {
		from.assign(argp + 1, argp + l);
		ign_date = true;
	} else
		from.assign(argp, argp + l);
	argp += l;
	argz -= l;
	if (argz < 4) {
		char err[4];
		*(uint32_t *)err = 3;
		result.resize(4);
		result.assign(err, err + 4);
		l = result.size();
		write(s, &l, sizeof(l));
		write(s, &result[0], result.size());
		return;
	}
	l = *(uint32_t *)argp;
	argp += 4;
	if (l > argz) {
		char err[4];
		*(uint32_t *)err = 3;
		result.resize(4);
		result.assign(err, err + 4);
		l = result.size();
		write(s, &l, sizeof(l));
		write(s, &result[0], result.size());
		return;
	}
	to.assign(argp, argp + l);
	int fromid, toid;
	boost::optional<int> fromid_o = finder.id_for_name(from);
	if (!fromid_o) {
		char err[4];
		*(uint32_t *)err = 0;
		result.resize(4);
		result.assign(err, err + 4);
		l = result.size();
		write(s, &l, sizeof(l));
		write(s, &result[0], result.size());
		return;
	}
	fromid = *fromid_o;

	boost::optional<int> toid_o = finder.id_for_name(to);
	if (!toid_o) {
		char err[4];
		*(uint32_t *)err = 1;
		result.resize(4);
		result.assign(err, err + 4);
		l = result.size();
		write(s, &l, sizeof(l));
		write(s, &result[0], result.size());
		return;
	}
	toid = *toid_o;

	std::vector<int> links = finder.solve(fromid, toid, ign_date);
	result.resize(4);
	*(uint32_t *)&result[0] = 3;
	for (std::vector<int>::const_iterator it = links.begin(), end = links.end(); it != end; ++it)
	{
		std::string const &s = *finder.name_for_id(*it);
		char len[4];
		*(uint32_t*)len = s.size();
		result.insert(result.end(), len, len + 4);
		result.insert(result.end(), s.begin(), s.end());
	}
	l = result.size();
	write(s, &l, sizeof(l));
	write(s, &result[0], result.size());
}

	
