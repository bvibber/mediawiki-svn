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

public abstract class Clock {
  private long adjust;
  private long lastTime;

  public static final long USECOND = 1;
  public static final long MSECOND = 1000 * USECOND;
  public static final long SECOND = 1000 * MSECOND;

  /* id types */
  public static final int SINGLE = 0;
  public static final int PERIODIC = 0;

  /* status */
  public static final int OK          =  0;
  public static final int EARLY       =  1;
  public static final int UNSCHEDULED =  2;
  public static final int BUSY        =  3;
  public static final int BADTIME     =  4;
  public static final int ERROR       =  5;
  public static final int UNSUPPORTED =  6;

  public class ClockID {
    long time;
    long interval;
    int type;
    int status;
    
    public ClockID (long time, long interval, int type) {
      this.time = time;
      this.interval = interval;
      this.type = type;
    }

    public long getTime() {
      return time;
    }

    public int waitID() {
      int res;
      
      res = waitFunc (this);

      if (type == PERIODIC)
        time += interval;

      return res;
    }
    public void unschedule() {
      unscheduleFunc(this);
    }
  }

  public Clock()
  {
    adjust = 0;
    lastTime = 0;
  }

  protected synchronized long adjust(long internal) {
    long ret;

    ret = internal + adjust;
    /* make sure the time is increasing, else return last_time */
    if (ret < lastTime) {
      ret = lastTime;
    } else {
      lastTime = ret;
    }
    return ret;
  }

  protected abstract long getInternalTime();

  protected abstract int waitFunc(ClockID id);
  protected abstract int waitAsyncFunc(ClockID id);
  protected abstract void unscheduleFunc(ClockID id);

  public synchronized long getTime() {
    long internal, ret;

    internal = getInternalTime();
    ret = adjust (internal);

    return ret;
  }
  public synchronized void setAdjust(long newAdjust) {
    adjust = newAdjust;
  }
  public synchronized long getAdjust() {
    return adjust;
  }

  public ClockID newSingleShotID(long time) {
    return new ClockID (time, 0, SINGLE);
  }
  public ClockID newPeriodicID(long time, long interval) {
    return new ClockID (time, interval, PERIODIC);
  }

}

