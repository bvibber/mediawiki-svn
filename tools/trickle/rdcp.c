/* @(#) $Header$ */
/* This source code is released into the public domain. */
/*
 * RDCP: Implementation.
 */

#include <sys/types.h>

#include <string.h>
#include <stdlib.h>
#include <unistd.h>
#include <alloca.h>

#include "t_stdint.h"
#ifdef T_STDINT
# include <stdint.h>
#endif
#include "t_xti.h"
#ifdef T_XTI
# include <xti.h>
#endif
#include "rdcp.h"

#define F_CLOSED 1

#define HANDLE_OK(h) ((h) && (h)->desc != -1 && (h)->recvf && (h)->sendf && !((h)->flags & F_CLOSED))

typedef int (*rdcp_internal_send_t)(int, const void *, size_t);
typedef int (*rdcp_internal_recv_t)(int, void *, size_t);

struct rdcp_handle {
	int		desc, flags;
struct 	rdcp_prefer	opts;
	rdcp_internal_send_t sendf;
	rdcp_internal_recv_t recvf;
};

static int
rdcp_internal_send_rdwr(fd, data, size)
	const void *data;
	size_t size;
{
	return write(fd, data, size);
}

#ifdef T_XTI
static int
rdcp_internal_send_xti(fd, data, size)
	const void *data;
	size_t size;
{
	return t_snd(fd, (void *)data, size, 0);
}
#endif

static int
rdcp_internal_recv_rdwr(fd, data, size)
	void *data;
	size_t size;
{
	return read(fd, data, size);
}

#ifdef T_XTI
static int
rdcp_internal_recv_xti(fd, data, size)
	void *data;
	size_t size;
{
	int f;
	return t_rcv(fd, data, size, &f);
}
#endif

/*
 * Write to a descriptor and handle errors.
 */
int
rdcp_internal_send(handle, data, size)
	struct rdcp_handle *handle;
	void *data;
	size_t size;
{
	if (handle->sendf(handle->desc, data, size) < size)
		return R_ERR_SYSERR;

	return 0;
}

int
rdcp_internal_receive(handle, data, size)
	struct rdcp_handle *handle;
	void *data;
	size_t size;
{
	int len;
	char *p = data;

	while ((len = handle->recvf(handle->desc, p, size)) > -1) {
		size -= len;
		if (size == 0)
			return 0;
		p += size;
	}
	return R_ERR_SYSERR;
}

struct rdcp_handle *
rdcp_handle_alloc(void)
{
	struct rdcp_handle *handle;
	if ((handle = malloc(sizeof(struct rdcp_handle))) == NULL)
		return NULL;
	memset(handle, 0, sizeof(*handle));
#ifdef T_XTI
	handle->sendf = rdcp_internal_send_xti;
	handle->recvf = rdcp_internal_recv_xti;
#endif
	handle->desc = -1;
	return handle;
}

int
rdcp_handle_type(handle, type)
	struct rdcp_handle* handle;
{
	if (!handle)
		return R_ERR_INVARG;

	if (type == ROPT_RDWR) {
		handle->sendf = rdcp_internal_send_rdwr;
		handle->recvf = rdcp_internal_recv_rdwr;
#ifdef T_XTI
	} else if (type == ROPT_XTI) {
		handle->sendf = rdcp_internal_send_xti;
		handle->recvf = rdcp_internal_recv_xti;
#endif
	} else
		return R_ERR_INVARG;
	return 0;
}

int
rdcp_handle_free(handle)
	struct rdcp_handle *handle;
{
	if (!handle)
		return R_ERR_INVARG;
	free(handle);
	return 0;
}

int
rdcp_bind(desc, handle, opts)
	struct rdcp_handle *handle;
	struct rdcp_prefer *opts;
{
	int		i;
	unsigned char	c;

	if ((opts->rp_rtype < 1 || opts->rp_rtype > 2)
	    /* Zero-size records not allowed. */
	    || (opts->rp_rtype == RDCP_RT_FIXED && !opts->rp_rsize))
	    	return R_ERR_INVARG;

