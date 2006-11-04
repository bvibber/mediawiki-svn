/* @(#) $Id$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp_entity: HTTP entity handling.
 */

#if defined __SUNPRO_C || defined __DECC || defined __HP_cc
# pragma ident "@(#)$Id$"
#endif

/* How does this work?
 * 
 * Each HTTP request can be divided into two entities: the request and the
 * response.  The client sends the request, i.e. the headers and possibly
 * a body, to the server, which considers it and sends a reply.
 * 
 * Internally, we read the request headers and ignore the
 * body [entity_read_headers].  We then examine the headers
 * [whttp:client_read_done] and decide if it has a body.  We modify
 * the entry slightly, and send it to the backend with either no source
 * or, if it had a body, the client's FDE as the source [entity_send].
 * We then wait for the server to reply with its header.  When it does
 * [whttp:backend_headers_done], we send the request to the client, using
 * the backend's FDE as the body, if it has one, and close it.
 * 
 * See "Entity sending" below for a detailed description of how entity
 * sending works.
 * 
 * TODO: We don't have to buffer the headers, _but_ it makes things easier
 * for now and doesn't cost much.  if we start not buffering we need to
 * decide what to do when the client goes away unexpectedly.  probably it's
 * easiest to just drop the backend connection (this is wasteful of backends
 * but we don't cache them at the moment anyway).  what do we do when the
 * client sends "Foo: bar\r\n  baz\r\n" and we decide after baz that we
 * shouldn't send that header after all?
 * 
 * There is a trade-off in some places between excessive copying and
 * excessive syscalls.  In some cases we copy data (headers) when we could
 * undo the parser mangling and send them as-is.  IMO this is not likely
 * to be a worthwhile optimisation, needs profiling.
 * 
 * As for FDE backending, Unix sucks:
 * 
 *    The sendfile() function copies data  from  in_fd  to  out_fd starting
 *    at  offset  off and of length len bytes. The in_fd argument should
 *    be a  file  descriptor  to  a  regular  file opened for reading.
 */

#include <sys/uio.h>

#include <unistd.h>
#include <errno.h>
#include <string.h>
#include <stdlib.h>
#include <stdio.h>
#include <assert.h>
/*LINTED*/
#include <fcntl.h>
#include <strings.h>
#include <event.h>
#include <ctype.h>
#include <zlib.h>

#include <vector>
using std::vector;
#include <utility>
#include <algorithm>
using std::search;
using std::max;
using std::min;

#include "willow.h"
#include "whttp.h"
#include "whttp_entity.h"
#include "wnet.h"
#include "wlog.h"
#include "wconfig.h"

#define ENTITY_STATE_START	0
#define ENTITY_STATE_HDR	1
#define ENTITY_STATE_DONE	2
#define ENTITY_STATE_SEND_HDR	3
#define ENTITY_STATE_SEND_BODY	4
#define ENTITY_STATE_SEND_BUF	5

#define ZLIB_BLOCK	(1024 * 32)

static void entity_error_callback(struct bufferevent *, short, void *);
static void entity_read_callback(struct bufferevent *, void *);
static int parse_headers(struct http_entity *);
static int parse_reqtype(struct http_entity *);
static int validhost(const char *);
static int via_includes_me(const char *);
static int write_zlib_eof(struct http_entity *);
static int write_data(struct http_entity *, void *buf, size_t len);

static void entity_send_file_done(struct fde *, void *, int);
static void entity_send_target_write(struct bufferevent *, void *);
static void entity_send_target_error(struct bufferevent *, short, void *);

const char *ent_errors[] = {
	/* 0  */	"Unknown error",
	/* -1 */	"Read error",
	/* -2 */	"Could not parse request headers",
	/* -3 */	"Invalid Host",
	/* -4 */	"Invalid request type",
	/* -5 */	"Too many headers",
	/* -6 */	"Forwarding loop detected",
	/* -7 */	"Invalid Accept-Encoding",
};

const char *ent_encodings[] = {
	"identity",
	"deflate",
	"x-deflate",
	"gzip",
	"x-gzip",
};

void
entity_set_response(http_entity *ent, int isresp)
{
	if (isresp) {
		if (ent->he_flags.response)
			return;
		if (ent->he_rdata.request.path)
			wfree(ent->he_rdata.request.path);
		if (ent->he_rdata.request.host)
			wfree(ent->he_rdata.request.host);
		bzero(&ent->he_rdata.response, sizeof(ent->he_rdata.response));
		ent->he_flags.response = 1;
	} else {
		if (!ent->he_flags.response)
			return;
		bzero(&ent->he_rdata.request, sizeof(ent->he_rdata.request));
		ent->he_flags.response = 0;
	}
}

