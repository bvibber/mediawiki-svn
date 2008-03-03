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
  protected WaitStatus waitFunc(ClockID id)
  {
    WaitStatus res = new WaitStatus();

    long real = getInternalTime();
    long entryt = id.time;
    long now = adjust (real);
    long systemTime = System.currentTimeMillis();

    res.jitter = now - entryt;

    if (res.jitter < 0) {
      Debug.log(Debug.DEBUG, "Waiting from "+now+" until "+entryt+" ("+(-res.jitter)+"us)");
      long millis;
      int nanos;

      millis = -res.jitter / Clock.MSECOND;
      nanos = (int) ((-res.jitter % Clock.MSECOND) * Clock.MSECOND);

      synchronized (this) {
        if (id.status == WaitStatus.UNSCHEDULED) {
	  res.status = WaitStatus.UNSCHEDULED;
	  return res;
	}

        id.status = WaitStatus.OK;
        try {
          wait (millis, nanos);
        }
        catch (InterruptedException e) {}
      }
      res.status = id.status;
    }
    else if (res.jitter == 0) {
      res.status = WaitStatus.OK;
    }
    else {
      Debug.log(Debug.DEBUG, "Wait for timestamp " + now + " is late by " + res.jitter + "us");
      res.status = WaitStatus.LATE;
    }

    return res;
  }
  protected WaitStatus waitAsyncFunc(ClockID id)
  {
    return WaitStatus.newOK();
  }
  protected void unscheduleFunc(ClockID id)
  {
    synchronized (this) {
      id.status = WaitStatus.UNSCHEDULED;
      notifyAll();
    }
  }
}

