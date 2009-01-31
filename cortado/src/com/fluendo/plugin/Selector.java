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
 * This element receives data from N sinks, and selects one of them
 * to send from its source.
 */
public class Selector extends Element
{
  private Vector sinks = new Vector();
  int selected = -1;
  Pad selectedPad = null;

  private Pad srcPad = new Pad(Pad.SRC, "src") {
    /**
     * Pushes the event to every sink.
     */
    protected boolean eventFunc (com.fluendo.jst.Event event) {
      boolean ret = true;
      for (int n=0; n<sinks.size(); ++n) {
        ret &= ((Pad)sinks.get(n)).pushEvent(event);
      }
      return ret;
    }
  };

  private int findPad (Pad pad) {
    for (int n=0; n<sinks.size(); ++n) {
      if (sinks.get(n) == pad)
        return n;
    }
    return -1;
  }

  /**
   * Requests a new sink pad to be created for the given peer.
   * The caps do not matter, as Selector is a caps agnostic element.
   */
  public Pad requestSinkPad(Pad peer) {
    Pad pad = new Pad(Pad.SINK, "sink"+sinks.size()) {
      protected boolean eventFunc (com.fluendo.jst.Event event) {
        if (selectedPad == this) {
          return srcPad.pushEvent (event);
        }
        return true;
      }

      protected int chainFunc (com.fluendo.jst.Buffer buf) {
        int result = Pad.OK;

        //Debug.log( Debug.DEBUG, parent.getName() + " <<< " + buf );
        Debug.debug("Selector got "+buf.caps+" buffer on "+this.toString());

        if (selectedPad == this) {
          Debug.debug("what a coincidence, we're selected - pushing");
          result = srcPad.push(buf);
        }

        return result;
      }

      protected boolean activateFunc (int mode)
      {
        return true;
      }
    };

    sinks.addElement(pad);
    addPad(pad);
    return pad;
  }

  public Selector() {
    super();

    addPad (srcPad);
  }

  /**
   * The selected sink may be selected via the "selected" property - negative to select nothing
   */
  public boolean setProperty (String name, java.lang.Object value) {
    if (name.equals("selected")) {
      int new_selected = Integer.valueOf(value.toString()).intValue();
      Debug.info("Selector: request to select "+new_selected+" (from "+selected+"), within 0-"+(sinks.size()-1));
      if (new_selected < 0 || new_selected >= sinks.size()) {
        selected = -1;
        selectedPad = null;
      }
      else {
        selected = new_selected;
        selectedPad = (Pad)sinks.get(selected);
      }
    }
    else {
      return super.setProperty(name, value);
    }

    return true;
  }

  public java.lang.Object getProperty (String name) {
    if (name.equals("selected")) {
      return new Integer(selected);
    }
    else {
      return super.getProperty(name);
    }
  }

  public String getFactoryName ()
  {
    return "selector";
  }
}
