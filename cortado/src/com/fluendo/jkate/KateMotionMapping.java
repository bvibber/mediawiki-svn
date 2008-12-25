/* JKate
 * Copyright (C) 2008 ogg.k.ogg.k <ogg.k.ogg.k@googlemail.com>
 *
 * Parts of JKate are based on code by Wim Taymans <wim@fluendo.com>
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

package com.fluendo.jkate;

public class KateMotionMapping {
  public static final KateMotionMapping kate_motion_mapping_none = new KateMotionMapping ();
  public static final KateMotionMapping kate_motion_mapping_frame = new KateMotionMapping ();
  public static final KateMotionMapping kate_motion_mapping_window = new KateMotionMapping ();
  public static final KateMotionMapping kate_motion_mapping_region = new KateMotionMapping ();
  public static final KateMotionMapping kate_motion_mapping_event_duration = new KateMotionMapping ();
  public static final KateMotionMapping kate_motion_mapping_bitmap_size = new KateMotionMapping ();

  private static final KateMotionMapping[] list = {
    kate_motion_mapping_none,
    kate_motion_mapping_frame,
    kate_motion_mapping_window,
    kate_motion_mapping_region,
    kate_motion_mapping_event_duration,
    kate_motion_mapping_bitmap_size,
  };

  private KateMotionMapping() {
  }

  /**
   * Create a KateMotionMapping object from an integer.
   */
  public static KateMotionMapping CreateMotionMapping(int idx) throws KateException {
    if (idx < 0 || idx >= list.length)
      throw new KateException("Motion mapping "+idx+" out of bounds");
    return list[idx];
  }
}
