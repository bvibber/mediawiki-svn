/*
 * Six degrees of Wikipedia: Server.
 * This source code is released into the public domain.
 *
 * Linux version, modified to use AF_UNIX socket instead of doors 2006-09-20.
 */

#pragma ident "@(#)linksd.cc	1.3 07/01/24 14:14:48"

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/un.h>

#include <iostream>
#include <map>
#include <list>
#include <set>
#include <vector>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <cassert>
#include <queue>
#include <string>
#include <algorithm>
#include <utility>
#include <fstream>
#include <sstream>
#include <exception>

#include <unistd.h>
#include <mysql.h>
#include <fcntl.h>

#include "linksc.h"

static void handle_request(int s, char *argp, size_t argz);

std::vector<std::string> names;
std::map<std::string, int> ids;
std::vector<int> isdate; 
std::vector<std::vector<int> > adjacency;
 
/*
 * Is this article a date?
 */
bool
is_date(std::string name) {
struct std::tm 		res;
std::string::size_type	t;
bool			a, b;
	while ((t = name.find_first_of("_")) != std::string::npos) 
		name[t] = ' ';
	std::memset(&res, 0, sizeof(res));
	a = strptime(name.c_str(), "%b %d", &res) != NULL;
	std::memset(&res, 0, sizeof(res));
	b = strptime(name.c_str(), "%Y", &res) != NULL;

	if (a || (b && name.length() <= 4 &&
	     /* wtf... strptime("%Y") on Solaris will return a "valid" year for
              * *any* string of four or less characters */
	     name.find_first_not_of("0123456789") == std::string::npos))
		return true;
	else	return false;
}

/*
 * I didn't write this function.  I don't even know if it works correctly :-).  However,
 * it seems to return the right results.  (Credit: ZorbaTHut @ EFnet #c++)
 */
std::vector<int>
findPath(int src, int dst, bool ign_date) {
std::vector<int>	back;
std::deque<int>		next;

	back.clear();
	back.resize(adjacency.size(), -1);
	next.clear();
	back.at(src) = -2;
	next.push_back(src);

	while (next.size()) {
	int	ts = next.at(0);
		next.pop_front();

		if (ts == dst) {
		std::vector<int>	path;
		int 			lastlink = back[dst];
			path.push_back(dst);

			while (lastlink != -2) {
				assert(lastlink != -1);
				path.push_back(lastlink);
				lastlink = back.at(lastlink);
			}
			std::reverse(path.begin(), path.end());
			return path;
		}

		for (int i = 0; i < adjacency.at(ts).size(); i++) {
			if (ign_date && isdate[adjacency.at(ts).at(i)])
				continue;
			if (back.at(adjacency.at(ts).at(i)) == -1) {
				back.at(adjacency.at(ts).at(i)) = ts;
				next.push_back(adjacency.at(ts).at(i));
			}
		}
	}
	return std::vector<int>();
}

static void *
start_request(void *arg)
{
int			s = (int)(uintptr_t)arg;
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
	handle_request(s, &buf[0], buf.size());
	close(s);
	return 0;
}

static void
handle_request(int s, char *argp, size_t argz)
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
	if (ids.find(from) == ids.end()) {
		char err[4];
		*(uint32_t *)err = 0;
		result.resize(4);
		result.assign(err, err + 4);
		l = result.size();
		write(s, &l, sizeof(l));
		write(s, &result[0], result.size());
		return;
	}
	fromid = ids[from];
	if (ids.find(to) == ids.end()) {
		char err[4];
		*(uint32_t *)err = 1;
		result.resize(4);
		result.assign(err, err + 4);
		l = result.size();
		write(s, &l, sizeof(l));
		write(s, &result[0], result.size());
		return;
	}
	toid = ids[to];
	std::vector<int> links = findPath(fromid, toid, ign_date);
	result.resize(4);
	*(uint32_t *)&result[0] = 3;
	for (std::vector<int>::const_iterator it = links.begin(), end = links.end(); it != end; ++it)
	{
		std::string s = names.at(*it);
		char len[4];
		*(uint32_t*)len = s.size();
		result.insert(result.end(), len, len + 4);
		result.insert(result.end(), s.begin(), s.end());
	}
	l = result.size();
	write(s, &l, sizeof(l));
	write(s, &result[0], result.size());
}

int
main(int argc, char *argv[])
{
	std::ifstream in(CACHE);
	std::string l;
	std::printf("retrieving links table...\n");
	while (std::getline(in, l)) {
		if (l.empty())
			break;
		int l_from, l_to;
		std::istringstream str(l);
		str >> l_from >> l_to;
		if (l_from >= adjacency.size())
			adjacency.resize(l_from + 1);
		std::vector<int>& l = adjacency.at(l_from);
		l.insert(l.end(), l_to);
	}

	std::printf("ok\n");
	std::printf("retrieving titles...\n");
	while (std::getline(in, l)) {
		int l_id;
		std::string l_ttl;
		std::istringstream str(l);
		str >> l_id;
		std::getline(str, l_ttl);
		while (!l_ttl.empty() && l_ttl[0] == ' ')
			l_ttl.erase(l_ttl.begin());
		if (l_id >= names.size()) {
			names.resize(l_id + 1);
			isdate.resize(l_id + 1);
		}
		if (is_date(l_ttl))
			isdate[l_id] = 1;
		else	isdate[l_id] = 0;
		names.at(l_id) = l_ttl;
		ids[l_ttl] = l_id;
	}
	std::printf("ok, %d links, %d titles\n", adjacency.size(), names.size());
	std::printf("filtering links...\n");
	for (int i = 1; i < adjacency.size(); ++i) {
		if (i >= names.size() || names[i].empty()) {
			adjacency.at(i).clear();
			continue;
		}
		for (std::vector<int>::iterator it = adjacency[i].begin(); it != adjacency[i].end();)
			if (*it >= names.size() || names[*it].empty())
				it = adjacency.at(i).erase(it);
			else ++it;
	}
	std::printf("ok\n");

	int did;
	struct sockaddr_un addr;

	if ((did = socket(AF_LOCAL, SOCK_STREAM, 0)) == -1) {
		std::perror("socket");
		std::exit(1);
	}
	unlink(DOOR);
	std::memset(&addr, 0, sizeof(addr));
	addr.sun_family = AF_LOCAL;
	strncpy(addr.sun_path, DOOR, sizeof(addr.sun_path));
	if (bind(did, (struct sockaddr *)&addr, sizeof(addr)) == -1) {
		std::perror("bind");
		std::exit(1);
	}

	if (listen(did, 5) == -1) {
		std::perror("listen");
		std::exit(1);
	}

	for (;;) {
	int			cli;
	struct sockaddr_un	cliaddr;
	size_t			clilen;
		clilen = sizeof(addr);
		std::memset(&cliaddr, 0, clilen);
		if ((cli = accept(did, (struct sockaddr *)&cliaddr, (socklen_t *)&clilen)) == -1) {
			std::perror("accept");
			std::exit(1);
		}

		pthread_attr_t attr;
		pthread_attr_init(&attr);
		pthread_attr_setdetachstate(&attr, PTHREAD_CREATE_DETACHED);
		pthread_t tid;

		if (pthread_create(&tid, &attr, start_request, (void *)cli) == -1) {
			std::perror("pthread_create");
			std::exit(1);
		}
	}
	close(did);
}
