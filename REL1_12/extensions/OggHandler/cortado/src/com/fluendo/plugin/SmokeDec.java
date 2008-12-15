/* Smoke Codec
 * Copyright (C) <2004> Wim Taymans <wim@fluendo.com>
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

import java.awt.*;
import com.fluendo.codecs.*;
import com.fluendo.jst.*;
import com.fluendo.utils.*;

public class SmokeDec extends Element 
{
  private Component component;
  private MediaTracker mediaTracker;
  private SmokeCodec smoke;
  private int width, height;

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
      Image img = null;

      img = smoke.decode(buf.data, buf.offset, buf.length);
      if (img != null) {
        if (img.getWidth(null) != width || img.getHeight(null) != height) {
          width = img.getWidth(null);
          height = img.getHeight(null);

          Debug.log(Debug.INFO, "smoke frame: "+width+","+height);

          caps = new Caps ("video/raw");
          caps.setFieldInt ("width", width);
          caps.setFieldInt ("height", height);
          caps.setFieldInt ("aspect_x", 1);
          caps.setFieldInt ("aspect_y", 1);
        }
        buf.object = img;
        buf.caps = caps;

        ret = srcPad.push(buf);
      }
      else {
        if ((smoke.flags & SmokeCodec.KEYFRAME) != 0) {
          Debug.log (Debug.WARNING, "could not decode jpeg image");
	}
        buf.free();
        ret = OK;
      }
      return ret;
    }
  };

  public SmokeDec() {
    super();

    addPad(srcPad);
    addPad(sinkPad);
  }

  public boolean setProperty (String name, java.lang.Object value) {
    if (name.equals("component")) {
      component = (Component) value;
      mediaTracker = new MediaTracker (component);
      smoke = new SmokeCodec (component, mediaTracker);
    }
    else
      return false;

    return true;
  }

  public java.lang.Object getProperty (String name) {
    if (name.equals("component")) {
      return component;
    }
    return null;
  }

  public String getFactoryName ()
  {
    return "smokedec";
  }
  public String getMime ()
  {
    return "video/x-smoke";
  }
  public int typeFind (byte[] data, int offset, int length)
  {
    if (data[offset+1] == 0x73) {
      return 10;
    }
    return -1;
  }
}
