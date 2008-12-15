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

import com.fluendo.utils.*;

public class SystemClock extends Clock {
  protected long getInternalTime()
  {
    return System.currentTimeMillis() * Clock.MSECOND;	  
  }
  protected int waitFunc(ClockID id)
  {
    int res;

    long real = getInternalTime();
    long entryt = id.time;
    long now = adjust (real);
    long diff = entryt - now;

    //Debug.log(Debug.DEBUG, "now: "+now+", entry: "+entryt+", diff: "+diff);

    if (diff > 0) {
      long millis;
      int nanos;

      millis = diff / Clock.MSECOND;
      nanos = (int) ((diff % Clock.MSECOND) * Clock.MSECOND);

      synchronized (this) {
        if (id.status == UNSCHEDULED)
	  return id.status;

        id.status = OK;
        try {
          wait (millis, nanos);
        }
        catch (InterruptedException e) {}
      }
      res = id.status;
    }
    else if (diff == 0) {
      res = OK;
    }
    else {
      res = EARLY;
    }
    return res;
  }
  protected int waitAsyncFunc(ClockID id)
  {
    return OK;
  }
  protected void unscheduleFunc(ClockID id)
  {
    synchronized (this) {
      id.status = UNSCHEDULED;
      notifyAll();
    }
  }
}

