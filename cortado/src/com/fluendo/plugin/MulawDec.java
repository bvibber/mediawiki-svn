/* Copyright (C) <2004> Wim Taymans <wim@fluendo.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Library General Public License for more details.
 *
 * You should have received a copy of the GNU Library General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place - Suite 330,
 * Boston, MA 02111-1307, USA.
 */

package com.fluendo.plugin;

import com.fluendo.jst.*;
import com.fluendo.utils.*;

public class MulawDec extends Element 
{
  private int rate, channels;

  private Pad srcPad = new Pad(Pad.SRC, "src") {
    protected boolean eventFunc (com.fluendo.jst.Event event) {
      return sinkPad.pushEvent(event);
    }
  };

  private Pad sinkPad = new Pad(Pad.SINK, "sink") {
    protected boolean eventFunc (com.fluendo.jst.Event event) {
      boolean result;

      switch (event.getType()) {
        case com.fluendo.jst.Event.FLUSH_START:
          result = srcPad.pushEvent (event);
          synchronized (streamLock) {
            Debug.log(Debug.INFO, "synced "+this);
          }
          break;
        case com.fluendo.jst.Event.FLUSH_STOP:
          result = srcPad.pushEvent(event);
          break;
        case com.fluendo.jst.Event.EOS:
        case com.fluendo.jst.Event.NEWSEGMENT:
        default:
          result = srcPad.pushEvent(event);
          break;
      }
      return result;
    }
    protected int chainFunc (com.fluendo.jst.Buffer buf) {
      int ret;

      if (caps == null) {
        Debug.log(Debug.INFO, "mulaw: rate: "+rate);
        Debug.log(Debug.INFO, "mulaw: channels: "+channels);

        caps = new Caps ("audio/x-mulaw");
        caps.setFieldInt ("rate", rate);
        caps.setFieldInt ("channels", channels);
      }

      buf.caps = caps;

      ret = srcPad.push(buf);

      return ret;
    }
  };

  public MulawDec() {
    super();

    rate = 8000;    
    channels = 1;    

    addPad (srcPad);
    addPad (sinkPad);
  }

  public String getFactoryName ()
  {
    return "mulawdec";
  }
  public String getMime ()
  {
    return "audio/x-mulaw";
  }
  public int typeFind (byte[] data, int offset, int length)
  {
    return -1;
  }
}
