/*
 * Copyright (c) 2005, 2008 Sun Microsystems, Inc. All Rights Reserved. 
 * Use is subject to license terms.
 *
 * Copyright (c) 2008 River Tarnell <river@loreley.flyingparchment.org.uk>.
 */

#include	<sys/sdt.h>
#include	<netdb.h>
#include	<string.h>
#include	"nsapi.h"

struct request {
	char const	*uri;
	char const	*ip;
	char const	*agent;
	char const	*method;
	char const	*status;
	char const	*referer;
};

int
dtrace_log_request(pb, sn, rq)
	pblock *pb;
	Session *sn;
	Request *rq;
{
struct request	 req;
char		*code, *s;
	if (!rq)
		return REQ_NOACTION;

	if ((req.ip = pblock_findval("ip", sn->client)) == NULL)
		req.ip = "-";
	if ((req.uri = pblock_findval("uri", rq->reqpb)) == NULL)
		req.uri = "-";
	if ((req.method = pblock_findval("method", rq->reqpb)) == NULL)
		req.method = "-";
	if ((req.agent = pblock_findval("user-agent", rq->headers)) == NULL)
		req.agent = "-";
	if ((req.status = pblock_findval("status", rq->srvhdrs)) == NULL)
		req.status = "-";
	if ((req.referer = pblock_findval("referer", rq->headers)) == NULL)
		req.referer = "-";


	/*
	 * "status" contains the full status, e.g. "200 OK".  We just want
	 * the code.
	 */
	code = STRDUP(req.status);
	if ((s = strchr(code, ' ')) != NULL)
		*s = 0;

	req.status = code;

	DTRACE_PROBE1(sjsws, log__request, &req);
	return REQ_NOACTION;
}
