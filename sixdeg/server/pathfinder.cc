/* $Id$ */
/*
 * Six degrees of Wikipedia: Server (pathfinder).
 * This source code is released into the public domain.
 */

#include <iostream>
#include <deque>

#include "pathfinder.h"
#include "bdb_adjacency_store.h"

bool
pathfinder::is_date(std::string name) 
{
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

pathfinder::pathfinder(bdb_adjacency_store &s)
	: store(s)
{
	pthread_mutex_init(&is_date_lock, 0);
}

bool
pathfinder::cached_is_date(std::string const &wiki, page_id_t id)
{
	pthread_mutex_lock(&is_date_lock);
	std::vector<int> &r = is_date_cache[wiki];
	if (id >= r.size())
		r.resize(id + 1, -1);
	if (r[id] == -1) {
		boost::optional<std::string> name = store.name_for_id(wiki, id);
		if (!name)
			r[id] = -2;
		else
			r[id] = is_date(*name);
	}

	bool answer = r[id] == 1;
	pthread_mutex_unlock(&is_date_lock);
	return answer;
}

boost::optional<std::string>
pathfinder::name_for_id(std::string const &wiki, page_id_t id) const
{
	return store.name_for_id(wiki, id);
}

boost::optional<page_id_t>
pathfinder::id_for_name(std::string const &wiki, std::string const &name) const
{
	return store.id_for_name(wiki, name);
}

std::vector<std::pair<page_id_t, text_id_t> >
pathfinder::solve(std::string const &wiki, page_id_t src, page_id_t dst, bool ign_dates)
{
	std::deque<page_id_t> open;
	std::map<page_id_t, page_id_t> parent;
	open.push_back(src);
	parent[src] = static_cast<page_id_t>(-1);

	bdb_adjacency_transaction trans(store);
	while (!open.empty()) {
		page_id_t trying = open.front();
		open.erase(open.begin());

		std::set<page_id_t> adj = trans.get_adjacencies(wiki, trying);

		for (std::set<page_id_t>::iterator it = adj.begin(), end = adj.end(); it != end; ++it) {
			if (*it == dst) {
				std::vector<page_id_t> ret;
				for (int i = trying; i != static_cast<page_id_t>(-1);) {
					ret.push_back(i);
					i = parent[i];
				}
				std::reverse(ret.begin(), ret.end());
				ret.push_back(*it);

				std::vector<std::pair<page_id_t, text_id_t> > r;
				for (std::size_t i = 0, end = ret.size(); i < end; ++i) {
					boost::optional<text_id_t> id = store.text_id_for_page(wiki, ret[i]);
					if (id)
						r.push_back(std::make_pair(ret[i], *id));
					else
						r.push_back(std::make_pair(ret[i], static_cast<text_id_t>(-1)));
				}

				return r;
			}

			if (parent[*it] == 0 && (!ign_dates || !cached_is_date(wiki, *it))) {
				parent[*it] = trying;
				open.push_back(*it);
			}
		}
	}
	return std::vector<std::pair<page_id_t, text_id_t> >();
}
