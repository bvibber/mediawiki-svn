/*
 * Six degrees of Wikipedia: Server (pathfinder).
 * This source code is released into the public domain.
 */

#include <deque>

#include "pathfinder.h"

void
pathfinder_mem::add_title(std::string const &name, int id)
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
	for (int i = 1; i < adjacency.size(); ++i) {
		if (i >= names.size() || names[i].empty()) {
			adjacency.at(i).clear();
			continue;
		}

		for (std::vector<int>::iterator it = adjacency[i].begin(); it != adjacency[i].end();)
			if (*it >= names.size() || names[*it].empty())
				it = adjacency.at(i).erase(it);
			else 
				++it;
	}
}

boost::optional<int>
pathfinder_mem::id_for_name(std::string const &name) const
{
	if (ids.find(name) == ids.end())
		return boost::optional<int>();

	return ids.find(name)->second;
}

boost::optional<std::string>
pathfinder_mem::name_for_id(int id) const
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
std::vector<int>
pathfinder_mem::solve(int src, int dst, bool ign_date) {
	std::vector<int> back;
	std::deque<int>	next;

	back.clear();
	back.resize(adjacency.size(), -1);
	next.clear();
	back.at(src) = -2;
	next.push_back(src);

	while (next.size()) {
	int	ts = next.at(0);
		next.pop_front();

		if (ts == dst) {
		std::vector<int>	path;
		int 			lastlink = back[dst];
			path.push_back(dst);

			while (lastlink != -2) {
				assert(lastlink != -1);
				path.push_back(lastlink);
				lastlink = back.at(lastlink);
			}
			std::reverse(path.begin(), path.end());
			return path;
		}

		for (int i = 0; i < adjacency.at(ts).size(); i++) {
			if (ign_date && isdate[adjacency.at(ts).at(i)])
				continue;
			if (back.at(adjacency.at(ts).at(i)) == -1) {
				back.at(adjacency.at(ts).at(i)) = ts;
				next.push_back(adjacency.at(ts).at(i));
			}
		}
	}
	return std::vector<int>();
}

pathfinder::pathfinder(void)
{
}

pathfinder_mem::pathfinder_mem(void)
{
}

void
pathfinder_mem::add_adjacency(int from, int to)
{
	if (from >= adjacency.size())
		adjacency.resize(from + 1);
	std::vector<int>& l = adjacency.at(from);
	l.push_back(to);
}
