/* @(#) $Header$ */
/* This source code is released into the public domain. */
/*
 * RDCP: Header definitions.
 */

#ifndef SM_RDCP_H_INCLUDED
#define SM_RDCP_H_INCLUDED

#include <sys/types.h>

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
 * Result codes.
 */
#define RDCP_RES_OK		1	/* Operation succeeded		*/
#define RDCP_RES_ERR		0	/* Operation failed 		*/
#define RDCP_RES_MASK 		0x1
#define RDCP_IS_OK(x) (((x) & RDCP_RES_MASK) == RDCP_RES_OK)

/*
 * Error codes.
 */
#define R_ERR_DISAGREE		1	/* Peer disagreed about stream format. 		*/
#define R_ERR_WRONGSIZE		2	/* Frame size was wrong for this stream.	*/
#define R_ERR_INVARG		3	/* Invalid internal argument.			*/
#define R_ERR_XTI		4	/* Ask XTI for the error.			*/

#define R_ERR_MASK 0xE
#define R_ERR(x) ((((x) & R_ERR_MASK)) >> 1)

/*
 * Record types.
 */
#define RDCP_RT_FIXED	1	/* Fixed-size records		*/
#define RDCP_RT_VAR	2	/* Variable-length records	*/

/*
 * Bind an XTI descriptor to an RDCP handle and negotiate preferences
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
void rdcp_handle_free(struct rdcp_handle*);

/*
 * Format RDCP error as string. 
 */
const char *rdcp_strerror(struct rdcp_handle *);

/*
 * Return XTI error number for handle.
 */
int rdcp_xtierrno(struct rdcp_handle *);

#endif

