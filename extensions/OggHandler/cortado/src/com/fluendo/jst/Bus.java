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

package com.fluendo.jst;

import java.util.*;

public class Bus {
  private Vector queue;
  private Vector handlers;
  private boolean flushing;
  private BusSyncHandler syncHandler;

  public Bus() {
    queue = new Vector();
    handlers = new Vector();
    flushing = false;
  }

  public synchronized void addHandler (BusHandler handler) {
    handlers.addElement (handler);
  }
  public synchronized void removeHandler (BusHandler handler) {
    handlers.removeElement (handler);
  }

  public synchronized void setSyncHandler (BusSyncHandler handler) {
    syncHandler = handler;
  }

  private void notifyHandlers (Vector handlers, Message message) {
    for (Enumeration e = handlers.elements(); e.hasMoreElements();)
    {
      BusHandler handler = (BusHandler) e.nextElement();
      handler.handleMessage (message);
    }
  }

  public void post(Message message) {
    boolean post = true;
    BusSyncHandler handler;

    synchronized (this) {
      if (flushing)
	return;
      handler = syncHandler;
    }
    post = (handler == null || handler.handleSyncMessage (message) == BusSyncHandler.PASS);

    synchronized (this) {
      if (post && !flushing) {
        queue.addElement (message);
        notifyAll();
      }
    }
  }
  public synchronized Message peek() {
    if (queue.isEmpty() || flushing)
      return null;
    return (Message) queue.firstElement();
  }
  public synchronized Message pop() {
    Message ret;

    if (queue.isEmpty() || flushing)
      return null;
    ret = (Message) queue.elementAt(0);
    queue.removeElementAt(0);
    return ret;
  }
  public synchronized Message poll(long timeout) {
    if (queue.isEmpty() && !flushing) {
      try {
        wait(timeout);
      }
      catch (InterruptedException e) {}
    }
    return pop();
  }
  public synchronized void setFlushing (boolean flush) {
    flushing = flush;
    queue.setSize(0);
    notifyAll();
  }
  public void waitAndDispatch() {
    Message msg;

    msg = poll (0);
    if (msg != null)
      notifyHandlers (handlers, msg);
  }
}
