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

public class Pipeline extends com.fluendo.jst.Element implements BusSyncHandler
{
  protected Vector elements = new Vector();

  protected Clock defClock;
  protected Clock fixedClock = null;

  protected Bus internalBus;
  private Thread busThread;

  private StateThread stateThread;
  private boolean stateDirty = false;
  private boolean polling = false;

  protected long streamTime;

  private class BusThread extends Thread
  {
    private Bus bus;
    private boolean stopping;

    public BusThread (Bus bus)
    {
      this.bus = bus;
      stopping = false;
    }
    public synchronized void run() {
      while (!stopping) {
        bus.waitAndDispatch ();
      }
    }
    public synchronized void shutDown() {
      stopping = true;
      bus.unblockPoll();
    }
  }

  private class StateThread extends Thread
  {
    private boolean stopping;
    private boolean stateDirty;

    public StateThread ()
    {
      stopping = false;
      stateDirty = false;
    }
    public void run() {
      while (!stopping) {
        synchronized (this) {
          while (!stateDirty) {
	    try {
              wait();
	    } catch (InterruptedException e) {}
	  }
          stateDirty = false;
        }
        synchronized (stateLock) {
          reCalcState(false);
        }
      }
    }
    public synchronized void stateDirty() {
      stateDirty = true;
      notifyAll ();
    }
  }

  public Pipeline() {
    this (null);
  }

  public String getFactoryName () {
    return "pipeline";
  }

  public Pipeline(String name) {
    super (name);

    defClock = new SystemClock(); 

    internalBus = new Bus();
    internalBus.setSyncHandler (this);
    bus = new Bus();
    busThread = new BusThread(bus);
    busThread.start();
    stateThread = new StateThread();
    stateThread.start();
  }

  public void useClock(Clock clock) {
    fixedClock = clock;
  }

  public boolean add(Element elem) {
    if (elem == null)
      return false;

    if (elem instanceof ClockProvider)
      defClock = ((ClockProvider)elem).provideClock();

    elements.addElement (elem);
    elem.baseTime = baseTime;
    elem.setBus (internalBus);
    return true;
  }
  public boolean remove(Element elem) {
    boolean res;

    if (elem == null)
      return false;

    if ((res = elements.removeElement (elem))) {
      elem.setBus (null);
      elem.setClock (null);
    }
    return res;
  }

  public Enumeration enumElements()
  {
    return elements.elements();
  }

  private class SortedEnumerator implements Enumeration
  {
    private Vector queue;
    private Hashtable hash;
    private java.lang.Object next;
    private int mode;

    private void addToQueue (Element elem)
    {
      queue.addElement (elem);
      hash.put (elem, new Integer(-1));
    }

    private void updateDegree (Element elem)
    {
      for (Enumeration p = elem.enumPads(); p.hasMoreElements();) {
        Pad pad = (Pad) p.nextElement();

	if (pad.direction == Pad.SINK) {
	  Pad peer;
	  Element peerParent;
	  int oldDeg, newDeg;

	  peer = pad.peer;
	  if (peer == null)
	    continue;

	  peerParent = (Element) peer.parent;
	  if (peerParent == null)
	    continue;

	  oldDeg = ((Integer)hash.get (peerParent)).intValue();
	  newDeg = oldDeg + mode;

	  if (newDeg == 0) {
	    addToQueue (peerParent);
	  }
	  else {
	    hash.put (peerParent, new Integer (newDeg));
	  }
	}
      }
    }

    public SortedEnumerator() {
      queue = new Vector();
      hash = new Hashtable();

      /* reset all degrees, add sinks to queue */
      for (Enumeration e = enumElements(); e.hasMoreElements();) {
        Element elem = (Element) e.nextElement();

        if (elem.isFlagSet (Element.FLAG_IS_SINK)) {
	  addToQueue (elem);
	}
	else {
	  hash.put (elem, new Integer(0));
	}
      }
      mode = 1;
      /* update all degrees */
      for (Enumeration e = enumElements(); e.hasMoreElements();) {
        updateDegree ((Element) e.nextElement());
      }
      mode = -1;
      queueNextElement();
    }
    private void queueNextElement () 
    {
      
      if (queue.isEmpty()) {
        int bestDeg = Integer.MAX_VALUE;
	Element bestElem = null;

        for (Enumeration e = enumElements(); e.hasMoreElements();) {
          Element elem = (Element) e.nextElement();
	  int deg;

	  deg = ((Integer)hash.get (elem)).intValue();
	  if (deg < 0)
	    continue;

	  if (bestElem == null || bestDeg > deg) {
	    bestElem = elem;
	    bestDeg = deg;
	  }
	}
	if (bestElem != null) {
	  if (bestDeg != 0) {
	    System.out.println ("loop detected!!");
	  }
	  next = bestElem;
	  hash.put (next, new Integer(-1));
	}
	else {
	  next = null;
	}
      }
      else {
        next = queue.elementAt (0);
        queue.removeElementAt (0);
      }
      if (next != null)
        updateDegree ((Element) next);
    }

    public boolean hasMoreElements()
    {
      return next != null;
    }
    public java.lang.Object nextElement() throws NoSuchElementException
    {
      java.lang.Object result = next;

      if (result == null)
        throw new NoSuchElementException();
      
      queueNextElement ();

      return result;
    }
  }

