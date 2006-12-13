/* Loreley: Lightweight HTTP reverse-proxy.                             */
/* topurl_printer: top-like display of most popular URLs		*/
/* Copyright (c) 2005, 2006 River Tarnell <river@attenuate.org>.        */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* @(#) $Id$ */

#include "stdinc.h"
using std::string;
using std::vector;
using std::ostream;

using boost::cref;
using boost::reference_wrapper;
using boost::multi_index_container;
using boost::multi_index::indexed_by;
using boost::multi_index::ordered_unique;
using boost::multi_index::ordered_non_unique;
using boost::multi_index::member;
using boost::multi_index::identity;
using boost::multi_index::tag;

#include <curses.h>

#include "printer.h"

namespace {

struct url_counter {
		struct url_entry {
		uint32_t	count;
		uint64_t	size;
		string		url;
		int		status;
		bool		cached;
	};

	typedef vector<reference_wrapper<url_entry const> > toplist;

	void	url_hit(string const &, uint64_t, int, bool);
	toplist get_topn(int n);

private:
	struct url{};
	struct count{};

	typedef multi_index_container<url_entry,
		indexed_by<
			ordered_unique<tag<url>,
				member<url_entry, string, &url_entry::url> >,
			ordered_non_unique<tag<count>,
				member<url_entry, uint32_t, &url_entry::count> >
		>
	> url_set;

	url_set	_urls;
};

void
url_counter::url_hit(string const &url_, uint64_t size, int status, bool cached)
{
url_set::index<url>::type::const_iterator
	it = _urls.get<url>().find(url_),
	end = _urls.get<url>().end();

	if (it == _urls.get<url>().end()) {
	url_entry	t;
		t.count = 1;
		t.url = url_;
		t.size = size;
		t.status = status;
		t.cached = cached;
		_urls.insert(t);
		return;
	}

url_entry	n = *it;
	n.count++;
	n.size = size;
	n.status = status;
	n.cached = cached;
	_urls.replace(it, n);
}

url_counter::toplist
url_counter::get_topn(int n)
{
toplist	ret;
url_set::index<count>::type::const_reverse_iterator 
	it = _urls.get<count>().rbegin(),
	end = _urls.get<count>().rend();

	for (; it != end && n--; ++it)
		ret.push_back(cref(*it));
	return ret;
}

} // anonymous namespace

struct tp_impl {
	tp_impl(int interval)
		: _lastprint(0)
		, _interval(interval) {
		initscr();
	}
	~tp_impl(void) {
		endwin();
	}

	void print(logent const &);

private:
	url_counter	_counter;
	time_t		_lastprint;
	int		_interval;
};

topurl_printer::topurl_printer(int interval) {
	impl = new tp_impl(interval);
}

topurl_printer::~topurl_printer(void) {
	delete impl;
}

void
topurl_printer::print(logent const &e)
{
	impl->print(e);
}

void
tp_impl::print(logent const &e)
{
	if (!_lastprint)
		time(&_lastprint);

	_counter.url_hit(e.r_path, e.r_docsize, e.r_status, e.r_cached);

	if (_lastprint + _interval > time(0))
		return;

url_counter::toplist	urls;
int	 i = 2;
char	 timestr[64];
time_t	 now;
tm	*tm;
	time(&now);
	tm = localtime(&now);
	strftime(timestr, sizeof(timestr), 
		"%a, %d %b %Y %H:%M:%S GMT", tm);
	urls = _counter.get_topn(LINES - 2);
	clear();
	move(0, 0);
	addstr(timestr);
	move(1, 0);
	addstr(const_cast<char *>("    # Hits  Cached       Size  URL"));
	move(2, 0);
	for (url_counter::toplist::iterator it = urls.begin(),
	     end = urls.end(); it != end; ++it) {
	url_counter::url_entry const	&u = it->get();
		addstr(const_cast<char *>(str(format("%10d  ") % u.count).c_str()));
		if (u.cached)
			addstr(const_cast<char *>("   YES  "));
		else
			addstr(const_cast<char *>("    NO  "));
		addstr(const_cast<char *>(str(format("%9d  ") % u.size).c_str()));
		addstr(const_cast<char *>(u.url.c_str()));
		move(i + 1, 0);
		++i;
	}
	refresh();
	time(&_lastprint);
}
