/* @(#) $Header$ */
/* This source code is released into the public domain. */
/*
 * RDCP: Header definitions.
 */

#ifndef SM_RDCP_H_INCLUDED
#define SM_RDCP_H_INCLUDED

#include <sys/types.h>

#if defined(__SVR4) || defined(__svr4__)
# define RDCP_SVR4
#endif

/*
 * Opaque handle to RDCP instance.
 */
struct rdcp_handle;

/*
 * Communications preferences for opening.
 */
struct rdcp_prefer {
	int rp_rtype;	/* Record type 				*/
	int rp_rsize;	/* Record size, or 0 for variable. 	*/
};

/*
 * A frame.
 */
struct rdcp_frame {
	size_t	 rf_len;
	void	*rf_buf;
};

/*
 * Error codes.
 */
#define R_ERR_DISAGREE		1	/* Peer disagreed about stream format. 		*/
#define R_ERR_WRONGSIZE		2	/* Frame size was wrong for this stream.	*/
#define R_ERR_INVARG		3	/* Invalid internal argument.			*/
#define R_ERR_SYSERR		4	/* Ask XTI for the error.			*/
#define R_ERR_CLOSED		5	/* Connection was already closed.		*/
#define R_ERR_NOMEM		6	/* Out of memory.				*/
/*
 * Record types.
 */
#define RDCP_RT_FIXED	1	/* Fixed-size records		*/
#define RDCP_RT_VAR	2	/* Variable-length records	*/

/*
 * Options.
 */
#define ROPT_RDWR	1
#ifdef RDCP_SVR4
#define ROPT_XTI	2
#endif

/*
 * Bind a file descriptor to an RDCP handle and negotiate preferences
 * with peer.
 */
int rdcp_bind(int desc, struct rdcp_handle* handle, struct rdcp_prefer *opts);

/*
 * Unbind a handle and deallocate its resouces.  Does not close
 * the descriptor.
 */
int rdcp_unbind(struct rdcp_handle*);

/*
 * Write one frame to the network.
 */
int rdcp_write(struct rdcp_handle*, struct rdcp_frame*);

/*
 * Read one frame from the network.  
 */
int rdcp_read(struct rdcp_handle*, struct rdcp_frame*);

/*
 * Deallocate a frame's data.
 */
int rdcp_frame_free(struct rdcp_frame*);

/*
 * Allocate a new handle.
 */
struct rdcp_handle *rdcp_handle_alloc(void);

/*
 * Free a handle.  Does not call rdcp_unbind.
 */
int rdcp_handle_free(struct rdcp_handle*);

/*
 * Format RDCP error as string. 
 */
const char *rdcp_strerror(int);

/*
 * Set descriptor type (rdrw / xti)
 */
int rdcp_handle_type(struct rdcp_handle*, int type);

#endif
