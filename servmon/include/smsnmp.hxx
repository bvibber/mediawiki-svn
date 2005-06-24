/* $Header$ */

#ifndef SMSNMP_H
#define SMSNMP_H

#include "smstdinc.hxx"

class snmpclient {
public:
			snmpclient	(str host, int port = 161);
	b::any		getoid		(str oidname);
		
private:
	std::string host;
	int port;
	std::string hostport;
};

#endif
