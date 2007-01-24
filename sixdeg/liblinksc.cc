/*
 * Six degrees of Wikipedia: Client library.
 */

#pragma ident "@(#)liblinksc.cc	1.3 06/09/20 00:47:26"

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/un.h>
#include <sys/stat.h>
#include <sys/mman.h>

#include <iostream>
#include <cstdio>
#include <cstdlib>
#include <cstring>
#include <string>
#include <vector>

#include <unistd.h>
#include <fcntl.h>

#include "linksc.h"

int
linksc_findpath(std::vector<std::string>& result, std::string src, std::string dst)
{
	std::vector<char> data;
	int s, l;
	struct sockaddr_un addr;

	std::memset(&addr, 0, sizeof(addr));
	addr.sun_family = AF_LOCAL;
	std::strcpy(addr.sun_path, DOOR);
	if ((s = socket(AF_LOCAL, SOCK_STREAM, 0)) == -1) {
perror("socket");
		return 3;
	}
	if (connect(s, (struct sockaddr *)&addr, sizeof(addr)) == -1) {
perror("connect");
		close(s);
		return 3;
	}

	uint32_t i;
	i = src.size();
	data.resize(4);
	*(uint32_t*)&data[0] = i;
	data.insert(data.end(), src.begin(), src.end());
	
	i = dst.size();
	data.resize(data.size() + 4);
	*(uint32_t*)&data[data.size() - 4] = i;
	data.insert(data.end(), dst.begin(), dst.end());

	i = data.size();
	if (write(s, &i, sizeof(i)) < sizeof(i)) {
		close(s);
		return 3;
	}

	if ((l = write(s, &data[0], data.size())) < data.size()) {
		close(s);
		return 3;
	}

	if (read(s, &i, sizeof(i)) < sizeof(i)) {
		close(s);
		return 3;
	}
	
	try {
		data.resize(i);
	} catch (std::bad_alloc&) {
		close(s);
		return 3;
	}

	if (read(s, &data[0], i) < i) {
		close(s);
		return 3;
	}

	if (i < 4) {
		close(s);
		return 3;
	}
	char *buf = &data[0];
	void *oaddr = &data[0];
	size_t osize = i;
	uint32_t status = *(uint32_t*)&data[0];
	result.resize(0);
	switch (status) {
	case 0:
		close(s);
		return 0;
	case 1:
		close(s);
		return 1;
	case 2:
		close(s);
		return 3;
	case 3:
		break;
	default:
		close(s);
		return 2;
	}
	i -= 4;
	buf += 4;
	while (i) {
		std::string h;
		status = *(uint32_t*)buf;
		i -= 4;
		buf += 4;
		h.assign(buf, status);
		i -= status;
		buf += status;
		result.insert(result.end(), h);
	}
	close(s);
	return 4;
}