void
entity_read_headers(http_entity *entity, header_cb func, void *udata)
{
	entity->_he_cbdata = udata;
	entity->_he_func = func;
	entity->he_flags.hdr_only = 1;

	WDEBUG((WLOG_DEBUG, "entity_read_headers: starting, source %d",
			entity->he_source.fde.fde->fde_fd));
	/* XXX source for an entity header read is _always_ an fde */
	entity->_he_frombuf = bufferevent_new(entity->he_source.fde.fde->fde_fd,
				entity_read_callback, NULL, entity_error_callback, entity);
	event_base_set(evb, &entity->_he_frombuf->ev_read);
	event_base_set(evb, &entity->_he_frombuf->ev_write);
	bufferevent_disable(entity->_he_frombuf, EV_WRITE);
	bufferevent_enable(entity->_he_frombuf, EV_READ);
}

void
entity_send(fde *fde, http_entity *entity, header_cb cb, void *data, int flags)
{
struct	header_list	*hl;
	int		 window = 15;

	errno = 0;

	entity->_he_func = cb;
	entity->_he_cbdata = data;
	entity->_he_target = fde;
	entity->he_flags.hdr_only = 0;
	if (!entity->he_flags.response && entity->he_rdata.request.reqtype == REQTYPE_POST) {
		entity->he_source.fde._wrt = entity->he_rdata.request.contlen;
	} else if (entity->he_flags.response)
		entity->he_source.fde._wrt = entity->he_rdata.request.contlen;

	entity->_he_tobuf = bufferevent_new(entity->_he_target->fde_fd,
		NULL, entity_send_target_write,
		entity_send_target_error, entity);
	event_base_set(evb, &entity->_he_tobuf->ev_read);
	event_base_set(evb, &entity->_he_tobuf->ev_write);

	bufferevent_disable(entity->_he_tobuf, EV_READ);
	bufferevent_enable(entity->_he_tobuf, EV_WRITE);
	if (entity->_he_frombuf) {
		bufferevent_disable(entity->_he_frombuf, EV_READ);
		bufferevent_disable(entity->_he_frombuf, EV_WRITE);
	}
	entity->_he_state = ENTITY_STATE_SEND_HDR;

	WDEBUG((WLOG_DEBUG, "entity_send: writing to %d [%s], enc=%d", fde->fde_fd, fde->fde_desc,
			entity->he_encoding));
	
	if (entity->he_flags.response) {
		evbuffer_add_printf(entity->_he_tobuf->output, "HTTP/1.1 %d %s\r\n",
			entity->he_rdata.response.status, entity->he_rdata.response.status_str);
	} else {
		evbuffer_add_printf(entity->_he_tobuf->output, "%s %s HTTP/1.%d\r\n",
			request_string[entity->he_rdata.request.reqtype],
			entity->he_rdata.request.path,
			entity->he_rdata.request.host ? 1 : 0);
	}
		
	if (flags & ENT_CHUNKED_OKAY) {
	struct header *contlen;
		entity->he_flags.chunked = 1;
		if (!entity->he_h_transfer_encoding)
			entity->he_headers.add("Transfer-Encoding", "chunked");
		if ((contlen = entity->he_headers.find("Content-Length")) != NULL)
			entity->he_headers.remove("Content-Length");
	}

	switch (entity->he_encoding) {
	case E_NONE:
		break;
	case E_GZIP: case E_X_GZIP:
		window += 16;
	case E_DEFLATE: case E_X_DEFLATE: {
		int err;

		if ((err = deflateInit2(&entity->_he_zbuf, config.complevel, Z_DEFLATED,
				window, 8, Z_DEFAULT_STRATEGY)) != Z_OK) {
			wlog(WLOG_WARNING, "deflateInit: %s", zError(err));
			entity->he_encoding = E_NONE;
		}
		break;
	}
	}
	evbuffer_add_printf(entity->_he_tobuf->output, "Content-Encoding: %s\r\n",
			    ent_encodings[entity->he_encoding]);
	for (vector<header>::iterator it = entity->he_headers.hl_hdrs.begin(),
	     end = entity->he_headers.hl_hdrs.end(); it != end; ++it)
		evbuffer_add_printf(entity->_he_tobuf->output, "%s: %s\r\n",
				    it->hr_name, it->hr_value);
	evbuffer_add_buffer(entity->_he_tobuf->output, entity->he_extraheaders);
	bufferevent_write(entity->_he_tobuf, const_cast<char *>("\r\n"), 2);
}

