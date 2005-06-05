/* @(#) $Header$ */
/* This source code is in the public domain. */
/*
 * trickle: copy one directory to another, slowly.
 */

#pragma ident "@(#) $Id$"

#include <sys/types.h>
#include <sys/stat.h>

#include <fcntl.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <dirent.h>
#include <limits.h>
#include <errno.h>
#include <utime.h>
#include <strings.h>

#include "trickle.h"
#include "rdcp.h"

const char *progname;

int tflag, uflag, Fflag, qflag, pflag, Pflag, rflag, Zflag;
char *rsh = "rsh", *remote, *trickle;
int archive;
int blocksleep, filesleep;
int blocksize = 8192;
char *src, *dest;
char *curdir;
FILE *archfile;

static void copy_directory(const char *dir, 
	void (*cf)(const char *, const char *));
static void copy_file(const char *name, const char *outname);
static void copy_file_net(const char *, const char *);
static int contemplate_file(const char *name, struct stat *sb);
static int samefile(const char *fa, const char *fb);
static int exclude(const char *name);
static void addexclude(const char *name);
static void copy_from_to(int from, int to, const char *destname);
static void discuss_files(void);
static void send_files(void);

static void (*arch_writeheader) (FILE *, const char *name);
static void (*arch_writeeof) (FILE *);

void
usage(void)
{
	fprintf(stderr,
"Usage: %s -s blocksize [-b blocksleep] [-f filesleep] [-tp | -ruFP] [-q] [-x name] "
"[-z program] [-T trickle] <src> <dest>\n"
"\t-s blocksize          amount of data to read/write at one time\n"
"\t-b blocksleep         time to sleep between each block (microseconds)\n"
"\t-f filesleep          time to sleep between each file (microseconds)\n"
"\t-t                    output a tar(1) file called <dest> instead of copying\n"
"\t-u                    don't copy files with a modification date older than the target\n"
"\t-F                    if <src> is a file, and <dest> already exists, overwrite without warning\n"
"\t-q                    be less verbose\n"
"\t-x name               don't include directories called \"name\"\n"
"\t-p                    write pax(1) format tar headers where required (long filenames)\n"
"\t-P                    preserve ownership and owner of copied files\n"
"\t-r			 copy files to a remote host via rsh\n"
"\t-z program            use an alternative rsh\n"
"\t-T trickle            name of trickle on remote host\n"
		,progname);
	exit(8);
}

int
main(argc, argv)
	int argc;
	char *argv[];
{
	int	 i;
struct	stat	 sb;
	char	*s;

	progname = argv[0];

	while ((i = getopt(argc, argv, "T:Zz:rPpqFuts:b:f:x:")) != -1) {
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
		case 'P':
			Pflag++;
			break;
		case 'z':
			rsh = optarg;
			break;
		case 'r':
			rflag++;
			break;
		case 'Z':
			Zflag++;
			break;
		case 'T':
			trickle = optarg;
			break;
		case 'h':
		default:
			usage();
		}
	}
	argc -= optind;
	argv += optind;

	if (Zflag) {
		if (isatty(0))
			fatal("m1", "-Z should not be used directly");
		discuss_files();
	}

	if ((rflag || uflag) && archive) {
		fatal("m2", "-u/-r and -t may not be specified together");
		usage();
	}

	if (tflag) {
		archive = 1;
		arch_writeheader = tar_writeheader;
		arch_writeeof = tar_writeeof;
	}

	if (argc != 2)
		usage();

	src = argv[0];
	dest = argv[1];

	if (s = index(dest, ':')) {
		*s++ = '\0';
		if (!rflag) {
			fprintf(stderr, "%s: remote host specified but not -r\n", progname);
			exit(8);
		}
		remote = dest;
		dest = s;
	}
	
	if (rflag)
		send_files();

	/*
	 * Ensure dest is an absolute path.
	 */

	if (!rflag && *dest != '/' && strcmp(dest, "-")) {
		char *cwd = getcwd(NULL, PATH_MAX);
		char *olddest = dest;
		dest = allocf("%s/%s", cwd, olddest);
		free(cwd);
	}

	if (archive) {
		if (rflag)
			fatal("-r is not compatible with archive mode");
		if (!strcmp(dest, "-"))
			archfile = stdout;
		else if ((archfile = fopen(dest, "w")) == NULL) {
			pfatal("m3", dest);
			exit(8);
		}
	}

	if (stat(src, &sb) < 0) {
		pfatal("m4", dest);
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
			pfatal("m5", dest);
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
					pfatal("m6", dest);
					exit(8);
				}
			}
		} else {
			char *t = dest, *slash = rindex(src, '/');
			dest = allocf("%s/%s", dest, slash ? slash : src);
		}

		copy_file(src, dest);
		exit(0);
	}
		
	if (chdir(src) < 0) {
		pfatal("m7", src);
		exit(8);
	}

	if (!qflag) fprintf(stderr, 
		"Copying from %s to %s%s, using blocksize %d, sleeps block/file %d/%d\n",
		src, tflag ? "tar file " : "", dest, blocksize, blocksleep, filesleep);

	curdir = strdup("");
	copy_directory(".", copy_file);
	
	if (archfile) {
		arch_writeeof(archfile);
		if (archfile != stdout)
			fclose(archfile);
	}

	return 0;
}

