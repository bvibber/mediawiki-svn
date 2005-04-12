/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * trickle: copy one directory to another, slowly.
 */

#include <sys/types.h>
#include <sys/stat.h>

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <alloca.h>
#include <unistd.h>
#include <dirent.h>
#include <limits.h>
#include <errno.h>
#include <utime.h>
#include <tar.h>

#define min(x,y) ((x) < (y) ? (x) : (y))
#define max(x,y) ((x) < (y) ? (y) : (x))

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
} __attribute__((packed));

const char *progname;

int tflag, uflag;
int blocksleep, filesleep;
int blocksize = 8192;
char *src, *dest;
char *curdir;
FILE *tarfile;

static void copy_directory(const char *dir);
static void copy_file(const char *name, struct stat *sb);
static size_t write_blocked(void *buf, size_t size, FILE *file);
static void write_tarheader(const char *name, struct stat *sb);
static void write_tareof(void);
static int newer(const char *fa, const char *fb);

void __attribute__((noreturn))
usage(void)
{
	fprintf(stderr,
		"Usage: %s -s blocksize [-b blocksleep] [-f filesleep] [-tu] <src> <dest>\n"
		"\t-s blocksize          amount of data to read/write at one time\n"
		"\t-b blocksleep         time to sleep between each block (microseconds)\n"
		"\t-f filesleep          time to sleep between each file (microseconds)\n"
		"\t-t                    output a tar(1) file called <dest> instead of copying\n"
		"\t-u                    don't copy files with a modification date older than the target\n",
		progname);
	exit(8);
}

int
main(argc, argv)
	char *argv[];
{
	int	i;

	progname = argv[0];

	while ((i = getopt(argc, argv, "uts:b:f:")) != -1) {
		switch(i) {
		case 't':
			tflag++;
			break;
		case 'u':
			uflag++;
			break;
		case 's':
			blocksize = atoi(optarg);
			break;
		case 'b':
			blocksleep = atoi(optarg);
			break;
		case 'f':
			filesleep = atoi(optarg);
			break;
		case 'h':
		default:
			usage();
		}
	}
	argc -= optind;
	argv += optind;

	if (argc != 2)
		usage();

	if (uflag && tflag) {
		fprintf(stderr, "%s: -u and -t may not be specified together\n", progname);
		usage();
	}

	src = argv[0];
	dest = argv[1];

	/*
	 * Ensure dest is an absolute path.
	 */
	if (*dest != '/' && strcmp(dest, "-")) {
		char *cwd = getcwd(NULL, PATH_MAX);
		char *olddest = dest;
		dest = alloca(strlen(cwd) + strlen(olddest) + 2);
		sprintf(dest, "%s/%s", cwd, olddest);
		free(cwd);
	}

	fprintf(stderr, "Copying from %s to %s%s, using blocksize %d, sleeps block/file %d/%d\n",
		src, tflag ? "tar file " : "", dest, blocksize, blocksleep, filesleep);

	if (tflag) {
		if (!strcmp(dest, "-"))
			tarfile = stdout;
		else if ((tarfile = fopen(dest, "w")) == NULL) {
			perror(dest);
			exit(8);
		}
	}

	if (chdir(src) < 0) {
		perror("chdir");
		exit(8);
	}

	curdir = strdup("");
	copy_directory(".");
	
	if (tarfile) {
		write_tareof();
		if (tarfile != stdout)
			fclose(tarfile);
	}

	return 0;
}

static void
copy_directory(dir)
	const char *dir;
{
	DIR 	*dirp;
struct	dirent	*dp;
struct	stat	 sb;
	char	*oldcur;

	if (chdir(dir) < 0) {
		perror("chdir");
		exit(8);
	}

	oldcur = curdir;
	curdir = alloca(strlen(oldcur) + strlen(dir) + 2);
	sprintf(curdir, "%s%s/", oldcur, dir);

	if ((dirp = opendir(".")) == NULL) {
		perror(dir);
		exit(8);
	}
	
	while (dp = readdir(dirp)) {
		if (!strcmp(dp->d_name, ".") || !strcmp(dp->d_name, ".."))
			continue;
	
		if (stat(dp->d_name, &sb) < 0) {
			perror("stat");
			exit(8);
		}

		if (sb.st_mode & S_IFDIR) {
			char *dpath;
			fprintf(stderr, "d  %s%s\n", curdir, dp->d_name);
			/*
			 * If not creating a tar file, we need to create the destination directory.
			 */
			if (!tflag) {
				dpath = alloca(strlen(dest) + strlen(curdir) + strlen(dp->d_name) + 3);
				sprintf(dpath, "%s/%s%s", dest, curdir, dp->d_name);
				/*
				 * We don't care about permissions, so if the directory already
				 * exists, just leave it.
				 */
				if (mkdir(dpath, sb.st_mode) < 0 && errno != EEXIST) {
					perror(dpath);
					exit(8);
				}
			}
			copy_directory(dp->d_name);
		} else if (sb.st_mode & S_IFREG) {
			copy_file(dp->d_name, &sb);
			usleep(filesleep);
		} else {
			/*
			 * Ignore special files...
			 */
			fprintf(stderr, "%s: ignoring %s%s: neither directory nor file\n", progname, curdir, dp->d_name);
		}
	}
	closedir(dirp);

	chdir("..");
	curdir = oldcur;
}

