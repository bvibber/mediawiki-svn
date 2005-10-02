/*
 * Rosary: SVr4/Solaris package manager.
 */

#include <sys/types.h>
#include <sys/wait.h>

#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <unistd.h>
#include <stdlib.h>
#include <dirent.h>
#include <curses.h>
#include <term.h>
#include <fnmatch.h>
#include <libtecla.h>

#include <glib.h>

#define CFGFILE "/etc/opt/rosary/rosary.conf"
#define PKGFILE "/etc/opt/rosary/db.pkg"
#define FILEFILE "/etc/opt/rosary/db.file"

struct package;
int		 initroot		(void);
int		 read_pkg_info		(struct package *pkg, const char *dir);
void		 pkgdb_write		(gpointer, gpointer, gpointer);
void		 pkgdb_writedep		(gpointer, gpointer);
void		 pkgsearch		(gpointer, gpointer, gpointer);
void		 print_pkghash		(GHashTable *);
void		 print_pkghash_one	(gpointer, gpointer, gpointer);
gboolean	 is_installable		(gpointer, gpointer, gpointer);
void		 print_package		(struct package *);
int		 is_installed		(const char *pkg);
int		 add_depends		(struct package *pkg);
void		 install_all		(void);
int		 install_one		(struct package *pkg);
int		 is_candidate		(const char *);
struct package	*next_installable	(void);
int		 run_cmd		(char **);
int		 install_cmd		(char **);
int		 search_cmd		(char **);
int		 shell_cmd		(char **);

GHashTable	*pkgs_byname;
GHashTable	*files_byname;
GHashTable	*inst_pkgs;

GHashTable	*to_install;
GHashTable	*toins_deps;
char		*pkgroot;

struct package {
	char	*name;
	char	*desc;
	GSList	*depends;
};

char		*progname;
const char	*commands =
	"commands:\n"
	"  install <pkgs>                install one or more packages\n"
	"  search <term>                 search package names and descriptions\n"
	"  shell                         enter interactive shell\n";
const char	*icommands =
	"  quit                          exit command shell\n";
int
main(ac, av)
int	 ac;
char	*av[];
{
int			 c;
int			 Iflag = 0;
struct package		*pkg;
GIOChannel		*f;
GError			*err = NULL;
char			*ln;
DIR			*dp;
struct dirent		*de;
static const char	*usage =
	"usage: %s [-I -m <media location>] <command>\n"
	"  -m\tspecify location of Solaris DVD/CD media\n"
	"  -I\tinitialise configuration\n";

	progname = av[0];

	while ((c = getopt(ac, av, "Im:")) != -1) {
		switch (c) {
		case 'I':
			Iflag++;
			break;
		case 'm':
			pkgroot = optarg;
			break;
		default:
			fprintf(stderr, usage, av[0]);
			fprintf(stderr, "\n%s", commands);
			exit(1);
		}
	}
	ac -= optind;
	av += optind;

	if (Iflag) {
		return initroot();
	}

	if (access(CFGFILE, F_OK) == 0) {
		fprintf(stderr, "Loading configuration from %s: ", CFGFILE);
		if ((f = g_io_channel_new_file(CFGFILE, "r", &err)) == NULL) {
			fprintf(stderr, "%s\n", err->message);
			return 1;
		}
		while (g_io_channel_read_line(f, &ln, NULL, NULL, &err) == G_IO_STATUS_NORMAL) {
		char *v, *o = ln;
			ln[strlen(ln) - 1] = '\0';
			if ((v = strchr(o, '=')) == NULL) {
				fprintf(stderr, "format error\n");
				return 1;
			}
			*v++ = '\0';
			if (!strcmp(o, "pkgroot"))
				pkgroot = strdup(v);
			g_free(ln);
		}
		g_io_channel_unref(f);
		fprintf(stderr, "OK\n");
	} else {
		fprintf(stderr, "No configuration exists; use -I to create.\n");
		return 0;
	}

	fprintf(stderr, "package root: %s\n", pkgroot);
	pkgs_byname = g_hash_table_new(g_str_hash, g_str_equal);
	fprintf(stderr, "Reading package database from %s: ", PKGFILE);
	if ((f = g_io_channel_new_file(PKGFILE, "r", &err)) == NULL) {
		fprintf(stderr, "%s\n", err->message);
		return 1;
	}
	while (g_io_channel_read_line(f, &ln, NULL, NULL, NULL) == G_IO_STATUS_NORMAL) {
	char *v, *o = ln;
		ln[strlen(ln) - 1] = '\0';
		if ((v = strchr(o, ' ')) == NULL) {
			fprintf(stderr, "format error\n");
			return 1;
		}
		*v++ = '\0';
		if (!strcmp(o, "p")) {
			pkg = calloc(1, sizeof(struct package));
			pkg->name = strdup(v);
			g_hash_table_insert(pkgs_byname, strdup(v), pkg);
		} else if (!strcmp(o, "desc")) {
			if (!pkg) {
				fprintf(stderr, "format error\n");
				exit(1);
			}
			pkg->desc = strdup(v);
		} else if (!strcmp(o, "dep")) {
			if (!pkg) {
				fprintf(stderr, "format error\n");
				exit(1);
			}
			pkg->depends = g_slist_prepend(pkg->depends, strdup(v));
		}
	}
	fprintf(stderr, "OK, %d packages\n", g_hash_table_size(pkgs_byname));
	g_io_channel_unref(f);

	fprintf(stderr, "Reading list of installed packages: ");
	inst_pkgs = g_hash_table_new(g_str_hash, g_str_equal);
	if ((dp = opendir("/var/sadm/pkg")) == NULL) {
		perror("/var/sadm/pkg");
		return 1;
	}
	while (de = readdir(dp)) {
	struct package	*ipkg;
		ipkg = calloc(1, sizeof(struct package));
		ipkg->name = strdup(de->d_name);
		g_hash_table_insert(inst_pkgs, strdup(de->d_name), ipkg);
	}
	closedir(dp);
	fprintf(stderr, "OK, %d packages\n", g_hash_table_size(inst_pkgs));

	fprintf(stderr, "\n");
	if (!*av) {
		fprintf(stderr, usage, progname);
		fprintf(stderr, "\n%s", commands);
		return 1;
	}

	if (run_cmd(av) == -1)
		return 1;
	return 0;
}

