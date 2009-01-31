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
import java.awt.font.*;
import java.text.*;
import com.fluendo.jkate.Event;
import com.fluendo.jkate.Tracker;
import com.fluendo.utils.*;

public class Item {
  private Tracker kin = null;
  private boolean alive = false;
  private Font font = null;
  private int font_size = 0;
  private String text = null;
  private TigerBitmap background_image = null;

  private int width = -1;
  private int height = -1;

  private float region_x;
  private float region_y;
  private float region_w;
  private float region_h;


  /**
   * Create a new item from a Kate event.
   */
  public Item(com.fluendo.jkate.Event ev) {
    this.kin = new Tracker(ev);
    text = null;
    if (ev.text != null && ev.text.length > 0) {
      try {
        text = new String(ev.text, "UTF8");
      }
      catch (Exception e) {
        Debug.warning("Failed to convert text from UTF-8 - text will not display");
        text = null;
      }
    }
  }

  /**
   * Create a font suitable for displaying on the given component
   */
  protected void createFont(Component c) {
    Dimension d = c.getSize();
    font_size = d.width / 32;
    if (font_size < 12) font_size = 12;
    font = new Font("sansserif", Font.BOLD, font_size); // TODO: should be selectable ?
  }

  /**
   * Regenerate any cached data to match any relevant changes in the
   * given component
   */
  protected void updateCachedData(Component c) {
    Dimension d = c.getSize();
    if (d.width == width && d.height == height)
      return;

    createFont(c);

    width = d.width;
    height = d.height;
  }

  /**
   * Updates the item at the given time.
   * returns true for alive, false for dead
   */
  public boolean update(Component c, double t) {
    com.fluendo.jkate.Event ev = kin.ev;
    if (ev == null) return false;
    if (t >= ev.end_time) return false;

    if (t < ev.start_time) {
      alive = false;
    }
    else {
      alive = true;
    }

    Dimension d = c.getSize();
    return kin.update(t-ev.start_time, d, d);
  }

  /**
   * Set up the region.
   */
  public void setupRegion(Component c) {
    if (kin.has[Tracker.has_region]) {
      region_x = kin.region_x;
      region_y = kin.region_y;
      region_w = kin.region_w;
      region_h = kin.region_h;
    }
    else {
      Dimension d = c.getSize();
      region_x = d.width * 0.1f;
      region_y = d.height * 0.8f;
      region_w = d.width * 0.8f;
      region_h = d.height * 0.1f;
    }
  }

  /**
   * Renders the item on the given image.
   */
  public void render(Component c, BufferedImage bimg) {
    com.fluendo.jkate.Event ev = kin.ev;

    if (!alive)
      return;

    updateCachedData(c);

    setupRegion(c);
    renderBackground(bimg);
    renderText(bimg);
  }

  /**
   * Render a background for the item, if approrpiate.
   * The background may be a color, or an image.
   */
  public void renderBackground(BufferedImage bimg)
  {
    if (kin.ev.bitmap != null) {
      if (background_image == null) {
        background_image = new TigerBitmap(kin.ev.bitmap, kin.ev.palette);
      }
      
      Graphics2D g = bimg.createGraphics();
      int rx = (int)(region_x+0.5), ry = (int)(region_y+0.5);
      int rw = (int)(region_w+0.5), rh = (int)(region_h+0.5);
      g.drawImage(background_image.getScaled(rw, rh), rx, ry, null);
      g.dispose();
    }
  }

  /**
   * Render text text for the item, if approrpiate.
   */
  public void renderText(BufferedImage bimg)
  {
    if (text == null)
      return;

    Graphics2D g = bimg.createGraphics();

    AttributedString atext = new AttributedString(text, font.getAttributes());
    AttributedCharacterIterator text_it = atext.getIterator();
    int text_end = text_it.getEndIndex();

    FontRenderContext frc = g.getFontRenderContext();
    LineBreakMeasurer lbm = new LineBreakMeasurer(text_it, frc);
    float dy = 0.0f;
    float shadow_dx = font_size * 0.05f, shadow_dy = font_size * 0.05f;
    while (lbm.getPosition() < text_end) {
      TextLayout layout = lbm.nextLayout(region_w);
      dy += layout.getAscent();
      float tw = layout.getAdvance();

      g.setColor(Color.black);
      layout.draw(g, region_x+((region_w-tw)/2)+shadow_dx, region_y+dy+shadow_dy);
      layout.draw(g, region_x+((region_w-tw)/2)-shadow_dx, region_y+dy-shadow_dy);
      layout.draw(g, region_x+((region_w-tw)/2)+shadow_dx, region_y+dy-shadow_dy);
      layout.draw(g, region_x+((region_w-tw)/2)-shadow_dx, region_y+dy+shadow_dy);
      g.setColor(Color.white);
      layout.draw(g, region_x+((region_w-tw)/2), region_y+dy);

      dy += layout.getDescent() + layout.getLeading();
    }

    g.dispose();
  }
}
