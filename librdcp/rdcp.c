/* @(#) $Header$ */
/* This source code is released into the public domain. */
/*
 * RDCP: Implementation.
 */

#include <sys/types.h>

#include <string.h>
#include <stdlib.h>
#include <alloca.h>
#include <xti.h>

#include "rdcp.h"

#define F_CLOSED 1

struct rdcp_handle {
	int		desc, flags, err, xerr;
struct 	rdcp_prefer	opts;
};

/*
 * Write to a descriptor and handle errors.
 */
int
rdcp_internal_send(handle, data, size)
	struct rdcp_handle *handle;
	void *data;
	size_t size;
{
	if (t_snd(handle->desc, data, size, 0) < size) {
		handle->err = RDCP_RES_ERR | (R_ERR_XTI << 1);
		handle->xerr = t_errno;
		return 1;
	}

	return 0;
}

int
rdcp_internal_receive(handle, data, size)
	struct rdcp_handle *handle;
	void *data;
	size_t size;
{
	int flags, len;
	char *p = data;

	while ((len = t_rcv(handle->desc, p, size, &flags)) > -1) {
		size -= len;
		if (size == 0)
			return 0;
		p += size;
	}
	handle->err = RDCP_RES_ERR | (R_ERR_XTI << 1);
	handle->xerr = t_errno;
	return 1;
}

struct rdcp_handle *
rdcp_handle_alloc(void)
{
	return malloc(sizeof(struct rdcp_handle));
}

void
rdcp_handle_free(handle)
	struct rdcp_handle *handle;
{
	free(handle);
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
		return handle->err = RDCP_RES_ERR | (R_ERR_INVARG << 1);

	memset(handle, 0, sizeof(*handle));
	handle->desc = desc;
	memcpy(&handle->opts, opts, sizeof(handle->opts));

	/*
	 * Write our data format preference to the stream. 
	 */
	c = (unsigned char) opts->rp_rtype;
	if (rdcp_internal_send(handle, &c, sizeof(c)))
		return handle->err;

	/*
	 * Record size for fixed-size streams.
	 */
	uint16_t rs = opts->rp_rsize;
	if (rs && rdcp_internal_send(handle, &rs, sizeof(rs)))
		return handle->err;
	
	/*
	 * Read record type for peer. 
	 */
	if (rdcp_internal_receive(handle, &c, sizeof(c)))
		return handle->err;
	if (c != opts->rp_rtype)
		return handle->err = RDCP_RES_ERR & (R_ERR_DISAGREE << 1);

	if (rs) {
		if (rdcp_internal_receive(handle, &rs, sizeof(rs)))
			return handle->err;
		if (rs != opts->rp_rsize)
			return handle->err = RDCP_RES_ERR & (R_ERR_DISAGREE << 1);
	}

	return handle->err = RDCP_RES_OK;
}

int
rdcp_unbind(handle)
	struct rdcp_handle *handle;
{
	handle->flags &= F_CLOSED;
	return handle->err = RDCP_RES_OK;
}

int
rdcp_read(handle, frame)
	struct rdcp_handle *handle;
	struct rdcp_frame *frame;
{
	size_t	 rlen, have = 0;

	if (handle->opts.rp_rtype == RDCP_RT_FIXED) {
		rlen = handle->opts.rp_rsize;
	} else if (handle->opts.rp_rtype == RDCP_RT_VAR){
		uint16_t flen;
		if (rdcp_internal_receive(handle, &flen, sizeof(flen)))
			return handle->err;
		rlen = flen;
	} else
		return handle->err = RDCP_RES_ERR | (R_ERR_INVARG << 1);

	frame->rf_buf = malloc(rlen);
	frame->rf_len = rlen;
	if (rdcp_internal_receive(handle, frame->rf_buf, rlen))
		return handle->err;

	return handle->err = RDCP_RES_OK;
}
		
int
rdcp_write(handle, frame)
	struct rdcp_handle *handle;
	struct rdcp_frame *frame;
{
	void	*data;
	size_t	 dlen;

	if (handle->opts.rp_rtype == RDCP_RT_FIXED) {
		if (handle->opts.rp_rsize != frame->rf_len)
			return handle->err = RDCP_RES_ERR | (R_ERR_WRONGSIZE << 1);
		data = frame->rf_buf;
		dlen = frame->rf_len;
	} else if (handle->opts.rp_rtype == RDCP_RT_VAR) {
		data = alloca(frame->rf_len + sizeof(uint16_t));
		uint16_t i = frame->rf_len;
		memcpy(data, &i, sizeof(i));
		memcpy((char *)data + sizeof(i), frame->rf_buf, i);
		dlen = i + sizeof(i);
	} else {
		return handle->err = RDCP_RES_ERR | (R_ERR_INVARG << 1);
	}

	if (rdcp_internal_send(handle, data, dlen))
		return handle->err;
	return handle->err = RDCP_RES_OK;
}

int
rdcp_frame_free(frame)
	struct rdcp_frame *frame;
{
	free(frame->rf_buf);
}

const char *errors[] = {
	/* 0 */ "success",
	/* 1 */ "peer disagrees about stream format",
	/* 2 */ "wrong frame size for stream",
	/* 3 */ "invalid argument",
	/* 4 */ "XTI error",
};

const char *
rdcp_strerror(handle)
	struct rdcp_handle *handle;
{
	const char *msg = errors[R_ERR(handle->err)];
}

int
rdcp_xtierrno(handle)
	struct rdcp_handle *handle;
{
	return handle->xerr;
}
