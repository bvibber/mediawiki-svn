/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * trickle: copy one directory to another, slowly.
 */

#include "trickle.h"

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
#include <strings.h>

const char *progname;

int tflag, uflag, Fflag, qflag, pflag;
int archive;
int blocksleep, filesleep;
int blocksize = 8192;
char *src, *dest;
char *curdir;
FILE *archfile;

static void copy_directory(const char *dir);
static void copy_file(const char *name, const char *outname);
static int newerorsame(const char *fa, const char *fb);
static int exclude(const char *name);
static void addexclude(const char *name);

static void (*arch_writeheader) (FILE *, const char *name);
static void (*arch_writeeof) (FILE *);

void
usage(void)
{
	fprintf(stderr,
"Usage: %s -s blocksize [-b blocksleep] [-f filesleep] [-tuF] <src> <dest>\n"
"\t-s blocksize          amount of data to read/write at one time\n"
"\t-b blocksleep         time to sleep between each block (microseconds)\n"
"\t-f filesleep          time to sleep between each file (microseconds)\n"
"\t-t                    output a tar(1) file called <dest> instead of copying\n"
"\t-u                    don't copy files with a modification date older than the target\n"
"\t-F                    if <src> is a file, and <dest> already exists, overwrite without warning\n"
"\t-q                    be less verbose\n"
"\t-x name               don't include directories called \"name\"\n"
"\t-p                    write SUSv3 \"pax\" format tar headers where required (long filenames)\n"
		,progname);
	exit(8);
}

int
main(argc, argv)
	int argc;
	char *argv[];
{
	int	i;
struct	stat	sb;

	progname = argv[0];

	while ((i = getopt(argc, argv, "pqFuts:b:f:x:")) != -1) {
		switch(i) {
		case 'F':
			Fflag++;
			break;
		case 'q':
			qflag++;
			break;
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
		case 'x':
			addexclude(optarg);
			break;
		case 'p':
			pflag++;
			break;
		case 'h':
		default:
			usage();
		}
	}
	argc -= optind;
	argv += optind;

	if (tflag) {
		archive = 1;
		arch_writeheader = tar_writeheader;
		arch_writeeof = tar_writeeof;
	}

	if (argc != 2)
		usage();

	if (uflag && archive) {
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

	if (archive) {
		if (!strcmp(dest, "-"))
			archfile = stdout;
		else if ((archfile = fopen(dest, "w")) == NULL) {
			perror(dest);
			exit(8);
		}
	}

	if (stat(src, &sb) < 0) {
		perror(src);
		exit(8);
	}

	if ((sb.st_mode & (S_IFREG | S_IFDIR)) == 0) {
		fprintf(stderr, "%s: %s: neither file nor directory\n", progname, src);
		exit(8);
	}

	if (sb.st_mode & S_IFREG) {
		struct stat sd;
		char *filename, *slash;

		errno = 0;
		if (stat(dest, &sd) < 0 && errno != ENOENT) {
			perror(dest);
			exit(8);
		}

		if ((sd.st_mode & S_IFDIR) == 0 || errno == ENOENT) {
			if (errno == 0 && !Fflag) {
				char c;

				fprintf(stderr, "%s: overwrite \"%s\"? [y/n] ", progname, dest);
				fflush(stderr);
				for (;;) switch (fgetc(stdin)) {
					case (int)'y': goto ok;
					case (int)'n': case EOF: exit(0);
					default: fprintf(stderr, "Please enter 'y' or 'n': \n");
						continue;
				} ok:
				if ((c = fgetc(stdin)) != '\n') ungetc(c, stdin);
				if (unlink(dest) < 0) {
					perror(dest);
					exit(8);
				}
			}
		} else {
			char *t = dest, *slash = rindex(src, '/');
			dest = malloc(strlen(t) + strlen(dest) + 1);
			sprintf(dest, "%s/%s", dest, slash ? slash : src);
		}

		copy_file(src, dest);
		exit(0);
	}
		
	if (chdir(src) < 0) {
		perror(src);
		exit(8);
	}

	if (!qflag) fprintf(stderr, "Copying from %s to %s%s, using blocksize %d, sleeps block/file %d/%d\n",
		src, tflag ? "tar file " : "", dest, blocksize, blocksleep, filesleep);

	curdir = strdup("");
	copy_directory(".");
	
	if (archfile) {
		arch_writeeof(archfile);
		if (archfile != stdout)
			fclose(archfile);
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
	
		if (lstat(dp->d_name, &sb) < 0) {
			perror(dp->d_name);
			exit(8);
		}

		if (sb.st_mode & S_IFDIR) {
			char *dpath;
			if (exclude(dp->d_name)) {
				if (!qflag) 
					fprintf(stderr, "de %s%s\n", curdir, dp->d_name);
				continue;
			}
			if (!qflag) 
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
			char *outname;
			outname = alloca(strlen(dest) + strlen(curdir) + strlen(dp->d_name) + 3);
			sprintf(outname, "%s/%s%s", dest, curdir, dp->d_name);

			copy_file(dp->d_name, outname);
			usleep(filesleep);
		} else {
			/*
			 * Ignore special files...
			 */
			fprintf(stderr, "%s: ignoring %s%s: neither directory nor file\n", 
						progname, curdir, dp->d_name);
		}
	}
	closedir(dirp);

	chdir("..");
	curdir = oldcur;
}

static void
copy_file(name, outname)
	const char *name, *outname;
{
	FILE	*f, *out = NULL;
	char	*buf;
	size_t	 bsize;
struct	stat	sb;

	if (lstat(name, &sb) < 0) {
		perror(name);
		exit(8);
	}

	if (archive) {
		arch_writeheader(archfile, name);
	} else {
		if (uflag && newerorsame(outname, name)) {
			if (!qflag) fprintf(stderr, "fu %s%s\n", curdir ? curdir : "", name);
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
			if (write_blocked(buf, bsize, archfile) < 1) {
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
	if (archive)
		fflush(archfile);
	else {
		struct utimbuf ut;

		ut.actime = sb.st_atime;
		ut.modtime = sb.st_mtime;
		if (utime(outname, &ut) < 0) {
			perror(outname);
			exit(8);
		}
	}

	if (!qflag) 
		fprintf(stderr, "f  %s%s %d bytes, %d blocks\n", curdir ? curdir : "",
			name, (int)sb.st_size, (int)sb.st_blocks);

}

size_t
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
newerorsame(fa, fb)
	const char *fa, *fb;
{
struct	stat	sa, sb;
	if (lstat(fa, &sa) < 0) {
		if (errno == ENOENT)
			return 0;
		perror(fa);
		exit(8);
	}

	if (lstat(fb, &sb) < 0) {
		if (errno == ENOENT)
			return 0;
		perror(fb);
		exit(8);
	}

	return sa.st_mtime >= sb.st_mtime;
}

char **excludes;
int nexcl;

static void
addexclude(name)
	const char *name;
{
	excludes = realloc(excludes, nexcl + 1);
	excludes[nexcl] = strdup(name);
	++nexcl;
}

static int
exclude(name)
	const char *name;
{
	char **excl;

	for (excl = excludes; excl < &excludes[nexcl]; ++excl)
		if (!strcmp(*excl, name))
			return 1;

	return 0;
}
