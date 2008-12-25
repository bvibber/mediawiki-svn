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

public class KateMotionSemantics {
  public static final KateMotionSemantics kate_motion_semantics_time = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_z = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_region_position = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_region_size = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_text_alignment_int = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_text_alignment_ext = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_text_position = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_text_size = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_marker1_position = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_marker2_position = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_marker3_position = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_marker4_position = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_glyph_pointer_1 = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_glyph_pointer_2 = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_glyph_pointer_3 = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_glyph_pointer_4 = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_text_color_rg = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_text_color_ba = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_background_color_rg = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_background_color_ba = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_draw_color_rg = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_draw_color_ba = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_style_morph = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_text_path = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_text_path_section = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_draw = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_text_visible_section = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_horizontal_margins = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_vertical_margins = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_bitmap_position = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_bitmap_size = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_marker1_bitmap = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_marker2_bitmap = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_marker3_bitmap = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_marker4_bitmap = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_glyph_pointer_1_bitmap = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_glyph_pointer_2_bitmap = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_glyph_pointer_3_bitmap = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_glyph_pointer_4_bitmap = new KateMotionSemantics ();
  public static final KateMotionSemantics kate_motion_semantics_draw_width = new KateMotionSemantics ();

  private static final KateMotionSemantics[] list = {
    kate_motion_semantics_time,
    kate_motion_semantics_z,
    kate_motion_semantics_region_position,
    kate_motion_semantics_region_size,
    kate_motion_semantics_text_alignment_int,
    kate_motion_semantics_text_alignment_ext,
    kate_motion_semantics_text_position,
    kate_motion_semantics_text_size,
    kate_motion_semantics_marker1_position,
    kate_motion_semantics_marker2_position,
    kate_motion_semantics_marker3_position,
    kate_motion_semantics_marker4_position,
    kate_motion_semantics_glyph_pointer_1,
    kate_motion_semantics_glyph_pointer_2,
    kate_motion_semantics_glyph_pointer_3,
    kate_motion_semantics_glyph_pointer_4,
    kate_motion_semantics_text_color_rg,
    kate_motion_semantics_text_color_ba,
    kate_motion_semantics_background_color_rg,
    kate_motion_semantics_background_color_ba,
    kate_motion_semantics_draw_color_rg,
    kate_motion_semantics_draw_color_ba,
    kate_motion_semantics_style_morph,
    kate_motion_semantics_text_path,
    kate_motion_semantics_text_path_section,
    kate_motion_semantics_draw,
    kate_motion_semantics_text_visible_section,
    kate_motion_semantics_horizontal_margins,
    kate_motion_semantics_vertical_margins,
    kate_motion_semantics_bitmap_position,
    kate_motion_semantics_bitmap_size,
    kate_motion_semantics_marker1_bitmap,
    kate_motion_semantics_marker2_bitmap,
    kate_motion_semantics_marker3_bitmap,
    kate_motion_semantics_marker4_bitmap,
    kate_motion_semantics_glyph_pointer_1_bitmap,
    kate_motion_semantics_glyph_pointer_2_bitmap,
    kate_motion_semantics_glyph_pointer_3_bitmap,
    kate_motion_semantics_glyph_pointer_4_bitmap,
    kate_motion_semantics_draw_width,
  };

  private KateMotionSemantics() {
  }

  /**
   * Create a KateMotionSemantics object from an integer.
   */
  public static KateMotionSemantics CreateMotionSemantics(int idx) throws KateException {
    if (idx < 0 || idx >= list.length)
      throw new KateException("Motion semantics "+idx+" out of bounds");
    return list[idx];
  }
}
