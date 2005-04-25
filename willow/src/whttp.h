/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * Willow: Lightweight HTTP reverse-proxy.
 * whttp: HTTP implementation.
 */

#ifndef WHTTP_H
#define WHTTP_H

struct readbuf {
	char	*rb_p;		/* start of allocated region	*/
	int	 rb_size;	/* size of allocated region	*/
	int	 rb_dsize;	/* [p,p+dsize) is valid data	*/
	int	 rb_dpos;	/* current data position	*/
};
#define READBUF_SPARE_SIZE(b) ((b)->rb_size - (b)->rb_dsize)
#define READBUF_SPARE_START(b) ((b)->rb_p + (b)->rb_dsize)
#define READBUF_DATA_LEFT(b) ((b)->rb_dsize - (b)->rb_dpos)
#define READBUF_INC_DATA_POS(b) ((b)->rb_dpos++)
#define READBUF_CUR_POS(b) ((b)->rb_p + (b)->rb_dpos)

struct fde;

void http_new(struct fde *);
void whttp_init(void);

#endif
