/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * trickle: copy one directory to another, slowly.
 */

#ifndef TRICKLE_H
#define TRICKLE_H

#pragma ident "@(#) $Id$"

#include <sys/types.h>

#include <stdio.h>

#include "t_stdint.h"
#ifdef T_STDINT
# include <stdint.h>
#endif

#define min(x,y) ((x) < (y) ? (x) : (y))
#define max(x,y) ((x) < (y) ? (y) : (x))

#define RECORD_SIZE	20	/* one record = RECORD_SIZE blocks	*/

extern int pflag;
extern char *dest;		/* destination name	*/
extern const char *progname;	/* argv[0]		*/
extern char *curdir;		/* cwd name		*/
extern char *trickle;
extern int records;

size_t write_blocked(void *buf, size_t size, FILE *file);
char *allocf();
void fatal();
void pfatal(const char *, const char *);

/** Protocol support */
#define PROTO_VERS	1
#define P_ACCEPT 1
#define P_DECLINE 2
#define T_FILE 1
#define T_DIR 2

struct pfile {
	char	*name;
	int	 type;
	int	 uid, gid;
	int	 actime, mtime;
	int	 mode;
};

struct rdcp_frame;
struct stat;

int		 proto_neg		(int);
int		 proto_rsh		(const char *, const char *);
struct pfile 	*proto_getfile		(void);
void		 proto_decline		(void);
void		 proto_accept		(void);
int		 proto_read		(struct rdcp_frame *);
void		 proto_write		(struct rdcp_frame *);
int		 proto_offer		(const char *file, struct stat *);
void		 proto_eof		(void);
void		 proto_writeblock	(void *, size_t);
char		*proto_readdir		(void);
void		 proto_ack		(void);
void		 proto_nack		(const char *error);

/** Tar support */

/*
 * POSIX 1003.1-1990/SUSv2 tar(1) header.
 *
 * Regarding name/prefix, SUSv2 says:
 *
 *    The name and the prefix fields produce the pathname of the file. The 
 *    hierarchical relationship of the file can be retained by specifying the 
 *    pathname as a path prefix, and a slash character and filename as the 
 *    suffix. A new pathname is formed, if prefix is not an empty string (its 
 *    first character is not NUL), by concatenating prefix (up to the first NUL 
 *    character), a slash character and name; otherwise, name is used alone. 
 *    In either case, name is terminated at the first NUL character. If prefix 
 *    begins with a NUL character, it will be ignored. In this manner, pathnames 
 *    of at most 256 characters can be supported.
 */
struct tar {
	char tr_name[100];	/* file name		*/
	char tr_mode[8];	/* mode			*/
	char tr_uid[8];		/* owner (numeric)	*/
	char tr_gid[8];		/* group (numeric)	*/
	char tr_size[12];	/* size in bytes	*/
	char tr_mtime[12];	/* mtime		*/
	char tr_chksum[8];	/* checksum of header	*/
	char tr_typeflag;	/* file type		*/
	char tr_linkname[100];	/* symlink target	*/
	char tr_magic[6];	/* tar magic: "ustar "	*/
	char tr_version[2];	/* tar version: "00"	*/
	char tr_uname[32];	/* owner (string)	*/
	char tr_gname[32];	/* group (string)	*/
	char tr_devmajor[8];	/* device major		*/
	char tr_devminor[8];	/* device minor		*/
	char tr_prefix[155];	/* directory		*/
};

void tar_writeheader(FILE *file, const char *name);
void tar_writeeof(FILE *file);

#endif
