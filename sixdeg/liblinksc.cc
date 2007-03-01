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

#include <boost/tokenizer.hpp>

#include <unistd.h>
#include <fcntl.h>

#include "linksc.h"
#include "encode_decode.h"

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

	request_encoder enc;
	enc.set_key("from", src);
	enc.set_key("to", dst);
	if (!enc.send_to(s)) {
		close(s);
		return 3;
	}

	request_decoder dec;
	char buf[1024];
	ssize_t n;

	while ((n = read(s, buf, sizeof buf)) > 0) {
		dec.add_data(buf, n);
		if (dec.error()) {
			close(s);
			return -1;
		}

		if (dec.finished())
			break;
	}

	if (dec.has_key("error")) {
		close(s);
		std::string error = dec.get_key("error");
		if (error == "no_from")
			return 0;
		if (error == "no_to")
			return 1;
		return 3;
	}

	if (!dec.has_key("path")) {
		close(s);
		return 3;
	}

	boost::char_separator<char> sep("|");
	boost::tokenizer<boost::char_separator<char> > tok(dec.get_key("path"), sep);
	std::copy(tok.begin(), tok.end(), std::back_inserter(result));
	close(s);
	return 4;
}