static void
entity_error_callback(struct bufferevent *be, short what, void *d)
{
struct	http_entity	*entity = (http_entity *)d;

	/*
	 * Some kind of error occured while we were reading from the backend.
	 */
	WDEBUG((WLOG_DEBUG, "entity_error_callback called, what=%hd errno=%d %s", what,
			errno, strerror(errno)));

	if (what & EVBUFFER_EOF) {
		/*
		 * End of file from backend.
		 */
		if (entity->he_encoding) {
			if (write_zlib_eof(entity) == -1) {
				entity->_he_func(entity, entity->_he_cbdata, -1);
				return;
			}
		}

		if (entity->he_flags.chunked && !entity->he_flags.eof) {
			WDEBUG((WLOG_DEBUG, "writing chunked data, append EOF"));
			entity->he_flags.eof = 1;
			bufferevent_enable(entity->_he_tobuf, EV_WRITE);
			bufferevent_write(entity->_he_tobuf, const_cast<void *>((void *)"0\r\n"), 3);
			return;
		}
		WDEBUG((WLOG_DEBUG, "entity_error_callback: EOF"));
		bufferevent_disable(entity->_he_frombuf, EV_READ);
		entity->_he_func(entity, entity->_he_cbdata, 1);
		//entity->he_flags.eof = 1;
		return;
	}

	entity->he_flags.error = 1;
	entity->_he_func(entity, entity->_he_cbdata, -1);
}

static int
write_zlib_eof(http_entity *entity)
{
	int zerr;
	unsigned char zbuf[16384];
	int n;

	entity->_he_zbuf.avail_in = 0;
	entity->_he_zbuf.next_out = zbuf;
	entity->_he_zbuf.avail_out = sizeof zbuf;

	zerr = deflate(&entity->_he_zbuf, Z_FINISH);

	if (zerr != Z_STREAM_END) {
		wlog(WLOG_WARNING, "deflate: %s", zError(zerr));
		deflateEnd(&entity->_he_zbuf);
		return -1;
	}
	n = sizeof zbuf - entity->_he_zbuf.avail_out;
	WDEBUG((WLOG_DEBUG, "writing zlib, append finish, left=%d avail=%d",
		n, entity->_he_zbuf.avail_out));
	if (n) {
		bufferevent_enable(entity->_he_tobuf, EV_WRITE);
		if (entity->he_flags.chunked)
			evbuffer_add_printf(entity->_he_tobuf->output, "%x\r\n", n);
		bufferevent_write(entity->_he_tobuf, zbuf, n);
		if (entity->he_flags.chunked)
			evbuffer_add_printf(entity->_he_tobuf->output, "\r\n");
	}
	deflateEnd(&entity->_he_zbuf);
	return 0;
}
static char *
find_rn(char *buf, char *end)
{
char	*s;
	for (s = buf; s < end; s += 2) {
		if (likely(*s != '\r' && *s != '\n'))
			continue;
		if (likely((s + 1 < end) && s[0] == '\r' && s[1] == '\n'))
			return s;
		if (likely(s > buf && s[-1] == '\r' && s[0] == '\n'))
			return s - 1;
	}
	return NULL;
}

static char *
find_rnrn(char *buf, char *end)
{
char	*s;
	/* find any \r or \n */
	for (s = buf; s < end; s += 4) {
		if (*s == '\r' || *s == '\n') {
		char	*q = s;
			while (q > buf && (q[-1] == '\r' || q[-1] == '\n'))
				q--;
			if (q + 3 >= end)
				continue;
			if (q[0] == '\r' && q[1] == '\n'
			    && q[2] == '\r' && q[3] == '\n')
				return q;
		}
	}
	/* nothing found... */
	return NULL;
}