static void
write_tarheader(name, sb)
	const char *name;
	struct stat *sb;
{
struct	tar	 hdr;
	char	*buf;
	int	 sum = 0, i;

	/*
	 * This is a very lax tar header.  It's accepted by Solaris tar
	 * and GNU tar, but misses some information we don't use.
	 */
	memset(&hdr, 0, sizeof(hdr));
	if (strlen(curdir) > 155)
		fprintf(stderr, "%s: warning: directory for %s%s truncated to 155 characters\n",
				progname, curdir, name);
	/*
	 * Trim the first two characters of curdir, which is always "./", and the last, 
	 * which is always a "/".  Saves 3 bytes for pathname...
	 */
	strncpy(hdr.tr_prefix, curdir + 2, min(sizeof(hdr.tr_prefix), max(0, strlen(curdir + 2) - 1)));
	if (strlen(name) > 100)
		fprintf(stderr, "%s: warning: filename for %s%s truncated to 100 characters\n",
				progname, curdir, name);
	strncpy(hdr.tr_name, name, sizeof(hdr.tr_name));

	sprintf(hdr.tr_mode, "%07o", (int)(sb->st_mode & 0777));
	sprintf(hdr.tr_uid, "%07o", (int)sb->st_uid);
	sprintf(hdr.tr_gid, "%07o", (int)sb->st_gid);
	sprintf(hdr.tr_size, "%011o", (int)sb->st_size);
	sprintf(hdr.tr_mtime, "%011o", (int)sb->st_mtime);
	memcpy(hdr.tr_magic, TMAGIC, TMAGLEN);
	memcpy(hdr.tr_version, TVERSION, TVERSLEN);
	sprintf(hdr.tr_uname, "%d", (int)sb->st_uid);
	sprintf(hdr.tr_gname, "%d", (int)sb->st_gid);
	strncpy(hdr.tr_chksum, "        ", 8);
	hdr.tr_typeflag = REGTYPE;
	
	for (buf = &hdr, i = sizeof(hdr); i--;)
	       sum += *buf++;
	snprintf(hdr.tr_chksum, 8, "%06o", sum);

	if (write_blocked(&hdr, sizeof(hdr), tarfile) < 1) {
		perror(dest);
		exit(8);
	}
}

static void
copy_file(name, sb)
	const char *name;
	struct stat *sb;
{
	FILE	*f, *out = NULL;
	char	*buf, *outname;
	size_t	 bsize;

	if (tflag) {
		write_tarheader(name, sb);
	} else {
		outname = alloca(strlen(dest) + strlen(curdir) + strlen(name) + 3);
		sprintf(outname, "%s/%s%s", dest, curdir, name);

		if (uflag && newer(outname, name)) {
			fprintf(stderr, "fu %s%s\n", curdir, name);
			return;
		}

		if ((out = fopen(outname, "w")) == NULL) {
			perror(outname);
			exit(8);
		}
	}

	if ((f = fopen(name, "r")) == NULL) {
		perror(name);
		exit(8);
	}

	buf = alloca(blocksize);
	while (bsize = fread(buf, 1, blocksize, f)) {
		if (bsize < blocksize) {
			if (ferror(f)) {
				perror(dest);
				exit(8);
			}
		}
		if (tflag) {
			if (write_blocked(buf, bsize, tarfile) < 1) {
				perror(dest);
				exit(8);
			}
		} else {
			if (fwrite(buf, bsize, 1, out) < 1) {
				perror(outname);
				exit(8);
			}
		}
		usleep(blocksleep);
	}

	if (out)
		fclose(out);
	fclose(f);
	if (tflag)
		fflush(tarfile);
	else {
		struct utimbuf ut;
		ut.actime = sb->st_atime;
		ut.modtime = sb->st_mtime;
		if (utime(outname, &ut) < 0) {
			perror(outname);
			exit(8);
		}
	}

	fprintf(stderr, "f  %s%s %d bytes, %d blocks\n", curdir, name, (int)sb->st_size, (int)sb->st_blocks);

}

static size_t
write_blocked(buf, size, file)
	void *buf;
	size_t size;
	FILE *file;
{
	char	 block[512];
	char	*p = buf;
	size_t	 ret = 0, tow;

	while (size) {
		tow = min(size, sizeof(block));

		memset(block, 0, sizeof block);
		memcpy(block, p, tow);
		if ((ret = fwrite(block, sizeof(block), 1, file)) < 1)
			return ret;
		p += tow;
		size -= tow;
	}
	return ret;
}

static int
newer(fa, fb)
	const char *fa, *fb;
{
struct	stat	sa, sb;
	if (stat(fa, &sa) < 0) {
		if (errno == ENOENT)
			return 0;
		perror(fa);
		exit(8);
	}

	if (stat(fb, &sb) < 0) {
		if (errno == ENOENT)
			return 0;
		perror(fb);
		exit(8);
	}

	return sa.st_mtime > sb.st_mtime;
}

static void
write_tareof(void)
{
	char buf[1] = {};

	/*
	 * Two-block EOF.
	 */
	write_blocked(buf, 1, tarfile);
	write_blocked(buf, 1, tarfile);
}
