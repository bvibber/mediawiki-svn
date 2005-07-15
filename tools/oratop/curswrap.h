/*
 * Curses wrappers.
 */

#ifndef CURSWRAP_H
#define CURSWRAP_H

extern const int CURS_A_BOLD;

void curs_initscr(void);
void curs_noecho(void);
void curs_echo(void);
void curs_cbreak(void);
void curs_endwin(void);
void curs_refresh(void);

int curs_cols(void);
int curs_lines(void);

void curs_move(int, int);
void curs_addstr(const char *);
int  curs_getch(void);
void curs_getnstr(char *, size_t);
void curs_clrtoeol(void);

void curs_standout(void);
void curs_standend(void);
void curs_attron(int attr);
void curs_attroff(int attr);

#endif