static void
entity_read_callback(bufferevent *be, void *d)
{
struct	http_entity	*entity = (http_entity *) d;
	int		 i;
#define RD_BUFSZ	 16386
	char		 buf[RD_BUFSZ];
	int		 wrote = 0, contdone = 0;

	/*
	 * Data was available from the backend.  If state is ENTITY_STATE_SEND_BODY,
	 * we're moving the request from backend->client, so do that. Otherwise,
	 * we're still reading header information.
	 */
	WDEBUG((WLOG_DEBUG, "entity_read_callback: called, source %d", 
			entity->he_source.fde.fde->fde_fd));

	if (entity->_he_state < ENTITY_STATE_SEND_BODY) {
	char		*buf = ((char *)EVBUFFER_DATA(entity->_he_frombuf->input)) + entity->_he_hdroff;
	char		*end, *found;
static	char const	*rnrn = "\r\n\r\n";
		if (entity->_he_hdroff == 0)
			buf = (char *)EVBUFFER_DATA(entity->_he_frombuf->input);
		end = (char *)EVBUFFER_DATA(entity->_he_frombuf->input) + EVBUFFER_LENGTH(entity->_he_frombuf->input);
		WDEBUG((WLOG_DEBUG, "entity_read_callback: end=%p; buf=%p; data=%p",
			end, buf, EVBUFFER_DATA(entity->_he_frombuf->input)));

		if ((found = find_rnrn(buf, end)) == NULL) {
			// need more data
			entity->_he_hdroff = max(buf, min(buf, end - 4)) - buf;
			WDEBUG((WLOG_DEBUG, "entity_read_callback: need more data, read %d [%s], hdroff=%d", 
				end - buf, buf, entity->_he_hdroff));
			return;
		}

		entity->_he_hdroff = (found + 4) - buf;	// so parse_headers knows how much to search
		if ((i = parse_headers(entity)) < 0) {
			WDEBUG((WLOG_DEBUG, "entity_read_callback: parse_headers returned -1"));
			entity->he_flags.error = 1;
			bufferevent_disable(entity->_he_frombuf, EV_READ);
			entity->_he_func(entity, entity->_he_cbdata, i);
			return;
		}
		WDEBUG((WLOG_DEBUG, "parse_headers returned; client now %d hdr_only=%d",
			entity->_he_state, entity->he_flags.hdr_only));

		if (entity->_he_state == ENTITY_STATE_DONE) {			
			if (entity->he_flags.hdr_only) {
				WDEBUG((WLOG_DEBUG, "entity_read_callback: client is ENTITY_STATE_DONE"));
				bufferevent_disable(entity->_he_frombuf, EV_READ);
				entity->_he_func(entity, entity->_he_cbdata, 0);
				return;
			} else
				entity->_he_state = ENTITY_STATE_SEND_BODY;
		}
		//bufferevent_disable(entity->_he_frombuf, EV_READ);
		//if (entity->he_flags.hdr_only)
			return;
	}

	if (entity->he_flags.eof) {
		entity->_he_func(entity, entity->_he_cbdata, 0);
		return;
	}

	bufferevent_enable(entity->_he_tobuf, EV_WRITE);

	if (entity->he_flags.eof) {
		/*
		 * This means the last chunk was written (see below).
		 */
		bufferevent_disable(entity->_he_frombuf, EV_READ);
		entity->_he_func(entity, entity->_he_cbdata, 0);
		bufferevent_enable(entity->_he_tobuf, EV_WRITE);
		return;
	}

	/*
	 * While data is available, read it and forward.  If we're using chunked encoding,
	 * don't read past the end of the chunk.
	 */
	for (;;) {
		size_t read;
		size_t want = RD_BUFSZ;

		/*
		 * If we're reading chunked data, check if we're starting a new chunk.
		 */
		if ((entity->he_te & TE_CHUNKED) && entity->_he_chunk_size == 0) {
			char *chunks;
			if ((chunks = evbuffer_readline(entity->_he_frombuf->input)) == NULL)
				return;
			entity->_he_chunk_size = strtol(chunks, NULL, 16);
			free(chunks);
			WDEBUG((WLOG_DEBUG, "new chunk, size=%d", entity->_he_chunk_size));
			if (entity->_he_chunk_size == 0) {
				/*
				 * Zero-sized chunk = end of request.
				 *
				 * If this client is receiving TE:chunked data, we have to write
				 * the terminating block and finish up the next time round.  If 
				 * not, mark it finished now.
				 */
				int more = 0;

				if (entity->he_encoding) {
					bufferevent_enable(entity->_he_tobuf, EV_WRITE);
					write_zlib_eof(entity);
					more = 1;
				}

				if (entity->he_flags.chunked) {
					bufferevent_disable(entity->_he_frombuf, EV_READ);
					bufferevent_enable(entity->_he_tobuf, EV_WRITE);
					bufferevent_write(entity->_he_tobuf, const_cast<void *>((void *)"0\r\n"), 3);
					more = 1;
				}

				if (more) {
					entity->he_flags.eof = 1;
					return;
				}

				bufferevent_disable(entity->_he_frombuf, EV_READ);
				if (!wrote)
					entity->_he_func(entity, entity->_he_cbdata, 0);
				else
					bufferevent_enable(entity->_he_tobuf, EV_WRITE);
				return;
			}
			/* +2 for CRLF */
			entity->_he_chunk_size += 2;
		}

		want = RD_BUFSZ;
		if (entity->_he_chunk_size)
			want = entity->_he_chunk_size;
		else if (entity->he_source.fde._wrt)
			want = entity->_he_chunk_size;

		want = entity->_he_chunk_size ? entity->_he_chunk_size : RD_BUFSZ;

		read = bufferevent_read(entity->_he_frombuf, buf, want);
		WDEBUG((WLOG_DEBUG, "rw %d, got %d wrote=%d wrt=%d", want, read, wrote,
				entity->he_source.fde._wrt));

		/*
		 * _wrt holds the remaining data to read from the source, if we know
		 * it (e.g. Content-Length header).  Decrement it by the amount we just
		 * read; if it ends up 0, we read the entire entity.
		 */
		if (entity->he_source.fde._wrt) {
			entity->he_source.fde._wrt -= read;
			if (entity->he_source.fde._wrt == 0)
				contdone = 1;
		}
		WDEBUG((WLOG_DEBUG, "contdone=%d", contdone));

		/*
		 * We ran out of data and haven't reached the end of the source entity.
		 * Schedule another read on the source and return.
		 */
		if (read == 0 && !contdone) {
			bufferevent_disable(entity->_he_tobuf, EV_WRITE);
			bufferevent_enable(entity->_he_frombuf, EV_READ);
			return;
		}
		
		if (entity->_he_chunk_size)
			entity->_he_chunk_size -= read;

		if ((entity->he_te & TE_CHUNKED) && entity->_he_chunk_size == 0)
			/* subtract the +2 we added above */
			read -= 2;

		entity->he_size += read;

		if (entity->he_cache_callback) {
			entity->he_cache_callback(buf, read, entity->he_cache_callback_data);
		}
		bufferevent_enable(entity->_he_tobuf, EV_WRITE);

		if ((i = write_data(entity, buf, read)) == -1) {
			entity->_he_func(entity, entity->_he_cbdata, -1);
			return;	
		}
		if (read > 0) {
			bufferevent_disable(entity->_he_frombuf, EV_READ);
			bufferevent_enable(entity->_he_tobuf, EV_WRITE);
			return;
		}
		wrote++;
		if (contdone) {
			if (entity->he_flags.chunked)
				bufferevent_write(entity->_he_tobuf, const_cast<char *>("0\r\n"), 3);
			bufferevent_disable(entity->_he_frombuf, EV_READ);
			//entity->_he_func(entity, entity->_he_cbdata, 0);
			entity->he_flags.eof = 1;
			return;
		}
	}

	bufferevent_disable(entity->_he_frombuf, EV_READ);
}

