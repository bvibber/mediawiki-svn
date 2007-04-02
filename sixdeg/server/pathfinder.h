/* $Id$ */
/*
 * Six degrees of Wikipedia: Server (pathfinder).
 * This source code is released into the public domain.
 */

#ifndef PATHFINDER_H
#define PATHFINDER_H

#include <vector>
#include <map>
#include <string>
#include <utility>

#include <boost/optional.hpp>

#include "linksc.h"

struct bdb_adjacency_store;

struct pathfinder {
	pathfinder(bdb_adjacency_store &);

	void add_adjacency(std::string const &, page_id_t, page_id_t);
	void add_title(std::string const &, std::string const &, page_id_t);
	boost::optional<std::string> name_for_id(std::string const &, page_id_t) const;
	boost::optional<page_id_t> id_for_name(std::string const &, std::string const &) const;
	void filter(void);

	std::vector<std::pair<page_id_t, text_id_t> > solve(std::string const &, page_id_t, page_id_t, bool);

private:
	bool cached_is_date(std::string const &, page_id_t id);
	static bool is_date(std::string);
	std::map<std::string, std::vector<int> > is_date_cache;
	pthread_mutex_t is_date_lock;
	bdb_adjacency_store &store;
};

#endif	/* !PATHFINDER_H */