int
search_cmd(av)
char	**av;
{
char	*term;
	if (!*av) {
		printf("search: not enough arguments\n");
		return -1;
	}
	printf("Packages matching \"%s\":\n", av[0]);
	term = g_strdup_printf("*%s*", av[0]);
	g_hash_table_foreach(pkgs_byname, pkgsearch, term);
	g_free(term);
	fprintf(stderr, "a = available, i = installed\n");
}

int
run_cmd(av)
char	**av;
{
static int	slev;
	if (!strcmp(av[0], "install"))
		return install_cmd(av + 1);
	if (!strcmp(av[0], "search"))
		return search_cmd(av + 1);
	if (!slev++ && !strcmp(av[0], "shell"))
		return shell_cmd(av + 1);
	if (!strcmp(av[0], "quit"))
		exit(0);
	if (!strcmp(av[0], "help")) {
		fprintf(stderr, "%s", commands);
		fprintf(stderr, "%s", icommands);
		return 0;
	}

	fprintf(stderr, "Unknown command '%s'\n", av[0]);
	return -1;
}

int
shell_cmd(av)
char	**av;
{
GetLine	*gl;
	gl = new_GetLine(4096, 256);
	for (;;) {
	char	*in;
	char	**args;
		in = gl_get_line(gl, "rosary> ", "", 0);
		if (!in)
			return 0;
		in[strlen(in) - 1] = '\0';
		args = g_strsplit(in, " ", 0);
		if (!args[0]) {
			g_strfreev(args);	
			free(in);
			continue;
		}
		run_cmd(args);
		g_strfreev(args);
	}
	del_GetLine(gl);
}

int
install_cmd(av)
char	**av;
{
int	c;
	to_install = g_hash_table_new(g_str_hash, g_str_equal);
	toins_deps = g_hash_table_new(g_str_hash, g_str_equal);
	while (*av) {
	struct package	*pkg;
	GSList		*dep;
		if ((pkg = g_hash_table_lookup(pkgs_byname, *av)) == NULL) {
			fprintf(stderr, "Nothing known about package '%s'\n", *av);
			return 1;
		}
		if (is_installed(pkg->name)) {
			fprintf(stderr, "%s is already installed\n", pkg->name);
			av++;
			continue;
		}
		g_hash_table_insert(to_install, strdup(*av), pkg);
		if (add_depends(pkg) == -1) {
			fprintf(stderr, "Cannot resolve dependencies.\n");
			return 1;
		}
		av++;
	}

	if (g_hash_table_size(to_install) == 0) {
		printf("Nothing to do.\n");
		return 0;
	}

	printf("The following packages will be installed:\n");
	print_pkghash(to_install);
	if (g_hash_table_size(toins_deps)) {
		printf("The following packages will be installed to satisfy dependencies:\n");
		print_pkghash(toins_deps);
	}

	printf("\nContinue? [y/N] ");
	c = getc(stdin);
	if (c != 'y') {
		printf("Okay, exiting\n");
		return 0;
	}
	install_all();
	return 0;
}