static int
write_data(http_entity *entity, void *buf, size_t len)
{
static	unsigned char zbuf[ZLIB_BLOCK * 2];

	WDEBUG((WLOG_DEBUG, "write_data: writing %d", len));

	switch (entity->he_encoding) {
	case E_NONE:
		if (entity->he_flags.chunked)
			evbuffer_add_printf(entity->_he_tobuf->output, "%x\r\n", len);
		bufferevent_write(entity->_he_tobuf, buf, len);
		if (entity->he_flags.chunked)
			evbuffer_add_printf(entity->_he_tobuf->output, "\r\n");
		return 0;

	case E_DEFLATE: case E_X_DEFLATE: case E_GZIP: case E_X_GZIP: {
		int zerr;

		entity->_he_zbuf.next_in = (unsigned char *)buf;
		entity->_he_zbuf.avail_in = len;
		entity->_he_zbuf.next_out = zbuf;
		entity->_he_zbuf.avail_out = sizeof zbuf;
		while (entity->_he_zbuf.avail_in) {
			zerr = deflate(&entity->_he_zbuf, Z_SYNC_FLUSH);
			if (zerr != Z_OK) {
				wlog(WLOG_WARNING, "deflate: %s", zError(zerr));
				return -1;
			}
			WDEBUG((WLOG_DEBUG, "avail_in=%d avail_out=%d",
				entity->_he_zbuf.avail_in, entity->_he_zbuf.avail_out));
			if (entity->he_flags.chunked)
				evbuffer_add_printf(entity->_he_tobuf->output, "%x\r\n", 
					(sizeof zbuf - entity->_he_zbuf.avail_out));
			bufferevent_write(entity->_he_tobuf, zbuf, 
				(sizeof zbuf - entity->_he_zbuf.avail_out));
			if (entity->he_flags.chunked)
				evbuffer_add_printf(entity->_he_tobuf->output, "\r\n");
			entity->_he_zbuf.next_out = zbuf;
			entity->_he_zbuf.avail_out = sizeof zbuf;
		}
		return 0;
	}
	}
	abort();
}

