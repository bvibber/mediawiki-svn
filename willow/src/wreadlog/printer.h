/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * printer: log format printers.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

#ifndef PRINTER_H
#define PRINTER_H

#include <iostream>
#include <boost/format.hpp>
using std::ostream;
using boost::format;

#include "packet_decoder.h"

struct entry_printer {
	virtual ~entry_printer(void) {}
	virtual void print(logent const &e) = 0;
	virtual void update(void) {}
};

struct willow_printer : entry_printer {
	willow_printer(ostream &of_) : of(of_) {}

	void print(logent const &e) {
		of << format("[%s] %s %s \"%.*s\" %lu %d %.*s %s\n")
			% e.fmt_reqtime("%Y-%m-%d %H:%M:%S")
			% e.r_cliaddr
			% e.r_reqtype
			% e.r_path
			% e.r_docsize
			% e.r_status
			% e.r_beaddr
			% (e.r_cached ? "HIT" : "MISS");
	}

private:
	ostream	&of;
};

struct clf_printer : entry_printer {
	clf_printer(ostream &of_) : of(of_) {}

	void print(logent const &e) {
		of << format("%s - - [%s] \"%s %.*s HTTP/1.0\" %d %lu\n")
			% e.r_cliaddr
			% e.fmt_reqtime("%d/%b/%Y %H:%M:%S %z")
			% e.r_reqtype
			% e.r_path
			% e.r_status
			% e.r_docsize;
	}

private:
	ostream	&of;
};

struct squid_printer : entry_printer {
	squid_printer(ostream &of_) : of(of_) {}

	void print(logent const &e) {
		of << format("%lu.0      0 %s TCP_%s/%d %lu %s %s - ")
			% e.r_reqtime
			% e.r_cliaddr
			% (e.r_cached ? "HIT" : "MISS")
			% e.r_status
			% e.r_docsize
			% e.r_reqtype
			% e.r_path;
		if (!e.r_cached)
			of << format("PARENT_HIT/%s -\n") % e.r_beaddr;
		else	
			of << "NONE/- -\n";
	}

private:
	ostream	&of;
};

struct tp_impl;
struct topurl_printer : entry_printer {
	topurl_printer(int);
	~topurl_printer();
	void print(logent const &e);

private:
	tp_impl	*impl;
};

#endif