void
install_all(void)
{
struct package	*next;
	while (next = next_installable()) {
		install_one(next);
	}
	if (g_hash_table_size(to_install) || g_hash_table_size(toins_deps))
		fprintf(stderr, "No installable packages; possible dependency loop?\n");
}

struct package *
next_installable(void)
{
struct package	*next;
	next = g_hash_table_find(toins_deps, is_installable, NULL);
	if (next) {
		g_hash_table_remove(toins_deps, next->name);
		return next;
	}
	next = g_hash_table_find(to_install, is_installable, NULL);
	if (next)
		g_hash_table_remove(to_install, next->name);
	return next;
}

gboolean
is_installable(key, value, ud)
gpointer	key, value, ud;
{
struct package	*pkg = value;
GSList		*dep;
	for (dep = pkg->depends; dep; dep = g_slist_next(dep)) {
		if (!is_installed(dep->data)) {
			return FALSE;
		}
	}
	return TRUE;
}

int
install_one(pkg)
struct package	*pkg;
{
pid_t	pid;
int	res;
	fprintf(stderr, "Installing %s...\n", pkg->name);
	switch (pid = fork()) {
	case -1:
		fprintf(stderr, "fork: %s\n", strerror(errno));
		exit(1);
	case 0:
		execl("/usr/sbin/pkgadd", "pkgadd", "-d", pkgroot, "--", pkg->name, NULL);
		fprintf(stderr, "execl: %s\n", strerror(errno));
		_exit(1);
	}
	if (waitpid(pid, &res, 0) < 0) {
		fprintf(stderr, "waitpid: %s\n", strerror(errno));
		exit(1);
	}
	if (WEXITSTATUS(res)) {
		fprintf(stderr, "pkgadd exited with return code %d\n", WEXITSTATUS(res));
		exit(1);
	}
	fprintf(stderr, "%s installed okay\n", pkg->name);
	g_hash_table_insert(inst_pkgs, pkg->name, pkg);
}

int
add_depends(pkg)
struct package	*pkg;
{
GSList	*dep;
int	 n = 0;
	for (dep = pkg->depends; dep; dep = g_slist_next(dep)) {
	struct package	*dpkg;
		if ((dpkg = g_hash_table_lookup(pkgs_byname, dep->data)) == NULL) {
			fprintf(stderr, "Package %s depends on unknown package %s\n", 
				pkg->name, dep->data);
			return -1;
		}
		if (!is_installed(dpkg->name) && !is_candidate(dpkg->name)) {
			g_hash_table_insert(toins_deps, dpkg->name, dpkg);
			if (add_depends(dpkg) == -1)
				return -1;
			n++;
		}
	}
	return n;
}

void
print_pkghash(hash)
GHashTable	*hash;
{
	g_hash_table_foreach(hash, print_pkghash_one, NULL);
}

void
print_pkghash_one(key, value, ud)
gpointer	key, value, ud;
{
	print_package(value);
}

void
print_package(pkg)
struct package	*pkg;
{
	printf("%c  %-30s %s\n", is_installed(pkg->name) ? 'i' : 'a',
		pkg->name, pkg->desc ? pkg->desc : "<no description>");
}

int
is_installed(name)
const char	*name;
{
	return g_hash_table_lookup(inst_pkgs, name) != NULL;
}

int
is_candidate(name)
const char	*name;
{
	return g_hash_table_lookup(to_install, name)
		|| g_hash_table_lookup(toins_deps, name);
}

void
pkgsearch(key, value, data)
gpointer	key, value, data;
{
const char	*term = data;
struct package	*pkg = value;
char		*lterm, *lname, *ldesc;
	lname = g_ascii_strdown(pkg->name, -1);
	ldesc = g_ascii_strdown(pkg->desc, -1);
	lterm = g_ascii_strdown(term, -1);
	if (fnmatch(lterm, ldesc, 0) == 0 || fnmatch(lterm, lname, 0) == 0)
		print_package(pkg);
	g_free(lname);
	g_free(ldesc);
	g_free(lterm);
}

