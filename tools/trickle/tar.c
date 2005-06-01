/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * trickle: copy one directory to another, slowly.
 */

#pragma ident "@(#) $Id$"

#include <sys/types.h>
#include <sys/stat.h>

#include <tar.h>
#include <unistd.h>
#include <string.h>
#include <stdlib.h>

#include "trickle.h"

void
tar_writeheader(file, name)
	FILE *file;
	const char *name;
{
struct	tar	 hdr;
struct	stat	 sb;
	char	*buf;
	int	 sum = 0, i;
	int	 truncate = 0;

	if (lstat(name, &sb) < 0) {
		perror(name);
		exit(8);
	}

	/*
	 * This is a very lax tar header.  It's accepted by Solaris tar
	 * and GNU tar, but misses some information we don't use.
	 */
	
	/*
	 * Long filenames can't be represented in standard tar(1).  If -p
	 * was specified, we write a pax(1) extended tar header, type "x",
	 * which contains the real filename.  However, we still write the
	 * standard ustar header to give tar users a chance to extract it.
	 *
	 * The pax format is described in IEEE Std 1003.1-2004, "The Open 
	 * Group Base Specifications Issue 6".
	 */
	if (strlen(curdir) > 155) {
		fprintf(stderr, "%s: warning: directory for %s%s truncated to 155 characters in ustar header\n",
				progname, curdir, name);
		truncate++;
	}

	if (strlen(name) > 100) {
		fprintf(stderr, "%s: warning: filename for %s%s truncated to 100 characters in ustar header\n",
				progname, curdir, name);
		truncate++;
	}

	if (truncate && pflag) {
		/*
		 * Write pax header.  It's just a tar header with type='x'.
		 */
		char *tardata;
		int len;

		/* Filename:
		 *   <length> path=<path>\n
		 * <length> includes the length of the decimal representation of
		 * itself!  just pad it to 31 characters...
		 */
		len = 31 + 6 + strlen(curdir) + 1 + strlen(name);
		tardata = allocf("%031d path=%s%s\n", len, curdir, name);

		memset(&hdr, 0, sizeof(hdr));
		sprintf(hdr.tr_size, "%011o", strlen(tardata));
		strcpy(hdr.tr_name, "@PAX.HEADER");
		sprintf(hdr.tr_mode, "%07o", 0600);
		memcpy(hdr.tr_magic, TMAGIC, TMAGLEN);
		memcpy(hdr.tr_version, TVERSION, TVERSLEN);
		hdr.tr_typeflag = 'x';
		strncpy(hdr.tr_chksum, "        ", 8);

		for (sum = 0, buf = (void *)&hdr, i = sizeof(hdr); i--;)
		       sum += *buf++;
		snprintf(hdr.tr_chksum, 8, "%06o", sum);

		if (write_blocked(&hdr, sizeof(hdr), file) < 1) {
			perror(dest);
			exit(8);
		}
		if (write_blocked(tardata, strlen(tardata), file) < 1) {
			perror(dest);
			exit(8);
		}
	}		

	memset(&hdr, 0, sizeof(hdr));

	/*
	 * Trim the first two characters of curdir, which is always "./", and the last, 
	 * which is always a "/".  Saves 3 bytes for pathname...
	 */
	strncpy(hdr.tr_prefix, curdir + 2, min(sizeof(hdr.tr_prefix), max(0, strlen(curdir + 2) - 1)));
	strncpy(hdr.tr_name, name, sizeof(hdr.tr_name));

	sprintf(hdr.tr_mode, "%07o", (int)(sb.st_mode & 0777));
	sprintf(hdr.tr_uid, "%07o", (int)sb.st_uid);
	sprintf(hdr.tr_gid, "%07o", (int)sb.st_gid);
	sprintf(hdr.tr_size, "%011o", (int)sb.st_size);
	sprintf(hdr.tr_mtime, "%011o", (int)sb.st_mtime);
	memcpy(hdr.tr_magic, TMAGIC, TMAGLEN);
	memcpy(hdr.tr_version, TVERSION, TVERSLEN);
	sprintf(hdr.tr_uname, "%d", (int)sb.st_uid);
	sprintf(hdr.tr_gname, "%d", (int)sb.st_gid);
	strncpy(hdr.tr_chksum, "        ", 8);
	hdr.tr_typeflag = REGTYPE;
	
	for (sum = 0, buf = (void *)&hdr, i = sizeof(hdr); i--;)
	       sum += *buf++;
	snprintf(hdr.tr_chksum, 8, "%06o", sum);

	if (write_blocked(&hdr, sizeof(hdr), file) < 1) {
		perror(dest);
		exit(8);
	}
}

void
tar_writeeof(file)
	FILE *file;
{
static	char	zbuf[1];

	/*
	 * Two-block EOF.
	 */
	write_blocked(zbuf, 1, file);
	write_blocked(zbuf, 1, file);
}
