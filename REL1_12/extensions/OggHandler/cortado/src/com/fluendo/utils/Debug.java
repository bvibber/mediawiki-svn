/* Cortado - a video player java applet
 * Copyright (C) 2004 Fluendo S.L.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Street #330, Boston, MA 02111-1307, USA.
 */

package com.fluendo.utils;

public class Debug {
  public static final int NONE = 0;
  public static final int ERROR = 1;
  public static final int WARNING = 2;
  public static final int INFO = 3;
  public static final int DEBUG = 4;

  public static int level = INFO;

  /* static id counter */
  private static int counter = 0;
  public static final int genId() {
    synchronized (Debug.class) {
      return counter++;
    }
  }

  public static final String[] prefix = {
       "[NONE] ",
       "[ERRO] ",
       "[WARN] ",
       "[INFO] ",
       "[DBUG] "};

  public static void log(int lev, String line) 
  {
    if (lev <= level) {
      System.out.println(prefix[lev]+line);
    }
  }
}
