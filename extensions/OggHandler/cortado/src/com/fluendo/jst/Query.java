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

public class Query {

  /* types */
  public static final int POSITION = 1;
  public static final int DURATION = 2;
  public static final int LATENCY = 3;
  public static final int JITTER = 4;
  public static final int RATE = 5;
  public static final int SEEKING = 6;
  public static final int SEGMENT = 7;
  public static final int CONVERT = 8;
  public static final int FORMATS = 9;

  private int type;
  private int format;
  private long value;

  private Query(int type) {
    value = -1;
    this.type = type;
  }

  public int getType () {
    return type;
  }

  public static Query newPosition(int format) {
    Query q = new Query(POSITION);
    q.format = format;
    return q;
  }
  public void setPosition(int format, long position) {
    this.format = format;
    this.value = position;
  }
  public int parsePositionFormat() {
    return format;
  }
  public long parsePositionValue() {
    return value;
  }

  public static Query newDuration(int format) {
    Query q = new Query(DURATION);
    q.format = format;
    return q;
  }
  public void setDuration(int format, long position) {
    this.format = format;
    this.value = position;
  }
  public int parseDurationFormat() {
    return format;
  }
  public long parseDurationValue() {
    return value;
  }
}
