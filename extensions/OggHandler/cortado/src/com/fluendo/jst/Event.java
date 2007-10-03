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

package com.fluendo.jst;

public class Event {

  /* types */
  public static final int FLUSH_START = 1;
  public static final int FLUSH_STOP = 2;
  public static final int EOS = 3;
  public static final int NEWSEGMENT = 4;
  public static final int SEEK = 5;
	
  private int type;
  private int format;
  private boolean update;
  private long start;
  private long stop;
  private long position;

  private Event(int type) {
    position = -1;
    this.type = type;
  }

  public int getType () {
    return type;
  }

  public static Event newEOS() {
    return new Event(EOS);
  }

  public static Event newFlushStart() {
    return new Event(FLUSH_START);
  }
  public static Event newFlushStop() {
    return new Event(FLUSH_STOP);
  }

  public static Event newSeek(int format, long position)
  {
    Event e = new Event(SEEK);
    e.format = format;
    e.position = position;
    return e;
  }
  public long parseSeekPosition () {
    return position;
  }
  public int parseSeekFormat () {
    return format;
  }

  public static Event newNewsegment(boolean update, int format, long start, long stop, long position) {
    Event e = new Event(NEWSEGMENT);
    e.update = update;
    e.format = format;
    e.start = start;
    e.stop = stop;
    e.position = position;
    return e;
  }
  public boolean parseNewsegmentUpdate () {
    return update;
  }
  public int parseNewsegmentFormat () {
    return format;
  }
  public long parseNewsegmentStart () {
    return start;
  }
  public long parseNewsegmentStop () {
    return stop;
  }
  public long parseNewsegmentPosition () {
    return position;
  }
}
