/* Copyright (C) <2008> ogg.k.ogg.k <ogg.k.ogg.k@googlemail.com>
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

import java.util.*;
import java.awt.*;
import java.awt.image.*;
import com.fluendo.jst.*;
import com.fluendo.jkate.Event;
import com.fluendo.jtiger.Renderer;
import com.fluendo.utils.*;

/* This element renders a Kate stream on incoming video */
public class KateOverlay extends Overlay
{
  private BufferedImage bimg = null;
  private Font font = null;
  private String text = null;
  private Renderer tr = new Renderer();

  private Pad kateSinkPad = new Pad(Pad.SINK, "katesink") {
    protected boolean eventFunc (com.fluendo.jst.Event event) {
      /* don't propagate, the video sink is the master */
      return true;
    }

    /**
     * This pad receives Kate events, and add them to the renderer.
     * They will be removed from it as they become inactive.
     */
    protected synchronized int chainFunc (com.fluendo.jst.Buffer buf) {
      addKateEvent((com.fluendo.jkate.Event)buf.object);
      return Pad.OK;
    }
  };

  /**
   * Create a new Kate overlay
   */
  public KateOverlay() {
    super();
    addPad(kateSinkPad);
  }


  /**
   * Add a new Kate event to the renderer.
   * This needs locking so the Kate events are not changed while the
   * overlay is rendering them to an image.
   */
  protected synchronized void addKateEvent(com.fluendo.jkate.Event ev) {
    tr.add(ev);
    Debug.log(Debug.DEBUG, "Kate overlay got Kate event: "+new String(ev.text));
  }

  /**
   * Overlay the Kate renderer onto the given image.
   */
  protected synchronized void overlay(com.fluendo.jst.Buffer buf) {
    Image img;

    if (buf.object instanceof ImageProducer) {
      img = component.createImage((ImageProducer)buf.object);
    }
    else if (buf.object instanceof Image) {
      img = (Image)buf.object;
    }
    else {
      System.out.println(this+": unknown buffer received "+buf);
      return;
    }

    /* before rendering, we update the state of the events; for now this
       just weeds out old ones, but at some point motions could be tracked. */
    int ret = tr.update(component, buf.timestamp/(double)Clock.SECOND);
    /* if there are no Kate events active, just return the buffer as is */
    if (ret == 1)
      return;

    Dimension d = component.getSize();
    int x = 0;
    int y = 0;
    int w = d.width;
    int h = d.height;

    if (bimg == null || bimg.getWidth() != w || bimg.getHeight() != h) {
      bimg = component.getGraphicsConfiguration().createCompatibleImage(w, h);
    }

    Graphics2D g2 = bimg.createGraphics();
    g2.drawImage(img, x, y, w, h, null);

    /* render Kate stream on top */
    tr.render(component, bimg);

    g2.dispose();

    buf.object = bimg;
  }

  public String getFactoryName ()
  {
    return "kateoverlay";
  }
}
