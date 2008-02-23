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

package com.fluendo.jst;

import java.util.*;

public class Pad extends com.fluendo.jst.Object implements Runnable
{
  /* pad directions */
  public static final int UNKNOWN = 0;
  public static final int SRC = 1;
  public static final int SINK = 2;

  /* flow return values */
  public static final int OK = 0;
  public static final int NOT_LINKED = -1;
  public static final int WRONG_STATE = -2;
  public static final int UNEXPECTED = -3;
  public static final int NOT_NEGOTIATED = -4;
  public static final int ERROR = -5;
  public static final int NOT_SUPPORTED = -6;

  /* modes */
  public static final int MODE_NONE = 0;
  public static final int MODE_PUSH = 1;
  public static final int MODE_PULL = 2;

  protected Pad peer;
  protected int direction = UNKNOWN;
  protected boolean flushing;
  protected java.lang.Object streamLock =  new java.lang.Object();
  int mode;
  private Vector capsListeners = new Vector();

  protected Caps caps;
  
  /* task stuff */
  private static final int T_STOP = 0;
  private static final int T_PAUSE = 1;
  private static final int T_START = 2;
  private Thread thread;
  private int taskState;

  public static final boolean isFlowFatal (int ret) 
  {
    return ret <= UNEXPECTED;
  }
  public static final boolean isFlowSuccess (int ret) 
  {
    return ret >= OK;
  }
  public static final String getFlowName (int ret) {
    switch (ret) {
      case OK:
        return "ok";
      case NOT_LINKED:
        return "not-linked";
      case WRONG_STATE:
        return "wrong-state";
      case UNEXPECTED:
        return "unexpected";
      case NOT_NEGOTIATED:
        return "not-negotiated";
      case ERROR:
        return "error";
      case NOT_SUPPORTED:
        return "not-supported";
      default:
        return "unknown";
    }
  }

  public Pad(int direction) {
    this (direction, null);
  }
  public Pad(int direction, String name) {
    super(name);
    this.direction = direction;
  }

  public String toString () {
    String parentName;
    String thisName;

    if (parent != null)
      parentName = parent.getName();
    else
      parentName = "";

    thisName = getName();
    if (thisName == null)
      thisName="";

    return "Pad: "+parentName+":"+thisName+" ["+super.toString()+"]";
  }
  public synchronized void addCapsListener(CapsListener listener)
  {
    capsListeners.addElement (listener);
  }
  public synchronized void removeCapsListener(CapsListener listener)
  {
    capsListeners.removeElement (listener);
  }
  private synchronized void doCapsListeners(Caps caps)
  {
    for (Enumeration e = capsListeners.elements(); e.hasMoreElements();) {
      CapsListener listener = (CapsListener) e.nextElement();
      listener.capsChanged (caps);
    }
  }

  public synchronized boolean link (Pad newPeer) {

    /* already was connected */
    if (peer != null)
      return false;

    /* wrong direction */
    if (direction != SRC) 
      return false;

    synchronized (newPeer) {
      if (newPeer.direction != SINK)
	return false;

      /* peer was connected */
      if (newPeer.peer != null)
	return false;

      peer = newPeer;
      peer.peer = this;
    }
    return true;
  }

  public synchronized void unlink () {
    if (peer == null)
      return;

    if (direction == SRC) {
      peer.unlink ();
    }
    peer = null;
  }

  public synchronized Pad getPeer () {
    return peer;
  }

  protected boolean eventFunc (Event event)
  {
    boolean result;

    switch (event.getType()) {
      case Event.FLUSH_START:
      case Event.FLUSH_STOP:
      case Event.EOS:
      case Event.SEEK:
      case Event.NEWSEGMENT:
      default:
        result = false;
        break;
    }
    return result;
  }

  public final boolean sendEvent (Event event) {
    boolean result;

    switch (event.getType()) {
      case Event.FLUSH_START:
        setFlushing (true);
        result = eventFunc (event);
        break;
      case Event.FLUSH_STOP:
        synchronized (streamLock) {
          setFlushing (false);
          result = eventFunc (event);
	}
        break;
      case Event.NEWSEGMENT:
      case Event.EOS:
        synchronized (streamLock) {
	  result = eventFunc (event);
	}
        break;
      case Event.SEEK:
        result = eventFunc (event);
        break;
      default:
        result = false;
        break;
    }
    return result;
  }

  public boolean query (Query query) {
    return false;
  }

  public synchronized Caps getCaps () {
    return this.caps;
  }

  protected boolean setCapsFunc (Caps caps) {
    return true;
  }

  public boolean setCaps (Caps caps) {
    boolean res = true;

    if (caps != null)
      res = setCapsFunc (caps);

    if (res) {
      this.caps = caps;
      if (caps != null)
        doCapsListeners (caps);
    }
    return res;
  }

  private final int chain (Buffer buffer) {
    synchronized (streamLock) {
      synchronized (this) {
        if (flushing) 
	  return WRONG_STATE;

	if (buffer.caps != null && buffer.caps != caps) {
	  if (!setCaps(buffer.caps)) {
	    buffer.free();
	    return NOT_NEGOTIATED;
	  }
	}
      }
      int res = chainFunc(buffer); 
      return res;
    }
  }

  protected int chainFunc (Buffer buffer)
  {
    return ERROR;
  }

  public final int push (Buffer buffer) {
    if (peer == null) {
      return NOT_LINKED;
    }
    return peer.chain (buffer);
  }

  public final boolean pushEvent (Event event) {
    if (peer == null)
      return false;

    return peer.sendEvent (event);
  }

  public synchronized void setFlushing (boolean flush) {
    flushing = flush;
  }
  public synchronized boolean isFlushing () {
    return flushing;
  }

  protected boolean activateFunc (int mode)
  {
    return true;
  }

  public final boolean activate (int newMode)
  {
    boolean res;

    if (mode == newMode)
      return true;

    if (newMode == MODE_NONE) {
      setFlushing (true);
    }
    if ((res = activateFunc (newMode)) == false)
      return false;

    if (newMode != MODE_NONE) {
      setFlushing (false);
    }
    else {
      synchronized (streamLock) {
        setCaps (null);
      }
    }
    mode = newMode;

    return res;
  }

  protected void taskFunc()
  {
  }

  public void run() {
    synchronized (streamLock) {
      while (taskState != T_STOP) {
        while (taskState == T_PAUSE) {
	  try {
	    streamLock.wait();
	  }
	  catch (InterruptedException ie) {}
	}
        if (taskState == T_STOP) 
	  break;

	try {
          taskFunc();
	}
	catch (Throwable t) {
          t.printStackTrace();
	}
      }
    }
  }

  public boolean startTask(String name)
  {
    synchronized (streamLock) {
      taskState = T_START;
      if (thread == null) {
        thread = new Thread(this, name);
        thread.start();
      }
      streamLock.notifyAll();
    }
    return true;
  }
  public boolean pauseTask()
  {
    taskState = T_PAUSE;
    synchronized (streamLock) {
      taskState = T_PAUSE;
    }
    return true;
  }

  public boolean stopTask()
  {
    Thread t;

    taskState = T_STOP;
    synchronized (streamLock) {
      taskState = T_STOP;
      streamLock.notifyAll();
      t = thread;
      thread = null;
    }
    try {
      t.join();
    }
    catch (InterruptedException ie) {}

    return true;
  }
}
