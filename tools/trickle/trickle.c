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
#include <tar.h>

#define min(x,y) ((x) < (y) ? (x) : (y))

struct tar {
	char tr_name[100];
	char tr_mode[8];
	char tr_uid[8];
	char tr_gid[8];
	char tr_size[12];
	char tr_mtime[12];
	char tr_chksum[8];
	char tr_typeflag;
	char tr_linkname[100];
	char tr_magic[6];
	char tr_version[2];
	char tr_uname[32];
	char tr_gname[32];
	char tr_devmajor[8];
	char tr_devminor[8];
	char tr_prefix[155];
} __attribute__((packed));

const char *progname;

int tflag;
int blocksleep, filesleep;
int blocksize;
char *src, *dest;
char *curdir;
FILE *tarfile;

static void copy_directory(const char *dir);
static void copy_file(const char *name, struct stat *sb);
static size_t write_blocked(void *buf, size_t size, FILE *file);
static void write_tarheader(const char *name, struct stat *sb);

void __attribute__((noreturn))
usage(void)
{
	fprintf(stderr,
		"Usage: %s -s blocksize -b blocksleep -f filesleep [-t] <dir1> <dest>\n"
		"\t-b blocksleep         time to sleep between each block (microseconds)\n"
		"\t-s blocksize          amount of data to reach at one time\n"
		"\t-f filesleep          time to sleep between each file (microseconds)\n"
		"\t-t                    output a tar(1) file instead of copying\n",
		progname);
	exit(8);
}

int
main(argc, argv)
	char *argv[];
{
	int	i;

	progname = argv[0];

	while ((i = getopt(argc, argv, "ts:b:f:")) != -1) {
		switch(i) {
		case 't':
			tflag++;
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

	if (!blocksize || !blocksleep || !filesleep)
		usage();

	src = argv[0];
	dest = argv[1];

	if (*dest != '/') {
		char *cwd = getcwd(NULL, PATH_MAX);
		char *olddest = dest;
		dest = alloca(strlen(cwd) + strlen(olddest) + 2);
		sprintf(dest, "%s/%s", cwd, olddest);
		free(cwd);
	}

	fprintf(stderr, "Copying from %s to %s%s, using blocksize %d, sleeps block/file %d/%d\n",
		src, tflag ? "tar file " : "", dest, blocksize, blocksleep, filesleep);

	if (tflag) {
		if ((tarfile = fopen(dest, "w")) == NULL) {
			perror(dest);
			exit(8);
		}
	}

	if (chdir(src) < 0) {
		perror("chdir");
		exit(8);
	}

	curdir = strdup(".");
	copy_directory(curdir);
	
	if (tarfile)
		fclose(tarfile);
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
	sprintf(curdir, "%s/%s", oldcur, dir);

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
			fprintf(stderr, "d %s/%s\n", curdir, dp->d_name);
			dpath = alloca(strlen(dest) + strlen(curdir) + strlen(dp->d_name) + 3);
			sprintf(dpath, "%s/%s/%s", dest, curdir, dp->d_name);
			if (mkdir(dpath, 0777) < 0) {
				perror(dpath);
				exit(8);
			}
			copy_directory(dp->d_name);
		} else if (sb.st_mode & S_IFREG) {
			fprintf(stderr, "f %s/%s\n", curdir, dp->d_name);
			copy_file(dp->d_name, &sb);
			usleep(filesleep);
		} else {
			fprintf(stderr, "%s: ignoring %s/%s: neither directory nor file\n", progname, curdir, dp->d_name);
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

	memset(&hdr, 0, sizeof(hdr));
	snprintf(hdr.tr_name, sizeof(hdr.tr_name), "%s/%s", curdir, name);
	sprintf(hdr.tr_mode, "%07o", sb->st_mode & 0777);
	sprintf(hdr.tr_uid, "%07o", sb->st_uid);
	sprintf(hdr.tr_gid, "%07o", sb->st_gid);
	sprintf(hdr.tr_size, "%011o", sb->st_size);
	sprintf(hdr.tr_mtime, "%011o", sb->st_mtime);
	memcpy(hdr.tr_magic, TMAGIC, TMAGLEN);
	memcpy(hdr.tr_version, TVERSION, TVERSLEN);
	sprintf(hdr.tr_uname, "%d", sb->st_uid);
	sprintf(hdr.tr_gname, "%d", sb->st_gid);
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

	if ((f = fopen(name, "r")) == NULL) {
		perror(name);
		exit(8);
	}

	if (tflag) {
		write_tarheader(name, sb);
	} else {
		outname = alloca(strlen(dest) + strlen(curdir) + strlen(name) + 3);
		sprintf(outname, "%s/%s/%s", dest, curdir, name);
		if ((out = fopen(outname, "w")) == NULL) {
			perror(outname);
			exit(8);
		}
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
	if (tflag)
		fflush(tarfile);
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
