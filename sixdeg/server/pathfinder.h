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

struct pathfinder {
	pathfinder(void);
	virtual ~pathfinder(void) {}

	virtual void add_adjacency(page_id_t, page_id_t) = 0;
	virtual void add_title(std::string const &, page_id_t) = 0;
	virtual boost::optional<std::string> name_for_id(page_id_t) const = 0;
	virtual boost::optional<page_id_t> id_for_name(std::string const &) const = 0;
	virtual void filter(void) = 0;

	virtual std::vector<std::pair<page_id_t, text_id_t> > solve(page_id_t, page_id_t, bool) = 0;

protected:
	static bool is_date(std::string name);
};

struct pathfinder_mem : pathfinder {
	pathfinder_mem(void);

	void add_adjacency(page_id_t, page_id_t);
	void add_title(std::string const &, page_id_t);
	boost::optional<std::string> name_for_id(page_id_t) const;
	boost::optional<page_id_t> id_for_name(std::string const &) const;
	void filter(void);

	std::vector<std::pair<page_id_t, text_id_t> > solve(page_id_t, page_id_t, bool);

private:
	std::vector<std::vector<page_id_t> > adjacency;
	std::vector<std::string> names;
	std::map<std::string, page_id_t> ids;
	std::vector<page_id_t> isdate; 
};

struct bdb_adjacency_store;

struct pathfinder_bdb : pathfinder {
	pathfinder_bdb(bdb_adjacency_store &);

	void add_adjacency(page_id_t, page_id_t);
	void add_title(std::string const &, page_id_t);
	boost::optional<std::string> name_for_id(page_id_t) const;
	boost::optional<page_id_t> id_for_name(std::string const &) const;
	void filter(void);

	std::vector<std::pair<page_id_t, text_id_t> > solve(page_id_t, page_id_t, bool);

private:
	bool cached_is_date(page_id_t id);
	std::vector<int> is_date_cache;
	pthread_mutex_t is_date_lock;
	bdb_adjacency_store &store;
};

#endif	/* !PATHFINDER_H */
