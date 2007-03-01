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

	void add_adjacency(int, int);
	void add_title(std::string const &, int);
	boost::optional<std::string> name_for_id(int) const;
	boost::optional<int> id_for_name(std::string const &) const;
	void filter(void);

	std::vector<int> solve(int, int, bool);

private:
	static bool is_date(std::string name);

	std::vector<std::vector<int> > adjacency;
	std::vector<std::string> names;
	std::map<std::string, int> ids;
	std::vector<int> isdate; 
};

#endif	/* !PATHFINDER_H */
