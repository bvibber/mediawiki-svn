/* $Id$ */
/*
 * Six degrees of Wikipedia: Server (request handler).
 * This source code is released into the public domain.
 */

#include <cstdio>
#include <cstdlib>
#include <vector>
#include <map>
#include <iostream>
#include <sstream>

#include <boost/bind.hpp>
#include <boost/format.hpp>

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
	std::string from, to, wiki = "enwiki_p";
	request_encoder encoder;

	if (s->decoder.has_key("wiki"))
		wiki = s->decoder.get_key("wiki");

	bool ign_date = s->decoder.has_key("ignore_dates");
	if (!s->decoder.has_key("from") || !s->decoder.has_key("to")) {
		s->encoder.set_key("error", "illegal_request");
		s->encoder.send_to(s->fd);
		delete s;
		return;
	}

	from = s->decoder.get_key("from");
	to = s->decoder.get_key("to");

	page_id_t fromid, toid;
	boost::optional<page_id_t> fromid_o = finder->id_for_name(wiki, from);
	if (!fromid_o) {
		s->encoder.set_key("error", "no_from");
		s->encoder.send_to(s->fd);
		delete s;
		return;
	}
	fromid = *fromid_o;

	boost::optional<page_id_t> toid_o = finder->id_for_name(wiki, to);
	if (!toid_o) {
		s->encoder.set_key("error", "no_to");
		s->encoder.send_to(s->fd);
		delete s;
		return;
	}
	toid = *toid_o;

	std::vector<std::pair<page_id_t, text_id_t> > links = finder->solve(wiki, fromid, toid, ign_date);
	std::string path;

	for (std::size_t i = 0, e = links.size(); i < e; ++i) {
		std::ostringstream strm;
		boost::optional<std::string> title = finder->name_for_id(wiki, links[i].first);
		if (!title)
			strm << "<none>" << "#0|";
		else {
			int id = links[i].second;

			strm << *title << '#' << id << '|';
		}
		path += strm.str();
	}
	s->encoder.set_key("path", path);
	s->encoder.send_to(s->fd);
	delete s;
}