static void
entity_send_target_write(struct bufferevent *buf, void *d)
{
struct	http_entity	*entity = (http_entity *)d;
static  char		 fbuf[ZLIB_BLOCK];

	WDEBUG((WLOG_DEBUG, "entity_send_target_write: eof=%d, state=%d", 
		entity->he_flags.eof, entity->_he_state));

	if (entity->he_flags.eof) {
		if (entity->_he_frombuf)
			bufferevent_disable(entity->_he_frombuf, EV_READ);
		bufferevent_disable(entity->_he_tobuf, EV_WRITE);
		entity->_he_func(entity, entity->_he_cbdata, 0);
		return;
	}

	/*
	 * Write to target completed.
	 */
	if (entity->_he_state == ENTITY_STATE_SEND_HDR) {
		/*
		 * Sending headers completed.  Decide what to do next.
		 */
		switch (entity->he_source_type) {
		case ENT_SOURCE_NONE:
			/* no body for this request */
			WDEBUG((WLOG_DEBUG, "entity_send_target_write: no body, return immediately"));
			entity->_he_func(entity, entity->_he_cbdata, 0);
			return;
		
		case ENT_SOURCE_BUFFER:
			/* write buffer, callback when done */
			WDEBUG((WLOG_DEBUG, "entity_send_target_write: source is buffer, %d bytes", 
					entity->he_source.buffer.len));
			entity->_he_state = ENTITY_STATE_SEND_BUF;
			bufferevent_write(entity->_he_tobuf,
				(void *)entity->he_source.buffer.addr,
				entity->he_source.buffer.len);
			return;

		case ENT_SOURCE_FILE:
			/* write file */
			//entity->_he_tobuf = entity->_he_frombuf = NULL;

			/*
			 * Compressed data can't be written using sendfile
			 */
			entity->_he_state = ENTITY_STATE_SEND_BODY;

			if (entity->he_encoding) {
				bufferevent_enable(entity->_he_tobuf, EV_WRITE);
				/* The write handler does this for us */
				return;
			}

			bufferevent_disable(entity->_he_tobuf, EV_WRITE);
#if 0				
			if (wnet_sendfile(entity->_he_target->fde_fd, entity->he_source.fd.fd, 
			    entity->he_source.fd.size - entity->he_source.fd.off,
			    entity->he_source.fd.off, entity_send_file_done, entity, 0) == -1) {
				entity->_he_func(entity, entity->_he_cbdata, -1);
			}
#endif
			return;
		}
		entity->_he_state = ENTITY_STATE_SEND_BODY;
	}
	
	if (entity->_he_state == ENTITY_STATE_SEND_BUF) {
		/*
		 * Writing buffer completed.
		 */
		bufferevent_disable(entity->_he_tobuf, EV_WRITE);
		if (entity->_he_frombuf)
			bufferevent_disable(entity->_he_frombuf, EV_READ);
		entity->_he_func(entity, entity->_he_cbdata, 0);
		return;
	}

	if (entity->he_source_type == ENT_SOURCE_FILE) {
		/*
		 * We only get here if we're writing deflate data.
		 */
		ssize_t i;

		WDEBUG((WLOG_DEBUG, "write file, eof=%d, size=%d, off=%d",
			entity->he_flags.eof, entity->he_source.fd.size,
			entity->he_source.fd.off));

		if (entity->he_flags.eof) {
			bufferevent_disable(entity->_he_tobuf, EV_WRITE);
			entity->_he_func(entity, entity->_he_cbdata, 0);
			return;
		}

		if ((i = read(entity->he_source.fd.fd, fbuf, sizeof fbuf)) == -1) {
			bufferevent_disable(entity->_he_tobuf, EV_WRITE);
			entity->_he_func(entity, entity->_he_cbdata, -1);
			return;
		}
		if (write_data(entity, fbuf, i) == -1) {
			bufferevent_disable(entity->_he_tobuf, EV_WRITE);
			entity->_he_func(entity, entity->_he_cbdata, -1);
			return;
		}
		entity->he_source.fd.off += i;
		if (entity->he_source.fd.off == (off_t) entity->he_source.fd.size) {
			entity->he_flags.eof = 1;
			write_zlib_eof(entity);
			return;
		}

		return;
	}

	WDEBUG((WLOG_DEBUG, "entity_send_target_write: FDE, drained=%d",
		entity->he_flags.drained));

	/*
	 * Otherwise, we're sending from an FDE, and the last write completed.
	 */
	bufferevent_enable(entity->_he_frombuf, EV_READ);
	bufferevent_disable(entity->_he_tobuf, EV_WRITE);
	if (!entity->he_flags.drained) {
		entity->he_flags.drained = 1;
}		entity_read_callback(entity->_he_frombuf, entity);
//	}
}

static void
entity_send_target_error(struct bufferevent *buf, short err, void *d)
{
struct	http_entity	*entity = (http_entity *)d;

	/*
	 * Writing to target produced an error.
	 */
	entity->_he_func(entity, entity->_he_cbdata, -1);
}

/*ARGSUSED*/
static void
entity_send_file_done(fde *fde, void *data, int res)
{
struct	http_entity	*entity = (http_entity *)data;

	WDEBUG((WLOG_DEBUG, "entity_send_file_done: called for %d [%s], res=%d", 
		fde->fde_fd, fde->fde_desc, res));
	entity->_he_func(entity, entity->_he_cbdata, res);
	return;
}	

static int
validhost(const char *host)
{
	for (; *host; ++host) {
		if (!(char_table[(unsigned char)*host] & CHAR_HOST))
			return 0;
	}
	return 1;
}

static int
via_includes_me(const char *s)
{
	char    *orig = wstrdup(s);
	char    *via = orig, *comma, *space;

	do {
		comma = strchr(via, ',');
		if (comma)
			*comma++ = '\0';
		via = strchr(via, ' ');
		if (!via)
			break;
		while (*via == ' ')
			++via;
		space = strchr(via, ' ');
		if (!space) {
			wfree(orig);
			return 0;
		}
		*space = '\0';
		if (!strcmp(via, my_hostname)) {
			wfree(orig);
			return 1;
		}
		via = comma;
	} while (comma);
	wfree(orig);
	return 0;
}

