/* Copyright (C) <2008> ogg.k.ogg.k <ogg.k.ogg.k@googlemail.com>
 * based on code Copyright (C) <2004> Wim Taymans <wim@fluendo.com>
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
import com.fluendo.jkate.*;
import com.fluendo.jst.*;
import com.fluendo.utils.*;

/**
 * Katedec is a decoder element for the Kate stream format.
 * See http://wiki.xiph.org/index.php/OggKate for more information.
 * Kate streams may be multiplexed in Ogg.
 * The Katedec element accepts Kate packets (presumably demultiplexed by an
 * Ogg demuxer element) on its sink, and generates Kate events on its source.
 * Kate events are Kate specific structures, which may then be interpreted
 * by a renderer.
*/
public class KateDec extends Element implements OggPayload
{
  /* Kate magic: 0x80 (BOS header) followed by "kate\0\0\0" */
  private static final byte[] signature = { -128, 0x6b, 0x61, 0x74, 0x65, 0x00, 0x00, 0x00 };

  private Info ki;
  private Comment kc;
  private State k;
  private Packet op;
  private int packetno;

  private long basetime = 0;
  private long lastTs;
  private boolean haveDecoder = false;

  /* 
   * OggPayload interface
   */
  public boolean isType (Packet op)
  {
    return typeFind (op.packet_base, op.packet, op.bytes) > 0;
  }
  public boolean isKeyFrame (Packet op)
  {
    return true;
  }

  /**
   * A discontinuous codec will not cause the pipeline to wait for data if starving
   */
  public boolean isDiscontinuous ()
  {
    return true;
  }
  public int takeHeader (Packet op)
  {
    int ret = ki.decodeHeader(kc, op);
    if (ret > 0) {
      k.decodeInit(ki);
      Debug.debug("Kate decoder ready");
      haveDecoder = true;
    }
    return ret;
  }
  public boolean isHeader (Packet op)
  {
    return (op.packet_base[op.packet] & 0x80) == 0x80;
  }
  public long getFirstTs (Vector packets)
  {
    int len = packets.size();
    int i;
    long time;
    com.fluendo.jst.Buffer data = null;

    /* first find buffer with valid offset */
    for (i=0; i<len; i++) {
      data = (com.fluendo.jst.Buffer) packets.elementAt(i);

      if (data.time_offset != -1)
        break;
    }
    if (i == packets.size())
      return -1;

    time = granuleToTime (data.time_offset);

    data = (com.fluendo.jst.Buffer) packets.elementAt(0);
    data.timestamp = time - (long) ((i+1) * (Clock.SECOND * ki.gps_denominator / ki.gps_numerator));

    return time;
  }

  /**
   * Converts a granule position to its time equivalent
  */
  public long granuleToTime (long gp)
  {
    long res;

    if (gp < 0 || !haveDecoder)
      return -1;

    res = (long) (k.granuleTime(gp) * Clock.SECOND);

    return res;
  }

  /**
   * Converts a granule position to its duration equivalent
   */
  public long granuleToDuration (long gp)
  {
    long res;

    if (gp < 0 || !haveDecoder)
      return -1;

    res = (long) (k.granuleDuration(gp) * Clock.SECOND);

    return res;
  }

  private Pad srcPad = new Pad(Pad.SRC, "src") {
    protected boolean eventFunc (com.fluendo.jst.Event event) {
      return sinkPad.pushEvent(event);
    }
  };

