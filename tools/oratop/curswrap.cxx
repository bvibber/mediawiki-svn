/*
 * Curses wrappers.
 */

#include <curses.h>

#include "curswrap.h"

const int CURS_A_BOLD = A_BOLD;
static bool init = false;

void
curs_initscr(void)
{
	init = true;
	initscr();
}

void
curs_cbreak(void)
{
	cbreak();
}

void
curs_noecho(void)
{
	noecho();
}

void
curs_endwin(void)
{
	if (init)
		endwin();
}

void
curs_move(int y, int x)
{
	move(y, x);
}

void
curs_addstr(const char *s)
{
	addstr((char *) s);
}

int
curs_cols(void)
{
	return COLS;
}

int
curs_lines(void)
{
	return LINES;
}

int
curs_getch(void)
{
	return getch();
}

void
curs_refresh(void)
{
	refresh();
}

void
curs_getnstr(char *s, size_t sz)
{
	wgetnstr(stdscr, s, sz);
}

void
curs_echo(void)
{
	echo();
}

void
curs_clrtoeol(void)
{
	clrtoeol();
}

void
curs_standout(void)
{
	standout();
}

void
curs_standend(void)
{
	standend();
}

void
curs_attron(int attr)
{
	attron(attr);
}

void
curs_attroff(int attr)
{
	attroff(attr);
}