static int
parse_headers(http_entity *entity)
{
char		*line;
char		*rdbuf;
char		*buf = (char *)EVBUFFER_DATA(entity->_he_frombuf->input);
char		*end = buf + entity->_he_hdroff;
char		*nexthdr;
static char	 rn[] = { '\r', '\n' };
size_t		 i, nread;
char		*lbuf, *lend;
bool		 sent_host = false;
	entity->_he_hdrbuf = new char[end - buf];
	lbuf = entity->_he_hdrbuf;
	lend = lbuf + (end - buf);
	WDEBUG((WLOG_DEBUG, "parse_headers: %d in buffer before read of %d",
		(int)EVBUFFER_LENGTH(entity->_he_frombuf->input), end - buf));
	if ((i = bufferevent_read(entity->_he_frombuf, lbuf, end - buf)) != (end - buf)) {
		WDEBUG((WLOG_DEBUG, "expected %d bytes, read %d!", end - buf, i));
		abort();
	}
	WDEBUG((WLOG_DEBUG, "parse_headers: %d left in buffer",
		(int)EVBUFFER_LENGTH(entity->_he_frombuf->input)));
	while ((nexthdr = find_rn(lbuf, lend)) != NULL) {
	char	*name = NULL, *value = NULL;
	size_t	 nlen, vlen;
	int	 error = 1;
		*nexthdr = '\0';
		line = lbuf; 
		lbuf = nexthdr + 2;
		if (!*line) {
			if (!entity->he_reqstr) {
				return ENT_ERR_INVREQ;
			}

			if (!sent_host && entity->he_rdata.request.host)
				evbuffer_add_printf(entity->he_extraheaders, "Host: %s\r\n",
					entity->he_rdata.request.host);

			entity->_he_state = ENTITY_STATE_DONE;
			return 0;
		}

		switch(entity->_he_state) {
		case ENTITY_STATE_START:
			entity->he_reqstr = wstrdup(line);
			if (parse_reqtype(entity) == -1) {
				return ENT_ERR_INVREQ;
			}
			entity->_he_state = ENTITY_STATE_HDR;
			break;
		case ENTITY_STATE_HDR:
			if (isspace(*line)) {
				char *s = line;
				if (!entity->he_headers.hl_hdrs.size()) {
					error = ENT_ERR_INVHDR;
					goto error;
				}
				while (isspace(*s))
					s++;
				entity->he_headers.append_last(s);
				continue;
			}

			name = line;
			value = strchr(name, ':');
			if (value == NULL) {
				error = ENT_ERR_INVREQ;
				goto error;
			}
			nlen = value - name;
			*value++ = '\0';
			vlen = nexthdr - value - 1;
			while (isspace(*value))
				++value;

			WDEBUG((WLOG_DEBUG, "header: from [%s], [%s] = [%s]",
				line, name, value));

			if (entity->he_headers.hl_hdrs.size() > MAX_HEADERS) {
				error = ENT_ERR_2MANY;
				goto error;
			}
			if (!strcasecmp(name, "Host")) {
				if (!validhost(value)) {
					error = ENT_ERR_INVHOST;
					goto error;
				}
				entity->he_headers.add(name, nlen, value, vlen);
				entity->he_rdata.request.host = value;
				sent_host = true;
			} else if (!strcasecmp(name, "Content-Length")) {
				entity->he_headers.add(name, nlen, value, vlen);
				entity->he_rdata.request.contlen = atoi(value);
			} else if (!strcasecmp(name, "Via")) {
				if (via_includes_me(value)) {
					error = ENT_ERR_LOOP;
					goto error;
				}
				entity->he_headers.add(name, nlen, value, vlen);
			} else if (!strcasecmp(name, "transfer-encoding")) {
				/* XXX */
				if (!strcasecmp(value, "chunked")) {
					entity->he_te |= TE_CHUNKED;
				}
				/* Don't forward transfer-encoding... */
			} else if (!strcasecmp(name, "Accept-Encoding")) {
				if (!entity->he_flags.response &&
				    qvalue_parse(entity->he_rdata.request.accept_encoding, value) == -1) {
					error = ENT_ERR_INVAE;
					WDEBUG((WLOG_DEBUG, "a-e parse failed"));
					goto error;
				}
			} else if (!strcasecmp(name, "User-Agent") && strstr(value, "MSIE")) {
				/*
				 * Some MSIE versions cannot handle chunked replies properly.
				 * Force a fallback to HTTP/1.0.
				 * http://support.microsoft.com/kb/263754
				 */
				entity->he_rdata.request.httpmin = 0;
			} else if (config.ncaches) {
				if (!strcasecmp(name, "Pragma")) {
					entity->he_h_pragma = wstrdup(value);
					entity->he_headers.add(name, nlen, value, vlen);
				} else if (!strcasecmp(name, "Cache-Control")) {
					entity->he_h_cache_control = wstrdup(value);
					entity->he_headers.add(name, nlen, value, vlen);
				} else if (!strcasecmp(name, "If-Modified-Since")) {
					entity->he_h_if_modified_since = wstrdup(value);
					entity->he_headers.add(name, nlen, value, vlen);
				} else if (!strcasecmp(name, "Transfer-Encoding")) {
					entity->he_h_transfer_encoding = wstrdup(value);
					entity->he_headers.add(name, nlen, value, vlen);
				} else if (!strcasecmp(name, "Last-Modified")) {
					entity->he_h_last_modified = wstrdup(value);
					entity->he_headers.add(name, nlen, value, vlen);
				} else
					entity->he_headers.add(name, nlen, value, vlen);
			} else
				entity->he_headers.add(name, nlen, value, vlen);
			break;
		error:
			return -error;
		}
	}
	return 0;
}

