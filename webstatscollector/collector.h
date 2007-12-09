/* Collector structures and some headers and some data */
/* $Id: collector.h 12318 2005-12-31 15:34:46Z midom $ */

#include <stdio.h>
#include <db4/db.h>

#define PERIOD 3600
#define PREFIX "dumps/"

DB *db;
DB *aggr;

/* Teh easy stats */
struct wcstats {
	unsigned long long wc_count;
	unsigned long long wc_bytes;
};

void dumpData(FILE *, DB *);

struct dumperjob {
	char *prefix;
	DB *db;
	time_t time;
};