  public Enumeration enumSorted()
  {
    return new SortedEnumerator();
  }
  
  private class SinkEnumerator implements Enumeration
  {
    private Enumeration e;
    private java.lang.Object next;

    public SinkEnumerator() {
      e = enumElements();
      queueNextElement();
    }
    private void queueNextElement()
    {
      next = null;
      while (e.hasMoreElements()) {
        Element elem = (Element) e.nextElement();

        if (elem.isFlagSet (Element.FLAG_IS_SINK)) {
          next = elem;
	  break;
	}
      }
    }

    public boolean hasMoreElements() 
    {
      return next != null;
    }
    public java.lang.Object nextElement() throws NoSuchElementException
    {
      java.lang.Object result = next;

      if (result == null)
        throw new NoSuchElementException();
      
      queueNextElement ();

      return result;
    }
  }

  public Enumeration enumSinks()
  {
    return new SinkEnumerator();
  }

  public int handleSyncMessage (Message message) 
  {
    switch (message.getType()) {
      case Message.EOS:
        break;
      case Message.STATE_DIRTY:
        synchronized (this) {
	  stateDirty = true;
          stateThread.stateDirty();
	}
        break;
      default:
        /* post to app */
        postMessage (message);
        break;
    }
    return BusSyncHandler.DROP;
  }

  public int getState(int[] resState, int[] resPending, long timeout) {
    reCalcState (false);
    return super.getState (resState, resPending, timeout);
  }

  private void reCalcState(boolean force) 
  {
    boolean haveAsync, haveNoPreroll;
    int res = SUCCESS;

    synchronized (this) {
      if (force)
        stateDirty = true;
      
      if (!stateDirty)
        return;

      if (polling)
        return;
 
      polling = true;
      stateDirty = false;
      haveAsync = false;
      haveNoPreroll = false;
    }
    for (Enumeration e = elements.elements(); e.hasMoreElements();) {
      Element elem = (Element) e.nextElement();

      res = elem.getState(null, null, 1);
      switch (res) {
        case ASYNC:
	  haveAsync = true;
          break;
        case NO_PREROLL:
	  haveNoPreroll = true;
          break;
      }
      if (res == FAILURE)
        break;
    }

    if (res != FAILURE) {
      if (haveNoPreroll)
        res = NO_PREROLL;
      if (haveAsync)
        res = ASYNC;
    }
    
    synchronized (this) {
      polling = false;

      switch (res) {
        case SUCCESS:
        case NO_PREROLL:
          res = continueState(res);
          break;
        case ASYNC:
	  lostState();
          break;
        case FAILURE:
          abortState();
          break;
        default:
          break;
      }
    }
    return;
  }

  protected int doChildStateChange(int transition)
  {
    int next;
    int result;
    boolean haveAsync, haveNoPreroll;

    next = getTransitionNext (transition);

    haveAsync = false;
    haveNoPreroll = false;

    for (Enumeration e = enumSorted(); e.hasMoreElements();) {
      Element elem = (Element) e.nextElement();

      elem.setBus (internalBus);
      elem.setClock (defClock);
      elem.baseTime = baseTime;

      result = elem.setState (next);

      switch (result) {
        case ASYNC:
          haveAsync = true;
	  break;
	case NO_PREROLL:
          haveNoPreroll = true;
	  break;
	case FAILURE:
          return result;
      }
    }

    result = super.changeState(transition);
    if (result == FAILURE)
      return result;

    if (haveNoPreroll)
      result = NO_PREROLL;
    else if (haveAsync)
      result = ASYNC;

    return result;
  }

  public int changeState(int transition)
  {
    int result;

    switch (transition) {
      case STOP_PAUSE:
        break;
      case PAUSE_PLAY:
        long now = defClock.getTime();
        baseTime = now - streamTime;
        break;
      default:
        break;
    }

    result = doChildStateChange(transition);

    switch (transition) {
      case STOP_PAUSE:
        streamTime = 0;
	break;
      case PLAY_PAUSE:
        long now = defClock.getTime();
        streamTime = now - baseTime;
        break;
      case PAUSE_STOP:
        break;
      default:
        break;
    }

    return result;
  }

  protected boolean doSendEvent(Event event)
  {
    boolean res = true;

    for (Enumeration e = enumSinks(); e.hasMoreElements();) {
      Element elem = (Element) e.nextElement();

      res &= elem.sendEvent (event);
    }
    return res;
  }

  private boolean doSeek(Event event)
  {
    boolean ret;

    setState (Element.PAUSE);

    ret = doSendEvent (event);

    streamTime = 0;
    setState (Element.PLAY);

    return ret;
  }

  public boolean sendEvent(Event event)
  {
    switch (event.getType()) {
      case Event.SEEK:
        return doSeek (event);
      default:
        return doSendEvent (event);
    }
  }

  public boolean query(Query query)
  {
    boolean res = true;

    for (Enumeration e = enumSinks(); e.hasMoreElements();) {
      Element elem = (Element) e.nextElement();

      if ((res = elem.query (query)))
        break;
    }
    return res;
  }
}
