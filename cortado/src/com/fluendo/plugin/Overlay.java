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
import com.fluendo.jst.*;
import com.fluendo.utils.*;

/**
 * This is a base overlay element, just passes images from sink to source.
 * Extend this and override the overlay function to draw something onto
 * images as they go from sink to source.
 */
public class Overlay extends Element
{
  protected Component component;

  private Pad videoSrcPad = new Pad(Pad.SRC, "videosrc") {
    protected boolean eventFunc (com.fluendo.jst.Event event) {
      return videoSinkPad.pushEvent(event);
    }
  };

  private Pad videoSinkPad = new Pad(Pad.SINK, "videosink") {
    protected boolean eventFunc (com.fluendo.jst.Event event) {
      return videoSrcPad.pushEvent (event);
    }

    /**
     * Receives an image, allows a derived class to overlay whatever it wants on it,
     * and sends it to the video source pad.
     */
    protected int chainFunc (com.fluendo.jst.Buffer buf) {
      int result;

      Debug.log( Debug.DEBUG, parent.getName() + " <<< " + buf );

      overlay(buf);

      result = videoSrcPad.push(buf);
      if (result != Pad.OK) {
        Debug.log( Debug.WARNING, parent.getName() + ": failed to push buffer to video source pad: "+result);
      }

      return result;
    }

    protected boolean activateFunc (int mode)
    {
      return true;
    }
  };

  public Overlay() {
    super();

    addPad (videoSinkPad);
    addPad (videoSrcPad);
  }

  /**
   * this function may be overridden to draw whatever the derived
   * class wants onto the incoming image.
   * By default, the image is passed without alteration.
   */
  protected void overlay(com.fluendo.jst.Buffer buf) {
    /* straight pass through by default */
  }

  public boolean setProperty (String name, java.lang.Object value) {
    if (name.equals("component")) {
      component = (Component) value;
    }
    else {
      return super.setProperty(name, value);
    }

    return true;
  }

  public java.lang.Object getProperty (String name) {
    if (name.equals("component")) {
      return component;
    }
    else {
      return super.getProperty(name);
    }
  }

  /* from the video sink code, I do not understand what this does semantically,
     the frame would be 0x0 sized. Maybe just to avoid possible null dereference,
     but I suspect there might be something more clever, so it goes in for safety */
  protected int changeState (int transition) {
    if (currentState == STOP && pendingState == PAUSE && component == null) {
      Frame frame = new Frame();
      component = (Component) frame;
    }
    return super.changeState(transition);
  }

  public String getFactoryName ()
  {
    return "overlay";
  }
}
