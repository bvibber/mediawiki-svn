/* Cortado - a video player java applet
 * Copyright (C) 2005 Fluendo S.L.
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

package com.fluendo.player;

import java.awt.*;
import java.net.URL;

import com.fluendo.jst.*;
import com.fluendo.utils.*;

public class CortadoPipeline extends Pipeline implements PadListener, CapsListener {

  private String url;
  private String userId;
  private String password;
  private boolean enableAudio;
  private boolean enableVideo;
  private Component component;
  private int bufferSize = -1;
  private int bufferLow = -1;
  private int bufferHigh = -1;
  private URL documentBase = null;
	
  private Element httpsrc;
  private Element buffer;
  private Element demux;
  private Element videodec;
  private Element audiodec;
  private Element videosink;
  private Element audiosink;
  private Element v_queue, a_queue;
  private Pad asinkpad, vsinkpad;
  private Pad apad, vpad;

  public boolean usingJavaX = false;

  private boolean setupVideoDec (String name) {
    videodec = ElementFactory.makeByName(name, "videodec");
    if (videodec == null) {
      noSuchElement (name);
      return false;
    }
    add(videodec);
    return true;
  }

  public void padAdded(Pad pad) {
    Caps caps = pad.getCaps ();
    Pad tmp;

    if (caps == null) {
      Debug.log(Debug.INFO, "pad added without caps: "+pad);
      return;
    }
    Debug.log(Debug.INFO, "pad added "+pad);

    String mime = caps.getMime();
    
    if (enableAudio && mime.equals("audio/x-vorbis")) {
      a_queue = ElementFactory.makeByName("queue", "a_queue");
      if (a_queue == null) {
        noSuchElement ("queue");
        return;
      }

      audiodec = ElementFactory.makeByName("vorbisdec", "audiodec");
      if (audiodec == null) {
        noSuchElement ("vorbisdec");
        return;
      }

      add(a_queue);
      add(audiodec);

      pad.link(a_queue.getPad("sink"));
      a_queue.getPad("src").link(audiodec.getPad("sink"));
      if (!audiodec.getPad("src").link(asinkpad)) {
        postMessage (Message.newError (this, "audiosink already linked"));
        return;
      }

      apad = pad;

      audiodec.setState (PAUSE);
      a_queue.setState (PAUSE);
    }
    else if (enableVideo && mime.equals("video/x-theora")) {
      v_queue = ElementFactory.makeByName("queue", "v_queue");
      if (v_queue == null) {
        noSuchElement ("queue");
	return;
      }

      if (!setupVideoDec ("theoradec"))
        return;

      add(v_queue);

      pad.link(v_queue.getPad("sink"));
      v_queue.getPad("src").link(videodec.getPad("sink"));
      if (!videodec.getPad("src").link(vsinkpad)) {
        postMessage (Message.newError (this, "videosink already linked"));
        return;
      }

      vpad = pad;

      videodec.setState (PAUSE);
      v_queue.setState (PAUSE);
    }
    else if (enableVideo && mime.equals("image/jpeg")) {
      if (!setupVideoDec ("jpegdec")) {
        return;
      }
      videodec.setProperty ("component", component);
      
      pad.link(videodec.getPad("sink"));
      if (!videodec.getPad("src").link(vsinkpad)) {
        postMessage (Message.newError (this, "videosink already linked"));
        return;
      }

      videodec.setState (PAUSE);
    }
    else if (enableVideo && mime.equals("video/x-smoke")) {
      if (!setupVideoDec ("smokedec")) {
        return;
      }
      videodec.setProperty ("component", component);
      
      pad.link(videodec.getPad("sink"));
      if (!videodec.getPad("src").link(vsinkpad)) {
        postMessage (Message.newError (this, "videosink already linked"));
        return;
      }
      vpad = pad;

      videodec.setState (PAUSE);
    }
  }
  
  public void padRemoved(Pad pad) {
    pad.unlink();
    if (pad == vpad) {
      Debug.log(Debug.INFO, "video pad removed "+pad);
      vsinkpad.unlink();
      vpad = null;
    }
    else if (pad == apad) {
      Debug.log(Debug.INFO, "audio pad removed "+pad);
      asinkpad.unlink();
      apad = null;
    }
  }

  public void noMorePads() {
    boolean changed = false;

    Debug.log(Debug.INFO, "all streams detected");

    if (apad == null && enableAudio) {
      Debug.log(Debug.INFO, "file has no audio, remove audiosink");
      audiosink.setState(STOP);
      remove (audiosink);
      audiosink = null;
      changed = true;
    }
    if (vpad == null && enableVideo) {
      Debug.log(Debug.INFO, "file has no video, remove videosink");
      videosink.setState(STOP);
      remove (videosink);
      videosink = null;
      changed = true;
    }
    if (changed)
      scheduleReCalcState();
  }

  public CortadoPipeline ()
  {
    super("pipeline");
    enableAudio = true;
    enableVideo = true;
  }

  public void setUrl(String anUrl) {
    url = anUrl;
  }
  public String getUrl() {
    return url;
  }
  public void setUserId(String aUserId) {
    userId = aUserId;
  }
  public void setPassword(String aPassword) {
    password = aPassword;
  }

  public void enableAudio(boolean b) {
    enableAudio = b;
  }
  public boolean isAudioEnabled() {
    return enableAudio;
  }

  public void enableVideo(boolean b) {
    enableVideo = b;
  }
  public boolean isVideoEnabled() {
    return enableVideo;
  }

  public void setComponent(Component c) {
    component = c;
  }
  public Component getComponent() {
    return component;
  }

  public void setDocumentBase(URL base) {
    documentBase = base;
  }
  public URL getDocumentBase() {
    return documentBase;
  }

  public void setBufferSize(int size) {
    bufferSize = size;
  }
  public int getBufferSize() {
    return bufferSize;
  }

  public void setBufferLow(int size) {
    bufferLow = size;
  }
  public int getBufferLow() {
    return bufferLow;
  }

  public void setBufferHigh(int size) {
    bufferHigh = size;
  }
  public int getBufferHigh() {
    return bufferHigh;
  }

  public boolean buildOgg()
  {
    demux = ElementFactory.makeByName("oggdemux", "demux");
    if (demux == null) {
      noSuchElement ("oggdemux");
      return false;
    }

    buffer = ElementFactory.makeByName("queue", "buffer");
    if (buffer == null) {
      demux = null;
      noSuchElement ("queue");
      return false;
    }
    buffer.setProperty("isBuffer", Boolean.TRUE);
    if (bufferSize != -1)
      buffer.setProperty("maxSize", new Integer (bufferSize * 1024));
    if (bufferLow != -1)
      buffer.setProperty("lowPercent", new Integer (bufferLow));
    if (bufferHigh != -1)
      buffer.setProperty("highercent", new Integer (bufferHigh));

    add(demux);
    add(buffer);

    httpsrc.getPad("src").link(buffer.getPad("sink"));
    buffer.getPad("src").link(demux.getPad("sink"));
    demux.addPadListener (this);

    buffer.setState(PAUSE);
    demux.setState(PAUSE);

    return true;
  }

  public boolean buildMultipart()
  {
    demux = ElementFactory.makeByName("multipartdemux", "demux");
    if (demux == null) {
      noSuchElement ("multipartdemux");
      return false;
    }
    add(demux);

    httpsrc.getPad("src").link(demux.getPad("sink"));

    demux.addPadListener (this);

    return true;
  }

  public void capsChanged(Caps caps) {
    String mime = caps.getMime();

    if (mime.equals ("application/ogg")) {
      buildOgg();
    }
    else if (mime.equals ("multipart/x-mixed-replace")) {
      buildMultipart();
    }
    else {
      postMessage (Message.newError (this, "unknown type: "+mime));
    }
  }
  private void noSuchElement(String elemName)
  {
    postMessage (Message.newError (this, "no such element: "+elemName+" (check plugins.ini)"));
  }

  private boolean build()
  {
    Configure configure = new Configure();
    String userAgent;
    String extra;
    String vendor = System.getProperty("java.vendor");

    httpsrc = ElementFactory.makeByName("httpsrc", "httpsrc");
    if (httpsrc == null) {
      noSuchElement ("httpsrc");
      return false;
    }
    
    httpsrc.setProperty("url", url);
    httpsrc.setProperty("userId", userId);
    httpsrc.setProperty("password", password);
    
    userAgent = "Cortado/" + configure.buildVersion + " " +
        vendor.substring(0, vendor.indexOf(" ")) + "/" + 
        System.getProperty("java.version");

    extra = "(" + System.getProperty("os.name")  + " " + 
          System.getProperty("os.version") + ")";

    try {
      String agent = System.getProperty("http.agent");
      if (agent != null) {
        extra = agent;
      }
    }
    catch (Exception e) {
    }
    userAgent += " " + extra;

    Debug.log(Debug.INFO, "setting User-Agent " + userAgent);

    httpsrc.setProperty("userAgent", userAgent);
    httpsrc.setProperty("documentBase", documentBase);
    add(httpsrc);

    httpsrc.getPad("src").addCapsListener (this);

    if (enableAudio) {
      try {
        Class.forName("javax.sound.sampled.AudioSystem");
        usingJavaX = true;
        audiosink = ElementFactory.makeByName("audiosinkj2", "audiosink");
	Debug.log(Debug.INFO, "using high quality javax.sound backend");
      }
      catch (ClassNotFoundException e) {
        audiosink = ElementFactory.makeByName("audiosinksa", "audiosink");
	Debug.log(Debug.INFO, "using low quality sun.audio backend");
      }
      if (audiosink == null) {
        noSuchElement ("audiosink");
        return false;
      }
      asinkpad = audiosink.getPad("sink");
      add(audiosink);
    }
    if (enableVideo) {
      videosink = ElementFactory.makeByName("videosink", "videosink");
      if (videosink == null) {
        noSuchElement ("videosink");
        return false;
      }
      videosink.setProperty ("component", component);
      vsinkpad = videosink.getPad("sink");
      add(videosink);
    }

    return true;
  }

  private boolean cleanup() {
    Debug.log(Debug.INFO, "cleanup");
    if (httpsrc != null) {
      remove (httpsrc);
      httpsrc = null;
    }
    if (audiosink != null) {
      remove (audiosink);
      audiosink = null;
      asinkpad = null;
    }
    if (videosink != null) {
      remove (videosink);
      videosink = null;
      vsinkpad = null;
    }
    if (buffer != null) {
      remove (buffer);
      buffer = null;
    }
    if (demux != null) {
      demux.removePadListener (this);
      remove (demux);
      demux = null;
    }
    if (v_queue != null) {
      remove (v_queue);
      v_queue = null;
    }
    if (a_queue != null) {
      remove (a_queue);
      a_queue = null;
    }
    if (videodec != null) {
      remove(videodec);
      videodec = null;
    }
    if (audiodec != null) {
      remove(audiodec);
      audiodec = null;
    }
    return true;
  }

  protected int changeState (int transition) {
    int res;

    switch (transition) {
      case STOP_PAUSE:
        if (!build())
          return FAILURE;
        break;
      default:
        break;
    }

    res = super.changeState (transition);

    switch (transition) {
      case PAUSE_STOP:
        cleanup();
        break;
      default:
        break;
    }

    return res;
  }

  protected boolean doSendEvent(com.fluendo.jst.Event event) {
    boolean res;

    if (event.getType() != com.fluendo.jst.Event.SEEK)
      return false;

    if (event.parseSeekFormat() != Format.PERCENT)
      return false;

    if (httpsrc == null)
      return false;

    res = httpsrc.getPad("src").sendEvent (event);
    getState(null, null, -1);

    return res;
  }

  protected long getPosition() {
    Query q;
    long result = 0;

    q = Query.newPosition(Format.TIME);
    if (super.query (q)) {
      result = q.parsePositionValue (); 
    }
    return result;
  }
}
