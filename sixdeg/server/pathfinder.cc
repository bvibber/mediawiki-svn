/*
 * Six degrees of Wikipedia: Server (pathfinder).
 * This source code is released into the public domain.
 */

#include <iostream>
#include <deque>

#include "pathfinder.h"
#include "bdb_adjacency_store.h"

void
pathfinder_mem::add_title(std::string const &name, page_id_t id)
{
	if (id >= names.size()) {
		names.resize(id + 1);
		isdate.resize(id + 1);
	}
	if (is_date(name))
		isdate[id] = 1;
	else
		isdate[id] = 0;
	names.at(id) = name;
	ids[name] = id;
}

void
pathfinder_mem::filter(void)
{
	for (std::size_t i = 1; i < adjacency.size(); ++i) {
		if (i >= names.size() || names[i].empty()) {
			adjacency.at(i).clear();
			continue;
		}

		for (std::vector<page_id_t>::iterator it = adjacency[i].begin(); it != adjacency[i].end();)
			if (*it >= names.size() || names[*it].empty())
				it = adjacency.at(i).erase(it);
			else 
				++it;
	}
}

boost::optional<page_id_t>
pathfinder_mem::id_for_name(std::string const &name) const
{
	if (ids.find(name) == ids.end())
		return boost::optional<page_id_t>();

	return ids.find(name)->second;
}

boost::optional<std::string>
pathfinder_mem::name_for_id(page_id_t id) const
{
	return names.at(id);
}

/*
 * Is this article a date?
 */
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

/*
 * I didn't write this function.  I don't even know if it works correctly :-).  However,
 * it seems to return the right results.  (Credit: ZorbaTHut @ EFnet #c++)
 */
std::vector<std::pair<page_id_t, text_id_t> >
pathfinder_mem::solve(page_id_t src, page_id_t dst, bool ign_date) {
	std::vector<page_id_t> back;
	std::deque<page_id_t>	next;

	back.clear();
	back.resize(adjacency.size(), static_cast<page_id_t>(-1));
	next.clear();
	back.at(src) = -2;
	next.push_back(src);

	while (next.size()) {
	page_id_t	ts = next.at(0);
		next.pop_front();

		if (ts == dst) {
		std::vector<page_id_t>	path;
		int 			lastlink = back[dst];
			path.push_back(dst);

			while (lastlink != -2) {
				assert(lastlink != -1);
				path.push_back(lastlink);
				lastlink = back.at(lastlink);
			}

			std::reverse(path.begin(), path.end());

			std::vector<std::pair<page_id_t, text_id_t> > ret;
			for (std::size_t i = 0, end = path.size(); i < end; ++i)
				ret.push_back(std::make_pair(path[i], static_cast<text_id_t>(-1)));

			return ret;
		}

		for (std::size_t i = 0; i < adjacency.at(ts).size(); i++) {
			if (ign_date && isdate[adjacency.at(ts).at(i)])
				continue;
			if (back.at(adjacency.at(ts).at(i)) == static_cast<page_id_t>(-1)) {
				back.at(adjacency.at(ts).at(i)) = ts;
				next.push_back(adjacency.at(ts).at(i));
			}
		}
	}
	return std::vector<std::pair<page_id_t, text_id_t> >();
}

pathfinder::pathfinder(void)
{
}

pathfinder_mem::pathfinder_mem(void)
{
}

void
pathfinder_mem::add_adjacency(page_id_t from, page_id_t to)
{
	if (from >= adjacency.size())
		adjacency.resize(from + 1);
	std::vector<page_id_t>& l = adjacency.at(from);
	l.push_back(to);
}

pathfinder_bdb::pathfinder_bdb(bdb_adjacency_store &s)
	: store(s)
{
	pthread_mutex_init(&is_date_lock, 0);
}

bool
pathfinder_bdb::cached_is_date(page_id_t id)
{
	pthread_mutex_lock(&is_date_lock);
	if (id >= is_date_cache.size())
		is_date_cache.resize(id + 1, -1);
	if (is_date_cache[id] == -1) {
		boost::optional<std::string> name = store.name_for_id(id);
		if (!name)
			is_date_cache[id] = -2;
		else
			is_date_cache[id] = is_date(*name);
	}

	bool answer = is_date_cache[id] == 1;
	pthread_mutex_unlock(&is_date_lock);
	return answer;
}

void
pathfinder_bdb::add_adjacency(page_id_t, page_id_t)
{
}

void
pathfinder_bdb::add_title(std::string const &, page_id_t)
{
}

boost::optional<std::string>
pathfinder_bdb::name_for_id(page_id_t id) const
{
	return store.name_for_id(id);
}

boost::optional<page_id_t>
pathfinder_bdb::id_for_name(std::string const &name) const
{
	return store.id_for_name(name);
}

void
pathfinder_bdb::filter(void)
{
}

std::vector<std::pair<page_id_t, text_id_t> >
pathfinder_bdb::solve(page_id_t src, page_id_t dst, bool ign_dates)
{
	std::deque<page_id_t> open;
	std::map<page_id_t, page_id_t> parent;
	open.push_back(src);
	parent[src] = -1;

	bdb_adjacency_transaction trans(store);
	while (!open.empty()) {
		page_id_t trying = open.front();
		open.erase(open.begin());

		std::set<page_id_t> adj = trans.get_adjacencies(trying);

		for (std::set<page_id_t>::iterator it = adj.begin(), end = adj.end(); it != end; ++it) {
			if (*it == dst) {
				std::vector<page_id_t> ret;
				for (int i = trying; i != -1;) {
					ret.push_back(i);
					i = parent[i];
				}
				std::reverse(ret.begin(), ret.end());
				ret.push_back(*it);

				std::vector<std::pair<page_id_t, text_id_t> > r;
				for (std::size_t i = 0, end = ret.size(); i < end; ++i) {
					boost::optional<text_id_t> id = store.text_id_for_page(ret[i]);
					if (id)
						r.push_back(std::make_pair(ret[i], *id));
					else
						r.push_back(std::make_pair(ret[i], static_cast<text_id_t>(-1)));
				}

				return r;
			}

			if (parent[*it] == 0 && (!ign_dates || !cached_is_date(*it))) {
				parent[*it] = trying;
				open.push_back(*it);
			}
		}
	}
	return std::vector<std::pair<page_id_t, text_id_t> >();
}
