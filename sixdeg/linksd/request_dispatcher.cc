/*
 * Six degrees of Wikipedia: Server (request handler).
 * This source code is released into the public domain.
 */

#include <cstdio>
#include <cstdlib>
#include <vector>
#include <map>
#include <iostream>

#include <boost/bind.hpp>

#include <sys/types.h>
#include <inttypes.h>
#include <pthread.h>

#include "request_dispatcher.h"
#include "pathfinder.h"
#include "encode_decode.h"
#include "client.h"

request_dispatcher::request_dispatcher(pathfinder *f)
	: finder(f)
	, queue(4)
{
}

struct request_data {
	int s;
	request_dispatcher *d;
};

void
request_dispatcher::dispatch(client *s)
{
	queue.schedule(boost::bind(&request_dispatcher::handle, this, s));
}

void
request_dispatcher::handle(client *s)
{
	std::string from, to;
	request_encoder encoder;

	bool ign_date = s->decoder.has_key("ignore_dates");
	if (!s->decoder.has_key("from") || !s->decoder.has_key("to")) {
		s->encoder.set_key("error", "illegal_request");
		s->encoder.send_to(s->fd);
		delete s;
		return;
	}

	from = s->decoder.get_key("from");
	to = s->decoder.get_key("to");

	int fromid, toid;
	boost::optional<int> fromid_o = finder->id_for_name(from);
	if (!fromid_o) {
		s->encoder.set_key("error", "no_from");
		s->encoder.send_to(s->fd);
		delete s;
		return;
	}
	fromid = *fromid_o;

	boost::optional<int> toid_o = finder->id_for_name(to);
	if (!toid_o) {
		s->encoder.set_key("error", "no_to");
		s->encoder.send_to(s->fd);
		delete s;
		return;
	}
	toid = *toid_o;

	std::vector<int> links = finder->solve(fromid, toid, ign_date);
	std::string path;

	for (int i = 0, e = links.size(); i < e; ++i) {
		path += *finder->name_for_id(links[i]);
		path += '|';
	}
	s->encoder.set_key("path", path);
	s->encoder.send_to(s->fd);
	delete s;
}