int
initroot(void)
{
int		 npkg = 0, nign = 0;
char		*tieol;
GHashTable	*pkgs;
DIR		*pd;
struct dirent	*de;
FILE		*f;
	if (!pkgroot) {
		fprintf(stderr, "Package root not specified (use -m <root>)\n");
		return 1;
	}

	if ((pd = opendir(pkgroot)) == NULL) {
		fprintf(stderr, "Cannot open package root %s: %s\n", pkgroot, strerror(errno));
		return 1;
	}

	fprintf(stderr, "Initialising package database from %s...\n", pkgroot);
	setupterm(NULL, 1, NULL);
	tieol = tigetstr("el");
	pkgs_byname = g_hash_table_new(g_str_hash, g_str_equal);
	pkgs = g_hash_table_new(g_str_hash, g_str_equal);
	chdir(pkgroot);
	while (de = readdir(pd)) {
	struct	package *pkg;
		if (de->d_name[0] == '.')
			continue;
		if (chdir(de->d_name) == -1) {
			nign++;
			continue;
		}
		if (access("pkgmap", F_OK) != 0) {
			nign++;
			continue;
		}
		fprintf(stderr, "\r%s%s", tieol, de->d_name);
		npkg++;
		pkg = calloc(1, sizeof(struct package));
		if (read_pkg_info(pkg, ".") == -1) {
			fprintf(stderr, "%s: cannot read package information\n", de->d_name);
			return 1;
		}
		g_hash_table_insert(pkgs_byname, pkg->name, pkg);
		if (chdir(pkgroot) == -1) {
			fprintf(stderr, "chdir(%s): %s\n", pkgroot, strerror(errno));
			return 1;
		}
	}
	closedir(pd);
	fprintf(stderr, "\r%sFound %d packages (%d ignored)\n", tieol, npkg, nign);

	fprintf(stderr, "Writing configuration file %s: ", CFGFILE);
	if ((f = fopen(CFGFILE, "w")) == NULL) {
		fprintf(stderr, "%s\n", strerror(errno));
		return 1;
	}
	fprintf(f, "pkgroot=%s\n", pkgroot);
	fclose(f);
	fprintf(stderr, "OK\n");

	fprintf(stderr, "Writing package database %s: ", PKGFILE);
	if ((f = fopen(PKGFILE, "w")) == NULL) {
		fprintf(stderr, "%s\n", strerror(errno));
		return 1;
	}
	g_hash_table_foreach(pkgs_byname, pkgdb_write, f);
	fclose(f);
	fprintf(stderr, "OK\n");
	return 0;
}

void
pkgdb_write(key, value, ud)
gpointer	key, value, ud;
{
FILE		*f = ud;
const char	*name = key;
struct package	*pkg = value;
	fprintf(f, "p %s\n", name);
	fprintf(f, "desc %s\n", pkg->desc);
	g_slist_foreach(pkg->depends, pkgdb_writedep, f);
}

void
pkgdb_writedep(data, ud)
gpointer	data, ud;
{
const char	*dep = data;
FILE		*f = ud;
	fprintf(f, "dep %s\n", dep);
}

int
read_pkg_info(pkg, dir)
struct package	*pkg;
const char	*dir;
{
GIOChannel	*f;
GError		*err = NULL;
char		*ln;
	if ((f = g_io_channel_new_file("pkginfo", "r", &err)) == NULL) {
		fprintf(stderr, "\rpkginfo: %s\n", err->message);
		return -1;
	}
	while (g_io_channel_read_line(f, &ln, NULL, NULL, &err) == G_IO_STATUS_NORMAL) {
	char *o, *v, *p;
		ln[strlen(ln) - 1] = '\0';
		if (*ln == '#') {
			g_free(ln);
			continue;
		}
		if ((v = strchr(ln, '=')) == NULL) {
			g_free(ln);
			continue;
		}
		o = ln;
		*v++ = '\0';
		if (!strcmp(o, "NAME"))
			pkg->desc = strdup(v);
		else if (!strcmp(o, "PKG")) {
			if (p = strpbrk(v, " \t"))
				*p = '\0';
			pkg->name = strdup(v);
		}
		g_free(ln);
	}
	g_io_channel_unref(f);
	if (!pkg->name) {
		fprintf(stderr, "\rpkginfo: no package name\n");
		return -1;
	}
	if ((f = g_io_channel_new_file("install/depend", "r", &err)) == NULL)
		return 0;
	while (g_io_channel_read_line(f, &ln, NULL, NULL, &err) == G_IO_STATUS_NORMAL) {
	char *v, *p;
		ln[strlen(ln) - 1] = '\0';
		if (*ln != 'P' || strlen(ln) < 3) {
			g_free(ln);
			continue;
		}
		ln += 2;
		if (v = strpbrk(ln, " \t"))
			*v = '\0';
		if (p = strpbrk(v, " \t"))
			*p = '\0';
		pkg->depends = g_slist_prepend(pkg->depends, strdup(ln));
		g_free(ln);
	}
	g_io_channel_unref(f);
	
	return 0;
}

