/*
 * Six degrees of Wikipedia: Server (pathfinder).
 * This source code is released into the public domain.
 */

#ifndef PATHFINDER_H
#define PATHFINDER_H

#include <vector>
#include <map>
#include <string>

#include <boost/optional.hpp>

struct pathfinder {
	pathfinder(void);
	virtual ~pathfinder(void) {}

	virtual void add_adjacency(int, int) = 0;
	virtual void add_title(std::string const &, int) = 0;
	virtual boost::optional<std::string> name_for_id(int) const = 0;
	virtual boost::optional<int> id_for_name(std::string const &) const = 0;
	virtual void filter(void) = 0;

	virtual std::vector<int> solve(int, int, bool) = 0;

protected:
	static bool is_date(std::string name);
};

struct pathfinder_mem : pathfinder {
	pathfinder_mem(void);

	void add_adjacency(int, int);
	void add_title(std::string const &, int);
	boost::optional<std::string> name_for_id(int) const;
	boost::optional<int> id_for_name(std::string const &) const;
	void filter(void);

	std::vector<int> solve(int, int, bool);

private:
	std::vector<std::vector<int> > adjacency;
	std::vector<std::string> names;
	std::map<std::string, int> ids;
	std::vector<int> isdate; 
};

#
#endif	/* !PATHFINDER_H */
