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

import java.util.*;
import com.jcraft.jogg.*;

interface OggPayload
{
  /**
   * Check if the packet contains the signature
   * of the payload.
   */
  public boolean isType (Packet op);
  /**
   * Initialize the payload with a header packet.
   */
  public int takeHeader (Packet op);
  /**
   * Check if the packet contains a header packet
   */
  public boolean isHeader (Packet op);
  /**
   * Check if the packet contains a keyframe
   */
  public boolean isKeyFrame (Packet op);
  /**
   * Get the first timestamp of the list of packets
   */
  public long getFirstTs (Vector packets);
  /**
   * Convert the granule pos to a timestamp
   */
  public long granuleToTime (long gp);
  /**
   * Get mime type
   */
  public String getMime ();
}