	if (!handle->recvf || !handle->sendf)
		return R_ERR_INVARG;

	handle->desc = desc;
	memcpy(&handle->opts, opts, sizeof(handle->opts));

	/*
	 * Write our data format preference to the stream. 
	 */
	c = (unsigned char) opts->rp_rtype;
	if (i = rdcp_internal_send(handle, &c, sizeof(c)))
		return i;

	/*
	 * Record size for fixed-size streams.
	 */
	uint16_t rs = opts->rp_rsize;
	if (rs && (i = rdcp_internal_send(handle, &rs, sizeof(rs))))
		return i;
	
	/*
	 * Read record type for peer. 
	 */
	if (i = rdcp_internal_receive(handle, &c, sizeof(c)))
		return i;
	if (c != opts->rp_rtype)
		return R_ERR_DISAGREE;

	if (rs) {
		if (i = rdcp_internal_receive(handle, &rs, sizeof(rs)))
			return i;
		if (rs != opts->rp_rsize)
			return R_ERR_DISAGREE;
	}

	return 0;
}

int
rdcp_unbind(handle)
	struct rdcp_handle *handle;
{
	if (!HANDLE_OK(handle))
		return R_ERR_INVARG;
	handle->flags &= F_CLOSED;
	return 0;
}

int
rdcp_read(handle, frame)
	struct rdcp_handle *handle;
	struct rdcp_frame *frame;
{
	size_t	 rlen, have = 0;
	int 	 i;

	if (!HANDLE_OK(handle))
		return R_ERR_INVARG;

	if (handle->opts.rp_rtype == RDCP_RT_FIXED) {
		rlen = handle->opts.rp_rsize;
	} else if (handle->opts.rp_rtype == RDCP_RT_VAR){
		uint16_t flen;
		if (i = rdcp_internal_receive(handle, &flen, sizeof(flen)))
			return i;
		rlen = flen;
	} else
		return R_ERR_INVARG;

	if ((frame->rf_buf = malloc(rlen)) == NULL)
		return R_ERR_NOMEM;

	frame->rf_len = rlen;
	if (i = rdcp_internal_receive(handle, frame->rf_buf, rlen))
		return i;

	return 0;
}
		
int
rdcp_write(handle, frame)
	struct rdcp_handle *handle;
	struct rdcp_frame *frame;
{
	void	*data;
	size_t	 dlen;
	int	 i;

	if (!HANDLE_OK(handle))
		return R_ERR_INVARG;
	if (handle->opts.rp_rtype == RDCP_RT_FIXED) {
		if (handle->opts.rp_rsize != frame->rf_len)
			return R_ERR_WRONGSIZE;
		data = frame->rf_buf;
		dlen = frame->rf_len;
	} else if (handle->opts.rp_rtype == RDCP_RT_VAR) {
		data = alloca(frame->rf_len + sizeof(uint16_t));
		if (data == NULL)
			return R_ERR_NOMEM;
		uint16_t i = frame->rf_len;
		memcpy(data, &i, sizeof(i));
		memcpy((char *)data + sizeof(i), frame->rf_buf, i);
		dlen = i + sizeof(i);
	} else {
		return R_ERR_INVARG;
	}

	if (i = rdcp_internal_send(handle, data, dlen))
		return i;
	return 0;
}

int
rdcp_frame_free(frame)
	struct rdcp_frame *frame;
{
	free(frame->rf_buf);
	return 0;
}

static const char *errors[] = {
	/* 0 */ "success",
	/* 1 */ "peer disagrees about stream format",
	/* 2 */ "wrong frame size for stream",
	/* 3 */ "invalid argument",
	/* 4 */ "XTI error",
	/* 5 */ "connection closed",
	/* 6 */ "out of memory",
};

const char *
rdcp_strerror(err)
{
	const char *msg = errors[err];
}