  private Pad sinkPad = new Pad(Pad.SINK, "sink") {
    protected boolean eventFunc (com.fluendo.jst.Event event) {
      boolean result;

      switch (event.getType()) {
        case com.fluendo.jst.Event.FLUSH_START:
	  result = srcPad.pushEvent (event);
	  synchronized (streamLock) {
            Debug.log(Debug.DEBUG, "synced "+this);
	  }
          break;
        case com.fluendo.jst.Event.FLUSH_STOP:
          result = srcPad.pushEvent(event);
          break;
        case com.fluendo.jst.Event.EOS:
          Debug.log(Debug.INFO, "got EOS "+this);
          result = srcPad.pushEvent(event);
          break;
        case com.fluendo.jst.Event.NEWSEGMENT:
          basetime = event.parseNewsegmentStart();
          Debug.info("new segment: base time "+basetime);
          result = srcPad.pushEvent(event);
          break;
	default:
          result = srcPad.pushEvent(event);
          break;
      }
      return result;
    }

    /**
     * receives Kate packets, and generates Kate events
     */
    protected int chainFunc (com.fluendo.jst.Buffer buf) {
      int result;
      long timestamp;

      Debug.log( Debug.DEBUG, parent.getName() + " <<< " + buf );

      op.packet_base = buf.data;
      op.packet = buf.offset;
      op.bytes = buf.length;
      op.b_o_s = (packetno == 0 ? 1 : 0);
      op.e_o_s = 0;
      op.packetno = packetno;
      timestamp = buf.timestamp;

      Debug.log(Debug.DEBUG, "Kate chainFunc with packetno "+packetno+", haveDecoder "+haveDecoder);

//      if (buf.isFlagSet (com.fluendo.jst.Buffer.FLAG_DISCONT)) {
//        Debug.log(Debug.INFO, "kate: got discont");
//        /* should flush, if we keep events to handle repeats in the future */
//        lastTs = -1;
//      }

      if (!haveDecoder) {
        //System.out.println ("decoding header");
        result = takeHeader(op);
        if (result < 0){
          buf.free();
          // error case; not a kate header
          Debug.log(Debug.ERROR, "does not contain Kate data.");
          return ERROR;
        }
        else if (result > 0) {
          // we've decoded all headers
          Debug.log(Debug.DEBUG, "Kate initialized for decoding");

          /* we're sending raw kate_event structures */
	  caps = new Caps ("application/x-kate-event");
        }
        buf.free();
        packetno++;

	return OK;
      }
      else {
        if ((op.packet_base[op.packet] & 0x80) == 0x80) {
          Debug.log(Debug.DEBUG, "ignoring header");
          buf.free();
          return OK;
        }

	if (timestamp != -1) {
	  lastTs = timestamp;
	}

	if (true) {
	  try{
            result = k.decodePacketin(op);
            if (result < 0) {
              buf.free();
              Debug.log(Debug.ERROR, "Error Decoding Kate.");
	      postMessage (Message.newError (this, "Error decoding Kate"));
              return ERROR;
            }
            com.fluendo.jkate.Event ev = k.decodeEventOut();
            if (ev != null) {
              buf.object = ev;
	      buf.caps = caps;
	      buf.timestamp = granuleToDuration(ev.start);
	      buf.timestampEnd = buf.timestamp + granuleToDuration(ev.duration);
              Debug.log( Debug.DEBUG, parent.getName() + " >>> " + buf );
              Debug.debug("Got Kate text: "+new String(ev.text)+" from "+buf.timestamp+" to "+buf.timestampEnd+", basetime "+basetime);
              result = srcPad.push(buf);
              Debug.log(Debug.DEBUG, "push returned "+result);
            }
            else {
              Debug.debug("Got no event");
	      buf.free();
              result = OK;
            }
          }
	  catch (Exception e) {
	    e.printStackTrace();
	    postMessage (Message.newError (this, e.getMessage()));
            result = ERROR;
	  }
	}
        else {
          result = OK;
	  buf.free();
	}
      }
      packetno++;

      return result;
    }

    protected boolean activateFunc (int mode)
    {
      return true;
    }
  };

  public KateDec() {
    super();

    ki = new Info();
    kc = new Comment();
    k = new State();
    op = new Packet();

    addPad (srcPad);
    addPad (sinkPad);
  }

  protected int changeState (int transition) {
    int res;

    switch (transition) {
      case STOP_PAUSE:
        lastTs = -1;
        packetno = 0;
	break;
      default:
        break;
    }

    res = super.changeState (transition);

    switch (transition) {
      case PAUSE_STOP:
	ki.clear();
	kc.clear();
	k.clear();
	break;
      default:
        break;
    }

    return res;
  }

  public String getFactoryName ()
  {
    return "katedec";
  }
  public String getMime ()
  {
    return "application/x-kate";
  }
  public int typeFind (byte[] data, int offset, int length)
  {
    if (MemUtils.startsWith (data, offset, length, signature))
      return 10;
    return -1;
  }
}
