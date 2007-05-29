/* Copyright (c) 2007 River Tarnell <river@attenuate.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */

/* $Id: whodo.c 22465 2007-05-27 03:21:02Z river $ */

/*
 * whodo: report logged in users and their activities.
 */

#include	<sys/types.h>
#include	<stdio.h>
#include	<stdlib.h>
#include	<string.h>
#include	<ctype.h>
#include	<unistd.h>
#include	<dirent.h>
#include	<utmpx.h>

/*
 * Process tree entry.
 */
typedef struct uproc {
	char		*pe_cmd;
	char		*pe_cmdline;
	pid_t		 pe_pid;
	struct uproc	*pe_child;	/* first child		*/
	struct uproc	*pe_next;	/* root list next 	*/
	struct uproc	*pe_sib;	/* next sibling		*/
} uproc_t;

uproc_t head;

void	 build_uproc_tree	(void);
void	 print_uproc_children	(uproc_t *, int indent);
void	 show_uproc		(uproc_t *, int pad);
void	 add_uproc		(pid_t pid, pid_t ppid, char const *cmd, char const *cmdline);
uproc_t	*find_uproc		(pid_t pid, int create);
void	 make_safe		(char *s);

int
main()
{
struct	utmpx	*ut;

	build_uproc_tree();

	setutxent();
	while ((ut = getutxent()) != NULL) {
		uproc_t	*pp;

		if (ut->ut_type != USER_PROCESS)
			continue;

		if ((pp = find_uproc(ut->ut_pid, 0)) != NULL) {
			/* 
			 * If we can't find ut_pid, we don't even print the 
			 * utmp entry; it's probably stale.
			 */
			(void) printf("%-8s %-16s\n", ut->ut_line, ut->ut_user);
			show_uproc(pp, 0);
			print_uproc_children(pp, 1);
			(void) printf("\n");
		}
	}
	endutxent();
	return 0;
}

uproc_t *
find_uproc(pid, create)
	pid_t pid;
	int create;
{
	uproc_t	*pp;
	for (pp = head.pe_next; pp; pp = pp->pe_next)
		if (pp->pe_pid == pid)
			return pp;

	if (create == 0)
		return NULL;

	pp = calloc(1, sizeof(*pp));
	if (pp == NULL) {
		(void) fprintf(stderr, "out of memory\n");
		_exit(1);
	}

	pp->pe_pid = pid;
	pp->pe_next = head.pe_next;
	head.pe_next = pp;
	return pp;
}

void
add_uproc(pid, ppid, comm, cmdline)
	pid_t pid, ppid;
	char const *comm, *cmdline;
{
	uproc_t *pp;
	uproc_t *proc = find_uproc(pid, 1),
		*parent = find_uproc(ppid, 1);

	proc->pe_cmd = strdup(comm);
	proc->pe_cmdline = strdup(cmdline);

	make_safe(proc->pe_cmd);
	make_safe(proc->pe_cmdline);

	/* add this process to its parent's child list */
	if (parent->pe_child == NULL) {
		parent->pe_child = proc;
	} else {
		for (pp = parent->pe_child; pp->pe_sib; pp = pp->pe_sib)
			;
		pp->pe_sib = proc;
	}
}

void
build_uproc_tree()
{
	DIR	*pdir;
struct	dirent	*dent;

	if ((pdir = opendir("/proc")) == NULL) {
		perror("/proc");
		exit(1);
	}

	while ((dent = readdir(pdir)) != NULL) {
		char const	*s;
		int		 pid, ppid;
		FILE		*f = NULL;
		char		 statpath[128], cmd[128], cmdline[128];
		size_t		 sz;

		for (s = dent->d_name; *s; ++s)
			if (!isdigit(*s))
				goto next;

		(void) snprintf(statpath, sizeof(statpath), "/proc/%s/stat",
				dent->d_name);
		if ((f = fopen(statpath, "r")) == NULL)
			goto next;

		if (fscanf(f, "%d %127s %*c %d", &pid, cmd, &ppid) != 3)
			goto next;

		(void) fclose(f);

		(void) snprintf(statpath, sizeof(statpath), "/proc/%s/cmdline",
				dent->d_name);
		if ((f = fopen(statpath, "r")) == NULL)
			goto next;

		sz = fread(cmdline, 1, sizeof(cmdline) - 1, f);
		if (sz == 0)
			cmdline[0] = '\0';
		else {
			char *s = cmdline;
			while (sz--) {
				if (*s == '\0')
					*s = ' ';
				s++;
			}
			*s = '\0';
		}

		add_uproc(pid, ppid, cmd, cmdline);

	next:
		if (f)
			(void) fclose(f);
	}

	(void) closedir(pdir);
}

void
print_uproc_children(pp, indent)
	uproc_t *pp;
	int indent;
{
	for (pp = pp->pe_child; pp; pp = pp->pe_sib) {
		show_uproc(pp, indent * 2);
		print_uproc_children(pp, indent + 1);
	}
}

void
show_uproc(pp, pad)
	uproc_t *pp;
	int pad;
{
	(void) printf("    %-5d ", pp->pe_pid);

	if (pp->pe_cmd)
		(void) printf(" %-*s", 10 + pad, pp->pe_cmd);
	else
		(void) printf(" ???");

	if (pp->pe_cmdline)
		(void) printf("   %s", pp->pe_cmdline);
	(void) printf("\n");
}

void
make_safe(s)
	char *s;
{
	while (*s) {
		if (!isprint(*s))
			*s = '?';
		s++;
	}
}
