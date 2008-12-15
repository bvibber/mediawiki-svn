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

import java.awt.*;
import java.awt.image.*;
import com.fluendo.utils.*;
import com.fluendo.jst.*;

public class VideoSink extends Sink
{
  private Component component;
  private boolean keepAspect;
  private boolean scale;
  private Frame frame;

  private int width, height;
  private int aspectX, aspectY;

  public VideoSink ()
  {
    keepAspect = true;
    scale = true;
  }

  protected boolean setCapsFunc (Caps caps)
  {
    String mime = caps.getMime();
    if (!mime.equals ("video/raw"))
      return false;

    width = caps.getFieldInt("width", -1);
    height = caps.getFieldInt("height", -1);

    if (width == -1 || height == -1)
      return false;

    aspectX = caps.getFieldInt("aspect_x", 1);
    aspectY = caps.getFieldInt("aspect_y", 1);

    /*
    Debug.log(Debug.DEBUG, this+" dimension: "+width+"x"+height+", aspect: "+aspectX+"/"+aspectY);

    if (aspectY > aspectX) {
      height = height * aspectY / aspectX;
    }
    else {
      width = width * aspectX / aspectY;
    }
    Debug.log(Debug.DEBUG, this+" scaled source: "+width+"x"+height);
    */

    component.setVisible(true);

    return true;
  }

  protected int preroll (Buffer buf)
  {
    return render (buf);
  }

  protected int render (Buffer buf)
  {
    Image image;
    int x, y, w, h;

    if (buf.object instanceof ImageProducer) {
      image = component.createImage((ImageProducer)buf.object);
    }
    else if (buf.object instanceof Image) {
      image = (Image)buf.object;
    }
    else {
      System.out.println(this+": unknown buffer received "+buf);
      return Pad.ERROR;
    }

    if (!component.isVisible())
      return Pad.NOT_NEGOTIATED;

    Dimension d = component.getSize();
    Graphics graphics = component.getGraphics();

    if (keepAspect) {
      double src_ratio, dst_ratio;

      src_ratio = (double) width / height;
      dst_ratio = (double) d.width / d.height;

      if (src_ratio > dst_ratio) {
        w = d.width;
        h = (int) (d.width / src_ratio);
        x = 0;
        y = (d.height - h) / 2;
      } else if (src_ratio < dst_ratio) {
        w = (int) (d.height * src_ratio);
        h = d.height;
        x = (d.width - w) / 2;
        y = 0;
      } else {
        x = 0;
        y = 0;
        w = d.width;
        h = d.height;
      }
    } else if (!scale) {
      w = Math.min (width, d.width);
      h = Math.min (height, d.height);
      x = (d.width - w) / 2;
      y = (d.height - h) / 2;
    } else {
      /* draw in available area */
      w = d.width;
      h = d.height;
      x = 0;
      y = 0;
    }
    graphics.drawImage (image, x, y, w, h, null);

    return Pad.OK;
  };

  public String getFactoryName ()
  {
    return "videosink";
  }

  public boolean setProperty (String name, java.lang.Object value) {
    if (name.equals("component")) {
      component = (Component) value;
    }
    else if (name.equals("keep-aspect")) {
      keepAspect = String.valueOf(value).equals("true");
    }
    else if (name.equals("scale")) {
      scale = String.valueOf(value).equals("true");
    }
    else
      return false;

    return true;
  }

  public java.lang.Object getProperty (String name) {
    if (name.equals("component")) {
      return component;
    }
    else if (name.equals("keep-aspect")) {
      return (keepAspect ? "true": "false");
    }
    return null;
  }

  protected int changeState (int transition) {
    if (currentState == STOP && pendingState == PAUSE && component == null) {
      frame = new Frame();
      component = (Component) frame;
    }
    return super.changeState(transition);
  }
}