static int 
parse_reqtype(http_entity *entity)
{
	char	*p, *s, *t;
	char	*request = entity->he_reqstr;;
	int	i;

	WDEBUG((WLOG_DEBUG, "parse_reqtype: called, response=%d", (int)entity->he_flags.response));
	
	/*
	 * These probably shouldn't be handled in the same function.
	 */
	if (entity->he_flags.response) {
		/* 
		 * HTTP/1.0
		 */
		if ((p = strchr(request, ' ')) == NULL)
			return -1;
		*p++ = '\0';
		
		/* 200 */
		if ((s = strchr(p, ' ')) == NULL)
			return -1;
		*s++ = '\0';
		entity->he_rdata.response.status = atoi(p);
		
		/* OK */
		entity->he_rdata.response.status_str = s;
		
		WDEBUG((WLOG_DEBUG, "parse_reqtype: \"%s\" \"%d\" \"%s\"",
				request, entity->he_rdata.response.status,
				entity->he_rdata.response.status_str));
		return 0;
	}	
	
	/* GET */
	if ((p = strchr(request, ' ')) == NULL)
		return -1;

	*p++ = '\0';

	for (i = 0; supported_reqtypes[i].name; i++)
		if (!strcmp(request, supported_reqtypes[i].name))
			break;

	entity->he_rdata.request.reqtype = supported_reqtypes[i].type;
	if (entity->he_rdata.request.reqtype == REQTYPE_INVALID)
		return -1;

	/* /path/to/file */
	if ((s = strchr(p, ' ')) == NULL)
		return -1;

	*s++ = '\0';
	if (*p != '/') {
		/*
		 * This normally means the request URI was of the form
		 * "http://host.tld/file".  Clients don't send this, but
		 * Squid does when it thinks we're a proxy.
		 *
		 * Extract the host and set it now.  If there's another host
		 * later, it'll overwrite it, but if the client sends two
		 * different hosts it's probably broken anyway...
		 *
		 * We could handle non-http URIs here, but there's not much
		 * points, the backend will reject it anyway.
		 */
		if (strncmp(p, "http://", 7))
			return -1;
		p += 7;
		t = strchr(p, '/');
		if (t == NULL)
			return -1;
		memmove(p - 1, p, t - p);
		entity->he_rdata.request.host = p - 1;
		t[-1] = '\0';
		entity->he_rdata.request.path = t;
	} else {
		entity->he_rdata.request.path = p;
	}

	/* HTTP/1.0 */
	if (sscanf(s, "HTTP/%d.%d", &entity->he_rdata.request.httpmaj,
			&entity->he_rdata.request.httpmin) != 2)
		return -1;

	return 0;
}

int
qvalue_parse(set<qvalue> &list, const char *header)
{
char **values;
char **value;

	values = wstrvec(header, ",", 0);
	for (value = values; *value; ++value) {
	char	**bits;
	qvalue	  entry;
		bits = wstrvec(*value, ";", 0);
		entry.name = wstrdup(bits[0]);
		if (bits[1])
			entry.val = atof(bits[1]);
		else
			entry.val = 1.0;
		list.insert(entry);
		wstrvecfree(bits);
	}

	wstrvecfree(values);
	return 0;
}

bool
qvalue_remove_best(set<qvalue> &list, qvalue &val)
{
set<qvalue>::iterator	it;
	if ((it = list.begin()) == list.end())
		return false;
	val = *it;
	list.erase(it);
	return true;
}

enum encoding
accept_encoding(const char *enc)
{
static	struct {
	const char *name;
	enum encoding value;
} encs[] = {
	{ "deflate",	E_DEFLATE	},
	{ "x-deflate",	E_X_DEFLATE	},
	{ "identity",	E_NONE		},
	{ "gzip",	E_GZIP		},
	{ "x-gzip",	E_X_GZIP	},
	{ NULL, E_NONE }
}, *s;
	for (s = encs; s->name; s++)
		if (!strcasecmp(s->name, enc))
			return s->value;
	return E_NONE;
}
