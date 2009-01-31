/* Copyright (C) <2008> ogg.k.ogg.k <ogg.k.ogg.k@googlecode.com>
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
import com.fluendo.utils.*;

/**
 * This class displays a simple text string on top of incoming video.
 */
public class TextOverlay extends Overlay
{
  private BufferedImage bimg = null;
  private Font font = null;
  private String text = null;

  public TextOverlay() {
    super();
  }

  /** 
   * Display a text string (from a property) onto the image.
   */
  protected void overlay(com.fluendo.jst.Buffer buf) {
    Image img;

    /* img retrieval from VideoSink.java */
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

    Dimension d = component.getSize();
    int x = 0;
    int y = 0;
    int w = d.width;
    int h = d.height;

    /* based on a java SDK example */
    if (bimg == null || bimg.getWidth() != w || bimg.getHeight() != h) {
      bimg = component.getGraphicsConfiguration().createCompatibleImage(w, h);
      int font_size = w / 32;
      if (font_size < 12) font_size = 12;
      font = new Font("sans", Font.BOLD, font_size); // TODO: should be selectable ?
    }

    Graphics2D g2 = bimg.createGraphics();
    g2.drawImage(img, x, y, w, h, null);

    /* render text on top */
    if (text != null) {
      double tw;
      g2.setFont(font);
      g2.setColor(Color.white);
      FontMetrics fm = g2.getFontMetrics();
      tw = fm.stringWidth(text);
      g2.drawString(text, x+(int)((w-tw)/2), y+(int)(h*0.85));
    }

    g2.dispose();

    buf.object = bimg;
  }

  public boolean setProperty (String name, java.lang.Object value) {
    if (name.equals("text")) {
      text = value.toString();
    }
    else {
      return super.setProperty(name, value);
    }

    return true;
  }

  public java.lang.Object getProperty (String name) {
    if (name.equals("text")) {
      return text;
    }
    else {
      return super.getProperty(name);
    }
  }

  public String getFactoryName ()
  {
    return "textoverlay";
  }
}