static void
copy_directory(dir, cf)
	const char *dir;
	void (*cf)(const char *, const char *);
{
	DIR 	*dirp;
struct	dirent	*dp;
struct	stat	 sb;
	char	*oldcur;

	if (chdir(dir) < 0) {
		pfatal("cd1", dir);
		exit(8);
	}

	oldcur = curdir;
	curdir = allocf("%s%s/", oldcur, dir);

	if ((dirp = opendir(".")) == NULL) {
		pfatal("cd2", dir);
		exit(8);
	}
	
	while (dp = readdir(dirp)) {
		if (!strcmp(dp->d_name, ".") || !strcmp(dp->d_name, ".."))
			continue;
	
		if (lstat(dp->d_name, &sb) < 0) {
			pfatal("cd3", dp->d_name);
			exit(8);
		}

		if ((sb.st_mode & S_IFLNK) == S_IFLNK) {
			if (!qflag)
				fprintf(stderr, "fs %s%s\n", curdir, dp->d_name);
			/* XXX should be possible to include symlinks in the output */
			continue;
		} else if (sb.st_mode & S_IFDIR) {
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
			if (!tflag && !rflag) {
				dpath = allocf("%s/%s%s", dest, curdir, dp->d_name);
				/*
				 * We don't care about permissions, so if the directory already
				 * exists, just leave it.
				 */
				if (mkdir(dpath, sb.st_mode) < 0 && errno != EEXIST) {
					pfatal("cd4", dpath);
					exit(8);
				}
				free(dpath);
			} else if (rflag) {
				dpath = allocf("%s/%s%s", dest, curdir, dp->d_name);
				proto_offer(dpath, &sb);
				free(dpath);
			}
			copy_directory(dp->d_name, cf);
		} else if (sb.st_mode & S_IFREG) {
			char *outname;
			if (archive)
				outname = allocf("%s%s", curdir, dp->d_name);
			else
				outname = allocf("%s/%s%s", dest, curdir, dp->d_name);
			cf(dp->d_name, outname);
			free(outname);
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
	free(curdir);
	curdir = oldcur;
}

static void
copy_file(name, outname)
	const char *name, *outname;
{
	int	 in, out;
	char	*buf;
	size_t	 bsize;
struct	stat	 sb;
struct	utimbuf	ut;

	if (lstat(name, &sb) < 0) {
		pfatal("cf1", name);
		exit(8);
	}

	if (archive) {
		arch_writeheader(archfile, name);
	} else {
		if (uflag && !contemplate_file(outname, &sb)) {
			if (!qflag) 
				fprintf(stderr, "fu %s%s\n", curdir ? curdir : "", name);
			return;
		}

		if (samefile(outname, name)) {
			if (!qflag)
				fprintf(stderr, "%s: %s%s and %s are the same file\n",
					progname, curdir, name, outname);
			return;
		}

		unlink(outname);
		if ((out = open(outname, O_WRONLY | O_CREAT | O_EXCL, sb.st_mode)) == -1) {
			if (errno == EEXIST) {
				fprintf(stderr, "%s: %s exists and I didn't expect it to\n",
					progname, outname);
			}
			pfatal("cf2", outname);
			exit(8);
		}
	}
	if ((in = open(name, O_RDONLY)) == -1) {
		pfatal("cf3", name);
		exit(8);
	}

	copy_from_to(in, out, outname);

	if (!archive) {
		ut.actime = sb.st_atime;
		ut.modtime = sb.st_mtime;
		if (utime(outname, &ut) < 0)
			fprintf(stderr, "%s: %s: %s (cf4)\n", progname, outname, strerror(errno));

		if (Pflag) {
			if (fchown(out, sb.st_uid, sb.st_gid) < 0)
				fprintf(stderr, "%s: %s: %s (cf5)\n", progname, outname, strerror(errno));
		}
	}

	close(in);
	close(out);
}

void
copy_from_to(from, to, destname)
	int from, to;
	const char *destname;
{
static	char *buf;
	int bytes = 0, blocks = 0, bsize;

	if (buf == NULL)
		buf = malloc(blocksize);

	while ((bsize = read(from, buf, blocksize)) > 0) {
		if (tflag) {
			if (write_blocked(buf, bsize, archfile) < 1) {
				pfatal("ct1", destname);
				exit(8);
			}
		} else {
			if (write(to, buf, bsize) < 1) {
				pfatal("ct2", destname);
				exit(8);
			}
		}
		bytes += bsize;
		blocks++;
		if (blocksleep)
			usleep(blocksleep);
	}

	if (bsize == -1) {
		if (destname)
			unlink(destname);
		exit(8);
	}

	if (archive)
		fflush(archfile);

	if (!qflag) 
		fprintf(stderr, "f  %s%s %d bytes, %d blocks\n", curdir ? curdir : "",
			destname, bytes, blocks);

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

	do {
		tow = min(size, sizeof(block));

		memset(block, 0, sizeof block);
		memcpy(block, p, tow);
		if ((ret = fwrite(block, sizeof(block), 1, file)) < 1)
			return ret;
		p += tow;
		size -= tow;
		++records;
	} while (size > 0);
	return ret;
}

static int
contemplate_file(name, sb)
	const char *name;
	struct stat *sb;
{
struct	stat	sa;
	if (lstat(name, &sa) < 0) {
		if (errno == ENOENT)
			return 1;
		pfatal("co1", name);
		exit(8);
	}

	return sa.st_mtime < sb->st_mtime;
}

static int
samefile(fa, fb)
	const char *fa, *fb;
{
struct	stat	sa, sb;
	if (lstat(fa, &sa) < 0) {
		if (errno == ENOENT)
			return 0;
		pfatal("sf1", fa);
		exit(8);
	}

	if (lstat(fb, &sb) < 0) {
		if (errno == ENOENT)
			return 0;
		pfatal("sf2", fb);
		exit(8);
	}

	return (sa.st_ino == sb.st_ino)
		&& (sa.st_dev == sb.st_dev);
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

static void
discuss_files()
{
struct	pfile		*file;
struct	stat		 sb;
struct	utimbuf		 ut;
struct	rdcp_frame	 frame;
	int		 in, out, n;
	char		 *dir;
	proto_neg(0);
	dir = proto_readdir();
	chdir(dir);
	free(dir);
	while (file = proto_getfile()) {
		sb.st_mtime = file->mtime;
		if (file->type == T_DIR) {
			mkdir(file->name, file->mode);
			proto_accept();
			continue;
		}

		if (!contemplate_file(file->name, &sb)) {
			proto_decline();
			continue;
		}
		proto_accept();

		unlink(file->name);
		if ((out = open(file->name, O_WRONLY | O_CREAT | O_EXCL, file->mode)) == -1)
			exit(8);

		while ((n = proto_read(&frame)) == 0) {
			if (frame.rf_len == 0)
				break;
			if (write(out, frame.rf_buf, frame.rf_len) < frame.rf_len)
				exit(8);
			rdcp_frame_free(&frame);
		}

		ut.modtime = ut.actime = file->mtime;
		utime(file->name, &ut);

		if (file->gid > -1 && file->uid > -1)
			fchown(out, file->uid, file->gid);
		close(out);
	}
	exit(0);
}

static void
send_files()
{
struct	rdcp_frame	 frame;
	int		 n, sock;

	if (!qflag) fprintf(stderr, 
		"Copying from %s to %s:%s, using blocksize %d, sleeps block/file %d/%d\n",
		src, remote, dest, blocksize, blocksleep, filesleep);

	sock = proto_rsh(remote, rsh);
	proto_neg(sock);
	if (chdir(src) < 0) {
		pfatal("sf1", src);
		exit(8);
	}
	frame.rf_buf = dest;
	frame.rf_len = strlen(dest);
	proto_write(&frame);
	curdir = strdup("");
	copy_directory(".", copy_file_net);
	exit(0);
}

static void
copy_file_net(from, to)
	const char *from, *to;
{
static	char	*buf;
struct	stat	 sb;
	int	 in, i;
	size_t	 bytes = 0, blocks = 0;
	if (buf == NULL)
		buf = malloc(blocksize);
	if ((in = open(from, O_RDONLY)) == -1) {
		pfatal("cn1", from);
		exit(8);
	}
	if (fstat(in, &sb) < 0) {
		pfatal("cn2", from);
		exit(8);
	}
	if (!proto_offer(from, &sb)) {
		fprintf(stderr, "fd %s\n", from);
		return;
	}
	while ((i = read(in, buf, blocksize)) > 0) {
		proto_writeblock(buf, i);
		bytes += i;
		blocks++;
		if (blocksleep)
			usleep(blocksleep);
	}
	proto_eof();
	close(in);
	fprintf(stderr, "fr %s %d bytes, %d blocks\n", from, (int)bytes, (int)blocks);
}
