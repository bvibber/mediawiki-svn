/* $Id$ */
/*
 * Six degrees of Wikipedia: Client library.
 */

#include <sys/types.h>
#include <sys/socket.h>
#include <sys/stat.h>
#include <sys/mman.h>
#include <netinet/in.h>
#include <arpa/inet.h>

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
#include "defs.h"
#include "encode_decode.h"

int
linksc_findpath(std::vector<std::string>& result, std::string const &src, std::string const &dst, bool ignore_dates)
{
	std::vector<char> data;
	int s;
	struct sockaddr_in addr;

	std::memset(&addr, 0, sizeof(addr));
	addr.sin_family = AF_UNIX;
	addr.sin_addr.s_addr = inet_addr("127.0.0.1");
	addr.sin_port = htons(PORT);

	if ((s = socket(PF_UNIX, SOCK_STREAM, 0)) == -1) {
		perror("socket");
		return LINKS_NO_CONNECT;
	}
	if (connect(s, (struct sockaddr *)&addr, sizeof(addr)) == -1) {
		perror("connect");
		close(s);
		return LINKS_NO_CONNECT;
	}

	request_encoder enc;
	enc.set_key("from", src);
	enc.set_key("to", dst);
	if (ignore_dates)
		enc.set_key("ignore_dates", "1");

	if (!enc.send_to(s)) {
		close(s);
		return LINKS_NO_CONNECT;
	}

	request_decoder dec;
	char buf[1024];
	ssize_t n;

	while ((n = read(s, buf, sizeof buf)) > 0) {
		dec.add_data(buf, n);
		if (dec.error()) {
			close(s);
			return LINKS_NO_CONNECT;
		}

		if (dec.finished())
			break;
	}

	if (dec.has_key("error")) {
		close(s);
		std::string error = dec.get_key("error");
		if (error == "no_from")
			return LINKS_NO_FROM;
		if (error == "no_to")
			return LINKS_NO_TO;
		return LINKS_NO_CONNECT;
	}

	if (!dec.has_key("path")) {
		close(s);
		return LINKS_NO_CONNECT;
	}

	boost::char_separator<char> sep("|");
	boost::tokenizer<boost::char_separator<char> > tok(dec.get_key("path"), sep);
	std::copy(tok.begin(), tok.end(), std::back_inserter(result));
	close(s);
	return LINKS_OKAY;
}

