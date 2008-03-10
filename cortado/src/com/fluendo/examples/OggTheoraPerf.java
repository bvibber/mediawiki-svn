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

import java.io.*;
import java.util.*;
import com.jcraft.jogg.*;
import com.fluendo.jheora.*;

public class OggTheoraPerf  
{
  private static final int BUFFSIZE = 8192;

  private InputStream inputStream;

  private SyncState oy;
  private Vector streams;
  private boolean stopping;

  interface Consumer {
    public void consume (Packet op);
  }

  class TheoraDec implements Consumer {
    private Info ti;
    private Comment tc;
    private State ts;
    private int packet = 0;
    private YUVBuffer yuv;

    public TheoraDec () {
      ti = new Info();
      tc = new Comment();
      ts = new State();
      yuv = new YUVBuffer();
    }
    public void consume (Packet op) {
      //System.out.println ("creating packet");
      if (packet < 3) {
        //System.out.println ("decoding header");
        if(ti.decodeHeader(tc, op) < 0){
          // error case; not a theora header
          System.err.println("does not contain Theora video data.");
          return;
        }
        if (packet == 2) {
          ts.decodeInit(ti);
      
          System.out.println("theora dimension: "+ti.width+"x"+ti.height);
          System.out.println("theora aspect: "+ti.aspect_numerator+"x"+ti.aspect_denominator);
          System.out.println("theora framerate: "+ti.fps_numerator+"x"+ti.fps_denominator);
        }
      }
      else {
        if (ts.decodePacketin(op) != 0) {
          System.err.println("Error Decoding Theora.");
          return;
        }
        if (ts.decodeYUVout(yuv) != 0) {
          System.err.println("Error getting the picture.");
          return;
        }
      }
      packet++;
    }
  }

  class OggStream {
    public int serialno;
    public StreamState os;
    public boolean bos;
    public Consumer consumer;

    public OggStream (int serial) {
      serialno = serial;
      os = new StreamState();
      os.init(serial);
      os.reset();
      bos = true;
    }
  }

  public OggTheoraPerf (InputStream is) {
    inputStream = is;

    oy = new SyncState();
    streams = new Vector();
    stopping = false;
  }

  public static void main(String[] args) {
    try {
      int run = 1;

      long start = System.currentTimeMillis();
      while (run-- > 0) {
        OggTheoraPerf perf = new OggTheoraPerf(new FileInputStream(args[0]));

        perf.start();
      }
      long end = System.currentTimeMillis();

      System.out.println("ellapsed: "+(end-start));
    }
    catch (Exception e) {
      e.printStackTrace();
    }
  }

  public void start() {
    int res;
    Page og;
    Packet op;

    System.out.println("started ogg reader");

    og = new Page();
    op = new Packet();

    try {
      while (!stopping) {
        int index = oy.buffer(BUFFSIZE);
        //System.out.println("reading "+index+" "+BUFFSIZE);
        int read = inputStream.read(oy.data, index, BUFFSIZE);
        //System.out.println("read "+read);
        if (read < 0)
          break;
        oy.wrote(read);
  
        while (!stopping) {
	  res = oy.pageout(og);
	  //System.out.println("pageout "+res);
          if (res == 0)
	    break; // need more data
          if(res == -1) { 
	    // missing or corrupt data at this page position
            // no reason to complain; already complained above
          }
          else {
	    int serial = og.serialno();
	    OggStream stream = null;
	    for (int i=0; i<streams.size(); i++) {
	      stream = (OggStream) streams.elementAt(i);
	      if (stream.serialno == serial)
	        break;
	      stream = null;
	    }
	    if (stream == null) {
  	      System.out.println("new stream "+serial);
	      stream = new OggStream(serial);
	      streams.addElement(stream);
	    }

	    //System.out.println("pagein");
            res = stream.os.pagein(og);
            if (res < 0) {
              // error; stream version mismatch perhaps
              System.err.println("Error reading first page of Ogg bitstream data.");
              return;
            }
	    while (!stopping) {
	      res = stream.os.packetout(op);
	      //System.out.println("packetout "+res);
              if(res == 0)
	        break; // need more data
              if(res == -1) { 
	        // missing or corrupt data at this page position
                // no reason to complain; already complained above
              }
              else {
                // we have a packet.  Decode it
	        if (stream.bos) {
	          // typefind
	          if (op.packet_base[op.packet+1] == 0x76) {
		    System.out.println ("found vorbis audio");
	          }
	          else if (op.packet_base[op.packet+1] == 0x73) {
		    System.out.println ("found smoke video");
	          }
	          else if (op.packet_base[op.packet+1] == 0x74) {
		    System.out.println ("found theora video");
		    stream.consumer = new TheoraDec();
		  }
	          stream.bos = false;
	        }
		if (stream.consumer != null) 
		  stream.consumer.consume (op);
	      }
  	    }
	  }
	}
      }
    }
    catch (Exception e) {
      e.printStackTrace();
      stopping = true;
    }
    
    System.out.println("ogg reader done");
  }

  public void stop() {
    stopping = true;
  }
}
