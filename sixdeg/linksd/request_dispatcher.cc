/*
 * Six degrees of Wikipedia: Server (request handler).
 * This source code is released into the public domain.
 */

#include <cstdio>
#include <cstdlib>
#include <vector>
#include <map>
#include <iostream>

#include <sys/types.h>
#include <inttypes.h>
#include <pthread.h>

#include "request_dispatcher.h"
#include "pathfinder.h"
#include "encode_decode.h"

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
	int s = rd->s, i;
	request_decoder decoder;
	char buf[1024];

	while ((i = read(s, buf, sizeof buf)) > 0) {
		decoder.add_data(buf, i);
		if (decoder.error()) {
			close(s);
			return 0;
		}

		if (decoder.finished())
			break;
	}

	if (i == -1) {
		std::perror("read");
		close(s);
		return 0;
	}

	rd->d->handle_request(s, decoder);
	close(s);
	return 0;
}

void
request_dispatcher::handle_request(int s, request_decoder &decoder)
{
	std::string from, to;
	request_encoder encoder;

	bool ign_date = decoder.has_key("ignore_dates");
	if (!decoder.has_key("from") || !decoder.has_key("to")) {
		encoder.set_key("error", "illegal_request");
		encoder.send_to(s);
		return;
	}

	from = decoder.get_key("from");
	to = decoder.get_key("to");

	int fromid, toid;
	boost::optional<int> fromid_o = finder.id_for_name(from);
	if (!fromid_o) {
		encoder.set_key("error", "no_from");
		encoder.send_to(s);
		return;
	}
	fromid = *fromid_o;

	boost::optional<int> toid_o = finder.id_for_name(to);
	if (!toid_o) {
		encoder.set_key("error", "no_to");
		encoder.send_to(s);
		return;
	}
	toid = *toid_o;

	std::vector<int> links = finder.solve(fromid, toid, ign_date);
	std::string path;

	for (int i = 0, e = links.size(); i < e; ++i) {
		path += *finder.name_for_id(links[i]);
		path += '|';
	}
	encoder.set_key("path", path);
	encoder.send_to(s);
}
