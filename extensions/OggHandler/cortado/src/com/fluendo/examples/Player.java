/* Cortado - a video player java applet
 * Copyright (C) 2004 Fluendo S.L.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Street #330, Boston, MA 02111-1307, USA.
 */

package com.fluendo.examples;

import com.fluendo.player.*;
import java.awt.*;

public class Player extends Frame {
  private static final long serialVersionUID = 1L;
Cortado applet;

  public Player(String url) {
    applet = new Cortado();
    applet.setSize(352, 270);
    setSize(352, 270);

    applet.setParam ("url", url);
    applet.setParam ("local", "false");
    //applet.setParam ("seekable", "true");
    //applet.setParam ("duration", "00352");
    applet.setParam ("framerate", "60");
    applet.setParam ("keepaspect", "true");
    applet.setParam ("video", "true");
    applet.setParam ("audio", "true");
    //applet.setParam ("audio", "false");
    applet.setParam ("bufferSize", "200");
    applet.setParam ("userId", "wim");
    applet.setParam ("password", "taymans");

    add(applet);
    show();

    applet.init();
    applet.start();
  }

  public static void main(String args[]) {
    Player p;

    if (args.length < 1) {
      System.out.println ("usage: Player <uri>");
      return;
    }

    p = new Player(args[0]);
    
    synchronized (p) {
      try {
        p.wait ();
      }
      catch (InterruptedException ie) {}
    }
  }
}
