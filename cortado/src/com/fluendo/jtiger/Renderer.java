/* JTiger
 * Copyright (C) 2008 ogg.k.ogg.k <ogg.k.ogg.k@googlemail.com>
 *
 * Parts of JTiger are based on code by Wim Taymans <wim@fluendo.com>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public License
 * as published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Library General Public License for more details.
 * 
 * You should have received a copy of the GNU Library General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
 */

package com.fluendo.jtiger;

import java.util.*;
import java.awt.*;
import java.awt.image.*;

public class Renderer {
  private Vector items = new Vector();

  /**
   * Add a new event to the renderer.
   */
  public void add(com.fluendo.jkate.Event ev) {
    items.addElement(new Item(ev));
  }

  /**
   * Update the renderer, and all the events it tracks.
   * Returns 1 if there is nothing to draw, as an optimization
   */
  public int update(Component c, double t) {
    for (int n=0; n<items.size(); ++n) {
      boolean ret = ((Item)items.get(n)).update(c, t);
      if (!ret) {
        items.removeElementAt(n);
        --n;
      }
    }
    if (items.size() == 0)
      return 1;
    return 0;
  }

  /**
   * Renders onto the given image.
   */
  public void render(Component c, BufferedImage bimg) {
    for (int n=0; n<items.size(); ++n) {
      ((Item)items.get(n)).render(c, bimg);
    }
  }
}
